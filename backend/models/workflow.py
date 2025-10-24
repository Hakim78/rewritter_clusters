"""
Workflow Model
Fichier: backend/models/workflow.py
"""
from sqlalchemy import Column, BigInteger, Integer, String, Enum, SmallInteger, Boolean, Text, JSON, Numeric, DateTime, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
from datetime import datetime

Base = declarative_base()

class Workflow(Base):
    __tablename__ = 'workflows'
    
    id = Column(BigInteger, primary_key=True, autoincrement=True)
    user_id = Column(Integer, ForeignKey('users.id'), nullable=False)
    project_id = Column(Integer, ForeignKey('projects.id'), nullable=True)
    
    workflow_id = Column(String(100), unique=True, nullable=False)
    workflow_type = Column(Enum('scratch', 'rewrite', 'cluster'), nullable=False)
    status = Column(Enum('pending', 'processing', 'completed', 'failed', 'cancelled'), default='pending')
    progress = Column(SmallInteger, default=0)
    current_step = Column(SmallInteger, default=1)
    total_steps = Column(SmallInteger, default=3)
    
    input_params = Column(JSON)
    
    title = Column(String(500))
    keyword = Column(String(255))
    language = Column(String(10), default='fr')
    
    minio_bucket = Column(String(100), default='seo-workflows')
    minio_path = Column(String(500))
    total_size_bytes = Column(Integer)
    files_count = Column(SmallInteger, default=0)
    compressed = Column(Boolean, default=True)
    
    generation_time_seconds = Column(Integer)
    tokens_used = Column(Integer)
    api_calls_count = Column(Integer, default=0)
    cost_usd = Column(Numeric(10, 4), default=0.0000)
    
    articles_count = Column(SmallInteger, default=1)
    
    error_message = Column(Text)
    error_code = Column(String(50))
    retry_count = Column(SmallInteger, default=0)
    
    step_details = Column(JSON)
    
    migrated_to_history = Column(Boolean, default=False)
    history_migration_at = Column(DateTime)
    
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    started_at = Column(DateTime)
    completed_at = Column(DateTime)
    
    def to_dict(self):
        """Convertir en dictionnaire"""
        return {
            'id': self.id,
            'workflow_id': self.workflow_id,
            'user_id': self.user_id,
            'workflow_type': self.workflow_type,
            'status': self.status,
            'progress': self.progress,
            'current_step': self.current_step,
            'total_steps': self.total_steps,
            'title': self.title,
            'keyword': self.keyword,
            'minio_path': self.minio_path,
            'files_count': self.files_count,
            'articles_count': self.articles_count,
            'generation_time_seconds': self.generation_time_seconds,
            'error_message': self.error_message,
            'migrated_to_history': self.migrated_to_history,
            'created_at': self.created_at.isoformat() if self.created_at else None,
            'completed_at': self.completed_at.isoformat() if self.completed_at else None
        }
