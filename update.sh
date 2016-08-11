hg in
hg pull
hg up
php composer.phar self-update
php composer.phar install
php app/console cache:clear --env=dev
php app/console cache:clear --env=prod
php app/console doctrine:migrations:migrate --env=prod
