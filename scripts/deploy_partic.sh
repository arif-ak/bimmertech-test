#!/usr/bin/env bash

#ssh-copy-id sylius@192.241.149.202

eval `ssh-agent -s`
ssh-add ../../.ssh/id_rsa

ssh -t shoptest@testshop.bimmer-tech.net -p522 "cd  www &&   bash scripts/backup.sh && sudo  bash scripts/update.sh"


