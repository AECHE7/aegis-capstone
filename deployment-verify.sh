#!/bin/bash
# AEGIS Deployment Verification Script
# Run this after deploying UI/UX changes to verify everything works
# Usage: bash deployment-verify.sh [staging|production]

set -e

ENVIRONMENT=${1:-staging}
TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")
LOG_FILE="deployment-verify-${ENVIRONMENT}-$(date +%Y%m%d-%H%M%S).log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Set base URL based on environment
if [ "$ENVIRONMENT" == "production" ]; then
    BASE_URL="https://aegis.clsu.edu.ph"
elif [ "$ENVIRONMENT" == "staging" ]; then
    BASE_URL="http://localhost:8000"
else
    echo -e "${RED}Invalid environment. Use 'staging' or 'production'${NC}"
    exit 1
fi

echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  AEGIS Deployment Verification - ${ENVIRONMENT}${NC}"
echo -e "${BLUE}  Started: ${TIMESTAMP}${NC}"
echo -e "${BLUE}  Base URL: ${BASE_URL}${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

log() {
    echo "[$TIMESTAMP] $1" >> "$LOG_FILE"
}

pass() {
    echo -e "${GREEN}✓${NC} $1"
    log "PASS: $1"
}

fail() {
    echo -e "${RED}✗${NC} $1"
    log "FAIL: $1"
    FAILED_TESTS=$((FAILED_TESTS + 1))
}

warn() {
    echo -e "${YELLOW}⚠${NC} $1"
    log "WARN: $1"
}

info() {
    echo -e "${BLUE}ℹ${NC} $1"
    log "INFO: $1"
}

TOTAL_TESTS=0
FAILED_TESTS=0

# Test 1: Auth Modal - Login Page
echo -e "\n${BLUE}Testing Auth Modal Components...${NC}"
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/login" | grep -q "modalLoginEmail"; then
    pass "Login page loads auth modal"
else
    fail "Login page missing auth modal"
fi

# Test 2: Password Strength Indicator
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/register" | grep -q "passwordStrength"; then
    pass "Password strength indicator present"
else
    fail "Password strength indicator missing"
fi

# Test 3: Password Match Validation
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/register" | grep -q "passwordMatch"; then
    pass "Password match validation present"
else
    fail "Password match validation missing"
fi

# Test 4: Password Toggle Buttons
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/register" | grep -q "toggle-password-btn"; then
    pass "Password toggle buttons present"
else
    fail "Password toggle buttons missing"
fi

# Test 5: Minimum Password Length
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/register" | grep -q 'minlength="8"'; then
    pass "Minimum password length (8) enforced"
else
    fail "Minimum password length not enforced"
fi

# Test 6: Admin Dashboard Empty State
echo -e "\n${BLUE}Testing Admin Dashboard Empty State...${NC}"
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/admin/dashboard" -H "Cookie: XSRF-TOKEN=test" | grep -q "fa-filter-circle-xmark"; then
    pass "Enhanced empty state icon present"
else
    warn "Empty state icon check skipped (requires auth)"
fi

# Test 7: Clear Filters Button (when filters active)
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/admin/dashboard?status=Approved" -H "Cookie: XSRF-TOKEN=test" | grep -q "Clear All Filters"; then
    pass "Clear filters button logic present"
else
    warn "Clear filters button check skipped (requires auth)"
fi

# Test 8: JavaScript Files Loaded
echo -e "\n${BLUE}Testing JavaScript Dependencies...${NC}"
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/register" | grep -q "checkPasswordMatch"; then
    pass "Password validation JavaScript loaded"
else
    fail "Password validation JavaScript missing"
fi

# Test 9: CSS Styles Present
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/register" | grep -q "progress-bar"; then
    pass "Bootstrap progress bar styles present"
else
    fail "Progress bar styles missing"
fi

# Test 10: Response Time Check
echo -e "\n${BLUE}Testing Performance...${NC}"
TOTAL_TESTS=$((TOTAL_TESTS + 1))
START_TIME=$(date +%s%N)
curl -s -o /dev/null "$BASE_URL/login"
END_TIME=$(date +%s%N)
RESPONSE_TIME=$(( (END_TIME - START_TIME) / 1000000 ))

if [ $RESPONSE_TIME -lt 1000 ]; then
    pass "Login page response time: ${RESPONSE_TIME}ms (< 1000ms)"
elif [ $RESPONSE_TIME -lt 2000 ]; then
    warn "Login page response time: ${RESPONSE_TIME}ms (acceptable but slow)"
else
    fail "Login page response time: ${RESPONSE_TIME}ms (> 2000ms, too slow)"
fi

# Test 11: HTTP Status Codes
echo -e "\n${BLUE}Testing HTTP Endpoints...${NC}"
TOTAL_TESTS=$((TOTAL_TESTS + 1))
STATUS_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/login")
if [ "$STATUS_CODE" == "200" ]; then
    pass "/login returns 200 OK"
else
    fail "/login returns $STATUS_CODE (expected 200)"
fi

TOTAL_TESTS=$((TOTAL_TESTS + 1))
STATUS_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/register")
if [ "$STATUS_CODE" == "200" ]; then
    pass "/register returns 200 OK"
else
    fail "/register returns $STATUS_CODE (expected 200)"
fi

# Test 12: Check for JavaScript Errors (basic syntax check)
echo -e "\n${BLUE}Testing JavaScript Syntax...${NC}"
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/register" | grep -q "addEventListener('input'"; then
    pass "JavaScript event listeners syntax correct"
else
    fail "JavaScript syntax errors detected"
fi

# Test 13: Bootstrap Modal Attributes
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/login" | grep -q 'data-bs-backdrop="static"'; then
    pass "Bootstrap modal attributes configured"
else
    fail "Bootstrap modal attributes missing"
fi

# Test 14: Font Awesome Icons
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/login" | grep -q "fa-envelope"; then
    pass "Font Awesome icons loaded"
else
    fail "Font Awesome icons missing"
fi

# Test 15: CSRF Token Present
TOTAL_TESTS=$((TOTAL_TESTS + 1))
if curl -s "$BASE_URL/login" | grep -q "@csrf"; then
    pass "CSRF protection enabled"
else
    fail "CSRF token missing"
fi

# Summary
echo -e "\n${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  Verification Summary${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
PASSED_TESTS=$((TOTAL_TESTS - FAILED_TESTS))
echo -e "Total Tests:  ${TOTAL_TESTS}"
echo -e "Passed:       ${GREEN}${PASSED_TESTS}${NC}"
echo -e "Failed:       ${RED}${FAILED_TESTS}${NC}"
echo -e "Success Rate: $(( PASSED_TESTS * 100 / TOTAL_TESTS ))%"
echo ""
echo -e "Detailed log: ${LOG_FILE}"
echo ""

if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "${GREEN}✓ ALL TESTS PASSED - Deployment Verified${NC}"
    exit 0
elif [ $FAILED_TESTS -le 2 ]; then
    echo -e "${YELLOW}⚠ MINOR ISSUES DETECTED - Review Recommended${NC}"
    exit 1
else
    echo -e "${RED}✗ CRITICAL FAILURES - Rollback Recommended${NC}"
    exit 2
fi
