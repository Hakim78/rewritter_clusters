"""
Routes administration (CRUD utilisateurs)
Fichier: backend/api/admin.py
"""

from flask import Blueprint, request, jsonify, g
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from backend.services.user_service import UserService
from backend.middleware.admin_middleware import admin_required
from backend.config import Config

# Créer le Blueprint
admin_bp = Blueprint('admin', __name__, url_prefix='/api/admin')

# Configuration de la base de données
engine = create_engine(Config.SQLALCHEMY_DATABASE_URI)
Session = sessionmaker(bind=engine)

@admin_bp.route('/users', methods=['GET'])
@admin_required
def get_all_users():
    """
    Récupérer la liste de tous les utilisateurs (admin only)
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Response:
        {
            "success": true,
            "users": [
                {
                    "id": 1,
                    "email": "user@example.com",
                    "name": "User Name",
                    "role": "admin",
                    "status": "active",
                    "created_at": "2025-01-01T00:00:00",
                    "last_login": "2025-01-01T12:00:00"
                }
            ]
        }
    """
    try:
        db = Session()
        try:
            users = UserService.get_all_users(db)
            
            return jsonify({
                'success': True,
                'users': [user.to_dict() for user in users]
            }), 200
            
        finally:
            db.close()
    
    except Exception as e:
        print(f"Erreur get_all_users: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@admin_bp.route('/users/<int:user_id>', methods=['GET'])
@admin_required
def get_user(user_id):
    """
    Récupérer un utilisateur par son ID (admin only)
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Response:
        {
            "success": true,
            "user": { ... }
        }
    """
    try:
        db = Session()
        try:
            user = UserService.get_user_by_id(db, user_id)
            
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
        print(f"Erreur get_user: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@admin_bp.route('/users', methods=['POST'])
@admin_required
def create_user():
    """
    Créer un nouvel utilisateur (admin only)
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Request body:
        {
            "email": "newuser@example.com",
            "name": "New User",
            "password": "password123",
            "role": "user",
            "status": "active"
        }
    
    Response:
        {
            "success": true,
            "message": "Utilisateur créé avec succès",
            "user": { ... }
        }
    """
    try:
        data = request.get_json()
        
        # Validation des données
        required_fields = ['email', 'name', 'password']
        for field in required_fields:
            if field not in data or not data[field]:
                return jsonify({
                    'success': False,
                    'error': f'Champ requis manquant: {field}'
                }), 400
        
        db = Session()
        try:
            # Créer l'utilisateur
            success, user, error = UserService.create_user(
                db=db,
                email=data['email'],
                name=data['name'],
                password=data['password'],
                role=data.get('role', 'user'),
                status=data.get('status', 'active'),
                created_by=g.user_id  # ID de l'admin qui crée
            )
            
            if not success:
                return jsonify({
                    'success': False,
                    'error': error
                }), 400
            
            return jsonify({
                'success': True,
                'message': 'Utilisateur créé avec succès',
                'user': user.to_dict()
            }), 201
            
        finally:
            db.close()
    
    except Exception as e:
        print(f"Erreur create_user: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@admin_bp.route('/users/<int:user_id>', methods=['PUT'])
@admin_required
def update_user(user_id):
    """
    Modifier un utilisateur (admin only)
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Request body:
        {
            "name": "Updated Name",
            "email": "updated@example.com",
            "role": "admin",
            "status": "inactive",
            "password": "newpassword123"  // optionnel
        }
    
    Response:
        {
            "success": true,
            "message": "Utilisateur modifié avec succès",
            "user": { ... }
        }
    """
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({
                'success': False,
                'error': 'Aucune donnée fournie'
            }), 400
        
        db = Session()
        try:
            success, user, error = UserService.update_user(db, user_id, data)
            
            if not success:
                return jsonify({
                    'success': False,
                    'error': error
                }), 400
            
            return jsonify({
                'success': True,
                'message': 'Utilisateur modifié avec succès',
                'user': user.to_dict()
            }), 200
            
        finally:
            db.close()
    
    except Exception as e:
        print(f"Erreur update_user: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@admin_bp.route('/users/<int:user_id>', methods=['DELETE'])
@admin_required
def delete_user(user_id):
    """
    Supprimer un utilisateur (admin only)
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Response:
        {
            "success": true,
            "message": "Utilisateur supprimé avec succès"
        }
    """
    try:
        # Empêcher l'admin de se supprimer lui-même
        if user_id == g.user_id:
            return jsonify({
                'success': False,
                'error': 'Impossible de supprimer votre propre compte'
            }), 400
        
        db = Session()
        try:
            success, error = UserService.delete_user(db, user_id)
            
            if not success:
                return jsonify({
                    'success': False,
                    'error': error
                }), 400
            
            return jsonify({
                'success': True,
                'message': 'Utilisateur supprimé avec succès'
            }), 200
            
        finally:
            db.close()
    
    except Exception as e:
        print(f"Erreur delete_user: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500


@admin_bp.route('/stats', methods=['GET'])
@admin_required
def get_stats():
    """
    Récupérer les statistiques des utilisateurs (admin only)
    
    Headers:
        Authorization: Bearer jwt_token...
    
    Response:
        {
            "success": true,
            "stats": {
                "total": 10,
                "active": 8,
                "inactive": 2,
                "admins": 2,
                "users": 8
            }
        }
    """
    try:
        db = Session()
        try:
            stats = UserService.get_stats(db)
            
            return jsonify({
                'success': True,
                'stats': stats
            }), 200
            
        finally:
            db.close()
    
    except Exception as e:
        print(f"Erreur get_stats: {e}")
        return jsonify({
            'success': False,
            'error': 'Erreur serveur'
        }), 500