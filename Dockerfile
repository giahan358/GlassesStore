FROM php:8.2-apache

# 1. Cài đặt các thư viện hệ thống cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql mbstring

# 2. DIỆT TẬN GỐC LỖI "More than one MPM loaded"
# Thay vì chỉ tắt (disable), ta xóa thẳng file load module event để Apache không bao giờ tìm thấy nó nữa.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# 3. Cấu hình DocumentRoot trỏ vào thư mục /public của Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/htdocs!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Thiết lập thư mục làm việc và copy code
WORKDIR /var/www/html
COPY . /var/www/html/

# 5. Cấp quyền ghi file cho Laravel (Quan trọng để không bị lỗi 500)
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 6. Mở cổng 80 cho Apache
EXPOSE 80