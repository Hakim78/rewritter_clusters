#!/bin/bash

echo "🚀 Démarrage de SEO Article Generator..."
echo ""

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

# Fonction pour cleanup
cleanup() {
    echo ""
    echo "🛑 Arrêt des serveurs..."
    kill $BACKEND_PID $FRONTEND_PID 2>/dev/null
    exit 0
}

trap cleanup INT TERM

# Lancer le backend Python
echo -e "${BLUE}📡 Démarrage Backend Python...${NC}"
cd backend
source venv/bin/activate

# Vérifier et installer les dépendances
echo -e "${BLUE}📦 Vérification des dépendances...${NC}"
pip install -q -r requirements.txt
echo -e "${GREEN}✓ Dépendances installées${NC}"

python app.py > ../logs/python/app.log 2>&1 &
BACKEND_PID=$!

# Attendre que le backend démarre
sleep 2

# Revenir à la racine
cd ..

# Lancer le frontend PHP
echo -e "${BLUE}🌐 Démarrage Frontend PHP...${NC}"
cd frontend/public
php -S localhost:8000 > ../../logs/php/server.log 2>&1 &
FRONTEND_PID=$!

# Attendre que le frontend démarre
sleep 2

echo ""
echo -e "${GREEN}✅ Serveurs démarrés avec succès !${NC}"
echo ""
echo "📍 URLs disponibles :"
echo "   • Frontend: http://localhost:8000"
echo "   • Backend API: http://localhost:5001"
echo "   • Test: http://localhost:8000/test_connection.php"
echo ""
echo "📋 Logs :"
echo "   • Python: logs/python/app.log"
echo "   • PHP: logs/php/server.log"
echo ""
echo "⚠️  Appuie sur Ctrl+C pour arrêter les serveurs"
echo ""

# Garder le script actif et surveiller les processus
while kill -0 $BACKEND_PID 2>/dev/null && kill -0 $FRONTEND_PID 2>/dev/null; do
    sleep 1
done

echo "⚠️  Un des serveurs s'est arrêté de manière inattendue"
echo "Vérifiez les logs pour plus d'informations"
cleanup
