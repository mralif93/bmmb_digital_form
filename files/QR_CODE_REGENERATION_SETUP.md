# QR Code Regeneration Setup

## Overview
QR codes are automatically regenerated every hour to ensure they remain up-to-date and secure.

## Implementation

### 1. Artisan Command
- **Command**: `php artisan qr-codes:regenerate`
- **Location**: `app/Console/Commands/RegenerateQrCodes.php`
- **Description**: Regenerates all active QR code images

### 2. Scheduled Task
- **Schedule**: Every hour
- **Location**: `routes/console.php`
- **Configuration**:
  - Runs hourly: `->hourly()`
  - Prevents overlapping: `->withoutOverlapping()`
  - Runs on one server only: `->onOneServer()`
  - Logs output: `->appendOutputTo(storage_path('logs/qr-codes-regeneration.log'))`

### 3. Cron Job Setup (Required)

For the scheduled task to run automatically, you need to add this to your server's crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

#### How to set up the cron job:

1. **Edit crontab**:
   ```bash
   crontab -e
   ```

2. **Add the Laravel scheduler**:
   ```bash
   * * * * * cd /Users/alif/Desktop/Project/Github/bmmb_digital_form && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **Verify the cron job is running**:
   ```bash
   php artisan schedule:list
   ```

### 4. Manual Testing

You can manually test the regeneration command:

```bash
php artisan qr-codes:regenerate
```

### 5. What Gets Regenerated

- All QR codes with `status = 'active'`
- Old QR code images are deleted
- New QR code images are generated and saved
- QR code content is regenerated based on type (branch, url, text, etc.)

### 6. Logs

Regeneration logs are stored at:
- `storage/logs/qr-codes-regeneration.log`

### 7. Notes

- Only active QR codes are regenerated
- Branch QR codes are regenerated with the latest branch URL
- The process prevents overlapping to avoid conflicts
- Old QR code image files are automatically deleted

