FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Очистка кэша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Получение последней версии Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание системного пользователя для запуска команд Composer и Artisan
RUN useradd -G www-data,root -u 1000 -d /home/dev dev
RUN mkdir -p /home/dev/.composer && \
    chown -R dev:dev /home/dev

# Установка рабочей директории
WORKDIR /var/www

# Копирование существующего приложения
COPY . /var/www

# Установка прав доступа
RUN chown -R dev:dev /var/www

# Переключение на пользователя dev
USER dev

# Запуск PHP-FPM
CMD ["php-fpm"] 