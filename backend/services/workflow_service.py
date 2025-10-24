"""
Workflow Service - Gestion des workflows avec MinIO
Fichier: backend/services/workflow_service.py
"""
import os
import json
import gzip
import logging
from datetime import datetime
from typing import Dict, Any, Optional
from minio import Minio
from minio.error import S3Error
from io import BytesIO
import pymysql
from backend.config import Config

logger = logging.getLogger(__name__)

class WorkflowService:
    """Service pour gérer les workflows et leur stockage dans MinIO"""
    
    def __init__(self):
        """Initialiser le client MinIO"""
        self.endpoint = os.getenv('MINIO_ENDPOINT', 'localhost:9000')
        self.access_key = os.getenv('MINIO_ACCESS_KEY', 'minioadmin')
        self.secret_key = os.getenv('MINIO_SECRET_KEY', 'minioadmin123')
        self.bucket = os.getenv('MINIO_BUCKET', 'seo-workflows')
        self.secure = os.getenv('MINIO_SECURE', 'False').lower() == 'true'
        
        self.client = Minio(
            self.endpoint,
            access_key=self.access_key,
            secret_key=self.secret_key,
            secure=self.secure
        )
        
        # Créer le bucket s'il n'existe pas
        self._ensure_bucket_exists()
    
    def _ensure_bucket_exists(self):
        """Créer le bucket s'il n'existe pas"""
        try:
            if not self.client.bucket_exists(self.bucket):
                self.client.make_bucket(self.bucket)
                logger.info(f"Bucket '{self.bucket}' créé")
        except S3Error as e:
            logger.error(f"Erreur création bucket: {e}")
    
    def _get_db_connection(self):
        """Créer une connexion MySQL"""
        return pymysql.connect(
            host=Config.DB_HOST,
            port=Config.DB_PORT,
            user=Config.DB_USER,
            password=Config.DB_PASSWORD,
            database=Config.DB_NAME,
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
    
    def create_workflow(self, user_id: int, workflow_id: str, workflow_type: str, 
                       input_params: Dict, total_steps: int = 3):
        """Créer un nouveau workflow en DB"""
        conn = self._get_db_connection()
        try:
            with conn.cursor() as cursor:
                sql = """
                INSERT INTO workflows (
                    user_id, workflow_id, workflow_type, status, progress,
                    current_step, total_steps, input_params, keyword, minio_path
                ) VALUES (%s, %s, %s, 'pending', 0, 1, %s, %s, %s, %s)
                """
                minio_path = f"user_{user_id}/workflow_{workflow_id}/"
                cursor.execute(sql, (
                    user_id, workflow_id, workflow_type, total_steps,
                    json.dumps(input_params), input_params.get('keyword'),
                    minio_path
                ))
            conn.commit()
            logger.info(f"Workflow {workflow_id} créé pour user {user_id}")
        finally:
            conn.close()
    
    def update_progress(self, workflow_id: str, progress: int, current_step: int):
        """Mettre à jour la progression d'un workflow"""
        conn = self._get_db_connection()
        try:
            with conn.cursor() as cursor:
                sql = """
                UPDATE workflows 
                SET progress = %s, current_step = %s, status = 'processing',
                    started_at = COALESCE(started_at, NOW())
                WHERE workflow_id = %s
                """
                cursor.execute(sql, (progress, current_step, workflow_id))
            conn.commit()
        finally:
            conn.close()
    
    def save_to_minio(self, workflow_id: str, filename: str, content: str, 
                     compress: bool = True) -> Dict:
        """Sauvegarder du contenu dans MinIO"""
        conn = self._get_db_connection()
        try:
            with conn.cursor() as cursor:
                cursor.execute("SELECT minio_path FROM workflows WHERE workflow_id = %s", (workflow_id,))
                result = cursor.fetchone()
                if not result:
                    raise ValueError(f"Workflow {workflow_id} introuvable")
                
                minio_path = result['minio_path']
            
            # Chemin dans MinIO
            object_name = f"{minio_path}{filename}"
            
            # Compression si demandée
            if compress:
                content_bytes = content.encode('utf-8')
                compressed = gzip.compress(content_bytes)
                data = BytesIO(compressed)
                size = len(compressed)
                object_name += '.gz'
            else:
                data = BytesIO(content.encode('utf-8'))
                size = len(content.encode('utf-8'))
            
            # Upload vers MinIO
            self.client.put_object(
                self.bucket,
                object_name,
                data,
                size,
                content_type='application/gzip' if compress else 'text/html'
            )
            
            # Mettre à jour les stats du workflow
            with conn.cursor() as cursor:
                sql = """
                UPDATE workflows 
                SET files_count = files_count + 1,
                    total_size_bytes = COALESCE(total_size_bytes, 0) + %s
                WHERE workflow_id = %s
                """
                cursor.execute(sql, (size, workflow_id))
            conn.commit()
            
            logger.info(f"Fichier {filename} sauvegardé dans MinIO pour {workflow_id}")
            
            return {
                'success': True,
                'object_name': object_name,
                'size': size,
                'compressed': compress
            }
        
        except S3Error as e:
            logger.error(f"Erreur upload MinIO: {e}")
            return {'success': False, 'error': str(e)}
        finally:
            conn.close()
    
    def complete_workflow(self, workflow_id: str, result_data: Dict, 
                         generation_time: int, tokens_used: int = 0):
        """Marquer un workflow comme terminé"""
        conn = self._get_db_connection()
        try:
            # Extraire le titre du résultat
            title = None
            articles_count = 1
            
            if 'article' in result_data:
                title = result_data['article'].get('seo_title', '')
            elif 'pillar' in result_data:
                title = result_data['pillar'].get('seo_title', '')
                if 'satellites' in result_data:
                    articles_count = 1 + len(result_data.get('satellites', []))
            
            with conn.cursor() as cursor:
                sql = """
                UPDATE workflows 
                SET status = 'completed', progress = 100, completed_at = NOW(),
                    generation_time_seconds = %s, tokens_used = %s,
                    title = %s, articles_count = %s
                WHERE workflow_id = %s
                """
                cursor.execute(sql, (generation_time, tokens_used, title, articles_count, workflow_id))
            conn.commit()
            
            logger.info(f"Workflow {workflow_id} marqué comme terminé")
        finally:
            conn.close()
    
    def fail_workflow(self, workflow_id: str, error_message: str, error_code: str = None):
        """Marquer un workflow comme échoué"""
        conn = self._get_db_connection()
        try:
            with conn.cursor() as cursor:
                sql = """
                UPDATE workflows 
                SET status = 'failed', error_message = %s, error_code = %s,
                    retry_count = retry_count + 1
                WHERE workflow_id = %s
                """
                cursor.execute(sql, (error_message, error_code, workflow_id))
            conn.commit()
            
            logger.error(f"Workflow {workflow_id} échoué: {error_message}")
        finally:
            conn.close()
    
    def get_user_workflows(self, user_id: int, limit: int = 50) -> list:
        """Récupérer les workflows d'un utilisateur"""
        conn = self._get_db_connection()
        try:
            with conn.cursor() as cursor:
                sql = """
                SELECT id, workflow_id, workflow_type, status, progress, 
                       title, keyword, files_count, articles_count,
                       generation_time_seconds, created_at, completed_at
                FROM workflows
                WHERE user_id = %s
                ORDER BY created_at DESC
                LIMIT %s
                """
                cursor.execute(sql, (user_id, limit))
                return cursor.fetchall()
        finally:
            conn.close()
    def get_workflow_by_id(self, workflow_id: str, user_id: int) -> Optional[Dict]:
        """Récupérer un workflow spécifique"""
        conn = self._get_db_connection()
        try:
            with conn.cursor() as cursor:
                sql = """
                SELECT id, workflow_id, workflow_type, status, progress, 
                       title, keyword, files_count, articles_count,
                       generation_time_seconds, tokens_used, total_size_bytes,
                       created_at, started_at, completed_at, error_message
                FROM workflows
                WHERE workflow_id = %s AND user_id = %s
                """
                cursor.execute(sql, (workflow_id, user_id))
                return cursor.fetchone()
        finally:
            conn.close()

    def list_workflow_files(self, workflow_id: str, user_id: int) -> Dict:
        """Lister tous les fichiers d'un workflow"""
        conn = self._get_db_connection()
        try:
            # Vérifier le workflow
            with conn.cursor() as cursor:
                cursor.execute(
                    "SELECT minio_path, workflow_type FROM workflows WHERE workflow_id = %s AND user_id = %s",
                    (workflow_id, user_id)
                )
                result = cursor.fetchone()
                
                if not result:
                    return {'success': False, 'error': 'Workflow non trouvé'}
                
                minio_path = result['minio_path']
                workflow_type = result['workflow_type']
            
            # Lister les fichiers dans MinIO
            try:
                objects = self.client.list_objects(self.bucket, prefix=minio_path, recursive=True)
                
                files = []
                for obj in objects:
                    filename = obj.object_name.replace(minio_path, '')
                    
                    # Déterminer le type
                    if 'pillar' in filename:
                        file_type = 'pillar'
                        label = 'Article Pilier'
                    elif 'satellite' in filename:
                        sat_num = filename.split('_')[1].split('.')[0]
                        file_type = 'satellite'
                        label = f'Article Satellite {sat_num}'
                    elif 'article_main' in filename:
                        file_type = 'main'
                        label = 'Article Principal'
                    elif 'article_rewritten' in filename:
                        file_type = 'rewritten'
                        label = 'Article Réécrit'
                    else:
                        file_type = 'other'
                        label = filename
                    
                    files.append({
                        'filename': filename,
                        'type': file_type,
                        'label': label,
                        'size': obj.size
                    })
                
                return {
                    'success': True,
                    'files': files,
                    'workflow_type': workflow_type
                }
                
            except S3Error as e:
                logger.error(f"Erreur MinIO: {e}")
                return {'success': False, 'error': 'Aucun fichier trouvé'}
        
        finally:
            conn.close()

    
    def get_file_from_minio(self, workflow_id: str, filename: str, user_id: int) -> Dict:
        """Récupérer un fichier depuis MinIO"""
        conn = self._get_db_connection()
        try:
            # Vérifier que le workflow appartient à l'utilisateur
            with conn.cursor() as cursor:
                cursor.execute(
                    "SELECT minio_path FROM workflows WHERE workflow_id = %s AND user_id = %s",
                    (workflow_id, user_id)
                )
                result = cursor.fetchone()
                
                if not result:
                    return {'success': False, 'error': 'Workflow non trouvé'}
                
                minio_path = result['minio_path']
            
            # Télécharger depuis MinIO
            object_name = f"{minio_path}{filename}"
            
            try:
                response = self.client.get_object(self.bucket, object_name)
                content_bytes = response.read()
                
                # Décompresser si .gz
                if filename.endswith('.gz'):
                    import gzip
                    content = gzip.decompress(content_bytes).decode('utf-8')
                else:
                    content = content_bytes.decode('utf-8')
                
                return {
                    'success': True,
                    'content': content,
                    'size': len(content_bytes)
                }
                
            except S3Error as e:
                logger.error(f"Fichier introuvable dans MinIO: {e}")
                return {'success': False, 'error': 'Fichier non trouvé'}
        
        finally:
            conn.close()
