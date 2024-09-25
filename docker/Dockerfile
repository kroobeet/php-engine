# Используем официальный образ PHP с поддержкой FPM
FROM php:8.2-fpm

# Установка необходимых PHP-расширений
RUN docker-php-ext-install pdo pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем все файлы приложения в контейнер
COPY . /var/www/html

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Установка рабочего каталога
WORKDIR /var/www/html

# Используем отдельный Nginx контейнер
