"""
Step 1: Website Scraping Module
Scrapes website content to understand the site structure and existing content
"""

import requests
import asyncio
import aiohttp
import time
import logging
from urllib.parse import urljoin, urlparse
from typing import Dict, List, Any, Optional
from bs4 import BeautifulSoup
from datetime import datetime

logger = logging.getLogger(__name__)

class WebsiteScraper:
    """Handles website content scraping and analysis"""

    def __init__(self):
        self.session = None
        self.timeout = 30
        self.max_retries = 3
        self.user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'

    async def scrape_website(self, url: str, internal_links: List[str] = None, external_links: List[str] = None) -> Dict[str, Any]:
        """
        Scrape the main website and additional links

        Args:
            url: Main website URL to scrape
            internal_links: List of internal pages to scrape
            external_links: List of external reference links

        Returns:
            Dictionary containing scraped content and metadata
        """
        start_time = time.time()
        logger.info(f"Starting website scraping for: {url}")

        try:
            async with aiohttp.ClientSession(
                timeout=aiohttp.ClientTimeout(total=self.timeout),
                headers={'User-Agent': self.user_agent}
            ) as session:
                self.session = session

                # Scrape main site
                main_content = await self._scrape_single_page(url)
                if not main_content.get('success'):
                    return {
                        'success': False,
                        'error': f"Failed to scrape main site: {main_content.get('error')}"
                    }

                # Scrape internal links
                internal_content = []
                if internal_links:
                    logger.info(f"Scraping {len(internal_links)} internal links...")
                    for link in internal_links[:5]:  # Limit to 5 internal links
                        try:
                            content = await self._scrape_single_page(link)
                            if content.get('success'):
                                internal_content.append(content)
                        except Exception as e:
                            logger.warning(f"Failed to scrape internal link {link}: {e}")

                # Analyze external links (no full scraping, just metadata)
                external_analysis = []
                if external_links:
                    logger.info(f"Analyzing {len(external_links)} external links...")
                    for link in external_links[:10]:  # Limit to 10 external links
                        try:
                            analysis = await self._analyze_external_link(link)
                            if analysis:
                                external_analysis.append(analysis)
                        except Exception as e:
                            logger.warning(f"Failed to analyze external link {link}: {e}")

                # Compile results
                processing_time = round(time.time() - start_time, 2)
                result = {
                    'success': True,
                    'processing_time': processing_time,
                    'timestamp': datetime.now().isoformat(),
                    'main_site': {
                        'url': url,
                        'content': main_content,
                        'domain': urlparse(url).netloc
                    },
                    'internal_pages': internal_content,
                    'external_references': external_analysis,
                    'stats': {
                        'main_word_count': len(main_content.get('text_content', '').split()),
                        'internal_pages_scraped': len(internal_content),
                        'external_links_analyzed': len(external_analysis),
                        'total_processing_time': processing_time
                    }
                }

                logger.info(f"Website scraping completed in {processing_time}s")
                return result

        except Exception as e:
            logger.error(f"Website scraping failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    async def _scrape_single_page(self, url: str) -> Dict[str, Any]:
        """Scrape content from a single page"""
        try:
            async with self.session.get(url) as response:
                if response.status != 200:
                    return {
                        'success': False,
                        'error': f"HTTP {response.status}",
                        'url': url
                    }

                html_content = await response.text()
                soup = BeautifulSoup(html_content, 'html.parser')

                # Remove script and style elements
                for script in soup(["script", "style", "nav", "footer", "header"]):
                    script.decompose()

                # Extract key elements
                title = soup.find('title')
                meta_description = soup.find('meta', attrs={'name': 'description'})
                h1_tags = soup.find_all('h1')
                h2_tags = soup.find_all('h2')
                h3_tags = soup.find_all('h3')

                # Get clean text content
                text_content = soup.get_text()
                text_content = ' '.join(text_content.split())  # Clean whitespace

                # Extract images
                images = []
                for img in soup.find_all('img'):
                    src = img.get('src')
                    alt = img.get('alt', '')
                    if src:
                        images.append({
                            'src': urljoin(url, src),
                            'alt': alt
                        })

                return {
                    'success': True,
                    'url': url,
                    'title': title.get_text().strip() if title else '',
                    'meta_description': meta_description.get('content', '') if meta_description else '',
                    'headings': {
                        'h1': [h.get_text().strip() for h in h1_tags],
                        'h2': [h.get_text().strip() for h in h2_tags],
                        'h3': [h.get_text().strip() for h in h3_tags]
                    },
                    'text_content': text_content[:5000],  # Limit to 5000 chars
                    'images': images[:10],  # Limit to 10 images
                    'word_count': len(text_content.split()),
                    'scraped_at': datetime.now().isoformat()
                }

        except Exception as e:
            logger.error(f"Failed to scrape {url}: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'url': url
            }

    async def _analyze_external_link(self, url: str) -> Optional[Dict[str, Any]]:
        """Analyze external link without full content scraping"""
        try:
            async with self.session.get(url) as response:
                if response.status != 200:
                    return None

                html_content = await response.text()
                soup = BeautifulSoup(html_content, 'html.parser')

                title = soup.find('title')
                meta_description = soup.find('meta', attrs={'name': 'description'})

                return {
                    'url': url,
                    'domain': urlparse(url).netloc,
                    'title': title.get_text().strip() if title else '',
                    'meta_description': meta_description.get('content', '') if meta_description else '',
                    'analyzed_at': datetime.now().isoformat()
                }

        except Exception as e:
            logger.warning(f"Failed to analyze external link {url}: {e}")
            return None