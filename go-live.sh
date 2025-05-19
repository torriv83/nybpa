#!/bin/bash

# Save current branch
CURRENT_BRANCH=$(git symbolic-ref --short -q HEAD)

if [ "$CURRENT_BRANCH" != "devtest" ]; then
  echo "You must be on 'devtest' branch to deploy."
  exit 1
fi

# Run tests
echo "✅ Running tests..."
php artisan test --parallel || { echo "❌ Tests failed. Aborting."; exit 1; }

# Merge devtest into master with same commit message
echo "🔀 Merging devtest into master..."
git checkout master
COMMIT_MSG=$(git log devtest -1 --pretty=%B)
git merge devtest --no-ff -m "$COMMIT_MSG"

# Push to origin
echo "☁️ Pushing master to origin..."
git push origin master

# Return to devtest
git checkout devtest
echo "🚀 Deploy triggered. You're back on devtest."
