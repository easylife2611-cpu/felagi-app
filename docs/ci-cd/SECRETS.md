# GitHub Actions Secrets — Felagi APK Signing

**Purpose:** Configure 4 secrets so GitHub Actions can build a signed
release APK for Felagi.

**When:** One-time setup, before your first tag push.

**Where:** GitHub repo → Settings → Secrets and variables → Actions → New repository secret.

---

## Overview

| Secret name | Content | Format |
|---|---|---|
| `KEYSTORE_BASE64` | `upload-keystore.jks` encoded to base64 | Single-line string |
| `KEYSTORE_PASSWORD` | The keystore password you chose | Plain text |
| `KEY_ALIAS` | The alias you chose (e.g. `upload`) | Plain text |
| `KEY_PASSWORD` | The key password you chose | Plain text |

**➡️ Prerequisites:** Run `bash scripts/ci/generate-keystore.sh` locally first.

---

## Step-by-step

### 1. Generate the keystore locally

On a trusted machine with JDK 17:

    git clone <your-repo-url>
    cd <repo>
    bash scripts/ci/generate-keystore.sh

You will be prompted for:

- keystore password
- key alias (default: `upload`)
- key password (Enter = same as keystore)
- your identity (name, org, city, country)

Output files (in current directory):

- `upload-keystore.jks`
- `keystore-base64.txt`
- `keystore-credentials.txt`

### 2. Add the 4 secrets on GitHub

GitHub repo → Settings → Secrets and variables → Actions →
**New repository secret**.

For `KEYSTORE_BASE64`, paste the entire content of
`keystore-base64.txt` (a single long line).

| Name | Value source |
|---|---|
| `KEYSTORE_BASE64` | contents of `keystore-base64.txt` |
| `KEYSTORE_PASSWORD` | `KEYSTORE_PASSWORD=` line in `keystore-credentials.txt` |
| `KEY_ALIAS` | `KEY_ALIAS=` line in `keystore-credentials.txt` |
| `KEY_PASSWORD` | `KEY_PASSWORD=` line in `keystore-credentials.txt` |

### 3. Verify

Push a tag:

    git tag v0.1.0-l354
    git push origin v0.1.0-l354

GitHub Actions will:

1. Run analyze + tests
2. Decode the keystore
3. Build a signed release APK
4. Upload it as an artifact (90 days)
5. Attach it to the GitHub Release

---

## Rotation

If a secret is compromised or lost, rotate by re-running the
generator with a fresh keystore and updating all 4 GitHub secrets.

**Warning:** If you publish to Google Play, changing the keystore
after your first release is not permitted without Play App Signing
enrolment. Enable Play App Signing before your first upload.

## Troubleshooting

### Workflow fails at "Decode keystore"

- Ensure `KEYSTORE_BASE64` is a **single line** (no wrapping).
- Regenerate with `base64 -w0` (the script already does this).

### Workflow fails at "Build APK (release)"

- Verify all 4 secrets are set (Settings → Secrets → Actions).
- Check the workflow log for the exact `keytool` error.

### APK is unsigned

- The `if` conditions require `github.ref_type == 'tag'`.
- Push a tag like `v0.1.0-l354` (not just a branch commit).

### "Tag already exists"

    git tag -d v0.1.0-l354
    git push origin :refs/tags/v0.1.0-l354
    git tag v0.1.0-l354
    git push origin v0.1.0-l354
