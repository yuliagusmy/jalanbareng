---
name: awwwards-motion-study
description: Use when building a site whose reference has animation — before writing any motion code, and again when verifying a build. Teaches what to record from a live site (and what to skip), how to study the recordings frame-by-frame, how to turn the study into a motion inventory, and how to verify the build's motion against the reference. Complements awwwards-inspiration (step 6) with the full capture→review→build procedure.
---

# Awwwards Motion Study — capture, review, build

Motion is design: an award-site hero that "looks the same" in a screenshot
can be a spinning 3D ring, a scrubbed pin, or a staggered reveal. Static
captures hide it; element posters (single frames) lie about it. This skill
covers the whole chain: **record → review → inventory → build → re-record**.

The one rule everything serves: **never write animation code from memory or
posters — every shipped animation must trace to frames you actually studied.**

## 1. What to capture

Record a **motion-through pass**: load, dwell, slow scroll, hover tour.
Three animation classes must all be on camera:

| Class | How it gets captured | Typical duration |
|---|---|---|
| Preloader / entrance | initial dwell after `load` | 5–8 s |
| Scroll-triggered + pinned/scrubbed | slow stepped scroll (~450 px / ~600 ms) | length of page |
| Hover / click / micro-interactions | virtual cursor visits interactive elements and dwells | 10–20 s |

**Capture these** — they are design:
- Preloader/entrance sequences and hero reveals
- Scroll-triggered reveals, parallax, horizontal pins, scrubbed timelines
- Hover states (cards, nav, buttons, image rollovers) and click effects
- Page transitions if the site has them, sticky/nav behaviors on scroll
- Marquees, tickers, counters — anything time-driven

**Skip these**:
- Content videos (stock footage, film background)
- Loading states (wait until resolved)
- Cookie banners / newsletter modals

## 2. Capture rules that prevent re-shoots

- **Capture BEFORE building** — the doctrine step. Review first, code second.
- Use a **consistent viewport** (1440×900 for desktop).
- For phone-class references pass `record_site_motion` a `viewport: "mobile"`
  (390×844 @3x with isMobile + hasTouch).
- **Pre-scroll** to fire lazy content, scroll back to top, then record.
- **Fixed Container Heights**: When building animations (like typewriters, tickers),
  always lock container height to prevent layout shifts.
