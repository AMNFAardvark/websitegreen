# FamilyVest Website Launch Audit (Updated Post-Fix)
Date: March 3, 2026  
Prepared for: FamilyVest migration to replace current InMotion-hosted site

Assumptions used to complete this audit:
- Prompt typo `familyvest.vom` was interpreted as `https://www.familyvest.com/`.
- Primary legacy crawl evidence is represented in `familyvest-live-site-inventory.csv` and `redirect-map-template.csv`.
- New-site route, metadata, and link integrity evidence comes from repository inspection + route status checks against the open Vercel preview URL: `https://familyvest-site-8mm8crchq-amnfaardvarks-projects.vercel.app/`.
- Screaming Frog preview exports were captured and included under `sf-preview-crawl-2026-03-03/` and are used primarily to validate preview headers/indexability behavior.

## A. Executive Summary
Overall score: **77/100**  
Readiness status: **Launch with Conditions**

Weighted scoring (required rubric):
- Functionality & QA (25%): 8.0/10
- Performance/CWV (20%): 6.7/10
- Technical SEO (20%): 8.2/10
- On-page SEO/Content (10%): 8.0/10
- UX/UI & Conversion (20%): 7.8/10
- Accessibility (5%): 6.5/10
- Weighted total: 7.67/10 => **77/100**

Top 10 issues by business impact:
1. **Redirect deployment coverage is incomplete for migration cutover** (final map has 153 rows, but `.htaccess` currently contains only a subset).  
   Evidence: `redirect-map-template.csv` and `.htaccess`.
2. **Conversion form transport is implemented but not production-verified on InMotion** (`mail()` delivery, mailbox routing, logging permissions).  
   Evidence: `form-handler.php` + lack of server-side submission proof from production stack.
3. **Mobile performance remains weak (LCP 4.8s on preview home)**, mostly due large hero video payload.  
   Evidence: Lighthouse mobile run (March 3, 2026), network payload table.
4. **Hero video payload is extremely large** (`destin-flyover.mp4` transfers ~6.7MB mobile / ~15.2MB desktop in Lighthouse).  
   Evidence: Lighthouse network requests (`audit-deliverables/2026-03-03/performance-lighthouse-2026-03-03.json`).
5. **Legacy URL footprint risk** (171 legacy HTML URLs vs 19 new templates): ranking equity depends on full 1:1 redirect finalization.  
   Evidence: `familyvest-live-site-inventory.csv`, `familyvest-draft-site-inventory-postfix.csv`.
6. **Preview has `X-Robots-Tag: noindex` by platform behavior** and canonicalized indexability signals in crawl exports; production headers must be validated after cutover.  
   Evidence: `sf-preview-crawl-2026-03-03/directives_all.csv`, `curl -I` on preview.
7. **Accessibility gaps persist in Autism Resource Navigator** (many form inputs/textarea missing explicit labels).  
   Evidence: template-level input/label scan on `autism-resource-navigator.html`.
8. **External dependency risk in Autism Resource Navigator** (many outbound resources can timeout/fail and degrade UX trust).  
   Evidence: headless crawl logs and outbound-heavy template structure.
9. **Canonical host standard must be enforced consistently on launch** (`.htaccess` currently forces non-www; confirm this matches GSC property and existing backlink patterns).  
   Evidence: `.htaccess` host rewrite rules.
10. **Redirect implementation in `.htaccess` is only a subset of full mapping template** and must be expanded before DNS cutover.  
   Evidence: `.htaccess` vs `redirect-map-template.csv`.

Top 5 “fix this week” tasks:
1. Deploy the full 153-row redirect map into production `.htaccess` and verify one-hop behavior for all legacy URLs.
2. Run production form E2E tests on InMotion (`contact`, `get-started`) with real delivery, fallback logging, and GA4 conversion events.
3. Optimize or replace hero video to reduce mobile LCP and byte weight.
4. Patch accessibility labels/aria for Autism Resource Navigator inputs and textareas.
5. Execute final post-cutover crawl + GSC validation within first 24 hours.

## B. Site Inventory & Indexability Table
Full inventory deliverables:
- Legacy/live inventory (171 URLs): `audit-deliverables/2026-03-03/familyvest-live-site-inventory.csv`
- New-site inventory (post-fix, 19 URLs): `audit-deliverables/2026-03-03/familyvest-draft-site-inventory-postfix.csv`
- New-site analysis summary: `audit-deliverables/2026-03-03/familyvest_newsite_analysis_postfix.json`

New-site flag summary (post-fix):
- Duplicate titles: 0
- Missing meta descriptions: 0
- Missing H1: 0
- Missing robots meta: 0
- Missing canonicals: 0
- Canonical/OG mismatches: 0
- Broken internal links: 0
- Non-200 audited HTML routes on preview: 0

Legacy/live flag summary (crawl-derived inventory):
- URL count: 171
- Status mix: 149x 200, 16x 301, 6x 404
- Missing/weak metadata and heading gaps still present on legacy corpus and must be resolved via mapping + selective content preservation.

Priority URL snapshot:

| URL | Status | Canonical | Indexability | Title Len | Meta Len | H1 | Inlinks/Outlinks | Notes |
|---|---:|---|---|---:|---:|---|---|---|
| https://familyvest.com/ | 200 | self | Indexable | 64 | 223 | Y | 17/15 | Launch homepage route works on preview |
| https://familyvest.com/about | 200 | self | Indexable | 29 | 177 | Y | 20/15 | Live route verified |
| https://familyvest.com/investment-management | 200 | self | Indexable | 45 | 240 | Y | 21/15 | Live route verified |
| https://familyvest.com/planning | 200 | self | Indexable | 65 | 171 | Y | 17/15 | Live route verified |
| https://familyvest.com/contact | 200 | self | Indexable | 31 | 148 | Y | 27/15 | Form endpoint wired |
| https://familyvest.com/get-started | 200 | self | Indexable | 35 | 143 | Y | 31/15 | Form endpoint wired |
| https://familyvest.com/login | 200 | self | Non-Indexable | 35 | 150 | Y | 18/16 | Intentional noindex utility page |
| https://familyvest.com/privacy | 200 | self | Indexable | 18 | 60 | Y | 15/6 | Legal route now resolved |

## C. Functionality & Link Integrity (Critical)
Defect/condition log:

| Affected page URL | Element text / selector | Problem | Severity | Exact fix | Owner | QA step to verify fix |
|---|---|---|---|---|---|---|
| Global migration | URL mapping + Apache rules (`redirect-map-template.csv`, `.htaccess`) | Final redirect map is ready, but server rules currently represent a subset of mappings | Critical | Publish complete redirect rules from the final CSV and validate one-hop outcomes | SEO + Dev | Re-crawl old URL list; 100% resolve via one-hop 301->200 |
| `/contact`, `/get-started` | `form#contactForm`, `form#gsForm` | Actions are present but production delivery unverified on InMotion | High | Validate `mail()` transport, mailbox routing, SPF/DKIM alignment, and fallback logging permissions | DevOps + Dev | Submit valid/invalid payloads on InMotion and confirm mailbox + logs + redirects |
| `/login` | `form#loginForm` | Redirect-only handler in place; needs final SSO behavior confirmation with Farther | Medium | Validate query-string handoff and failed-input handling | Dev | Test valid email, invalid email, and no-email flows |
| `/autism-resource-navigator` | Multiple outbound links/resources | External dependency volume introduces timeout/failure risk | Medium | Add periodic outbound link QA and de-prioritize unreliable sources in UX hierarchy | Content + Dev | Automated outbound-link check + manual spot test |
| Launch server config | `.htaccess` redirect block | Only subset of full redirect template currently implemented | Critical | Generate and deploy complete redirect rules from final CSV | Dev | Compare production redirect list vs final CSV (100% parity) |

Also report (required):
- Broken internal links (new site): **0** (`familyvest_newsite_analysis_postfix.json`)
- Redirect chains/loops (new site): no chains observed in current checks; must be confirmed in full post-cutover crawl
- Dead-end pages: no major dead-end templates in current internal graph
- Broken conversion paths: no missing form actions; production delivery test still required

## D. UX/UI + Conversion Quality Review
Template-level recommendations (before -> after):

| Template | Before | After |
|---|---|---|
| Home | Strong visual design, but heavy hero media hurts mobile speed and CTA response time | Replace/stream hero video, keep same visual identity, preserve above-the-fold CTA prominence |
| About | Good credibility narrative; title still relatively short for geo + fiduciary intent | Expand title/meta with geo + credential terms while keeping brand tone |
| Services (`planning*`, `investment-management`) | Good structure and CTA pathways | Add localized service proof blocks (Destin + Emerald Coast + nationwide) and internal links to related service pages |
| Blog/Resources | Functional hub and Autism tool present | Improve content clustering and explicit pillar/child internal links for topic authority |
| Contact/Get Started | Forms now wired, but production conversion instrumentation still pending | Add GA4 success/error events and explicit confirmation-state analytics |
| Legal/Compliance | Legal pages now present and routable | Add “Last Updated” and reviewer attribution to improve trust/compliance clarity |

Concrete conversion improvements:
- Replace generic CTA copy with a higher-intent variant: `Schedule Your 20-Minute Fiduciary Intro`.
- Add reassurance line near primary CTA: `Leave with a clear next-step plan and no obligation.`
- Add proof adjacent to CTA: credential badges, fiduciary statement, and service-fit qualifiers.

## E. Performance + Core Web Vitals Audit
Measured baselines (preview home, March 3, 2026):
- Mobile Lighthouse: Performance **69**, FCP **4.6s**, LCP **4.8s**, CLS **0**, TBT **0ms**, transfer **~7,006 KiB**
- Desktop Lighthouse: Performance **88**, FCP **1.4s**, LCP **1.7s**, CLS **0**, TBT **0ms**, transfer **~15,343 KiB**

Largest payload drivers from Lighthouse network:
- `/destin-flyover.mp4`: ~6.7MB (mobile profile), ~15.2MB (desktop profile)
- Root document: ~219KB
- Logo image: ~141KB

Prioritized performance backlog:

| Task | Impact | Effort | Expected metric lift | Owner |
|---|---|---|---|---|
| Replace/stream hero video (`destin-flyover.mp4`) with adaptive or poster-first strategy | High | M | Mobile LCP -1.0s to -2.0s | Dev |
| Reduce homepage media bytes (poster compression, video preload tuning) | High | S-M | Transfer -3MB to -8MB | Dev |
| Apply responsive image formats (`webp/avif` + `srcset`) on major templates | Medium | M | FCP/LCP improvement, bandwidth reduction | Dev |
| Self-host/subset fonts and preload only required weights | Medium | M | FCP -0.1s to -0.4s | Dev |
| Add post-launch CWV monitoring and alert thresholds | Medium | S | Faster regression detection | Analytics + DevOps |

## F. Technical SEO Audit
Required checks with risk-if-ignored:
- `robots.txt`: present and valid in repo.  
  Risk if ignored: crawler confusion for utility endpoints.
- XML sitemap: present and valid in repo for primary indexable routes.  
  Risk if ignored: slower re-discovery during migration.
- Canonicals: present on all audited templates; no canonical/OG mismatches post-fix.  
  Risk if ignored: URL signal fragmentation.
- Meta robots logic: explicit noindex on utility pages (e.g., login/404/thank-you), index/follow on primary pages.  
  Risk if ignored: accidental index bloat or sensitive page indexation.
- HTTPS consistency: enforced in `.htaccess`; preview also served over HTTPS.  
  Risk if ignored: trust/security regression.
- 404 behavior: custom `404.html` configured with `ErrorDocument 404 /404.html`.  
  Risk if ignored: poor UX + crawl waste.
- URL hygiene/clean routing: extensionless routing rules are in place for Apache.  
  Risk if ignored: clean URL 404s on InMotion.
- Structured data: present on key pages; maintain URL consistency in JSON-LD after final domain cutover.  
  Risk if ignored: reduced rich-result coherence.
- OG/Twitter metadata: now points to existing logo image path on audited templates.  
  Risk if ignored: weak social previews.
- Index bloat risk: controlled in current 19-page corpus; migration risk remains tied to legacy URL handling.

Screaming Frog evidence note:
- Preview crawl exports show `X-Robots-Tag: noindex` and canonicalized/noindex classification behavior typical of preview deployments.
- This is expected in preview and must be revalidated on production domain after launch.

## G. On-Page SEO & Content Effectiveness
Priority URL recommendations:

### 1) Home (`https://familyvest.com/`)
- SEO title: `Fee-Only Fiduciary Financial Advisor in Destin, FL | FamilyVest`
- Meta description: `FamilyVest provides fiduciary financial planning and investment management in Destin, Santa Rosa Beach, Miramar Beach, and nationwide. Schedule your complimentary intro call.`
- H1: `Fiduciary Financial Planning in Destin, FL and Nationwide`
- Suggested internal links: `/planning-retirement`, `/planning-special-needs`, `/investment-management`, `/get-started`

### 2) About (`https://familyvest.com/about`)
- SEO title: `About FamilyVest | Todd Sensing, CFA, CFP | Fiduciary Advisor`
- Meta description: `Meet Todd Sensing, CFA and CFP professional. FamilyVest delivers fee-only fiduciary guidance for retirees, families with special-needs planning concerns, and business owners.`
- H1: `About FamilyVest and Todd Sensing, CFA, CFP`
- Suggested internal links: `/investment-management`, `/planning-special-needs`, `/get-started`

### 3) Investment Management (`https://familyvest.com/investment-management`)
- SEO title: `Fiduciary Investment Management in Destin, FL | FamilyVest`
- Meta description: `Tax-aware, evidence-based investment management from a fiduciary advisor. Get a portfolio strategy aligned to your goals, risk, and retirement timeline.`
- H1: `Fiduciary Investment Management Built for Long-Term Goals`
- Suggested internal links: `/planning`, `/planning-retirement`, `/get-started`, `/about`

Content effectiveness focus:
- Preserve high-value legacy content via selective republish or precise redirecting.
- Build internal topical clusters from service pages into relevant blog/resource hubs.
- Expand localized sections for Destin + Emerald Coast intents while retaining national positioning.

## H. Migration Plan to InMotion (Protect SEO Equity)
### Pre-Launch
1. Freeze final URL list and prepare deployment-ready redirect rules from the finalized mapping file.
2. Build final 1:1 redirect map (old->new) with no chains.
3. Merge full redirect rule set into production `.htaccess`.
4. Confirm canonical host policy (www vs non-www) and align with GSC property strategy.
5. Validate `robots.txt`, `sitemap.xml`, canonical tags, and utility-page noindex behavior.
6. Validate forms and logging on InMotion with production mailbox target.
7. Baseline KPIs: rankings, sessions, leads, indexed pages, top landing pages.

### Launch Day
1. Upload site and `.htaccess` to InMotion.
2. Enable final redirects and force HTTPS.
3. Smoke test top routes and conversion paths.
4. Re-submit sitemap in Google Search Console and Bing Webmaster Tools.
5. Add GA4 launch annotation.
6. Run immediate crawl to confirm:
   - 0 broken internal links
   - 0 redirect chains
   - 100% mapped legacy URLs resolving as expected

### First 30 Days
1. Week 1 daily checks: 404s, soft-404s, top landing pages, leads.
2. Fix new 404s within 24 hours.
3. Weekly: compare rankings, sessions, and conversions vs baseline.
4. Re-crawl for redirect/canonical drift.
5. Monitor CWV under real traffic and tune media/caching.

Must include:
1. Redirect template: `old_url,new_url,status_code,notes` (file included: `redirect-map-template.csv`)
2. Full Apache redirect snippet generated from template: `inmotion-redirects-full-snippet.htaccess`
3. Rollback criteria and steps (see `INMOTION_DEPLOY_RUNBOOK.md`)
4. 30/60/90 stabilization roadmap (included below in Queues B/C)

## I. Accessibility & Compliance Snapshot
Findings:
- Keyboard/navigation basics appear present on primary templates (skip link + semantic nav/footer patterns).
- Focus treatment exists but needs explicit keyboard QA on InMotion post-deploy.
- Major risk area: `autism-resource-navigator.html` has numerous inputs/textareas missing explicit labels.
- Contrast and semantics on core templates are broadly acceptable; verify with automated scanner post-cutover.
- Compliance pages now exist (`/privacy`, `/terms`, `/disclosures`, `/adv`) and are linked sitewide.

High-risk ADA/WCAG gaps to address first:
1. Add explicit `<label for>` or `aria-label` for all interactive form controls in Autism Resource Navigator.
2. Validate error messaging and focus management after form submission failures.
3. Run automated + manual keyboard audits on all conversion-critical templates.

## J. Prioritized Implementation Plan
### Queue A (Critical, 0-7 days)

| Task | Why it matters | Exact implementation steps | Owner | Effort | Success KPI |
|---|---|---|---|---|---|
| Deploy full final redirect set | Prevents ranking/equity loss on cutover | Use final 153-row CSV to generate/publish complete `.htaccess` redirects | SEO + Dev | M | 100% legacy map coverage, 0 chains, 0 unmapped rows |
| Production form validation on InMotion | Protects lead capture and conversion continuity | Test POST flows, verify mailbox delivery, validate error/success redirects, enable GA4 events | Dev + DevOps | M | Successful lead delivery and tracked conversions |
| Mobile performance remediation for hero media | Directly impacts conversion + SEO on mobile | Replace/stream video, optimize poster, retest Lighthouse mobile | Dev | M | Mobile LCP <= 3.0s target trend |
| Final launch smoke crawl | Catch launch-breaking SEO/UX defects early | Crawl top routes + mapped legacy URL list immediately post-cutover | SEO + Dev | S | 0 critical crawl errors |

### Queue B (High Value, 8-30 days)

| Task | Why it matters | Exact implementation steps | Owner | Effort | Success KPI |
|---|---|---|---|---|---|
| Accessibility remediation (Autism tool) | Reduces legal/accessibility risk and improves UX | Add labels/ARIA + error/focus handling, run accessibility QA | Dev + QA | M | Zero high-severity form-label issues |
| CWV hardening and monitoring | Stabilizes UX and ranking signals | Set CWV dashboards + alerts, optimize fonts/images/media iteratively | Dev + Analytics | M | Improved mobile performance score and stable CWV trends |
| Internal linking enrichment | Supports topical authority and conversion flow | Add contextual links between services/blog/resource pages | Content + SEO | S-M | Increased page depth and assisted conversions |

### Queue C (Strategic, 31-90 days)

| Task | Why it matters | Exact implementation steps | Owner | Effort | Success KPI |
|---|---|---|---|---|---|
| Legacy content preservation strategy | Recovers long-tail SEO value from consolidated corpus | Rebuild high-intent legacy content hubs or targeted pages | SEO + Content | L | Organic entry growth on non-brand terms |
| Conversion experiment program | Increases lead efficiency over time | A/B test CTA copy/layout, form friction reduction, trust modules | CRO + Dev | M-L | Higher form completion and call conversion rate |
| Schema and SERP enhancement | Improves rich-result eligibility and CTR | Expand structured data coverage and validate in rich result tools | SEO + Dev | M | Higher rich-result presence on target queries |

## K. Launch Gate Checklist
Do-not-launch blockers:
1. Any legacy URL without a tested one-hop redirect outcome.
2. Any failed production form submission path (`contact` / `get-started`).
3. Any critical route returning non-200 unexpectedly after deploy.

Final pre-launch QA checklist:
- Redirect map finalized and deployed.
- Primary routes return expected status and canonical tags.
- Forms pass success/failure tests.
- `robots.txt` and `sitemap.xml` are live and correct.
- Utility pages that should not index are marked noindex.
- HTTPS and host canonicalization behave exactly as intended.

Post-launch monitoring checklist:
- Day 1: crawl + Search Console + conversion sanity checks.
- Week 1: daily 404 and indexing checks.
- Week 2-4: weekly ranking/session/conversion deltas vs baseline.
- Month 2-3: CWV tuning, internal-link improvements, content recovery priorities.
