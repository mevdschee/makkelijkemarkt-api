gemeenteamsterdam-makkelijkemarkt-api
=================================


How to set-up:

    # set up database your database with commands like
    # sudo -u postgres createuser -D -A -P env_makkelijkemarkt_api
    # sudo -u postgres createdb -O env_makkelijkemarkt_api env_makkelijkemarkt_api
    # sudo -u postgres psql env_makkelijkemarkt_api -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp"'
    
    # clone the code
    hg clone ssh://hg@bitbucket.org/datalabamsterdam/makkelijkemarkt-api
    cd makkelijkemarkt-api
    hg up master

    # install composer and run it
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install
        
    # set up file system permissions: www-data, recursive
    sudo setfacl -R -m g:www-data:rwx app/cache
    sudo setfacl -R -m g:www-data:rwx app/logs
    sudo setfacl -R -m g:www-data:rwx web/media
    # set up file system permissions: www-data, default recursive
    sudo setfacl -R -m d:g:www-data:rwx app/cache
    sudo setfacl -R -m d:g:www-data:rwx app/logs
    sudo setfacl -R -m d:g:www-data:rwx web/media
    
    # init database
    php app/console doctrine:migrations:migrate --env=prod
  


Make it shine:

* Configure your vhost to automaticly rewrite to app.php, Examples for Nginx are given in the extra-directory


Server requirements:

* PHP 5.5
* Nginx or IIS, Apache should also run fine
* Some glue between your webserver and PHP (like PHP-FPM or Apache PHP mod, IIS FastCgi)


Imports:

* Markt: php app/console makkelijkemarkt:import:perfectview:markt Markt.csv --env=prod
* Koopman: php app/console makkelijkemarkt:import:perfectview:koopman Koopman.csv --env=prod
* Sollicitatie: php app/console makkelijkemarkt:import:perfectview:sollicitatie Sollicitatie.csv --env=prod
