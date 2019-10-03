#!/usr/bin/env bash

#ssh-copy-id sylius@192.241.149.202

eval `ssh-agent -s`
ssh-add ../../.ssh/id_rsa

ssh -t sylius@192.241.149.202 "cd  bt-webshop &&   bash scripts/backup.sh && sudo  bash scripts/update.sh"


