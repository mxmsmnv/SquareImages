# SquareImages for ProcessWire

**Version:** 1.3.0
**Author:** Maxim Alex  
**Website:** [smnv.org](https://smnv.org)  
**Email:** maxim@smnv.org  
**Release Date:** May 16, 2026
**License:** MIT

A ProcessWire module that creates perfect square images from any source format. Born from the real-world need to display vertical product images (like Coca-Cola bottles) uniformly in galleries and grids.

---

## 📖 The Story: From Pain to Solution

### The Problem

Imagine you're building an e-commerce site selling beverages. You have product photos:

```
Coca-Cola Bottle (JPG):  500 × 1500px  (vertical)
Coca-Cola Bottle (PNG):  600 × 1800px  (vertical)
Coca-Cola Bottle (GIF):  450 × 1350px  (vertical)
```

Your product grid needs **uniform squares** for a clean layout:

```
┌──────────┐ ┌──────────┐ ┌──────────┐
│          │ │          │ │          │
│  BOTTLE  │ │  BOTTLE  │ │  BOTTLE  │
│          │ │          │ │          │
└──────────┘ └──────────┘ └──────────┘
  300×300      300×300      300×300
```

But if you just resize:

```
❌ Option 1: width(300)
┌───┐ ┌───┐ ┌───┐
│ | │ │ | │ │ | │  ← Too narrow!
│ | │ │ | │ │ | │
│ | │ │ | │ │ | │
│ | │ │ | │ │ | │
└───┘ └───┘ └───┘
100×300  100×300  100×300

❌ Option 2: height(300)
┌──────┐
│BOTTLE│  ← Too wide!
└──────┘
900×300

❌ Option 3: size(300, 300)
┌──────────┐
│  BOTTLE  │  ← Distorted!
└──────────┘
300×300 (stretched)
```

**None of these work!** You need smart cropping that:
1. Finds the shorter dimension
2. Centers the crop on the longer dimension
3. Creates a perfect square
4. Preserves aspect ratio
5. Maintains quality

### The Solution: SquareImages

```php
// Simple, elegant, perfect
$square = $image->square(300);
```

**Result:**
```
✅ Perfect squares, centered crop, no distortion

┌──────────┐ ┌──────────┐ ┌──────────┐
│          │ │          │ │          │
│  BOTTLE  │ │  BOTTLE  │ │  BOTTLE  │
│          │ │          │ │          │
└──────────┘ └──────────┘ └──────────┘
  300×300      300×300      300×300
```

**This module was born from this exact use case** - transforming vertical Coca-Cola bottle photos into uniform squares for a product gallery. Now it's battle-tested and ready for your projects.

---

## ✨ Features

- **🎯 Perfect Squares**: Always creates exactly square images
- **🔄 Smart Cropping**: Automatically centers a square crop on the longer dimension
- **📦 Format Preservation**: Maintains original format (JPG→JPG, PNG→PNG, GIF→GIF)
- **🌐 WebP Support**: Chain with `->webp()` for optimal compression
- **⚡ Performance**: Cached square files and direct URL helper
- **🎨 Quality**: Uses GD with configurable JPEG/WebP quality and PNG compression
- **💾 Caching**: Automatic caching of generated images
- **🔧 Multiple Methods**: `square()`, `squareWidth()`, `squareHeight()`, `getSquareURL()`

---

## 📦 Installation

### Method 1: ProcessWire Modules Directory
1. Download the module
2. Place in `/site/modules/SquareImages/`
3. Install via Modules → Refresh → Install

### Method 2: Manual
1. Download ZIP
2. Extract to `/site/modules/SquareImages/`
3. Refresh modules in admin
4. Install

---

## 🚀 Quick Start

### Basic Usage

```php
// Get any image
$image = $page->images->first();

// Create 500×500 square
$square = $image->square(500);

// Use it
echo "<img src='{$square->url}' alt='Product'>";
```

### Real-World Example: Product Gallery

```php
<div class="product-grid">
    <?php foreach ($page->products as $product): ?>
        <?php $thumb = $product->photo->square(300); ?>
        <div class="product-card">
            <img src="<?= $thumb->url ?>" alt="<?= $product->title ?>">
            <h3><?= $product->title ?></h3>
            <p>$<?= $product->price ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

**CSS:**
```css
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.product-card img {
    width: 100%;
    height: auto;
}
```

**Result:** Perfect grid of uniform squares, regardless of original image dimensions!

---

## 📚 API Documentation

### Methods

#### `square($size)`
Creates a square image of specified size.

```php
$square = $image->square(400);
// Returns: Pageimage object (400×400)
```

By default this crops the center of the image to fill the square. To preserve
the whole image and add transparent/white padding instead:

```php
$square = $image->square(400, ['mode' => 'contain']);
```

#### `squareWidth($width)`
Creates a square based on width dimension.

```php
$square = $image->squareWidth(500);
// Returns: Pageimage object (500×500)
```

#### `squareHeight($height)`
Creates a square based on height dimension.

```php
$square = $image->squareHeight(600);
// Returns: Pageimage object (600×600)
```

#### `getSquareURL($size)`
Returns URL string directly.

```php
$url = $image->getSquareURL(300);
// Returns: String URL
echo "<img src='{$url}'>";
```

### WebP Conversion

Chain with `->webp()` for modern format:

```php
$webp = $image->square(500)->webp();
// Returns: 500×500 WebP image (25-65% smaller!)
```

**Use with `<picture>` for fallback:**
```html
<?php
$square = $image->square(500);
$webp = $square->webp();
?>
<picture>
    <source srcset="<?= $webp->url ?>" type="image/webp">
    <img src="<?= $square->url ?>" alt="Product">
</picture>
```

---

## 💡 Use Cases

### 1. Product Galleries
Uniform product images from mixed aspect ratios:
```php
foreach ($products as $product) {
    $thumb = $product->image->square(250);
    echo "<img src='{$thumb->url}'>";
}
```

### 2. Team Photos
Perfect avatars from portrait photos:
```php
$avatar = $page->photo->square(150);
echo "<img src='{$avatar->url}' class='avatar'>";
```

### 3. Blog Post Thumbnails
Consistent thumbnails from various image sizes:
```php
$thumb = $post->featured_image->square(400);
```

### 4. Social Media Previews
Generate square OG images:
```php
$og = $page->hero->square(1200);
echo "<meta property='og:image' content='{$og->url}'>";
```

### 5. Responsive Galleries
Multiple sizes for responsive design:
```php
$small = $image->square(300);
$medium = $image->square(600);
$large = $image->square(1200);
```

```html
<img 
    srcset="
        <?= $small->url ?> 300w,
        <?= $medium->url ?> 600w,
        <?= $large->url ?> 1200w
    "
    sizes="(max-width: 600px) 300px, (max-width: 1200px) 600px, 1200px"
    src="<?= $medium->url ?>"
>
```

---

## 🎨 Advanced Examples

### E-commerce Product Catalog

```php
<div class="catalog">
    <?php foreach ($page->children('template=product') as $product): ?>
        <?php 
        $thumb = $product->images->first()->square(350);
        $webp = $thumb->webp();
        ?>
        <article class="product">
            <picture>
                <source srcset="<?= $webp->url ?>" type="image/webp">
                <img src="<?= $thumb->url ?>" alt="<?= $product->title ?>">
            </picture>
            <h2><?= $product->title ?></h2>
            <p class="price">$<?= $product->price ?></p>
            <a href="<?= $product->url ?>">View Details</a>
        </article>
    <?php endforeach; ?>
</div>
```

### Instagram-Style Grid

```php
<div class="instagram-grid">
    <?php foreach ($page->gallery as $photo): ?>
        <div class="grid-item">
            <a href="<?= $photo->url ?>">
                <img src="<?= $photo->square(300)->url ?>" loading="lazy">
            </a>
        </div>
    <?php endforeach; ?>
</div>

<style>
.instagram-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
}

.grid-item {
    aspect-ratio: 1;
    overflow: hidden;
}

.grid-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
```

### Multi-Size Thumbnails

```php
<?php
$sizes = [
    'thumbnail' => 150,
    'small' => 300,
    'medium' => 600,
    'large' => 1200
];

$variants = [];
foreach ($sizes as $name => $size) {
    $variants[$name] = $image->square($size);
}
?>

<!-- Use variants -->
<img src="<?= $variants['thumbnail']->url ?>" class="thumb">
<img src="<?= $variants['medium']->url ?>" class="featured">
```

---

## ⚡ Performance Tips

### 1. Use getSquareURL() for Simple Rendering
```php
// Returns string directly
$url = $image->getSquareURL(300);

// Returns a Pageimage object
$square = $image->square(300);
$url = $square->url;
```

### 2. Cache in Variables
```php
// Good
$square = $image->square(500);
echo $square->url;
echo $square->width;
echo $square->height;

// Bad (creates square 3 times!)
echo $image->square(500)->url;
echo $image->square(500)->width;
echo $image->square(500)->height;
```

### 3. Use WebP for Modern Browsers
```php
// 25-65% smaller file sizes!
$webp = $image->square(500)->webp();
```

### 4. Lazy Load Images
```html
<img src="<?= $thumb->url ?>" loading="lazy">
```

---

## 🧪 Testing

The module includes comprehensive test files:

### Test Suite: `test-squareimages.php`
- Tests all image formats (JPG, PNG, GIF)
- Verifies 6 different sizes (100-1000px)
- Tests all 4 methods
- WebP conversion testing
- Automated pass/fail validation

**Place in your template:**
```php
<?php include('./test-squareimages.php'); ?>
```

---

## 🐛 Troubleshooting

### Images Not Square
**Problem:** Images are distorted or not square.  
**Solution:** Check that source image exists and has valid dimensions.

### WebP Not Working
**Problem:** `->webp()` returns original format.  
**Solution:** Ensure GD or ImageMagick supports WebP on your server.

### Cache Issues
**Problem:** Changes not appearing.  
**Solution:** Clear ProcessWire image cache or use different size.

### Memory Errors
**Problem:** Out of memory errors.  
**Solution:** Reduce image size or increase PHP memory_limit.

---

## 📊 Version History

### v1.3.0 (May 16, 2026)
- Added explicit `['mode' => 'contain']` option for padded square output
- Fixed generation failures so `square()` returns `null` instead of the original image
- Changed default output to centered crop-to-fill behavior
- Hardened lock cleanup, WebP fallback, path validation, and gallery handling
- Escaped dynamic values in the test template
- Corrected invalid documentation examples

### v1.2.0 (December 27, 2025)
- Added comprehensive test suite
- WebP conversion support
- Format badges in tests
- All-images processing in tests
- Performance optimizations
- Full documentation

### v1.1.0 (2025)
- Added `getSquareURL()` method
- Performance improvements
- Bug fixes

### v1.0.0 (2025)
- Initial release
- Basic square() functionality

---

## 🤝 Contributing

Contributions welcome! Please:

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Open Pull Request

---

## 📄 License

MIT License

Copyright (c) 2025 Maxim Alex (smnv.org)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

---

## 📞 Support

- **Email:** maxim@smnv.org
- **Website:** [smnv.org](https://smnv.org)

---

## 🙏 Credits

Created to solve a real problem: displaying vertical Coca-Cola bottle photos uniformly in product grids.

Test images (Coca-Cola bottles) used for demonstration purposes only. Coca-Cola® is a registered trademark of The Coca-Cola Company. Not affiliated.

---

**Made with ❤️ by Maxim Alex**

*Transform any image into a perfect square. Simple, elegant, powerful.*
