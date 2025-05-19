#!/bin/bash

# 👤 Konfigurer hvem som lager taggen
git config user.name "CI Bot"
git config user.email "ci@torriv.local"

# 👉 Start på devtest
CURRENT_BRANCH=$(git symbolic-ref --short -q HEAD)

if [ "$CURRENT_BRANCH" != "devtest" ]; then
  echo "You must be on 'devtest' branch to deploy."
  exit 1
fi

# ✅ Kjør tester
echo "✅ Running tests..."
php artisan test --parallel || { echo "❌ Tests failed. Aborting."; exit 1; }

# 💬 Finn siste tag fra devtest
echo "🔍 Finding latest devtest tag..."
LATEST_TAG=$(git tag --list 'v*' --merged devtest | sort -V | tail -n1)

if [ -z "$LATEST_TAG" ]; then
  echo "❌ Ingen tidligere tag funnet på devtest. Aborting."
  exit 1
fi

# 🔀 Merge til master med samme commit-melding
echo "🔀 Merging devtest into master..."
git checkout master
COMMIT_MSG=$(git log devtest -1 --pretty=%B)
git merge devtest --no-ff -m "$COMMIT_MSG"

# 🏷️ Flytt taggen til master sin HEAD
echo "🏷️ Tagging master with $LATEST_TAG"
git tag -f "$LATEST_TAG"

# ☁️ Push både master og tag
echo "☁️ Pushing master + tag to origin..."
git push origin master
git push origin --force "$LATEST_TAG"

# ↩️ Tilbake til devtest
git checkout devtest
echo "🚀 Deploy complete. You're back on devtest."
