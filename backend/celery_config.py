"""
Celery Configuration
"""
from celery import Celery
import os

# Configuration Redis
REDIS_URL = os.getenv('REDIS_URL', 'redis://localhost:6379/0')

# Créer l'app Celery
celery_app = Celery(
    'seo_platform',
    broker=REDIS_URL,
    backend=REDIS_URL,
    include=[
        'celery_tasks.workflow_tasks'
    ]
)

# Configuration
celery_app.conf.update(
    task_serializer='json',
    accept_content=['json'],
    result_serializer='json',
    timezone='Europe/Paris',
    enable_utc=True,
    
    # Performance
    worker_prefetch_multiplier=1,  # Un job à la fois par worker
    worker_max_tasks_per_child=50,  # Redémarre worker après 50 tasks
    
    # Timeouts
    task_soft_time_limit=1800,  # 30 minutes soft limit
    task_time_limit=2400,  # 40 minutes hard limit
    
    # Retry
    task_acks_late=True,  # Confirme seulement après exécution
    task_reject_on_worker_lost=True,
    
    # Results
    result_expires=3600,  # Garde résultats 1h
    result_extended=True,
)

# Task routes (optionnel - pour prioriser certains workflows)
celery_app.conf.task_routes = {
    'celery_tasks.workflow_tasks.workflow1_task': {'queue': 'default'},
    'celery_tasks.workflow_tasks.workflow2_task': {'queue': 'default'},
    'celery_tasks.workflow_tasks.workflow3_task': {'queue': 'default'},
}
