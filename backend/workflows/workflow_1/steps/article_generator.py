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

logger = logging.getLogger(__name__)

class ArticleGenerator:
    """Generates optimized articles using Claude with 4-expert approach: SEO, People First, LLMO, RAG"""

    def __init__(self):
        self.client = Anthropic(api_key=os.getenv('ANTHROPIC_API_KEY'))
        self.model = "claude-sonnet-4-5-20250929"  # Claude Sonnet 4.5
        self.max_tokens = 8000

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
        """Build the comprehensive generation prompt with 4-expert methodology"""

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

        prompt = f"""
Tu es un expert en rédaction SEO combinant 4 expertises : SEO technique, People First, LLMO (Large Language Model Optimization), et RAG-Friendly content.

## OBJECTIF
Rédiger un article expert, long, structuré et durable, optimisé simultanément pour :
1. Les moteurs de recherche (SEO)
2. Les lecteurs humains (People First)
3. Les IA génératives (LLMO)
4. L'indexation dans des bases RAG

## CONTEXTE UTILISATEUR
- **Domaine d'activité** : {user_requirements.get('domain', 'Non spécifié')}
- **Mot-clé principal** : {user_requirements.get('keyword', 'Non spécifié')}
- **Brief utilisateur** : {user_requirements.get('guideline', 'Non spécifié')}
- **URL du site** : {user_requirements.get('site_url', 'Non spécifié')}

## ANALYSE DU SITE EXISTANT
### Structure actuelle :
- Ton du contenu : {insights.get('content_tone', 'À déterminer')}
- Audience cible : {insights.get('target_audience', 'À déterminer')}
- Thèmes principaux : {', '.join(insights.get('main_topics', [])[:5])}

### Opportunités identifiées :
{self._format_list(insights.get('seo_opportunities', []), 'SEO')}

### Lacunes de contenu :
{self._format_list(insights.get('content_gaps', []), 'Lacunes')}

### Stratégie de contenu recommandée :
{insights.get('content_strategy', 'Développer un contenu approfondi et structuré')}

### Mots-clés secondaires suggérés :
{', '.join(insights.get('keyword_opportunities', [])[:10])}

## LIENS À INTÉGRER
### Liens internes à utiliser (2-4 liens naturels) :
{internal_links_formatted}

### Références externes pour contexte :
{external_refs_formatted}

## RÈGLES DE RÉDACTION (4 EXPERTS COMBINÉS)

### 1. EXPERT SEO TECHNIQUE
- Structure HTML propre SANS balise <h1> (réservée au CMS)
- Hiérarchie : <h2> pour sections principales, <h3> pour sous-sections
- Balises sémantiques : <ul>, <li>, <strong>, <p>
- Titre SEO : MAX 60 caractères, inclure le mot-clé principal
- Meta description : MAX 155 caractères, claire et incitative
- Extrait WordPress : 2 lignes orientées bénéfices métier
- Intégrer 2-4 liens internes avec ancres naturelles (utilise les liens fournis)
- Densité de mots-clés : naturelle, pas de sur-optimisation
- Mots-clés longue traîne et entités sémantiques
- Prévoir schema.org (Article, FAQ si pertinent)
- Mentionner dates/tendances/évolutions récentes (2024-2025)

### 2. EXPERT PEOPLE FIRST
- Priorité = VALEUR pour l'utilisateur, pas pour l'algorithme
- Style pédagogique, clair, accessible, sans jargon inutile
- Répondre à des questions réelles avec informations concrètes
- Paragraphes courts (3-4 phrases maximum)
- Ton professionnel, crédible, jamais artificiel ou marketing
- Exemples concrets, cas pratiques, contexte réel
- Pas d'emoji, pas de superlatifs excessifs
- Copywriting orienté bénéfices immédiats

### 3. EXPERT LLMO (Large Language Model Optimization)
- Chaque section = autonome (titre explicite + réponse complète)
- Mot-clé principal dans titre + première phrase de chaque section
- Formats facilement extractibles par IA :
  * Listes numérotées
  * Tableaux comparatifs HTML (<table>)
  * Définitions claires
- Entités précises (noms propres, normes, lieux, concepts)
- Éviter formulations vagues, toujours privilégier la précision
- FAQ obligatoire : 3-5 questions, réponses courtes et factuelles

### 4. EXPERT RAG-FRIENDLY
- Contenu en blocs autonomes (réutilisables indépendamment)
- Éviter dépendances de contexte ("comme vu ci-dessus", "voir plus loin")
- Langage clair, phrases affirmatives, sans ambiguïté
- Créer toile thématique : liens vers autres contenus
- Données fraîches, vérifiables, horodatées (2024-2025)
- Formats normalisés (FAQ, listes, tableaux)
- Structure permettant extraction facile par embeddings

## FORMAT DE RÉPONSE EXIGÉ

Fournis ta réponse dans ce format EXACT (utilise ces balises XML) :

<SEO_TITLE>
Titre SEO de 50-60 caractères incluant le mot-clé principal
</SEO_TITLE>

<META_DESCRIPTION>
Meta description de 140-155 caractères, claire, incitative, avec mot-clé
</META_DESCRIPTION>

<WORDPRESS_EXCERPT>
Extrait WordPress sur 2 lignes orienté bénéfices métier concrets
</WORDPRESS_EXCERPT>

<HTML_CONTENT>
<!-- Article complet en HTML structuré -->
<!-- Commence par <h2> (PAS de <h1>) -->
<!-- Introduction de 2-3 paragraphes -->

<h2>Première section principale</h2>
<p>Paragraphe intro avec mot-clé principal dans la première phrase...</p>

<h3>Sous-section détaillée</h3>
<p>Contenu pédagogique...</p>
<ul>
<li><strong>Point clé 1</strong> : explication concrète</li>
<li><strong>Point clé 2</strong> : exemple pratique</li>
</ul>

<h2>Deuxième section principale</h2>
<!-- Continue avec structure claire -->

<!-- Intègre 2-4 liens internes naturellement dans le texte -->
<!-- Exemple : <a href="URL_INTERNE">texte d'ancre naturel</a> -->

<!-- Ajoute des tableaux si pertinent : -->
<table>
<thead>
<tr><th>Critère</th><th>Description</th></tr>
</thead>
<tbody>
<tr><td>Critère 1</td><td>Explication</td></tr>
</tbody>
</table>

</HTML_CONTENT>

<FAQ_SECTION>
<!-- FAQ en HTML avec 3-5 questions -->
<div class="faq">
<h2>Questions fréquentes</h2>

<div class="faq-item">
<h3>Question 1 précise et courte ?</h3>
<p>Réponse factuelle courte (2-3 phrases max).</p>
</div>

<div class="faq-item">
<h3>Question 2 précise et courte ?</h3>
<p>Réponse factuelle courte.</p>
</div>

<!-- 3-5 questions total -->
</div>
</FAQ_SECTION>

<FAQ_JSON>
[
  {{
    "question": "Question 1 précise et courte ?",
    "answer": "Réponse factuelle courte (2-3 phrases max)."
  }},
  {{
    "question": "Question 2 précise et courte ?",
    "answer": "Réponse factuelle courte."
  }}
]
</FAQ_JSON>

<SECONDARY_KEYWORDS>
mot-clé secondaire 1, mot-clé secondaire 2, mot-clé secondaire 3
</SECONDARY_KEYWORDS>

<ENTITIES>
entité 1, entité 2, entité 3, entité 4
</ENTITIES>

<INTERNAL_LINKS_USED>
URL1, URL2, URL3
</INTERNAL_LINKS_USED>

<SCHEMA_MARKUP>
{{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Titre de l'article",
  "description": "Description courte",
  "author": {{
    "@type": "Organization",
    "name": "Nom du site"
  }},
  "datePublished": "{datetime.now().strftime('%Y-%m-%d')}",
  "dateModified": "{datetime.now().strftime('%Y-%m-%d')}"
}}
</SCHEMA_MARKUP>

<READABILITY_SCORE>
Facile / Moyen / Expert (auto-évaluation)
</READABILITY_SCORE>

## CONSIGNES FINALES

1. **Longueur** : Article de 1500-2500 mots minimum
2. **Qualité** : Chaque phrase doit apporter de la valeur
3. **Structure** : Utilise titres, listes, tableaux pour faciliter la lecture
4. **Liens internes** : Intègre 2-4 liens de manière naturelle dans le texte
5. **Actualité** : Mentionne tendances/dates/évolutions 2024-2025
6. **Ton** : Professionnel, expert, pédagogique, jamais marketing ou artificiel
7. **SEO** : Naturel, pas de bourrage de mots-clés
8. **Pas d'emoji** : Rédaction professionnelle pure
9. **IMPORTANT** : Tu DOIS inclure les sections FAQ_SECTION ET FAQ_JSON dans ta réponse

⚠️ RAPPEL CRITIQUE : Fournis TOUTES les sections demandées ci-dessus, y compris :
- SEO_TITLE
- META_DESCRIPTION
- WORDPRESS_EXCERPT
- HTML_CONTENT
- FAQ_SECTION (HTML)
- FAQ_JSON (format JSON strict)
- SECONDARY_KEYWORDS
- ENTITIES
- INTERNAL_LINKS_USED
- SCHEMA_MARKUP
- READABILITY_SCORE

COMMENCE LA RÉDACTION MAINTENANT.
"""
        return prompt

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