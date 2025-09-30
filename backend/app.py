"""
Backend Python Flask - Application principale
Fichier: backend/app.py
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from dotenv import load_dotenv

# Importer les routes
from backend.api.auth import auth_bp
from backend.api.admin import admin_bp
from backend.middleware.auth_middleware import token_required

# Charger les variables d'environnement
load_dotenv()

app = Flask(__name__)
CORS(app)  # Permettre les requêtes cross-origin depuis PHP

# Configuration
app.config['DEBUG'] = True
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'dev-secret-key')

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

@app.route('/api/test-post', methods=['POST'])
def test_post():
    """Test d'envoi de données depuis PHP"""
    data = request.get_json()
    
    return jsonify({
        'status': 'success',
        'message': 'Données reçues avec succès',
        'received_data': data,
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
        
        # Validation basique
        required_fields = ['site_url', 'domain', 'guideline', 'keyword']
        for field in required_fields:
            if field not in data:
                return jsonify({
                    'status': 'error',
                    'message': f'Champ manquant: {field}'
                }), 400
        
        # Simulation du workflow (à remplacer par le vrai code)
        result = {
            'status': 'success',
            'message': 'Article créé avec succès',
            'article': {
                'title': f"Article SEO sur {data['keyword']}",
                'html_content': '<h1>Article généré</h1><p>Contenu exemple...</p>',
                'image_url': 'https://via.placeholder.com/800x400',
                'meta_description': 'Description SEO exemple',
                'word_count': 1500
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

@app.route('/api/workflow3', methods=['POST'])
@token_required
def workflow3():
    """Option 3: Création d'un cluster de 3 articles (PROTÉGÉ)"""
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