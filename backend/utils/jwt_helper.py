"""
JWT Helper - Génération et validation des tokens
Fichier: backend/utils/jwt_helper.py
"""

import jwt
from datetime import datetime, timedelta
from typing import Dict, Optional
import os

# Clé secrète depuis .env
SECRET_KEY = os.getenv('SECRET_KEY', 'dev-secret-key-change-in-production')
ALGORITHM = 'HS256'
TOKEN_EXPIRATION_HOURS = 24

class JWTHelper:
    """Classe utilitaire pour gérer les JWT"""
    
    @staticmethod
    def generate_token(user_id: int, email: str, role: str) -> str:
        """
        Générer un token JWT
        
        Args:
            user_id: ID de l'utilisateur
            email: Email de l'utilisateur
            role: Rôle de l'utilisateur (admin/user)
        
        Returns:
            Token JWT encodé
        """
        payload = {
            'user_id': user_id,
            'email': email,
            'role': role,
            'iat': datetime.utcnow(),  # Issued at
            'exp': datetime.utcnow() + timedelta(hours=TOKEN_EXPIRATION_HOURS)  # Expiration
        }
        
        token = jwt.encode(payload, SECRET_KEY, algorithm=ALGORITHM)
        return token
    
    @staticmethod
    def decode_token(token: str) -> Optional[Dict]:
        """
        Décoder et valider un token JWT
        
        Args:
            token: Token JWT à décoder
        
        Returns:
            Payload du token si valide, None sinon
        """
        try:
            # Enlever "Bearer " si présent
            if token.startswith('Bearer '):
                token = token[7:]
            
            payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            return payload
            
        except jwt.ExpiredSignatureError:
            # Token expiré
            return None
        except jwt.InvalidTokenError:
            # Token invalide
            return None
        except Exception as e:
            # Autre erreur
            print(f"Erreur décodage JWT: {e}")
            return None
    
    @staticmethod
    def verify_token(token: str) -> tuple[bool, Optional[Dict], Optional[str]]:
        """
        Vérifier la validité d'un token
        
        Args:
            token: Token JWT à vérifier
        
        Returns:
            Tuple (valid, payload, error_message)
        """
        try:
            # Enlever "Bearer " si présent
            if token.startswith('Bearer '):
                token = token[7:]
            
            payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            return (True, payload, None)
            
        except jwt.ExpiredSignatureError:
            return (False, None, 'Token expiré')
        except jwt.InvalidTokenError:
            return (False, None, 'Token invalide')
        except Exception as e:
            return (False, None, f'Erreur: {str(e)}')
    
    @staticmethod
    def get_token_expiration() -> int:
        """Retourner la durée de validité du token en secondes"""
        return TOKEN_EXPIRATION_HOURS * 3600