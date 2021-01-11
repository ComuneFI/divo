#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

apt-get update -yqq
apt-get install gnupg2 -yqq
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
apt-get update -yqq
apt-get install git yarn libmcrypt-dev libpq-dev libcurl4-gnutls-dev libicu-dev libvpx-dev libjpeg-dev libpng-dev libxpm-dev zlib1g-dev libfreetype6-dev libxml2-dev libexpat1-dev libbz2-dev libgmp3-dev libldap2-dev unixodbc-dev libsqlite3-dev libaspell-dev libsnmp-dev libpcre3-dev libtidy-dev -yqq

# Optionally install Xdebug
#pecl install xdebug-2.9.6
#docker-php-ext-enable xdebug

# Install PHP extensions
# docker-php-ext-install mbstring pdo_pgsql curl json intl gd xml zip bz2 opcache
docker-php-ext-install gd zip pdo_pgsql

# Install and run Composer
curl -sS https://getcomposer.org/installer | php
php composer.phar config -g secure-http false
#php composer.phar global require hirak/prestissimo
php composer.phar install
#yarn install
#yarn upgrade
#yarn encore prod
