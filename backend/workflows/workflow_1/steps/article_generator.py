"""
Step 3: Article Generation Module using Anthropic Claude
Generates expert SEO articles optimized for humans, search engines, LLMs, and RAG systems
"""

import os
import time
import json
import logging
from typing import Dict, List, Any
from datetime import datetime
from anthropic import Anthropic
import pymysql

logger = logging.getLogger(__name__)

class ArticleGenerator:
    """Generates optimized articles using Claude with 4-expert approach: SEO, People First, LLMO, RAG"""

    def __init__(self, workflow_id=1):
        self.client = Anthropic(api_key=os.getenv('ANTHROPIC_API_KEY'))
        self.model = "claude-sonnet-4-5-20250929"  # Claude Sonnet 4.5
        self.max_tokens = 16000  # Increased to ensure all sections are generated
        self.workflow_id = workflow_id

        # Ancienne méthode (fichiers) - conservée en fallback
        self.template_path = os.path.join(
            os.path.dirname(os.path.dirname(__file__)),
            'templates',
            'article_prompt_template.txt'
        )

    async def generate_article(self,
                              scraped_data: Dict[str, Any],
                              analysis_data: Dict[str, Any],
                              user_requirements: Dict[str, Any]) -> Dict[str, Any]:
        """
        Generate a complete SEO-optimized article based on scraped data and analysis

        Args:
            scraped_data: Output from website scraper (step 1)
            analysis_data: Output from content analyzer (step 2)
            user_requirements: User input (keyword, domain, guideline, links)

        Returns:
            Complete article with metadata, SEO elements, and HTML content
        """
        start_time = time.time()
        logger.info("Starting article generation with Claude...")

        try:
            # Build the generation prompt with 4-expert approach
            generation_prompt = self._build_generation_prompt(
                scraped_data=scraped_data,
                analysis_data=analysis_data,
                user_requirements=user_requirements
            )

            # Call Claude API
            response = self.client.messages.create(
                model=self.model,
                max_tokens=self.max_tokens,
                temperature=0.7,  # Balance between creativity and consistency
                messages=[
                    {
                        "role": "user",
                        "content": generation_prompt
                    }
                ]
            )

            article_content = response.content[0].text

            # Debug: Log first 500 chars of response
            logger.info(f"Claude response preview (first 500 chars): {article_content[:500]}")
            logger.info(f"Claude response length: {len(article_content)} characters")

            # Parse the structured article response
            parsed_article = self._parse_article_response(article_content)

            # Debug logging
            logger.info(f"Parsed FAQ JSON: {parsed_article.get('faq_json', 'NOT FOUND')}")
            logger.info(f"Parsed secondary keywords: {parsed_article.get('secondary_keywords', 'NOT FOUND')}")
            logger.info(f"Parsed HTML content length: {len(parsed_article.get('html_content', ''))}")

            processing_time = round(time.time() - start_time, 2)

            result = {
                'success': True,
                'processing_time': processing_time,
                'timestamp': datetime.now().isoformat(),
                'article': {
                    'seo_title': parsed_article.get('seo_title', ''),
                    'meta_description': parsed_article.get('meta_description', ''),
                    'wordpress_excerpt': parsed_article.get('wordpress_excerpt', ''),
                    'html_content': parsed_article.get('html_content', ''),
                    'faq_section': parsed_article.get('faq_section', ''),
                    'faq_json': parsed_article.get('faq_json', []),
                    'schema_markup': parsed_article.get('schema_markup', {}),
                    'internal_links': parsed_article.get('internal_links', []),
                    'keywords': {
                        'primary': user_requirements.get('keyword', ''),
                        'secondary': parsed_article.get('secondary_keywords', []),
                        'entities': parsed_article.get('entities', [])
                    },
                    'word_count': self._count_words(parsed_article.get('html_content', '')),
                    'readability_score': parsed_article.get('readability_score', 'N/A')
                },
                'metrics': {
                    'generation_time': processing_time,
                    'content_length': len(parsed_article.get('html_content', '')),
                    'word_count': self._count_words(parsed_article.get('html_content', '')),
                    'tokens_used': response.usage.input_tokens + response.usage.output_tokens,
                    'input_tokens': response.usage.input_tokens,
                    'output_tokens': response.usage.output_tokens
                },
                'raw_output': article_content  # For debugging purposes
            }

            logger.info(f"Article generation completed in {processing_time}s - {result['article']['word_count']} words")
            return result

        except Exception as e:
            logger.error(f"Article generation failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    def _build_generation_prompt(self,
                                 scraped_data: Dict[str, Any],
                                 analysis_data: Dict[str, Any],
                                 user_requirements: Dict[str, Any]) -> str:
        """Build the comprehensive generation prompt with 4-expert methodology using template"""

        # Extract key data
        insights = analysis_data.get('insights', {})
        content_summary = analysis_data.get('content_summary', {})

        # Format internal links for the prompt
        internal_links_formatted = self._format_links_for_prompt(
            user_requirements.get('internal_links', [])
        )

        # Format external references
        external_refs = scraped_data.get('external_references', [])
        external_refs_formatted = self._format_external_refs(external_refs)

        # Load template (try database first, fallback to file)
        try:
            template = self._load_template_from_db()
            if not template:
                logger.warning("No template found in database, falling back to file")
                with open(self.template_path, 'r', encoding='utf-8') as f:
                    template = f.read()
        except Exception as e:
            logger.error(f"Failed to load template: {e}")
            raise Exception(f"Template loading failed: {e}")

        # Prepare variables for injection
        variables = {
            'DOMAIN': user_requirements.get('domain', 'Non spécifié'),
            'KEYWORD': user_requirements.get('keyword', 'Non spécifié'),
            'GUIDELINE': user_requirements.get('guideline', 'Non spécifié'),
            'SITE_URL': user_requirements.get('site_url', 'Non spécifié'),
            'CONTENT_TONE': insights.get('content_tone', 'À déterminer'),
            'TARGET_AUDIENCE': insights.get('target_audience', 'À déterminer'),
            'MAIN_TOPICS': ', '.join(insights.get('main_topics', [])[:5]),
            'SEO_OPPORTUNITIES': self._format_list(insights.get('seo_opportunities', []), 'SEO'),
            'CONTENT_GAPS': self._format_list(insights.get('content_gaps', []), 'Lacunes'),
            'CONTENT_STRATEGY': insights.get('content_strategy', 'Développer un contenu approfondi et structuré'),
            'KEYWORD_OPPORTUNITIES': ', '.join(insights.get('keyword_opportunities', [])[:10]),
            'INTERNAL_LINKS': internal_links_formatted,
            'EXTERNAL_REFS': external_refs_formatted,
            'CURRENT_DATE': datetime.now().strftime('%Y-%m-%d')
        }

        # Inject variables into template
        prompt = template
        for key, value in variables.items():
            prompt = prompt.replace(f'{{{key}}}', str(value))

        return prompt

    def _load_template_from_db(self) -> str:
        """Load the active template for this workflow from the database"""
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
                    logger.warning(f"No active template found in database for workflow {self.workflow_id}")
                    return None

        except Exception as e:
            logger.error(f"Failed to load template from database: {e}")
            return None

        finally:
            if conn:
                conn.close()

    def _format_list(self, items: List[str], prefix: str = '') -> str:
        """Format a list of items for the prompt"""
        if not items:
            return f"Aucune {prefix.lower()} spécifique identifiée"

        formatted = '\n'.join([f"- {item}" for item in items[:10]])
        return formatted

    def _format_links_for_prompt(self, links: List[str]) -> str:
        """Format internal links for the prompt"""
        if not links:
            return "Aucun lien interne fourni - tu peux suggérer des liens génériques"

        formatted = '\n'.join([f"- {link}" for link in links[:10]])
        return formatted

    def _format_external_refs(self, external_refs: List[Dict]) -> str:
        """Format external references for context"""
        if not external_refs:
            return "Aucune référence externe"

        formatted = []
        for ref in external_refs[:5]:
            formatted.append(f"- {ref.get('domain')}: {ref.get('title', 'N/A')}")

        return '\n'.join(formatted)

    def _parse_article_response(self, response: str) -> Dict[str, Any]:
        """Parse the structured article response from Claude"""
        parsed = {}

        # Define sections to extract
        sections = {
            'seo_title': 'SEO_TITLE',
            'meta_description': 'META_DESCRIPTION',
            'wordpress_excerpt': 'WORDPRESS_EXCERPT',
            'html_content': 'HTML_CONTENT',
            'faq_section': 'FAQ_SECTION',
            'faq_json': 'FAQ_JSON',
            'secondary_keywords': 'SECONDARY_KEYWORDS',
            'entities': 'ENTITIES',
            'internal_links_used': 'INTERNAL_LINKS_USED',
            'schema_markup': 'SCHEMA_MARKUP',
            'readability_score': 'READABILITY_SCORE'
        }

        for key, tag in sections.items():
            try:
                start_tag = f"<{tag}>"
                end_tag = f"</{tag}>"
                start_idx = response.find(start_tag)
                end_idx = response.find(end_tag)

                if start_idx != -1 and end_idx != -1:
                    content = response[start_idx + len(start_tag):end_idx].strip()

                    # Special handling for different types
                    if key == 'secondary_keywords':
                        parsed[key] = [kw.strip() for kw in content.split(',') if kw.strip()]
                    elif key == 'entities':
                        parsed[key] = [entity.strip() for entity in content.split(',') if entity.strip()]
                    elif key == 'internal_links_used':
                        parsed['internal_links'] = [link.strip() for link in content.split(',') if link.strip()]
                    elif key == 'schema_markup':
                        try:
                            parsed[key] = json.loads(content)
                        except json.JSONDecodeError:
                            parsed[key] = {}
                            logger.warning("Failed to parse schema_markup as JSON")
                    elif key == 'faq_json':
                        try:
                            parsed[key] = json.loads(content)
                        except json.JSONDecodeError:
                            parsed[key] = []
                            logger.warning("Failed to parse faq_json as JSON")
                    else:
                        parsed[key] = content
                else:
                    # Provide default values
                    if key in ['secondary_keywords', 'entities']:
                        parsed[key] = []
                    elif key == 'internal_links_used':
                        parsed['internal_links'] = []
                    elif key in ['schema_markup']:
                        parsed[key] = {}
                    elif key == 'faq_json':
                        parsed[key] = []
                    else:
                        parsed[key] = ''

            except Exception as e:
                logger.warning(f"Failed to parse section {key}: {e}")
                if key in ['secondary_keywords', 'entities', 'faq_json']:
                    parsed[key] = []
                elif key == 'schema_markup':
                    parsed[key] = {}
                else:
                    parsed[key] = ''

        return parsed

    def _count_words(self, html_content: str) -> int:
        """Count words in HTML content (excluding tags)"""
        try:
            from bs4 import BeautifulSoup
            soup = BeautifulSoup(html_content, 'html.parser')
            text = soup.get_text()
            words = text.split()
            return len(words)
        except Exception as e:
            logger.warning(f"Failed to count words: {e}")
            # Fallback: simple word count
            return len(html_content.split())