"""
Workflow Manager for SEO Article Generation
Orchestrates the 4-step process:
1. Website Scraping
2. Content Analysis (LLM)
3. Article Generation (LLM)
4. Image Generation (Ideogram)
"""

import os
import asyncio
import logging
from typing import Dict, Any, Optional
from datetime import datetime

from .steps.website_scraper import WebsiteScraper
from .steps.content_analyzer import ContentAnalyzer
from .steps.article_generator import ArticleGenerator
from .steps.image_generator import ImageGenerator

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class WorkflowManager:
    """Main orchestrator for the article generation workflow"""

    def __init__(self):
        self.scraper = WebsiteScraper()
        self.analyzer = ContentAnalyzer()
        self.generator = ArticleGenerator()
        self.image_gen = ImageGenerator()

    async def execute_workflow1(self, user_data: Dict[str, Any], progress_callback=None) -> Dict[str, Any]:
        """
        Execute the complete workflow for option 1 (Create new article)

        Args:
            user_data: Data from frontend containing:
                - site_url: str
                - domain: str
                - keyword: str
                - guideline: str
                - internal_links: List[str] (optional)
                - external_links: List[str] (optional)
            progress_callback: Optional callback function(step, status, progress_percent)

        Returns:
            Complete article data with metadata
        """
        workflow_id = f"wf1_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        logger.info(f"Starting workflow {workflow_id} for site: {user_data.get('site_url')}")

        try:
            # Step 1: Website Scraping
            logger.info("Step 1: Scraping website content...")
            if progress_callback:
                progress_callback(1, 'in_progress', 10)

            scraping_result = await self.scraper.scrape_website(
                url=user_data['site_url'],
                internal_links=user_data.get('internal_links', []),
                external_links=user_data.get('external_links', [])
            )

            if not scraping_result.get('success'):
                raise Exception(f"Website scraping failed: {scraping_result.get('error')}")

            if progress_callback:
                progress_callback(1, 'completed', 25)

            # Step 2: Content Analysis
            logger.info("Step 2: Analyzing scraped content...")
            if progress_callback:
                progress_callback(2, 'in_progress', 25)

            analysis_result = await self.analyzer.analyze_content(
                scraped_data=scraping_result,
                user_context={
                    'domain': user_data['domain'],
                    'keyword': user_data['keyword'],
                    'guideline': user_data['guideline']
                }
            )

            if not analysis_result.get('success'):
                raise Exception(f"Content analysis failed: {analysis_result.get('error')}")

            if progress_callback:
                progress_callback(2, 'completed', 50)

            # Step 3: Article Generation
            logger.info("Step 3: Generating optimized article...")
            if progress_callback:
                progress_callback(3, 'in_progress', 50)

            generation_result = await self.generator.generate_article(
                scraped_data=scraping_result,
                analysis_data=analysis_result,
                user_requirements={
                    'site_url': user_data['site_url'],
                    'domain': user_data['domain'],
                    'keyword': user_data['keyword'],
                    'guideline': user_data['guideline'],
                    'internal_links': user_data.get('internal_links', []),
                    'external_links': user_data.get('external_links', [])
                }
            )

            if not generation_result.get('success'):
                raise Exception(f"Article generation failed: {generation_result.get('error')}")

            if progress_callback:
                progress_callback(3, 'completed', 75)

            # Step 4: Image Generation
            logger.info("Step 4: Generating featured image...")
            if progress_callback:
                progress_callback(4, 'in_progress', 75)

            image_result = await self.image_gen.generate_image(
                article_data=generation_result['article'],
                user_requirements={
                    'site_url': user_data['site_url'],
                    'domain': user_data['domain'],
                    'keyword': user_data['keyword'],
                    'guideline': user_data['guideline']
                }
            )

            # Add image to article (even if failed, we provide None)
            if image_result.get('success'):
                generation_result['article']['image_url'] = image_result['image_url']
                generation_result['article']['image_prompt'] = image_result.get('prompt_used', '')
                logger.info(f"Image generated successfully: {image_result['image_url']}")
            else:
                generation_result['article']['image_url'] = None
                generation_result['article']['image_prompt'] = ''
                logger.warning(f"Image generation failed: {image_result.get('error')}")

            if progress_callback:
                progress_callback(4, 'completed', 100)

            # Compile final result
            final_result = {
                'workflow_id': workflow_id,
                'status': 'success',
                'timestamp': datetime.now().isoformat(),
                'steps_completed': ['scraping', 'analysis', 'generation', 'image_generation'],
                'article': generation_result['article'],
                'metadata': {
                    'scraping_stats': scraping_result.get('stats', {}),
                    'analysis_insights': analysis_result.get('insights', {}),
                    'generation_metrics': generation_result.get('metrics', {}),
                    'image_generation': image_result if image_result.get('success') else {'success': False, 'error': image_result.get('error')}
                },
                'processing_time': {
                    'scraping': scraping_result.get('processing_time'),
                    'analysis': analysis_result.get('processing_time'),
                    'generation': generation_result.get('processing_time'),
                    'image_generation': image_result.get('processing_time', 0)
                }
            }

            logger.info(f"Workflow {workflow_id} completed successfully")
            return final_result

        except Exception as e:
            logger.error(f"Workflow {workflow_id} failed: {str(e)}")
            return {
                'workflow_id': workflow_id,
                'status': 'error',
                'error': str(e),
                'timestamp': datetime.now().isoformat()
            }

    def execute_workflow1_sync(self, user_data: Dict[str, Any], progress_callback=None) -> Dict[str, Any]:
        """Synchronous wrapper for the async workflow"""
        try:
            loop = asyncio.get_event_loop()
        except RuntimeError:
            loop = asyncio.new_event_loop()
            asyncio.set_event_loop(loop)

        return loop.run_until_complete(self.execute_workflow1(user_data, progress_callback))