# Stage 1: Build stage for composer dependencies
FROM composer:latest AS builder
WORKDIR /app

# Copy dependency files first to leverage Docker cache
COPY composer.json composer.lock* ./

# Install dependencies. 
# We use --ignore-platform-reqs because the composer image does not have the mongodb extension installed.
# This is safe as long as we install the extension in the final stage.
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --ignore-platform-reqs
RUN composer require mongodb/mongodb:2.5.0 --ignore-platform-reqs

# Copy the rest of the application source code to generate the optimized autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize

# Stage 2: Final production image
FROM trafex/php-nginx

# Switch to root to install system dependencies
USER root

# Install the required PHP mongodb extension
RUN apk add --no-cache php85-mongodb

# Clear the default directory to ensure a clean environment
RUN rm -rf /var/www/html/*

# Set the working directory for the application
WORKDIR /var/www/html

# Copy the application files from the host
COPY public ./public
COPY internal ./internal
COPY public/index.html ./public/

# Copy the vendor directory from the builder stage
COPY --from=builder /app/vendor ./vendor

# Update Nginx configuration to point to the /public directory
RUN sed -i "s/\/var\/www\/html/\/var\/www\/html\/public/g" /etc/nginx/conf.d/default.conf

# Switch back to the non-privileged user
USER nobody