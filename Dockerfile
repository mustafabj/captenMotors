FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    unzip curl libzip-dev zip libonig-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring intl zip

    RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Enable mod_rewrite
RUN a2enmod rewrite

# Set Apache document root to /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory "/var/www/html/public">\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>' > /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel
