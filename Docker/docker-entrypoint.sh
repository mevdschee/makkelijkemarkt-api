#!/usr/bin/env bash
echo Starting server

set -u
set -e

DB_HOST=${SYMFONY__MM_API__DATABASE__HOST:-makkelijkemarkt-db.service.consul}
DB_PORT=${SYMFONY__MM_API__DATABASE__PORT:-5432}

cat > /app/app/config/parameters.yml <<EOF
parameters:
    database_host:      ${DB_HOST}
    database_port:      ${DB_PORT}
    database_name:      ${SYMFONY__MM_API__DATABASE__NAME}
    database_user:      ${SYMFONY__MM_API__DATABASE__USER}
    database_password:  ${SYMFONY__MM_API__DATABASE__PASSWORD}
    mailer_transport:   ${SYMFONY__MM_API__MAILER__TRANSPORT}
    mailer_host:        ${SYMFONY__MM_API__MAILER__HOST}
    mailer_user:        ${SYMFONY__MM_API__MAILER__USER}
    mailer_password:    ${SYMFONY__MM_API__MAILER__PASSWORD}
    mailer_port:        ${SYMFONY__MM_API__MAILER__PORT}
    mailer_encryption:  ${SYMFONY__MM_API__MAILER__ENCRYPTION}
    secret:             ${SYMFONY__MM_API__SECRET}
    android_version:    1.0.5
    android_build:      2016050517
    mm_app_key:         ${SYMFONY__MM_API__APP_KEY}
EOF

php composer.phar install

cd /app/app
php console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";"
php console doctrine:migrations:status
#php console --no-interaction doctrine:migrations:migrate
php console cache:clear --env=prod
php console cache:warmup --env=prod
chown -R www-data:www-data /app/app/cache && find /app/app/cache -type d -exec chmod -R 0770 {} \; && find /app/app/cache -type f -exec chmod -R 0660 {} \;
php console assetic:dump --env=prod

service php7.0-fpm start
nginx -g "daemon off;"
