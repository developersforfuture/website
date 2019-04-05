#!/usr/bin/env bash

echo "+ + + Run test_script.sh + + +"

export TAG=$(git describe --abbrev=0 --tags)
if [ ${CI_COMMIT_REF_SLUG} == 'master' ]; then
    export VERSION_TAG=${TAG};
else
    export VERSION_TAG=${CI_COMMIT_REF_SLUG};
fi;

make ci_pull
make ci_up
make unit_test
