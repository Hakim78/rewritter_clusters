"""
Middleware d'authentification
Fichier: backend/middleware/auth_middleware.py
"""

from functools import wraps
from flask import request, jsonify, g
from backend.utils.jwt_helper import JWTHelper

def token_required(f):
    """
    Décorateur pour protéger les routes nécessitant une authentification
    
    Usage:
        @app.route('/api/protected')
        @token_required
        def protected_route():
            user_id = g.user_id
            user_email = g.user_email
            user_role = g.user_role
            return jsonify({'message': 'Access granted'})
    """
    @wraps(f)
    def decorated(*args, **kwargs):
        # Récupérer le token depuis le header Authorization
        token = request.headers.get('Authorization')
        
        if not token:
            return jsonify({
                'success': False,
                'error': 'Token manquant',
                'message': 'Veuillez fournir un token d\'authentification'
            }), 401
        
        # Vérifier et décoder le token
        valid, payload, error = JWTHelper.verify_token(token)
        
        if not valid:
            return jsonify({
                'success': False,
                'error': 'Token invalide',
                'message': error
            }), 401
        
        # Stocker les infos utilisateur dans g (context global Flask)
        g.user_id = payload.get('user_id')
        g.user_email = payload.get('email')
        g.user_role = payload.get('role')
        
        return f(*args, **kwargs)
    
    return decorated


def optional_token(f):
    """
    Décorateur pour les routes avec authentification optionnelle
    Le token est vérifié s'il est présent, mais la route fonctionne sans
    
    Usage:
        @app.route('/api/public')
        @optional_token
        def public_route():
            if hasattr(g, 'user_id'):
                # Utilisateur connecté
                pass
            else:
                # Utilisateur non connecté
                pass
    """
    @wraps(f)
    def decorated(*args, **kwargs):
        token = request.headers.get('Authorization')
        
        if token:
            valid, payload, error = JWTHelper.verify_token(token)
            
            if valid and payload:
                g.user_id = payload.get('user_id')
                g.user_email = payload.get('email')
                g.user_role = payload.get('role')
        
        return f(*args, **kwargs)
    
    return decorated