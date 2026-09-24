---
name: awwwards-inspiration
description: Use when building a site that needs design references (e.g. "make it feel premium", "dark 3D portfolio vibes"), when the user asks for standalone inspiration research ("show me award-winning e-commerce sites", "what's trending in brutalism"), or when setting up or maintaining the awwwards-mcp local index or live-capture tooling (awwwards-index, capture_live_site).
---

# Awwwards Inspiration

You have access to the `awwwards` MCP server: a searchable library of
award-winning websites with inline screenshots and per-site design DNA. Use
it to ground design decisions in real, proven references instead of guessing.

## When to use this skill

1. **During site builds** — before writing any UI code, gather references and
   state a design direction.
2. **Standalone research** — the user wants inspiration, trends, or examples
   ("show me dark 3D portfolio sites").
3. **Index & capture ops** — building the local search index, or taking fresh
   screenshots of live URLs.

## The inspiration loop

Run this loop before building anything visual:

1. **Restate the goal as concrete attributes.** Turn the user's request into
   mood, color, technology, and industry terms. "Make it feel premium" becomes
   e.g. "dark, elegant, WebGL, agency portfolio".
2. **Ground your vocabulary.** If unsure which filters exist, call
   `list_categories` first — it returns every color hex and tag/technology
   slug you can search by.
3. **Search.** Call `search_sites` with 1–3 filters (e.g.
   `{ color: "#404040", tags: ["3d", "portfolio"] }`). Judge the results from
   the inline screenshots, not just titles. Shortlist 2–3 candidates.
   Free-text queries are porter-stem + prefix-matched and BM25-ranked
   (`"magazines"` finds Magazine-tagged sites, best matches first); multi-word
   queries keep AND semantics — both tokens must hit the same site.
4. **Live reference URL named? Capture it full-page first.** If the user
   points at a specific live site (e.g. "recreate cerebrium.ai"), call
   `capture_live_site` on that URL before anything else and design from the
   full-page PNG — every section, top to bottom. For mobile-excellence
   references pass `viewport: "mobile"` — and capture BOTH viewports when the
   desktop and mobile designs diverge.
5. **Get the design DNA.** Call `get_site_details` on the top pick for its
   palette, technologies, design elements, awards, and description.
6. **Study MOTION before building.** Call `get_site_elements` on shortlisted
   sites — it returns per-element video URLs. Study the motion arcs (easing,
   overlap, entrance order) before writing animation code.
7. **State the design direction before writing code.** In prose: palette
   (hexes from references), type mood, layout patterns, and page architecture.
8. **Verify structure, then polish.** After building, capture your build
   full-page and compare section by section against the reference.
