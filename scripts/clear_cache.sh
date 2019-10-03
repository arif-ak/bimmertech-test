#!/usr/bin/env bash

rm -R var/cache  && rm -R var/logs
mkdir var/cache && chmod 777 -R var/cache
mkdir var/logs  && chmod 777 -R var/logs
