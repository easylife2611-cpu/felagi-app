# Release Process — Felagi APK

**Purpose:** How to produce a signed release APK, verify it, and
publish it to this site's `public/downloads/` directory.

## Overview

    ┌──────────────────────────────────────────────────────────┐
    │  1. Bump version  (pubspec.yaml)                         │
    │  2. Commit + push (branch)                               │
    │  3. Tag + push    (vX.Y.Z-L###)                          │
    │  4. GitHub Actions builds signed APK                     │
    │  5. Download artifact from GitHub                        │
    │  6. Copy APK + .sha256 to public/downloads/              │
    │  7. Update release notes + SOT                           │
    └──────────────────────────────────────────────────────────┘

## 1. Bump version

Edit `mobile/app/pubspec.yaml`:

    version: 1.4.3+1

The `versionCode` (after `+`) **must increase** on every Google Play
upload. The `versionName` (before `+`) is the human-readable version.

## 2. Commit + push

    git add mobile/app/pubspec.yaml
    git commit -m "L###: bump version to 1.4.3+1"
    git push origin feature/ai-guided-need-creation

This triggers a workflow run that **does not** produce a signed APK
(no tag). It runs analyze + tests only.

## 3. Tag + push

Choose a tag that matches the session:

    git tag v1.4.3-L355
    git push origin v1.4.3-L355

This triggers the full workflow including:
- Decode keystore from secrets
- Build signed release APK
- Upload artifact (90 days)
- Attach APK to GitHub Release

## 4. Download the APK

**Option A — GitHub UI:**

- Repo → Actions → latest "Build APK" run
- Scroll to Artifacts → click `app-release-<sha>`
- Extract the downloaded `.zip`

**Option B — GitHub CLI:**

    gh run list --workflow=build-apk.yml --limit=5
    gh run download <run-id> -n app-release-<sha>

**Option C — GitHub Release page:**

- Repo → Releases → v1.4.3-L355
- Download `app-release.apk` directly

## 5. Copy to public/downloads/

    cd ~/felagi_app
    TAG="v1.4.3-L355"
    mkdir -p public/downloads/apk
    cp ~/Downloads/app-release.apk \
       "public/downloads/apk/Felagi_${TAG}.apk"
    sha256sum "public/downloads/apk/Felagi_${TAG}.apk" \
       > "public/downloads/apk/Felagi_${TAG}.apk.sha256"

## 6. Verify

    cd public/downloads/apk
    sha256sum -c "Felagi_${TAG}.apk.sha256"

Expected output:

    Felagi_v1.4.3-L355.apk: OK

Public URLs:

    https://zagcreativity.com/downloads/apk/Felagi_v1.4.3-L355.apk
    https://zagcreativity.com/downloads/apk/Felagi_v1.4.3-L355.apk.sha256

## 7. Update SOT + ledgers

- Bump `SOURCE_OF_TRUTH.md` HEAD + APK URL
- Append to `CHANGE_LOG.md`
- Append to `IMPLEMENTATION_LEDGER.md`
- Rebuild the release bundle (Phase F6 pattern)

## Notes

- `public/downloads/` is gitignored — APKs are **not** committed.
- Only the URLs go into SOT / ledgers.
- Keep at least the **last 3 APKs** for rollback.
