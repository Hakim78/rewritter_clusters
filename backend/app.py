"""
Backend Python Flask - Application principale
Fichier: backend/app.py
"""

# Ajouter le répertoire racine au PYTHONPATH pour résoudre les imports
import sys
import os


current_dir = os.path.dirname(os.path.abspath(__file__))
parent_dir = os.path.dirname(current_dir)
sys.path.insert(0, parent_dir)
sys.path.insert(0, current_dir)

from flask import Flask, request, jsonify
from flask_cors import CORS
import logging
from dotenv import load_dotenv

# Importer les routes
from backend.api.auth import auth_bp
from backend.api.admin import admin_bp
from backend.middleware.auth_middleware import token_required

# Import workflow managers
from workflows.workflow_1.workflow_manager import WorkflowManager as WorkflowManager1
from workflows.workflow_2.workflow_manager import WorkflowManager as WorkflowManager2
from workflows.workflow_3.workflow_manager import WorkflowManager as WorkflowManager3
from workflows.workflow_2.steps.article_scraper import ArticleScraper

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

# Initialize workflow managers
workflow_manager_1 = WorkflowManager1()
workflow_manager_2 = WorkflowManager2()
workflow_manager_3 = WorkflowManager3()
article_scraper = ArticleScraper()

# Store workflow progress in memory (in production, use Redis or database)
workflow_progress = {}

# ========================================
# ENREGISTREMENT DES BLUEPRINTS
# ========================================
app.register_blueprint(auth_bp)
app.register_blueprint(admin_bp)

# ========================================
# ROUTES DE TEST
# ========================================
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

# ========================================
# WORKFLOWS (PROTÉGÉS PAR AUTHENTIFICATION)
# ========================================
@app.route('/api/workflow1', methods=['POST'])
@token_required
def workflow1():
    """Option 1: Création d'un nouvel article SEO (PROTÉGÉ)"""
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
                result = workflow_manager_1.execute_workflow1_sync(data, progress_callback=update_progress)

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

@app.route('/api/preview-article', methods=['POST'])
@token_required
def preview_article():
    """Preview article content from URL"""
    try:
        data = request.get_json()
        article_url = data.get('article_url')

        if not article_url:
            return jsonify({
                'success': False,
                'message': 'URL manquante'
            }), 400

        # Use async preview method
        import asyncio
        try:
            loop = asyncio.get_event_loop()
        except RuntimeError:
            loop = asyncio.new_event_loop()
            asyncio.set_event_loop(loop)

        result = loop.run_until_complete(article_scraper.preview_article(article_url))
        return jsonify(result)

    except Exception as e:
        logger.error(f"Preview error: {str(e)}")
        return jsonify({
            'success': False,
            'message': str(e)
        }), 500

@app.route('/api/test-workflow2', methods=['POST'])
def test_workflow2():
    """Test workflow 2 without authentication"""
    try:
        data = request.get_json()

        # Process test data
        result = {
            'status': 'success',
            'message': 'Test workflow 2 réussi',
            'processed_data': {
                'input_mode': data.get('input_mode', 'url'),
                'article_url': data.get('article_url', 'N/A'),
                'article_title': data.get('article_title', 'N/A'),
                'keyword': data.get('keyword', 'N/A'),
                'internal_links_count': len(data.get('internal_links', []))
            },
            'article': {
                'seo_title': f"Article optimisé: {data.get('keyword', 'Test')}",
                'html_content': '<h1>Article de test réécrit</h1><p>Contenu optimisé SEO + LLMO + RAG...</p>',
                'word_count': 500
            }
        }

        return jsonify(result)

    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/workflow2', methods=['POST'])
@token_required
def workflow2():
    """Option 2: Réécriture d'un article existant (PROTÉGÉ)"""
    try:
        data = request.get_json()
        logger.info(f"Received workflow2 request with data: {data}")

        # Validation based on input mode
        input_mode = data.get('input_mode', 'url')

        if input_mode == 'url':
            if 'article_url' not in data or not data['article_url']:
                return jsonify({
                    'status': 'error',
                    'message': 'URL de l\'article manquante'
                }), 400
        elif input_mode == 'manual':
            if 'article_title' not in data or 'article_content' not in data:
                return jsonify({
                    'status': 'error',
                    'message': 'Titre et contenu de l\'article manquants'
                }), 400

        if 'keyword' not in data:
            return jsonify({
                'status': 'error',
                'message': 'Mot-clé manquant'
            }), 400

        # Clean internal links
        if 'internal_links' in data:
            data['internal_links'] = [link.strip() for link in data['internal_links'] if link and link.strip()]

        logger.info(f"Processing workflow2 with cleaned data: {data}")

        # Generate workflow ID
        from datetime import datetime
        workflow_id = f"wf2_{datetime.now().strftime('%Y%m%d_%H%M%S_%f')}"

        # Initialize progress tracking
        workflow_progress[workflow_id] = {
            'workflow_id': workflow_id,
            'status': 'in_progress',
            'current_step': 1,
            'total_steps': 3,
            'progress_percent': 10,
            'step_details': {
                'step_1': {'status': 'in_progress', 'name': 'Extraction du contenu'},
                'step_2': {'status': 'pending', 'name': 'Réécriture et optimisation'},
                'step_3': {'status': 'pending', 'name': 'Génération de l\'image'}
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

        # Execute workflow asynchronously
        import threading

        def execute_workflow_async():
            try:
                # Execute workflow with progress callback
                result = workflow_manager_2.execute_workflow2_sync(data, progress_callback=update_progress)

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
        logger.error(f"Workflow2 endpoint error: {str(e)}", exc_info=True)
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/workflow3', methods=['POST'])
@token_required
def workflow3():
    """Option 3: Cluster Generation - 1 Pillar + 3 Satellites (PROTÉGÉ)"""
    try:
        data = request.get_json()
        logger.info(f"Received workflow3 request with data: {data}")

        # Validation
        required_fields = ['pillar_url', 'keyword']
        for field in required_fields:
            if field not in data:
                return jsonify({
                    'status': 'error',
                    'message': f'Champ manquant: {field}'
                }), 400

        logger.info(f"Processing workflow3 with data: {data}")

        # Generate workflow ID
        from datetime import datetime
        workflow_id = f"wf3_{datetime.now().strftime('%Y%m%d_%H%M%S_%f')}"

        # Initialize progress tracking (4 steps for workflow 3)
        workflow_progress[workflow_id] = {
            'workflow_id': workflow_id,
            'status': 'in_progress',
            'current_step': 1,
            'total_steps': 4,
            'progress_percent': 10,
            'step_details': {
                'step_1': {'status': 'in_progress', 'name': 'Analyse du pilier'},
                'step_2': {'status': 'pending', 'name': 'Réécriture du pilier'},
                'step_3': {'status': 'pending', 'name': 'Génération des satellites'},
                'step_4': {'status': 'pending', 'name': 'Génération des images'}
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

        # Execute workflow asynchronously
        import threading

        def execute_workflow_async():
            try:
                # Execute workflow with progress callback
                result = workflow_manager_3.execute_workflow3_sync(data, progress_callback=update_progress)

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
        logger.error(f"Workflow3 endpoint error: {str(e)}", exc_info=True)
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

# ========================================
# GESTION DES ERREURS
# ========================================
@app.errorhandler(404)
def not_found(error):
    return jsonify({
        'success': False,
        'error': 'Route non trouvée'
    }), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({
        'success': False,
        'error': 'Erreur serveur interne'
    }), 500

if __name__ == '__main__':
    print("\n" + "="*50)
    print("🚀 Backend Flask démarré avec succès!")
    print("="*50)
    print("\n📍 Routes disponibles:")
    print("   • Test API: http://localhost:5001/api/test")
    print("   • Login: POST http://localhost:5001/api/auth/login")
    print("   • Verify: POST http://localhost:5001/api/auth/verify")
    print("   • Admin Users: GET http://localhost:5001/api/admin/users")
    print("   • Workflows: POST http://localhost:5001/api/workflow1/2/3")
    print("\n⚠️  Les workflows nécessitent maintenant un token JWT!\n")
    
    app.run(host='0.0.0.0', port=5001, debug=True)