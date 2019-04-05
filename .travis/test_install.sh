#!/usr/bin/env bash

echo "+ + + Run test_install.sh + + +"
sudo apt update
sudo apt install -y python3 m4 curl git make
pip3 install docker-compose
