"""
Workflow Manager for Cluster Generation (Workflow 3)
Orchestrates: Analysis → Pillar Rewrite → 3 Satellites → Images
"""

import os
import asyncio
import logging
from typing import Dict, Any
from datetime import datetime

from .steps.cluster_analyzer import ClusterAnalyzer
from .steps.pillar_rewriter import PillarRewriter
from .steps.satellite_generator import SatelliteGenerator
from .steps.image_generator import ImageGenerator

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


class WorkflowManager:
    """Main orchestrator for cluster generation workflow"""

    def __init__(self):
        self.analyzer = ClusterAnalyzer()
        self.pillar_rewriter = PillarRewriter()
        self.satellite_generator = SatelliteGenerator()
        self.image_gen = ImageGenerator()

    async def execute_workflow3(self, user_data: Dict[str, Any], progress_callback=None) -> Dict[str, Any]:
        """
        Execute the complete cluster generation workflow

        Args:
            user_data: Data from frontend containing:
                - pillar_url: str (URL of pillar article)
                - keyword: str (main keyword)
                - generate_images: bool (optional, default True)
            progress_callback: Optional callback function(step, status, progress_percent)

        Returns:
            Cluster data with 1 pillar + 3 satellites
        """
        workflow_id = f"wf3_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        logger.info(f"Starting workflow {workflow_id} for cluster generation")

        try:
            # Step 1: Analyze pillar and identify satellite themes
            logger.info("Step 1: Analyzing pillar article...")
            if progress_callback:
                progress_callback(1, 'in_progress', 10)

            analysis_result = await self.analyzer.analyze_and_extract(
                pillar_url=user_data.get('pillar_url'),
                keyword=user_data.get('keyword')
            )

            if not analysis_result.get('success'):
                raise Exception(f"Analysis failed: {analysis_result.get('error')}")

            if progress_callback:
                progress_callback(1, 'completed', 25)

            # Step 2: Rewrite pillar article
            logger.info("Step 2: Rewriting pillar article...")
            if progress_callback:
                progress_callback(2, 'in_progress', 25)

            pillar_result = await self.pillar_rewriter.rewrite_pillar(
                pillar_data=analysis_result['pillar_article'],
                satellite_themes=analysis_result['satellite_themes'],
                main_keyword=user_data.get('keyword')
            )

            if not pillar_result.get('success'):
                raise Exception(f"Pillar rewriting failed: {pillar_result.get('error')}")

            if progress_callback:
                progress_callback(2, 'completed', 45)

            # Step 3: Generate 3 satellite articles
            logger.info("Step 3: Generating satellite articles...")
            if progress_callback:
                progress_callback(3, 'in_progress', 45)

            satellites_result = await self.satellite_generator.generate_satellites(
                pillar_data=analysis_result['pillar_article'],
                satellite_themes=analysis_result['satellite_themes'],
                main_keyword=user_data.get('keyword')
            )

            if not satellites_result.get('success'):
                raise Exception(f"Satellite generation failed: {satellites_result.get('error')}")

            if progress_callback:
                progress_callback(3, 'completed', 75)

            # Step 4: Generate images (optional)
            logger.info("Step 4: Generating images...")
            if progress_callback:
                progress_callback(4, 'in_progress', 75)

            articles = [pillar_result['pillar_article']] + satellites_result['satellites']

            if user_data.get('generate_images', True):
                # Generate images for all articles
                for i, article in enumerate(articles):
                    logger.info(f"Generating image {i+1}/{len(articles)}")

                    image_result = await self.image_gen.generate_image(
                        article_data=article,
                        user_requirements={
                            'keyword': user_data.get('keyword'),
                            'guideline': f"{article['type'].capitalize()}: {article['seo_title']}"
                        }
                    )

                    if image_result.get('success'):
                        article['image_url'] = image_result['image_url']
                        article['image_prompt'] = image_result.get('prompt_used', '')
                    else:
                        article['image_url'] = None
                        article['image_prompt'] = ''
                        logger.warning(f"Image generation failed for {article['type']}")
            else:
                logger.info("Image generation skipped")

            if progress_callback:
                progress_callback(4, 'completed', 100)

            # Build internal linking map
            internal_links_map = self._build_linking_map(articles)

            # Compile final result
            final_result = {
                'workflow_id': workflow_id,
                'status': 'success',
                'timestamp': datetime.now().isoformat(),
                'cluster': {
                    'pillar': pillar_result['pillar_article'],
                    'satellites': satellites_result['satellites'],
                    'total_articles': len(articles),
                    'internal_links_map': internal_links_map
                },
                'metadata': {
                    'main_keyword': user_data.get('keyword'),
                    'pillar_source': user_data.get('pillar_url'),
                    'satellite_themes': analysis_result['satellite_themes'],
                    'images_generated': user_data.get('generate_images', True)
                },
                'processing_time': {
                    'analysis': analysis_result.get('processing_time', 0),
                    'pillar_rewriting': pillar_result.get('processing_time', 0),
                    'satellites_generation': satellites_result.get('processing_time', 0)
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

    def _build_linking_map(self, articles: list) -> list:
        """Build internal linking structure for the cluster"""
        links = []

        # Pillar links to all satellites
        for i, sat in enumerate(articles[1:], 1):
            links.append({
                'from': 'pillar',
                'to': f'satellite_{i}',
                'anchor': sat['seo_title']
            })

        # Each satellite links to pillar
        for i in range(1, len(articles)):
            links.append({
                'from': f'satellite_{i}',
                'to': 'pillar',
                'anchor': articles[0]['seo_title']
            })

        # Satellites link to each other
        for i in range(1, len(articles)):
            for j in range(1, len(articles)):
                if i != j:
                    links.append({
                        'from': f'satellite_{i}',
                        'to': f'satellite_{j}',
                        'anchor': articles[j]['seo_title']
                    })

        return links

    def execute_workflow3_sync(self, user_data: Dict[str, Any], progress_callback=None) -> Dict[str, Any]:
        """Synchronous wrapper for the async workflow"""
        try:
            loop = asyncio.get_event_loop()
        except RuntimeError:
            loop = asyncio.new_event_loop()
            asyncio.set_event_loop(loop)

        return loop.run_until_complete(self.execute_workflow3(user_data, progress_callback))
