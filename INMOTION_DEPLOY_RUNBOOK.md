# InMotion Deployment Runbook (Queue A)

## 1) Upload Site Files
- Upload all root files from this repo to the InMotion document root (`public_html` or target vhost root).
- Ensure these server files are present:
  - `.htaccess`
  - `robots.txt`
  - `sitemap.xml`
  - `form-handler.php`
  - `portal-redirect.php`

## 2) Configure Lead Inbox
- In cPanel -> `Software` -> `Select PHP Version` -> `Options`, confirm `mail()` support is enabled.
- Set env var for lead routing:
  - `FAMILYVEST_LEADS_EMAIL=your-inbox@domain.com`
- If env vars are not available, update the fallback recipient in `form-handler.php`.

## 3) Validate Rewrite/Redirect Layer
- Confirm extensionless routes work:
  - `/about`, `/planning`, `/contact`, `/privacy`
- Confirm migration redirects work:
  - `/client-login` -> `/login`
  - `/complimentary-assessment` -> `/get-started`
  - `/autism-resources` -> `/autism-resource-navigator`
  - `/family-vest/blog/...` -> `/blog`
- Confirm the full final redirect map is deployed (not just sample rows).
- Use `audit-deliverables/2026-03-03/inmotion-redirects-full-snippet.htaccess` as the full 153-rule redirect source.

## 4) Validate Conversion Paths
- Submit `contact` form and `get-started` form with test email.
- Confirm redirect to `/thank-you` and email receipt.
- Test `/login` redirects to `https://app.farther.com` as expected.

## 5) Validate Crawl and Index Controls
- `https://familyvest.com/robots.txt` returns 200.
- `https://familyvest.com/sitemap.xml` returns 200 and valid XML.
- Run Screaming Frog crawl on production domain:
  - expect no internal broken links.
  - expect no missing canonical on indexable pages.
  - expect no redirect chains.

## 6) Launch Monitoring (Day 1-7)
- Daily:
  - GSC coverage errors
  - top landing page sessions
  - leads submitted from forms
  - 404 and redirect misses in server logs

## 7) Staging Verification
- Preview access is currently open and validated in `QA-STAGING-ACCESS.md`.
- Continue using preview for QA only; final indexability and redirect behavior must be validated on the InMotion-hosted production domain after cutover.
