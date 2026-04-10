#!/usr/bin/env bats

SCRIPT_DIR="/var/www/html/socrates-blade"
SCRIPT="$SCRIPT_DIR/run-scan.sh"
PYTHON_SCRIPT="$SCRIPT_DIR/socrates-blade.py"
EXPORT_PHP="$SCRIPT_DIR/export_routes.php"
ROUTES_JSON="$SCRIPT_DIR/routes.json"

setup() {
    TEST_DIR="$(mktemp -d)"
    export LOG_FILE="$TEST_DIR/test.log"
    export REPORT_DIR="$TEST_DIR/reports"
}

teardown() {
    rm -rf "$TEST_DIR"
}

@test "show help with -h flag" {
    run "$SCRIPT" -h
    [ "$status" -eq 0 ]
}

@test "show help with --help flag" {
    run "$SCRIPT" --help
    [ "$status" -eq 0 ]
}

@test "missing target URL shows error" {
    run "$SCRIPT"
    [ "$status" -eq 1 ]
}

@test "invalid URL format" {
    run "$SCRIPT" "ftp://example.com"
    [ "$status" -eq 1 ]
}

@test "valid http URL with dry-run" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost'
    [ "$status" -eq 0 ]
}

@test "valid https URL with dry-run" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate https://localhost'
    [ "$status" -eq 0 ]
}

@test "unknown option shows error" {
    run "$SCRIPT" --unknown-option "http://localhost"
    [ "$status" -eq 1 ]
}

@test "parse -u flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -u testuser'
    [ "$status" -eq 0 ]
}

@test "parse -p flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -p secretpass'
    [ "$status" -eq 0 ]
}

@test "parse --threads flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --threads 10'
    [ "$status" -eq 0 ]
}

@test "parse --timeout flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --timeout 30'
    [ "$status" -eq 0 ]
}

@test "parse --aggressive flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --aggressive'
    [ "$status" -eq 0 ]
}

@test "parse --brute-force flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --brute-force'
    [ "$status" -eq 0 ]
}

@test "parse --proxy flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --proxy http://127.0.0.1:8080'
    [ "$status" -eq 0 ]
}

@test "parse --wordlist flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --wordlist /tmp/wordlist.txt'
    [ "$status" -eq 0 ]
}

@test "parse --max-attempts flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --max-attempts 5'
    [ "$status" -eq 0 ]
}

@test "parse --csrf-field flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --csrf-field token_field'
    [ "$status" -eq 0 ]
}

@test "parse --verify-ssl flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --verify-ssl'
    [ "$status" -eq 0 ]
}

@test "parse -o flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -o report.json'
    [ "$status" -eq 0 ]
}

@test "parse --html-report flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --html-report report.html'
    [ "$status" -eq 0 ]
}

@test "parse --report-dir flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --report-dir /tmp/reports'
    [ "$status" -eq 0 ]
}

@test "parse --no-sync flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --no-sync'
    [ "$status" -eq 0 ]
}

@test "parse -v flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -v'
    [ "$status" -eq 0 ]
}

@test "parse --verbose flag" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --verbose'
    [ "$status" -eq 0 ]
}

@test "parse multiple flags" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -u admin -p password --aggressive --threads 10 --timeout 30'
    [ "$status" -eq 0 ]
}

@test "duplicate target URL shows error" {
    run "$SCRIPT" "http://localhost" "http://example.com"
    [ "$status" -eq 1 ]
}

@test "dry-run shows commands" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost'
    [ "$status" -eq 0 ]
    [[ "$output" == *"Dry Run Mode"* ]]
}

@test "dry-run shows python3" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost'
    [ "$status" -eq 0 ]
    [[ "$output" == *"python3"* ]]
}

@test "banner is displayed" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost'
    [ "$status" -eq 0 ]
    [[ "$output" == *"Scriptlog"* ]]
}

@test "--no-sync shows disabled message" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost --no-sync'
    [ "$status" -eq 0 ]
    [[ "$output" == *"disabled"* ]]
}

@test "handles URL with port" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost:8080'
    [ "$status" -eq 0 ]
}

@test "handles URL with path" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost/blog'
    [ "$status" -eq 0 ]
}

@test "handles URL with query string" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate "http://localhost?test=1"'
    [ "$status" -eq 0 ]
}

@test "handles password with special chars" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -p "P@ssw0rd"'
    [ "$status" -eq 0 ]
}

@test "handles username with special chars" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -u "user@example.com"'
    [ "$status" -eq 0 ]
}

@test "returns 0 on successful dry-run" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost'
    [ "$status" -eq 0 ]
}

@test "returns 1 on missing target URL" {
    run "$SCRIPT"
    [ "$status" -eq 1 ]
}

@test "returns 1 on invalid URL" {
    run "$SCRIPT" "invalid-url"
    [ "$status" -eq 1 ]
}

@test "returns 1 on unknown option" {
    run "$SCRIPT" --unknown "http://localhost"
    [ "$status" -eq 1 ]
}

@test "routes.json exists" {
    run test -f "$ROUTES_JSON"
    [ "$status" -eq 0 ]
}

@test "routes.json is valid JSON" {
    run python3 -c "import json; json.load(open('$ROUTES_JSON'))"
    [ "$status" -eq 0 ]
}

@test "routes.json has metadata and routes" {
    run python3 -c "import json; d=json.load(open('$ROUTES_JSON')); assert 'metadata' in d and 'routes' in d"
    [ "$status" -eq 0 ]
}

@test "routes.json has frontend routes" {
    run python3 -c "import json; d=json.load(open('$ROUTES_JSON')); assert 'frontend' in d['routes']"
    [ "$status" -eq 0 ]
}

@test "routes.json has admin routes" {
    run python3 -c "import json; d=json.load(open('$ROUTES_JSON')); assert 'admin' in d['routes']"
    [ "$status" -eq 0 ]
}

@test "routes.json has api routes" {
    run python3 -c "import json; d=json.load(open('$ROUTES_JSON')); assert 'api' in d['routes']"
    [ "$status" -eq 0 ]
}

@test "routes.json has public routes" {
    run python3 -c "import json; d=json.load(open('$ROUTES_JSON')); assert 'public' in d['routes']"
    [ "$status" -eq 0 ]
}

@test "routes.json has sensitive routes" {
    run python3 -c "import json; d=json.load(open('$ROUTES_JSON')); assert 'sensitive' in d['routes']"
    [ "$status" -eq 0 ]
}

@test "socrates-blade.py is accessible" {
    run test -f "$PYTHON_SCRIPT"
    [ "$status" -eq 0 ]
}

@test "socrates-blade.py shows help" {
    run python3 "$PYTHON_SCRIPT" --help
    [ "$status" -eq 0 ]
}

@test "socrates-blade.py accepts target URL" {
    run python3 "$PYTHON_SCRIPT" http://localhost --help
    [ "$status" -eq 0 ]
}

@test "all scanning flags work together" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost -u admin -p password --aggressive --brute-force --threads 10'
    [ "$status" -eq 0 ]
}

@test "build_python_args includes routes-file" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost'
    [ "$status" -eq 0 ]
    [[ "$output" == *"--routes-file"* ]]
}

@test "build_python_args includes routes.json" {
    run /bin/bash -c 'echo "" | /var/www/html/socrates-blade/run-scan.sh --dry-run --no-validate http://localhost'
    [ "$status" -eq 0 ]
    [[ "$output" == *"routes.json"* ]]
}
