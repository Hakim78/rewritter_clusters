"""
Routes API pour la gestion des workflows
Fichier: backend/routes/workflow_routes.py
"""
from flask import Blueprint, jsonify, request, g
from backend.middleware.auth_middleware import token_required
from backend.services.workflow_service import WorkflowService
import logging

logger = logging.getLogger(__name__)
workflow_bp = Blueprint('workflow', __name__)
workflow_service = WorkflowService()

@workflow_bp.route('/workflows', methods=['GET'])
@token_required
def get_user_workflows():
    """Récupérer tous les workflows d'un utilisateur"""
    try:
        limit = request.args.get('limit', 50, type=int)
        workflows = workflow_service.get_user_workflows(g.user_id, limit)
        
        return jsonify({
            'success': True,
            'workflows': workflows,
            'total': len(workflows)
        }), 200
        
    except Exception as e:
        logger.error(f"Erreur récupération workflows: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500

@workflow_bp.route('/workflows/<workflow_id>', methods=['GET'])
@token_required
def get_workflow_detail(workflow_id):
    """Récupérer les détails d'un workflow spécifique"""
    try:
        workflow = workflow_service.get_workflow_by_id(workflow_id, g.user_id)
        
        if not workflow:
            return jsonify({'success': False, 'error': 'Workflow non trouvé'}), 404
        
        return jsonify({
            'success': True,
            'workflow': workflow
        }), 200
        
    except Exception as e:
        logger.error(f"Erreur récupération workflow {workflow_id}: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500

@workflow_bp.route('/workflows/<workflow_id>/files', methods=['GET'])
@token_required
def get_workflow_files(workflow_id):
    """Lister tous les fichiers d'un workflow (pour les clusters)"""
    try:
        files = workflow_service.list_workflow_files(workflow_id, g.user_id)
        
        if not files['success']:
            return jsonify(files), 404
        
        return jsonify({
            'success': True,
            'files': files['files'],
            'workflow_type': files['workflow_type']
        }), 200
        
    except Exception as e:
        logger.error(f"Erreur listage fichiers: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500

@workflow_bp.route('/workflows/<workflow_id>/download', methods=['GET'])
@token_required
def download_workflow_file(workflow_id):
    """Télécharger un fichier d'un workflow depuis MinIO"""
    try:
        filename = request.args.get('filename', 'article_main.html.gz')
        
        result = workflow_service.get_file_from_minio(workflow_id, filename, g.user_id)
        
        if not result['success']:
            return jsonify(result), 404
        
        return jsonify({
            'success': True,
            'content': result['content'],
            'filename': filename
        }), 200
        
    except Exception as e:
        logger.error(f"Erreur téléchargement fichier: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500
