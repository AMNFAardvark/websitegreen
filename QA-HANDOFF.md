# FamilyVest Website QA & Code Review Handoff

## Project Summary

Static HTML website for FamilyVest (wealth management, operating within Farther). 12 pages + assets deployed to Vercel as a preview/demo site. All pages are self-contained HTML files with inline CSS and embedded assets.

## Site Architecture

| Page | File | Purpose | Key Features |
|------|------|---------|-------------|
| Homepage | index.html | Landing page | Flyover video hero (destin-flyover.mp4), service cards, testimonials |
| About | about.html | Team/company info | Team headshots (placeholder), company story |
| Planning | planning.html | Financial planning hub | Links to 3 sub-pages |
| Retirement Planning | planning-retirement.html | Retirement services | Accessed via /planning/retirement (Vercel rewrite) |
| Special Needs Planning | planning-special-needs.html | Special needs services | Accessed via /planning/special-needs (Vercel rewrite) |
| Business Exits | planning-business-exits.html | Business exit planning | Accessed via /planning/business-exits (Vercel rewrite) |
| Investment Management | investment-management.html | Investment services | 7 content sections, process flow, stats |
| Blog | blog.html | Blog listing | Thumbnail placeholders (10) |
| Contact | contact.html | Contact form | Dark theme (navy-deep background) |
| Login | login.html | Client portal login | Dark theme, TODO: auth endpoint |
| Get Started | get-started.html | Onboarding wizard | Dark theme (navy background), multi-step form |
| Autism Resource Navigator | autism-resource-navigator.html | State-by-state autism resources | Interactive tool, 1MB+ (largest page) |

## Tech Stack

- Pure HTML/CSS/JS (no framework, no build step)
- Google Fonts: Cormorant Garamond + DM Sans
- CSS custom properties (variables) in :root
- IntersectionObserver for scroll-triggered fade-in animations
- Vercel for hosting with clean URL rewrites
- No external dependencies except Google Fonts and optional analytics

## Design System

### CSS Variables (defined in :root on every page)

```css
--font-display: 'Cormorant Garamond', Georgia, serif
--font-body: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif
--navy: #0a1628
--navy-deep: #060e1a
--navy-mid: #1a2a42
--navy-light: #243448
--gold: #c8a55c
--gold-light: #e8c97a
--gold-muted: rgba(200,165,92,0.15)
--off-white: #f5f3ef
--accent-cyan: #2BAAE2
--white: #ffffff
--text: #1a2a42
--text-light: #4a5568
--text-muted: #808A94
--sand: #d4c5a0
--sand-dark: #b8a67a
--content-max: 1200px
--content-narrow: 720px
```

### Standard Components

**Navigation:** `<nav class="nav" id="mainNav">` with dropdown for Planning sub-pages. Scroll-triggered hide/show. Mobile hamburger toggle via `#navToggle` / `#navLinks`.

**Footer:** `<footer class="site-footer">` with brand info, navigation links, legal text.

**Animations:** Elements with `class="fade-in"` start at `opacity:0` and become visible via IntersectionObserver adding `class="visible"`.

**Logo:** FamilyVest at Farther PNG, embedded as base64 data URI in nav-logo div on every page.

## Vercel Configuration (vercel.json)

```json
{
  "cleanUrls": true,
  "trailingSlash": false,
  "rewrites": [
    { "source": "/planning/retirement", "destination": "/planning-retirement.html" },
    { "source": "/planning/special-needs", "destination": "/planning-special-needs.html" },
    { "source": "/planning/business-exits", "destination": "/planning-business-exits.html" }
  ],
  "headers": [
    {
      "source": "/(.*)",
      "headers": [
        { "key": "X-Frame-Options", "value": "SAMEORIGIN" },
        { "key": "X-Content-Type-Options", "value": "nosniff" }
      ]
    }
  ]
}
```

## Issues Fixed in This Session

### Critical (pages were broken/invisible)

1. **6 truncated HTML files** -- blog.html, contact.html, get-started.html, investment-management.html, planning-business-exits.html, planning-retirement.html were missing `</script>`, `</body>`, `</html>` closing tags. The IntersectionObserver script was cut off mid-function. This caused all fade-in content to remain invisible (opacity:0).

2. **55 undefined CSS variables** -- Variables like `--white`, `--text`, `--sand`, `--sand-dark`, `--section-pad`, `--text-muted` were used in CSS but never defined in `:root`. Caused backgrounds, text colors, and spacing to silently fail.

3. **siteNav/mainNav mismatch** -- Original pages used `<nav id="siteNav" class="site-nav">`. Standardization changed to `<nav id="mainNav" class="nav">` but left old JavaScript referencing `getElementById('siteNav')` and old `.site-nav` CSS rules. The null reference killed script execution on scroll.

4. **contact.html dark-on-dark** -- Body had `background: var(--navy-deep)` with `color: var(--navy-mid)` making text invisible on dark background. Changed to `color: var(--off-white)`.

### Moderate

5. **Missing flyover video** -- Homepage referenced `destin-flyover.mp4` but file was not in the site folder. Copied from workspace root (`Destin_30A_Trees.mp4`).

6. **Header logo not rendering** -- Pages used old base64 JPEG logo. Replaced with new FamilyVest at Farther PNG logo (optimized to 24KB base64).

## Known Remaining Items for QA

### Content Placeholders
- **blog.html**: 10 blog post thumbnail images are placeholders
- **about.html**: Team headshot images are placeholders (except Todd's)
- **get-started.html**: 2 YouTube video embed IDs are placeholders (`YOUR_VIDEO_ID`)
- **login.html**: Auth endpoint is a TODO (`action="#"`)

### QA Checklist for Next Session

#### Rendering & Visual
- [ ] Every page loads with visible content (no blank sections)
- [ ] Logo renders correctly in nav on all 12 pages
- [ ] Homepage flyover video plays
- [ ] Navigation dropdown shows planning sub-pages on hover
- [ ] Mobile hamburger menu works on all pages
- [ ] Footer renders consistently across all pages
- [ ] fade-in animations trigger on scroll
- [ ] Contact page text is readable on dark background
- [ ] Color scheme is consistent across pages (navy/gold/white theme)

#### Navigation & Links
- [ ] All nav links resolve (no 404s)
- [ ] /planning/retirement, /planning/special-needs, /planning/business-exits all work
- [ ] /investment-management loads with all 7 sections visible
- [ ] Back/forward browser navigation works
- [ ] Logo click returns to homepage from all pages
- [ ] CTA buttons link to correct destinations

#### Performance & SEO
- [ ] Page load times (target: <3s on broadband, excluding video)
- [ ] Largest Contentful Paint (LCP) -- video and hero images
- [ ] Meta titles and descriptions on all pages
- [ ] Open Graph tags for social sharing
- [ ] JSON-LD structured data present
- [ ] Canonical URLs set
- [ ] Image alt text for accessibility
- [ ] Skip-to-content link works

#### Code Quality
- [ ] No duplicate script blocks with conflicting logic
- [ ] No unused CSS rules (especially .site-nav remnants)
- [ ] No undefined CSS variables
- [ ] All HTML files have proper closing tags
- [ ] No console errors on any page
- [ ] External resources (Google Fonts, Cloudflare) load
- [ ] Form submissions handled (contact, get-started)

#### Cross-Browser / Device
- [ ] Chrome, Safari, Firefox on desktop
- [ ] iOS Safari, Android Chrome on mobile
- [ ] Responsive breakpoints: 768px, 480px
- [ ] Nav collapse to hamburger on mobile

## File Inventory

```
familyvest-site-FIXED/
├── index.html                 (354KB) Homepage with flyover video
├── about.html                 (344KB) About/team page
├── planning.html              (268KB) Planning hub
├── planning-retirement.html   (213KB) Retirement planning
├── planning-special-needs.html (192KB) Special needs planning
├── planning-business-exits.html (183KB) Business exit planning
├── investment-management.html (292KB) Investment services
├── blog.html                  (206KB) Blog listing
├── contact.html               (62KB)  Contact form
├── login.html                 (176KB) Client portal login
├── get-started.html           (432KB) Onboarding wizard
├── autism-resource-navigator.html (999KB) Autism resource tool
├── familyvest-logo.png        (137KB) Full-size logo
├── familyvest-logo-nav.png    (25KB)  Nav-optimized logo
├── destin-flyover.mp4         (29MB)  Homepage hero video
├── vercel.json                Config: clean URLs + rewrites
├── deploy.sh                  One-command Vercel deploy
├── _qa-preview.html           (20KB)  QA dashboard (internal)
└── wordpress-files/           PHP files (not deployed)
```

## Deploy Instructions

From Terminal on Mac:
```bash
# Preview deploy
bash ~/Desktop/FamilyVest/familyvest-site-FIXED/deploy.sh

# Production deploy
cd ~/Desktop/FamilyVest/familyvest-site-FIXED
npx vercel --prod --yes
```

## Prompt for New Conversation

Copy-paste this to start a QA/code review session:

---

I need you to QA and code review a static HTML website for FamilyVest (wealth management firm). The site is 12 HTML pages deployed to Vercel. All files are in my workspace at `familyvest-site-FIXED/`.

Read the `QA-HANDOFF.md` file first for full context -- it has the site architecture, design system, known issues, and a detailed QA checklist.

Priority tasks:
1. Open each page in the browser and verify it renders correctly -- visible content, working nav, consistent styling
2. Check for console errors on every page
3. Verify all navigation links work (especially planning sub-pages which use Vercel rewrites)
4. Run a code quality pass: find any remaining dead CSS, duplicate scripts, or undefined variables
5. Test mobile responsiveness at 768px and 480px breakpoints
6. Check SEO fundamentals: meta tags, structured data, canonical URLs
7. Report all findings with severity (critical/moderate/low) and specific file + line references

The live preview URL is: [paste your Vercel URL here]

---
