# Socrates Blade

**Security Testing Framework for Scriptlog PHP Blogware**

![Socrates Blade Mascot](assets/socrates-blade-min.png)
![Version](https://img.shields.io/badge/version-3.2.0-blue)
[![CI](https://github.com/scriptlog/socrates-blade/actions/workflows/ci.yml/badge.svg)](https://github.com/scriptlog/socrates-blade/actions/workflows/ci.yml)
![License](https://img.shields.io/badge/license-MIT-green)

---

## What Is Socrates Blade?

Socrates Blade is a security scanner built for [Scriptlog](https://github.com/cakmoel/Scriptlog) - a PHP blog platform. It checks your web application for common security flaws and gives you a clear report of what it finds.

Think of it as a health check for your website's security. It looks for weaknesses that attackers might try to exploit, so you can fix them before they become a problem.

### What It Detects

| Issue | What It Means |
|-------|---------------|
| **SQL Injection** | Attackers could tamper with your database through input fields |
| **Cross-Site Scripting (XSS)** | Malicious scripts could be injected into your pages |
| **Path Traversal** | Unauthorized users might access files outside the web root |
| **Server-Side Request Forgery (SSRF)** | Your server could be tricked into making dangerous requests |

And many more - the scanner tests against hundreds of attack patterns.

---

## System Requirements

| Requirement | Minimum Version | Why |
|-------------|----------------|-----|
| **Python** | 3.8+ | Runs the scanner engine |
| **PHP** | 7.4+ | Extracts application routes from your Scriptlog installation |
| **curl** | Any recent version | Sends test requests to your site |
| **OS** | Linux, macOS, Windows (WSL) | Best results on Unix-like systems |

To check your versions:

```bash
python3 --version
php --version
```

---

## Installation

### 1. Download Socrates Blade

```bash
git clone https://github.com/scriptlog/socrates-blade.git
cd socrates-blade
```

### 2. Set Up a Virtual Environment

A virtual environment keeps Python dependencies organized and avoids conflicts with other projects.

```bash
python3 -m venv venv
source venv/bin/activate        # Linux / macOS
venv\Scripts\activate.bat       # Windows (CMD)
venv\Scripts\Activate.ps1       # Windows (PowerShell)
```

### 3. Install Dependencies

```bash
pip install -r scanrequirements.txt
```

### 4. Verify It Works

```bash
python3 socrates-blade.py --help
```

You should see a help message listing all available options.

---

## Quick Start

### Place Socrates Blade Inside Your Scriptlog Installation

For the smoothest experience, copy the tool into your Scriptlog directory:

```bash
cp -r socrates-blade /var/www/phpsite/public_html/
cd /var/www/phpsite/public_html/socrates-blade
```

When placed inside Scriptlog, the route extractor (`export_routes.php`) automatically finds your `config.php` and reads the correct application URL.

### Standalone Installation

If you prefer to keep the scanner separate:

```bash
cp -r /your/scriptlog/lib /your/scanner/directory/socrates-blade/
cp /your/scriptlog/config.php /your/scanner/directory/socrates-blade/
```

### Generate Application Routes

Routes tell the scanner what pages and endpoints to test.

```bash
php export_routes.php > routes.json
```

This produces a `routes.json` file containing:
- Current timestamp
- Application URL from `config.php`
- All available routes in your Scriptlog installation (frontend, admin, API, and more)

### Run Your First Scan

Make sure your web server is running, then:

```bash
./run-scan.sh http://localhost
```

The scanner will:
1. Confirm your URL is reachable
2. Check system requirements
3. Set up the Python environment
4. Run security tests against all discovered routes
5. Generate a results report

### Try a Dry Run First

A dry run shows what the scan would do without actually sending any requests:

```bash
./run-scan.sh http://localhost --dry-run
```

This is useful for learning how the scanner works before running a real test.

---

## Common Usage Examples

### Basic Scan (No Authentication)

```bash
./run-scan.sh http://localhost
```

### Scan with Authentication

Some areas of your site require login. Supply credentials to test those too:

```bash
./run-scan.sh http://localhost \
    -u admin \
    -p your_password \
    -o findings.json
```

### Generate an HTML Report

```bash
./run-scan.sh http://localhost \
    -u admin \
    -p your_password \
    --html-report report.html
```

### Aggressive Mode (Thorough Testing)

Aggressive mode runs deeper tests but takes longer:

```bash
./run-scan.sh http://localhost --aggressive --timeout 30
```

### Route Scans Through a Proxy (e.g., Burp Suite)

```bash
./run-scan.sh http://localhost --proxy http://127.0.0.1:8080
```

### Skip URL Validation

Use this if your target isn't directly reachable but you still want to scan:

```bash
./run-scan.sh http://localhost --no-validate
```

---

## Understanding Scan Results

### Severity Levels

| Level | What It Means | When to Fix |
|-------|---------------|-------------|
| **CRITICAL** | Immediate danger - data breach or full system compromise possible | Within 24 hours |
| **HIGH** | Serious vulnerability that can be exploited | Within 7 days |
| **MEDIUM** | Moderate risk - should be addressed | Within 30 days |
| **LOW** | Minor issue - fix when convenient | Within 90 days |

### Report Formats

| Format | Command | Best For |
|--------|---------|----------|
| **JSON** | `-o report.json` | Automation, CI/CD pipelines |
| **HTML** | `--html-report report.html` | Reading in a browser |

---

## Command-Line Options

### Authentication

| Flag | Description |
|------|-------------|
| `-u, --username <name>` | Your login username |
| `-p, --password <pass>` | Your login password |

### Scan Behavior

| Flag | Description |
|------|-------------|
| `--aggressive` | Run more thorough tests (slower) |
| `--brute-force` | Test password guessing |
| `--threads <n>` | Number of parallel tests (default: 5) |
| `--timeout <seconds>` | Max wait time for responses (default: 5s) |
| `--proxy <url>` | Route traffic through a proxy |
| `--wordlist <file>` | Custom password list for brute force |

### Reports

| Flag | Description |
|------|-------------|
| `-o, --output <file>` | Save JSON report to file |
| `--html-report <file>` | Save HTML report to file |
| `--report-dir <dir>` | Directory for report storage |

### Other

| Flag | Description |
|------|-------------|
| `--no-sync` | Skip route synchronization |
| `--no-validate` | Skip URL reachability check |
| `--dry-run` | Preview actions without executing |
| `-v, --verbose` | Show detailed progress |
| `-h, --help` | Display help message |

---

## Running Tests

```bash
# URL validator tests
./tests/bash/test_url_validator.sh

# BATS test suite
bats tests/bash/run-scan.sh.test.bats
```

---

## CI/CD Integration

Add security scanning to your GitHub Actions pipeline:

```yaml
name: Security Scan
on: [push, pull_request]

jobs:
  security-scan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Set up Python
        uses: actions/setup-python@v5
        with:
          python-version: '3.11'
      - name: Install dependencies
        run: pip install -r scanrequirements.txt
      - name: Run security scan
        run: |
          ./run-scan.sh ${{ secrets.TARGET_URL }} \
            -u ${{ secrets.TARGET_USER }} \
            -p ${{ secrets.TARGET_PASS }} \
            -o scan-results.json \
            --html-report scan-report.html
```

---

## Troubleshooting

### "Python not found"

Install Python 3, then check:

```bash
python3 --version
```

### "curl not found"

Install curl using your system's package manager (e.g., `apt install curl`, `brew install curl`).

### "Permission denied"

Make the script executable:

```bash
chmod +x run-scan.sh
```

### Scan Fails to Connect

- Make sure your web server is running
- Try `--no-validate` if the URL isn't reachable
- Check firewall settings

---

## Important Notes

### Only Test Systems You Own - or Have Written Permission to Test

Running security tools against websites without authorization is illegal. Always:

- Get written permission from the system owner before testing
- Use the tool on your own development environment
- Follow responsible disclosure practices if you find real vulnerabilities

### Respect Rate Limits

Don't overwhelm the target server. Adjust `--timeout` and `--threads` to keep your testing reasonable.

---

## Project Structure

```
socrates-blade/
├── socrates-blade.py       # Main scanner engine
├── run-scan.sh             # Automation wrapper (start here)
├── config.py               # Scanner configuration
├── routes.json             # Application routes extracted from Scriptlog
├── export_routes.php       # Route extractor for Scriptlog installations
├── scanrequirements.txt    # Python package dependencies
├── url-validator-lib.sh    # URL validation library sourced by run-scan.sh
├── payloads/               # Attack test payloads
│   ├── xss.txt            # 116 XSS test strings
│   ├── sqli.txt           # 150 SQL injection test strings
│   ├── traversal.txt      # 139 path traversal test strings
│   └── ssrf.txt           # 191 SSRF test strings
├── wordlists/              # Brute force wordlists
│   ├── passwords.txt      # 498 common passwords
│   └── usernames.txt      # 1916 common usernames
├── tests/
│   ├── bash/              # Shell script tests
│   └── python/unit/       # Python unit tests
├── reports/                # Generated scan reports
├── lib/                    # PHP library for route extraction
├── venv/                   # Python virtual environment
├── CODE_OF_CONDUCT.md     # Community guidelines
├── CONTRIBUTING.md        # Contribution guide
├── LICENSE.md             # MIT License
├── SECURITY.md            # Security policy
└── README.md              # This file
```

### Route Coverage

The route extractor (`export_routes.php`) covers all Scriptlog endpoints:

| Category | Routes | What It Tests |
|----------|--------|---------------|
| Frontend | 12 | Home, posts, categories, tags, archives, search, pages, privacy, downloads |
| Admin | 75+ | All admin pages (auth, content, users, media, settings, import/export, etc.) |
| API | 45 | Full REST API (posts, categories, comments, archives, search, GDPR, media) |
| Public | 3 | Comment submission, contact forms, subscriptions |
| Sensitive | 6 | Install wizard, configuration files |

**Total: 142 routes** (up from ~62 in v1.0)

---

## Getting Help

- [Issue Tracker](https://github.com/scriptlog/socrates-blade/issues) - Report bugs or request features
- Browse the source: `socrates-blade.py` and `config.py`
- Review attack payloads in the `payloads/` directory to understand what each test does

---

## License

[MIT License](LICENSE.md)

**Version**: 3.2.0  
**Last Updated**: April 2026  
**Maintained by**: M.Noermoehammad
