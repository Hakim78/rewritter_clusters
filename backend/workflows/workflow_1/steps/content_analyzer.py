"""
Step 2: Content Analysis Module using Anthropic Claude
Analyzes scraped website content to understand structure, topics, and optimization opportunities
"""

import os
import time
import logging
from typing import Dict, List, Any
from datetime import datetime
from anthropic import Anthropic

logger = logging.getLogger(__name__)

class ContentAnalyzer:
    """Analyzes website content using Claude to understand structure and extract insights"""

    def __init__(self):
        self.client = Anthropic(api_key=os.getenv('ANTHROPIC_API_KEY'))
        self.model = "claude-sonnet-4-5-20250929"  # Claude Sonnet 4.5
        self.max_tokens = 4000

    async def analyze_content(self, scraped_data: Dict[str, Any], user_context: Dict[str, Any]) -> Dict[str, Any]:
        """
        Analyze scraped website content to understand structure and extract insights

        Args:
            scraped_data: Output from website scraper
            user_context: User requirements (domain, keyword, guideline)

        Returns:
            Analysis results with insights and recommendations
        """
        start_time = time.time()
        logger.info("Starting content analysis with Claude...")

        try:
            # Prepare content for analysis
            analysis_prompt = self._build_analysis_prompt(scraped_data, user_context)

            # Call Claude API
            response = self.client.messages.create(
                model=self.model,
                max_tokens=self.max_tokens,
                temperature=0.3,
                messages=[
                    {
                        "role": "user",
                        "content": analysis_prompt
                    }
                ]
            )

            analysis_result = response.content[0].text

            # Parse the structured response
            insights = self._parse_analysis_response(analysis_result)

            processing_time = round(time.time() - start_time, 2)

            result = {
                'success': True,
                'processing_time': processing_time,
                'timestamp': datetime.now().isoformat(),
                'raw_analysis': analysis_result,
                'insights': insights,
                'user_context': user_context,
                'content_summary': {
                    'main_topics': insights.get('main_topics', []),
                    'content_gaps': insights.get('content_gaps', []),
                    'seo_opportunities': insights.get('seo_opportunities', []),
                    'target_audience': insights.get('target_audience', ''),
                    'content_tone': insights.get('content_tone', ''),
                    'competitor_analysis': insights.get('competitor_analysis', {})
                }
            }

            logger.info(f"Content analysis completed in {processing_time}s")
            return result

        except Exception as e:
            logger.error(f"Content analysis failed: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'processing_time': round(time.time() - start_time, 2)
            }

    def _build_analysis_prompt(self, scraped_data: Dict[str, Any], user_context: Dict[str, Any]) -> str:
        """Build the analysis prompt for Claude"""

        main_site = scraped_data.get('main_site', {})
        main_content = main_site.get('content', {})
        internal_pages = scraped_data.get('internal_pages', [])
        external_refs = scraped_data.get('external_references', [])

        prompt = f"""
Tu es un expert en analyse de contenu web et SEO. Analyse le contenu du site web suivant et fournis des insights détaillés.

## CONTEXTE UTILISATEUR:
- Domaine d'activité: {user_context.get('domain', 'Non spécifié')}
- Mot-clé principal: {user_context.get('keyword', 'Non spécifié')}
- Brief utilisateur: {user_context.get('guideline', 'Non spécifié')}

## CONTENU DU SITE PRINCIPAL:
URL: {main_site.get('url', 'Non disponible')}
Titre: {main_content.get('title', 'Non disponible')}
Meta description: {main_content.get('meta_description', 'Non disponible')}

Structure des titres:
- H1: {', '.join(main_content.get('headings', {}).get('h1', []))}
- H2: {', '.join(main_content.get('headings', {}).get('h2', [])[:5])}
- H3: {', '.join(main_content.get('headings', {}).get('h3', [])[:5])}

Contenu textuel (extrait): {main_content.get('text_content', '')[:2000]}
Nombre de mots: {main_content.get('word_count', 0)}

## PAGES INTERNES ANALYSÉES:
{self._format_internal_pages(internal_pages)}

## RÉFÉRENCES EXTERNES:
{self._format_external_references(external_refs)}

## ANALYSE DEMANDÉE:

Analyse ce contenu et fournis une réponse structurée avec les sections suivantes (utilise exactement ces balises):

<MAIN_TOPICS>
Liste les 5 principaux sujets/thèmes abordés sur le site
</MAIN_TOPICS>

<TARGET_AUDIENCE>
Décris l'audience cible du site basée sur le contenu analysé
</TARGET_AUDIENCE>

<CONTENT_TONE>
Identifie le ton et le style du contenu (professionnel, décontracté, technique, etc.)
</CONTENT_TONE>

<SEO_OPPORTUNITIES>
Liste les opportunités d'amélioration SEO identifiées
</SEO_OPPORTUNITIES>

<CONTENT_GAPS>
Identifie les lacunes de contenu par rapport au mot-clé cible et au brief
</CONTENT_GAPS>

<COMPETITOR_ANALYSIS>
Analyse comparative basée sur les références externes
</COMPETITOR_ANALYSIS>

<CONTENT_STRATEGY>
Recommandations stratégiques pour le nouvel article à créer
</CONTENT_STRATEGY>

<KEYWORD_OPPORTUNITIES>
Suggestions de mots-clés secondaires et sémantiques pertinents
</KEYWORD_OPPORTUNITIES>
"""
        return prompt

    def _format_internal_pages(self, internal_pages: List[Dict]) -> str:
        """Format internal pages data for the prompt"""
        if not internal_pages:
            return "Aucune page interne analysée"

        formatted = []
        for page in internal_pages[:3]:  # Limit to 3 pages
            if page.get('success'):
                formatted.append(f"- {page.get('url')}: {page.get('title', 'Titre non disponible')}")

        return '\n'.join(formatted) if formatted else "Aucune page interne analysée"

    def _format_external_references(self, external_refs: List[Dict]) -> str:
        """Format external references for the prompt"""
        if not external_refs:
            return "Aucune référence externe"

        formatted = []
        for ref in external_refs[:5]:  # Limit to 5 references
            formatted.append(f"- {ref.get('domain')}: {ref.get('title', 'Titre non disponible')}")

        return '\n'.join(formatted) if formatted else "Aucune référence externe"

    def _parse_analysis_response(self, response: str) -> Dict[str, Any]:
        """Parse the structured response from Claude"""
        insights = {}

        # Extract sections using regex-like approach
        sections = {
            'main_topics': 'MAIN_TOPICS',
            'target_audience': 'TARGET_AUDIENCE',
            'content_tone': 'CONTENT_TONE',
            'seo_opportunities': 'SEO_OPPORTUNITIES',
            'content_gaps': 'CONTENT_GAPS',
            'competitor_analysis': 'COMPETITOR_ANALYSIS',
            'content_strategy': 'CONTENT_STRATEGY',
            'keyword_opportunities': 'KEYWORD_OPPORTUNITIES'
        }

        for key, tag in sections.items():
            try:
                start_tag = f"<{tag}>"
                end_tag = f"</{tag}>"
                start_idx = response.find(start_tag)
                end_idx = response.find(end_tag)

                if start_idx != -1 and end_idx != -1:
                    content = response[start_idx + len(start_tag):end_idx].strip()

                    # Convert list-like content to arrays
                    if key in ['main_topics', 'seo_opportunities', 'content_gaps', 'keyword_opportunities']:
                        # Split by lines and clean up
                        items = [item.strip('- •').strip() for item in content.split('\n') if item.strip()]
                        insights[key] = [item for item in items if item]
                    else:
                        insights[key] = content

            except Exception as e:
                logger.warning(f"Failed to parse section {key}: {e}")
                insights[key] = ""

        return insights