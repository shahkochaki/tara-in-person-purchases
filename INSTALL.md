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

1. **Submit to Packagist**:

   - Go to [https://packagist.org](https://packagist.org)
   - Click "Submit"
   - Enter: `https://github.com/shahkochaki/tara-in-person-purchases`

2. **Create GitHub Release**:

   ```bash
   git tag v1.0.0
   git push --tags
   ```

3. **Setup Auto-Update Webhook**:

   **Step 1**: Get Packagist API Token

   - Go to Packagist.org → Profile → Settings → API Token
   - Copy your API token

   **Step 2**: Add GitHub Webhook

   - Go to your GitHub repository: `https://github.com/shahkochaki/tara-in-person-purchases`
   - Click **Settings** → **Webhooks** → **Add webhook**
   - Set these values:
     - **Payload URL**: `https://packagist.org/api/github?username=YOUR_PACKAGIST_USERNAME`
     - **Content type**: `application/json`
     - **Secret**: Your Packagist API Token (from Step 1)
     - **Which events**: Select "Just the push event"
     - **Active**: ✅ Checked
   - Click **Add webhook**

4. **Alternative: Use Packagist Auto-Update Service**

   - In your Packagist package page, click "Enable Auto-Update"
   - Follow the instructions to set up GitHub Service Hook

5. **Wait for sync** (can take 5-10 minutes)

## Current Status

- ✅ GitHub Repository: Available
- ⏳ Packagist: Pending registration
- 🔄 Composer Install: Use GitHub method above
