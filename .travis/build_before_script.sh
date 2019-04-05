#!/usr/bin/env bash

echo "+ + + Run build_before_script.sh + + +"
docker login -u${REGISTRY_USER} -p${REGISTRY_PASSWORD} ${REGISTRY}
