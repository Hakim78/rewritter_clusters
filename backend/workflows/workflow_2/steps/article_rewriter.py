"""
Step 2: Article Rewriter using Anthropic Claude
Rewrites and optimizes articles for SEO, LLMO, RAG, and People-first
"""

import os
import time
import json
import logging
from typing import Dict, Any
from datetime import datetime
from anthropic import Anthropic
import pymysql

logger = logging.getLogger(__name__)


class ArticleRewriter:
    """Rewrites articles using Claude with comprehensive optimization"""

    def __init__(self, workflow_id=2):
        self.client = Anthropic(api_key=os.getenv('ANTHROPIC_API_KEY'))
        self.model = "claude-sonnet-4-5-20250929"
        self.max_tokens = 16000
        self.workflow_id = workflow_id

    async def rewrite_article(self,
                             article_data: Dict[str, Any],
                             user_requirements: Dict[str, Any]) -> Dict[str, Any]:
        """
        Rewrite and optimize an existing article

        Args:
            article_data: Extracted article content from step 1
            user_requirements: User inputs (keyword, internal_links, etc.)

        Returns:
            Rewritten article with all optimizations
        """
        start_time = time.time()
        logger.info("Starting article rewriting with Claude...")

        try:
            # Build the rewriting prompt
            rewriting_prompt = self._build_rewriting_prompt(
                article_data=article_data,
                user_requirements=user_requirements
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

            # Log response preview
            logger.info(f"Claude response preview (first 500 chars): {rewritten_content[:500]}")
            logger.info(f"Claude response length: {len(rewritten_content)} characters")

            # Parse the structured response
            parsed_article = self._parse_rewritten_response(rewritten_content)

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
                    'internal_links_added': parsed_article.get('internal_links_added', []),
                    'keywords': {
                        'primary': user_requirements.get('keyword', ''),
                        'secondary': parsed_article.get('secondary_keywords', [])
                    },
                    'word_count': self._count_words(parsed_article.get('html_content', '')),
                    'improvements': parsed_article.get('improvements', [])
                },
                'metrics': {
                    'rewriting_time': processing_time,
                    'content_length': len(parsed_article.get('html_content', '')),
                    'word_count': self._count_words(parsed_article.get('html_content', '')),
                    'tokens_used': response.usage.input_tokens + response.usage.output_tokens,
                    'input_tokens': response.usage.input_tokens,
                    'output_tokens': response.usage.output_tokens
                },
                'raw_output': rewritten_content
            }

            logger.info(f"Article rewriting completed in {processing_time}s - {result['article']['word_count']} words")
            return result

        except Exception as e:
            logger.error(f"Article rewriting failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    def _build_rewriting_prompt(self,
                                article_data: Dict[str, Any],
                                user_requirements: Dict[str, Any]) -> str:
        """Build the comprehensive rewriting prompt using database template"""

        # Load template from database
        try:
            template = self._load_template_from_db()
            if not template:
                raise Exception("No active template found in database for workflow 2")
        except Exception as e:
            logger.error(f"Failed to load template: {e}")
            raise Exception(f"Template loading failed: {e}")

        # Format internal links
        internal_links = user_requirements.get('internal_links', [])
        internal_links_formatted = '\n'.join([f"- {link}" for link in internal_links]) if internal_links else "Aucun lien interne fourni"

        # Prepare variables for injection
        variables = {
            'ORIGINAL_TITLE': article_data.get('title', 'Sans titre'),
            'ORIGINAL_CONTENT': article_data.get('content_html', ''),
            'ORIGINAL_TEXT': article_data.get('content_text', '')[:5000],  # Limit text
            'ORIGINAL_META_DESC': article_data.get('meta_description', 'Non disponible'),
            'WORD_COUNT': str(article_data.get('word_count', 0)),
            'KEYWORD': user_requirements.get('keyword', 'Non spécifié'),
            'INTERNAL_LINKS': internal_links_formatted,
            'CURRENT_DATE': datetime.now().strftime('%Y-%m-%d'),
            'SOURCE_URL': article_data.get('source_url', 'Manuel') or 'Manuel'
        }

        # Inject variables into template
        prompt = template
        for key, value in variables.items():
            prompt = prompt.replace(f'{{{key}}}', str(value))

        return prompt

    def _load_template_from_db(self) -> str:
        """Load the active template for workflow 2 from the database"""
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

    def _parse_rewritten_response(self, response: str) -> Dict[str, Any]:
        """Parse the structured rewritten article response from Claude"""
        parsed = {}

        sections = {
            'seo_title': 'SEO_TITLE',
            'meta_description': 'META_DESCRIPTION',
            'wordpress_excerpt': 'WORDPRESS_EXCERPT',
            'html_content': 'HTML_CONTENT',
            'faq_section': 'FAQ_SECTION',
            'faq_json': 'FAQ_JSON',
            'secondary_keywords': 'SECONDARY_KEYWORDS',
            'internal_links_added': 'INTERNAL_LINKS_ADDED',
            'improvements': 'IMPROVEMENTS'
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
                    elif key == 'internal_links_added':
                        parsed[key] = [link.strip() for link in content.split(',') if link.strip()]
                    elif key == 'improvements':
                        parsed[key] = [imp.strip() for imp in content.split('\n') if imp.strip()]
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
                    if key in ['secondary_keywords', 'internal_links_added', 'improvements']:
                        parsed[key] = []
                    elif key == 'faq_json':
                        parsed[key] = []
                    else:
                        parsed[key] = ''

            except Exception as e:
                logger.warning(f"Failed to parse section {key}: {e}")
                if key in ['secondary_keywords', 'internal_links_added', 'improvements', 'faq_json']:
                    parsed[key] = []
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
            return len(html_content.split())