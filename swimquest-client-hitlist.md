# SwimQuest — Client Hit List

30 tasks (23 Not Started, 7 In Progress) grouped by bundle. Cards linked to Trello.

---

## 🅰️ Bundle: Port missing sections from Trip → Event

The Event single template is missing 4 sections that exist on Trip: **TripIntro**, **Intro Gallery**, **Mid Gallery**, **Promo**. Plus the EventSingle class doesn't fetch the gallery ACF fields. Closing this bundle resolves ~6 cards.

Reference: `components/trip-single/template.php` (full) vs `components/event-single/template.php` (missing 4 lines). `EventSingle::transform()` doesn't fetch `intro_gallery` / `mid_gallery`.

- [ ] **Add Promo block to event template** — `components/event-single/template.php`, after `TripDates`, mirror trip line 26
  Closes: [Promo Block isn't appearing under dates on Scilly Swim Challenge](https://trello.com/c/4XaMY2Dv), [Promo not appearing on Events template](https://trello.com/c/03qytWDk)
- [ ] **Add TripIntro + Intro Gallery + Mid Gallery to event template** — also fetch `intro_gallery` / `mid_gallery` in `EventSingle::transform()` (mirror `TripSingle.php:26-37`)
  Closes: [Intro&Galleries not appearing in Events](https://trello.com/c/fBhYcjuJ), [Elements not pulling through on King of the Thames](https://trello.com/c/0n2yvYQI)
- [ ] **Audit Event template against Trip in full** — confirm all 17 sections in trip render or are deliberately omitted; document the omissions

---

## 🅱️ Bundle: Dates & Booking behavior (Events)

All in `components/trip-dates/` (used by both Trip and Event) and `TripPageHeader`. Conditional rendering changes.

- [ ] **Hide empty stat rows on event header** — `components/trip-page-header/TripPageHeader.php:78-111` already has `formatRange()` / `array_filter()`; verify they're applied in the events flow and that the title row doesn't render with no value
  Closes: [Leave stats blank → don't show](https://trello.com/c/LSX8Dhkc)
- [ ] **Hide "Dates and Booking" section + top button when no dates exist** — guard in event template + page header
  Closes: [Remove booking dates → remove section](https://trello.com/c/oRzd94aY)
- [ ] **Single-date formatting** — when start == end show "8 Feb" not "8-8 Feb"; fix in the date range formatter in `TripData` / `TripDates`
  Closes: [Sole date display](https://trello.com/c/O4UTKFOw)
- [ ] **"Coming soon" CTA when no dates/booking link** — add ACF option (form URL) on event; render fallback button when dates array is empty
  Closes: [Coming soon button](https://trello.com/c/flpZnz2X) — *needs form URL from client*
- [ ] **Temperature: single number → no dash** — `formatRange()` guard, or specific handling in stats rendering. Currently `28 - 0` style appears.
  Closes: [Temperature field 1 number](https://trello.com/c/N4GT87Kp)
- [ ] **Investigate "not pulling in all events"** — `Theme/Modules/Events/module.php:38-59` filters archive to upcoming only via `filterArchiveToUpcomingEvents()`. Confirm with client whether past/no-date events should be hidden or shown.
  Closes: [Events archive not pulling all events](https://trello.com/c/lkaMw1lX) — *❓ confirm intent with client*

---

## 🅲 Quick wins (obvious from code)

- [ ] **Destinations archive: hide empty terms** — `Theme/Controllers/DestinationsController.php:12-18`, `hide_empty => false` → `true`
  Closes: [Destinations: hide if no trips assigned](https://trello.com/c/y07tMdej)
- [ ] **Related Trips: allow Events** — `acf-json/group_trip_fields.json` `field_trip_related_trips`, add `"event"` to `post_type` array
  Closes: [Related trips include events](https://trello.com/c/RoUDb8gN)
- [ ] **Intro paragraph → rich text** — `acf-json/group_trip_fields.json` `intro_body`: `textarea` → `wysiwyg`; render with `wp_kses_post` (template change required)
  Closes: [Link in intro paragraphs](https://trello.com/c/31vZISBT)
- [ ] **Reorder trip styles on homepage** — `Theme/Controllers/TripStylesController.php:12-16` uses `menu_order` (admin-set). Set the term menu_order in WP admin to: Relax & Explore / Technique / Challenge / Short Swims / Family. (Code change only if menu_order isn't being respected.)
  Closes: [Reorder trip styles](https://trello.com/c/PT7TDvdY)
- [ ] **About Us page hierarchy** — Re-parent About Us out from under Events in WP page tree; add Guides as child of About Us (`wp post list --post_type=page` to confirm current state)
  Closes: [About Us nested under events](https://trello.com/c/aacVWlua)

---

## 🅳 Component / styling work

- [ ] **Homepage horizontal scroll (mobile)** — needs Chrome DevTools investigation. Likely culprit: a flex/grid child with `min-width: auto` (see memory: `css_grid_flex_min_width_auto`). Check hero, marquees, carousels.
  Closes: [Homepage horizontal scroll](https://trello.com/c/oElP7Xxr)
- [ ] **Guest story design — top image** — `singular.php:22-37` routes stories to default page-header + the_content. Build `components/story-single/` (mirror trip approach) with a designed hero strip rather than full-bleed image.
  Closes: [Guest story design](https://trello.com/c/k1CMWWxC) — *❓ confirm desired layout*
- [ ] **Team page role subtitle** — `components/text-items/template.php` already has `meta` field rendered above title. Either expose `meta` for team items or add a dedicated `role` subfield in the team component.
  Closes: [HQ team role subtitle](https://trello.com/c/VBikBKso) — *❓ confirm: small-caps light-blue style under name?*
- [ ] **Form styling** — Turkey family booking form, restyle to match brand
  Closes: [Form styling](https://trello.com/c/h4RADrOJ) — *❓ which form plugin? CF7 / Gravity / Fluent?*
- [ ] **Itinerary day heading override** — `preview_days` repeater already has a `title` subfield (`components/trip-itinerary-preview/TripItineraryPreview.php:17`). Verify it's being rendered in place of "Day N" when populated; if not, update template to prefer custom title over `sprintf('Day %d')`.
  Closes: [Override "Day 1" headings](https://trello.com/c/qSLTcfWC)
- [ ] **Getting There: general note field** — add a top-level textarea/wysiwyg to the Getting There ACF group, render in `TripGettingThere`
  Closes: [General note on Getting There](https://trello.com/c/xZEJ4e9H) — *❓ confirm with Alice: above dropdowns, below, or per-dropdown?*

---

## 🅴 Needs client clarification (ask before building)

- [ ] **Mailchimp signup** — no newsletter component exists. Footer has `footer_form` shortcode field.
  Closes: [Mailchimp email signups](https://trello.com/c/z3LA3Rba)
  *❓ Use MC4WP plugin + shortcode in footer, or build native component posting to Mailchimp API?*
- [ ] **Feefo placement** — no Feefo integration in code yet.
  Closes: [Feefo review placement](https://trello.com/c/NZRPO0gp)
  *❓ Inline under review block, floating widget, or side button?* - Solution: Add button link back into testimonial cards, it was there before.
- [ ] **Partnerships page** — no partnerships component / page template in codebase yet.
  Closes: [External links yellow block](https://trello.com/c/k5s5m5IY), [Contact block on partnerships](https://trello.com/c/x13z7KHj), [Partnerships URL](https://trello.com/c/b9PmYtio)
  *❓ Is the page being built from existing blocks (and needs styling fixes), or does it need a custom template?* 
- [ ] **Screenshot 2026-06-18 at 11.48.34.png** — card has no text, just an image
  Closes: [Screenshot card](https://trello.com/c/XWFTOjin) — *❓ what's the issue here?* Fix yellow buttons for this card style
- [ ] **CMS Training guide** — internal/admin documentation task
  Closes: [CMS Training guide](https://trello.com/c/nnlz4WHa) — *❓ scope and format?*

---

## Summary

- **Bundle A (Trip→Event parity)**: 3 tasks → closes 4 cards
- **Bundle B (Dates & Booking)**: 6 tasks → closes 6 cards
- **Quick wins**: 5 tasks → closes 5 cards
- **Component/styling**: 6 tasks → closes 6 cards
- **Needs clarification**: 6 tasks → closes 9 cards

**Suggested order**: Bundle A → Bundle B → Quick wins → clarification round → Component/styling.
