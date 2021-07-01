set -e
docker-compose --profile test up -d
docker-compose run app php artisan test
docker-compose rm -s -f "test-db"