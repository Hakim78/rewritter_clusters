"""
Model User - SQLAlchemy
Fichier: backend/models/user.py
"""

from sqlalchemy import Column, Integer, String, Enum, DateTime, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from datetime import datetime

Base = declarative_base()

class User(Base):
    __tablename__ = 'users'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    email = Column(String(255), unique=True, nullable=False, index=True)
    name = Column(String(255), nullable=False)
    password_hash = Column(String(255), nullable=False)
    role = Column(Enum('admin', 'user'), default='user', nullable=False)
    status = Column(Enum('active', 'inactive', 'pending'), default='pending', nullable=False)
    created_at = Column(DateTime, default=datetime.utcnow, nullable=False)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow, nullable=False)
    last_login = Column(DateTime, nullable=True)
    created_by = Column(Integer, ForeignKey('users.id'), nullable=True)
    
    # Relation self-referential (créateur)
    creator = relationship('User', remote_side=[id], backref='created_users')
    
    def to_dict(self, include_sensitive=False):
        """Convertir l'objet User en dictionnaire"""
        data = {
            'id': self.id,
            'email': self.email,
            'name': self.name,
            'role': self.role,
            'status': self.status,
            'created_at': self.created_at.isoformat() if self.created_at else None,
            'updated_at': self.updated_at.isoformat() if self.updated_at else None,
            'last_login': self.last_login.isoformat() if self.last_login else None,
            'created_by': self.created_by
        }
        
        if include_sensitive:
            data['password_hash'] = self.password_hash
        
        return data
    
    def is_admin(self):
        """Vérifier si l'utilisateur est admin"""
        return self.role == 'admin'
    
    def is_active(self):
        """Vérifier si l'utilisateur est actif"""
        return self.status == 'active'
    
    def __repr__(self):
        return f"<User(id={self.id}, email='{self.email}', role='{self.role}', status='{self.status}')>"