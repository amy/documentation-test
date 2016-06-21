#!/bin/bash
#set -ev
set -euxo pipefail
IFS=$'\n\t'

function generateDocs {

  ./vendor/phpdocumentor/phpdocumentor/bin/phpdoc -d ../$1 -t ./docs --template="xml" --ignore="vendor/*"
  ./vendor/bin/phpdocmd ./docs/structure.xml docs/

}

function loadSSHKey {

  # Get the deploy key by using Travis's stored variables to decrypt deploy_key.enc
  ENCRYPTED_KEY_VAR="encrypted_${ENCRYPTION_LABEL}_key"
  ENCRYPTED_IV_VAR="encrypted_${ENCRYPTION_LABEL}_iv"
  ENCRYPTED_KEY=${!ENCRYPTED_KEY_VAR}
  ENCRYPTED_IV=${!ENCRYPTED_IV_VAR}
  openssl aes-256-cbc -K $ENCRYPTED_KEY -iv $ENCRYPTED_IV -in id_rsa_travisTest.enc -out id_rsa_travisTest -d
  chmod 600 id_rsa_travisTest
  eval `ssh-agent -s`
  ssh-add id_rsa_travisTest

}

if [ "${TRAVIS_PULL_REQUEST}" = "false" ]; then

  loadSSHKey

  TARGET_BRANCH="master"
  SOURCE_REPO="source"
  SINK_REPO="sink"

  REPO=`git config remote.origin.url`
  #SSH_REPO=${REPO/https:\/\/github.com\//git@github.com:}
  #SSH_REPO=git@github.com:amy/anotherTest.git
  SHA=`git rev-parse --verify HEAD`

  git clone $REPO $SOURCE_REPO
  cd $SOURCE_REPO



  generateDocs $SOURCE_REPO
  #git checkout $TARGET_BRANCH || git checkout --orphan $TARGET_BRANCH
  #cd ..

  #rm -rf out/**/* || exit 0

  #cd out
  git config user.name "Travis CI"
  git config user.email "$COMMIT_AUTHOR_EMAIL"

  # If there are no changes to the compiled out (e.g. this is a README update) then just bail.
  #if [ -z `git diff --exit-code` ]; then
   #   echo "No changes to the output on this push; exiting."
  #    exit 0
  #fi

  # Commit the "changes", i.e. the new version.
  # The delta will show diffs between new and old versions.
  #git --no-pager diff
  #git diff --exit-code
  git add docs
  git commit -m "Deploy to GitHub Pages: ${SHA}"

  cd ..

  mkdir SINK_REPO

  git clone git@github.com:amy/anotherTest.git $SINK_REPO

  git config user.name "Travis CI"
  git config user.email "$COMMIT_AUTHOR_EMAIL"

  cp -a $SOURCE_REPO/docs $SINK_REPO
  cd $SINK_REPO
  git add .
  git commit -m "updated docs"

  # Now that we're all set up, we can push.
  git push origin HEAD
  #git push -fq origin $TARGET_BRANCH


fi
