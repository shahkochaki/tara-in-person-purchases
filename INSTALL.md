# Tara In-Person Purchases - Installation Guide

## Installation Options

### Option 1: Install from GitHub (Recommended until Packagist is available)

Add this to your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/shahkochaki/tara-in-person-purchases.git"
        }
    ],
    "require": {
        "shahkochaki/tara-in-person-purchases": "dev-main"
    }
}
```

Then run:
```bash
composer install
```

### Option 2: Install from Packagist (Once available)

```bash
composer require shahkochaki/tara-in-person-purchases
```

## Setup Steps for Packagist

1. **Create GitHub Release**:
   ```bash
   git tag v1.0.0
   git push --tags
   ```

2. **Submit to Packagist**:
   - Go to [https://packagist.org](https://packagist.org)
   - Click "Submit"
   - Enter: `https://github.com/shahkochaki/tara-in-person-purchases`
   - Enable auto-update webhook

3. **Wait for sync** (can take 5-10 minutes)

## Current Status
- ✅ GitHub Repository: Available
- ⏳ Packagist: Pending registration
- 🔄 Composer Install: Use GitHub method above