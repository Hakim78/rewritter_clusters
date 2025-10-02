"""
Script de migration : Importer les fichiers .txt de prompts vers la base de données
Fichier: backend/scripts/migrate_prompts_to_db.py

Usage:
    python backend/scripts/migrate_prompts_to_db.py
"""

import os
import sys
from pathlib import Path
from datetime import datetime

# Ajouter le répertoire parent au path
sys.path.insert(0, str(Path(__file__).parent.parent))

from dotenv import load_dotenv
import pymysql

# Charger les variables d'environnement
load_dotenv()

def get_db_connection():
    """Créer une connexion à la base de données"""
    return pymysql.connect(
        host=os.getenv('DB_HOST'),
        port=int(os.getenv('DB_PORT', 3306)),
        user=os.getenv('DB_USER'),
        password=os.getenv('DB_PASSWORD'),
        database=os.getenv('DB_NAME'),
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

def get_admin_user_id(conn):
    """Récupérer l'ID du premier utilisateur admin"""
    with conn.cursor() as cursor:
        cursor.execute("SELECT id FROM users WHERE role = 'admin' ORDER BY id ASC LIMIT 1")
        result = cursor.fetchone()
        if result:
            return result['id']
        else:
            raise Exception("Aucun utilisateur admin trouvé dans la base de données")

def import_template_file(conn, workflow_id, file_path, user_id):
    """Importer un fichier template dans la base de données"""

    # Vérifier si le fichier existe
    if not os.path.exists(file_path):
        print(f"⚠️  Fichier non trouvé: {file_path}")
        return False

    # Lire le contenu du fichier
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    if not content.strip():
        print(f"⚠️  Fichier vide: {file_path}")
        return False

    try:
        with conn.cursor() as cursor:
            # Désactiver tous les templates actifs pour ce workflow
            cursor.execute("""
                UPDATE prompt_templates
                SET is_active = FALSE
                WHERE workflow_id = %s AND is_active = TRUE
            """, (workflow_id,))

            # Obtenir le prochain numéro de version
            cursor.execute("""
                SELECT COALESCE(MAX(version), 0) + 1 as next_version
                FROM prompt_templates
                WHERE workflow_id = %s
            """, (workflow_id,))

            next_version = cursor.fetchone()['next_version']

            # Insérer le nouveau template
            cursor.execute("""
                INSERT INTO prompt_templates
                (workflow_id, version, content, created_by, is_active, notes, backup_file)
                VALUES (%s, %s, %s, %s, TRUE, %s, %s)
            """, (
                workflow_id,
                next_version,
                content,
                user_id,
                f'Migration initiale depuis fichier .txt (version {next_version})',
                file_path
            ))

            template_id = cursor.lastrowid

            # Logger l'action dans l'audit trail
            cursor.execute("""
                INSERT INTO prompt_audit_log
                (template_id, action, user_id, details)
                VALUES (%s, 'create', %s, %s)
            """, (
                template_id,
                user_id,
                f'{{"source": "migration", "file": "{file_path}", "workflow": {workflow_id}}}'
            ))

        conn.commit()
        print(f"✓ Workflow {workflow_id} - Version {next_version} importée ({len(content)} caractères)")
        return True

    except Exception as e:
        conn.rollback()
        print(f"✗ Erreur lors de l'import du workflow {workflow_id}: {e}")
        return False

def main():
    """Fonction principale de migration"""

    print("=" * 60)
    print("🔄 Migration des prompts templates vers la base de données")
    print("=" * 60)
    print()

    # Chemins des fichiers templates
    base_path = Path(__file__).parent.parent / 'workflows'

    templates_to_import = [
        (1, base_path / 'workflow_1' / 'templates' / 'article_prompt_template.txt'),
        (2, base_path / 'workflow_2' / 'templates' / 'article_prompt_template.txt'),
        (3, base_path / 'workflow_3' / 'templates' / 'article_prompt_template.txt'),
    ]

    try:
        # Connexion à la BDD
        print("📡 Connexion à la base de données...")
        conn = get_db_connection()
        print("✓ Connecté à la base de données")
        print()

        # Récupérer l'ID admin
        user_id = get_admin_user_id(conn)
        print(f"👤 Utilisateur admin trouvé (ID: {user_id})")
        print()

        # Importer chaque template
        print("📦 Import des templates...")
        print()

        success_count = 0
        for workflow_id, file_path in templates_to_import:
            if import_template_file(conn, workflow_id, str(file_path), user_id):
                success_count += 1

        print()
        print("=" * 60)
        print(f"✅ Migration terminée : {success_count}/{len(templates_to_import)} templates importés")
        print("=" * 60)
        print()

        # Afficher un résumé
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT
                    workflow_id,
                    COUNT(*) as versions_count,
                    MAX(version) as latest_version
                FROM prompt_templates
                GROUP BY workflow_id
                ORDER BY workflow_id
            """)

            results = cursor.fetchall()

            if results:
                print("📊 Résumé des templates en base de données :")
                print()
                for row in results:
                    print(f"   Workflow {row['workflow_id']}: {row['versions_count']} version(s), dernière v{row['latest_version']}")
                print()

        conn.close()

    except Exception as e:
        print(f"❌ Erreur fatale : {e}")
        sys.exit(1)

if __name__ == '__main__':
    main()