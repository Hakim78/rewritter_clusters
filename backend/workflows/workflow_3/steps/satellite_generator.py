"""
Step 3: Satellite Articles Generator for Workflow 3
Generates 3 satellite articles based on identified themes
"""

import os
import time
import json
import logging
from typing import Dict, Any, List
from datetime import datetime
from anthropic import Anthropic
import pymysql

logger = logging.getLogger(__name__)


class SatelliteGenerator:
    """Generates 3 satellite articles for the cluster"""

    def __init__(self, workflow_id=3):
        self.client = Anthropic(api_key=os.getenv('ANTHROPIC_API_KEY'))
        self.model = "claude-sonnet-4-5-20250929"
        self.max_tokens = 16000
        self.workflow_id = workflow_id

    async def generate_satellites(self,
                                 pillar_data: Dict[str, Any],
                                 satellite_themes: List[Dict[str, str]],
                                 main_keyword: str) -> Dict[str, Any]:
        """
        Generate 3 satellite articles

        Args:
            pillar_data: Pillar article data (for context)
            satellite_themes: List of 3 themes
            main_keyword: Main cluster keyword

        Returns:
            List of 3 satellite articles
        """
        start_time = time.time()
        logger.info("Starting satellite articles generation...")

        try:
            satellites = []

            # Generate each satellite article
            for i, theme in enumerate(satellite_themes, 1):
                logger.info(f"Generating satellite {i}/3: {theme['theme']}")

                satellite_article = await self._generate_single_satellite(
                    theme=theme,
                    pillar_title=pillar_data.get('title', ''),
                    main_keyword=main_keyword,
                    satellite_number=i
                )

                if satellite_article['success']:
                    satellites.append(satellite_article['article'])
                else:
                    logger.warning(f"Satellite {i} generation failed: {satellite_article.get('error')}")
                    # Continue with other satellites even if one fails

            processing_time = round(time.time() - start_time, 2)

            result = {
                'success': len(satellites) > 0,
                'processing_time': processing_time,
                'timestamp': datetime.now().isoformat(),
                'satellites': satellites,
                'total_generated': len(satellites)
            }

            logger.info(f"Satellite generation completed: {len(satellites)}/3 articles in {processing_time}s")
            return result

        except Exception as e:
            logger.error(f"Satellite generation failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    async def _generate_single_satellite(self,
                                        theme: Dict[str, str],
                                        pillar_title: str,
                                        main_keyword: str,
                                        satellite_number: int) -> Dict[str, Any]:
        """Generate a single satellite article"""

        try:
            # Build the satellite prompt
            prompt = self._build_satellite_prompt(
                theme=theme,
                pillar_title=pillar_title,
                main_keyword=main_keyword,
                satellite_number=satellite_number
            )

            # Call Claude API
            response = self.client.messages.create(
                model=self.model,
                max_tokens=self.max_tokens,
                temperature=0.7,
                messages=[
                    {
                        "role": "user",
                        "content": prompt
                    }
                ]
            )

            response_content = response.content[0].text

            # Parse the response
            parsed_article = self._parse_satellite_response(response_content)

            # Log parsed data for debugging
            logger.info(f"Satellite {satellite_number} parsed - Title: {parsed_article.get('seo_title', 'MISSING')[:50]}")
            logger.info(f"Satellite {satellite_number} - HTML content length: {len(parsed_article.get('html_content', ''))}")
            logger.info(f"Satellite {satellite_number} - Word count: {self._count_words(parsed_article.get('html_content', ''))}")

            article_data = {
                'type': 'satellite',
                'number': satellite_number,
                'theme': theme['theme'],
                'seo_title': parsed_article.get('seo_title', f"Satellite {satellite_number}"),
                'meta_description': parsed_article.get('meta_description', ''),
                'wordpress_excerpt': parsed_article.get('wordpress_excerpt', ''),
                'html_content': parsed_article.get('html_content', ''),
                'faq_section': parsed_article.get('faq_section', ''),
                'faq_json': parsed_article.get('faq_json', []),
                'word_count': self._count_words(parsed_article.get('html_content', '')),
                'internal_links': parsed_article.get('internal_links', [])
            }

            # Validate critical fields
            if not article_data['html_content']:
                logger.error(f"Satellite {satellite_number} - No HTML content generated!")
                logger.debug(f"Raw response preview: {response_content[:500]}")

            return {
                'success': True,
                'article': article_data
            }

        except Exception as e:
            logger.error(f"Failed to generate satellite {satellite_number}: {str(e)}")
            return {
                'success': False,
                'error': str(e)
            }

    def _build_satellite_prompt(self,
                                theme: Dict[str, str],
                                pillar_title: str,
                                main_keyword: str,
                                satellite_number: int) -> str:
        """Build satellite generation prompt"""

        # Try loading from database, fallback to embedded template
        try:
            template = self._load_template_from_db()
            if not template:
                template = self._get_fallback_satellite_template()
        except Exception as e:
            logger.warning(f"Using fallback template: {e}")
            template = self._get_fallback_satellite_template()

        # Prepare variables
        variables = {
            'SATELLITE_NUMBER': str(satellite_number),
            'SATELLITE_THEME': theme['theme'],
            'SATELLITE_FOCUS': theme['focus'],
            'SATELLITE_KEYWORD': theme['target_keyword'],
            'PILLAR_TITLE': pillar_title,
            'MAIN_KEYWORD': main_keyword,
            'CURRENT_DATE': datetime.now().strftime('%Y-%m-%d')
        }

        # Inject variables
        prompt = template
        for key, value in variables.items():
            prompt = prompt.replace(f'{{{key}}}', str(value))

        return prompt

    def _load_template_from_db(self) -> str:
        """Load template from database"""
        try:
            conn = pymysql.connect(
                host=os.getenv('DB_HOST'),
                port=int(os.getenv('DB_PORT', 3306)),
                user=os.getenv('DB_USER'),
                password=os.getenv('DB_PASSWORD'),
                database=os.getenv('DB_NAME'),
                charset='utf8mb4',
                cursorclass=pymysql.cursors.DictCursor
            )

            with conn.cursor() as cursor:
                cursor.execute("""
                    SELECT content, version
                    FROM prompt_templates
                    WHERE workflow_id = %s AND is_active = TRUE
                    ORDER BY version DESC
                    LIMIT 1
                """, (self.workflow_id,))

                result = cursor.fetchone()

                if result:
                    return result['content']
                else:
                    return None

        except Exception as e:
            logger.error(f"Failed to load template: {e}")
            return None

        finally:
            if conn:
                conn.close()

    def _get_fallback_satellite_template(self) -> str:
        """Fallback template for satellite generation"""
        return """Crée un article satellite pour un cluster thématique.

Satellite #{SATELLITE_NUMBER}
Thème: {SATELLITE_THEME}
Focus: {SATELLITE_FOCUS}
Mot-clé: {SATELLITE_KEYWORD}

Article pilier: {PILLAR_TITLE}
Mot-clé principal cluster: {MAIN_KEYWORD}

Génère un article satellite autonome avec:
- Titre SEO ≤60 chars avec mot-clé
- Meta description ≤155 chars
- Extrait WordPress (2 lignes)
- Contenu HTML structuré (<h2>, <h3>, listes)
- 2-3 liens internes (vers pilier + autres satellites)
- FAQ (2-3 questions courtes)
- Contenu People-First, LLMO, RAG-friendly

Format de sortie:
<SEO_TITLE>titre</SEO_TITLE>
<META_DESCRIPTION>meta</META_DESCRIPTION>
<WORDPRESS_EXCERPT>extrait</WORDPRESS_EXCERPT>
<HTML_CONTENT>contenu HTML</HTML_CONTENT>
<FAQ_SECTION>FAQ HTML</FAQ_SECTION>
<FAQ_JSON>[...]</FAQ_JSON>
<INTERNAL_LINKS>lien1, lien2</INTERNAL_LINKS>
"""

    def _parse_satellite_response(self, response: str) -> Dict[str, Any]:
        """Parse satellite article response"""
        parsed = {}

        sections = {
            'seo_title': 'SEO_TITLE',
            'meta_description': 'META_DESCRIPTION',
            'wordpress_excerpt': 'WORDPRESS_EXCERPT',
            'html_content': 'HTML_CONTENT',
            'faq_section': 'FAQ_SECTION',
            'faq_json': 'FAQ_JSON',
            'internal_links': 'INTERNAL_LINKS'
        }

        for key, tag in sections.items():
            try:
                start_tag = f"<{tag}>"
                end_tag = f"</{tag}>"
                start_idx = response.find(start_tag)
                end_idx = response.find(end_tag)

                if start_idx != -1 and end_idx != -1:
                    content = response[start_idx + len(start_tag):end_idx].strip()

                    if key == 'faq_json':
                        try:
                            parsed[key] = json.loads(content)
                        except json.JSONDecodeError:
                            parsed[key] = []
                    elif key == 'internal_links':
                        parsed[key] = [link.strip() for link in content.split(',') if link.strip()]
                    else:
                        parsed[key] = content
                else:
                    if key in ['faq_json', 'internal_links']:
                        parsed[key] = []
                    else:
                        parsed[key] = ''

            except Exception as e:
                logger.warning(f"Failed to parse section {key}: {e}")
                if key in ['faq_json', 'internal_links']:
                    parsed[key] = []
                else:
                    parsed[key] = ''

        return parsed

    def _count_words(self, html_content: str) -> int:
        """Count words in HTML"""
        try:
            from bs4 import BeautifulSoup
            soup = BeautifulSoup(html_content, 'html.parser')
            text = soup.get_text()
            return len(text.split())
        except:
            return len(html_content.split())
