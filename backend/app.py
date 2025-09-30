"""
Backend Python Flask - Application de Test
Fichier: backend/app.py
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import logging
from dotenv import load_dotenv

# Import workflow manager
from workflows.workflow_1.workflow_manager import WorkflowManager

# Charger les variables d'environnement
load_dotenv()

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Permettre les requêtes cross-origin depuis PHP

# Configuration
app.config['DEBUG'] = True
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'dev-secret-key')

# Initialize workflow manager
workflow_manager = WorkflowManager()

# Store workflow progress in memory (in production, use Redis or database)
workflow_progress = {}

# Route de test basique
@app.route('/api/test', methods=['GET'])
def test():
    """Route de test pour vérifier que l'API fonctionne"""
    return jsonify({
        'status': 'success',
        'message': 'Backend Python Flask fonctionne correctement!',
        'version': '1.0.0'
    })

@app.route('/api/workflow-progress/<workflow_id>', methods=['GET'])
def get_workflow_progress(workflow_id):
    """Get real-time progress of a workflow"""
    if workflow_id in workflow_progress:
        return jsonify(workflow_progress[workflow_id])
    else:
        return jsonify({
            'status': 'not_found',
            'message': 'Workflow not found'
        }), 404

# Route de test avec données POST
@app.route('/api/test-post', methods=['POST'])
def test_post():
    """Test d'envoi de données depuis PHP"""
    data = request.get_json()

    # Gestion du domaine personnalisé
    domain = data.get('domain', 'N/A')
    if domain == 'Autre' and data.get('custom_domain'):
        domain = data.get('custom_domain')

    # Gestion des liens
    internal_links = data.get('internal_links', [])
    external_links = data.get('external_links', [])

    links_info = ""
    if internal_links:
        links_info += f"<p><strong>Liens internes:</strong> {', '.join(internal_links)}</p>"
    if external_links:
        links_info += f"<p><strong>Liens externes:</strong> {', '.join(external_links)}</p>"

    # Traitement des données pour test
    processed_data = {
        'processed_title': f"Article de test sur : {data.get('keyword', 'sujet inconnu')}",
        'processed_content': f"""<h1>Article de test</h1>
            <p>Votre demande concernant '{data.get('keyword', 'N/A')}' a été reçue et traitée.</p>
            <p><strong>Site web:</strong> {data.get('site_url', 'N/A')}</p>
            <p><strong>Domaine:</strong> {domain}</p>
            <p><strong>Brief:</strong> {data.get('guideline', 'N/A')}</p>
            {links_info}""",
        'timestamp': __import__('datetime').datetime.now().isoformat(),
        'links_processed': {
            'internal_count': len(internal_links),
            'external_count': len(external_links),
            'total_links': len(internal_links) + len(external_links)
        }
    }

    return jsonify({
        'status': 'success',
        'message': 'Données reçues et traitées avec succès',
        'received_data': data,
        'processed_data': processed_data,
        'data_type': type(data).__name__
    })

# Route pour workflow 1 (création d'article)
@app.route('/api/workflow1', methods=['POST'])
def workflow1():
    """Option 1: Création d'un nouvel article SEO"""
    try:
        data = request.get_json()
        logger.info(f"Received workflow1 request with data: {data}")

        # Validation basique
        required_fields = ['site_url', 'domain', 'guideline', 'keyword']
        for field in required_fields:
            if field not in data:
                return jsonify({
                    'status': 'error',
                    'message': f'Champ manquant: {field}'
                }), 400

        # Gestion du domaine personnalisé
        if data.get('domain') == 'Autre' and data.get('custom_domain'):
            data['domain'] = data.get('custom_domain')

        # Traitement des liens (s'assurer qu'ils sont des listes)
        if 'internal_links[]' in data:
            data['internal_links'] = data.pop('internal_links[]') if isinstance(data.get('internal_links[]'), list) else [data.pop('internal_links[]')]

        if 'external_links[]' in data:
            data['external_links'] = data.pop('external_links[]') if isinstance(data.get('external_links[]'), list) else [data.pop('external_links[]')]

        # Nettoyer les liens vides
        if 'internal_links' in data:
            data['internal_links'] = [link.strip() for link in data['internal_links'] if link and link.strip()]

        if 'external_links' in data:
            data['external_links'] = [link.strip() for link in data['external_links'] if link and link.strip()]

        logger.info(f"Processing workflow1 with cleaned data: {data}")

        # Generate workflow ID
        from datetime import datetime
        workflow_id = f"wf1_{datetime.now().strftime('%Y%m%d_%H%M%S_%f')}"

        # Initialize progress tracking
        workflow_progress[workflow_id] = {
            'workflow_id': workflow_id,
            'status': 'in_progress',
            'current_step': 1,
            'total_steps': 4,
            'progress_percent': 10,
            'step_details': {
                'step_1': {'status': 'in_progress', 'name': 'Analyse du site'},
                'step_2': {'status': 'pending', 'name': 'Analyse stratégique'},
                'step_3': {'status': 'pending', 'name': 'Rédaction de l\'article'},
                'step_4': {'status': 'pending', 'name': 'Génération de l\'image'}
            }
        }

        # Progress callback
        def update_progress(step, status, progress_percent=None):
            if workflow_id in workflow_progress:
                workflow_progress[workflow_id]['current_step'] = step
                workflow_progress[workflow_id]['step_details'][f'step_{step}']['status'] = status
                if progress_percent:
                    workflow_progress[workflow_id]['progress_percent'] = progress_percent
                logger.info(f"Progress update: Workflow {workflow_id} - Step {step} - {status}")

        # Return workflow_id immediately for client to poll
        import threading

        def execute_workflow_async():
            try:
                # Execute workflow with progress callback
                result = workflow_manager.execute_workflow1_sync(data, progress_callback=update_progress)

                # Store final result
                if result.get('status') == 'success':
                    workflow_progress[workflow_id].update({
                        'status': 'completed',
                        'result': result
                    })
                else:
                    workflow_progress[workflow_id].update({
                        'status': 'error',
                        'error': result.get('error', 'Unknown error')
                    })

            except Exception as e:
                logger.error(f"Workflow {workflow_id} failed: {str(e)}", exc_info=True)
                workflow_progress[workflow_id].update({
                    'status': 'error',
                    'error': str(e)
                })

        # Start workflow in background thread
        thread = threading.Thread(target=execute_workflow_async)
        thread.start()

        # Return workflow ID for polling
        return jsonify({
            'status': 'started',
            'workflow_id': workflow_id,
            'message': 'Workflow started successfully'
        })

    except Exception as e:
        logger.error(f"Workflow1 endpoint error: {str(e)}", exc_info=True)
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

# Route pour workflow 2 (réécriture article)
@app.route('/api/workflow2', methods=['POST'])
def workflow2():
    """Option 2: Réécriture d'un article existant"""
    try:
        data = request.get_json()
        
        if 'article_url' not in data:
            return jsonify({
                'status': 'error',
                'message': 'URL de l\'article manquante'
            }), 400
        
        # Simulation
        result = {
            'status': 'success',
            'message': 'Article réécrit avec succès',
            'article': {
                'title': 'Article réécrit',
                'html_content': '<h1>Article optimisé</h1><p>Contenu réécrit...</p>',
                'image_url': 'https://via.placeholder.com/800x400',
                'improvements': ['SEO optimisé', 'People-first', 'RAG LLMO compatible']
            }
        }
        
        return jsonify(result)
        
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

# Route pour workflow 3 (cluster d'articles)
@app.route('/api/workflow3', methods=['POST'])
def workflow3():
    """Option 3: Création d'un cluster de 3 articles"""
    try:
        data = request.get_json()
        
        if 'article_url' not in data:
            return jsonify({
                'status': 'error',
                'message': 'URL de l\'article manquante'
            }), 400
        
        # Simulation
        result = {
            'status': 'success',
            'message': 'Cluster créé avec succès',
            'articles': [
                {
                    'type': 'main',
                    'title': 'Article principal réécrit',
                    'html_content': '<h1>Article principal</h1>',
                    'image_url': 'https://via.placeholder.com/800x400'
                },
                {
                    'type': 'satellite',
                    'title': 'Article satellite 1',
                    'html_content': '<h1>Article lié 1</h1>',
                    'image_url': 'https://via.placeholder.com/800x400'
                },
                {
                    'type': 'satellite',
                    'title': 'Article satellite 2',
                    'html_content': '<h1>Article lié 2</h1>',
                    'image_url': 'https://via.placeholder.com/800x400'
                }
            ],
            'internal_links': ['lien1 -> lien2', 'lien1 -> lien3']
        }
        
        return jsonify(result)
        
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001, debug=True)