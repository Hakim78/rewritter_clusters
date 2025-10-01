"""
Service d'authentification
Fichier: backend/services/auth_service.py
"""

import bcrypt
from sqlalchemy.orm import Session
from datetime import datetime
from typing import Optional, Dict

# from backend.models.user import User
from models.user import User
from utils.jwt_helper import JWTHelper

class AuthService:
    """Service pour gérer l'authentification"""
    
    @staticmethod
    def hash_password(password: str) -> str:
        """
        Hasher un mot de passe avec bcrypt
        
        Args:
            password: Mot de passe en clair
        
        Returns:
            Hash du mot de passe
        """
        salt = bcrypt.gensalt()
        hashed = bcrypt.hashpw(password.encode('utf-8'), salt)
        return hashed.decode('utf-8')
    
    @staticmethod
    def verify_password(password: str, password_hash: str) -> bool: 
        """
        Vérifier un mot de passe contre son hash
        
        Args:
            password: Mot de passe en clair
            password_hash: Hash stocké en BDD
        
        Returns:
            True si le mot de passe est correct
        """
        try:
            # LOGS DE DEBUG
            print(f"=== DEBUG verify_password ===")
            print(f"Password reçu: '{password}'")
            print(f"Hash reçu (20 premiers car.): {password_hash[:20]}")
            print(f"Longueur du hash: {len(password_hash)}")
            print(f"Type password: {type(password)}")
            print(f"Type hash: {type(password_hash)}")
            
            result = bcrypt.checkpw(
                password.encode('utf-8'),
                password_hash.encode('utf-8')
            )
            
            print(f"Résultat bcrypt.checkpw: {result}")
            print(f"=============================")
            
            return result
        except Exception as e:
            print(f"Erreur vérification password: {e}")
            return False
    
    @staticmethod
    def authenticate(db: Session, email: str, password: str) -> tuple[bool, Optional[User], Optional[str], Optional[str]]:
        """
        Authentifier un utilisateur
        
        Args:
            db: Session SQLAlchemy
            email: Email de l'utilisateur
            password: Mot de passe en clair
        
        Returns:
            Tuple (success, user, token, error_message)
        """
        try:
            # Chercher l'utilisateur par email
            user = db.query(User).filter(User.email == email).first()
            
            if not user:
                return (False, None, None, 'Email ou mot de passe incorrect')
            
            # Vérifier le mot de passe
            if not AuthService.verify_password(password, user.password_hash):
                return (False, None, None, 'Email ou mot de passe incorrect')
            
            # Vérifier le statut
            if user.status != 'active':
                return (False, None, None, 'Compte inactif ou en attente')
            
            # Générer le token JWT
            token = JWTHelper.generate_token(
                user_id=user.id,
                email=user.email,
                role=user.role
            )
            
            # Mettre à jour last_login
            user.last_login = datetime.utcnow()
            db.commit()
            
            return (True, user, token, None)
            
        except Exception as e:
            db.rollback()
            print(f"Erreur authentification: {e}")
            return (False, None, None, 'Erreur serveur')
    
    @staticmethod
    def verify_token(token: str) -> tuple[bool, Optional[Dict], Optional[str]]:
        """
        Vérifier la validité d'un token JWT
        
        Args:
            token: Token JWT
        
        Returns:
            Tuple (valid, payload, error_message)
        """
        return JWTHelper.verify_token(token)
    
    @staticmethod
    def get_user_from_token(db: Session, token: str) -> Optional[User]:
        """
        Récupérer un utilisateur depuis un token JWT
        
        Args:
            db: Session SQLAlchemy
            token: Token JWT
        
        Returns:
            User si trouvé et token valide, None sinon
        """
        valid, payload, error = JWTHelper.verify_token(token)
        
        if not valid or not payload:
            return None
        
        user_id = payload.get('user_id')
        if not user_id:
            return None
        
        return db.query(User).filter(User.id == user_id).first()