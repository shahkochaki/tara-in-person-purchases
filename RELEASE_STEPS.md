# مراحل Release در GitHub

برای تکمیل فرآیند release، مراحل زیر را در GitHub دنبال کنید:

## ۱. وارد Repository شوید
- به https://github.com/shahkochaki/tara-in-person-purchases بروید

## ۲. ایجاد Release جدید
- روی تب "Releases" کلیک کنید
- روی "Create a new release" کلیک کنید

## ۳. تنظیمات Release
- **Tag version**: v2.0.0 (که قبلاً ایجاد شده)
- **Release title**: v2.0.0: Enhanced Configuration Management and Security
- **Description**: متن زیر را کپی کنید:

```markdown
## 🚀 Version 2.0.0 - Major Update

### ✨ New Features
- **Complete Configuration Management System**: Support for custom config arrays and runtime updates
- **Environment Variables Support**: Full .env file integration
- **Enhanced Security**: Removed all hardcoded sensitive information
- **Token Expiry Validation**: Automatic token refresh with configurable buffer
- **Terminal Session Management**: Improved terminal selection and management
- **Complete Purchase Flow**: Single method for entire purchase process

### 🔧 Improvements  
- **Better API Flow**: Following exact Tara API specifications
- **Enhanced Error Handling**: Comprehensive validation and error messages
- **Configuration Validation**: Automatic validation of required credentials
- **Runtime Configuration**: Update settings without restarting

### 📚 Documentation
- **API_FLOW_GUIDE.md**: Complete API workflow documentation
- **CONFIG_GUIDE.md**: Configuration management guide
- **SETUP_GUIDE.md**: Initial setup instructions
- **Updated Examples**: Security best practices included

### ⚠️ Breaking Changes
- **Constructor Changes**: Now requires credentials via config or environment variables
- **No Default Credentials**: Removed hardcoded values for security
- **Validation Required**: Must provide username, password, and branch code

### 🔒 Security Enhancements
- No sensitive information in source code
- Proper credential management through environment variables
- Configuration validation and error handling
- Security documentation and best practices

### 📦 Installation & Setup
1. Update to v2.0.0: `composer require shahkochaki/tara-in-person-purchases:^2.0`
2. Copy environment file: `cp vendor/shahkochaki/tara-in-person-purchases/.env.example .env`
3. Configure your credentials in `.env` file
4. Follow the SETUP_GUIDE.md for detailed instructions

### 🔄 Migration from v1.x
If upgrading from v1.x, please note the breaking changes:
- Set up environment variables for credentials
- Update your code to handle the new constructor requirements
- Review the new configuration system in CONFIG_GUIDE.md
```

## ۴. انتشار Release
- "Set as the latest release" را تیک بزنید
- روی "Publish release" کلیک کنید

## ۵. بررسی نهایی
پس از انتشار، موارد زیر را بررسی کنید:
- Tag در لیست tags موجود باشد
- Release در لیست releases نمایش داده شود
- Documentation به‌روزرسانی شده باشد
- Package در Packagist (در صورت استفاده) به‌روزرسانی شود