# --- Etapa 1: compilar assets con Vite ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Etapa 2: imagen final PHP + Nginx ---
FROM richarvey/nginx-php-fpm:latest

COPY --from=assets /app /var/www/html

ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY conf/nginx/nginx-site.conf /etc/nginx/sites-available/default.conf

CMD ["/start.sh"]