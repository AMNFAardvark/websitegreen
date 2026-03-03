# QA Staging Access (Vercel)

Deployment protection was removed for this audit cycle, and the preview is accessible.

Current validated preview URL:
- https://familyvest-site-8mm8crchq-amnfaardvarks-projects.vercel.app/

## Validation command

```bash
curl -I https://familyvest-site-8mm8crchq-amnfaardvarks-projects.vercel.app/
```

Expected:
- `HTTP/2 200`
- header includes `x-robots-tag: noindex` (expected for preview environments)

## Notes
- Do not use preview URL indexability as production intent.
- Re-validate headers/crawl behavior on the production domain immediately after InMotion cutover.
