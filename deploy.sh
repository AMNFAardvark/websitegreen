#!/bin/bash
# FamilyVest Fixed Site - Deploy to Vercel
SITE_DIR="$(cd "$(dirname "$0")" && pwd)"
echo "Deploying FamilyVest from: $SITE_DIR"
echo ""
if ! command -v npx &> /dev/null; then
    echo "Node.js/npx not found. Install with: brew install node"
    exit 1
fi
cd "$SITE_DIR"
npx vercel --yes
echo ""
echo "Done! Preview URL shown above."
echo "To push to production: npx vercel --prod --yes"
