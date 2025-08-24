FROM php:8.3-fpm

ARG uid
ARG user

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add user to www-data group
RUN useradd -u $uid -ms /bin/bash -g www-data $user

COPY . /var/www

# change all files ownership to user
COPY --chown=$user:www-data . /var/www

# switch to user
USER $user

EXPOSE 9000

CMD ["php-fpm"]







