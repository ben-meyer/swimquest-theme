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
- [x] **Hide "Dates and Booking" section + top button when no dates exist** — `TripDates` self-hides via `validate()` when `dates` is empty. `TripPageHeader` CTA and `TripSectionNav` primary action now branch on the no-dates case (see "Coming soon" item below) instead of rendering a broken `#trip-dates` anchor.
  Closes: [Remove booking dates → remove section](https://trello.com/c/oRzd94aY)
- [x] **Single-date formatting** — when start == end show "8 Feb" not "8-8 Feb"; fix in the date range formatter in `TripData` / `TripDates`
  Closes: [Sole date display](https://trello.com/c/O4UTKFOw)
- [x] **"Coming soon" CTA when no dates/booking link** — Added trip-level `trip_enquiry_url` ACF field as the no-dates fallback. `TripData::getPrimaryEnquiryAction()` now consults this when no row-level enquiry URL exists; `TripData::getPrimaryBookingAction()` returns null (or a non-link "Coming Soon" label when no enquiry URL is set anywhere) when `dates` is empty. Page header + Trip Nav render the resulting CTA accordingly. Listings/calendar already exclude no-dates trips via `getUpcomingPostIds()`.
  Closes: [Coming soon button](https://trello.com/c/flpZnz2X)
- [x] **Temperature: single number → no dash** — `formatRange()` guard, or specific handling in stats rendering. Currently `28 - 0` style appears.
  Closes: [Temperature field 1 number](https://trello.com/c/N4GT87Kp)
- [x] **Investigate "not pulling in all events"** — `Theme/Modules/Events/module.php:38-59` filters archive to upcoming only via `filterArchiveToUpcomingEvents()`. Confirm with client whether past/no-date events should be hidden or shown.
  Closes: [Events archive not pulling all events](https://trello.com/c/lkaMw1lX) — *❓ confirm intent with client* We've asked client what they want to do - we order by date as per Calendar.

---

## 🅲 Quick wins (obvious from code)

- [x] **Destinations archive: hide empty terms** — `Theme/Controllers/DestinationsController.php`, `hide_empty => false` → `true`. Country taxonomy is registered for both `trip` and `events` post types, so empty = no trips AND no events assigned.
  Closes: [Destinations: hide if no trips assigned](https://trello.com/c/y07tMdej)
- [x] **Related Trips: allow Events** — Added `"events"` (plural, the actual slug per `Theme/Modules/Events/PostType.php`) to `post_type` array on `field_trip_related_trips` in `acf-json/group_trip_fields.json`; instructions updated. `TripCards`/`TripCard` render path has no post-type assumptions so events render via the same card component.
  Closes: [Related trips include events](https://trello.com/c/RoUDb8gN)
- [x] **Intro paragraph → rich text** — `intro_lead` and `intro_body` switched from `textarea` to `wysiwyg` (basic_formatting toolbar, no media upload). `trip-intro/template.php` wrappers changed from `<p>` to `<div>` to avoid nested-paragraph invalidity. Margin reset on nested `<p>` in `styles.pcss` so wysiwyg-generated paragraphs don't introduce stray gaps. Existing plain-text DB content renders fine — ACF's wysiwyg `get_field()` runs `wpautop()` so no migration needed.
  Closes: [Link in intro paragraphs](https://trello.com/c/31vZISBT)
- [x] **Reorder trip styles on homepage** — Installed `simple-custom-post-order` plugin via composer. Theme query is plugin-agnostic (no `orderby` passed). Client activates plugin → Settings → SCPOrder → tick "Trip Styles" only → drag terms into desired order.
  Closes: [Reorder trip styles](https://trello.com/c/PT7TDvdY)
- [ ] **About Us page hierarchy** — Re-parent About Us out from under Events in WP page tree; add Guides as child of About Us (`wp post list --post_type=page` to confirm current state)
  Closes: [About Us nested under events](https://trello.com/c/aacVWlua)
- [x] **Editor role: nav-menus access (quicklinks)** — `Gust/WordPress/Admin.php` top-level Menus item cap `manage_options` → `edit_theme_options`; `Theme/Modules/Core/Menus.php` grants editors `edit_theme_options`, hides Appearance sidebar item, redirects direct URLs (themes/customize/widgets/theme-editor) and removes the admin-bar Customize node for non-admins.
  Closes: editor quicklinks/menu access (Trello card)

---

## 🅳 Component / styling work

- [ ] **Homepage horizontal scroll (mobile)** — needs Chrome DevTools investigation. Likely culprit: a flex/grid child with `min-width: auto` (see memory: `css_grid_flex_min_width_auto`). Check hero, marquees, carousels.
  Closes: [Homepage horizontal scroll](https://trello.com/c/oElP7Xxr)
- [x] **Guest story design — top image** — `singular.php:22-37` routes stories to default page-header + the_content. Build `components/story-single/` (mirror trip approach) with a designed hero strip rather than full-bleed image. We Added a boundary
  Closes: [Guest story design](https://trello.com/c/k1CMWWxC) — *❓ confirm desired layout*
- [x] **Team page role subtitle** — Added `role` ACF text field on the `guide` post type (`Theme/Modules/Guides/acf-json/group_guide_role.json`, mirrors the story `contributor_name` pattern). New `guide` branch in `Card::transform()` populates `content.meta` from the role field, rendered via the existing `.g-card__meta` markup. Team page Cards block (set to selected guides) will show each guide's role beneath their name.
  Closes: [HQ team role subtitle](https://trello.com/c/VBikBKso)
- [x] **Form styling** — CF7. Added `assets/styles/3-patterns/_contact-form-7.pcss` base pattern: brand-styled checkboxes/radios (appearance:none + custom box), inline-grid centring (covers both CF7 markup variants — with/without `<label>` wrapper), explicit `transform: none` to defeat the base `translateY(0.15em)` baseline nudge that breaks custom-styled boxes. Inherited by every CF7 form on the site.
  Closes: [Form styling](https://trello.com/c/h4RADrOJ)
- [x] **Itinerary day heading override** — `preview_days` repeater already has a `title` subfield (`components/trip-itinerary-preview/TripItineraryPreview.php:17`). Verify it's being rendered in place of "Day N" when populated; if not, update template to prefer custom title over `sprintf('Day %d')`.
  Closes: [Override "Day 1" headings](https://trello.com/c/qSLTcfWC) - Client archived this themselves
- [x] **Getting There: general note field** — Added `general_note` (wysiwyg, basic_formatting toolbar, no media upload) as a sub-field of the `getting_there_stages` repeater. Each stage can carry its own note; renders inside the stage's bordered box, beneath the trip finish time, styled with `type-small` to match the rest of the block. Supports links and basic inline formatting.
  Closes: [General note on Getting There](https://trello.com/c/xZEJ4e9H)

---

## 🅴 Needs client clarification (ask before building)

- [ ] **Mailchimp signup** — no newsletter component exists. Footer has `footer_form` shortcode field.
  Closes: [Mailchimp email signups](https://trello.com/c/z3LA3Rba)
  *❓ Use MC4WP plugin + shortcode in footer, or build native component posting to Mailchimp API?*
- [x] **Feefo placement** — Reverted commit `0e54ad6` ("remove button from testimonial cards") to restore the ACF `button` field on `testimonial-cards` and its template render. Client enters the Feefo URL into the button field per card.
  Closes: [Feefo review placement](https://trello.com/c/NZRPO0gp)
- [x] **Partnerships page** — Custom-content Cards block was hiding the read-more button when the link's title field was empty, which only happened for external URLs (WP's link picker auto-populates the title for internal posts). Fixed `Card.php` to fall back to the parent Cards block's default text ("Read More"), propagate the link target so "open in new tab" reaches both the button and the heading link, and stamp the `btn--theme-2` (yellow) variant on every custom-card button so internal and external links look consistent.
  Closes: [External links yellow block](https://trello.com/c/k5s5m5IY), [Contact block on partnerships](https://trello.com/c/x13z7KHj), [Partnerships URL](https://trello.com/c/b9PmYtio)
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
