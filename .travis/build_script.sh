#!/usr/bin/env bash

echo "+ + + Run build_script.sh + + +"
export VERSION_TAG=$(git describe --abbrev=0 --tags)
docker build ${BUILD_ARGS} --build-arg db_password=${DB_PASSWORD} --build-arg version=${RUNTIME}-${VERSION_TAG} --build-arg commit=$(git rev-parse --short HEAD) --pull -t ${CONTAINER_IMAGE}-${RUNTIME}:${VERSION_TAG} ${CONTEXT_PATH}
docker push ${CONTAINER_IMAGE}-${RUNTIME}:${VERSION_TAG}
