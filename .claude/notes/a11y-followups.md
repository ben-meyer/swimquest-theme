# A11y follow-ups

Sub-threshold items from the 2026-05-08 a11y scan. The two 75/100+ items
(skip-link focus + nav landmark labels) were fixed in `00055aa`.

## Heading hierarchy break in `trip-dates` (~65/100, WCAG 1.3.1)

`components/trip-dates/template.php` uses `<h4>` for the section heading
then `<h6>` for each date label, skipping `<h5>`. Either bump inner labels
to `<h5>` or use the `Heading` component with a computed level.

## `target="_blank"` with no "opens in new tab" affordance (~60/100, WCAG 3.2.5 + 2.4.4)

`Gust\Components\Link::transform` adds `rel="noopener"` on `_blank` but
emits no visible icon and no sr-only "(opens in new tab)" text. Affects
every external link via the component (trip dates booking, social icons,
get-in-touch WhatsApp, etc.). One-line fix in `Link.php` to append
`<span class="screen-reader-text">(opens in new tab)</span>` when
`target === '_blank'`.

## No `aria-current="page"` on active menu items (~55/100, WCAG 4.1.2)

`Menu::transform` already detects `$item->is_current_item`, but the
rendered output only adds a CSS class — no `aria-current="page"` on the
active link. Add it in `MenuList` / `MenuItem` template when the item is
current.
