FROM php:8.0-apache
WORKDIR /var/www/html
COPY composer.* ./

RUN apt-get update && apt-get install -y git unzip zip wget

RUN bash -c "wget https://getcomposer.org/download/2.1.9/composer.phar  && php composer.phar install  --no-autoloader"
COPY . ./
RUN bash -c "php composer.phar dump-autoload --no-scripts --ignore-platform-reqs"

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache

RUN a2enmod rewrite



