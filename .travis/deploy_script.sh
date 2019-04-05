#!/usr/bin/env bash

echo "+ + + Run deploy_script.sh + + +"
ssh gitlab@${SSH_HOST} "sudo su && kubectl apply -f https://gitlab.com/developeers/php-track-web/raw/master/kubernetes/app.${RUNTIME}.yaml"
