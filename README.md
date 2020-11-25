
# WARNING: Work-In-Progress!! 

Dit is de port van de API van Symfony 2 naar Symfony 5. Deze is NIET af.

# Makkelijke Markt: Dagelijkse administratie van straatmarkten

Makkelijke Markt is een project van de Gemeente Amsterdam. Meer informatie over dit project is te vinden op de website van het [Datalab van de Gemeente Amsterdam](http://www.datalabamsterdam.nl)

Makkelijke Markt bestaat uit drie repositories: [API](https://github.com/Amsterdam/makkelijkemarkt-api), [Android App](https://github.com/Amsterdam/makkelijkemarkt-androidapp), [Web dashboard](https://github.com/Amsterdam/makkelijkemarkt-dashboard)

Meer informatie [datapunt.ois@amsterdam.nl](datapunt.ois@amsterdam.nl)

## Waarom is deze code gedeeld

Het FIXXX-team van de Gemeente Amsterdam ontwikkelt software voor de gemeente.
Veel van deze software wordt vervolgens als open source gepubliceerd zodat andere
gemeentes, organisaties en burgers de software als basis en inspiratie kunnen 
gebruiken om zelf vergelijkbare software te ontwikkelen.
De Gemeente Amsterdam vindt het belangrijk dat software die met publiek geld wordt
ontwikkeld ook publiek beschikbaar is.

## Onderhoud en security

Deze repository bevat een "as-is" kopie van het project op moment van publiceren.
Deze kopie wordt niet actief onderhouden.

## Wat mag ik met deze code

De Gemeente Amsterdam heeft deze code gepubliceerd onder de Mozilla Public License v2.
Een kopie van de volledige licentie tekst is opgenomen in het bestand LICENSE.

Het FIXXX-team heeft de verdere doorontwikkeling van deze software overgedragen 
aan de probleemeigenaar. De code in deze repository zal dan ook niet actief worden
bijgehouden door het FIXXX-team.

## Open Source

Dit project maakt gebruik van diverse andere Open Source software componenten. O.a. 
[Symfony](http://www.symfony.com), 
[Doctrine](http://www.doctrine-project.org/), 
[Composer](https://getcomposer.org/), 
[Monolog](https://github.com/Seldaek/monolog), 
[Twig](http://twig.sensiolabs.org/), 
[Swiftmailer](http://swiftmailer.org/), 
[PHPExcel](https://github.com/PHPOffice/PHPExcel),
[ExcelBundle](https://github.com/liuggio/ExcelBundle),
[Nelmio API Doc](https://github.com/nelmio/NelmioApiDocBundle),
[Nelmio Cors Bundle](https://github.com/nelmio/NelmioCorsBundle)


## Installeren van de API

Om deze software te draaien moet je beschikking hebben over een webserver met PHP
(Apache, Nginx of IIS) en een PostgreSQL databaseserver.

Om de SMS functionaliteit te kunnen gebruiken heb je een contract nodig met een 
SMS service provider. Geïmplementeerd is de provider [MessageBird](https://www.messagebird.com/nl/).

Maak een nieuwe PostgreSQL database aan voor dit project. Voer met een superuser
onderstaand statement uit om de UUID functies beschikbaar te maken.

    CREATE EXTENSION "uuid-ossp";
    
Maak een clone van de code.

    git clone git@github.org:amsterdam/makkelijkemarkt-api.git
    cd makkelijkemarkt-api
    composer install
  
Afhankelijk van je systeem en de rechtenstructuur moet je sommige directories 
beschrijfbaar maken. Zie ook [Setting up or Fixing File Permissions in de handleiding van Symfony 2.7](http://symfony.com/doc/2.7/setup/file_permissions.html).
De volgende directories moeten schrijfbaar zijn voor Symfony

* app/cache
* app/logs
* web/media

Installeer composer en voer een composer install uit

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install

Voer Doctrine Migrations uit om de database te initialiseren
    
    php app/console doctrine:migrations:migrate

Voer tenslote een cache clear uit voor dev en prod om zeker te weten dat alle cache gereed is.    

    php app/console cache:clear --env=dev
    php app/console cache:clear --env=prod
    
Configueer tenslote een vhost van de webserver. Zie ook de specifieke handleiding 
per webserver [in de Symfony 2.7 handleiding](http://symfony.com/doc/2.7/setup/web_server_configuration.html)

## Cronjobs

Om gegevens uit Ceniam te importeren zijn de volgende imports beschikbaar, deze kunnen periodieke gedraaid worden 

* Markt: php app/console makkelijkemarkt:import:perfectview:markt Markt.csv --env=prod
* Koopman: php app/console makkelijkemarkt:import:perfectview:koopman Koopman.csv --env=prod
* Sollicitatie: php app/console makkelijkemarkt:import:perfectview:sollicitatie Sollicitatie.csv --env=prod
* Fotos: php app/console makkelijkemarkt:import:perfectview:foto Koopman.CSV fotos/ --env=prod

