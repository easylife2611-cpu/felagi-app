# CI/CD — Felagi APK Build

**Purpose:** Automated, signed Android APK builds via GitHub Actions.

**Why:** The production web server (shared hosting) does not run
Android build tooling. All builds happen on GitHub-hosted runners.

## Quick start

    # One-time: generate keystore locally (JDK 17 required)
    bash scripts/ci/generate-keystore.sh

    # One-time: add 4 secrets on GitHub (Settings → Secrets → Actions)
    # See SECRETS.md for the exact names + values.

    # Every release:
    git tag vX.Y.Z-L###
    git push origin vX.Y.Z-L###

## Documents

| File | Purpose |
|---|---|
| `README.md` | This index |
| `SECRETS.md` | How to generate + add the 4 GitHub secrets |
| `SIGNING.md` | How release signing works (local + CI) |
| `RELEASE.md` | End-to-end release process (tag → APK → download) |

## Files in this setup

| Path | Purpose |
|---|---|
| `.github/workflows/build-apk.yml` | GitHub Actions workflow |
| `scripts/ci/generate-keystore.sh` | Local keystore generator |
| `docs/ci-cd/` | This documentation |

## Workflow triggers

| Event | Runs? | Signed? |
|---|---|---|
| PR to `main` | ✅ | ❌ (debug fallback) |
| Push to `main` | ✅ | ❌ (debug fallback) |
| Push to `feature/ai-guided-need-creation` | ✅ | ❌ (debug fallback) |
| Push tag `v*` | ✅ | ✅ (release keystore) |
| Manual `workflow_dispatch` | ✅ | ❌ (unless tag) |

## Gates

Every run includes:

- `flutter analyze` — must be 0 issues
- `flutter test` — all must pass (23 as of L354)

If either gate fails, the build stops before APK generation.

## Outputs

| Trigger | Artifact | Retention |
|---|---|---|
| Debug / branch | `app-debug-<sha>` | 30 days |
| Tag `v*` | `app-release-<sha>` | 90 days |
| Tag `v*` | GitHub Release with APK attached | Permanent |
