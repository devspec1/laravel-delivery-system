FROM php:7.4.3-apache

# Set working directory
WORKDIR /var/www/html/

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libonig-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip4 \
    libzip-dev \
    libicu-dev \
    libbz2-dev \
    libreadline-dev \
    g++

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# apache configs + document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN openssl req -new -newkey rsa:4096 -days 3650 -nodes -x509 -subj \
    "/C=../ST=...../L=..../O=..../CN=..." \
    -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.crt

RUN openssl x509 -in /etc/ssl/certs/ssl-cert-snakeoil.crt -out /etc/ssl/certs/ssl-cert-snakeoil.pem -outform PEM

RUN ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf

# mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers expires deflate filter ssl

# start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"


# Install extensions
RUN docker-php-ext-install \
    bz2 \
    intl \
    iconv \
    bcmath \
    opcache \
    calendar \
    pdo_mysql \
    zip \
    exif \
    pcntl \
    gd \
    tokenizer

    
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type f -exec chmod 664 {} \; 
RUN find /var/www/html -type d -exec chmod 775 {} \;
RUN chmod 775 .env

# # Generate artisan key
#RUN php artisan key:generate

# #  php artisan make:auth
#RUN php artisan make:auth

# #Install laravel requirements
# RUN composer install

# #Migrate db
# RUN php artisan migrate:fresh --seed
