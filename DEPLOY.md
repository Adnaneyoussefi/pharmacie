# Oracle Cloud Deployment Guide

This guide explains how to deploy this Symfony app (or any new project) to Oracle Cloud Always Free.

---

## Prerequisites

- Oracle Cloud account (free forever at cloud.oracle.com)
- A running VM instance on Oracle Cloud (Ubuntu 22.04)
- Your SSH private key file (`.key`)
- Your project hosted on GitHub

---

## First Time Setup (Do Once)

### 1. Connect to Your Server

```powershell
ssh -i C:\path\to\your\private-key.key ubuntu@YOUR_PUBLIC_IP
```

**Tip:** Save this in your SSH config for easier access:

```
# C:\Users\YourName\.ssh\config
Host oracle
    HostName YOUR_PUBLIC_IP
    User ubuntu
    IdentityFile C:\path\to\your\private-key.key
```

Then connect simply with:

```bash
ssh oracle
```

---

### 2. Install Docker (Only Once on the VM)

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install docker.io docker-compose -y
sudo usermod -aG docker ubuntu
newgrp docker
```

Verify installation:

```bash
docker --version
docker-compose --version
```

---

## Deploying a New Project

### Step 1: Connect to the Server

```powershell
ssh -i C:\path\to\your\private-key.key ubuntu@YOUR_PUBLIC_IP
```

---

### Step 2: Clone the Project

```bash
cd ~
git clone https://YOUR_GITHUB_TOKEN@github.com/YOUR_USERNAME/YOUR_REPO.git
cd YOUR_REPO
```

> **Note:** Use a GitHub Personal Access Token instead of your password.
> Generate one at: GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)

---

### Step 3: Configure .env

```bash
vim .env
```

Set the following values:

```env
APP_ENV=prod
APP_SECRET=your_app_secret
DATABASE_URL="mysql://root:root@db:3306/yourdb?serverVersion=mariadb-10.4.17"
```

> **Important:** The database host must be `db` (the Docker service name), not `127.0.0.1`.

Save and exit vim: `Esc` → `:wq` → `Enter`

---

### Step 4: Update Ports in docker-compose.yml

Each project must use different ports to avoid conflicts. Edit `docker-compose.yml`:

```bash
vim docker-compose.yml
```

Change the ports and container names:

| Service    | Project 1 | Project 2 | Project 3 |
|------------|-----------|-----------|-----------|
| App        | 8080      | 8082      | 8084      |
| PhpMyAdmin | 8081      | 8083      | 8085      |
| DB         | 3307      | 3308      | 3309      |

Also update container names to avoid conflicts:

```yaml
container_name: symfony_app_2      # change the suffix per project
container_name: symfony_db_2
container_name: symfony_phpmyadmin_2
```

---

### Step 5: Build and Start Docker

```bash
docker-compose up -d --build
```

Verify all containers are running:

```bash
docker ps
```

You should see 3 containers with status `Up`.

---

### Step 6: Install Composer Dependencies

```bash
docker exec -it symfony_app composer install --no-interaction
```

> Replace `symfony_app` with your container name if you changed it.

---

### Step 7: Run Migrations

```bash
docker exec -it symfony_app php bin/console doctrine:migrations:migrate --no-interaction
```

---

### Step 8: Open Ports in Oracle Cloud Firewall

1. Go to Oracle Cloud dashboard
2. Click your instance → click the **Subnet** link
3. Click **Security List**
4. Click **Add Ingress Rules**
5. Add a rule for each port:
    - **Source CIDR:** `0.0.0.0/0`
    - **Destination Port:** `8080` (repeat for each port you need)

---

### Step 9: Open Ports in VM Firewall

```bash
sudo iptables -I INPUT -p tcp --dport 8080 -j ACCEPT
sudo iptables -I INPUT -p tcp --dport 8081 -j ACCEPT
sudo netfilter-persistent save
```

> Add more rules for each additional port (8082, 8083, etc.)

If `netfilter-persistent` is not installed:

```bash
sudo apt install iptables-persistent -y
sudo netfilter-persistent save
```

---

### Step 10: Access Your App

```
http://YOUR_PUBLIC_IP:8080
```

PhpMyAdmin:

```
http://YOUR_PUBLIC_IP:8081
```

---

## Updating an Existing Project

When you push new changes to GitHub, run these commands on the server:

```bash
cd ~/YOUR_REPO
git pull
docker-compose up -d --build
docker exec -it symfony_app composer install --no-interaction
docker exec -it symfony_app php bin/console doctrine:migrations:migrate --no-interaction
```

---

## Useful Docker Commands

```bash
# View running containers
docker ps

# View logs of a container
docker logs symfony_app

# Enter a container shell
docker exec -it symfony_app bash

# Stop all containers
docker-compose down

# Restart containers
docker-compose restart
```

---

## Notes

- The VM has **1GB RAM** — limit to **2-3 projects** max
- Containers restart automatically after VM reboot (`restart: unless-stopped` in docker-compose.yml)
- Keep your **SSH private key** safe — losing it means losing access to the server
- Always use `db` as the database host in `.env`, never `127.0.0.1`