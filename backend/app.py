"""
Backend Python Flask - Application de Test
Fichier: backend/app.py
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from dotenv import load_dotenv

# Charger les variables d'environnement
load_dotenv()

app = Flask(__name__)
CORS(app)  # Permettre les requêtes cross-origin depuis PHP

# Configuration
app.config['DEBUG'] = True
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'dev-secret-key')

# Route de test basique
@app.route('/api/test', methods=['GET'])
def test():
    """Route de test pour vérifier que l'API fonctionne"""
    return jsonify({
        'status': 'success',
        'message': 'Backend Python Flask fonctionne correctement!',
        'version': '1.0.0'
    })

# Route de test avec données POST
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

# Route pour workflow 1 (création d'article)
@app.route('/api/workflow1', methods=['POST'])
def workflow1():
    """Option 1: Création d'un nouvel article SEO"""
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