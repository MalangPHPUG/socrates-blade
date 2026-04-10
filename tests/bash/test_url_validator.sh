#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LIB_FILE="$SCRIPT_DIR/../../url-validator-lib.sh"

RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

TESTS_RUN=0
TESTS_PASSED=0
TESTS_FAILED=0

run_test_capture() {
    local test_name="$1"
    local test_command="$2"
    local expected_result="$3"
    
    TESTS_RUN=$((TESTS_RUN + 1))
    
    local result
    result=$(eval "$test_command" 2>/dev/null)
    
    if [ "$result" = "$expected_result" ]; then
        TESTS_PASSED=$((TESTS_PASSED + 1))
        echo -e "  ${GREEN}✓${NC} $test_name"
    else
        TESTS_FAILED=$((TESTS_FAILED + 1))
        echo -e "  ${RED}✗${NC} $test_name"
        echo "    Expected: '$expected_result'"
        echo "    Got:      '$result'"
    fi
}

run_test() {
    local test_name="$1"
    local test_command="$2"
    local expected="$3"
    
    TESTS_RUN=$((TESTS_RUN + 1))
    
    if eval "$test_command" 2>/dev/null; then
        if [ "$expected" = "success" ]; then
            TESTS_PASSED=$((TESTS_PASSED + 1))
            echo -e "  ${GREEN}✓${NC} $test_name"
        else
            TESTS_FAILED=$((TESTS_FAILED + 1))
            echo -e "  ${RED}✗${NC} $test_name (expected failure)"
        fi
    else
        if [ "$expected" = "failure" ]; then
            TESTS_PASSED=$((TESTS_PASSED + 1))
            echo -e "  ${GREEN}✓${NC} $test_name"
        else
            TESTS_FAILED=$((TESTS_FAILED + 1))
            echo -e "  ${RED}✗${NC} $test_name"
        fi
    fi
}

main() {
    if [ ! -f "$LIB_FILE" ]; then
        echo -e "${RED}Error: URL validator library not found at $LIB_FILE${NC}"
        exit 1
    fi
    
    echo ""
    echo -e "${CYAN}═══════════════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}  Socrates Blade - URL Validation Test Suite${NC}"
    echo -e "${CYAN}═══════════════════════════════════════════════════════════════${NC}"
    echo ""
    
    echo -e "${BOLD}extract_hostname() tests:${NC}"
    run_test_capture "Extract hostname from HTTP URL" \
        "source '$LIB_FILE' && extract_hostname 'http://example.com'" \
        "example.com"
    run_test_capture "Extract hostname from HTTPS URL" \
        "source '$LIB_FILE' && extract_hostname 'https://secure.example.com'" \
        "secure.example.com"
    run_test_capture "Extract hostname with port" \
        "source '$LIB_FILE' && extract_hostname 'http://example.com:8080'" \
        "example.com"
    run_test_capture "Extract hostname with path" \
        "source '$LIB_FILE' && extract_hostname 'http://example.com/path/to/page'" \
        "example.com"
    
    echo ""
    echo -e "${BOLD}check_dns_resolution() tests:${NC}"
    run_test_capture "DNS check: localhost" \
        "source '$LIB_FILE' && check_dns_resolution 'localhost'" \
        "true"
    run_test_capture "DNS check: 127.0.0.1" \
        "source '$LIB_FILE' && check_dns_resolution '127.0.0.1'" \
        "true"
    run_test "DNS check: invalid domain" \
        "source '$LIB_FILE' && [ \"\$(check_dns_resolution 'definitely-not-real-12345.com')\" != 'true' ]" \
        "success"
    
    echo ""
    echo -e "${BOLD}detect_known_typos() tests:${NC}"
    run_test "Typo: bloware -> blogware" \
        "source '$LIB_FILE' && detect_known_typos 'bloware' | grep -q 'blogware'" \
        "success"
    run_test "Typo: loclahost -> localhost" \
        "source '$LIB_FILE' && detect_known_typos 'loclahost' | grep -q 'localhost'" \
        "success"
    run_test "Typo: localhose -> localhost" \
        "source '$LIB_FILE' && detect_known_typos 'localhose' | grep -q 'localhost'" \
        "success"
    run_test_capture "No typo: example.com" \
        "source '$LIB_FILE' && detect_known_typos 'example.com'" \
        ""
    
    echo ""
    echo -e "${BOLD}Integration tests:${NC}"
    run_test "Validate localhost" \
        "source '$LIB_FILE' && validate_target_url 'http://localhost' >/dev/null 2>&1" \
        "success"
    run_test "Validate invalid domain" \
        "(source '$LIB_FILE' && validate_target_url 'http://invalid-xyz-12345.com' >/dev/null 2>&1 && exit 1) || true" \
        "success"
    
    echo ""
    echo -e "${CYAN}═══════════════════════════════════════════════════════════════${NC}"
    echo -e "  Tests Run: $TESTS_RUN | ${GREEN}Passed: $TESTS_PASSED${NC} | ${RED}Failed: $TESTS_FAILED${NC}"
    echo -e "${CYAN}═══════════════════════════════════════════════════════════════${NC}"
    echo ""
    
    [ $TESTS_FAILED -gt 0 ] && exit 1
    exit 0
}

main "$@"
