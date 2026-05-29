# Code Punch List

Items surfaced during audits that need investigation or fixing in code. These are separate from spec updates — they are real gaps, bugs, or missing artefacts.

---

## Open Items

### 1. Banner — missing `block.json`
**Type**: Missing artefact / possible bug  
**Detail**: The spec lists Banner as `[Block]`. The ACF field group (`group_component_banner.json`) targets `acf/banner` as its location rule, meaning it expects a registered block. But no `block.json` exists in `components/banner/` — so the block is never registered with WordPress, and it cannot be inserted in the Gutenberg editor. The ACF group would never fire.  
**Question**: Is Banner intentionally a partial (remove ACF location rule and fix spec), or should a `block.json` be created?

---

### 2. Footer ACF group not exported to JSON
**Type**: Missing artefact / version control gap  
**Detail**: The `acf-options-footer` field group exists in the database (confirmed — `SiteFooter.php` reads `featured_in_heading`, `featured_in_logos`, `footer_text_top`, `footer_text_bottom`, `footer_form`, `footer_images` from the `option` context). No JSON file for this group exists in `acf-json/` or any module directory — it has never been saved to disk.  
**Question**: Export it from ACF admin (Field Groups → select group → save to JSON), or is this intentional?

---

### 3. Taxonomy term ACF fields not in JSON
**Type**: Missing artefact / version control gap  
**Detail**: The spec documents term-level ACF fields for `skill_level`, `country`, and `location` (each has `subheading` textarea and `image` array). No JSON files for these groups were found on disk. They may only exist in the database.  
**Question**: Same as above — export from ACF admin so they're version-controlled.

---

### 4. `TripData::getStatusLabel()` missing `sold_out_private` case
**Type**: Bug  
**Detail**: `TripData::getStatusLabel()` is called by `getPrimaryBookingAction()`, which feeds the section nav secondary CTA. The method handles `bookable` and `sold_out` but has no case for `sold_out_private` — it falls through to the default and returns `"Book Now"`, which is wrong.  
**Fix needed**: Add `case 'sold_out_private': return 'Private Group';` (or whatever label is correct).  
**Question**: Confirm the right label — is it "Private Group", "Sold Out", or something else?

---

## Previously Fixed Items

*(Moved here from WEBSITE-SPEC.md punch list for reference)*

- Trip Dates sold_out_label never reaches template — **[FIXED]**
- Trip Dates price/nights computed but suppressed — **[FIXED]**
- Routes comment wrong — **[FIXED]**
- TaxonomyFilters::transform() reads unused $args['object'] — **[FIXED]**
- Card::transform() fallback read_more_text — **[FIXED]**
- Cards.slider_on_mobile conditional logic undocumented — **[FIXED]**
- Cards card_background_color and tag runtime args — **[FIXED]**
