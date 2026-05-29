# Deploy Theme Files to Staging

## Steps

1. **Build assets**
   ```bash
   npm run build
   ```

2. **Install composer without dev dependencies**
   ```bash
   composer install --no-dev
   ```
   This excludes `squizlabs/php_codesniffer` and other dev packages. Without `--no-dev`, the phpcs test fixtures get deployed and trigger the hosting provider's malware scanner.

3. **Merge `development` → `main` and push**
   ```bash
   git checkout main
   git merge development --no-edit
   git push origin main
   git checkout development
   ```

4. **Run the deploy script**
   ```bash
   bash dev-scripts/deploy.sh staging
   ```
   Do NOT modify `deploy.sh` — it must stay in sync with upstream `kite-co-code/gust`.

5. **Fix permissions on the remote server**
   ```bash
   ssh benmeyer@benmeyer.digital "chmod -R 755 ~/swimquest.benmeyer.digital/wp-content/"
   ```
   rsync copies local Mac permissions (700) which the web server can't read. This must be run after every deploy.

## Notes

- `main` is the production branch — deploys to staging server (benmeyer.digital)
- `.deployignore` excludes source files but keeps `public/build/` and `assets/theme-config.json`
- The deploy script runs `wp cache flush` on the remote server automatically
- To sync plugins separately (e.g. after a fresh deployment): `bash dev-scripts/sync-plugins.sh`
