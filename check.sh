#!/bin/bash

echo "🔍 Vérification du statut des serveurs..."
echo ""

# Backend Python
if lsof -Pi :5001 -sTCP:LISTEN -t >/dev/null ; then
    echo "✅ Backend Python : Running sur port 5001"
else
    echo "❌ Backend Python : Not running"
fi

# Frontend PHP
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "✅ Frontend PHP : Running sur port 8000"
else
    echo "❌ Frontend PHP : Not running"
fi

echo ""

# Test API
echo "🧪 Test API Backend..."
if curl -s http://localhost:5001/api/test >/dev/null ; then
    echo "✅ API Backend : Accessible"
else
    echo "❌ API Backend : Non accessible"
fi
