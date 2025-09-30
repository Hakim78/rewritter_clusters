"""
Configuration Backend Python
Fichier: backend/config.py
"""

import os
from dotenv import load_dotenv

# Charger les variables d'environnement
load_dotenv()

class Config:
    """Configuration de base"""
    
    # Application
    SECRET_KEY = os.getenv('SECRET_KEY', 'dev-secret-key-change-in-production')
    DEBUG = os.getenv('APP_DEBUG', 'True') == 'True'
    ENV = os.getenv('APP_ENV', 'development')
    
    # API
    API_HOST = os.getenv('PYTHON_API_HOST', '0.0.0.0')
    API_PORT = int(os.getenv('PYTHON_API_PORT', 5000))
    
    # Base de données
    DB_HOST = os.getenv('DB_HOST', 'localhost')
    DB_PORT = int(os.getenv('DB_PORT', 3306))
    DB_NAME = os.getenv('DB_NAME', 'seo_articles')
    DB_USER = os.getenv('DB_USER', 'root')
    DB_PASSWORD = os.getenv('DB_PASSWORD', '')
    
    # URL de connexion SQLAlchemy
    SQLALCHEMY_DATABASE_URI = (
        f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}@"
        f"{DB_HOST}:{DB_PORT}/{DB_NAME}"
    )
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    
    # APIs LLM
    OPENAI_API_KEY = os.getenv('OPENAI_API_KEY', '')
    ANTHROPIC_API_KEY = os.getenv('ANTHROPIC_API_KEY', '')
    
    # API Génération d'images
    REPLICATE_API_TOKEN = os.getenv('REPLICATE_API_TOKEN', '')
    STABILITY_API_KEY = os.getenv('STABILITY_API_KEY', '')
    
    # Services externes
    SCRAPING_API_KEY = os.getenv('SCRAPING_API_KEY', '')
    
    # Limites et timeouts
    MAX_ARTICLE_LENGTH = int(os.getenv('MAX_ARTICLE_LENGTH', 5000))
    TIMEOUT_SECONDS = int(os.getenv('TIMEOUT_SECONDS', 300))
    MAX_RETRIES = int(os.getenv('MAX_RETRIES', 3))
    
    # Logging
    LOG_LEVEL = os.getenv('LOG_LEVEL', 'INFO')
    LOG_FILE_PATH = os.getenv('LOG_FILE_PATH', 'logs/app.log')
    
    # Dossiers
    UPLOAD_FOLDER = 'uploads'
    TEMP_FOLDER = 'temp'
    OUTPUT_FOLDER = 'output'
    
    # Workflows
    WORKFLOW_TIMEOUT = 600  # 10 minutes
    
    @staticmethod
    def init_app(app):
        """Initialise l'application avec la configuration"""
        pass


class DevelopmentConfig(Config):
    """Configuration pour le développement"""
    DEBUG = True
    TESTING = False


class ProductionConfig(Config):
    """Configuration pour la production"""
    DEBUG = False
    TESTING = False
    
    # En production, assure-toi que ces valeurs sont définies
    @classmethod
    def init_app(cls, app):
        Config.init_app(app)
        
        # Vérifications de sécurité
        assert cls.SECRET_KEY != 'dev-secret-key-change-in-production', \
            "Change SECRET_KEY in production!"
        assert cls.OPENAI_API_KEY, "OPENAI_API_KEY must be set!"


class TestingConfig(Config):
    """Configuration pour les tests"""
    TESTING = True
    DEBUG = True
    SQLALCHEMY_DATABASE_URI = 'sqlite:///:memory:'


# Dictionnaire des configurations
config = {
    'development': DevelopmentConfig,
    'production': ProductionConfig,
    'testing': TestingConfig,
    'default': DevelopmentConfig
}


def get_config():
    """Retourne la configuration en fonction de l'environnement"""
    env = os.getenv('APP_ENV', 'development')
    return config.get(env, config['default'])
