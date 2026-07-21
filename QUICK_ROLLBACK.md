# 🚨 QUICK ROLLBACK GUIDE - AEGIS UI/UX Changes

**If something breaks after deployment, follow these steps IMMEDIATELY:**

---

## ⚡ FASTEST ROLLBACK (30 seconds)

```bash
# 1. SSH into server
ssh user@server

# 2. One-line rollback
git revert ee22fab --no-edit && php artisan view:clear && sudo systemctl reload php8.2-fpm

# 3. Verify
curl -I https://aegis.clsu.edu.ph/login | grep "200 OK"
```

**Done!** Old version restored.

---

## 📋 DETAILED ROLLBACK (3 minutes)

### Step 1: Identify Current Commit
```bash
git log -1 --oneline
# Output: ee22fab feat(ui/ux): enhance login/register modal...
```

### Step 2: Create Rollback Commit
```bash
git revert ee22fab --no-edit
```

### Step 3: Clear Caches
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Step 4: Restart Services
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

### Step 5: Verify Rollback
```bash
# Check auth modal (should show OLD version without password strength)
curl https://aegis.clsu.edu.ph/register | grep "passwordStrength"
# Should return NOTHING if rollback successful

# Check empty state (should show OLD simple version)
curl https://aegis.clsu.edu.ph/admin/dashboard | grep "Clear All Filters"
# Should return NOTHING if rollback successful
```

---

## 🔥 EMERGENCY MANUAL ROLLBACK (if git fails)

### Auth Modal File:
```bash
# Download old version
wget https://raw.githubusercontent.com/AECHE7/aegis-capstone/80940db/resources/views/components/auth-modal.blade.php \
  -O resources/views/components/auth-modal.blade.php

# Or copy from backup
cp /var/backups/aegis/auth-modal.blade.php.bak resources/views/components/auth-modal.blade.php
```

### Empty State File:
```bash
# Download old version
wget https://raw.githubusercontent.com/AECHE7/aegis-capstone/80940db/resources/views/admin/partials/application_table.blade.php \
  -O resources/views/admin/partials/application_table.blade.php

# Or copy from backup
cp /var/backups/aegis/application_table.blade.php.bak resources/views/admin/partials/application_table.blade.php
```

### Clear Caches:
```bash
php artisan view:clear
sudo systemctl reload php8.2-fpm
```

---

## ✅ POST-ROLLBACK VERIFICATION

```bash
# Run verification script
bash deployment-verify.sh production

# Expected output:
# ✗ Password strength indicator present (SHOULD FAIL after rollback)
# ✗ Password match validation present (SHOULD FAIL after rollback)
# ✓ Login page loads auth modal (should still pass)
# ✓ /login returns 200 OK (should still pass)
```

---

## 📞 WHO TO NOTIFY

**Immediately notify:**
1. **Technical Lead:** [NAME/PHONE]
2. **Product Owner:** [NAME/PHONE]
3. **OSA Admin Team:** support@clsu.edu.ph

**Incident Report Template:**
```
Subject: [URGENT] AEGIS Rollback Executed

Rolled back commit: ee22fab
Reason: [DESCRIBE ISSUE]
Impact: [NUMBER] users affected
Rollback time: [TIME]
Current status: Stable on previous version

Root cause analysis: In progress
Re-deployment ETA: TBD after fixes
```

---

## 🔍 COMMON ISSUES & QUICK FIXES

### Issue 1: "Password strength not showing"
**Severity:** Low (users can still register)
**Fix:** Check browser console for JavaScript errors
**Rollback?** NO - Monitor for 1 hour

### Issue 2: "Modal doesn't open"
**Severity:** CRITICAL (users cannot login)
**Fix:** None - ROLLBACK IMMEDIATELY
**Rollback?** YES

### Issue 3: "Clear filters button 404"
**Severity:** Medium (admins can manually clear)
**Fix:** Check route exists: `php artisan route:list | grep admin.dashboard`
**Rollback?** MAYBE - If >5 complaints in 10 minutes

### Issue 4: "Page is slow"
**Severity:** Medium
**Fix:** Check server resources: `htop`, `php-fpm status`
**Rollback?** ONLY if >2 seconds response time

---

## 📊 ROLLBACK DECISION MATRIX

| Issue | Users Affected | Response Time | Rollback? |
|-------|---------------|---------------|-----------|
| Visual glitch | Any | N/A | NO |
| JavaScript error (non-blocking) | <10 | N/A | NO |
| Form doesn't submit | >5 | <5 min | YES |
| 500 errors | >1 | <2 min | YES |
| Modal won't open | >3 | <3 min | YES |
| Slow performance (>2s) | >10 | <10 min | YES |

---

## 🛠️ PREVENTION FOR NEXT TIME

**Before deploying:**
- [ ] Run `bash deployment-verify.sh staging` first
- [ ] Test on at least 2 browsers
- [ ] Create backup: `cp file.blade.php file.blade.php.bak`
- [ ] Tag current version: `git tag backup-$(date +%Y%m%d)`
- [ ] Deploy during low-traffic window (10 PM - 6 AM)

**During deployment:**
- [ ] Keep monitoring dashboard open
- [ ] Watch error logs: `tail -f storage/logs/laravel.log`
- [ ] Have this rollback guide open

**After deployment:**
- [ ] Monitor for 30 minutes actively
- [ ] Check error rate every 5 minutes
- [ ] Validate with test accounts

---

## 📝 ROLLBACK CHECKLIST

```markdown
- [ ] Identified issue severity (use decision matrix)
- [ ] Decided rollback is necessary
- [ ] Notified technical lead
- [ ] Executed rollback command
- [ ] Cleared all caches
- [ ] Restarted services
- [ ] Verified rollback successful (curl tests)
- [ ] Tested login/register manually
- [ ] Notified stakeholders (OSA, Product Owner)
- [ ] Created incident report
- [ ] Scheduled post-mortem meeting
```

---

## 🎯 REMEMBER

1. **Don't panic** - Rollback is safe and fast
2. **Communicate** - Tell the team immediately
3. **Document** - Note what went wrong
4. **Learn** - Update tests to catch this next time

**Rollback is NOT a failure - it's a safety mechanism!**

---

**Last Updated:** July 21, 2026
**Document Owner:** DevOps Team
**Emergency Contact:** support@clsu.edu.ph
