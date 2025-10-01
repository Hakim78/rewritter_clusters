"""
Service de gestion des utilisateurs (CRUD)
Fichier: backend/services/user_service.py
"""

from sqlalchemy.orm import Session
from typing import List, Optional, Dict
from datetime import datetime

# from backend.models.user import User
from models.user import User
from services.auth_service import AuthService

class UserService:
    """Service pour gérer les utilisateurs (admin only)"""
    
    @staticmethod
    def get_all_users(db: Session) -> List[User]:
        """
        Récupérer tous les utilisateurs
        
        Args:
            db: Session SQLAlchemy
        
        Returns:
            Liste des utilisateurs
        """
        return db.query(User).order_by(User.created_at.desc()).all()
    
    @staticmethod
    def get_user_by_id(db: Session, user_id: int) -> Optional[User]:
        """
        Récupérer un utilisateur par ID
        
        Args:
            db: Session SQLAlchemy
            user_id: ID de l'utilisateur
        
        Returns:
            User si trouvé, None sinon
        """
        return db.query(User).filter(User.id == user_id).first()
    
    @staticmethod
    def get_user_by_email(db: Session, email: str) -> Optional[User]:
        """
        Récupérer un utilisateur par email
        
        Args:
            db: Session SQLAlchemy
            email: Email de l'utilisateur
        
        Returns:
            User si trouvé, None sinon
        """
        return db.query(User).filter(User.email == email).first()
    
    @staticmethod
    def create_user(
        db: Session,
        email: str,
        name: str,
        password: str,
        role: str = 'user',
        status: str = 'active',
        created_by: Optional[int] = None
    ) -> tuple[bool, Optional[User], Optional[str]]:
        """
        Créer un nouvel utilisateur
        
        Args:
            db: Session SQLAlchemy
            email: Email de l'utilisateur
            name: Nom de l'utilisateur
            password: Mot de passe en clair
            role: Rôle (admin/user)
            status: Statut (active/inactive/pending)
            created_by: ID de l'admin créateur
        
        Returns:
            Tuple (success, user, error_message)
        """
        try:
            # Vérifier si l'email existe déjà
            existing_user = UserService.get_user_by_email(db, email)
            if existing_user:
                return (False, None, 'Cet email est déjà utilisé')
            
            # Valider le rôle
            if role not in ['admin', 'user']:
                return (False, None, 'Rôle invalide')
            
            # Valider le statut
            if status not in ['active', 'inactive', 'pending']:
                return (False, None, 'Statut invalide')
            
            # Hasher le mot de passe
            password_hash = AuthService.hash_password(password)
            
            # Créer l'utilisateur
            user = User(
                email=email,
                name=name,
                password_hash=password_hash,
                role=role,
                status=status,
                created_by=created_by,
                created_at=datetime.utcnow(),
                updated_at=datetime.utcnow()
            )
            
            db.add(user)
            db.commit()
            db.refresh(user)
            
            return (True, user, None)
            
        except Exception as e:
            db.rollback()
            print(f"Erreur création utilisateur: {e}")
            return (False, None, 'Erreur lors de la création')
    
    @staticmethod
    def update_user(
        db: Session,
        user_id: int,
        data: Dict
    ) -> tuple[bool, Optional[User], Optional[str]]:
        """
        Modifier un utilisateur
        
        Args:
            db: Session SQLAlchemy
            user_id: ID de l'utilisateur
            data: Données à modifier
        
        Returns:
            Tuple (success, user, error_message)
        """
        try:
            user = UserService.get_user_by_id(db, user_id)
            if not user:
                return (False, None, 'Utilisateur introuvable')
            
            # Champs modifiables
            if 'name' in data:
                user.name = data['name']
            
            if 'email' in data and data['email'] != user.email:
                # Vérifier si le nouvel email existe déjà
                existing = UserService.get_user_by_email(db, data['email'])
                if existing:
                    return (False, None, 'Cet email est déjà utilisé')
                user.email = data['email']
            
            if 'role' in data:
                if data['role'] not in ['admin', 'user']:
                    return (False, None, 'Rôle invalide')
                user.role = data['role']
            
            if 'status' in data:
                if data['status'] not in ['active', 'inactive', 'pending']:
                    return (False, None, 'Statut invalide')
                user.status = data['status']
            
            if 'password' in data and data['password']:
                # Changer le mot de passe
                user.password_hash = AuthService.hash_password(data['password'])
            
            user.updated_at = datetime.utcnow()
            
            db.commit()
            db.refresh(user)
            
            return (True, user, None)
            
        except Exception as e:
            db.rollback()
            print(f"Erreur modification utilisateur: {e}")
            return (False, None, 'Erreur lors de la modification')
    
    @staticmethod
    def delete_user(db: Session, user_id: int) -> tuple[bool, Optional[str]]:
        """
        Supprimer un utilisateur
        
        Args:
            db: Session SQLAlchemy
            user_id: ID de l'utilisateur
        
        Returns:
            Tuple (success, error_message)
        """
        try:
            user = UserService.get_user_by_id(db, user_id)
            if not user:
                return (False, 'Utilisateur introuvable')
            
            db.delete(user)
            db.commit()
            
            return (True, None)
            
        except Exception as e:
            db.rollback()
            print(f"Erreur suppression utilisateur: {e}")
            return (False, 'Erreur lors de la suppression')
    
    @staticmethod
    def get_stats(db: Session) -> Dict:
        """
        Récupérer les statistiques des utilisateurs
        
        Args:
            db: Session SQLAlchemy
        
        Returns:
            Dictionnaire de statistiques
        """
        total = db.query(User).count()
        active = db.query(User).filter(User.status == 'active').count()
        admins = db.query(User).filter(User.role == 'admin').count()
        
        return {
            'total': total,
            'active': active,
            'inactive': total - active,
            'admins': admins,
            'users': total - admins
        }
    
    # @staticmethod
    # def workflow1(db: Session) -> List[User]:
    #     """
    #     Exemple de workflow 1: Récupérer tous les utilisateurs actifs
        
    #     Args:
    #         db: Session SQLAlchemy
        
    #     Returns:
    #         Liste des utilisateurs actifs
    #     """
    #     return db.query(User).filter(User.status == 'active').all()