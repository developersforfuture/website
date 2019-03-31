#!/usr/bin/env bash

DIR=$1
PROD=$2||false
run () {
    comment="+++ "$1" +++"
    command=$2
    echo ${comment}
    echo "Command: "${command}
    ${command} > /tmp/output 2> /tmp/error
    OUT=$?
    if [ ${OUT} -eq 0 ];then
       echo "+++ DONE +++"
       cat /tmp/output
       echo
    else
       echo "+++ Errors +++"
       cat /tmp/output
       cat /tmp/error
       exit ${OUT}
    fi
}

rm var/cache/ -rf
run "Drop and init dbal:" "php ${DIR}bin/console${OPTION} doctrine:phpcr:init:dbal --drop --force -n -vvv"
run "Create workspace: " "php ${DIR}bin/console${OPTION} doctrine:phpcr:workspace:create developers"
run "Init repositories:" "php ${DIR}bin/console${OPTION} doctrine:phpcr:repository:init -n -vvv"
run "Load date fixtures:" "php ${DIR}bin/console${OPTION} doctrine:phpcr:fixtures:load -n -vvv"
run "Warm up cache:" "php ${DIR}bin/console${OPTION} cache:warmup -n --no-debug -vvv"
