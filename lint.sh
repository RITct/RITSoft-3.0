set -e
docker-compose run app ./vendor/bin/phpcs --ignore=./database/migrations --standard=PSR12 --extensions=php \
./app ./routes ./tests ./database