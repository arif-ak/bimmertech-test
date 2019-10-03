#!/usr/bin/env bash

#clear cache and logs
docker-compose exec web bash scripts/clear_cache.sh
echo " --------------------------------------- cache cleared ------------------------------------------"

# stop docker
cd .docker && docker-compose down && cd ../

echo " --------------------------------------- stop docker  -------------------------------------------"

bash scripts/backup.sh
echo " -------------------------------------------- backup  -------------------------------------------"
pwd

eval `ssh-agent -s`
ssh-add ../.ssh/id_rsa

# check and update from git
git checkout -- . && git clean -fd &&  git status  &&  git pull
echo " --------------------------------------- git pull  ----------------------------------------------"

# run docker
cd .docker && docker-compose up -d
echo " ---------------------------------------- start docker  -----------------------------------------"

#clear cache and logs
docker-compose exec web bash scripts/clear_cache.sh
echo " --------------------------------------- cache cleared ------------------------------------------"

# update database
docker-compose exec web php bin/console doctrine:schema:update --force
echo " --------------------------------------- database updated ---------------------------------------"

#update composer
docker-compose exec web bash scripts/update_composer.sh
echo " --------------------------------------- run composer install -----------------------------------"

sleep 10s

#update elastic
docker-compose exec web php bin/console fos:elastica:populate
echo " --------------------------------------- database elastic ---------------------------------------"

echo " ------------------------------------------- finish ---------------------------------------------"

