"""
Middleware admin
Fichier: backend/middleware/admin_middleware.py
"""

from functools import wraps
from flask import request, jsonify, g
from backend.utils.jwt_helper import JWTHelper

def admin_required(f):
    """
    Décorateur pour protéger les routes réservées aux administrateurs
    Vérifie à la fois l'authentification ET le rôle admin
    
    Usage:
        @app.route('/api/admin/users')
        @admin_required
        def admin_route():
            admin_id = g.user_id
            return jsonify({'message': 'Admin access granted'})
    """
    @wraps(f)
    def decorated(*args, **kwargs):
        # Récupérer le token
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
        
        # Vérifier le rôle admin
        user_role = payload.get('role')
        if user_role != 'admin':
            return jsonify({
                'success': False,
                'error': 'Accès refusé',
                'message': 'Cette action est réservée aux administrateurs'
            }), 403
        
        # Stocker les infos dans g
        g.user_id = payload.get('user_id')
        g.user_email = payload.get('email')
        g.user_role = user_role
        
        return f(*args, **kwargs)
    
    return decorated