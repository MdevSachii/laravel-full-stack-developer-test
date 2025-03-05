FROM php:8.2.16-apache

RUN apt-get update && apt-get install -y lsb-release curl sudo nano git zip unzip libzip-dev zip gnupg dirmngr

RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://repo.charm.sh/apt/gpg.key | sudo gpg --dearmor -o /etc/apt/keyrings/charm.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/charm.gpg] https://repo.charm.sh/apt/ * *" | sudo tee /etc/apt/sources.list.d/charm.list

RUN apt-get update && apt-get install -y gum

RUN curl -sL https://deb.nodesource.com/setup_18.x | sudo -E bash - &&\
    apt-get -y install nodejs &&\
    ln -s /usr/bin/nodejs /usr/local/bin/node

# 2. apache configs + document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

# 4. Install PHP extensions
# RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql pcntl

RUN pecl install redis && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd

RUN apt-get update && \
    apt-get install -y libxml2-dev

RUN docker-php-ext-install soap

RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*

# RUN mkdir -p /usr/src/php/ext/imagick; \
#     curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
#     docker-php-ext-install imagick;

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --version=2.5.8

RUN pecl install excimer

# COPY devtools /usr/bin/

# RUN chmod +x /usr/bin/devtools

ARG uid

RUN useradd -G www-data,root -u $uid -d /home/app app-user

RUN mkdir -p /home/app/.composer && \
    chown -R app-user:app-user /home/app && \
    mkdir /var/www/html/storage && \
    chown -R 777 /var/www/html/storage
