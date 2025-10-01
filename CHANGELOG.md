# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.0] - 2025-10-01

### Added

- **Enhanced Error Handling**: Standardized error format across all API methods
- New `parseErrorResponse()` method for consistent error parsing
- `TaraErrorHandlingExample.php` with comprehensive error handling demonstrations
- User-friendly error messages in Persian with error code mapping
- Error analysis and categorization system with suggestions
- Support for API error format: `{"data":{"code":84780028,"message":"موجودی کافی نیست."},"success":false}`

### Changed

- **BREAKING**: All API methods now return standardized error format: `'error' => ['title' => '', 'code' => 0000, 'message' => '']`
- Enhanced error responses with structured format including title, code, and message
- Improved documentation with error handling examples and common error codes table
- All API endpoints now use consistent error parsing and formatting

### Fixed

- API error responses now properly extract error codes and messages
- Standardized error handling across login, getAccessCode, getMerchandiseGroups, purchaseTrace, purchaseRequest, purchaseVerify, purchaseReverse, and purchaseInquiry methods
- Enhanced error debugging with full response data preservation

### Documentation

- Updated README.md with comprehensive error handling section
- Added error codes reference table with Persian descriptions
- Enhanced examples showing proper error handling patterns
- Added troubleshooting guide for common error scenarios

## [2.1.0] - 2025-09-30

### Added

- Terminal token authentication for all purchase operations
- TaraExampleFixed.php with complete purchaseData solution
- Troubleshooting section in README for common issues
- Enhanced documentation with direct GitHub raw file links
- Quick setup commands with curl for configuration files
- Comprehensive error handling examples

### Changed

- **BREAKING**: Purchase operations now use terminal tokens instead of user tokens
- purchaseRequest() now accepts optional terminalCode parameter
- purchaseVerify() now accepts optional terminalCode parameter
- purchaseReverse() now accepts optional terminalCode parameter
- purchaseInquiry() now accepts optional terminalCode parameter
- completePurchaseFlow() updated to pass terminalCode to all operations
- Improved API compliance with Tara360 specifications

### Fixed

- Fixed undefined purchaseData issue in examples
- Resolved security concerns with proper token usage
- Added missing purchase data creation steps in documentation
- Enhanced terminal access code validation

### Security

- Implemented terminal-specific authentication tokens
- Improved security isolation between terminals
- Enhanced API compliance for secure transactions

### Documentation

- Updated API reference with new terminal token requirements
- Added comprehensive troubleshooting guide
- Enhanced README with direct configuration copy links
- Improved examples with proper error handling
- Added security notes about terminal token usage

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
