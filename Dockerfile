FROM php:8.2-apache

# 1. Cài đặt các thư viện hệ thống và PHP extension cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql mbstring

# 2. FIX LỖI "More than one MPM loaded" VÀ BẬT REWRITE
# Tắt mpm_event, bật mpm_prefork và rewrite module
RUN a2dismod mpm_event || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# 3. Thay đổi DocumentRoot (Sửa lỗi Legacy format bằng cách dùng dấu =)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/htdocs!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Copy code vào container
COPY . /var/www/html/

# 5. Cấp quyền và đảm bảo thư mục tồn tại
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80