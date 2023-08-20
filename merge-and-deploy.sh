#!/bin/bash

# Save current branch
CURRENT_BRANCH=$(git symbolic-ref --short -q HEAD)

# Check if on Staging branch, if so exit
if [ "$CURRENT_BRANCH" = "Staging" ]; then
  echo "You are already on the Staging branch. Merge manually if needed."
  exit 1
fi

# Step 1: Run tests with PestPHP
echo "Running tests..."
sh vendor/bin/sail pest

# # Step 2: Merge current branch into Staging
echo "Merging $CURRENT_BRANCH into Staging..."
git checkout Staging
git merge --no-edit $CURRENT_BRANCH

# Step 3: Run npm build
echo "Running npm build..."
sh vendor/bin/sail npm run build

if [ $? -ne 0 ]; then
  echo "Build failed. Aborting."
  exit 1
fi

# Step 4: Run tests with PestPHP
echo "Running tests..."
sh vendor/bin/sail pest

if [ $? -ne 0 ]; then
  echo "Tests failed. Aborting."
  exit 1
fi

# Step 5: Commit changes
echo "Committing changes to Staging..."
git add -A
git commit -m "Build and tests passed."

# # Step 6: Merge Staging into Master
echo "Merging Staging into Master..."
git checkout master
git merge --no-edit Staging

# Push Master to GitHub
#echo "Pushing changes to GitHub..."
#git push origin master

# Checkout back to the original branch
echo "Switching back to $CURRENT_BRANCH..."
git checkout $CURRENT_BRANCH

echo "Process completed successfully."
echo "Remember to push to github."

##!/bin/bash
#
## Check current branch
#BRANCH_NAME=$(git symbolic-ref --short -q HEAD)
#
#if [ "$BRANCH_NAME" != "Staging" ]; then
#  echo "You must be on the Staging branch to run this script."
#  exit 1
#fi
#
## Step 1: Run npm build
#echo "Running npm build..."
#sh vendor/bin/sail npm run build
#
#if [ $? -ne 0 ]; then
#  echo "Build failed. Aborting."
#  exit 1
#fi
#
## Step 2: Run tests with PestPHP
#echo "Running tests..."
#sh vendor/bin/sail pest
#
#if [ $? -ne 0 ]; then
#  echo "Tests failed. Aborting."
#  exit 1
#fi
#
## Step 3: Commit changes
#echo "Committing changes to Staging..."
#git add -A
#git commit -m "Build and tests passed."
#
## Merge Staging into Master
#echo "Merging Staging into Master..."
#git checkout master
#git merge --no-edit Staging
#
## Push Master to GitHub
#echo "Pushing changes to GitHub..."
#git push origin master
#
#echo "Process completed successfully."
#echo "Remember to push to github."
