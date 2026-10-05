#!/usr/bin/env bash
# Generate an Android upload keystore for Felagi.
#
# Run this ONCE on a trusted local machine (NOT on a server).
# The resulting .jks file + passwords go into GitHub Actions secrets.
#
# Requirements:
#   - JDK 17 installed locally (keytool on PATH)
#
# Usage:
#   bash scripts/ci/generate-keystore.sh
#
# Outputs:
#   - upload-keystore.jks       (DO NOT COMMIT)
#   - keystore-base64.txt       (for GitHub secret KEYSTORE_BASE64)
#   - keystore-credentials.txt  (passwords — store securely)

set -euo pipefail

KS_FILE="upload-keystore.jks"
KS_B64="keystore-base64.txt"
KS_CREDS="keystore-credentials.txt"

# ── Sanity checks ──
if ! command -v keytool >/dev/null 2>&1; then
  echo "❌ keytool not found. Install JDK 17 first."
  exit 1
fi

if [ -f "$KS_FILE" ]; then
  echo "⚠️  $KS_FILE already exists."
  echo "   Delete it manually if you want to regenerate."
  exit 1
fi

# ── Prompt for identity ──
read -rp "Keystore password (min 6 chars): " KS_PASS
read -rp "Key alias [upload]: " KEY_ALIAS
KEY_ALIAS="${KEY_ALIAS:-upload}"
read -rp "Key password (Enter = same as keystore): " KEY_PASS
KEY_PASS="${KEY_PASS:-$KS_PASS}"
read -rp "Full name (CN): " D_NAME
read -rp "Org unit (OU): " D_OU
read -rp "Org (O): " D_ORG
read -rp "City (L): " D_CITY
read -rp "State (ST): " D_STATE
read -rp "Country code (C) [ET]: " D_COUNTRY
D_COUNTRY="${D_COUNTRY:-ET}"

# ── Generate keystore ──
echo ""
echo "Generating keystore..."
keytool -genkeypair \
  -v \
  -keystore "$KS_FILE" \
  -alias "$KEY_ALIAS" \
  -keyalg RSA \
  -keysize 2048 \
  -validity 10000 \
  -storepass "$KS_PASS" \
  -keypass "$KEY_PASS" \
  -dname "CN=$D_NAME, OU=$D_OU, O=$D_ORG, L=$D_CITY, ST=$D_STATE, C=$D_COUNTRY"

if [ ! -f "$KS_FILE" ]; then
  echo "❌ Keystore generation failed."
  exit 1
fi

# ── Encode to base64 (single line) ──
base64 -w0 "$KS_FILE" > "$KS_B64"
echo "✅ $KS_B64 created"

# ── Write credentials file ──
cat > "$KS_CREDS" <<CREDS
KEYSTORE_FILE=$KS_FILE
KEYSTORE_PASSWORD=$KS_PASS
KEY_ALIAS=$KEY_ALIAS
KEY_PASSWORD=$KEY_PASS
CREDS
chmod 600 "$KS_CREDS"
echo "✅ $KS_CREDS created (permissions: 600)"

# ── Next steps ──
cat <<'NEXT'

════════════════════════════════════════════════════════════
  ✅ Keystore generated
════════════════════════════════════════════════════════════

📁 Generated files (DO NOT COMMIT):
   - upload-keystore.jks
   - keystore-base64.txt
   - keystore-credentials.txt

🔐 Add these 4 secrets to GitHub (Settings → Secrets → Actions):

   KEYSTORE_BASE64      ← copy value from keystore-base64.txt
   KEYSTORE_PASSWORD    ← KEYSTORE_PASSWORD from keystore-credentials.txt
   KEY_ALIAS            ← KEY_ALIAS from keystore-credentials.txt
   KEY_PASSWORD         ← KEY_PASSWORD from keystore-credentials.txt

🧹 Clean up local files when done (after adding to GitHub):
   rm upload-keystore.jks keystore-base64.txt keystore-credentials.txt

   ⚠️  Store a password-manager copy of keystore-credentials.txt first!
   ⚠️  If lost, you cannot update the app on Google Play.
   ⚠️  Keep upload-keystore.jks in a secure backup too.

📖 See docs/ci-cd/SECRETS.md for full details.

NEXT

# Verify .gitignore covers the generated files
if grep -q "upload-keystore.jks" .gitignore 2>/dev/null; then
  echo "✅ .gitignore already covers keystore"
else
  echo "⚠️  Add to .gitignore:"
  echo "   upload-keystore.jks"
  echo "   keystore-base64.txt"
  echo "   keystore-credentials.txt"
fi
