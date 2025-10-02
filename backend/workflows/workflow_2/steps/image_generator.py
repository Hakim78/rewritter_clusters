"""
Step 4: Image Generation Module using Ideogram API
Generates custom images based on article content
"""

import os
import time
import logging
import requests
from typing import Dict, Any
from datetime import datetime

logger = logging.getLogger(__name__)

class ImageGenerator:
    """Generates images using Ideogram API based on article content"""

    def __init__(self):
        self.api_key = os.getenv('IDEOGRAM_API_KEY')
        self.api_url = "https://api.ideogram.ai/v1/ideogram-v3/generate"
        self.style_type = "REALISTIC"
        self.resolution = "1216x704"

    async def generate_image(self,
                            article_data: Dict[str, Any],
                            user_requirements: Dict[str, Any]) -> Dict[str, Any]:
        """
        Generate an image based on article content

        Args:
            article_data: Output from article generator (step 3)
            user_requirements: User input for context

        Returns:
            Image URL and metadata
        """
        start_time = time.time()
        logger.info("Starting image generation with Ideogram...")

        try:
            # Build prompt from article
            image_prompt = self._build_image_prompt(article_data, user_requirements)

            logger.info(f"Image prompt: {image_prompt[:200]}...")

            # Call Ideogram API v3
            response = requests.post(
                self.api_url,
                headers={
                    "Api-Key": self.api_key,
                    "Content-Type": "application/json"
                },
                json={
                    "prompt": image_prompt,
                    "style_type": self.style_type,
                    "resolution": self.resolution
                },
                timeout=60
            )

            if response.status_code != 200:
                logger.error(f"Ideogram API error: {response.status_code} - {response.text}")
                raise Exception(f"Ideogram API error: {response.status_code}")

            result = response.json()
            logger.info(f"Ideogram API response: {result}")

            # Extract image URL from v3 response
            if 'data' in result and len(result['data']) > 0:
                image_url = result['data'][0].get('url')
            elif 'url' in result:
                image_url = result.get('url')
            else:
                raise Exception("No image URL returned from Ideogram API")

            processing_time = round(time.time() - start_time, 2)

            logger.info(f"Image generated successfully in {processing_time}s")

            return {
                'success': True,
                'image_url': image_url,
                'prompt_used': image_prompt,
                'style_type': self.style_type,
                'resolution': self.resolution,
                'processing_time': processing_time,
                'timestamp': datetime.now().isoformat()
            }

        except Exception as e:
            logger.error(f"Image generation failed: {str(e)}")
            processing_time = round(time.time() - start_time, 2)

            return {
                'success': False,
                'error': str(e),
                'processing_time': processing_time,
                'image_url': None
            }

    def _build_image_prompt(self,
                           article_data: Dict[str, Any],
                           user_requirements: Dict[str, Any]) -> str:
        """Build an effective image generation prompt from article content"""

        # Extract key information
        seo_title = article_data.get('seo_title', '')
        keyword = user_requirements.get('keyword', '')
        domain = user_requirements.get('domain', '')

        # Extract first paragraph from HTML content for context
        html_content = article_data.get('html_content', '')

        # Simple extraction of first meaningful paragraph
        import re
        paragraphs = re.findall(r'<p>(.*?)</p>', html_content, re.DOTALL)
        first_paragraph = ''
        if paragraphs:
            # Clean HTML tags from first paragraph
            first_paragraph = re.sub(r'<[^>]+>', '', paragraphs[0])[:200]

        # Build a descriptive prompt for image generation
        prompt = f"""Professional, high-quality featured image for an article about {keyword}.

Topic: {seo_title}

Context: {first_paragraph if first_paragraph else domain}

Style: Modern, clean, professional business illustration. Vibrant colors. Suitable for a blog article header.

Requirements:
- High resolution, photorealistic or professional illustration style
- No text or typography in the image
- Suitable for web article featured image
- Visually appealing and professional
- Related to: {keyword}"""

        # Truncate if too long (Ideogram has limits)
        if len(prompt) > 1000:
            prompt = prompt[:1000]

        return prompt

    def download_image(self, image_url: str, save_path: str) -> bool:
        """
        Download image from URL to local path

        Args:
            image_url: URL of the image
            save_path: Local path to save image

        Returns:
            Success boolean
        """
        try:
            response = requests.get(image_url, timeout=30)
            if response.status_code == 200:
                os.makedirs(os.path.dirname(save_path), exist_ok=True)
                with open(save_path, 'wb') as f:
                    f.write(response.content)
                logger.info(f"Image downloaded to {save_path}")
                return True
            else:
                logger.error(f"Failed to download image: {response.status_code}")
                return False
        except Exception as e:
            logger.error(f"Image download failed: {str(e)}")
            return False