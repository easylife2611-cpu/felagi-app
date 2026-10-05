# APK Signing — Felagi

**Purpose:** How release APK signing works in CI, and why the
configuration is safe both locally and on GitHub Actions.

## Behavior matrix

| Context | key.properties present? | Signing key used |
|---|---|---|
| Local `flutter run --debug` | No | Debug (Android default) |
| Local `flutter run --release` | No | Debug (fallback) |
| Local release with key.properties | Yes | Release keystore |
| GitHub Actions on PR / branch | No | Debug (fallback) |
| GitHub Actions on tag `v*` | Yes (from secrets) | **Release keystore** |

## How it works

### 1. Gradle loads key.properties (if present)

File `mobile/app/android/app/build.gradle.kts`:

    val keystoreProperties = Properties()
    val keystorePropertiesFile = rootProject.file("key.properties")
    if (keystorePropertiesFile.exists()) {
        keystoreProperties.load(FileInputStream(keystorePropertiesFile))
    }

### 2. Release signing config

    signingConfigs {
        create("release") {
            if (keystorePropertiesFile.exists()) {
                keyAlias = keystoreProperties["keyAlias"] as String
                keyPassword = keystoreProperties["keyPassword"] as String
                storeFile = file(keystoreProperties["storeFile"] as String)
                storePassword = keystoreProperties["storePassword"] as String
            }
        }
    }

### 3. Conditional selection

    buildTypes {
        release {
            signingConfig = if (keystorePropertiesFile.exists())
                signingConfigs.getByName("release")
            else
                signingConfigs.getByName("debug")
        }
    }

## Why this is safe

**Locally (no key.properties):**
- Release APK falls back to debug signing — same as Flutter default.
- No secrets are read, no errors if the file is missing.

**On GitHub Actions (PR / branch):**
- Keystore secrets are only decoded on tag pushes.
- Even if secrets were present, `if` conditions gate them.

**On GitHub Actions (tag):**
- Keystore decoded from `KEYSTORE_BASE64` secret.
- `key.properties` written to `mobile/app/android/key.properties`.
- Release APK signed with your production key.

## Security notes

- **Never** commit `upload-keystore.jks` or `key.properties`.
- Both are covered by `android/.gitignore` and root `.gitignore`.
- The base64 file (`keystore-base64.txt`) and credentials file
  are also ignored.
- GitHub secrets are encrypted at rest and not visible after saving.
- Rotate by re-running the generator and updating all 4 secrets.

## Path reference

    mobile/app/android/key.properties          ← Gradle reads this
    mobile/app/android/app/upload-keystore.jks ← Gradle reads this

Both paths match what the workflow writes.

## Common issues

### "Keystore file not found" during build

- `KEYSTORE_BASE64` is empty or malformed.
- Regenerate with `base64 -w0` (the script does this).

### "key.properties not found"

- Workflow is running on a PR / branch (expected).
- Release signing only activates on tag pushes.

### Signed but wrong key

- Verify `KEY_ALIAS` matches the alias used when generating.
- Check `keystore-credentials.txt` for the exact values.

### APK install fails on device

- Ensure `applicationId = "com.felagi.felagi_app"` is unique.
- Uninstall the debug build first (different signature).
