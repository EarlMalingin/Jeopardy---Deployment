# 🚀 QUICK RAILWAY SETUP

## ⚡ **5-Minute Deployment Guide**

### **Step 1: Create GitHub Repository**
1. Go to [GitHub.com](https://github.com)
2. Click "New repository"
3. Name: `jeopardy-game`
4. Make it **Public**
5. Click "Create repository"

### **Step 2: Upload Your Code**
Run these commands in your project folder:

```bash
# Initialize git repository
git init

# Add all files
git add .

# Commit changes
git commit -m "Initial commit - Jeopardy Game"

# Set main branch
git branch -M main

# Add remote (replace YOUR_USERNAME with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/jeopardy-game.git

# Push to GitHub
git push -u origin main
```

### **Step 3: Deploy to Railway**
1. Go to [Railway.app](https://railway.app)
2. Click "Start a New Project"
3. Choose "Deploy from GitHub repo"
4. Select your `jeopardy-game` repository
5. Click "Deploy Now"

### **Step 4: Add Database**
1. In Railway dashboard, click "New"
2. Select "Database" → "MySQL"
3. Wait for it to provision

### **Step 5: Configure Environment**
In Railway → Variables tab, add:

```env
APP_NAME="Jeopardy Game"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your-mysql-password
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### **Step 6: Setup Database**
In Railway → Deployments → View Logs, run:

```bash
php artisan key:generate
php artisan migrate
```

### **Step 7: Your Game is Live!**
Visit: **https://your-app-name.railway.app/jeopardy**

---

## 🎯 **That's It!**

Your Jeopardy game is now **FREE** and **LIVE** on the internet!

**Total time: 5 minutes**
**Cost: $0**
**Features: All included!**

---

## 🆓 **Free Tier Benefits:**
- ✅ 500 hours/month (24/7 usage)
- ✅ 512MB RAM
- ✅ 1GB storage
- ✅ MySQL database
- ✅ SSL certificate
- ✅ Custom domain support

---

## 🎮 **Test Your Game:**
- ✅ Main page: `/jeopardy`
- ✅ Lobby creation
- ✅ Player joining
- ✅ Game creation
- ✅ Multiplayer functionality
- ✅ Custom games
- ✅ 60-question support

---

**🚀 Your Jeopardy game is ready to play!**

