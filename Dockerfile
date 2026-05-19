FROM php:8.2-fpm-alpine

# Cài đặt các extension cần thiết cho PostgreSQL (Supabase) và Laravel
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Cấu hình thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ source code vào container
COPY . .

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Cấu hình phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Thiết lập file cấu hình Nginx tối giản cho Laravel
RUN echo 'server { \
    listen 80; \
    root /var/www/html/public; \
    index index.php index.html; \
    location / { try_files $uri $uri/ /index.php?$query_string; } \
    location ~ \.php$ { \
        try_files $uri =404; \
        fastcgi_split_path_info ^(.+\.php)(/.+)$; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        include fastcgi_params; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        fastcgi_param PATH_INFO $fastcgi_path_info; \
    } \
}' > /etc/nginx/http.d/default.conf

# Chạy cả PHP-FPM và Nginx cùng lúc bằng lệnh khởi động
CMD php-fpm -D && nginx -g "daemon off;"