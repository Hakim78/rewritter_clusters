"""
Step 1: Cluster Analyzer for Workflow 3
Analyzes the pillar article and identifies 3 satellite themes
"""

import asyncio
import aiohttp
import time
import logging
from typing import Dict, Any, List
from bs4 import BeautifulSoup
from datetime import datetime

logger = logging.getLogger(__name__)


class ClusterAnalyzer:
    """Analyzes pillar article and identifies satellite themes"""

    def __init__(self):
        self.timeout = 30
        self.user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'

    async def analyze_and_extract(self, pillar_url: str, keyword: str) -> Dict[str, Any]:
        """
        Extract pillar article and identify 3 satellite themes

        Args:
            pillar_url: URL of the pillar article to transform
            keyword: Main keyword for the cluster

        Returns:
            Dictionary with pillar data and satellite themes
        """
        start_time = time.time()

        try:
            logger.info(f"Analyzing pillar article: {pillar_url}")

            # Step 1: Extract pillar article content
            pillar_data = await self._scrape_pillar_article(pillar_url)

            # Step 2: Identify 3 satellite themes from content
            satellite_themes = self._identify_satellite_themes(
                pillar_content=pillar_data['content_text'],
                pillar_title=pillar_data['title'],
                main_keyword=keyword
            )

            processing_time = round(time.time() - start_time, 2)

            result = {
                'success': True,
                'processing_time': processing_time,
                'timestamp': datetime.now().isoformat(),
                'pillar_article': {
                    'title': pillar_data['title'],
                    'content_html': pillar_data['content_html'],
                    'content_text': pillar_data['content_text'],
                    'meta_description': pillar_data['meta_description'],
                    'word_count': pillar_data['word_count'],
                    'source_url': pillar_url
                },
                'satellite_themes': satellite_themes,
                'main_keyword': keyword
            }

            logger.info(f"Cluster analysis completed in {processing_time}s - {len(satellite_themes)} satellites identified")
            return result

        except Exception as e:
            logger.error(f"Cluster analysis failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    async def _scrape_pillar_article(self, url: str) -> Dict[str, Any]:
        """Scrape the pillar article content"""
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

                    # Remove unwanted elements
                    for element in soup(['script', 'style', 'nav', 'footer', 'header',
                                        'aside', 'iframe', 'noscript', 'form']):
                        element.decompose()

                    # Find main article content
                    article_element = (
                        soup.find('article') or
                        soup.find('main') or
                        soup.find('div', class_=lambda c: c and ('content' in c.lower() or 'article' in c.lower()))
                    )

                    content_soup = article_element if article_element else soup.find('body')

                    # Extract title
                    title = None
                    title_element = content_soup.find('h1')
                    if title_element:
                        title = title_element.get_text().strip()
                    else:
                        title_tag = soup.find('title')
                        if title_tag:
                            title = title_tag.get_text().strip()

                    # Extract meta description
                    meta_desc = soup.find('meta', attrs={'name': 'description'})
                    meta_description = meta_desc.get('content', '') if meta_desc else ''

                    # Get HTML and text content
                    html_content = str(content_soup)
                    text_content = content_soup.get_text()
                    text_content = ' '.join(text_content.split())

                    # Count words
                    word_count = len(text_content.split())

                    return {
                        'title': title or 'Sans titre',
                        'content_html': html_content,
                        'content_text': text_content[:15000],  # Limit for safety
                        'meta_description': meta_description,
                        'word_count': word_count
                    }

        except Exception as e:
            logger.error(f"Failed to scrape pillar article: {str(e)}")
            raise Exception(f"Pillar article scraping failed: {str(e)}")

    def _identify_satellite_themes(self, pillar_content: str, pillar_title: str, main_keyword: str) -> List[Dict[str, str]]:
        """
        Identify 3 complementary satellite themes based on pillar content

        This is a simplified version - in production, you could use Claude API
        to intelligently identify themes. For now, we'll use a rule-based approach.
        """

        # Extract headings and key topics from content
        # In a real implementation, you'd use Claude here for intelligent theme extraction

        # For now, return 3 predefined satellite themes structure
        # These will be populated by Claude in the satellite generation step

        satellites = [
            {
                'theme': f'Coût et rentabilité - {main_keyword}',
                'focus': 'Aspects financiers et ROI',
                'target_keyword': f'{main_keyword} prix'
            },
            {
                'theme': f'Techniques et bonnes pratiques - {main_keyword}',
                'focus': 'Méthodes d\'application et conseils pratiques',
                'target_keyword': f'{main_keyword} installation'
            },
            {
                'theme': f'Comparatif et alternatives - {main_keyword}',
                'focus': 'Comparaison avec solutions alternatives',
                'target_keyword': f'{main_keyword} vs alternatives'
            }
        ]

        logger.info(f"Identified {len(satellites)} satellite themes for cluster")
        return satellites