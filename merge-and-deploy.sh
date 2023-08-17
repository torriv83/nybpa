#!/bin/bash

# Check current branch
BRANCH_NAME=$(git symbolic-ref --short -q HEAD)

if [ "$BRANCH_NAME" != "Staging" ]; then
  echo "You must be on the Staging branch to run this script."
  exit 1
fi

# Step 1: Run npm build
echo "Running npm build..."
sh vendor/bin/sail npm run build

if [ $? -ne 0 ]; then
  echo "Build failed. Aborting."
  exit 1
fi

# Step 2: Run tests with PestPHP
echo "Running tests..."
sh vendor/bin/sail pest

if [ $? -ne 0 ]; then
  echo "Tests failed. Aborting."
  exit 1
fi

# Step 3: Commit changes
echo "Committing changes to Staging..."
git add -A
git commit -m "Build and tests passed."

# Merge Staging into Master
echo "Merging Staging into Master..."
git checkout master
git merge --no-edit Staging

# Push Master to GitHub
echo "Pushing changes to GitHub..."
git push origin master

echo "Process completed successfully."
