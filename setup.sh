#!/bin/bash

echo "🔧 Setting up Laravel backend..."
cd backend

if [ ! -f .env ]; then
  cp .env.example .env
  echo ".env file copied"
fi

composer install
php artisan key:generate
php artisan migrate  # Optional, comment if not needed

cd ..


echo " Setting up Nuxt frontend..."
cd frontend
npm install

cd ..

echo "✅ Setup complete. Starting apps..."
chmod +x start.sh
./start.sh
