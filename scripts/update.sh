#!/usr/bin/env bash

rm -R var/cache  && rm -R var/logs
mkdir var/cache && chmod 777 -R var/cache
mkdir var/logs  && chmod 777 -R var/logs

cd .docker && docker-compose down && cd ../

pwd

eval `ssh-agent -s`
ssh-add ../.ssh/id_rsa

git status && git pull

cd .docker && docker-compose up

