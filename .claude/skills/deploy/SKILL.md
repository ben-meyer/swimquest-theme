---
name: deploy
description: Deploy SwimQuest theme files and/or database to staging. Use when deploying code changes, pushing the database, or doing a full deployment. Trigger keywords: deploy, staging, push database, sync database, fresh deployment.
---

# Deploy to Staging

Full deployment workflow for SwimQuest theme files and database.

## Available Workflows

| Workflow | Use Case |
|----------|----------|
| [Files](files.md) | Deploy theme files to staging |
| [Database](database.md) | Push local database to staging |
| [Full](full.md) | Both files and database (most common) |

## Workflow Selection

- **"deploy"** or **"fresh deployment"** → Use [full.md](full.md)
- **"deploy files"** or **"push theme"** → Use [files.md](files.md)
- **"push database"** or **"sync database"** → Use [database.md](database.md)
