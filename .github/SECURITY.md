# Security Policy

## Supported Versions

We release patches for security vulnerabilities in the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

We take security vulnerabilities seriously. If you discover a security issue, please report it responsibly.

### How to Report

**Please do NOT report security vulnerabilities through public GitHub issues.**

Instead, please report them via one of the following methods:

1. **GitHub Security Advisories** (Preferred)
   - Go to the [Security tab](../../security/advisories) of this repository
   - Click "Report a vulnerability"
   - Fill out the form with details

2. **Email**
   - Send an email to: security@example.com (replace with actual email)
   - Use the subject line: `[SECURITY] Accelade Filters Vulnerability Report`

### What to Include

Please include the following information in your report:

- **Type of vulnerability** (e.g., XSS, SQL injection, CSRF, etc.)
- **Location** of the affected source code (file path, line numbers)
- **Steps to reproduce** the vulnerability
- **Proof of concept** or exploit code (if possible)
- **Impact** assessment of the vulnerability
- **Suggested fix** (if you have one)

### What to Expect

1. **Acknowledgment**: We will acknowledge receipt of your report within 48 hours.

2. **Investigation**: We will investigate the issue and determine its severity and impact.

3. **Updates**: We will keep you informed about our progress throughout the process.

4. **Resolution**: Once the issue is resolved, we will:
   - Release a security patch
   - Publish a security advisory
   - Credit you for the discovery (unless you prefer to remain anonymous)

### Timeline

We aim to:

- Acknowledge reports within **48 hours**
- Provide an initial assessment within **1 week**
- Release a patch within **30 days** for critical issues
- Release a patch within **90 days** for non-critical issues

### Safe Harbor

We consider security research conducted in accordance with this policy to be:

- Authorized and lawful
- Helpful to our security posture
- Exempt from legal action by us

We will not pursue legal action against researchers who:

- Act in good faith
- Avoid privacy violations and data destruction
- Do not exploit vulnerabilities beyond what's necessary for the report
- Report vulnerabilities promptly

## Security Best Practices

When using Accelade Filters in your application:

### XSS Prevention

- Always escape user input in Blade templates using `{{ }}` (not `{!! !!}`)
- Sanitize filter values before using in database queries
- Use parameterized queries (Laravel's query builder does this automatically)

### SQL Injection Prevention

- Always use Laravel's query builder or Eloquent when applying filters
- Never concatenate raw user input into SQL queries
- Validate filter column names against an allowlist

### Content Security Policy

Consider implementing a Content Security Policy header:

```php
// In a middleware
$response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'");
```

### Data Validation

- Validate all filter values before applying to queries
- Use proper type casting for numeric and date filters
- Sanitize search terms in text filters

## Vulnerability Disclosure

After a vulnerability has been fixed, we will:

1. Release a new version with the patch
2. Publish a security advisory on GitHub
3. Update this document if needed
4. Notify users through appropriate channels

## Contact

For security-related questions that are not vulnerabilities, please open a regular GitHub issue or discussion.

Thank you for helping keep Accelade Filters and its users safe!
