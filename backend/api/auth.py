"""
Routes d'authentification
Fichier: backend/api/auth.py
"""

from flask import Blueprint, request, jsonify, g
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from backend.services.auth_service import AuthService
from backend.middleware.auth_middleware import token_required
from backend.config import Config

# Créer le Blueprint
auth_bp = Blueprint('auth', __name__, url_prefix='/api/auth')

# Configuration de la base de données
engine = create_engine(Config.SQLALCHEMY_DATABASE_URI)
Session = sessionmaker(bind=engine)

@auth_bp.route('/login', methods=['POST'])
def login():
    """
    Authentification d'un utilisateur
    
    Request body:
        {
            "email": "user@example.com",
            "password": "password123"
        }
    
    Response:
        {
            "success": true,
            "message": "Connexion réussie",
            "token": "jwt_token...",
            "user": {
                "id": 1,
                "email": "user@example.com",
                "name": "User Name",
                "role": "admin",
                "status": "active"
            }
        }
    """
    try:
        data = request.get_json()
        
        # Validation des données
        if not data:
            return jsonify({
                'success': False,
                'error': 'Aucune donnée fournie'
            }), 400
        
        email = data.get('email', '').strip()
        password = data.get('password', '')
        
        if not email or not password:
            return jsonify({
                'success': False,
                'error': 'Email et mot de passe requis'
            }), 400
        
        # Authentification
        db = Session()
        try:
            success, user, token, error = AuthService.authenticate(db, email, password)
            
            if not success:
                return jsonify({
                    'success': False,
                    'error': error
                }), 401
            
            return jsonify({
                'success': True,
                'message': 'Connexion réussie',
                'token': token,
                'user': user.to_dict()
            }), 200
            
        finally:
            db.close()
    
    except Exception as e:
        print(f"Erreur login: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@auth_bp.route('/verify', methods=['POST'])
def verify():
    """
    Vérifier la validité d'un token JWT
    
    Request body:
        {
            "token": "jwt_token..."
        }
    
    Response:
        {
            "success": true,
            "valid": true,
            "user": {
                "id": 1,
                "email": "user@example.com",
                "role": "admin"
            }
        }
    """
    try:
        data = request.get_json()
        
        if not data or 'token' not in data:
            return jsonify({
                'success': False,
                'error': 'Token manquant'
            }), 400
        
        token = data['token']
        
        # Vérifier le token
        valid, payload, error = AuthService.verify_token(token)
        
        if not valid:
            return jsonify({
                'success': True,
                'valid': False,
                'error': error
            }), 200
        
        return jsonify({
            'success': True,
            'valid': True,
            'user': {
                'id': payload.get('user_id'),
                'email': payload.get('email'),
                'role': payload.get('role')
            }
        }), 200
    
    except Exception as e:
        print(f"Erreur verify: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@auth_bp.route('/me', methods=['GET'])
@token_required
def get_current_user():
    """
    Récupérer les informations de l'utilisateur connecté
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Response:
        {
            "success": true,
            "user": {
                "id": 1,
                "email": "user@example.com",
                "name": "User Name",
                "role": "admin",
                "status": "active"
            }
        }
    """
    try:
        db = Session()
        try:
            token = request.headers.get('Authorization', '').replace('Bearer ', '')
            user = AuthService.get_user_from_token(db, token)
            
            if not user:
                return jsonify({
                    'success': False,
                    'error': 'Utilisateur introuvable'
                }), 404
            
            return jsonify({
                'success': True,
                'user': user.to_dict()
            }), 200
            
        finally:
            db.close()
    
    except Exception as e:
        print(f"Erreur get_current_user: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@auth_bp.route('/logout', methods=['POST'])
@token_required
def logout():
    """
    Déconnexion (invalidation côté client, pas de blacklist côté serveur)
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Response:
        {
            "success": true,
            "message": "Déconnexion réussie"
        }
    """
    return jsonify({
        'success': True,
        'message': 'Déconnexion réussie'
    }), 200