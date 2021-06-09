# Use this script if your user id or group id != 1000
docker build . --build-arg UID=$(id -u) --build-arg GID=$(id -g)
docker-compose up -d
