#!/bin/bash

echo "🛑 Arrêt des serveurs..."

# Tuer les processus Python et PHP
pkill -f "python app.py"
pkill -f "php -S localhost:8000"

echo "✅ Serveurs arrêtés"
