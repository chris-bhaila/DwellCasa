---
name: cursor-pointer on all clickable elements
description: Always add cursor-pointer to every clickable element (buttons, links, interactive spans, etc.)
type: feedback
---

Always add `cursor-pointer` to every clickable element — buttons, anchor tags, interactive spans, and any element with a click handler.

**Why:** User explicitly called this out as a consistent requirement across the UI.

**How to apply:** Any time you write or edit a button, <a>, or element with an event listener that's interactive, include `cursor-pointer` in its Tailwind classes.
