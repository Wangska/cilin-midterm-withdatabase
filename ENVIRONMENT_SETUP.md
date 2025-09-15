# Environment Variables Setup

Your application now uses environment variables for database configuration, just like in the image you showed.

## Required Environment Variables

You need to set these environment variables before running the application:

```bash
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=notes_system
DB_PORT=3306
```

## How to Set Environment Variables

### Option 1: Windows (Command Prompt)
```cmd
set DB_HOST=localhost
set DB_USERNAME=root
set DB_PASSWORD=
set DB_DATABASE=notes_system
set DB_PORT=3306
```

### Option 2: Windows (PowerShell)
```powershell
$env:DB_HOST="localhost"
$env:DB_USERNAME="root"
$env:DB_PASSWORD=""
$env:DB_DATABASE="notes_system"
$env:DB_PORT="3306"
```

### Option 3: Create a batch file
Create a file called `set_env.bat` with:
```batch
@echo off
set DB_HOST=localhost
set DB_USERNAME=root
set DB_PASSWORD=
set DB_DATABASE=notes_system
set DB_PORT=3306
echo Environment variables set successfully!
```

Then run it before starting your web server.

## For Production (Coolify/Docker)
In your Coolify or Docker environment, set these as environment variables through the admin panel.

## Notes
- The database connection now uses `mysqli_connect()` with environment variables
- All User and Note classes have been updated to work with mysqli instead of PDO
- Make sure your database `notes_system` exists before running the application
