FROM php:8.3-apache

# Extensión pdo_pgsql (PostgreSQL/Neon) + módulos de Apache que usa el .htaccess del proyecto
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Documento raíz = public/ con AllowOverride para respetar los .htaccess del proyecto
RUN printf '%s\n' \
    '<VirtualHost *:*>' \
    '    DocumentRoot /var/www/html/public' \
    '    <Directory /var/www/html/public>' \
    '        AllowOverride All' \
    '        Require all granted' \
    '    </Directory>' \
    '</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .
RUN mkdir -p storage/logs storage/uploads \
    && chown -R www-data:www-data storage

# Puerto que Render inyecta en la variable PORT (por defecto 10000)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
