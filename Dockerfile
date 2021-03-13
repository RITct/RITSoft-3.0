FROM php:7.4.1-apache

RUN apt-get update -y && apt-get install -y libmcrypt-dev openssl
RUN pecl install mcrypt-1.0.3 && docker-php-ext-enable mcrypt
RUN docker-php-ext-install pdo pdo_mysql

# Node/NPM
ENV NODE_VERSION=14.0.0
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./src /ritsoft
WORKDIR /ritsoft
RUN composer install \
&& npm install

CMD ["./startserver.sh"]
