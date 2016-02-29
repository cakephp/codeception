#!/usr/bin/env bash

# Return if we are not in a Pull Request
[[ "$TRAVIS_PULL_REQUEST" = "false" ]] && return

GITHUB_PR_URL=https://api.github.com/repos/$TRAVIS_REPO_SLUG/pulls/$TRAVIS_PULL_REQUEST
GITHUB_PR_BODY=$(curl -s $GITHUB_PR_URL 2>/dev/null)

if [[ $GITHUB_PR_BODY =~ \"ref\":\ *\"([a-zA-Z0-9_-]*)\" ]]; then
  export TRAVIS_PR_BRANCH=${BASH_REMATCH[1]}
else
  return
fi

GITHUB_BRANCH_URL=https://api.github.com/repos/$TRAVIS_REPO_SLUG/branches/$TRAVIS_PR_BRANCH
if [ $(curl -s --head  --request GET $GITHUB_BRANCH_URL | grep "200 OK" > /dev/null) ]; then
  TRAVIS_BRANCH=$TRAVIS_PR_BRANCH
  if [[ $GITHUB_PR_BODY =~ \"repo\":.*\"clone_url\":\ *\"https://github\.com/([a-zA-Z0-9_-]*/[a-zA-Z0-9_-]*)\.git.*\"base\" ]]; then
    export TRAVIS_REPO_SLUG=${BASH_REMATCH[1]}
  fi
fi
