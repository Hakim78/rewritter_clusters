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

from flask import Flask, request, jsonify, g
from flask_cors import CORS
import logging
from dotenv import load_dotenv

# Importer les routes
from backend.api.auth import auth_bp
from backend.api.admin import admin_bp
from backend.middleware.auth_middleware import token_required
from backend.routes.workflow_routes import workflow_bp

# Import Celery tasks
from celery_tasks.workflow_tasks import workflow1_task, workflow2_task, workflow3_task

# Import workflow service
from services.workflow_service import WorkflowService

# Import workflow managers (pour preview seulement)
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
CORS(app)

# Configuration
app.config['DEBUG'] = True
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'dev-secret-key')

# Initialize services
article_scraper = ArticleScraper()
workflow_service = WorkflowService()

# ========================================
# ENREGISTREMENT DES BLUEPRINTS
# ========================================
app.register_blueprint(auth_bp)
app.register_blueprint(admin_bp)
app.register_blueprint(workflow_bp, url_prefix='/api')

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
    """Get real-time progress of a workflow via Celery"""
    try:
        from celery_config import celery_app
        
        # Récupérer le résultat de la task Celery
        task = celery_app.AsyncResult(workflow_id)
        
        if task.state == 'PENDING':
            response = {
                'status': 'pending',
                'current_step': 0,
                'progress_percent': 0
            }
        elif task.state == 'PROGRESS':
            response = {
                'status': 'in_progress',
                'current_step': task.info.get('current_step', 0),
                'total_steps': task.info.get('total_steps', 4),
                'progress_percent': task.info.get('progress', 0)
            }
        elif task.state == 'SUCCESS':
            response = {
                'status': 'completed',
                'result': task.result
            }
        elif task.state == 'FAILURE':
            response = {
                'status': 'error',
                'error': str(task.info)
            }
        else:
            response = {
                'status': task.state.lower(),
                'info': str(task.info)
            }
        
        return jsonify(response)
        
    except Exception as e:
        logger.error(f"Error getting workflow progress: {str(e)}")
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/test-post', methods=['POST'])
def test_post():
    """Test d'envoi de données depuis PHP"""
    data = request.get_json()
    domain = data.get('domain', 'N/A')
    if domain == 'Autre' and data.get('custom_domain'):
        domain = data.get('custom_domain')

    internal_links = data.get('internal_links', [])
    external_links = data.get('external_links', [])

    links_info = ""
    if internal_links:
        links_info += f"<p><strong>Liens internes:</strong> {', '.join(internal_links)}</p>"
    if external_links:
        links_info += f"<p><strong>Liens externes:</strong> {', '.join(external_links)}</p>"

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
# WORKFLOWS (PROTÉGÉS PAR AUTHENTIFICATION) - CELERY
# ========================================
@app.route('/api/workflow1', methods=['POST'])
@token_required
def workflow1():
    """Option 1: Création d'un nouvel article SEO (PROTÉGÉ) - CELERY"""
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

        # Traitement des liens
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

        # Lancer la task Celery
        task = workflow1_task.apply_async(
            args=[workflow_id, g.user_id, data],
            task_id=workflow_id
        )

        return jsonify({
            'status': 'started',
            'workflow_id': workflow_id,
            'task_id': task.id,
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
    """Option 2: Réécriture d'un article existant (PROTÉGÉ) - CELERY"""
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

        # Lancer la task Celery
        task = workflow2_task.apply_async(
            args=[workflow_id, g.user_id, data],
            task_id=workflow_id
        )

        return jsonify({
            'status': 'started',
            'workflow_id': workflow_id,
            'task_id': task.id,
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
    """Option 3: Cluster Generation - 1 Pillar + 3 Satellites (PROTÉGÉ) - CELERY"""
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

        # Lancer la task Celery
        task = workflow3_task.apply_async(
            args=[workflow_id, g.user_id, data],
            task_id=workflow_id
        )

        return jsonify({
            'status': 'started',
            'workflow_id': workflow_id,
            'task_id': task.id,
            'message': 'Workflow started successfully'
        })

    except Exception as e:
        logger.error(f"Workflow3 endpoint error: {str(e)}", exc_info=True)
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

# ========================================
# ROUTES WORKFLOWS HISTORIQUE
# ========================================
@app.route('/api/workflows/user/<int:user_id>', methods=['GET'])
@token_required
def get_user_workflows(user_id):
    """Récupérer les workflows d'un utilisateur"""
    try:
        # Vérifier que l'user demande ses propres workflows ou est admin
        if g.user_id != user_id and g.user_role != 'admin':
            return jsonify({
                'status': 'error',
                'message': 'Accès non autorisé'
            }), 403

        limit = int(request.args.get('limit', 50))
        workflows = workflow_service.get_user_workflows(user_id, limit)

        return jsonify({
            'status': 'success',
            'workflows': workflows,
            'count': len(workflows)
        })
    except Exception as e:
        logger.error(f"Error fetching workflows: {str(e)}")
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/workflows/<workflow_id>', methods=['GET'])
@token_required
def get_workflow_detail(workflow_id):
    """Récupérer les détails d'un workflow"""
    try:
        # TODO: Implémenter la récupération depuis MinIO
        return jsonify({
            'status': 'success',
            'message': 'Route en développement'
        })
    except Exception as e:
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
    print("🚀 Backend Flask avec Celery démarré!")
    print("="*50)
    print("\n📍 Routes disponibles:")
    print("   • Test API: http://localhost:5001/api/test")
    print("   • Login: POST http://localhost:5001/api/auth/login")
    print("   • Verify: POST http://localhost:5001/api/auth/verify")
    print("   • Admin Users: GET http://localhost:5001/api/admin/users")
    print("   • Workflows: POST http://localhost:5001/api/workflow1/2/3")
    print("\n⚡ Celery + Redis activés!")
    print("   • Workers: 10 concurrent")
    print("   • Flower: http://localhost:5555/flower\n")

    app.run(host='0.0.0.0', port=5001, debug=True)
