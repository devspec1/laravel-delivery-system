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

RUN mkdir -p /root/.ssh
RUN touch /root/.ssh/deploy_key

RUN echo """-----BEGIN RSA PRIVATE KEY-----""" >> /root/.ssh/deploy_key
RUN echo """MIIEowIBAAKCAQEA5RffRP4gDgYwRQrJmfee8+bUAkQ51WEBgzfR/7x8gC2iqf/R""" >> /root/.ssh/deploy_key
RUN echo """q+jqo3FgVNrfzHb9VrUPXk+OP7netczHrPu8Uw+hWbRfrEr1uzxTqjodKyPcrSy9""" >> /root/.ssh/deploy_key
RUN echo """OQ+O9uJbC/xF6gL0xeg8s3ij0P6bpxFew+0OCyj+f4TcqdNHoaoR3yF4P+m4K1N/""" >> /root/.ssh/deploy_key
RUN echo """H3XOagMRgA6/yeah5JAtBVLXBzG0pfZQRc0KVUMALR7AxgxrFwkUtNxvHkyjX+RO""" >> /root/.ssh/deploy_key
RUN echo """3NbhIP9aHejTRVLhl4sVNd71T4WjBRNvlPtU6tLCfAUsCxMjVni0FStNGcF6jWHC""" >> /root/.ssh/deploy_key
RUN echo """pS21Fv5WHjCi1u7+rX6tjstQ0Jfh1dC4AF6WAQIDAQABAoIBACbCczPpCuNUiLYl""" >> /root/.ssh/deploy_key
RUN echo """rRYvnuUFGLmJcX94ehkJSK5L5YSKNAsvDPpY3dnpRLMWlOay+wnAeGksbJS7jcKU""" >> /root/.ssh/deploy_key
RUN echo """cvQBY0tqSZIzwjwUMp8LBdy3GFhEKQHjn+AXkciTY3QTqoB9H4ycTijPMGgbq5oI""" >> /root/.ssh/deploy_key
RUN echo """hk//u3CH7wcEI5FX/baMX5mwv/eP8XahmzLq+kpXlwSWtkmdB4/sRdKMqnBQovlr""" >> /root/.ssh/deploy_key
RUN echo """xH01XLIFF03Xbs7/Q6SRD7ALa6gI74MaYqHWHTlFgt3QBQQ4BQPsLNqXdgcQBcMd""" >> /root/.ssh/deploy_key
RUN echo """TUMm1PPcj/a0z3scJ5IrUwqt+XEFNPfns4QEL7H8xUfXT1eJBQ0uv/XfKXAW1h+5""" >> /root/.ssh/deploy_key
RUN echo """iiGqrkECgYEA8iztMASY5S7Y+qZyI8luda/JoCb//WXviQVzgZSwI0kr1N9aD4Nd""" >> /root/.ssh/deploy_key
RUN echo """oH0aWC2OlMhOdPQq/LHdizCqpHu8tKodNE7hOqQMd/lfOBN6L/r3k3tGpGyCfaFC""" >> /root/.ssh/deploy_key
RUN echo """EVLkInErWhYh4FhhCqGsSuFb0aJk3+8s4tiH8Rt0JZYWtcwOGuTa4hMCgYEA8ivE""" >> /root/.ssh/deploy_key
RUN echo """GLKMEziI/u0+KL0pF5bzHbQnVFfcI0SOrTzkJ4X0OsBQZ/gbViYy+pBIaTeYa3by""" >> /root/.ssh/deploy_key
RUN echo """cmTE5sLUnS0A1V2WP6nKWAMhh1g6EdgJ6Me6DmUrPn45BrQrJOR7sr+ulZO5UpvG""" >> /root/.ssh/deploy_key
RUN echo """dT/pIghHeaoM+xUAnhcACYzZlgE9bUWctNw8ChsCgYB8ZomcjfAAYnVBJDkjmvhr""" >> /root/.ssh/deploy_key
RUN echo """6dXXt9Dt2OwX5b30xW1JYu/qFKWNrHxu0XSz8Qr58H8k4rwmPDPCqUgu4AUKhQwl""" >> /root/.ssh/deploy_key
RUN echo """b5OQ7O4evvGju5Wbif6dOskJ81eAs1Jd1ceszZdoWlAijyOiM3Rurp7c69+HjLPw""" >> /root/.ssh/deploy_key
RUN echo """/yuttd2O5S9bSavBMughEwKBgQC4Lrkx33nRlIn8+QrxiQybuF6nFMFk0H3JBPdO""" >> /root/.ssh/deploy_key
RUN echo """oqUTujmKBYIh0P1ZhCv5jYrFG1d9RDYY8rMensd90yBzJn6DZOtUDO2PNnbT42+F""" >> /root/.ssh/deploy_key
RUN echo """74F+OUuud+l/Q8AcivnZdRefA39LaNaDjlwNWiaiTccZn1uc4PlSSiGiiMbjOLJ6""" >> /root/.ssh/deploy_key
RUN echo """i5XOSQKBgAMxyTHqolPAPupwyCwSf5YojyqWuv66zr3FIrkKawPFyJw8UBYbFnWF""" >> /root/.ssh/deploy_key
RUN echo """HTk07vIEW4dx8+KoLMrNIZwH1eZtnQcjDEc/er9fMi4HQmJ3nmxGTjmWrYxNbHwh""" >> /root/.ssh/deploy_key
RUN echo """bCNVMd8GYMoNTxFm61na8qGQ5aL5Skj5C4fD1XPB6S1U0bq/eMYx""" >> /root/.ssh/deploy_key
RUN echo """-----END RSA PRIVATE KEY-----""" >> /root/.ssh/deploy_key

RUN chmod 600 /root/.ssh/deploy_key

RUN touch /root/.ssh/config

RUN echo """Host gitlab.srv.rideon.co""" >> /root/.ssh/config
RUN echo """    HostName gitlab.srv.rideon.co"""
RUN echo """    User deploy_user""" >> /root/.ssh/config
RUN echo """    IdentityFile /root/.ssh/deploy_key""" >> /root/.ssh/config
RUN echo """    StrictHostKeyChecking no""" >> /root/.ssh/config
RUN echo """    UserKnownHostsFile=/dev/null""" >> /root/.ssh/config

#RUN chmod 775 .env

# # Generate artisan key
#RUN php artisan key:generate

# #  php artisan make:auth
#RUN php artisan make:auth

# #Install laravel requirements
# RUN composer install

# #Migrate db
# RUN php artisan migrate:fresh --seed
