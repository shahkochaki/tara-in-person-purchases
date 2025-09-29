# Packagist Auto-Update Setup Guide

## Quick Setup (Recommended Method)

### Method 1: Using Packagist's Built-in Auto-Update

1. **Go to your package page on Packagist**:
   ```
   https://packagist.org/packages/shahkochaki/tara-in-person-purchases
   ```

2. **Look for the yellow warning message** that says:
   ```
   This package is not auto-updated. Please set up the GitHub Hook for Packagist so that it gets updated whenever you push!
   ```

3. **Click the "GitHub Hook" link** in that message

4. **Follow the automatic setup** - Packagist will guide you through the process

### Method 2: Manual GitHub Webhook Setup

If the automatic method doesn't work, follow these steps:

#### Step 1: Get Your Packagist Credentials
- Go to [Packagist.org](https://packagist.org)
- Login and go to **Profile** → **Settings**
- Note down your **Username** and copy your **API Token**

#### Step 2: Add GitHub Webhook
- Go to: `https://github.com/shahkochaki/tara-in-person-purchases/settings/hooks`
- Click **"Add webhook"**
- Fill in these details:
  - **Payload URL**: `https://packagist.org/api/github?username=YOUR_PACKAGIST_USERNAME`
  - **Content type**: `application/json`
  - **Secret**: Your Packagist API Token
  - **Trigger**: Select "Just the push event"
  - **Active**: ✅ Checked
- Click **"Add webhook"**

#### Step 3: Test the Webhook
- Make a small change to your repository (like updating README)
- Commit and push the changes
- Check if Packagist updates automatically

## Verification

After setup, you can verify it's working by:
1. Making a small commit to your repository
2. Pushing to GitHub
3. Checking your Packagist page - it should update within a few minutes
4. The yellow warning message should disappear

## Troubleshooting

If webhook doesn't work:
- Check webhook delivery in GitHub Settings → Webhooks
- Verify API token is correct
- Ensure username in payload URL matches your Packagist username
- Try re-creating the webhook