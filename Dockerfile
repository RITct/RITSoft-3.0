FROM php:7.4.1-cli-buster

# USER SETUP
ARG UID=1000
ARG GID=1000
ENV UNAME=docker
RUN groupadd -g $GID -o $UNAME && useradd -m -u $UID -g $GID -o -s /bin/bash $UNAME

# DEPENDANCIES
RUN apt-get update -y && apt-get install -y libmcrypt-dev openssl libpq-dev zip unzip && pecl install mcrypt-1.0.3 && \
    docker-php-ext-enable mcrypt  && docker-php-ext-install pdo pdo_pgsql

# Node/NPM
ENV NODE_VERSION=14.0.0
USER $UNAME
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
ENV NVM_DIR=/home/$UNAME/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION} && . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION} && \
    . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="$NVM_DIR/versions/node/v${NODE_VERSION}/bin/:${PATH}"
USER root
# COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./src /ritsoft

WORKDIR /ritsoft

RUN cd /ritsoft && \
    composer install --prefer-dist && \
    npm install && \
    npm install browser-sync browser-sync-webpack-plugin@2.0.1 --save-dev --production=false && \
    chmod +x ./startserver.sh

CMD ["./startserver.sh"]
