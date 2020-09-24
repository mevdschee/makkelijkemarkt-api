#!/usr/bin/env bash
echo Starting server

set -u
set -e

cat > /app/app/config/parameters.yml <<EOF
parameters:
    database_host:      ${MM_API__DATABASE__HOST}
    database_port:      ${MM_API__DATABASE__PORT}
    database_name:      ${MM_API__DATABASE__NAME}
    database_user:      ${MM_API__DATABASE__USER}
    database_password:  ${MM_API__DATABASE__PASSWORD}
    mailer_transport:   ${MM_API__MAILER__TRANSPORT}
    mailer_host:        ${MM_API__MAILER__HOST}
    mailer_user:        ${MM_API__MAILER__USER}
    mailer_password:    ${MM_API__MAILER__PASSWORD}
    mailer_port:        ${MM_API__MAILER__PORT}
    mailer_encryption:  ${MM_API__MAILER__ENCRYPTION}
    secret:             ${MM_API__SECRET}
    android_version:    1.4.6
    android_build:      2017020244
    mm_app_key:         ${MM_API__APP_KEY}
    trusted_proxies: [10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16]
EOF

php composer.phar install

cd /app/app
php console doctrine:migrations:status
#php console --no-interaction doctrine:migrations:migrate
php console cache:clear --env=prod
php console cache:warmup --env=prod
chown -R www-data:www-data /app/app/cache && find /app/app/cache -type d -exec chmod -R 0770 {} \; && find /app/app/cache -type f -exec chmod -R 0660 {} \;
php console assetic:dump --env=prod

# Configure access to /download URL
mkdir -p /etc/nginx/htpasswd.d
echo -e $MM_API__NGINX_HTPASSWD > /etc/nginx/htpasswd.d/makkelijkemarkt-api.amsterdam.nl

# Make sure log files exist, so tail won't return a non-zero exitcode
touch /app/app/logs/dev.log
touch /app/app/logs/prod.log
touch /var/log/nginx/access.log
touch /var/log/nginx/error.log

tail -f /app/app/logs/dev.log &
tail -f /app/app/logs/prod.log &
tail -f /var/log/nginx/access.log &
tail -f /var/log/nginx/error.log &

chgrp www-data /app/app/logs/*.log
chmod 775 /app/app/logs/*.log

nginx

chgrp -R www-data /var/lib/nginx
chmod -R 775 /var/lib/nginx/tmp

php-fpm -F
