"""
Celery Tasks pour les Workflows
"""
from celery_config import celery_app
from services.workflow_service import WorkflowService
from workflows.workflow_1.workflow_manager import WorkflowManager as WorkflowManager1
from workflows.workflow_2.workflow_manager import WorkflowManager as WorkflowManager2
from workflows.workflow_3.workflow_manager import WorkflowManager as WorkflowManager3
import logging
import time

logger = logging.getLogger(__name__)

# Initialize services
workflow_service = WorkflowService()
workflow_manager_1 = WorkflowManager1()
workflow_manager_2 = WorkflowManager2()
workflow_manager_3 = WorkflowManager3()

@celery_app.task(name='celery_tasks.workflow_tasks.workflow1_task', bind=True)
def workflow1_task(self, workflow_id, user_id, data):
    """
    Task Celery pour Workflow 1 (From Scratch)
    """
    logger.info(f"Starting workflow1 task: {workflow_id}")
    
    try:
        # Créer workflow en DB
        workflow_service.create_workflow(
            user_id=user_id,
            workflow_id=workflow_id,
            workflow_type='scratch',
            input_params=data,
            total_steps=4
        )
        
        # Progress callback
        def update_progress(step, status, progress_percent=None):
            workflow_service.update_progress(
                workflow_id, 
                progress_percent or (step * 25), 
                step
            )
            # Update Celery task state
            self.update_state(
                state='PROGRESS',
                meta={
                    'current_step': step,
                    'total_steps': 4,
                    'progress': progress_percent or (step * 25)
                }
            )
        
        # Execute workflow
        start_time = time.time()
        result = workflow_manager_1.execute_workflow1_sync(data, progress_callback=update_progress)
        generation_time = int(time.time() - start_time)
        
        # Save to MinIO
        if result.get('status') == 'success' and 'article' in result:
            article = result['article']
            workflow_service.save_to_minio(workflow_id, 'article_main.html', article.get('html_content', ''), compress=True)
            
            import json
            metadata = {
                'seo_title': article.get('seo_title', ''),
                'meta_description': article.get('meta_description', ''),
                'wordpress_excerpt': article.get('wordpress_excerpt', ''),
                'image_url': article.get('image_url', ''),
                'faq_json': article.get('faq_json', []),
                'secondary_keywords': article.get('secondary_keywords', []),
                'word_count': article.get('word_count', 0)
            }
            workflow_service.save_to_minio(workflow_id, 'metadata.json', json.dumps(metadata, ensure_ascii=False), compress=True)
            
            workflow_service.complete_workflow(workflow_id, result, generation_time)
            return {'status': 'success', 'workflow_id': workflow_id, 'result': result}
        else:
            workflow_service.fail_workflow(workflow_id, result.get('error', 'Unknown error'))
            return {'status': 'error', 'workflow_id': workflow_id, 'error': result.get('error')}
            
    except Exception as e:
        logger.error(f"Workflow1 task failed: {str(e)}", exc_info=True)
        workflow_service.fail_workflow(workflow_id, str(e))
        raise


@celery_app.task(name='celery_tasks.workflow_tasks.workflow2_task', bind=True)
def workflow2_task(self, workflow_id, user_id, data):
    """
    Task Celery pour Workflow 2 (Réécriture)
    """
    logger.info(f"Starting workflow2 task: {workflow_id}")
    
    try:
        workflow_service.create_workflow(
            user_id=user_id,
            workflow_id=workflow_id,
            workflow_type='rewrite',
            input_params=data,
            total_steps=3
        )
        
        def update_progress(step, status, progress_percent=None):
            workflow_service.update_progress(workflow_id, progress_percent or (step * 33), step)
            self.update_state(
                state='PROGRESS',
                meta={
                    'current_step': step,
                    'total_steps': 3,
                    'progress': progress_percent or (step * 33)
                }
            )
        
        start_time = time.time()
        result = workflow_manager_2.execute_workflow2_sync(data, progress_callback=update_progress)
        generation_time = int(time.time() - start_time)
        
        if result.get('status') == 'success' and 'article' in result:
            article = result['article']
            workflow_service.save_to_minio(workflow_id, 'article_main.html', article.get('html_content', ''), compress=True)
            
            import json
            metadata = {
                'seo_title': article.get('seo_title', ''),
                'meta_description': article.get('meta_description', ''),
                'wordpress_excerpt': article.get('wordpress_excerpt', ''),
                'image_url': article.get('image_url', ''),
                'faq_json': article.get('faq_json', []),
                'secondary_keywords': article.get('secondary_keywords', []),
                'word_count': article.get('word_count', 0)
            }
            workflow_service.save_to_minio(workflow_id, 'metadata.json', json.dumps(metadata, ensure_ascii=False), compress=True)
            
            workflow_service.complete_workflow(workflow_id, result, generation_time)
            return {'status': 'success', 'workflow_id': workflow_id, 'result': result}
        else:
            workflow_service.fail_workflow(workflow_id, result.get('error', 'Unknown error'))
            return {'status': 'error', 'workflow_id': workflow_id, 'error': result.get('error')}
            
    except Exception as e:
        logger.error(f"Workflow2 task failed: {str(e)}", exc_info=True)
        workflow_service.fail_workflow(workflow_id, str(e))
        raise


@celery_app.task(name='celery_tasks.workflow_tasks.workflow3_task', bind=True)
def workflow3_task(self, workflow_id, user_id, data):
    """
    Task Celery pour Workflow 3 (Cluster)
    """
    logger.info(f"Starting workflow3 task: {workflow_id}")
    
    try:
        workflow_service.create_workflow(
            user_id=user_id,
            workflow_id=workflow_id,
            workflow_type='cluster',
            input_params=data,
            total_steps=4
        )
        
        def update_progress(step, status, progress_percent=None):
            workflow_service.update_progress(workflow_id, progress_percent or (step * 25), step)
            self.update_state(
                state='PROGRESS',
                meta={
                    'current_step': step,
                    'total_steps': 4,
                    'progress': progress_percent or (step * 25)
                }
            )
        
        start_time = time.time()
        result = workflow_manager_3.execute_workflow3_sync(data, progress_callback=update_progress)
        generation_time = int(time.time() - start_time)
        
        if result.get('status') == 'success' and 'cluster' in result:
            import json
            
            # Save pillar
            if 'pillar' in result['cluster']:
                pillar = result['cluster']['pillar']
                workflow_service.save_to_minio(workflow_id, 'pillar.html', pillar.get('html_content', ''), compress=True)
                workflow_service.save_to_minio(workflow_id, 'article_main.html', pillar.get('html_content', ''), compress=True)
                
                pillar_metadata = {
                    'seo_title': pillar.get('seo_title', ''),
                    'meta_description': pillar.get('meta_description', ''),
                    'wordpress_excerpt': pillar.get('wordpress_excerpt', ''),
                    'image_url': pillar.get('image_url', ''),
                    'faq_json': pillar.get('faq_json', []),
                    'secondary_keywords': pillar.get('secondary_keywords', []),
                    'word_count': pillar.get('word_count', 0)
                }
                workflow_service.save_to_minio(workflow_id, 'pillar_metadata.json', json.dumps(pillar_metadata, ensure_ascii=False), compress=True)
            
            # Save satellites
            if 'satellites' in result['cluster']:
                for i, satellite in enumerate(result['cluster']['satellites'], 1):
                    workflow_service.save_to_minio(workflow_id, f'satellite_{i}.html', satellite.get('html_content', ''), compress=True)
                    
                    sat_metadata = {
                        'seo_title': satellite.get('seo_title', ''),
                        'meta_description': satellite.get('meta_description', ''),
                        'wordpress_excerpt': satellite.get('wordpress_excerpt', ''),
                        'image_url': satellite.get('image_url', ''),
                        'faq_json': satellite.get('faq_json', []),
                        'secondary_keywords': satellite.get('secondary_keywords', []),
                        'word_count': satellite.get('word_count', 0)
                    }
                    workflow_service.save_to_minio(workflow_id, f'satellite_{i}_metadata.json', json.dumps(sat_metadata, ensure_ascii=False), compress=True)
            
            workflow_service.complete_workflow(workflow_id, result, generation_time)
            return {'status': 'success', 'workflow_id': workflow_id, 'result': result}
        else:
            workflow_service.fail_workflow(workflow_id, result.get('error', 'Unknown error'))
            return {'status': 'error', 'workflow_id': workflow_id, 'error': result.get('error')}
            
    except Exception as e:
        logger.error(f"Workflow3 task failed: {str(e)}", exc_info=True)
        workflow_service.fail_workflow(workflow_id, str(e))
        raise
