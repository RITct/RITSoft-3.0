FROM php:7.3
RUN apt-get update
RUN apt-get install -y git-core curl build-essential openssl libssl-dev

# Node/NPM
ENV NODE_VERSION=14.0.0
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV DB_DATABASE=/ritsoft/database/database.sqlite
COPY ./src /ritsoft
RUN cd /ritsoft \
&& composer install \
&& npm install \
&& php artisan migrate

CMD ["php", "/ritsoft/artisan", "serve", "--host=0.0.0.0", "--port=8000"]
