# Import legacy WordPress blog → `story` post type

Banked plan for the SwimQuest XML import. Not actioned yet — client is not pressing for it and would need to be coordinated so they aren't writing to the DB during the run. Pick this back up when the client signs off on a freeze window.

## Source file

`~/Downloads/swimquestswimmingholidays.WordPress.2026-06-22.xml` (WXR, ~649K).

## Dry-run inventory (run 2026-06-22)

| What | Count |
|------|-------|
| `<wp:post_type>post` items | 106 |
| `<wp:attachment>` items in XML | 0 |
| Unique inline `<img>` URLs in content | 243 |
| Source hosts | 251 × `swimquest.uk.com`, 2 × dropbox previews, 2 × tumblr |

Re-run the dry-run before importing — the XML may have moved on:

```bash
XML=~/Downloads/swimquestswimmingholidays.WordPress.2026-06-22.xml
grep -oE '<wp:post_type><!\[CDATA\[[^]]+\]\]></wp:post_type>' "$XML" | sort | uniq -c
grep -oE 'https?://[^"<> ]+\.(jpg|jpeg|png|gif|webp|svg)' "$XML" | sort -u | wc -l
```

## Why not the default WP Importer

`wp import` / the WP Importer plugin maps `post_type` 1:1 from the XML — no mapping UI. So default `post` rows come in as default `post`, not `story`.

## Why not WP All Import Pro

Pro plugin (paid). Free version doesn't expose the post_type mapping for custom types in a usable way.

## Chosen approach — XML pre-process + `wp import`

`sed` the WXR to rewrite every `post_type=post` to `post_type=story` before importing. Pages, nav menu items, attachments etc. have different `post_type` strings and aren't touched. Then run `wp import` with `--fetch-attachments` so inline media is pulled into the local uploads dir.

```bash
XML=~/Downloads/swimquestswimmingholidays.WordPress.2026-06-22.xml
sed 's|<wp:post_type><!\[CDATA\[post\]\]></wp:post_type>|<wp:post_type><![CDATA[story]]></wp:post_type>|g' \
  "$XML" > /tmp/import-as-story.xml

cd /Users/benmeyer/Development/code/work/swimquest/wordpress
wp plugin install wordpress-importer --activate   # one-off
wp import /tmp/import-as-story.xml --authors=create --fetch-attachments
```

`--fetch-attachments` only pulls inline images whose original URL is still reachable. Dropbox preview URLs and tumblr links from this XML will 404 — they'll come through as broken `<img>` tags and will need either a manual fix-up or a script that scans imported `post_content` and flags missing images.

## Rollback

Imported rows are scoped to the `story` post type. Roll back by date:

```bash
BEFORE=$(date -u -v-1H +"%Y-%m-%d %H:%M:%S")   # one hour ago, adjust as needed
IDS=$(wp post list --post_type=story --post_status=any \
  --date_query="[{\"after\":\"$BEFORE\"}]" --format=ids)
wp post delete $IDS --force
```

The 105 legacy default-`post` rows that existed pre-clean-up were already deleted in this session (`wp post delete $(wp post list --post_type=post …) --force`) — re-running the importer creates fresh `story` rows, it doesn't touch any other content.

## Promoting to staging

Two routes; **prefer Option 1**.

### Option 1 — import locally, then push DB + uploads to staging

Piggy-back the existing wp-sync workflow:

1. Import locally per above; verify in the admin and on the front-end.
2. Temporarily edit `wp-sync.yml`: set `uploads: true` in the **push** section.
3. `wp sync push staging`
4. Reset `uploads: false` so subsequent pushes don't keep syncing the uploads dir.

This uses the same DB/media push you already use for deploys and inherits the URL search-replace handling.

### Option 2 — repeat the import on staging directly

Use when you specifically want staging to diverge from local. Higher operational risk (two imports, two chances of a half-finished run).

```bash
scp -P 2223 ~/Downloads/swimquestswimmingholidays.WordPress.2026-06-22.xml \
  swimquest@185.219.237.7:/tmp/
ssh -p 2223 swimquest@185.219.237.7
cd /home/swimquest/site/public_html
sed 's|<wp:post_type><!\[CDATA\[post\]\]></wp:post_type>|<wp:post_type><![CDATA[story]]></wp:post_type>|g' \
  /tmp/swimquestswimmingholidays.WordPress.2026-06-22.xml > /tmp/import-as-story.xml
wp plugin install wordpress-importer --activate
wp import /tmp/import-as-story.xml --authors=create --fetch-attachments
```

## Coordination required before running

- Get client confirmation that nothing important is being written to the DB during the window — wp-sync's DB push is a full overwrite, so any concurrent admin work on staging will be lost.
- Sanity-check by listing recent edits before the push: `wp post list --post_status=any --orderby=modified --order=desc --posts_per_page=10 --fields=ID,post_title,post_modified`.
