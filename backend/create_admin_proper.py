import sys
import os
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from services.user_service import UserService
from config import Config

# Créer la session DB
engine = create_engine(Config.SQLALCHEMY_DATABASE_URI)
Session = sessionmaker(bind=engine)
db = Session()

try:
    # Supprimer l'ancien
    from models.user import User  # Import relatif, sans "backend."
    old_user = db.query(User).filter(User.email == 'hakim@test.com').first()
    if old_user:
        db.delete(old_user)
        db.commit()
        print("✅ Ancien utilisateur supprimé")
    
    # Créer avec UserService (comme le ferait l'API)
    success, user, error = UserService.create_user(
        db=db,
        email='hakim@test.com',
        name='Hakim',
        password='password',
        role='admin',
        status='active'
    )
    
    if success:
        print(f"\n✅ Utilisateur créé avec succès !")
        print(f"Email: {user.email}")
        print(f"Nom: {user.name}")
        print(f"Rôle: {user.role}")
        print(f"Hash (30 premiers car): {user.password_hash[:30]}...")
        print(f"Longueur hash: {len(user.password_hash)}")
        
        # Test de vérification immédiat
        from services.auth_service import AuthService
        test = AuthService.verify_password('password', user.password_hash)
        print(f"\n🔐 Test de vérification: {'✅ SUCCÈS' if test else '❌ ÉCHEC'}")
        
        print(f"\nIdentifiants:")
        print(f"Email: hakim@test.com")
        print(f"Password: password")
    else:
        print(f"❌ Erreur: {error}")
        
except Exception as e:
    print(f"❌ Erreur: {e}")
    import traceback
    traceback.print_exc()
finally:
    db.close()