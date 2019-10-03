#!/usr/bin/env bash

DATE=`date '+%Y-%m-%d_%H:%M:%S'`;

mkdir -p ../backup/backup-$DATE &&  chmod 777 -R ../backup/backup-$DATE


cp -r ../bt-webshop ../backup/backup-$DATE
cp -r ../databases ../backup/backup-$DATE

echo " --------------------------------------- backup done ---------------------------------------"

