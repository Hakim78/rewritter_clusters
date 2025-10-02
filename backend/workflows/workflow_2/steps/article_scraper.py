"""
Step 1: Article Scraper for Workflow 2
Extracts article content from URL or accepts manual input
"""

import asyncio
import aiohttp
import time
import logging
from typing import Dict, Any, Optional
from bs4 import BeautifulSoup
from datetime import datetime

logger = logging.getLogger(__name__)


class ArticleScraper:
    """Handles article content extraction from URLs or manual input"""

    def __init__(self):
        self.timeout = 30
        self.user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'

    async def extract_article(self,
                             input_mode: str,
                             article_url: Optional[str] = None,
                             article_title: Optional[str] = None,
                             article_content: Optional[str] = None) -> Dict[str, Any]:
        """
        Extract article content based on input mode

        Args:
            input_mode: 'url' or 'manual'
            article_url: URL to scrape (if mode is 'url')
            article_title: Manual title (if mode is 'manual')
            article_content: Manual content (if mode is 'manual')

        Returns:
            Dictionary with extracted article data
        """
        start_time = time.time()

        try:
            if input_mode == 'url':
                if not article_url:
                    raise ValueError("article_url is required for URL mode")

                logger.info(f"Extracting article from URL: {article_url}")
                result = await self._scrape_article_from_url(article_url)

            elif input_mode == 'manual':
                if not article_title or not article_content:
                    raise ValueError("article_title and article_content are required for manual mode")

                logger.info("Processing manually provided article content")
                result = self._process_manual_content(article_title, article_content)

            else:
                raise ValueError(f"Invalid input_mode: {input_mode}")

            processing_time = round(time.time() - start_time, 2)
            result['processing_time'] = processing_time
            result['success'] = True
            result['timestamp'] = datetime.now().isoformat()

            logger.info(f"Article extraction completed in {processing_time}s")
            return result

        except Exception as e:
            logger.error(f"Article extraction failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    async def _scrape_article_from_url(self, url: str) -> Dict[str, Any]:
        """Scrape article content from URL - extracts ONLY article content"""
        try:
            async with aiohttp.ClientSession(
                timeout=aiohttp.ClientTimeout(total=self.timeout),
                headers={'User-Agent': self.user_agent}
            ) as session:
                async with session.get(url) as response:
                    if response.status != 200:
                        raise Exception(f"HTTP {response.status} error")

                    html_content = await response.text()
                    soup = BeautifulSoup(html_content, 'html.parser')

                    # Remove unwanted elements (nav, footer, header, ads, etc.)
                    for element in soup(['script', 'style', 'nav', 'footer', 'header',
                                        'aside', 'iframe', 'noscript', 'form']):
                        element.decompose()

                    # Try to find the main article content
                    article_element = (
                        soup.find('article') or
                        soup.find('main') or
                        soup.find('div', class_=lambda c: c and ('content' in c.lower() or 'article' in c.lower())) or
                        soup.find('div', id=lambda i: i and ('content' in i.lower() or 'article' in i.lower()))
                    )

                    if article_element:
                        content_soup = article_element
                    else:
                        # Fallback to body
                        content_soup = soup.find('body') or soup

                    # Extract title
                    title = None
                    title_element = content_soup.find('h1')
                    if title_element:
                        title = title_element.get_text().strip()
                    else:
                        # Fallback to page title
                        title_tag = soup.find('title')
                        if title_tag:
                            title = title_tag.get_text().strip()

                    # Extract meta description
                    meta_desc = soup.find('meta', attrs={'name': 'description'})
                    meta_description = meta_desc.get('content', '') if meta_desc else ''

                    # Get clean HTML content (preserve structure)
                    html_content = str(content_soup)

                    # Get clean text for word count
                    text_content = content_soup.get_text()
                    text_content = ' '.join(text_content.split())

                    # Count words
                    word_count = len(text_content.split())

                    return {
                        'title': title or 'Sans titre',
                        'content_html': html_content,
                        'content_text': text_content[:10000],  # Limit for safety
                        'meta_description': meta_description,
                        'word_count': word_count,
                        'source_url': url,
                        'extraction_method': 'url_scraping'
                    }

        except Exception as e:
            logger.error(f"Failed to scrape article from {url}: {str(e)}")
            raise Exception(f"Article scraping failed: {str(e)}")

    def _process_manual_content(self, title: str, content: str) -> Dict[str, Any]:
        """Process manually provided article content"""
        try:
            # Parse content if it's HTML
            soup = BeautifulSoup(content, 'html.parser')

            # Get text content
            text_content = soup.get_text()
            text_content = ' '.join(text_content.split())

            # Count words
            word_count = len(text_content.split())

            return {
                'title': title.strip(),
                'content_html': content,
                'content_text': text_content[:10000],
                'meta_description': '',  # Not available in manual mode
                'word_count': word_count,
                'source_url': None,
                'extraction_method': 'manual_input'
            }

        except Exception as e:
            logger.error(f"Failed to process manual content: {str(e)}")
            raise Exception(f"Manual content processing failed: {str(e)}")

    async def preview_article(self, url: str) -> Dict[str, Any]:
        """Preview article without full extraction (for UI preview button)"""
        try:
            result = await self._scrape_article_from_url(url)

            # Return limited preview data
            return {
                'success': True,
                'data': {
                    'title': result['title'],
                    'word_count': result['word_count'],
                    'excerpt': result['content_text'][:300]
                }
            }
        except Exception as e:
            return {
                'success': False,
                'message': str(e)
            }