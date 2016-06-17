#!/bin/bash
#set -ev
set -euo pipefail
IFS=$'\n\t'

function generateDocs {

  ./vendor/phpdocumentor/phpdocumentor/bin/phpdoc -d ../documentation-test -t ./docs --template="xml" --ignore="vendor/*"
  ./vendor/bin/phpdocmd ./docs/structure.xml docs/

}

if [ "${TRAVIS_PULL_REQUEST}" = "false" ]; then

  generateDocs


fi