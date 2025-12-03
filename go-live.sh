#!/bin/bash

# 👤 Konfigurer hvem som gjør merge
#git config user.name "CI Bot"
#git config user.email "ci@torriv.local"

# 👉 Sjekk at vi er på devtest
CURRENT_BRANCH=$(git symbolic-ref --short -q HEAD)

if [ "$CURRENT_BRANCH" != "devtest" ]; then
  echo "❌ Du må være på 'devtest'-branchen for å deploye."
  exit 1
fi

# ✅ Kjør tester
echo "✅ Kjører tester..."
./vendor/bin/pest --parallel || { echo "❌ Tester feilet. Avbryter."; exit 1; }

# 🔀 Merge devtest → master med samme commit-melding
echo "🔀 Merger devtest inn i master..."
git checkout master
COMMIT_MSG=$(git log devtest -1 --pretty=%B)
git merge devtest --no-ff -m "$COMMIT_MSG"

# ☁️ Push master
echo "☁️ Pusher master til origin..."
git push origin master

# 🔁 Gå tilbake til devtest
git checkout devtest
echo "🚀 Deploy fullført. Du er tilbake på devtest."
