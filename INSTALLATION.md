# Installation Guide

Complete installation instructions for the SquareImages module.

---

## Requirements

- ProcessWire 3.0 or higher
- PHP 8.2 or higher
- GD or ImageMagick extension

---

## Installation Methods

### Method 1: Direct Download (Recommended)

1. Download the `SquareImages-v1.3.0.zip` file
2. Extract the archive
3. Upload the `SquareImages` folder to `/site/modules/`
4. Log in to your ProcessWire admin
5. Go to **Modules** → **Refresh**
6. Find **Square Images** and click **Install**

### Method 2: Manual Installation

1. Clone or download this repository
2. Place the module files in `/site/modules/SquareImages/`
3. Ensure the folder structure is:
   ```
   /site/modules/SquareImages/
   ├── SquareImages.module.php
   ├── README.md
   ├── LICENSE
   └── ...
   ```
4. In ProcessWire admin, go to **Modules** → **Refresh**
5. Install the **Square Images** module

---

## Post-Installation

### 1. Verify Installation

After installation, the module should appear in your modules list with:
- **Title:** Square Images
- **Version:** 1.3.0
- **Author:** Maxim Semenov

### 2. Test the Module

Create a test page with the test file:

```php
// In your template file
include('./test-squareimages.php');
```

**Upload test images** (like the included Coca-Cola bottles) and verify:
- All images appear in Test 0
- Different sizes work in Test 1
- All methods work in Test 2
- WebP conversion works in Test 2B

### 3. Check Permissions

Ensure ProcessWire can write to:
```
/site/assets/files/[page-id]/
```

This is where square images will be cached.

---

## Configuration

No configuration needed! The module works out of the box.

### Optional: Enable WebP Support

For WebP conversion, ensure your server supports it:

**Check GD support:**
```php
<?php
$info = gd_info();
echo $info['WebP Support'] ? 'WebP: Yes' : 'WebP: No';
?>
```

**Check ImageMagick support:**
```bash
convert -list format | grep WEBP
```

If WebP is not supported, `->webp()` will return the original format.

---

## Usage

### Basic Example

```php
// Get an image
$image = $page->images->first();

// Create a 500×500 square
$square = $image->square(500);

// Output
echo "<img src='{$square->url}' alt='Square Image'>";
```

### All Available Methods

```php
// Method 1: square(size)
$square = $image->square(400);

// Method 2: squareWidth(width)
$square = $image->squareWidth(400);

// Method 3: squareHeight(height)
$square = $image->squareHeight(400);

// Method 4: getSquareURL(size) - returns URL string directly
$url = $image->getSquareURL(400);
```

### WebP Conversion

```php
$square = $image->square(500);
$webp = $square->webp();

// Use with picture element
?>
<picture>
    <source srcset="<?= $webp->url ?>" type="image/webp">
    <img src="<?= $square->url ?>" alt="Image">
</picture>
```

---

## Testing

### Using Test Suite

1. Upload test images to a page (JPG, PNG, GIF formats recommended)
2. Add to your template:
   ```php
   <?php include('./test-squareimages.php'); ?>
   ```
3. View the page in your browser
4. Verify all tests pass

### Expected Results

With 3 test images (like the Coca-Cola bottles):
- **Test 0:** Shows 3 original images
- **Test 1:** Shows 18 squares (6 sizes × 3 images)
- **Test 1B:** Shows 3 squares at 500×500
- **Test 2:** Shows 12 results (4 methods × 3 images)
- **Test 2B:** Shows 3 WebP conversions

**Total:** 50+ tests should pass with ~95%+ success rate

---

## Troubleshooting

### Images Not Appearing

**Problem:** Square images don't appear  
**Solution:**
1. Check file permissions on `/site/assets/files/`
2. Verify GD or ImageMagick is installed
3. Check ProcessWire's image settings

### WebP Not Working

**Problem:** `->webp()` returns original format  
**Solution:**
1. Check if server supports WebP:
   ```php
   var_dump(gd_info()['WebP Support']);
   ```
2. Update GD library if needed
3. Or use ImageMagick instead

### Memory Errors

**Problem:** PHP memory errors with large images  
**Solution:**
1. Increase PHP memory limit in `php.ini`:
   ```ini
   memory_limit = 256M
   ```
2. Or in your template:
   ```php
   ini_set('memory_limit', '256M');
   ```

### Cache Issues

**Problem:** Changes not appearing  
**Solution:**
1. Clear ProcessWire image cache:
   - Setup → Files → Clear Image Cache
2. Or use a different size to force regeneration
3. Or manually delete cached files in `/site/assets/files/[page-id]/`

---

## Upgrading

### From v1.0.0, v1.1.0, or v1.2.0 to v1.3.0

1. **Backup** your site first!
2. Replace the module files in `/site/modules/SquareImages/`
3. In ProcessWire admin, go to **Modules**
4. **Refresh** the modules list
5. Module should show as version 1.3.0

**No database changes** required - all changes are code-only.

**Existing square images** will continue to work - no regeneration needed.

---

## Uninstallation

### To Completely Remove

1. In ProcessWire admin, go to **Modules**
2. Find **Square Images**
3. Click **Uninstall**
4. Delete `/site/modules/SquareImages/` folder

**Note:** Generated square images will remain in `/site/assets/files/` until manually deleted.

### To Remove Only Generated Images

Clear all cached square images:
1. Setup → Files → Clear Image Cache
2. Select "All" or specific pages
3. Click "Clear"

Or manually delete `.400x400sq.jpg` files (and similar) from:
```
/site/assets/files/[page-id]/
```

---

## Support

### Getting Help

- **Email:** maxim@smnv.org
- **Website:** [smnv.org](https://smnv.org)
- **Documentation:** See README.md and EXAMPLES.md

### Reporting Issues

When reporting issues, please include:
1. ProcessWire version
2. PHP version
3. Module version
4. Error messages (from browser console or PHP logs)
5. Steps to reproduce

### Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

## Next Steps

1. ✅ Module installed
2. ✅ Tests passed
3. ✅ Basic usage understood

**Now what?**

- Read [EXAMPLES.md](EXAMPLES.md) for real-world code
- Check [README.md](README.md) for full documentation
- Start creating perfect squares! 🎯

---

**Made with ❤️ by Maxim Semenov**

*Installation made simple.*
