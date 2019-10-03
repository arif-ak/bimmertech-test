#!/bin/bash/env bash

unalias $(alias | grep winpty | cut -d"=" -f1 | cut -d" " -f2)

yes|php ../bin/console sylius:fixtures:load my

echo END my fixture

yes|php ../bin/console sylius:fixtures:load taxons

echo END taxons fixture

yes|php ../bin/console sylius:fixtures:load products

echo END products fixture

yes|php ../bin/console sylius:fixtures:load orders

echo END orders fixture

yes|php ../bin/console sylius:fixtures:load carModel

echo END carModel fixture

yes|php ../bin/console sylius:fixtures:load buyersGuideOption

echo END buyersGuideOption fixture

yes|php ../bin/console sylius:fixtures:load contactPage

echo END contactPage fixture

yes|php ../bin/console sylius:fixtures:load helperPage

echo END helperPage fixture

yes|php ../bin/console sylius:fixtures:load aboutUsPage

echo END aboutUsPage fixture

echo END

sleep 3