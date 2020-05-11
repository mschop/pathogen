ARG PHP_VERSION

FROM php:${PHP_VERSION}-cli

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update && apt-get -y upgrade \
    && apt-get -y --no-install-recommends install apt-utils unzip git

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions xdebug zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/bin/composer
