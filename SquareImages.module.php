<?php namespace ProcessWire;

/**
 * SquareImages
 * 
 * Creates square thumbnails while maintaining aspect ratio with white/transparent background
 * Adds new methods that don't interfere with ProcessWire core functionality
 * 
 * Born from the real-world need to display vertical product images (like Coca-Cola bottles)
 * uniformly in grids and galleries.
 * 
 * Usage:
 * - $image->square(500);
 * - $image->squareWidth(500);
 * - $image->squareHeight(500);
 * - $image->getSquareURL(500);
 * - $page->getSquareGallery(500, 700);
 * 
 * @author Maxim Alex <maxim@smnv.org>
 * @version 1.3.0
 * @see https://smnv.org
 * @license MIT
 * 
 */

class SquareImages extends WireData implements Module, ConfigurableModule {

	/**
	 * Module information
	 */
	public static function getModuleInfo() {
		return [
			'title' => 'Square Images',
			'summary' => 'Creates perfect square images from any source format with smart cropping',
			'version' => '1.3.0',
			'author' => 'Maxim Alex',
			'href' => 'https://smnv.org',
			'icon' => 'crop',
			'autoload' => true,
			'singular' => true,
			'requires' => 'ProcessWire>=3.0.0',
		];
	}

	/**
	 * Default configuration
	 */
	public function __construct() {
		parent::__construct();
		
		$this->set('defaultSize', 512);
		$this->set('maxSize', 5120);
		$this->set('maxFileSizeMB', 10); // Store in MB, convert when using
		$this->set('jpegQuality', 95);
		$this->set('pngCompression', 9);
		$this->set('skipAnimatedGifs', 0);
		$this->set('placeholderImage', '/site/templates/images/1x1.png');
		$this->set('enableLogging', 0);
	}
	
	/**
	 * Get property - handle checkbox empty strings
	 */
	public function __get($key) {
		$value = parent::__get($key);
		
		// Convert checkbox empty strings to false
		if (($key === 'skipAnimatedGifs' || $key === 'enableLogging') && $value === '') {
			return false;
		}
		
		return $value;
	}

	/**
	 * Initialize the module
	 */
	public function init() {
		// Add new square() method - SAFE, no conflicts with core
		$this->addHook('Pageimage::square', $this, 'hookSquare');
		
		// Add convenient alias methods
		$this->addHook('Pageimage::squareWidth', $this, 'hookSquareWidth');
		$this->addHook('Pageimage::squareHeight', $this, 'hookSquareHeight');
		
		// Add helper method for getting square URL directly
		$this->addHook('Pageimage::getSquareURL', $this, 'hookGetSquareURL');
		
		// Add gallery helper method
		$this->addHook('Page::getSquareGallery', $this, 'hookGetSquareGallery');
	}

	/**
	 * Hook: Pageimage::square($size, $options)
	 * Main method for creating square images
	 * 
	 * @param HookEvent $event
	 */
	public function hookSquare(HookEvent $event) {
		$image = $event->object;
		$size = $this->validateSize($event->arguments(0));
		$options = $event->arguments(1) ?: [];
		if (!is_array($options)) {
			$options = [];
		}
		
		if (!$image || !$image instanceof Pageimage) {
			$event->return = null;
			return;
		}
		
		// Return null on failure so callers can distinguish a failed variation
		// from a valid square image.
		$event->return = $this->createSquareImage($image, $size, $options);
	}
	
	/**
	 * Hook: Pageimage::squareWidth($pixels)
	 * Alias for square() method
	 */
	public function hookSquareWidth(HookEvent $event) {
		$size = $this->validateSize($event->arguments(0));
		$options = $event->arguments(1) ?: [];
		if (!is_array($options)) {
			$options = [];
		}
		$event->arguments = [$size, $options];
		$this->hookSquare($event);
	}
	
	/**
	 * Hook: Pageimage::squareHeight($pixels)
	 * Alias for square() method
	 */
	public function hookSquareHeight(HookEvent $event) {
		$size = $this->validateSize($event->arguments(0));
		$options = $event->arguments(1) ?: [];
		if (!is_array($options)) {
			$options = [];
		}
		$event->arguments = [$size, $options];
		$this->hookSquare($event);
	}
	
	/**
	 * Validate and sanitize size parameter
	 * 
	 * @param mixed $size
	 * @return int
	 */
	protected function validateSize($size) {
		// Strict numeric validation - prevent type juggling exploits
		if (!is_numeric($size) || is_array($size) || is_object($size)) {
			$size = $this->defaultSize;
		}
		
		$size = (int) $size;
		
		// Enforce minimum and maximum
		if ($size < 1) $size = $this->defaultSize;
		if ($size > $this->maxSize) $size = $this->maxSize;
		
		return $size;
	}

	/**
	 * Creates a square image with centered crop by default.
	 * 
	 * @param Pageimage $image The source image
	 * @param int $targetSize Size of the square
	 * @param array $options Additional options
	 * @return Pageimage|null
	 */
	protected function createSquareImage($image, $targetSize, $options = []) {
		
		// Get paths
		$originalPath = $image->filename;
		$originalDir = dirname($originalPath);
		$originalExtension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
		$mode = isset($options['mode']) ? strtolower((string) $options['mode']) : 'crop';
		if (!in_array($mode, ['crop', 'contain'], true)) {
			$mode = 'crop';
		}
		
		// Check file size limit (security)
		$fileSize = @filesize($originalPath);
		if ($fileSize === false) {
			$this->error("Could not determine file size: {$originalPath}");
			return null;
		}
		
		// Convert MB to bytes for comparison
		$maxFileSizeBytes = ((int) $this->maxFileSizeMB) * 1024 * 1024;
		
		// Safety: minimum 1MB
		if ($maxFileSizeBytes < 1048576) {
			$maxFileSizeBytes = 52428800; // 50MB default
		}
		
		if ($fileSize > $maxFileSizeBytes) {
			$sizeInMB = round($fileSize / 1024 / 1024, 2);
			$maxInMB = round($maxFileSizeBytes / 1024 / 1024, 2);
			$this->error("Image file too large: {$sizeInMB}MB (max: {$maxInMB}MB)");
			return null;
		}
		
		// Validate image format
		if (!$this->validateImageFormat($originalPath, $originalExtension)) {
			$this->error("Invalid or unsupported image format: {$originalPath}");
			return null;
		}
		
		// Check for animated GIF
		if ($originalExtension === 'gif' && $this->isAnimatedGif($originalPath)) {
			if ($this->skipAnimatedGifs) {
				if ($this->enableLogging) {
					$this->log("Skipping animated GIF: " . basename($originalPath));
				}
				return null;
			} else {
				if ($this->enableLogging) {
					$this->log("Animated GIF detected, processing first frame only: " . basename($originalPath));
				}
			}
		}
		
		// Get clean base name (without existing variations)
		$basename = $image->basename(false); // Gets original name without variations
		$nameWithoutExt = pathinfo($basename, PATHINFO_FILENAME);
		
		// Determine actual extension (WebP might fallback to JPG)
		$actualExtension = $originalExtension;
		if ($originalExtension === 'webp' && !function_exists('imagecreatefromwebp')) {
			$this->error("WebP read support is not available: {$originalPath}");
			return null;
		}
		if ($originalExtension === 'webp' && !function_exists('imagewebp')) {
			$actualExtension = 'jpg';
			if ($this->enableLogging) {
				$this->log("WebP not supported, will save as JPEG");
			}
		}
		
		// Create cached filename with ACTUAL extension
		$modeSuffix = $mode === 'contain' ? 'contain' : 'crop';
		$cachedName = "{$nameWithoutExt}.{$targetSize}x{$targetSize}sq-{$modeSuffix}.{$actualExtension}";
		$cachedPath = "{$originalDir}/{$cachedName}";
		
		// Early exit if cached version exists
		if (file_exists($cachedPath)) {
			if ($this->enableLogging) {
				$this->log("Using cached square image: {$cachedName}");
			}
			// Return Pageimage object from cache
			return $this->getPageimageFromPath($image, $cachedPath);
		}
		
		// CRITICAL: Use file locking to prevent race condition
		// Multiple simultaneous requests for same image could corrupt the file
		$lockFile = $cachedPath . '.lock';
		$lockHandle = @fopen($lockFile, 'w');
		
		if ($lockHandle === false) {
			$this->error("Cannot create lock file: {$lockFile}");
			return null;
		}
		
		// Try to get exclusive lock (non-blocking)
		if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
			// Another process is creating this image, wait for it
			if ($this->enableLogging) {
				$this->log("Waiting for another process to create: {$cachedName}");
			}
			
			// Wait for shared lock (blocks until other process finishes)
			flock($lockHandle, LOCK_SH);
			flock($lockHandle, LOCK_UN);
			fclose($lockHandle);
			
			// Check if file was created successfully
			if (file_exists($cachedPath)) {
				if ($this->enableLogging) {
					$this->log("Using cached square image created by another process: {$cachedName}");
				}
				return $this->getPageimageFromPath($image, $cachedPath);
			}
			
			// File still doesn't exist, something went wrong
			$this->error("Race condition: file not created by other process");
			return null;
		}
		
		// We have exclusive lock, proceed with creation
		
		// Check directory is writable
		if (!is_writable($originalDir)) {
			$this->error("Directory not writable: {$originalDir}");
			// Release lock if we have it
			if (isset($lockHandle) && $lockHandle) {
				flock($lockHandle, LOCK_UN);
				fclose($lockHandle);
				@unlink($lockFile);
			}
			return null;
		}
		
		// SECURITY: Prevent symlink attacks
		// Ensure the cached path is within the assets directory
		$realCachedDir = realpath($originalDir);
		$realAssetsPath = realpath($this->wire('config')->paths->assets);
		
		if (!$this->isPathWithin($realCachedDir, $realAssetsPath)) {
			$this->error("Security: Invalid path detected");
			// Release lock
			$this->releaseLock($lockHandle, $lockFile);
			return null;
		}
		
		// Sanitize filename to prevent directory traversal
		$safeCachedName = basename($cachedName);
		$safeCachedName = str_replace(["\0", '..'], '', $safeCachedName);
		$cachedPath = $realCachedDir . DIRECTORY_SEPARATOR . $safeCachedName;
		
		// Create GD resource based on format
		$src = $this->createGDResource($originalPath, $originalExtension);
		if (!$src) {
			$this->error("Failed to create GD resource from: {$originalPath}");
			// Release lock
			$this->releaseLock($lockHandle, $lockFile);
			return null;
		}

		$src = $this->applyExifOrientation($src, $originalPath, $originalExtension);
		
		// Get actual dimensions
		$srcWidth = imagesx($src);
		$srcHeight = imagesy($src);
		
		// Validate dimensions (check for false, zero, negative, or unreasonably large)
		if ($srcWidth === false || $srcHeight === false || 
		    $srcWidth < 1 || $srcHeight < 1 ||
		    $srcWidth > 100000 || $srcHeight > 100000) {
			imagedestroy($src);
			$this->releaseLock($lockHandle, $lockFile);
			$this->error("Invalid image dimensions: {$srcWidth}x{$srcHeight}");
			return null;
		}
		
		// Optimization: if image is already the target size, return original
		if ($srcWidth === $targetSize && $srcHeight === $targetSize) {
			imagedestroy($src);
			$this->releaseLock($lockHandle, $lockFile);
			if ($this->enableLogging) {
				$this->log("Image already target size, using original");
			}
			return $image;
		}
		
		// Calculate proportional dimensions. Crop fills the square, contain pads it.
		if ($mode === 'contain') {
			$scale = min($targetSize / $srcWidth, $targetSize / $srcHeight);
			$newWidth = (int) round($srcWidth * $scale);
			$newHeight = (int) round($srcHeight * $scale);
			$srcX = 0;
			$srcY = 0;
			$copySrcWidth = $srcWidth;
			$copySrcHeight = $srcHeight;
			$dstX = (int) round(($targetSize - $newWidth) / 2);
			$dstY = (int) round(($targetSize - $newHeight) / 2);
		} else {
			$cropSize = min($srcWidth, $srcHeight);
			$srcX = (int) floor(($srcWidth - $cropSize) / 2);
			$srcY = (int) floor(($srcHeight - $cropSize) / 2);
			$copySrcWidth = $cropSize;
			$copySrcHeight = $cropSize;
			$newWidth = $targetSize;
			$newHeight = $targetSize;
			$dstX = 0;
			$dstY = 0;
		}
		
		// Ensure dimensions are valid after calculation
		if ($newWidth < 1 || $newHeight < 1 || $newWidth > $targetSize || $newHeight > $targetSize) {
			imagedestroy($src);
			$this->releaseLock($lockHandle, $lockFile);
			$this->error("Calculated dimensions invalid: {$newWidth}x{$newHeight}");
			return null;
		}
		
		// Create destination image
		$dst = imagecreatetruecolor($targetSize, $targetSize);
		if ($dst === false) {
			imagedestroy($src);
			$this->error("Failed to create destination image");
			// Release lock
			$this->releaseLock($lockHandle, $lockFile);
			return null;
		}
		
		// Handle transparency based on format
		if ($actualExtension === 'png' || $actualExtension === 'webp') {
			// Alpha channel transparency (PNG, WebP)
			imagealphablending($dst, false);
			imagesavealpha($dst, true);
			$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
			imagefill($dst, 0, 0, $transparent);
		} elseif ($actualExtension === 'gif') {
			// Indexed transparency (GIF)
			$transparentIndex = imagecolortransparent($src);
			if ($transparentIndex >= 0) {
				// Get the transparent color
				$transparentColor = imagecolorsforindex($src, $transparentIndex);
				
				// Validate color array (imagecolorsforindex can return false or incomplete array)
				if ($transparentColor !== false && 
				    isset($transparentColor['red'], $transparentColor['green'], $transparentColor['blue'])) {
					// Allocate same color in destination
					$transparentNew = imagecolorallocate($dst, 
						$transparentColor['red'], 
						$transparentColor['green'], 
						$transparentColor['blue']
					);
					
					if ($transparentNew !== false) {
						// Fill background and set as transparent
						imagefill($dst, 0, 0, $transparentNew);
						imagecolortransparent($dst, $transparentNew);
					} else {
						// Fallback to white if color allocation failed
						$white = imagecolorallocate($dst, 255, 255, 255);
						imagefill($dst, 0, 0, $white);
					}
				} else {
					// Invalid transparent color, use white background
					$white = imagecolorallocate($dst, 255, 255, 255);
					imagefill($dst, 0, 0, $white);
				}
			} else {
				// No transparency - white background
				$white = imagecolorallocate($dst, 255, 255, 255);
				imagefill($dst, 0, 0, $white);
			}
		} else {
			// White background for JPEG and others
			$white = imagecolorallocate($dst, 255, 255, 255);
			imagefill($dst, 0, 0, $white);
		}
		
		// Resample and copy
		$resampled = imagecopyresampled(
			$dst, $src,
			$dstX, $dstY,
			$srcX, $srcY,
			$newWidth, $newHeight,
			$copySrcWidth, $copySrcHeight
		);

		if (!$resampled) {
			imagedestroy($src);
			imagedestroy($dst);
			$this->releaseLock($lockHandle, $lockFile);
			$this->error("Failed to resample image: {$originalPath}");
			return null;
		}
		
		// Save the image based on format (use actual extension, not original)
		$saved = $this->saveGDImage($dst, $cachedPath, $actualExtension);
		
		// Clean up GD resources
		imagedestroy($src);
		imagedestroy($dst);
		
		// Release lock and remove lock file
		$this->releaseLock($lockHandle, $lockFile);
		
		if (!$saved) {
			$this->error("Failed to save square image: {$cachedPath}");
			return null;
		}
		
		if ($this->enableLogging) {
			$this->log("Created square image: {$cachedName}");
		}
		
		// Return as Pageimage object
		return $this->getPageimageFromPath($image, $cachedPath);
	}

	/**
	 * Release an image generation lock and remove its marker file.
	 *
	 * @param resource|null $lockHandle
	 * @param string $lockFile
	 */
	protected function releaseLock($lockHandle, $lockFile) {
		if ($lockHandle) {
			flock($lockHandle, LOCK_UN);
			fclose($lockHandle);
		}
		if ($lockFile) {
			@unlink($lockFile);
		}
	}

	/**
	 * Confirm a path is inside the configured ProcessWire assets directory.
	 *
	 * @param string|false $path
	 * @param string|false $parent
	 * @return bool
	 */
	protected function isPathWithin($path, $parent) {
		if ($path === false || $parent === false) {
			return false;
		}

		$path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$parent = rtrim($parent, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		return strpos($path, $parent) === 0;
	}

	/**
	 * Apply EXIF orientation for JPEG sources before resizing.
	 *
	 * @param resource $src
	 * @param string $path
	 * @param string $extension
	 * @return resource
	 */
	protected function applyExifOrientation($src, $path, $extension) {
		if (!in_array($extension, ['jpg', 'jpeg'], true) || !function_exists('exif_read_data')) {
			return $src;
		}

		$exif = @exif_read_data($path);
		$orientation = isset($exif['Orientation']) ? (int) $exif['Orientation'] : 1;
		$rotated = null;

		switch ($orientation) {
			case 3:
				$rotated = imagerotate($src, 180, 0);
				break;
			case 6:
				$rotated = imagerotate($src, -90, 0);
				break;
			case 8:
				$rotated = imagerotate($src, 90, 0);
				break;
		}

		if ($rotated) {
			imagedestroy($src);
			return $rotated;
		}

		return $src;
	}
	
	/**
	 * Create GD resource from file based on type
	 * 
	 * @param string $path
	 * @param string $extension
	 * @return resource|false
	 */
	protected function createGDResource($path, $extension) {
		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				return @imagecreatefromjpeg($path);
			case 'png':
				return @imagecreatefrompng($path);
			case 'gif':
				return @imagecreatefromgif($path);
			case 'webp':
				if (function_exists('imagecreatefromwebp')) {
					return @imagecreatefromwebp($path);
				}
				break;
		}
		return false;
	}
	
	/**
	 * Save GD image based on format
	 * 
	 * @param resource $image
	 * @param string $path
	 * @param string $extension
	 * @return bool
	 */
	protected function saveGDImage($image, $path, $extension) {
		// Enable progressive/interlaced for better loading
		if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
			imageinterlace($image, 1);
		}
		
		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				return imagejpeg($image, $path, $this->jpegQuality);
			case 'png':
				// Preserve alpha channel for PNG
				imagesavealpha($image, true);
				return imagepng($image, $path, $this->pngCompression);
			case 'gif':
				return imagegif($image, $path);
			case 'webp':
				// WebP support checked earlier, this should only be called if supported
				if (function_exists('imagewebp')) {
					imagesavealpha($image, true);
					return imagewebp($image, $path, $this->jpegQuality);
				}
				// Fallback shouldn't reach here (handled in createSquareImage)
				$this->error("WebP save called but not supported");
				return false;
		}
		return false;
	}
	
	/**
	 * Validate image format matches extension
	 * 
	 * @param string $path File path
	 * @param string $extension Expected extension
	 * @return bool
	 */
	protected function validateImageFormat($path, $extension) {
		$info = @getimagesize($path);
		if (!$info) {
			return false;
		}
		
		// Map extensions to image types
		$typeMap = [
			'jpg' => IMAGETYPE_JPEG,
			'jpeg' => IMAGETYPE_JPEG,
			'png' => IMAGETYPE_PNG,
			'gif' => IMAGETYPE_GIF,
			'webp' => defined('IMAGETYPE_WEBP') ? IMAGETYPE_WEBP : 18,
		];
		
		if (!isset($typeMap[$extension])) {
			return false;
		}
		
		// Check if actual type matches expected
		if ($info[2] !== $typeMap[$extension]) {
			if ($this->enableLogging) {
				$this->log("Format mismatch: expected {$extension}, got type {$info[2]}");
			}
			return false;
		}
		
		// Additional check for CMYK JPEG (not supported by GD)
		if ($extension === 'jpg' || $extension === 'jpeg') {
			if (isset($info['channels']) && $info['channels'] == 4) {
				$this->error("CMYK JPEG not supported: {$path}");
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Check if GIF is animated
	 * 
	 * @param string $path Path to GIF file
	 * @return bool
	 */
	protected function isAnimatedGif($path) {
		if (!($fh = @fopen($path, 'rb'))) {
			return false;
		}
		
		$count = 0;
		// Read file in smaller chunks for better performance
		while (!feof($fh) && $count < 2) {
			$chunk = fread($fh, 1024 * 10); // 10KB chunks (faster than 100KB)
			if ($chunk === false) break; // Error reading
			$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
			// Early exit once we know it's animated
			if ($count >= 2) break;
		}
		
		fclose($fh);
		return $count > 1;
	}
	
	/**
	 * Get Pageimage object from file path
	 * Uses ProcessWire's proper method to create Pageimage
	 * 
	 * @param Pageimage $original
	 * @param string $path
	 * @return Pageimage|null
	 */
	protected function getPageimageFromPath($original, $path) {
		// Get the page and field that owns this image
		$page = $original->page;
		$field = $original->field;
		
		if (!$page || !$field) return null;
		
		// Get the Pageimages object
		$pageimages = $page->getUnformatted($field->name);
		
		if (!$pageimages) return null;
		
		// Get just the filename
		$filename = basename($path);
		
		// Find or create the Pageimage
		foreach ($pageimages as $img) {
			if (basename($img->filename) === $filename) {
				return $img;
			}
		}
		
		// If not found, try to create new Pageimage with the path
		// This works for variations
		$basename = $original->basename();
		$variation = str_replace($basename, $filename, $original->filename);
		
		if (file_exists($variation)) {
			// Clone the original and update its filename
			$new = clone $original;
			$new->setFilename($variation);
			$new->setOriginal($original);
			return $new;
		}
		
		return null;
	}

	/**
	 * Hook: Pageimage::getSquareURL($size)
	 * Returns URL of square image (creates if needed)
	 */
	public function hookGetSquareURL(HookEvent $event) {
		$image = $event->object;
		$size = $this->validateSize($event->arguments(0));
		
		if (!$image || !$image instanceof Pageimage) {
			$event->return = $this->placeholderImage;
			return;
		}
		
		// Use the new square() method - NO core conflicts
		$squareImage = $image->square($size);
		
		$event->return = $squareImage ? $squareImage->url : $image->url;
	}

	/**
	 * Hook: Page::getSquareGallery($thumbSize, $largeSize, $fieldName)
	 * Returns JSON-encoded gallery data
	 */
	public function hookGetSquareGallery(HookEvent $event) {
		$page = $event->object;
		$thumbSize = $this->validateSize($event->arguments(0));
		$largeSize = $this->validateSize($event->arguments(1));
		$fieldName = $event->arguments(2) ?: 'images';
		
		$images = $page->get($fieldName);
		
		if ($images instanceof Pageimage) {
			$images = [$images];
		}

		if (!$images || !is_iterable($images)) {
			$event->return = json_encode([]);
			return;
		}

		if (is_countable($images) && !count($images)) {
			$event->return = json_encode([]);
			return;
		}
		
		$galleryData = [];
		
		foreach ($images as $image) {
			if (!$image instanceof Pageimage) {
				continue;
			}

			// Skip very small images (use smaller of the two sizes as threshold)
			$minSize = min($thumbSize, $largeSize);
			if ($image->width <= $minSize && $image->height <= $minSize) {
				continue;
			}
			
			// Create square versions with error handling
			$thumb = $image->square($thumbSize);
			$large = $image->square($largeSize);
			
			// Skip if either failed
			if (!$thumb || !$large) {
				if ($this->enableLogging) {
					$this->log("Skipped image in gallery: failed to create square versions");
				}
				continue;
			}
			
			$galleryData[] = [
				'photo' => $large->url,
				'thumbnail' => $thumb->url,
				'alt' => $page->title,
				'description' => $page->title
			];
		}
		
		$event->return = json_encode($galleryData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Module configuration
	 */
	public function getModuleConfigInputfields(InputfieldWrapper $inputfields) {
		
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Size Settings';
		$fieldset->icon = 'arrows';
		
		$f = $this->modules->get('InputfieldInteger');
		$f->name = 'defaultSize';
		$f->label = 'Default Size';
		$f->description = 'Default square size when not specified';
		$f->value = $this->defaultSize;
		$f->min = 1;
		$f->columnWidth = 50;
		$fieldset->add($f);
		
		$f = $this->modules->get('InputfieldInteger');
		$f->name = 'maxSize';
		$f->label = 'Maximum Size';
		$f->description = 'Maximum allowed square size (security limit)';
		$f->value = $this->maxSize;
		$f->min = 100;
		$f->max = 10000;
		$f->columnWidth = 50;
		$fieldset->add($f);
		
		$f = $this->modules->get('InputfieldInteger');
		$f->name = 'maxFileSizeMB';
		$f->label = 'Maximum File Size (MB)';
		$f->description = 'Maximum source image file size in megabytes';
		$f->notes = 'Prevents processing of extremely large files (security limit)';
		$f->value = $this->maxFileSizeMB;
		$f->min = 1;
		$f->max = 500;
		$f->columnWidth = 50;
		$fieldset->add($f);
		
		$inputfields->add($fieldset);
		
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Image Quality Settings';
		$fieldset->icon = 'sliders';
		
		$f = $this->modules->get('InputfieldInteger');
		$f->name = 'jpegQuality';
		$f->label = 'JPEG Quality';
		$f->description = 'Quality for JPEG/WebP images (0-100)';
		$f->value = $this->jpegQuality;
		$f->min = 0;
		$f->max = 100;
		$f->columnWidth = 33;
		$fieldset->add($f);
		
		$f = $this->modules->get('InputfieldInteger');
		$f->name = 'pngCompression';
		$f->label = 'PNG Compression';
		$f->description = 'Compression level for PNG (0-9)';
		$f->value = $this->pngCompression;
		$f->min = 0;
		$f->max = 9;
		$f->columnWidth = 34;
		$f->notes = 'Higher values = smaller files, slower processing';
		$fieldset->add($f);
		
		$inputfields->add($fieldset);
		
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'GIF Options';
		$fieldset->icon = 'file-image-o';
		
		$f = $this->modules->get('InputfieldCheckbox');
		$f->name = 'skipAnimatedGifs';
		$f->label = 'Skip Animated GIFs';
		$f->description = 'Do not process animated GIF files (return null instead)';
		$f->notes = 'When disabled, only first frame is processed';
		if ((bool) $this->skipAnimatedGifs) $f->attr('checked', 'checked');
		$fieldset->add($f);
		
		$inputfields->add($fieldset);
		
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Advanced Options';
		$fieldset->icon = 'cog';
		$fieldset->collapsed = Inputfield::collapsedYes;
		
		$f = $this->modules->get('InputfieldText');
		$f->name = 'placeholderImage';
		$f->label = 'Placeholder Image';
		$f->description = 'Path to default placeholder image when image is missing';
		$f->value = $this->placeholderImage;
		$fieldset->add($f);
		
		$f = $this->modules->get('InputfieldCheckbox');
		$f->name = 'enableLogging';
		$f->label = 'Enable Logging';
		$f->description = 'Log square image creation (for debugging)';
		if ((bool) $this->enableLogging) $f->attr('checked', 'checked');
		$fieldset->add($f);
		
		$inputfields->add($fieldset);
		
		return $inputfields;
	}
	
	/**
	 * Called when module is installed
	 */
	public function ___install() {
		// Check GD library
		if (!extension_loaded('gd')) {
			throw new WireException('GD library is required but not installed');
		}
		
		$this->message('SquareImages module installed successfully');
	}
	
	/**
	 * Called when module is uninstalled
	 */
	public function ___uninstall() {
		// Module uninstalled
		$this->message('SquareImages module uninstalled');
	}
}
