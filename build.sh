#!/bin/bash

box build && cd paginate/ && box build && cd ../ && cd profiler/ && box build && cd ../ && cd related/ && box build && cd ../ && cp ./paginate/build/paginate.phar ./build/ && cp ./related/build/related.phar ./build/ && cp profiler/build/profiler.phar build/ && cd ./build/ && zip stati.zip *.phar && cd ../