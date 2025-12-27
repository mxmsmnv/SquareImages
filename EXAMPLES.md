# SquareImages Usage Examples

Practical, copy-paste ready code examples for common scenarios.

---

## Basic Usage

### Simple Square Image

```php
// Get image and create 500×500 square
$image = $page->images->first();
$square = $image->square(500);

echo "<img src='{$square->url}' alt='Product'>";
```

---

## E-commerce Examples

### Product Grid

```php
<div class="product-grid">
    <?php foreach ($page->products as $product): ?>
        <?php $thumb = $product->photo->square(300); ?>
        <div class="product-card">
            <img src="<?= $thumb->url ?>" alt="<?= $product->title ?>">
            <h3><?= $product->title ?></h3>
            <p class="price">$<?= $product->price ?></p>
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
```

### Product with Multiple Sizes

```php
// Thumbnail for grid
$thumb = $product->image->square(250);

// Medium for product page
$medium = $product->image->square(600);

// Large for lightbox
$large = $product->image->square(1200);
```

### WebP for Modern Browsers

```php
<?php
$square = $product->image->square(500);
$webp = $square->webp();
?>

<picture>
    <source srcset="<?= $webp->url ?>" type="image/webp">
    <img src="<?= $square->url ?>" alt="<?= $product->title ?>">
</picture>
```

---

## Gallery Examples

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
```

**CSS:**
```css
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
    transition: transform 0.3s;
}

.grid-item:hover img {
    transform: scale(1.1);
}
```

### Masonry Grid with Uniform Sizes

```php
<div class="masonry">
    <?php foreach ($page->portfolio as $item): ?>
        <?php $thumb = $item->image->square(400); ?>
        <div class="masonry-item">
            <img src="<?= $thumb->url ?>" alt="<?= $item->title ?>">
            <div class="overlay">
                <h3><?= $item->title ?></h3>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

---

## Team/Avatar Examples

### Team Member Grid

```php
<div class="team-grid">
    <?php foreach ($page->children('template=team-member') as $member): ?>
        <?php $avatar = $member->photo->square(200); ?>
        <div class="team-member">
            <img src="<?= $avatar->url ?>" alt="<?= $member->title ?>" class="avatar">
            <h3><?= $member->title ?></h3>
            <p><?= $member->role ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

**CSS:**
```css
.avatar {
    border-radius: 50%;
    width: 100%;
    height: auto;
}
```

### User Profile Avatar

```php
// Small avatar for comments
$avatar_small = $user->photo->square(50);

// Medium avatar for profile
$avatar_medium = $user->photo->square(150);

// Large avatar for full profile
$avatar_large = $user->photo->square(300);
```

---

## Blog Examples

### Blog Post Thumbnails

```php
<div class="blog-grid">
    <?php foreach ($blog->children('limit=12') as $post): ?>
        <?php 
        $thumb = $post->featured_image->square(400);
        $webp = $thumb->webp();
        ?>
        <article class="blog-card">
            <picture>
                <source srcset="<?= $webp->url ?>" type="image/webp">
                <img src="<?= $thumb->url ?>" alt="<?= $post->title ?>">
            </picture>
            <div class="content">
                <h2><?= $post->title ?></h2>
                <p><?= $post->summary ?></p>
                <a href="<?= $post->url ?>">Read More</a>
            </div>
        </article>
    <?php endforeach; ?>
</div>
```

### Related Posts

```php
<aside class="related-posts">
    <h3>Related Articles</h3>
    <?php foreach ($page->siblings('limit=3') as $related): ?>
        <?php $thumb = $related->featured_image->square(150); ?>
        <div class="related-item">
            <img src="<?= $thumb->url ?>">
            <a href="<?= $related->url ?>"><?= $related->title ?></a>
        </div>
    <?php endforeach; ?>
</aside>
```

---

## Responsive Examples

### Responsive Images with srcset

```php
<?php
$small = $image->square(300);
$medium = $image->square(600);
$large = $image->square(1200);
?>

<img 
    srcset="
        <?= $small->url ?> 300w,
        <?= $medium->url ?> 600w,
        <?= $large->url ?> 1200w
    "
    sizes="(max-width: 600px) 300px, (max-width: 1200px) 600px, 1200px"
    src="<?= $medium->url ?>"
    alt="Responsive Square"
>
```

### Picture Element with WebP

```php
<?php
$square_300 = $image->square(300);
$square_600 = $image->square(600);
$webp_300 = $square_300->webp();
$webp_600 = $square_600->webp();
?>

<picture>
    <source 
        type="image/webp"
        srcset="<?= $webp_300->url ?> 300w, <?= $webp_600->url ?> 600w"
        sizes="(max-width: 600px) 300px, 600px"
    >
    <source 
        srcset="<?= $square_300->url ?> 300w, <?= $square_600->url ?> 600w"
        sizes="(max-width: 600px) 300px, 600px"
    >
    <img src="<?= $square_600->url ?>" alt="Image">
</picture>
```

---

## SEO Examples

### Open Graph Images

```php
<?php
// Create square OG image (1200×1200 recommended for Facebook/LinkedIn)
$og_image = $page->featured_image->square(1200);
?>

<meta property="og:image" content="<?= $og_image->httpUrl ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="1200">
<meta property="og:image:type" content="image/jpeg">
```

### Twitter Card Images

```php
<?php
// Twitter summary card (1:1 aspect ratio)
$twitter_image = $page->featured_image->square(800);
?>

<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="<?= $twitter_image->httpUrl ?>">
```

---

## Performance Optimization

### Lazy Loading

```php
<?php foreach ($page->gallery as $photo): ?>
    <?php $thumb = $photo->square(400); ?>
    <img 
        src="<?= $thumb->url ?>" 
        loading="lazy"
        alt="Gallery photo"
    >
<?php endforeach; ?>
```

### Using getSquareURL() for Speed

```php
// Faster - returns URL string directly
<?php foreach ($products as $product): ?>
    <img src="<?= $product->image->getSquareURL(300) ?>">
<?php endforeach; ?>

// Slower - creates object
<?php foreach ($products as $product): ?>
    <?php $square = $product->image->square(300); ?>
    <img src="<?= $square->url ?>">
<?php endforeach; ?>
```

### Caching in Variables

```php
// Good - create once, use multiple times
<?php
$square = $image->square(500);
echo $square->url;
echo $square->width;
echo $square->height;
?>

// Bad - creates square 3 times!
<?php
echo $image->square(500)->url;
echo $image->square(500)->width;
echo $image->square(500)->height;
?>
```

---

## Advanced Examples

### Multiple Formats Gallery

```php
<div class="format-gallery">
    <?php foreach ($page->images as $img): ?>
        <?php
        $square = $img->square(400);
        $webp = $square->webp();
        ?>
        <div class="format-item">
            <h4><?= $img->basename ?></h4>
            
            <!-- Original format -->
            <div class="format-version">
                <p>Original Format (<?= strtoupper($square->ext) ?>)</p>
                <img src="<?= $square->url ?>">
                <p><?= round($square->filesize / 1024, 2) ?> KB</p>
            </div>
            
            <!-- WebP format -->
            <div class="format-version">
                <p>WebP Format</p>
                <img src="<?= $webp->url ?>">
                <p><?= round($webp->filesize / 1024, 2) ?> KB</p>
                <p class="savings">
                    Saved: <?= round((($square->filesize - $webp->filesize) / $square->filesize) * 100, 1) ?>%
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

### Custom Sizes Array

```php
<?php
// Define size variants
$sizes = [
    'thumb' => 150,
    'small' => 300,
    'medium' => 600,
    'large' => 1200
];

// Generate all variants
$variants = [];
foreach ($sizes as $name => $size) {
    $variants[$name] = $image->square($size);
}
?>

<!-- Use variants -->
<img src="<?= $variants['thumb']->url ?>" class="thumbnail">
<img src="<?= $variants['medium']->url ?>" class="featured">
<a href="<?= $variants['large']->url ?>" class="lightbox">View Large</a>
```

### Conditional Sizing

```php
<?php
// Different sizes based on image count
$count = $page->images->count();

if ($count <= 3) {
    $size = 600; // Larger for few images
} elseif ($count <= 9) {
    $size = 400; // Medium for moderate count
} else {
    $size = 200; // Smaller for many images
}

foreach ($page->images as $img) {
    $square = $img->square($size);
    echo "<img src='{$square->url}'>";
}
?>
```

---

## Integration Examples

### With UIKit Modal

```php
<?php foreach ($page->gallery as $photo): ?>
    <?php 
    $thumb = $photo->square(300);
    $large = $photo->square(1200);
    ?>
    <a href="<?= $large->url ?>" data-caption="<?= $photo->description ?>" uk-lightbox>
        <img src="<?= $thumb->url ?>">
    </a>
<?php endforeach; ?>
```

### With Slick Slider

```php
<div class="slider">
    <?php foreach ($page->slideshow as $slide): ?>
        <?php $square = $slide->square(800); ?>
        <div class="slide">
            <img src="<?= $square->url ?>" alt="<?= $slide->title ?>">
        </div>
    <?php endforeach; ?>
</div>

<script>
$('.slider').slick({
    dots: true,
    infinite: true,
    speed: 500,
    slidesToShow: 1,
    adaptiveHeight: true
});
</script>
```

---

## Real-World Scenario: Beverage Store

*The exact use case that inspired this module!*

```php
<!-- Product Catalog -->
<div class="beverage-grid">
    <?php foreach ($page->children('template=product') as $product): ?>
        <?php 
        // Vertical bottle photo → perfect square
        $bottle = $product->photo->square(350);
        $webp = $bottle->webp();
        ?>
        <article class="beverage-card">
            <picture>
                <source srcset="<?= $webp->url ?>" type="image/webp">
                <img src="<?= $bottle->url ?>" alt="<?= $product->title ?>">
            </picture>
            <h2><?= $product->title ?></h2>
            <p class="size"><?= $product->volume ?> ml</p>
            <p class="price">$<?= $product->price ?></p>
            <button>Add to Cart</button>
        </article>
    <?php endforeach; ?>
</div>
```

**Before SquareImages:**
- Coca-Cola bottle: 500×1500px (vertical mess)
- Beer bottle: 600×1800px (different size!)
- Wine bottle: 450×1350px (inconsistent!)

**After SquareImages:**
- All products: 350×350px (perfect grid!)
- Uniform appearance
- Clean, professional layout

---

## Tips & Best Practices

### 1. Choose Appropriate Sizes

```php
// Thumbnails
$thumb = $image->square(150);

// Grid items
$grid = $image->square(300-400);

// Featured images
$featured = $image->square(600-800);

// Full screen
$large = $image->square(1200-1600);
```

### 2. Use WebP When Possible

```php
// 25-65% smaller file sizes!
$webp = $image->square(500)->webp();
```

### 3. Implement Lazy Loading

```php
<img src="<?= $square->url ?>" loading="lazy">
```

### 4. Cache Results

```php
// Cache square in variable if used multiple times
$square = $image->square(500);
```

### 5. Use getSquareURL() for Lists

```php
// Faster for simple rendering
echo "<img src='" . $image->getSquareURL(300) . "'>";
```

---

**Created by Maxim Alex | smnv.org**

*Making images square, one pixel at a time.* 🎯
