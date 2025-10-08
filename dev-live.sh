#!/bin/bash

echo "🔥 Mode Dev avec Live Reload dans le navigateur"
echo ""

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Cleanup
cleanup() {
    echo ""
    echo "🛑 Arrêt des serveurs..."
    kill $BACKEND_PID $BROWSERSYNC_PID 2>/dev/null
    pkill -f "php -S localhost:8000"
    pkill -f "browser-sync"
    exit 0
}

trap cleanup INT TERM

# 1. Lancer Backend Python (avec auto-reload Flask intégré)
echo -e "${BLUE}🐍 Backend Python sur port 5001${NC}"
cd backend
source venv/bin/activate
python app.py > ../logs/python/app.log 2>&1 &
BACKEND_PID=$!
cd ..

sleep 2

# 2. Lancer PHP sur port 8000
echo -e "${BLUE}🌐 Frontend PHP sur port 8000${NC}"
cd frontend/public
php -S localhost:8000 > ../../logs/php/server.log 2>&1 &
cd ../..

sleep 2

# 3. Lancer Browser-sync qui proxie vers PHP et live-reload
echo -e "${BLUE}🔄 Browser-sync avec Live Reload sur port 3000${NC}"
browser-sync start \
    --proxy "localhost:8000" \
    --port 3000 \
    --files "frontend/**/*.php, frontend/**/*.html, frontend/**/*.css, frontend/**/*.js, backend/**/*.py" \
    --no-notify \
    --no-open &
BROWSERSYNC_PID=$!

sleep 3

echo ""
echo -e "${GREEN}✅ Environnement de développement lancé !${NC}"
echo ""
echo -e "${YELLOW}📍 URLs importantes :${NC}"
echo -e "   ${GREEN}• Ouvre cette URL : http://localhost:3000${NC} �� (avec Live Reload)"
echo "   • Backend API : http://localhost:5001"
echo "   • PHP direct : http://localhost:8000 (sans live reload)"
echo ""
echo -e "${YELLOW}♻️  Live Reload activé sur :${NC}"
echo "   • Fichiers PHP : frontend/**/*.php"
echo "   • Assets : frontend/**/*.{css,js,html}"
echo "   • Python : backend/**/*.py"
echo ""
echo -e "${YELLOW}✨ La page se rafraîchira automatiquement à chaque sauvegarde !${NC}"
echo ""
echo "⚠️  Appuie sur Ctrl+C pour arrêter"
echo ""

# Garder actif
wait
