#!/bin/bash

DIR=/home/gallery

cd /home
mkdir gallery
cd $DIR && npm run dev
# cd $DIR && rm -rf node_modules/ && rm -rf package-lock.json && npm install && npm run dev