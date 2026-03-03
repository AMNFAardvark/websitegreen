# Screaming Frog Preview Crawl Notes

Date: 2026-03-03
Preview URL tested for this audit cycle: https://familyvest-site-8mm8crchq-amnfaardvarks-projects.vercel.app/

Export set included in this folder:
- `internal_all.csv`
- `internal_html.csv`
- `response_codes_all.csv`
- `canonicals_all.csv`
- `directives_all.csv`
- `h1_all.csv`
- `h2_all.csv`
- `page_titles_all.csv`
- `meta_description_all.csv`
- `images_all.csv`
- `sitemaps_all.csv`
- `crawl_overview.csv`
- `issues_overview_report.csv`

Observed behavior:
- Preview crawl signals include `X-Robots-Tag: noindex` and `Canonicalised,noindex` behavior.
- This is common for preview/staging environments and is not treated as production indexation intent.

How this audit handled it:
- Used these exports as evidence for preview header/indexability behavior.
- Used code-level inventory + direct route status checks for full 19-route launch validation.
