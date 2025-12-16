# Simple PHP-FPM + Nginx container suitable for Cloud Run
# - PHP-FPM handles PHP execution
# - Nginx serves static files and proxies PHP requests to PHP-FPM

FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx supervisor

# Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Supervisor configuration to run both Nginx and PHP-FPM in a single container
COPY supervisord.conf /etc/supervisord.conf

# Application code
WORKDIR /var/www/html
COPY public/ /var/www/html/
COPY src/ /var/www/html/src/
COPY bootstrap.php /var/www/html/bootstrap.php

EXPOSE 8080

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
