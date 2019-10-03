#!/usr/bin/env bash

mkdir ../../databases

cp parameters.yml ../app/config && chmod 777 ../app/config/parameters.yml

mkdir ../var/cache && chmod 777 -R ../var/cache
mkdir ../var/logs && chmod 777 -R ../var/logs

chmod 777 -R ../vendor && chmod 777 -R ../web/media && chmod 777 -R ../web/bundles

docker-compose exec web php bin/console doctrine:database:create
docker-compose exec web php bin/console doctrine:schema:update --force

docker-compose exec web   php bin/console  sylius:fixtures:load my
docker-compose exec web   php bin/console  sylius:fixtures:load carModel

docker-compose exec web php bin/console ckeditor:install
docker-compose exec web php bin/console assets:install