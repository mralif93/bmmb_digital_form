# Offline Mode - PWA Implementation

## Overview

The BMMB Digital Forms application now supports offline functionality through Progressive Web App (PWA) technology. Users can access and use the application even without an internet connection.

## Features

### ✅ What Works Offline

1. **Cached Pages**: Previously visited pages are accessible offline
2. **Form Templates**: All form templates (DAR, DCR, RAF, SRF) can be viewed offline
3. **Static Assets**: CSS, JavaScript, and images are cached for offline use
4. **Login/Register Pages**: Authentication pages are available offline
5. **Responsive Design**: All UI components work seamlessly offline

### 📱 PWA Capabilities

- **Installable**: Users can install the app on their devices
- **Standalone Mode**: App runs in its own window without browser UI
- **Background Sync**: Forms can be queued and submitted when back online
- **Offline-First**: Intelligent caching strategy for best offline experience

## How It Works

### Service Worker (`sw.js`)

The service worker handles:
- **Static Caching**: Caches essential assets on first visit
- **Dynamic Caching**: Caches pages as you visit them
- **Network Strategies**:
  - **Cache First**: For static assets (CSS, JS, images)
  - **Network First**: For HTML pages and API calls
- **Offline Fallback**: Shows offline page when network fails

### Cached Resources

On first visit, the service worker caches:
- Homepage and main navigation
- All form pages (DAR, DCR, RAF, SRF)
- Login and Register pages
- Offline fallback page
- Static assets from CDNs

## Installation

### For Users

1. **Automatic**: The service worker installs automatically on first visit
2. **Manual Install**:
   - Visit the website on mobile or desktop
   - Look for "Add to Home Screen" or "Install" prompt
   - Click to install the app

### For Developers

1. **Service Worker**: Located at `/public/sw.js`
2. **Manifest**: Located at `/public/manifest.json`
3. **Registration**: Handled by `/public/register-sw.js`

## Testing Offline Mode

### Method 1: Chrome DevTools
1. Open Chrome DevTools (F12)
2. Go to "Application" tab
3. Select "Service Workers"
4. Check "Offline" checkbox
5. Refresh the page

### Method 2: Network Throttling
1. Open Chrome DevTools
2. Go to "Network" tab
3. Select "Offline" from dropdown
4. Test the application

### Method 3: Disconnect Network
1. Disable WiFi/Network on device
2. Open the installed app
3. Verify cached content loads

## Cache Management

### Clear Cache
1. Open Chrome DevTools
2. Go to "Application" tab
3. Click "Storage" → "Clear site data"
4. Refresh the page to rebuild cache

### View Cache
1. Open Chrome DevTools
2. Go to "Application" tab
3. Expand "Cache Storage"
4. View cached resources

## Service Worker Strategies

### Cache First Strategy
Used for:
- Static assets (CSS, JS, images)
- Fonts and icons
- CDN resources

### Network First Strategy
Used for:
- HTML pages
- API responses
- Dynamic content

### Fallback Strategy
- Shows offline page when network fails
- Retries connection automatically
- Queues failed requests for later

## Browser Support

| Browser | Support |
|---------|---------|
| Chrome | ✅ Full Support |
| Edge | ✅ Full Support |
| Firefox | ✅ Full Support |
| Safari | ✅ iOS 16.4+ |
| Opera | ✅ Full Support |

## Troubleshooting

### Service Worker Not Installing
- Check browser console for errors
- Verify HTTPS (required for service workers)
- Check if service worker is already installed
- Clear browser cache

### Content Not Caching
- Check if resources are accessible
- Verify cache names match
- Check for CORS errors
- Review browser console

### Offline Page Not Showing
- Verify `/public/offline.html` exists
- Check service worker registration
- Clear cache and reinstall

## Future Enhancements

- [ ] IndexedDB for form data storage
- [ ] Background sync for form submissions
- [ ] Push notifications
- [ ] Web Share API integration
- [ ] Periodic background sync

## Files Created

1. **`/public/sw.js`** - Service worker for offline functionality
2. **`/public/manifest.json`** - PWA manifest file
3. **`/public/offline.html`** - Offline fallback page
4. **`/public/register-sw.js`** - Service worker registration script

## Configuration

### Manifest Customization

Edit `/public/manifest.json` to customize:
- App name and description
- Theme colors
- Icons and screenshots
- Display mode
- Shortcuts

### Service Worker Caching

Edit `/public/sw.js` to customize:
- Cache names and versions
- Assets to pre-cache
- Caching strategies
- Background sync logic

## Security Considerations

- Service workers require HTTPS (except localhost)
- Cache data can be large - consider cleanup strategies
- Be careful with caching sensitive data
- Implement proper cache expiration

## Performance Tips

1. **Pre-cache Critical Assets**: Add important files to STATIC_ASSETS
2. **Lazy Load Images**: Don't cache large images immediately
3. **Version Your Caches**: Update CACHE_NAME when changing assets
4. **Monitor Cache Size**: Clean old caches regularly
5. **Use Compression**: Enable gzip for better caching

## Support

For issues or questions:
- Check browser console for errors
- Review service worker status in DevTools
- Clear cache and reinstall
- Contact development team

---

**Last Updated**: {{ date('Y-m-d') }}
**Version**: 1.0.0

