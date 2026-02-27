# 🚀 Deployment Guide — Symfony 4.4 on Railway

## Files Added to Your Project

| File | Purpose |
|------|---------|
| `Dockerfile` | Builds your PHP 7.4 + Apache app image |
| `docker-compose.yml` | Local development with MySQL + phpMyAdmin |
| `railway.json` | Tells Railway how to build & run your app |
| `.gitlab-ci.yml` | Auto-deploys to Railway on every push to `main` |

---

## 🖥️ Step 1: Test Locally with Docker

```bash
# Build and start all services
docker-compose up --build

# Your app will be available at:
# http://localhost:8080  → Symfony app
# http://localhost:8081  → phpMyAdmin

# Run Doctrine migrations inside the container
docker exec -it symfony_app php bin/console doctrine:migrations:migrate

# Stop everything
docker-compose down
```

---

## ☁️ Step 2: Set Up Railway

### 2.1 Create a Railway Account
Go to https://railway.app and sign up (free tier available).

### 2.2 Create a New Project
1. Click **"New Project"**
2. Choose **"Empty Project"**
3. Note your **Project ID** (visible in the URL or project settings)

### 2.3 Add a MySQL Database
1. Inside your Railway project, click **"+ New Service"**
2. Select **"Database" → "MySQL"** (or MariaDB)
3. Railway will auto-generate the connection details

### 2.4 Get Your Railway Token
1. Go to https://railway.app/account/tokens
2. Create a new token and copy it

---

## 🔑 Step 3: Configure GitLab CI/CD Variables

In your GitLab project, go to **Settings → CI/CD → Variables** and add:

| Variable | Value | Protected | Masked |
|----------|-------|-----------|--------|
| `RAILWAY_TOKEN` | Your Railway token | ✅ Yes | ✅ Yes |
| `RAILWAY_PROJECT_ID` | Your Railway project ID | ✅ Yes | No |
| `RAILWAY_PUBLIC_URL` | Your Railway app URL (after first deploy) | No | No |

---

## ⚙️ Step 4: Configure Environment Variables in Railway

In your Railway project → your app service → **Variables**, add:

```
APP_ENV=prod
APP_SECRET=2f02097bb64a9f688d7fd82f31d9a146
DATABASE_URL=mysql://USER:PASSWORD@HOST:PORT/DATABASE?serverVersion=mariadb-10.4
```

> ⚠️ Railway provides the DATABASE_URL for your MySQL service automatically.
> Copy it from the MySQL service → **Connect** tab and paste it into your app service variables.

---

## 📁 Step 5: Handle File Uploads (VichUploader)

Since Railway's filesystem is **ephemeral** (files are lost on redeploy), you have two options:

### Option A (Easy): Use Railway's Volume (Paid plan)
Add a volume mounted at `/var/www/html/public/uploads`.

### Option B (Free): Use Cloudflare R2 or AWS S3
Update your VichUploader config to use S3-compatible storage instead of local filesystem.

---

## 🔄 Step 6: First Deploy & Run Migrations

After your first deploy on Railway:

1. Go to your app service in Railway
2. Click **"Shell"** (or use Railway CLI: `railway run`)
3. Run:
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

---

## 🔁 Auto-Deploy Flow

```
git push origin main
       ↓
GitLab CI runs lint checks
       ↓
railway up deploys your Docker image
       ↓
Your app is live on Railway!
```
