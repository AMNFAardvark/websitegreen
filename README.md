# FamilyVest Website — Static HTML Build

## Overview

Static HTML website for FamilyVest at Farther. Core marketing templates plus legal/compliance and utility pages. Designed for Vercel preview deployment and InMotion production deployment.

**Live preview:** Deploy via `bash deploy.sh` or `npx vercel --yes`
**Production:** `npx vercel --prod --yes`

---

## Site Architecture

| Page | File | Description |
|------|------|-------------|
| Home | `index.html` | Hero flyover video, services overview, CTAs |
| About | `about.html` | Team bio, credentials, philosophy |
| Planning Hub | `planning.html` | Planning overview with links to sub-pages |
| Retirement | `planning-retirement.html` | Retirement planning detail page |
| Special Needs | `planning-special-needs.html` | Special needs planning (ABLE, SNTs, Medicaid) |
| Business Exits | `planning-business-exits.html` | Business exit/succession planning |
| Investments | `investment-management.html` | Investment management philosophy and process |
| Blog | `blog.html` | Blog/resources listing |
| Contact | `contact.html` | Full-page Google Map with overlay card, form, reviews |
| Login | `login.html` | Client portal login page |
| Get Started | `get-started.html` | Onboarding/scheduling page |
| Autism Navigator | `autism-resource-navigator.html` | Interactive special needs resource tool |
| Privacy | `privacy.html` | Privacy policy |
| Terms | `terms.html` | Terms of use |
| Disclosures | `disclosures.html` | Regulatory disclosures |
| ADV | `adv.html` | ADV request information |
| Forgot Password | `forgot-password.html` | Password-reset routing page |
| Thank You | `thank-you.html` | Post-form submission confirmation |
| 404 | `404.html` | Custom not-found page |

---

## Tech Stack

- **HTML/CSS/JS** — Self-contained, no build step required
- **Fonts** — Google Fonts (Cormorant Garamond + DM Sans), loaded via CDN
- **Images/Logo** — Base64 encoded inline (no external image dependencies)
- **Video** — `destin-flyover.mp4` (29MB) for homepage hero
- **Hosting** — Vercel with `cleanUrls` and URL rewrites for `/planning/*` sub-paths
- **Animations** — IntersectionObserver for `.fade-in` (scroll reveal) and `.rv` (contact page reveal)

---

## Design System

### Colors (CSS Custom Properties)
```
--navy: #0a1628        (primary background)
--navy-deep: #060e1a   (deepest background)
--navy-mid: #1a2a42    (mid-tone panels)
--gold: #c8a55c        (accent, CTAs)
--gold-light: #e8c97a  (gradient endpoints)
--off-white: #f5f3ef   (light backgrounds)
```

### Typography
```
--font-display: 'Cormorant Garamond'  (headings)
--font-body: 'DM Sans'                (body text)
```

---

## Vercel Configuration

`vercel.json` handles:
- `cleanUrls: true` — Strips `.html` extensions from URLs
- URL rewrites: `/planning/retirement` → `planning-retirement.html`, etc.
- Security headers: X-Frame-Options, X-Content-Type-Options

---

## Deployment

### Preview deploy
```bash
bash deploy.sh
# or
cd ~/Desktop/FamilyVest/familyvest-site-FIXED && npx vercel --yes
```

### Production deploy
```bash
cd ~/Desktop/FamilyVest/familyvest-site-FIXED && npx vercel --prod --yes
```

### InMotion deploy
- Upload repo root files to InMotion document root.
- Keep `.htaccess`, `robots.txt`, `sitemap.xml`, `form-handler.php`, and `portal-redirect.php` in place.
- Follow [INMOTION_DEPLOY_RUNBOOK.md](INMOTION_DEPLOY_RUNBOOK.md) for cutover and validation steps.

---

## QA Checklist

### Critical — Must verify after every deploy

- [ ] **Homepage**: Flyover video plays, nav dropdown works, all CTAs link correctly
- [ ] **Planning dropdown**: Hover reveals Retirement, Special Needs, Business Exits links
- [ ] **Planning sub-pages**: All 3 load via `/planning/retirement`, `/planning/special-needs`, `/planning/business-exits`
- [ ] **Investment Management**: Content renders (not blank), fade-in animations trigger on scroll
- [ ] **Contact page**: Google Map visible, overlay card visible (form, phone, reviews), form fields usable
- [ ] **Logo**: FamilyVest at Farther logo renders in nav header on all pages
- [ ] **Footer**: Consistent across all 12 pages, all links work
- [ ] **Mobile nav**: Hamburger menu opens, all links accessible, planning sub-links visible

### Secondary — Code quality and performance

- [ ] **HTML validation**: No unclosed tags, no duplicate IDs
- [ ] **CSS variables**: All `var(--name)` references have matching `:root` definitions
- [ ] **JavaScript**: No console errors, IntersectionObserver runs on all pages
- [ ] **Lighthouse**: Performance, Accessibility, SEO scores
- [ ] **Cross-browser**: Chrome, Safari, Firefox, mobile Safari
- [ ] **Responsive**: Test at 1440px, 1024px, 768px, 375px breakpoints
- [ ] **Video**: Homepage hero video loads and plays (may be slow — 29MB file)

### Known Items / Future Work

- [ ] SEO meta tags: Some pages may need unique OG images
- [x] Form submission: Contact/Get Started/Login now wired to server-side endpoints (`form-handler.php`, `portal-redirect.php`)
- [ ] Video optimization: `destin-flyover.mp4` is 29MB, consider compression or streaming
- [ ] Image optimization: Base64 logos add ~34KB per page. Consider external image hosting post-launch
- [ ] Accessibility: ARIA labels, focus management, skip links present but need audit
- [ ] Analytics: `gtag` and `dataLayer` event tracking stubs present, need GA4 property ID
- [x] Cloudflare email dependency removed from `get-started.html` (uses direct `mailto:`)
- [x] Privacy/Terms/Disclosures/ADV pages created and linked
- [ ] Set `FAMILYVEST_LEADS_EMAIL` server env var in InMotion for production form routing

---

## File Inventory

### Deployable files (push these to GitHub)
```
index.html                    — Homepage
about.html                    — About page
planning.html                 — Planning hub
planning-retirement.html      — Retirement planning
planning-special-needs.html   — Special needs planning
planning-business-exits.html  — Business exits planning
investment-management.html    — Investment management
blog.html                     — Blog/resources
contact.html                  — Contact with map
login.html                    — Client login
get-started.html              — Get started/scheduling
autism-resource-navigator.html — Autism resource tool
privacy.html                  — Privacy policy
terms.html                    — Terms of use
disclosures.html              — Regulatory disclosures
adv.html                      — ADV request page
forgot-password.html          — Password reset helper
thank-you.html                — Post-form confirmation
404.html                      — Custom not-found page
form-handler.php              — Contact/Get Started form backend (PHP)
portal-redirect.php           — Login redirect endpoint to Farther portal
robots.txt                    — Crawl directives
sitemap.xml                   — XML sitemap
.htaccess                     — InMotion rewrite/redirect config
redirect-map-template.csv     — Launch redirect mapping worksheet
QA-STAGING-ACCESS.md          — Steps to disable Vercel QA protection
destin-flyover.mp4            — Homepage hero video (29MB)
familyvest-logo.png           — Full logo image
familyvest-logo-nav.png       — Nav-sized logo
vercel.json                   — Vercel deployment config
deploy.sh                     — Deployment helper script
```

### Support files (do not deploy)
```
_qa-preview.html              — QA dashboard (internal)
QA-HANDOFF.md                 — QA handoff notes
README.md                     — This file
wordpress-files/              — Legacy WordPress reference
.vercel/                      — Vercel project config (auto-generated)
```

---

## Recent Fixes Applied (March 3, 2026)

1. **Contact page card invisible** — `.rv` class elements had `opacity:0` with no script to add `.on`. Added IntersectionObserver for `.rv` elements.
2. **Contact page broken JS** — IntersectionObserver created as `io` but referenced as `observer`. Fixed variable name.
3. **Planning dropdown CSS conflict** — All 3 planning sub-pages had legacy `display:none` rules overriding standardized dropdown CSS. Removed legacy duplicates.
4. **Duplicate `id="mainNav"`** — Header and nav both had `id="mainNav"` on about.html and contact.html. Removed from header.
5. **6 truncated HTML files** — blog, contact, get-started, investment-management, planning-business-exits, planning-retirement were missing `</script></body></html>`. Reconstructed.
6. **55 undefined CSS variables** — Variables like `--white`, `--text`, `--sand` used but never defined in `:root`. Added definitions.
7. **siteNav/mainNav mismatch** — JS referenced `getElementById('siteNav')` but nav had `id="mainNav"`. Removed broken scripts.
8. **Logo not rendering** — Embedded new FamilyVest at Farther logo as base64 in all 12 pages.
9. **Video missing** — Copied `Destin_30A_Trees.mp4` as `destin-flyover.mp4` to site folder.
# websitegreen
