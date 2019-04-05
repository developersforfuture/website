#!/usr/bin/env bash

echo "+ + + Run test_before_script.sh + + +"
docker login -u${REGISTRY_USER} -p${REGISTRY_PASSWORD} ${REGISTRY}
