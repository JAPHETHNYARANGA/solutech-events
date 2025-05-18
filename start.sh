#!/bin/bash

echo "Starting Laravel backend..."
cd backend
php artisan serve &
LARAVEL_PID=$!
cd ..

echo "Starting Nuxt frontend..."
cd frontend
npm run dev &
NUXT_PID=$!
cd ..

# Wait for both processes
trap "kill $LARAVEL_PID $NUXT_PID" SIGINT
wait $LARAVEL_PID $NUXT_PID
