# Full Deployment (Files + Database)

Run these in order. Files first, then database.

## 1. Deploy Files

See [files.md](files.md) for full detail. Summary:

```bash
npm run build
composer install --no-dev
git checkout main
git merge development --no-edit
git push origin main
git checkout development
bash dev-scripts/deploy.sh staging
```

## 2. Push Database

See [database.md](database.md) for the wp-config.php prerequisite fix.

```bash
# Confirm wp-config.php has the $table_prefix guard (see database.md)
wp sync push staging   # confirm with y
```

## Done

The staging site at `https://swimquest-4btr8.projectbeta.co.uk` will have the latest code and database.
