# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-09-30

### Added
- Complete configuration management system
- Support for custom configuration arrays
- Runtime configuration updates
- Environment variables support (.env)
- Configuration validation and error handling
- Multiple configuration examples and guides
- Terminal session management
- Token expiry validation with buffer
- Complete purchase flow method
- Enhanced security with no hardcoded credentials

### Changed
- **BREAKING**: Constructor now requires credentials via config or environment variables
- Improved API flow following exact Tara specifications
- Enhanced terminal management with proper access codes
- Better error handling and validation
- Updated examples with security best practices

### Security
- Removed all hardcoded sensitive information
- Implemented proper credential management
- Added configuration validation
- Enhanced security documentation

### Documentation
- Added comprehensive API_FLOW_GUIDE.md
- Added CONFIG_GUIDE.md for configuration management
- Added SETUP_GUIDE.md for initial setup
- Updated examples with security considerations
- Enhanced inline documentation

## [1.0.0] - 2025-01-29

### Added

- Initial release of Tara In-Person Purchases for Laravel
- Complete Tara360 API integration
- Support for in-person credit card purchases
- Authentication and token management
- Terminal selection and management
- Purchase flow (trace, request, verify, reverse, inquiry)
- Helper methods for payment data creation
- Constants for product units and origins
- Custom exception handling
- Service provider for Laravel integration
- Configuration file with environment variables
- Comprehensive documentation
- Unit tests
- GitHub Actions CI/CD pipeline
- PSR-4 autoloading
- MIT license

### Features

- **Authentication**: Login with username/password, automatic token management
- **Terminal Management**: Get available terminals, select terminal by index
- **Purchase Flow**: Complete purchase workflow with trace, request, verify operations
- **Product Management**: Support for product units, origins, and merchandise groups
- **Error Handling**: Standardized error responses and custom exceptions
- **Laravel Integration**: Service provider, config publishing, dependency injection
- **Testing**: PHPUnit tests for core functionality
- **Documentation**: Comprehensive README with examples in English and Persian

### API Endpoints Supported

- `POST /merchant/login` - Authentication
- `POST /merchant/access/code/{branchCode}` - Get terminals
- `POST /purchase/merchandise/groups` - Get product categories
- `POST /purchase/trace/{terminalCode}` - Create payment trace
- `POST /purchase/request/{traceNumber}` - Submit purchase request
- `POST /purchase/verify/{traceNumber}` - Verify purchase
- `POST /purchase/reverse/{traceNumber}` - Reverse/cancel purchase
- `POST /purchase/inquiry/{id}` - Inquiry purchase status
