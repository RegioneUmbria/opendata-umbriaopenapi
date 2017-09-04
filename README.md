# Umbria Open API

A project ([UmbriaOpenAPI](https://umbriaopenapi.regione.umbria.it)) showing how to use Linked Open Data of [Regione Umbria](http://www.regione.umbria.it/home).

Features:
* geolocalized data is shown on a map

* web browsing of LOD data entities

* charts for statistical data

* RESTful API for data querying

* SPARQL language interactive manual

* Telegram BOT Server


Linked Open Data are retrieved from [Regione Umbria's catalog](http://dati.umbria.it/). [Here](http://dati.umbria.it/dataset/turismo-attrattori) you can find an example of a Linked Open Data dataset used.

These data should be retrieved periodically in RDF format. RDF entities are mapped as PHP objects, stored in a relational database and used by the portal.

## Get started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

The project require a webserver with PHP and MySQL modules and PHP CLI enabled.

### Installing

Clone or download this project.

Make sure to place the project under a directory your web server is configured to work with (we assume the directory is /var/www/opendata-umbriaopenapi).

Check if your environment satisfy Symfony requirements ([see](https://symfony.com/doc/current/reference/requirements.html))

Install project dependencies with [Composer](https://getcomposer.org/):

```cd /var/www/opendata-umbriaopenapi``` 

```composer install``` 

Create the database:  

```php app/console doctrine:database:create``` 

and tables with: 

``` php app/console doctrine:schema:update --force``` ([see](http://symfony.com/doc/current/doctrine.html)).

**NOTE** Symfony *console* file is placed under *app* directory instead of *bin* directory as in the last Symfony versions.


## Running
If well configured, the project homepage should be visible at ```http://localhost/opendata-umbriaopenapi/web/```

Download RDF data and persist them performing a PUT request at ```http://localhost/opendata-umbriaopenapi/web/entities_update/[0|1]/[0|1]/[0|1]/[0|1]/[0|1]/[0|1]/[0|1]/[0|1]/[0|1]``` .
Consult routing rule *umbria_open_api_entities_update* [here](src/Umbria/OpenApiBundle/Resources/config/routing.yml) to better understand how to call this service. Note it may be a large time consuming task.
This service can be called periodically (with cronjobs) to ensure data is always up to date.

## Run with Docker
Install [Docker](https://docs.docker.com/engine/installation/#server) and [Docker Compose](https://docs.docker.com/compose/install/#master-builds).

**NOTE** if you'are using docker with Windows:
 * place this project in a folder under your user's home directory
 * set the environment variable COMPOSE_CONVERT_WINDOWS_PATHS to 1 and restart docker.
 
From the root directory of this project create and run the environment:

``` docker-compose build```

``` docker-compose up -d```

Check if docker containers are running:

``` docker ps```

The output should be something like this:

```
CONTAINER ID        IMAGE                       COMMAND                  CREATED             STATUS              PORTS                    NAMES
0bf0274d3f59        opendataumbriaopenapi_php   "docker-php-entryp..."   21 minutes ago      Up 21 minutes       0.0.0.0:8888->80/tcp     php
a56f13aed63b        mysql                       "docker-entrypoint..."   About an hour ago   Up 21 minutes       0.0.0.0:3306->3306/tcp   mysql
```

Run bash in docker web service container:

``` docker exec -it web bash```

Install project dependencies, create database and set logs and cache folders permissions:

``` 
composer install
``` 

``` 
mkdir app/logs 
mkdir app/cache 
chown www-data:www-data app/logs/
chown www-data:www-data app/cache/

php app/console doctrine:database:create
php app/console doctrine:schema:update --force

exit
```

Map in your local host the IP address of the host where you installed docker with the umbriaopenapi.it domain. 
Example (docker running on your local host):
``` 
# /etc/hosts

127.0.0.1 umbriaopenapi.it
``` 

Open your browser and check if everything works at [http://umbriaopenapi.it:8888](http://umbriaopenapi.it:8888)

It's possible to debug on 9000 port with a Xdubug client.

## Built With

* PHP language with [Symfony framework version 2.8.11](https://symfony.com/).

* [EasyRDF](http://www.easyrdf.org/) is the library for RDF Entity to PHP Object Mapping.

* [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle) for RESTful API documentation.

* [Google Charts](https://developers.google.com/chart/) for statistical data charts.

* [Telegram bundle](https://packagist.org/packages/shaygan/telegram-bot-api-bundle) to implement Telegram BOT server.

## Authors

* [Umbriadigitale](http://www.umbriadigitale.it/)
    * Contact 1:  Azzurra Pantella - *Project manager* - [AzzurraP](https://github.com/AzzurraP)
    * Contact 2: Lorenzo Ranucci - *Project developer* - [lorenzoranucci](https://github.com/lorenzoranucci)

* [Dipartimento di Informatica, Sistemistica e Comunicazione - Università degli Studi Milano Bicocca](http://www.disco.unimib.it)



