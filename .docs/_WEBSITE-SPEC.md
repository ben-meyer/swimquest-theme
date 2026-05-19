# Website Specification

## Overview

**Title**: SwimQuest Swimming Holidays
**Live URL**: https://swimquest.uk.com
**Staging URL**: https://swimquest-4btr8.projectbeta.co.uk/
**PHP Version**: 8.3+

<!-- Brief description of the website's purpose and target audience -->
SwimQuest provide quality, friendly, safe swimming holidays for people of all ages and abilities. The target audience
is women in their early 60s, recently retired, solo travellers and swimming enthusiasts from beginner to ex-olympian 
standard.

---

## Required Plugins

<!-- All plugins auto-update. Add/remove as needed. -->

- **ACF Pro** - Custom fields and Gutenberg blocks
- **Extended CPTs** - Custom post type and taxonomy registration helpers
- **Yoast SEO** - SEO management
- **Gravity Forms** - Enquiry and contact forms

---

## Content Types

<!--
  Each content type includes:
  - Basic config (URL, dashicon, supports, taxonomies)
  - Archive routing (if applicable)
  - Custom fields (if any)

  Format:
  ### slug
  Description.

  - URL: /url-structure/%postname%/
  - Dashicon: dashicons-xxx
  - Supports: title, editor, thumbnail, excerpt, etc.
  - Taxonomies: category, post_tag, custom-tax

  **Archive** (/archive-url/)
  - Template: Listing
  - Route: decorate:post_type:slug

  **Fields:**
  - **field_name** (type) - Description
-->

### page
Static pages.

- URL: /%pagename%/
- Dashicon: dashicons-admin-page
- Supports: WordPress built-in defaults (post type is not re-registered)
- Taxonomies: none
- Editor template: pre-inserts `acf/page-header` block (locked)

---

### trip
Core swim holiday product. Each post represents a trip that may run on multiple departure dates. No standard archive — trips are accessed via Destinations, Calendar, and Trip Style taxonomy archives.

- URL: /trip/%postname%/
- Dashicon: dashicons-palmtree
- Supports: title, excerpt, thumbnail, revisions, custom-fields, slug
- Taxonomies: trip_style, skill_level, swim_type, country, location
- Editor: Gutenberg disabled — all content is managed via ACF fields and locked template sections

**Header fields (post-level):**
- **trip_heading** (text) - Optional title override for the trip page header
- **trip_description** (textarea) - Hero summary paragraph shown in the page header
- **trip_header_image** (image, return: array) - Optional hero image override

**Fields:**
- **dates** (repeater) - One or more date windows; UI shows date range if single, "Multiple dates" if more than one
  - **start_date** (date_picker, return: Y-m-d)
  - **end_date** (date_picker, return: Y-m-d)
  - **price** (number, min: 0, step: 0.01) - Price for this departure in GBP
  - **status** (select) - Availability: bookable, sold_out
  - **booking_url** (url) - Booking link shown when status is bookable
  - **enquiry_url** (url) - Enquiry link for that departure
- **itinerary** (post_object, post_type: itinerary, allow_null: true) - Linked reusable itinerary
- **accommodation** (post_object, post_type: accommodation, allow_null: true) - Linked accommodation post
- **intro_lead** (textarea) - Large intro paragraph shown above the highlights
- **intro_body** (textarea) - Standard intro paragraph shown below the lead
- **intro_gallery** (gallery) - Gallery shown above the highlights, after the intro paragraphs
- **mid_gallery** (gallery) - Gallery shown below the itinerary preview block
- **highlights** (repeater, max: 4) - Highlight cards used in the locked Highlights section
  - **image** (image, return: array)
  - **heading** (text)
  - **description** (textarea)
- **included_items** (repeater) - Single-line items for "What's included"
  - **label** (text)
- **not_included_items** (repeater) - Single-line items for "Not included"
  - **label** (text)
- **getting_there_stages** (repeater) - Structured travel stages
  - **title** (text)
  - **start_time** (text) - Optional departure time displayed at the top of the stage
  - **finish_time** (text) - Optional arrival time displayed at the end of the stage
  - **steps** (repeater)
    - **icon** (select: plane, ferry, car, bus, train, walk) — `walk` is handled in the template but missing from the ACF field choices
    - **title** (text)
    - **description** (wysiwyg)
- **reviews_embed_code** (textarea) - Raw embed code for the Reviews section
- **related_stories** (relationship, post_type: story, max: 2) - Manual story selection
- **related_trips** (relationship, post_type: trip, max: 3) - Manual trip selection
- **faqs** (repeater) - FAQ section rendered via the Accordion component
  - **question** (text)
  - **answer** (wysiwyg)
- **promo_title** (text) - Heading for the Promo block rendered between Dates and Get In Touch
- **promo_subheading** (text) - Promo block subheading
- **promo_link** (link) - Promo block CTA

**Single Template**
- Template: Hard-coded PHP single via `TripSingle` orchestrator component
- Route: single:trip
- Editor model: classic admin screen with Gutenberg disabled and no main content editor
- Layout order: Trip Page Header, Section Nav, **Trip Intro**, **Gallery (intro)**, Highlights, Itinerary, **Gallery (mid)**, Accommodation, What's Included, Getting There, Reviews, FAQs, Dates & Book, Promo, Get In Touch, Related Stories, Related Trips
- Render rule: any section with no content must not render on the front end, and its jump link must also be hidden

---

### accommodation
Accommodation pages linked from trips.

- URL: /accommodation/%postname%/
- Dashicon: dashicons-building
- Supports: title, editor, thumbnail, revisions, custom-fields
- Taxonomies: none

**Fields:**
- **star_rating** (number, min: 1, max: 5) - Summary rating used on trip teaser and accommodation single
- **tags** (repeater) - Short facility labels
  - **label** (text)
- **description** (wysiwyg) - Accommodation summary copy
- **summary_gallery** (gallery, min: 1, max: 3) - Square-cropped gallery used in the trip teaser
- **rooms_intro** (textarea) - Intro copy above the "View accommodation" CTA

---

### events
Swimming events. Mirrors the trip post type in structure and purpose — events may also have multiple departures. The distinction is editorial/content rather than structural.

- Slug: events (plural)
- URL: /events/%postname%/
- Dashicon: dashicons-calendar-alt
- Supports: title, editor, excerpt, thumbnail, revisions, custom-fields
- Taxonomies: trip_style, skill_level, swim_type, country, location

**Archive** (/events/)
- Template: Listing
- Route: decorate:post_type:events

**Fields:**
- **dates** (repeater) - One or more date windows
  - **start_date** (date_picker, return: Y-m-d)
  - **end_date** (date_picker, return: Y-m-d)
  - **price** (number, min: 0, step: 0.01) - Price in GBP
  - **status** (select) - Availability: bookable, sold_out, sold_out_private
  - **booking_url** (url) - Booking link shown when status is bookable
- Guides are standalone pages linked editorially — no ACF relationship field on events

---

### itinerary
Reusable day-by-day itinerary documents. Assigned to trips via post_object field. One-to-one relationship with trip in practice, but designed to be reused across multiple trips with the same route. Publicly accessible and intended to be print-friendly — print stylesheet is a future requirement, not needed for scaffolding.

- URL: /itinerary/%postname%/
- Dashicon: dashicons-list-view
- Supports: title, editor, thumbnail, revisions, custom-fields
- Taxonomies: none

**Fields:**
- **preview_days** (repeater, max: 3) - First-three-days teaser used by Trip Itinerary Preview
  - **title** (text, required)
  - **summary** (textarea, required)

Full day-by-day content is authored via the Gutenberg editor. Target model: locked structured day blocks, with day numbering derived from order and optional standalone gallery blocks inserted between day sections — not yet implemented.

---

### story
Guest stories and reviews. Used in the Stories & Reviews section under About Us.

- URL: /stories/%postname%/
- Dashicon: dashicons-format-quote
- Supports: title, editor, thumbnail, excerpt, revisions, custom-fields
- Taxonomies: none

**Fields:**
- **contributor_name** (text) - Byline name shown on story cards and singles. Leave blank to hide byline.

**Archive** (/stories/)
- Template: Listing
- Route: decorate:post_type:story

---

### guide
Guide and coach biographies. Used in "Meet our team" section under About Us.

- URL: /guide/%postname%/
- Dashicon: dashicons-id-alt
- Supports: title, editor, thumbnail, revisions, custom-fields
- Taxonomies: none

**Fields:**
- **role** (text) - e.g. "Head Guide", "Swimming Coach" — **NOT YET IMPLEMENTED** (no ACF group exists for guide)
- Full biography authored via Gutenberg editor

---

## Taxonomies

<!--
  Each taxonomy includes:
  - Basic config (post types, hierarchical)
  - Archive routing (if applicable)
-->

### trip_style
Trip style categories used in primary navigation. 

- Post types: trip, events
- Hierarchical: no
- Rewrite slug: trip-styles
- Terms:
  - Short Swims & Dips
  - Relax & Explore
  - Technique & Improvement
  - Challenge & Distance
  - Groups & Bespoke
  - Family

**Archive** (/trip-styles/%slug%/) — one archive per term, e.g. /trip-styles/family/, /trip-styles/short-swims-dips/. WordPress generates these automatically from taxonomy registration; the single route below handles all terms with the same template.
- Template: Listing
- Route: decorate:taxonomy:trip_style

---

### skill_level
Swimmer ability level. Multi-select — a trip can suit more than one level.

- Post types: trip, events
- Hierarchical: no
- Public: false (no REST API exposure, no front-end archives)
- Terms: Dipper, Beginner, Intermediate, Advanced, Challenger, All Abilities

**Term fields (admin only):**
- **subheading** (textarea)
- **image** (image, return: array)

No archive (used as a filter only).

---

### country
Country-level taxonomy. Flat — country names only. Labeled "Destinations" in navigation and archive pages; slug `country` is the internal term.

- Post types: trip, events
- Hierarchical: no
- Rewrite slug: destinations

**Term fields:**
- **subheading** (textarea) - Shown on the archive page header
- **image** (image, return: array) - Shown on the archive page header

**Archive** (/destinations/%slug%/) — per-country listing
- Template: Listing
- Route: decorate:taxonomy:country

---

### location
City or location name. Used alongside `country` to form the display location string (e.g. "Mathraki, Greece").

- Post types: trip, events
- Hierarchical: no
- Rewrite slug: locations

**Term fields:**
- **subheading** (textarea) - Shown on the archive page header
- **image** (image, return: array) - Shown on the archive page header

**Archive** (/locations/%slug%/) — per-location listing
- Template: Listing
- Route: decorate:taxonomy:location

---

### swim_type
Type of swimming environment. Used to filter trips and events by the water context.

- Post types: trip, events
- Hierarchical: no
- Rewrite slug: swim-type
- Terms: Sea, Pool, Open Water, Lake, River

Registered as public with rewrite enabled — WordPress generates archives at `/swim-type/%slug%/` but these are not linked from the front end. Used primarily as a filter.

---

## Standalone Routes

<!--
  Pages and routes not tied to content type archives.
-->

### Homepage (/)
- Template: Default

### Events Archive (/events/)
Decorated archive listing all upcoming events.
- Template: Listing
- Route: decorate:post_type:events

### Search Results (/search/)
- Template: Default
- Route: decorate:search

### 404
- Template: Default
- Route: decorate:404

### Destinations (/destinations/)
Owned route scaffolded for a destination index page. Controller currently returns an empty string; intended output is an alphabetical index of country terms linking to /destinations/%slug%/.
- Template: Default
- Route: owned

### Trip Styles (/trip-styles/)
Overview/wayfinding page linking into all Trip Style taxonomy archives. Not a taxonomy archive itself — a static editorial page.
- Template: Default
- Route: owned

### Calendar (/calendar/)
Chronological listing of all upcoming trip departures, grouped by month. Shows one calendar entry per departure date for trips with multiple departures.

**Display Logic:**
- Lists all trips with at least one upcoming departure (start_date >= today)
- Shows multiple entries per trip when multiple departures are available
- Groups entries chronologically by month/year
- De-lists individual departures once their start_date has passed
- Completely removes trips once all departure start_dates are in the past

**Example:** Trip with departures April 4-11, April 27-May 4, April 29-May 6:
- Before April 4: Shows 3 entries
- April 12-26: Shows 2 entries (April 27 & April 29 departures)
- May 5-28: Shows 1 entry (April 29 departure only)
- After May 6: Trip completely removed

- Template: Default
- Route: owned

---

## Site Settings

<!--
  Global settings stored in ACF options pages.

  Format:
  - **field_name** (type) - Description
  - **field_name** (type, option: value) - With options
  - **field_name** (group)
    - **nested_field** (type)

  Common types: text, textarea, wysiwyg, image, file, gallery, select,
  checkbox, radio, true_false, link, page_link, post_object, relationship,
  taxonomy, user, google_map, date_picker, color_picker, group, repeater
-->

Logo is a static SVG file (`logo-alt.svg`) rendered via `Gust\Image::get()` — not managed via ACF.

### General (acf-options-general)

- **social_networks** (repeater)
  - **network** (select: facebook, twitter, youtube, instagram, linkedin, tiktok)
  - **url** (url)
- **site_email_sender_name** (text) - Sender name for system emails
- **site_email_address** (email) - Sender address for system emails
- **google_api_key** (password) - Google Maps / Places API key

### Get In Touch (acf-options-general — separate ACF group `group_get-in-touch`)

- **get_in_touch_contacts** (repeater) - Contact items shown in the "Get in touch" section on trip pages
  - **icon** (select: whatsapp, email, phone)
  - **label** (text)
  - **value** (text)
  - **url** (text)

### Analytics (acf-options-general — separate ACF group `group_analytics`)

- **gtm_enabled** (true_false, default: 0) - Enable Google Tag Manager
- **gtm_code** (text) - GTM container ID (e.g. `GTM-XXXXXXX`). Conditionally shown when `gtm_enabled` is true
- **analytics_privacy_first** (true_false, default: 1) - Disables Google Signals and ad personalisation. Conditionally shown when `gtm_enabled` is true
- **head_scripts** (repeater) - Scripts to inject into `<head>`. Each row can optionally require cookie consent
  - **head_script_html** (textarea) - Full `<script>` tag or inline HTML
  - **head_script_requires_consent** (true_false, default: 1) - Block execution until cookies accepted
- **body_scripts** (repeater) - Scripts to inject after `<body>` open. Same sub-fields as head_scripts

### Cookie Consent (acf-options-general — separate ACF group `group_cookie-consent`)

- **cookie_consent_enabled** (true_false, default: 1) - Show the cookie consent banner
- **cookie_consent_text** (wysiwyg, default: "We use cookies...") - Banner message text. Conditionally shown when `cookie_consent_enabled` is true

### Header (acf-options-header)

- **header_call_to_action_1** (link) - Primary CTA button in the site header

### Footer (acf-options-footer)

- **featured_in_heading** (text, default: "Featured in") - Heading for the "Featured in" media-logo strip at the top of the footer
- **featured_in_logos** (repeater) - Logos for the "Featured in" strip
  - **image** (image, return: array)
  - **link** (link)
- **footer_text_top** (wysiwyg) - Footer text above the copyright line
- **footer_text_bottom** (wysiwyg) - Footer copyright text (default: "Company Name © [year]")
- **footer_form** (text) - Gravity Forms shortcode
- **footer_images** (repeater) - Logo/partner image row in the footer
  - **image** (image, return: array)
  - **link** (link)

---

## Menus

<!--
  Format:
  - **menu_location** - Description of where it appears
-->

- **header** - Main navigation in site header
- **footer-1** - Primary footer navigation links
- **footer-2** - Secondary footer navigation links

---

## Components

<!--
  Each component includes:
  - Name and block status
  - Description
  - Full ACF field group definition

  Block status: [Block] = ACF Gutenberg block, [Partial] = PHP partial only
-->

### Site Header [Partial]

Top-of-page navigation rendered on every page. Hard-coded into the theme template — not editor-placeable.

**Render rule:**
- Always renders.

**Data source:**
- WP menu at the `header` theme location (top-level items and their first-level children)
- ACF option (`acf-options-header`): `header_call_to_action_1`
- Static assets: `logo-alt.svg` (default) and `logo-white.svg` (hero variant) — both rendered via `Gust\Image::get()`, both linked to home; visibility swapped by CSS based on header state
- Note: `social_networks` is not currently consumed by the header

**Top bar layout (both breakpoints):**
- Logo on the left
- On desktop: inline navigation + CTA on the right
- On mobile: burger toggle (label toggles "Menu" / "Close") on the right
- White background; bar is `position: fixed` at the top of the viewport once JS has set the `--site-header--top` offset (`.site-header--positioned` class) — this applies at all breakpoints
- Top offset is calculated from the WP admin bar plus any `[data-header-offset]` element and exposed as `--site-header--top`; downstream sticky elements (e.g. `.trip-section-nav`) use it to stack below the bar

**Scroll behaviour:**
- Bar stays fixed at the top of the viewport at all breakpoints — it does not hide on scroll
- A `.site-header--hidden` CSS hook exists for a future hide-on-scroll-down / reveal-on-scroll-up pattern, but no JS currently toggles it

**Transparent variant (hero pages):**
- JS detects hero pages by inspecting the first element inside `.site-main__content` (falls back to `.site-main__inner`, then `.site-main`); if it matches `.page-header.has-hero-image`, `.homepage-hero-header.has-background-image`, or `.trip-page-header`, the `.site-header--hero` class is added.
- **Desktop only:** bar background goes transparent and link, burger, and CTA divider colours invert to white; the white-variant logo (`logo-white.svg`) replaces `logo-alt.svg` while the bar overlays the hero image. Once the user scrolls past the bottom of the hero, JS adds `.site-header--scrolled` and the bar returns to solid white background with brand-navy logo + links (CTA divider returns to bright-blue).
- **Mobile:** no colour inversion. The bar keeps its solid white background and brand-navy logo + links regardless of the hero behind it.
- On pages without a matching hero element, the bar uses the solid white background by default at all breakpoints.

**Desktop behaviour (≥ `screens.site-header` breakpoint):**
- Top-level menu items render inline to the right of the logo
- Items with children show a dropdown caret (`v`) next to the label
- A vertical divider rule is rendered immediately before `header_call_to_action_1` to visually separate the primary nav from the CTA — bright-blue on the white variant, orange on the transparent hero variant, returns to bright-blue once `.site-header--scrolled` is set
- `header_call_to_action_1` renders as an inline uppercase display-type link after the menu
- Hovering or focusing a top-level item with children opens a full-width mega-menu panel anchored below the top bar:
  - Parent label as a heading at the top-left of the panel
  - Child links arranged in a 3-column grid
  - Closes on mouse-out / blur / Escape
- Burger toggle is hidden

**Mobile behaviour (< `screens.site-header` breakpoint):**
- Top bar shows logo + burger toggle (label toggles "Menu" / "Close"); the bar remains visible while the menu is open
- Tapping the burger opens a panel that starts immediately *below* the bar (so logo + toggle stay above it) and animates in via clip-path + opacity transition; panel content fades and slides up. Body scroll is locked while open via `no-scroll` on `<html>`.
- Panel body (vertical list, thin horizontal divider lines between top-level items):
  - Top-level menu items rendered in large League Gothic uppercase display type
  - Items with children show a chevron (right when collapsed, rotates to down when expanded) and expand **inline** to reveal child links indented beneath the parent — no separate sub-panel or drill-down. Sub-links use small body type with no uppercase transform.
  - `header_call_to_action_1` rendered inline within the list
- Tapping the burger again or pressing Escape closes the panel with the reverse animation
- No social-icon row is currently rendered in the panel

### Page Header [Block]

Full-width hero that auto-populates from the current page, post, term, or router page, with optional custom heading, subheading, CTA, and image. Block is restricted to `page` and `gust-template` post types.

**Fields:**
- **heading** (text) - Overrides the default title
- **subheading** (wysiwyg) - Supporting text
- **primary_call_to_action** (link) - Primary button link
- **image** (image, return: array) - Optional override image for the current object

**Programmatic-only inputs:**
- **back_link** (array with `url` and `label`) - Replaces breadcrumbs with a "Back" link. Injected automatically for accommodation/itinerary singles.

**Auto-population by context:**
- `post` (blog): hero featured image, adds publication date and author as meta, applies `article` type, removes background colour
- `story`: removes background and image, applies left-aligned variant (`page-header--align-left`), suppresses breadcrumbs, applies `story` type. Reads `contributor_name` field and renders "By {name}" as meta text.
- `guide`: square 300px featured image (when present), suppresses breadcrumbs, forces subheading to "Meet our team", removes background, applies `guide` type. The subheading renders **above** the heading, while the heading renders below — this is the only type where the order is reversed.
- `accommodation` / `itinerary`: removes background and image, applies left-aligned variant (`page-header--align-left`), suppresses breadcrumbs, injects a "Back" link to the post-type archive (falls back to home)
- `trip_style` term: pulls `subheading` from term-level ACF fields (`group_trip_style_taxonomy`); suppresses hero image on archives
- General `WP_Term`: reads `subheading` and `image` from term ACF fields if available
- `page` (top-level, no parent): suppresses breadcrumbs
- `page` (front page): adds `page-header--home` class, suppresses breadcrumbs

### Trip Page Header [Block]

Trip hero section registered as `acf/trip-page-header`, restricted to the `trip` post type. Replaces `Page Header` on `trip` singles and combines editorial trip fields with derived trip metadata. Renders two zones: summary items (calendar, location, price) and stat items (duration, distance, etc.).

**Render rule:**
- Always present in the hard-coded `trip` template

**Auto-population logic:**
- Heading defaults to post title
- Date summary uses `Multiple dates` when more than one departure exists
- Location is built from `location` + `country`
- Price is derived from the cheapest `dates` row
- Ability level and swim type come from taxonomies
- Image falls back to featured image unless overridden
- A "View dates & book" CTA button (anchor to `#trip-dates`) is rendered in the summary zone whenever the trip has at least one date row

**Data source:**
- Post-level ACF and taxonomies on `trip`

**Fields (block-level — `group_component_trip_page_header`):**
- **heading** (text) - Optional title override; falls back to post-level `trip_heading`, then post title
- **description** (textarea) - Hero summary paragraph; falls back to post-level `trip_description`
- **image** (image, return: array) - Optional hero image override; falls back to post-level `trip_header_image`, then featured image
- **non_swimmers_text** (text) - Block-level override for the non-swimmers banner; falls back to post-level `welcome_text`

**Fields (post-level — "Stats" tab in `group_trip_page_sections`):**
- **duration_nights** (number)
- **distance_min_km** (number)
- **distance_max_km** (number)
- **water_temp_min_c** (number)
- **water_temp_max_c** (number)
- **max_group_size** (number)
- **welcome_text** (text) - Non-swimmers banner copy (overridable per-block via `non_swimmers_text`)
- **technique_coaching_text** (text)

### Trip Section Nav [Block]

Sticky trip-only section navigation under the hero, registered as `acf/trip-section-nav`. Jump links are generated dynamically based on which hard-coded sections actually have content.

**Render rule:**
- Do not render if no eligible locked sections have content

**Auto-population logic:**
- Jump links cover: Highlights, Itinerary, Accommodation, What's Included, Getting There, Reviews, FAQs (only for sections with content). No jump link for Dates & Book.
- Primary CTA opens the first available departure `enquiry_url`
- Secondary CTA has three states:
  - Multiple dates (or zero): "View dates & book" anchor link to `#trip-dates`
  - Single bookable date with `booking_url`: "Book" linking to the external booking URL
  - Single sold-out date: non-linked status label (e.g. "Sold Out")

**Fields:**
- No block fields. Derived from trip data and relationships.

### Trip Highlights [Block]

Locked Highlights section for the trip single page.

**Render rule:**
- Do not render if `highlights` is empty

**Data source:**
- Post-level ACF: `highlights`

### Trip Itinerary Preview [Block]

Locked itinerary teaser on the trip page.

**Render rule:**
- Do not render if `itinerary` is not selected

**Data source:**
- Related post: `itinerary`
- Reads the `preview_days` repeater (max 3) from the linked itinerary post; each item has `title` (text) and `summary` (textarea)
- Fallback when `preview_days` is empty: itinerary post title + excerpt (or first 60 words of content)
- CTA links to the itinerary permalink

### Trip Accommodation Preview [Block]

Locked accommodation teaser on the trip page.

**Render rule:**
- Do not render if `accommodation` is not selected

**Data source:**
- Related post: `accommodation`
- Pulls summary fields, tags, star rating, square gallery, rooms intro, and CTA

### Trip Includes [Block]

Locked "What's included" section.

**Render rule:**
- Do not render if both `included_items` and `not_included_items` are empty

**Data source:**
- Post-level ACF: `included_items`, `not_included_items`

### Trip Getting There [Block]

Locked structured travel-information section.

**Render rule:**
- Do not render if `getting_there_stages` is empty

**Data source:**
- Post-level ACF: `getting_there_stages`

### Trip Reviews [Block]

Locked reviews embed section.

**Render rule:**
- Do not render if `reviews_embed_code` is empty

**Data source:**
- Post-level ACF: `reviews_embed_code`

### Trip Related Stories [Block]

Manual related-story section at the end of the trip page.

**Render rule:**
- Do not render if `related_stories` is empty

**Data source:**
- Post-level ACF relationship: `related_stories`

### Trip Related Trips [Block]

Manual related-trip section at the end of the trip page. Renders using `TripCards` (3-column layout).

**Render rule:**
- Do not render if `related_trips` is empty

**Data source:**
- Post-level ACF relationship: `related_trips`

### Trip Single [Partial]

Top-level orchestrator for the entire trip single template. Validates the current post is a `trip`, reads the `faqs` ACF field, and delegates rendering to all locked trip components in layout order.

**Render rule:**
- Only renders when the current post is of type `trip`

**Data source:**
- Post-level ACF: `faqs` (repeater with `question` and `answer` sub-fields) — mapped into Accordion items

### Trip Get In Touch [Partial]

Global "Get in touch" contact bar rendered on trip singles between Dates & Book and Related Stories. Driven by site-wide options, not per-trip fields. No section nav jump link is generated for this section.

**Render rule:**
- Do not render if `get_in_touch_contacts` option is empty

**Data source:**
- ACF options page (`acf-options-general`): `get_in_touch_contacts` repeater
- Sub-fields: `icon` (select: whatsapp, email, phone), `label` (text), `value` (text), `url` (text)

### Get In Touch [Block]

Generic contact block (`acf/get-in-touch`) for use on any page. Distinct from `Trip Get In Touch` which reads from global options.

**Fields:**
- **contacts** (repeater)
  - **type** (text, required) - Contact method label, e.g. "Office", "Sales", "Email". Items where `type` equals "Email" render as an email entry; all others render as phone entries.
  - **value** (text, required) - Phone number or email address to display

### Image Full Width [Block]

Full-viewport-width image block (`acf/image-full-width`). Uses CSS breakout pattern (100vw, 600px height, object-cover).

**Fields:**
- **image** (image, return: id)

### Cards [Block]

Responsive card grid for taxonomy terms, editorial posts, and custom content. Does **not** handle Trip posts — use `TripCards` for those (exception: `TripRelatedTrips` uses `TripCards` for related trip rendering).

**Fields:**
- **heading** (text) - Section heading
- **subheading** (wysiwyg) - Supporting text
- **button** (link) - Optional footer link
- **card_source** (button_group: recent, selected, trip_styles, destinations, custom) - Source for the cards
- **custom_cards** (repeater, min: 1) - Manual card content shown when `card_source` is `custom`
  - **heading** (text)
  - **image** (image, return: array)
  - **text** (wysiwyg)
  - **link** (link)
- **post_type** (button_group: story) - Post type used when `card_source` is `recent`
- **limit** (button_group: 2, 3, 4, 6) - Number of recent posts to query
- **selected** (relationship, post_type: page, story, events, guide, max: 9) - Selected posts when `card_source` is `selected` (excludes `trip`)
- **selected_trip_styles** (taxonomy, trip_style, multi_select) - Specific trip styles when `card_source` is `trip_styles`; empty = all
- **selected_destinations** (taxonomy, country, multi_select, min: 2, max: 6) - Specific destinations when `card_source` is `destinations`; empty = all
- **type** (button_group: default, horizontal, carousel) - Switches card layout
- **card_image_fit** (select: default, contain) - Image fit mode
- **columns** (select: 2, 3, 4) - Grid column count
- **slider_on_mobile** (true_false) - Enable horizontal scroll on smaller screens. Conditionally hidden when `type` is `carousel`.

**Runtime behaviour:**
- When `card_source` is `trip_styles` or `destinations`, or when the programmatic `card_type` arg is `trip-style`, read-more button auto-sets to "Find Your Trip"; otherwise "Read More"
- On taxonomy archives, applies `cards--taxonomy-term-grid` CSS class

### Card [Partial]

Single card renderer used by the `Cards` block and archive grids.

**Fields:**
- No editor fields. Consumes either a `WP_Post`, a `WP_Term`, or a prepared `content` array at runtime.

### TripCards [Block]

3-column grid of trip cards. Used as an editor block (with heading/subheading and source options) and also rendered programmatically on `trip_style` and `country` taxonomy archives.

**Fields:**
- **heading** (text) - Section heading
- **subheading** (wysiwyg) - Supporting text
- **card_source** (button_group: recent, selected) - Trip source
- **limit** (button_group: 3, 6) - Number of recent trips to query (max 6)
- **selected** (relationship, post_type: trip, min: 1, max: 6) - Manual trip selection when `card_source` is `selected`

**Archive usage:**
- `trip_style`, `country`, and `location` taxonomy archives render TripCards with the queried trips (no block fields, populated from the archive query)

### TripCard [Partial]

Single trip card renderer used by `TripCards`. Consumes a `WP_Post` of type `trip` and derives all display data from post-level ACF fields and taxonomies.

**Layout (top to bottom):**
1. Image (featured image, `gust_card_square` size)
2. Title (trip name)
3. Meta list with icons:
   - Dates: single range or "Multiple dates"
   - Location: `location` + `country` taxonomy terms
   - Skill levels: `skill_level` taxonomy terms, comma-separated
4. Price: "From" on first line, "£X,XXX" on second line (cheapest departure from `dates` repeater)
5. CTA button: "View Trip & Book" (`color-context-orange` style)

**Fields:**
- No editor fields. Runtime input is a `WP_Post` of type `trip`.

### Accordion [Block]

Expandable content list for FAQs or rich text sections.

**Fields:**
- **heading** (text) - Optional section heading
- **accordion_items** (repeater)
  - **title** (text)
  - **content** (wysiwyg)

### Banner [Block]

Compact banner with inline image and message.

**Fields:**
- **image** (image, return: array) - Icon, logo, or image displayed inline with the banner message
- **message** (wysiwyg, required) - Banner text
- **image_height** (range, default: 26) - Inline image height in pixels

**Programmatic-only inputs:**
- **show_close_button** (bool) - When true, renders a dismissable close button. Not editor-exposed.

### Logo Grid [Block]

Grid of logos with optional links and a selectable aspect ratio.

**Fields:**
- **heading** (text) - Optional section heading
- **subheading** (wysiwyg) - Supporting text
- **logos** (repeater)
  - **image** (image, return: array)
  - **link** (link)

**Programmatic-only inputs:**
- **featured_text** (text) - Optional small label rendered above the heading
- **columns** (number) - Override grid column count (adds `cards--columns-{n}` class)
- **background_color** (text) - Adds `has-{color}-background-color` and `has-background` classes when set and not `none`
- **display** (text, default: `grid`) - Passed to the template as `logo-grid--{$display}` class

### Media & Content [Block]

Split layout combining text content with either an image or a video.

**Fields:**
- **heading** (text) - Main heading
- **subheading** (text) - Secondary heading
- **content** (wysiwyg) - Body content
- **button_1** (link) - Optional CTA button
- **media_type** (button_group: image, video) - Media source type
- **video** (oembed) - Video embed URL when `media_type` is `video`
- **image** (image, return: array) - Required cover image / fallback media image
- **media_side** (button_group: left, right) - Which side the media appears on

### Quote [Block]

Pull quote with optional credit and role.

**Fields:**
- **quote** (textarea, required) - Quote text. Newlines are rendered via `nl2br()` in the template; the ACF field uses no auto-formatting.
- **credit** (text) - Person or source name
- **role** (text) - Role or title

### Trip Dates [Block]

Server-rendered departure list for the current trip.

**Fields:**
- No block fields. Reads the current trip's `dates` repeater field. Night count and price-display fields are computed and rendered on each departure row.

### Taxonomy Filters [Partial]

Derived filter bar that renders term links for the current taxonomy or object context.

**Fields:**
- No editor fields. Runtime inputs include `taxonomy`, `current_item`, `label`, and `show`. The `label` defaults to "Filter by {taxonomy singular name}" when a taxonomy is provided.

### Gallery [Block]

Lightbox-enabled image gallery (`acf/gallery`).

**Fields:**
- **heading** (text) - Optional section heading
- **images** (gallery, return: array)

### Promo [Block]

Compact highlighted call-to-action strip with title, subheading, and a single link (`acf/promo`). Used standalone or rendered programmatically inside the Trip Single layout (driven by post-level `promo_*` fields when no block fields are set).

**Fields:**
- **title** (text, required)
- **subheading** (text)
- **link** (link)

**Programmatic-only inputs:**
- **post_id** (int) - When provided, falls back to that post's `promo_title`, `promo_subheading`, and `promo_link` ACF fields

### Testimonial Cards [Block]

3-column grid of testimonial / review cards (`acf/testimonial-cards`).

**Fields:**
- **heading** (text)
- **subheading** (wysiwyg)
- **button** (link)
- **card_source** (button_group: custom, stories) - Source for the cards
- **custom_items** (repeater) - Used when `card_source` is `custom`
  - **stars** (number, min: 0, max: 5)
  - **quote** (textarea)
  - **author_name** (text)
  - **author_detail** (text)
  - **image** (image, return: array)
- **story_items** (repeater) - Used when `card_source` is `stories`
  - **story** (post_object, post_type: story)
  - **stars** (number, min: 0, max: 5)

### Text Items [Block]

Structured list of labelled text entries (`acf/text-items`).

**Fields:**
- **heading** (text)
- **items** (repeater)
  - **meta** (text)
  - **title** (text)
  - **description** (wysiwyg)

### Homepage Hero Header [Block]

Alternate full-bleed homepage hero (`acf/homepage-hero-header`), restricted to `page` and `gust-template` post types. Mirrors `Page Header` but supports additional image-position variants (background, mini, inset) — these were removed from the base Page Header component but retained here for the homepage layout.

**Fields:**
- **heading** (text)
- **subheading** (wysiwyg)
- **primary_call_to_action** (link)
- **image** (image, return: array)
- **image_position** (button_group: background, mini, inset)

### Calendar Listings [Partial]

Server-rendered chronological departure listing for the `/calendar/` route. Groups upcoming trip departures by month and de-lists individual departures once their start date has passed.

**Fields:**
- No editor fields. Driven by the active trip query.

### Event Single [Partial]

Top-level orchestrator for the entire `events` single template. Validates the current post is of type `events`, reads the `faqs` ACF field, and delegates rendering to all locked event components in layout order.

**Render rule:**
- Only renders when the current post is of type `events`

**Layout order:** Trip Page Header, Section Nav, Highlights, Itinerary Preview, Accommodation Preview, What's Included, Getting There, Reviews, FAQs, Dates & Book, Get In Touch, Related Stories, Related Trips

**Data source:**
- Post-level ACF: `faqs` (repeater with `question` and `answer` sub-fields) — mapped into Accordion items

### Related Stories [Block]

Standalone editor block (`acf/related-stories`) that shows up to two related stories side by side. Distinct from `Trip Related Stories` which is hard-coded on trip singles.

**Fields:**
- **heading** (text) - Optional heading (defaults to "Related stories" if left blank)
- **stories** (relationship, post_type: story, max: 2) - Story posts to display

**Render rule:**
- Do not render if `stories` is empty

### Testimonial Card [Partial]

Single testimonial / review card renderer used by `TestimonialCards`. Consumes a prepared data array with star rating, quote, and author info.

**Fields:**
- No editor fields. Programmatic-only inputs consumed at runtime:
  - **stars** (number, 0–5) - Star rating
  - **quote** (string) - Testimonial text
  - **author_name** (string) - Author name
  - **author_detail** (string) - Role or location text
  - **image** (array) - Optional author portrait image
  - **url** (string) - Optional link URL wrapping the quote

**Render rule:**
- Do not render if `quote` is empty

### Trip Intro [Partial]

Intro paragraphs for the trip single page. Renders a large lead paragraph and a standard body paragraph from post-level ACF fields.

**Render rule:**
- Do not render if both `intro_lead` and `intro_body` are empty

**Data source:**
- Post-level ACF: `intro_lead` (textarea), `intro_body` (textarea)

---

## Integrations

<!-- Third-party services and APIs -->

- **Feefo** - Customer reviews embedded on trip single pages
  - Integrated via the `Trip Reviews` block component which reads the `reviews_embed_code` ACF field on each trip
  - Embed code is output raw (unescaped) to support Feefo's widget scripts

---

## Other Functionality

<!-- Custom features, cron jobs, CLI commands, special behaviors -->

<!-- Example:
- **Event expiry** - Events automatically unpublished 24h after end date (wp-cron)
- **Import CLI** - `wp import-events` pulls events from external API
- **Member area** - Password-protected pages for logged-in users
-->

Code-side Punch List (separate follow-up work)
                                                                                                                                                                                                          
  These are real bugs / inconsistencies the audits surfaced. None of them are spec problems — they need fixing in code.                                                                                   
                                                                                                                                                                                                          
  Bugs                                                                                                                                                                                                    
                                                                                                                                                                                                          
  1. Events ACF location rule uses singular slug — **[USER TO FIX]** Theme/Modules/Events/acf-json/group_event_fields.json targets post_type == event, but the post type is registered as events (plural).
  2. Trip status sold_out_private handled in PHP but not selectable — **[USER TO FIX]** TripData::getStatusLabel() and getSoldOutLabel() handle sold_out_private, but the trip ACF status select only offers bookable and sold_out.
  3. Trip Dates sold_out_label never reaches the template — **[FIXED]** Updated template.php to use `$row['sold_out_label']`.
  4. Trip Dates price/nights computed but suppressed — **[FIXED]** Un-commented nights and price_display in template.php.
  5. getting_there_stages template references missing fields — **[USER TO FIX]** Confirm only one of the two competing ACF groups should be active.
  6. Two competing trip ACF groups — **[USER TO FIX]** acf-json/group_trip_fields.json (root) and Theme/Modules/Trips/acf-json/group_trip_page_sections.json.
  7. Guide role field never registered — **[USER TO FIX]** Register missing ACF field.
  8. Routes comment wrong — **[FIXED]** Updated comment in `Theme/Routes/routes.php` to `/locations/`.
  9. HomepageHeroHeader still has mini variant — **[CONFIRMED]** Kept for now as it is likely intentional for homepage chrome.
                                                                                                                                                                                                          
  Dead code                                                                                                                                                                                               
                                                            
  10. TaxonomyFilters::transform() reads $args['object'] — **[FIXED]** Removed unused assignment.
  11. Card::transform() fallback read_more_text — **[FIXED]** Removed unreachable default.
                                                            
  Minor                                                                                                                                                                                                   
                                                            
  12. Cards.slider_on_mobile conditional logic undocumented — **[FIXED]** Added comment in `Cards.php`.
  13. Cards card_background_color and tag runtime args — **[FIXED]** Added one-line comments in `Cards.php` make() signature.
                                                                                                                                                                                                          
  ---                                                       
  Branches: development is up to date with origin. PR #2's feature branch (feat/page-header-back-link) can be deleted locally if you want — say the word.         