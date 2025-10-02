"""
Workflow Manager for Article Rewriting (Workflow 2)
Orchestrates the 3-step process:
1. Article Extraction (URL or manual)
2. Article Rewriting & Optimization (LLM)
3. Image Generation (Ideogram)
"""

import os
import asyncio
import logging
from typing import Dict, Any, Optional
from datetime import datetime

from .steps.article_scraper import ArticleScraper
from .steps.article_rewriter import ArticleRewriter
from .steps.image_generator import ImageGenerator

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


class WorkflowManager:
    """Main orchestrator for the article rewriting workflow"""

    def __init__(self):
        self.scraper = ArticleScraper()
        self.rewriter = ArticleRewriter()
        self.image_gen = ImageGenerator()

    async def execute_workflow2(self, user_data: Dict[str, Any], progress_callback=None) -> Dict[str, Any]:
        """
        Execute the complete workflow for option 2 (Rewrite existing article)

        Args:
            user_data: Data from frontend containing:
                - input_mode: 'url' or 'manual'
                - article_url: str (if mode is 'url')
                - article_title: str (if mode is 'manual')
                - article_content: str (if mode is 'manual')
                - keyword: str
                - internal_links: List[str] (optional)
            progress_callback: Optional callback function(step, status, progress_percent)

        Returns:
            Rewritten article data with metadata
        """
        workflow_id = f"wf2_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        logger.info(f"Starting workflow {workflow_id} for article rewriting")

        try:
            # Step 1: Article Extraction
            logger.info("Step 1: Extracting article content...")
            if progress_callback:
                progress_callback(1, 'in_progress', 10)

            extraction_result = await self.scraper.extract_article(
                input_mode=user_data.get('input_mode', 'url'),
                article_url=user_data.get('article_url'),
                article_title=user_data.get('article_title'),
                article_content=user_data.get('article_content')
            )

            if not extraction_result.get('success'):
                raise Exception(f"Article extraction failed: {extraction_result.get('error')}")

            if progress_callback:
                progress_callback(1, 'completed', 33)

            # Step 2: Article Rewriting
            logger.info("Step 2: Rewriting and optimizing article...")
            if progress_callback:
                progress_callback(2, 'in_progress', 33)

            rewriting_result = await self.rewriter.rewrite_article(
                article_data=extraction_result,
                user_requirements={
                    'keyword': user_data.get('keyword', ''),
                    'internal_links': user_data.get('internal_links', [])
                }
            )

            if not rewriting_result.get('success'):
                raise Exception(f"Article rewriting failed: {rewriting_result.get('error')}")

            if progress_callback:
                progress_callback(2, 'completed', 66)

            # Step 3: Image Generation
            logger.info("Step 3: Generating new featured image...")
            if progress_callback:
                progress_callback(3, 'in_progress', 66)

            image_result = await self.image_gen.generate_image(
                article_data=rewriting_result['article'],
                user_requirements={
                    'keyword': user_data.get('keyword', ''),
                    'guideline': f"Réécriture optimisée de l'article: {extraction_result.get('title', '')}"
                }
            )

            # Add image to article (even if failed, we provide None)
            if image_result.get('success'):
                rewriting_result['article']['image_url'] = image_result['image_url']
                rewriting_result['article']['image_prompt'] = image_result.get('prompt_used', '')
                logger.info(f"Image generated successfully: {image_result['image_url']}")
            else:
                rewriting_result['article']['image_url'] = None
                rewriting_result['article']['image_prompt'] = ''
                logger.warning(f"Image generation failed: {image_result.get('error')}")

            if progress_callback:
                progress_callback(3, 'completed', 100)

            # Compile final result
            final_result = {
                'workflow_id': workflow_id,
                'status': 'success',
                'timestamp': datetime.now().isoformat(),
                'steps_completed': ['extraction', 'rewriting', 'image_generation'],
                'article': rewriting_result['article'],
                'metadata': {
                    'extraction_stats': {
                        'original_word_count': extraction_result.get('word_count', 0),
                        'extraction_method': extraction_result.get('extraction_method', 'unknown')
                    },
                    'rewriting_metrics': rewriting_result.get('metrics', {}),
                    'image_generation': image_result if image_result.get('success') else {
                        'success': False,
                        'error': image_result.get('error')
                    }
                },
                'processing_time': {
                    'extraction': extraction_result.get('processing_time', 0),
                    'rewriting': rewriting_result.get('processing_time', 0),
                    'image_generation': image_result.get('processing_time', 0)
                },
                'original_article': {
                    'title': extraction_result.get('title', ''),
                    'word_count': extraction_result.get('word_count', 0),
                    'source_url': extraction_result.get('source_url')
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

    def execute_workflow2_sync(self, user_data: Dict[str, Any], progress_callback=None) -> Dict[str, Any]:
        """Synchronous wrapper for the async workflow"""
        try:
            loop = asyncio.get_event_loop()
        except RuntimeError:
            loop = asyncio.new_event_loop()
            asyncio.set_event_loop(loop)

        return loop.run_until_complete(self.execute_workflow2(user_data, progress_callback))