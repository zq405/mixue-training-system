# 使用官方 PHP Apache 镜像
FROM php:8.1-apache

# 安装必要的扩展
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 启用 Apache 重写模块
RUN a2enmod rewrite

# 设置工作目录
WORKDIR /var/www/html

# 复制所有项目文件到容器
COPY . /var/www/html/

# 设置正确的权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 配置 Apache 的 .htaccess 支持
RUN echo "<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# 暴露端口 80
EXPOSE 80