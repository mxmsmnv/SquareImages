# Changelog

All notable changes to the SquareImages module will be documented in this file.

## [1.3.0] - 2026-05-16

### Added
- Added explicit `['mode' => 'contain']` option for padded square output.
- Added EXIF orientation handling for JPEG sources before resizing.

### Changed
- `square()` now returns `null` on generation failure instead of silently returning the original image.
- Default square generation now performs centered crop-to-fill output, matching the documented smart-cropping behavior.
- Square cache filenames now include the generation mode to avoid crop/contain collisions.

### Fixed
- Fixed lock cleanup on early returns during image generation.
- Fixed WebP handling when the server can read WebP but cannot save WebP.
- Hardened assets-path validation against prefix matches.
- Made `getSquareGallery()` tolerant of single-image fields, invalid field values, and non-image items.
- Escaped dynamic values in the test template.
- Corrected invalid size examples in the documentation.

## [1.2.0] - 2025-12-27

### Added
- Comprehensive test suite (`test-squareimages.php`)
- WebP conversion support and testing
- Format badges displaying file types in tests
- All-images processing in Test 1 and Test 2
- Performance benchmarks
- Detailed documentation with real-world examples
- Coca-Cola bottle story explaining module origin
- Visual examples and use cases

### Changed
- Improved test coverage (50+ tests)
- Enhanced documentation structure
- Better error handling

### Fixed
- WebP filename conflicts with unique sizing
- Test consistency across all image formats

## [1.1.0] - 2025

### Added
- `getSquareURL()` method for faster URL generation
- Performance optimizations

### Changed
- Improved method inheritance patterns

### Fixed
- Minor bugs in URL generation

## [1.0.0] - 2025

### Added
- Initial release
- `square()` method for creating square images
- `squareWidth()` method
- `squareHeight()` method
- Basic documentation
- MIT License

### Features
- Smart cropping algorithm
- Format preservation (JPG, PNG, GIF)
- Automatic caching
- ProcessWire 3.x compatibility
