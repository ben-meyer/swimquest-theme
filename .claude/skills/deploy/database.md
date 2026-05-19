# Push Database to Staging

## Prerequisites: wp-config.php fix

Before `wp sync` will work, `wp-config.php` must have a `$table_prefix` guard. Without it, WP CLI fails with:
```
Error: The site you have requested is not installed.
Your table prefix is ''. Found installation with table prefix: wp_.
```

**Why it fails:** `wp-config.php` only loads database constants when `IS_DDEV_PROJECT == 'true'`, but the native `wp` CLI doesn't set this env var. `$table_prefix` is never set, and `wp-settings.php` errors out.

**Check if the fix is already in place:**
```bash
grep -n 'table_prefix' /Users/benmeyer/Development/code/work/swimquest/wordpress/wp-config.php
```

**If missing, add the guard.** Open `wp-config.php` and find this block:
```php
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */
```

Insert the guard between those two comments:
```php
/* Add any custom values between this line and the "stop editing" line. */

if ( ! isset( $table_prefix ) || empty( $table_prefix ) ) {
	$table_prefix = 'wp_';
}

/* That's all, stop editing! Happy publishing. */
```

`wp-config.php` is gitignored — this change only applies locally and must be re-applied if the file is regenerated.

## Steps

1. **Ensure wp-config.php fix is in place** (see above)

2. **Run from the theme directory** (where `wp-sync.yml` lives):
   ```bash
   wp sync push staging
   ```
   Confirm with `y` when prompted.

## What it does

- Exports local DB, imports to staging
- Runs search-replace: `https://swimquest.ddev.site` → `https://swimquest.benmeyer.digital`
- Flushes rewrite rules on remote

## Notes

- `uploads: false` in `wp-sync.yml` — media is not synced by default. To sync uploads, temporarily set `uploads: true`, run the sync, then reset to `false`.
- Must run from the theme directory (`wp-content/themes/gust/`) — that's where `wp-sync.yml` is located.
- Uses native Homebrew `wp` CLI + Jerome's wp-sync plugin, not `ddev wp`.
- After DB push, run `ssh benmeyer@benmeyer.digital "chmod -R 755 ~/swimquest.benmeyer.digital/wp-content/"` to fix permissions.
