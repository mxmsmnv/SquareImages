<?php namespace ProcessWire; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SquareImages Test Suite v1.2</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						'primary': '#2563eb',
						'secondary': '#64748b',
						'success': '#22c55e',
						'error': '#ef4444',
					}
				}
			}
		}
	</script>
</head>
<body class="bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
		
		<!-- Header -->
		<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-8 mb-12 shadow-lg">
			<h1 class="text-4xl font-bold mb-2">SquareImages Module v1.2</h1>
			<p class="text-xl text-blue-100">Complete Test Suite</p>
			<p class="text-sm text-blue-200 mt-2">By Maxim Alex | smnv.org | maxim@smnv.org</p>
		</div>
		
		<?php
		// Get test images
		if ($page->images->count() == 0) {
			echo '<div class="bg-red-100 border-l-4 border-red-500 p-6"><p class="text-red-700 font-bold">❌ No images found on this page! Please upload test images (e.g., Coca-Cola bottles).</p></div>';
			return;
		}
		
		// Start timing
		$startTime = microtime(true);
		$testsRun = 0;
		$testsPassed = 0;
		$testsFailed = 0;
		?>
		
		<!-- Module Info -->
		<div class="mb-12 bg-white shadow-md">
			<h2 class="text-2xl font-bold bg-gray-800 text-white px-6 py-4 mb-6">📋 Module Information</h2>
			<div class="p-6">
				<table class="w-full">
					<thead>
						<tr class="bg-gray-100">
							<th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase">Property</th>
							<th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase">Value</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<tr class="hover:bg-gray-50">
							<td class="px-6 py-4 font-semibold">Module Name</td>
							<td class="px-6 py-4"><?= $modules->getModuleInfo('SquareImages')['title'] ?></td>
						</tr>
						<tr class="hover:bg-gray-50">
							<td class="px-6 py-4 font-semibold">Version</td>
							<td class="px-6 py-4 font-mono"><?= $modules->getModuleInfo('SquareImages')['version'] ?></td>
						</tr>
						<tr class="hover:bg-gray-50">
							<td class="px-6 py-4 font-semibold">Author</td>
							<td class="px-6 py-4">Maxim Alex</td>
						</tr>
						<tr class="hover:bg-gray-50">
							<td class="px-6 py-4 font-semibold">Total Images on Page</td>
							<td class="px-6 py-4"><span class="bg-blue-600 text-white px-3 py-1 font-bold rounded"><?= $page->images->count() ?> images</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<h3 class="text-xl font-bold px-6 py-3 bg-gray-100 border-l-4 border-blue-600">All Images Details:</h3>
			<div class="p-6">
				<table class="w-full">
					<thead>
						<tr class="bg-gray-800 text-white">
							<th class="px-4 py-3 text-left font-bold">#</th>
							<th class="px-4 py-3 text-left font-bold">Filename</th>
							<th class="px-4 py-3 text-left font-bold">Original Size</th>
							<th class="px-4 py-3 text-left font-bold">File Size</th>
							<th class="px-4 py-3 text-left font-bold">Format</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<?php 
						$imageNum = 0;
						foreach ($page->images as $img): 
							$imageNum++;
						?>
						<tr class="hover:bg-gray-50">
							<td class="px-4 py-3 font-bold"><?= $imageNum ?></td>
							<td class="px-4 py-3 font-mono text-sm"><?= $img->basename ?></td>
							<td class="px-4 py-3"><?= $img->width ?>x<?= $img->height ?>px</td>
							<td class="px-4 py-3"><?= round($img->filesize / 1024, 2) ?> KB</td>
							<td class="px-4 py-3"><span class="bg-gray-700 text-white px-2 py-1 font-bold text-xs rounded"><?= strtoupper($img->ext) ?></span></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		
		<!-- Test 0: All Images Overview -->
		<div class="mb-12 bg-white shadow-md">
			<h2 class="text-2xl font-bold bg-gray-800 text-white px-6 py-4 mb-6">🖼️ Test 0: All Page Images (Original)</h2>
			<p class="px-6 mb-6">Showing all <?= $page->images->count() ?> images on this page</p>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
				<?php 
				$imgNum = 0;
				foreach ($page->images as $img): 
					$imgNum++;
				?>
					<div class="border border-gray-300 bg-white shadow">
						<div class="bg-blue-600 text-white px-4 py-2 border-b border-gray-300">
							<h3 class="font-bold">Image #<?= $imgNum ?></h3>
						</div>
						<div class="p-4">
							<img src="<?= $img->url ?>" alt="Original <?= $imgNum ?>" class="w-full border border-gray-200">
							<div class="mt-4 text-sm space-y-1">
								<p class="font-mono text-xs"><?= $img->basename ?></p>
								<p>Original: <span class="font-bold"><?= $img->width ?>x<?= $img->height ?>px</span></p>
								<p><?= round($img->filesize / 1024, 2) ?> KB</p>
								<p><span class="bg-gray-700 text-white px-2 py-1 text-xs font-bold rounded"><?= strtoupper($img->ext) ?></span></p>
							</div>
							<div class="mt-3 pt-3 border-t border-gray-200">
								<p class="text-xs font-mono bg-gray-100 px-3 py-2 font-bold rounded">
									Original image (no square())
								</p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		
		<!-- Test 1: Different Sizes -->
		<div class="mb-12 bg-white shadow-md">
			<h2 class="text-2xl font-bold bg-gray-800 text-white px-6 py-4 mb-6">🔢 Test 1: Different Square Sizes</h2>
			<p class="px-6 mb-6">Testing square() method with various sizes on ALL <?= $page->images->count() ?> images</p>
			
			<?php 
			$imgNum = 0;
			foreach ($page->images as $testImage): 
				$imgNum++;
			?>
				<h3 class="text-xl font-bold px-6 py-3 bg-gray-100 border-l-4 border-blue-600 mx-6 mb-4">Image #<?= $imgNum ?>: <?= $testImage->basename ?></h3>
				<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 px-6 mb-8">
					<?php
					$sizes = [100, 200, 300, 500, 800, 1000];
					foreach ($sizes as $size) {
						$testsRun++;
						$square = $testImage->square($size);
						if ($square && $square->width == $size && $square->height == $size) {
							$testsPassed++;
							$status = '<span class="bg-green-600 text-white px-2 py-1 text-xs font-bold rounded">✓ PASS</span>';
							$borderClass = 'border-green-600';
						} else {
							$testsFailed++;
							$status = '<span class="bg-red-600 text-white px-2 py-1 text-xs font-bold rounded">✗ FAIL</span>';
							$borderClass = 'border-red-600';
						}
						?>
						<div class="border-2 <?= $borderClass ?> shadow">
							<div class="bg-gray-100 px-3 py-2 border-b-2 <?= $borderClass ?>">
								<h4 class="font-bold text-sm"><?= $size ?>x<?= $size ?>px</h4>
							</div>
							<?php if ($square): ?>
								<div class="p-3">
									<img src="<?= $square->url ?>" alt="Square <?= $size ?>" class="w-full border border-gray-200 mb-2">
									<div class="text-xs space-y-1">
										<p>Actual: <span class="font-bold"><?= $square->width ?>x<?= $square->height ?>px</span></p>
										<p><?= round($square->filesize / 1024, 2) ?> KB</p>
										<p><span class="bg-gray-700 text-white px-2 py-1 font-bold rounded"><?= strtoupper($square->ext) ?></span></p>
										<p><?= $status ?></p>
									</div>
									<div class="mt-2 pt-2 border-t border-gray-200">
										<p class="text-xs font-mono bg-blue-100 px-2 py-1 font-bold text-center rounded">
											square(<?= $size ?>)
										</p>
									</div>
								</div>
							<?php else: ?>
								<p class="p-3 text-red-600 font-bold text-xs">Failed to create</p>
							<?php endif; ?>
						</div>
					<?php } ?>
				</div>
			<?php endforeach; ?>
		</div>
		
		<!-- Test 1B: All Images to Square -->
		<div class="mb-12 bg-white shadow-md">
			<h2 class="text-2xl font-bold bg-gray-800 text-white px-6 py-4 mb-6">🎯 Test 1B: All Images → 500x500 Squares</h2>
			<p class="px-6 mb-6">Converting ALL <?= $page->images->count() ?> images to 500x500 squares</p>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
				<?php 
				$imgNum = 0;
				foreach ($page->images as $img): 
					$imgNum++;
					$testsRun++;
					$square = $img->square(500);
					if ($square && $square->width == 500 && $square->height == 500) {
						$testsPassed++;
						$status = '<span class="bg-green-600 text-white px-2 py-1 text-xs font-bold rounded">✓ PASS</span>';
						$borderClass = 'border-green-600';
					} else {
						$testsFailed++;
						$status = '<span class="bg-red-600 text-white px-2 py-1 text-xs font-bold rounded">✗ FAIL</span>';
						$borderClass = 'border-red-600';
					}
				?>
					<div class="border-2 <?= $borderClass ?> shadow">
						<div class="bg-blue-600 text-white px-4 py-2 border-b-2 <?= $borderClass ?>">
							<h3 class="font-bold">Image #<?= $imgNum ?></h3>
						</div>
						<?php if ($square): ?>
							<div class="p-4">
								<img src="<?= $square->url ?>" alt="Square <?= $imgNum ?>" class="w-full border border-gray-200 mb-3">
								<div class="text-sm space-y-1">
									<p>Original: <span class="font-bold"><?= $img->width ?>x<?= $img->height ?>px</span></p>
									<p>Square: <span class="font-bold"><?= $square->width ?>x<?= $square->height ?>px</span></p>
									<p>Size: <?= round($square->filesize / 1024, 2) ?> KB</p>
									<p>Format: <span class="bg-gray-700 text-white px-2 py-1 text-xs font-bold rounded"><?= strtoupper($square->ext) ?></span></p>
									<p class="mt-2"><?= $status ?></p>
								</div>
								<div class="mt-3 pt-3 border-t border-gray-200">
									<p class="text-xs font-mono bg-blue-100 px-3 py-2 font-bold text-center rounded">
										$image->square(500)
									</p>
								</div>
							</div>
						<?php else: ?>
							<p class="p-4 text-red-600 font-bold">Failed to create</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		
		<!-- Test 2: Different Methods -->
		<div class="mb-12 bg-white shadow-md">
			<h2 class="text-2xl font-bold bg-gray-800 text-white px-6 py-4 mb-6">🔨 Test 2: Different Methods</h2>
			<p class="px-6 mb-6">Testing square(), squareWidth(), squareHeight(), getSquareURL() on ALL <?= $page->images->count() ?> images</p>
			
			<?php 
			$imgNum = 0;
			foreach ($page->images as $testImage): 
				$imgNum++;
			?>
				<h3 class="text-xl font-bold px-6 py-3 bg-gray-100 border-l-4 border-blue-600 mx-6 mb-4">Image #<?= $imgNum ?>: <?= $testImage->basename ?></h3>
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-6 mb-8">
					<?php
					$methods = [
						['method' => 'square', 'param' => 400, 'label' => 'square(400)'],
						['method' => 'squareWidth', 'param' => 400, 'label' => 'squareWidth(400)'],
						['method' => 'squareHeight', 'param' => 400, 'label' => 'squareHeight(400)'],
					];
					
					foreach ($methods as $test) {
						$testsRun++;
						$result = $testImage->{$test['method']}($test['param']);
						if ($result) {
							$testsPassed++;
							?>
							<div class="border-2 border-green-600 shadow">
								<div class="bg-gray-100 px-4 py-2 border-b-2 border-green-600">
									<h4 class="font-bold"><?= $test['label'] ?></h4>
								</div>
								<div class="p-4">
									<img src="<?= $result->url ?>" alt="<?= $test['label'] ?>" class="w-full border border-gray-200 mb-2">
									<div class="text-sm">
										<p class="font-bold"><?= $result->width ?>x<?= $result->height ?>px</p>
										<p><?= round($result->filesize / 1024, 2) ?> KB</p>
										<p><span class="bg-gray-700 text-white px-2 py-1 text-xs font-bold rounded"><?= strtoupper($result->ext) ?></span></p>
										<p class="mt-2"><span class="bg-green-600 text-white px-2 py-1 text-xs font-bold rounded">✓ PASS</span></p>
									</div>
									<div class="mt-3 pt-3 border-t border-gray-200">
										<p class="text-xs font-mono bg-blue-100 px-2 py-1 font-bold text-center rounded">
											$image-><?= $test['label'] ?>
										</p>
									</div>
								</div>
							</div>
							<?php
						} else {
							$testsFailed++;
							echo '<div class="border-2 border-red-600 shadow p-4"><h4 class="font-bold">' . $test['label'] . '</h4><p class="text-red-600 font-bold mt-2">✗ FAIL</p></div>';
						}
					}
					
					// Test getSquareURL
					$testsRun++;
					$url = $testImage->getSquareURL(400);
					if ($url && is_string($url)) {
						$testsPassed++;
						$squareForExt = $testImage->square(400);
						?>
						<div class="border-2 border-green-600 shadow">
							<div class="bg-gray-100 px-4 py-2 border-b-2 border-green-600">
								<h4 class="font-bold">getSquareURL(400)</h4>
							</div>
							<div class="p-4">
								<img src="<?= $url ?>" alt="Direct URL" class="w-full border border-gray-200 mb-2">
								<div class="text-sm">
									<p>Returns URL only</p>
									<p><span class="bg-gray-700 text-white px-2 py-1 text-xs font-bold rounded"><?= strtoupper($squareForExt->ext) ?></span></p>
									<p class="mt-2"><span class="bg-green-600 text-white px-2 py-1 text-xs font-bold rounded">✓ PASS</span></p>
								</div>
								<div class="mt-3 pt-3 border-t border-gray-200">
									<p class="text-xs font-mono bg-blue-100 px-2 py-1 font-bold text-center rounded">
										$image->getSquareURL(400)
									</p>
								</div>
							</div>
						</div>
					<?php } else {
						$testsFailed++;
						echo '<div class="border-2 border-red-600 shadow p-4"><h4 class="font-bold">getSquareURL(400)</h4><p class="text-red-600 font-bold mt-2">✗ FAIL</p></div>';
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>
		
		<!-- Test 2B: WebP Conversion -->
		<div class="mb-12 bg-white shadow-md">
			<h2 class="text-2xl font-bold bg-gray-800 text-white px-6 py-4 mb-6">🌐 Test 2B: WebP Conversion</h2>
			<p class="px-6 mb-6">Testing square()->webp() for all image formats</p>
			<?php
			$testsRun++;
			$webpSuccess = true;
			?>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6 mb-8">
				<?php 
				$imgNum = 0;
				foreach ($page->images as $img): 
					$imgNum++;
					$uniqueSize = 400 + ($imgNum * 10);
					$square = $img->square($uniqueSize);
					$webp = $square ? $square->webp() : null;
					
					if (!$webp || strtolower($webp->ext) !== 'webp') {
						$webpSuccess = false;
					}
				?>
					<div class="border-2 <?= $webp ? 'border-green-600' : 'border-red-600' ?> shadow">
						<div class="bg-blue-600 text-white px-4 py-2 border-b-2 <?= $webp ? 'border-green-600' : 'border-red-600' ?>">
							<h3 class="font-bold">Image #<?= $imgNum ?>: <?= strtoupper($img->ext) ?> → WebP</h3>
						</div>
						<?php if ($webp): ?>
							<div class="p-4">
								<img src="<?= $webp->url ?>" alt="WebP <?= $imgNum ?>" class="w-full border border-gray-200 mb-3">
								<div class="text-xs space-y-1 bg-gray-50 p-3 rounded">
									<p class="font-mono text-xs break-all"><?= $webp->basename ?></p>
									<p>Original: <?= $img->ext ?> (<?= round($img->filesize / 1024, 2) ?> KB)</p>
									<p>Square: <?= $square->ext ?> (<?= round($square->filesize / 1024, 2) ?> KB)</p>
									<p>WebP: <?= $webp->ext ?> (<?= round($webp->filesize / 1024, 2) ?> KB)</p>
									<p>Saved: <?= round(($square->filesize - $webp->filesize) / 1024, 2) ?> KB</p>
									<p>Format: <span class="bg-gray-700 text-white px-2 py-1 font-bold rounded"><?= strtoupper($webp->ext) ?></span></p>
									<p class="mt-2 text-green-600 font-bold"><span class="bg-green-600 text-white px-2 py-1 rounded">✓ PASS</span></p>
								</div>
								<div class="mt-3 pt-3 border-t border-gray-200">
									<p class="text-xs font-mono bg-blue-100 px-2 py-1 font-bold text-center rounded">
										$image->square(<?= $uniqueSize ?>)->webp()
									</p>
								</div>
							</div>
						<?php else: ?>
							<p class="p-4 text-red-600 font-bold">Failed to create WebP</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			
			<?php 
			if ($webpSuccess) {
				$testsPassed++;
				echo '<div class="bg-green-100 border-l-4 border-green-600 p-6 mx-6 mb-6"><p class="text-green-700 font-bold text-lg">✅ All formats successfully converted to WebP!</p></div>';
			} else {
				$testsFailed++;
				echo '<div class="bg-red-100 border-l-4 border-red-600 p-6 mx-6 mb-6"><p class="text-red-700 font-bold">❌ Some WebP conversions failed</p></div>';
			}
			?>
			
			<h3 class="text-xl font-bold px-6 py-3 bg-gray-100 border-l-4 border-blue-600 mx-6 mb-4">WebP Size Comparison:</h3>
			<div class="mx-6 overflow-x-auto">
				<table class="w-full text-sm border border-gray-300">
					<thead>
						<tr class="bg-gray-800 text-white">
							<th class="px-4 py-3 text-left font-bold">Image</th>
							<th class="px-4 py-3 text-left font-bold">Format</th>
							<th class="px-4 py-3 text-left font-bold">Original</th>
							<th class="px-4 py-3 text-left font-bold">Square</th>
							<th class="px-4 py-3 text-left font-bold">WebP</th>
							<th class="px-4 py-3 text-left font-bold">Filename</th>
							<th class="px-4 py-3 text-left font-bold">Savings</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<?php 
						$imgNum = 0;
						foreach ($page->images as $img): 
							$imgNum++;
							$uniqueSize = 400 + ($imgNum * 10);
							$square = $img->square($uniqueSize);
							$webp = $square ? $square->webp() : null;
							if (!$webp) continue;
							
							$savingsVsSquare = round((($square->filesize - $webp->filesize) / $square->filesize) * 100, 1);
						?>
						<tr class="hover:bg-gray-50">
							<td class="px-4 py-3 font-bold">#<?= $imgNum ?></td>
							<td class="px-4 py-3"><span class="bg-gray-700 text-white px-2 py-1 font-bold text-xs rounded"><?= strtoupper($img->ext) ?></span></td>
							<td class="px-4 py-3"><?= round($img->filesize / 1024, 2) ?> KB</td>
							<td class="px-4 py-3"><?= round($square->filesize / 1024, 2) ?> KB</td>
							<td class="px-4 py-3 font-bold text-green-600"><?= round($webp->filesize / 1024, 2) ?> KB</td>
							<td class="px-4 py-3 font-mono text-xs"><?= $webp->basename ?></td>
							<td class="px-4 py-3"><span class="bg-green-600 text-white px-2 py-1 font-bold text-xs rounded">-<?= $savingsVsSquare ?>%</span></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		
		<!-- Test Summary -->
		<?php
		$endTime = microtime(true);
		$totalTime = $endTime - $startTime;
		$successRate = $testsRun > 0 ? round(($testsPassed / $testsRun) * 100, 1) : 0;
		?>
		<div class="mb-12 bg-white shadow-md">
			<h2 class="text-2xl font-bold bg-gray-800 text-white px-6 py-4 mb-6">📊 Test Summary</h2>
			<div class="grid grid-cols-2 md:grid-cols-5 gap-4 p-6 mb-8">
				<div class="bg-gray-100 border-2 border-gray-300 p-6 text-center shadow">
					<div class="text-4xl font-bold text-gray-800 mb-2"><?= $testsRun ?></div>
					<div class="text-sm font-bold uppercase text-gray-600">Total Tests</div>
				</div>
				<div class="bg-green-600 border-2 border-green-700 p-6 text-center shadow">
					<div class="text-4xl font-bold text-white mb-2"><?= $testsPassed ?></div>
					<div class="text-sm font-bold uppercase text-white">Passed</div>
				</div>
				<div class="bg-red-600 border-2 border-red-700 p-6 text-center shadow">
					<div class="text-4xl font-bold text-white mb-2"><?= $testsFailed ?></div>
					<div class="text-sm font-bold uppercase text-white">Failed</div>
				</div>
				<div class="bg-blue-600 border-2 border-blue-700 p-6 text-center shadow">
					<div class="text-4xl font-bold text-white mb-2"><?= $successRate ?>%</div>
					<div class="text-sm font-bold uppercase text-white">Success Rate</div>
				</div>
				<div class="bg-gray-800 p-6 text-center shadow">
					<div class="text-4xl font-bold text-white mb-2"><?= round($totalTime * 1000) ?>ms</div>
					<div class="text-sm font-bold uppercase text-gray-300">Total Time</div>
				</div>
			</div>
			
			<div class="p-6">
				<?php if ($successRate == 100): ?>
					<div class="bg-green-600 border-2 border-green-700 p-8 text-center shadow-lg">
						<h2 class="text-3xl font-bold text-white mb-4">🎉 ALL TESTS PASSED! 🎉</h2>
						<p class="text-xl text-white font-semibold">SquareImages module is working perfectly!</p>
					</div>
				<?php elseif ($successRate >= 80): ?>
					<div class="bg-yellow-500 border-2 border-yellow-600 p-8 text-center shadow-lg">
						<h2 class="text-3xl font-bold text-gray-900 mb-4">⚠️ MOST TESTS PASSED</h2>
						<p class="text-xl text-gray-900 font-semibold">Module is mostly working, but some issues detected.</p>
					</div>
				<?php else: ?>
					<div class="bg-red-600 border-2 border-red-700 p-8 text-center shadow-lg">
						<h2 class="text-3xl font-bold text-white mb-4">❌ CRITICAL FAILURES</h2>
						<p class="text-xl text-white font-semibold">Module has significant issues. Check configuration and logs.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- Footer -->
		<div class="border-t-2 border-gray-300 pt-8 mt-12">
			<p class="text-center text-sm text-gray-600">
				SquareImages v<?= $modules->getModuleInfo('SquareImages')['version'] ?> | 
				By Maxim Alex | 
				smnv.org | 
				December 27, 2025
			</p>
		</div>
		
	</div>
</body>
</html>
