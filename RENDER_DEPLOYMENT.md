# Render Deployment Guide for Jeopardy App

## Overview
This guide will help you deploy your Laravel Jeopardy application to Render using Docker.

## Prerequisites
- A Render account
- Your Laravel application code pushed to a Git repository (GitHub, GitLab, etc.)

## Deployment Steps

### 1. Create a New Web Service on Render

1. Log in to your Render dashboard
2. Click "New +" and select "Web Service"
3. Connect your Git repository
4. Choose the repository containing your Jeopardy app

### 2. Configure the Web Service

**Basic Settings:**
- **Name**: `jeopardy-app` (or your preferred name)
- **Environment**: `Docker`
- **Region**: Choose the closest to your users
- **Branch**: `main` (or your default branch)
- **Dockerfile Path**: `Dockerfile.render`

**Build Settings:**
- **Build Command**: Leave empty (Docker will handle this)
- **Start Command**: Leave empty (defined in Dockerfile)

### 3. Environment Variables

Add these environment variables in Render:

```
APP_NAME=Jeopardy
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 4. Generate APP_KEY

After setting up the environment variables, you'll need to generate an APP_KEY. You can do this by:

1. Running the service once
2. Going to the "Shell" tab in Render
3. Running: `php artisan key:generate`
4. Copy the generated key and update the APP_KEY environment variable

### 5. Database Setup

The app uses SQLite by default, which is perfect for Render. The database file will be created automatically in the container.

### 6. Deploy

1. Click "Create Web Service"
2. Render will automatically build and deploy your application
3. The first build may take 5-10 minutes

## Important Notes

### File Permissions
The Dockerfile handles file permissions automatically, but if you encounter issues:

1. Go to the "Shell" tab in Render
2. Run: `chmod -R 755 storage bootstrap/cache`

### Environment Variables
- Make sure `APP_URL` matches your Render service URL
- Set `APP_DEBUG=false` for production
- The `PORT` variable is automatically set by Render

### Database
- SQLite is used by default and works well on Render
- The database file is created in the container
- For production, consider using a managed database service

### Caching
The Dockerfile automatically runs:
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

## Troubleshooting

### Build Failures
1. Check the build logs in Render
2. Ensure all required files are present
3. Verify the Dockerfile path is correct

### Runtime Errors
1. Check the service logs in Render
2. Verify environment variables are set correctly
3. Ensure the APP_KEY is generated

### Database Issues
1. Check if the SQLite file exists: `ls -la database/`
2. Run migrations manually: `php artisan migrate`
3. Check database permissions

## Performance Optimization

### For Better Performance:
1. Enable Render's auto-scaling if needed
2. Consider using a CDN for static assets
3. Monitor your service's performance in Render's dashboard

### Cost Optimization:
1. Start with the free tier
2. Monitor usage and upgrade only when needed
3. Use Render's sleep mode for development environments

## Support

If you encounter issues:
1. Check Render's documentation
2. Review the Laravel deployment guide
3. Check the service logs in Render dashboard
