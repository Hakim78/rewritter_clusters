"""
Step 2: Pillar Article Rewriter for Workflow 3
Rewrites and optimizes the pillar article with links to satellites
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


class PillarRewriter:
    """Rewrites pillar article with optimization for cluster structure"""

    def __init__(self, workflow_id=3):
        self.client = Anthropic(api_key=os.getenv('ANTHROPIC_API_KEY'))
        self.model = "claude-sonnet-4-5-20250929"
        self.max_tokens = 16000
        self.workflow_id = workflow_id

    async def rewrite_pillar(self,
                            pillar_data: Dict[str, Any],
                            satellite_themes: List[Dict[str, str]],
                            main_keyword: str) -> Dict[str, Any]:
        """
        Rewrite and optimize pillar article for cluster

        Args:
            pillar_data: Extracted pillar article content
            satellite_themes: List of 3 satellite themes
            main_keyword: Main keyword for the cluster

        Returns:
            Optimized pillar article
        """
        start_time = time.time()
        logger.info("Starting pillar article rewriting...")

        try:
            # Build the rewriting prompt
            rewriting_prompt = self._build_pillar_prompt(
                pillar_data=pillar_data,
                satellite_themes=satellite_themes,
                main_keyword=main_keyword
            )

            # Call Claude API
            response = self.client.messages.create(
                model=self.model,
                max_tokens=self.max_tokens,
                temperature=0.7,
                messages=[
                    {
                        "role": "user",
                        "content": rewriting_prompt
                    }
                ]
            )

            rewritten_content = response.content[0].text
	    
            logger.info(f"===== PILLAR CLAUDE RESPONSE (first 1000 chars) =====")
            logger.info(rewritten_content[:1000])
            logger.info(f"===== PILLAR CLAUDE RESPONSE LENGTH: {len(rewritten_content)} =====")	
	    # Parse the structured response
            parsed_article = self._parse_pillar_response(rewritten_content)

            processing_time = round(time.time() - start_time, 2)

            result = {
                'success': True,
                'processing_time': processing_time,
                'timestamp': datetime.now().isoformat(),
                'pillar_article': {
                    'type': 'pillar',
                    'seo_title': parsed_article.get('seo_title', ''),
                    'meta_description': parsed_article.get('meta_description', ''),
                    'wordpress_excerpt': parsed_article.get('wordpress_excerpt', ''),
                    'html_content': parsed_article.get('html_content', ''),
                    'faq_section': parsed_article.get('faq_section', ''),
                    'faq_json': parsed_article.get('faq_json', []),
                    'word_count': self._count_words(parsed_article.get('html_content', '')),
                    'satellite_links_added': parsed_article.get('satellite_links', [])
                },
                'metrics': {
                    'rewriting_time': processing_time,
                    'tokens_used': response.usage.input_tokens + response.usage.output_tokens
                }
            }

            logger.info(f"Pillar rewriting completed in {processing_time}s")
            return result

        except Exception as e:
            logger.error(f"Pillar rewriting failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    def _build_pillar_prompt(self,
                            pillar_data: Dict[str, Any],
                            satellite_themes: List[Dict[str, str]],
                            main_keyword: str) -> str:
        """Build the pillar rewriting prompt"""

        # Load template from database
        try:
            template = self._load_template_from_db()
            if not template:
                raise Exception("No active template found in database for workflow 3 pillar")
        except Exception as e:
            logger.error(f"Failed to load template: {e}")
            # Fallback to embedded template
            template = self._get_fallback_pillar_template()

        # Format satellite themes
        satellites_formatted = '\n'.join([
            f"{i+1}. {theme['theme']} (Focus: {theme['focus']})"
            for i, theme in enumerate(satellite_themes)
        ])

        # Prepare variables
        variables = {
            'ORIGINAL_TITLE': pillar_data.get('title', ''),
            'PILLAR_TITLE': pillar_data.get('title', ''),
            'ORIGINAL_CONTENT': pillar_data.get('content_html', ''),
            'ORIGINAL_TEXT': pillar_data.get('content_text', '')[:5000],
            'WORD_COUNT': str(pillar_data.get('word_count', 0)),
            'KEYWORD': main_keyword,
            'SATELLITE_THEMES': satellites_formatted,
            'CURRENT_DATE': datetime.now().strftime('%Y-%m-%d'),
            'SOURCE_URL': pillar_data.get('source_url', '')
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
                    logger.info(f"Loaded template from database: workflow {self.workflow_id}, version {result['version']}")
                    return result['content']
                else:
                    return None

        except Exception as e:
            logger.error(f"Failed to load template from database: {e}")
            return None

        finally:
            if conn:
                conn.close()

    def _get_fallback_pillar_template(self) -> str:
        """Fallback template if database is unavailable"""
        return """Réécris et optimise cet article pilier pour un cluster thématique.

Article original: {ORIGINAL_TITLE}
Contenu: {ORIGINAL_TEXT}
Mot-clé: {KEYWORD}

3 articles satellites à prévoir:
{SATELLITE_THEMES}

Optimise l'article pilier avec:
- SEO (titre ≤60 chars, meta ≤155 chars)
- Liens vers les 3 satellites (ancres naturelles)
- Structure HTML (<h2>, <h3>, listes)
- FAQ (3-5 questions)

Format de sortie:
<SEO_TITLE>titre</SEO_TITLE>
<META_DESCRIPTION>meta</META_DESCRIPTION>
<WORDPRESS_EXCERPT>extrait</WORDPRESS_EXCERPT>
<HTML_CONTENT>contenu HTML</HTML_CONTENT>
<FAQ_SECTION>FAQ HTML</FAQ_SECTION>
<FAQ_JSON>[...]</FAQ_JSON>
<SATELLITE_LINKS>lien1, lien2, lien3</SATELLITE_LINKS>
"""

    def _parse_pillar_response(self, response: str) -> Dict[str, Any]:
        """Parse pillar article response from Claude"""
        parsed = {}

        sections = {
            'seo_title': 'SEO_TITLE',
            'meta_description': 'META_DESCRIPTION',
            'wordpress_excerpt': 'WORDPRESS_EXCERPT',
            'html_content': 'HTML_CONTENT',
            'faq_section': 'FAQ_SECTION',
            'faq_json': 'FAQ_JSON',
            'satellite_links': 'SATELLITE_LINKS'
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
                    elif key == 'satellite_links':
                        parsed[key] = [link.strip() for link in content.split(',') if link.strip()]
                    else:
                        parsed[key] = content
                else:
                    if key in ['faq_json', 'satellite_links']:
                        parsed[key] = []
                    else:
                        parsed[key] = ''

            except Exception as e:
                logger.warning(f"Failed to parse section {key}: {e}")
                if key in ['faq_json', 'satellite_links']:
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
