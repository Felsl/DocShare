FROM php:8.2-apache

# Enable Apache mod_rewrite (cần cho MVC, .htaccess)
RUN a2enmod rewrite

# Cài extension PHP cần thiết
RUN docker-php-ext-install pdo pdo_mysql

# Set thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ source vào container
COPY . /var/www/html

# Set quyền cho thư mục upload
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/site/uploads

# Apache config: cho phép .htaccess
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

EXPOSE 80
