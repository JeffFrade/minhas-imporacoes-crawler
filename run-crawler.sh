#!/bin/bash

docker-compose up -d
docker exec -it minhas-importacoes-crawler-php-fpm php artisan crawler:start
