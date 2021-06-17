FROM php:8.0

ARG UID=1000
ARG GID=1000
ENV UNAME=docker
RUN groupadd -g $GID -o $UNAME && useradd -m -u $UID -g $GID -o -s /bin/bash $UNAME

# Dependancies
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends libpq-dev zip unzip \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Node/NPM
ENV NODE_VERSION=14.0.0
USER $UNAME
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
ENV NVM_DIR=/home/$UNAME/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION} && . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION} && \
    . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="$NVM_DIR/versions/node/v${NODE_VERSION}/bin/:${PATH}"
USER root

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./src /ritsoft

WORKDIR /ritsoft

RUN chown $UNAME -R /ritsoft

USER $UNAME

RUN cd /ritsoft && \
    composer install --prefer-dist && \
    npm install && \
    chmod +x ./startserver.sh && \
    chmod +x ./wait_for_it.sh

CMD ["./startserver.sh"]
