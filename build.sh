#!/bin/bash

composer --no-dev update && box build && cp ./build/stati.phar ./build/stati && cd ./build/ && zip stati.zip stati && cd ../ && composer update

