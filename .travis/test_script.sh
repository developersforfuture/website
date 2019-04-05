#!/usr/bin/env bash

echo "+ + + Run test_script.sh + + +"
export TAG=$(git describe --abbrev=0 --tags)
if [ ${CI_COMMIT_REF_SLUG} == 'master' ]; then
    export VERSION_TAG=${TAG};
else
    export VERSION_TAG=${CI_COMMIT_REF_SLUG};
fi;

docker run -it -d ${CONTAINER_IMAGE}-${RUNTIME}:${VERSION_TAG}
make unit_test
