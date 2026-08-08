<?php
$base_url = "https://mars.nasa.gov/rss/api/?feed=raw_images&category=mars2020,ingenuity&feedtype=json&ver=1.2";
// Camera Search options and translations:
// -- Entry, Descent and Landing Cameras --
// EDL_RDCAM - Rover Down-Look Camera
// EDL_RUCAM - Rover Up-Look Camera
// EDL_DDCAM - Descent Stage Down-Look Camera
// EDL_PUCAM2 - Parachute Up-Look Camera B
// EDL_PUCAM1 - Parachute Up-Look Camera A
// LCAM - Lander Vision System Camera
//
// -- Science Cameras --
// MCZ_RIGHT - Mastcam-Z-Right
// MCZ_LEFT - Mastcam-Z-Left
// SKYCAM - MEDIA SkyCam
// PIXL_MCC - PIXL Micro Context Camera
// SHERLOC_WATSON - SHERLOC - WATSON
// SHERLOC_ACI - SHERLOC Context Imager
// SHERLOC_RMI - SuperCam Remote Micro Imager
//
// -- Engineering Cameras --
// REAR_HAZCAM_RIGHT - Rear Hazcam Right
// REAR_HAZCAM_LEFT - Rear Hazcam Left
// FRONT_HAZCAM_RIGHT_A - Front Hazcam Right
// FRONT_HAZCAM_LEFT_A - Front Hazcam Left
// NAVCAM_RIGHT - Navigation Camera Right
// NAVCAM_LEFT - Navigation Camera Left
// CACHECAM - Sample Caching System (CacheCam)
//
// -- Mars Helicopter Tech Demo Cameras --
// HELI_NAV - Ingenuity Navigation Camera
// HELI_RTE - Ingenuity Color Camera
//
// -- Process Setting -- 
// &extended=product_type::raw - Raw images
// &extended=product_type::color - Color-Processed

// https://www.php.net/manual/en/function.array-search.php#124961
// https://www.php.net/manual/en/function.array-search.php#120784
// Cameras
$cams = [
'RDLC' => ['api' => 'EDL_RDCAM', 'desc' => 'Rover Down-Look Camera'], 
'RULC' => ['api' => 'EDL_RUCAM', 'desc'  => 'Rover Up-Look Camera'], 
'DDLC' => ['api' => 'EDL_DDCAM', 'desc' => 'Descent Stage Down-Look Camera'],
'PULCA' => ['api' => 'EDL_PUCAM2', 'desc' => 'Parachute Up-Look Camera A'],
'PULCB' => ['api' => 'EDL_PUCAM1', 'desc' => 'Parachute Up-Look Camera B'],
'LVSC' => ['api' => 'LCAM', 'desc' => 'Lander Vision System Camera'],
'MZR' => ['api' => 'MCZ_RIGHT', 'desc' => 'Mastcam-Z-Right'],
'MZL' => ['api' => 'MCZ_LEFT', 'desc' => 'Mastcam-Z-Left'],
'SKYC' => ['api' => 'SKYCAM', 'desc' => 'MEDIA SkyCam'],
'PIXL' => ['api' => 'PIXL_MCC', 'desc' => 'PIXL Micro Context Camera'],
'SWAT' => ['api' => 'SHERLOC_WATSON', 'desc' => 'SHERLOC - WATSON Camera'],
'SIMGR' => ['api' => 'SHERLOC_ACI', 'desc' => 'SHERLOC Context Imager'],
'SCMI' => ['api' => 'SHERLOC_RMI', 'desc' => 'SuperCam Remote Micro Imager'],
'RHR' => ['api' => 'REAR_HAZCAM_RIGHT', 'desc' => 'Rear Hazcam Right'],
'RHL' => ['api' => 'REAR_HAZCAM_LEFT', 'desc' => 'Rear Hazcam Left'],
'FHR' => ['api' => 'FRONT_HAZCAM_RIGHT_A', 'desc' => 'Front Hazcam Right'],
'FHL' => ['api' => 'FRONT_HAZCAM_LEFT_A', 'desc' => 'Front Hazcam Left'],
'NCR' => ['api' => 'NAVCAM_RIGHT', 'desc' => 'Navigation Camera Right'],
'NCL' => ['api' => 'NAVCAM_LEFT', 'desc' => 'Navigation Camera Left'],
'SCS' => ['api' => 'CACHECAM', 'desc' => 'Sample Caching System (CacheCam)'],
'HNAV' => ['api' => 'HELI_NAV', 'desc' => 'Ingenuity Navigation Camera'],
'HCOL' => ['api' => 'HELI_RTE', 'desc' => 'Ingenuity Color Camera']
];

// Arguments stuff
// Setting Defaults
$sol = null;
$rawmode = 'raw';
$selected_cam = null;

foreach ($argv as $arg) {
	// find the sol number, store match in $m
	if (preg_match('/sol([0-9]+)/i', $arg, $m)) {
		$sol = $m[1];
	}
	// Image Mode
	if (preg_match('/^(raw|color)$/i', $arg, $m)) {
		$rawmode = strtolower($m[1]);
	}
	// Camera Codes
	else {
		// Making sure everything is in caps
		$upperArg = strtoupper($arg);
		// if ALL is chosen, add all the arguments
		if ($upperArg === 'ALL' || isset($cams[$upperArg])) {
			$selected_cam = $upperArg;
		}
	}
}

// If no camera is selected, or invalid.. tell the user we done goofed.
if (!$selected_cam) {
	echo "Missing or invalid Camera!\n";
	echo "List of cameras:\n";
	echo sprintf("  %-8s %s\n", "ALL", "All Cameras");
	foreach ($cams as $code => $info) {
		echo sprintf ("  %-8s %s\n", $code, $info['desc']);
	}
	exit(1);
}

// Get current SOL if not given.
if ($sol === null) {
	echo "\nGetting current SOL as none was given\n";
	$solGrab = json_decode(@file_get_contents($base_url . "&num=1"), True);
	if (!isset($solGrab['images'][0]['sol'])) {
		exit("\nUnable to get current/latest SOL...\n");
	}
	$sol = $solGrab['images'][0]['sol'];
	if (isset($sol)) {
		echo "Sol: " . $sol . "\n";
	}
}

// Prepare the search
$search = "";
if ($selected_cam !== 'ALL') {
	$search = "&search=" . $cam[$selected_cam];
}

$queryUrl = "{$base_url}&num=100{$search}&condition_2={$sol}:sol:gte&condition_3={$sol}:sol:lte&extended=sample_type::full,product_type::{$rawmode}";

// Get Pages
$firstPage = json_decode(@file_get_contents($queryUrl . "&page=0"), True);
if (empty($firstPage['images'])) {
	exit("\nNo images matching search for Sol {$sol}.\n");
}

$perPage = $firstPage['per_page'] ?? 100;
$totalImages = $firstPage['total_results'] ?? 0;
// https://www.php.net/manual/en/function.ceil.php
// make sure we get a solid number, or it'll mess with things.. also, round up so we don't miss images.
$totalPages = ceil($totalImages / $perPage);

echo "\nImages: {$totalImages}, Pages: {$totalPages}\n";

// Create folder if it does not already exist
$base_dir = "images/sol{$sol}";
if (!file_exists($base_dir)) {
	mkdir($base_dir, 0777, true);
}

// Download images per page
for ($page = 0; $page < $totalPages; $page++) {
	echo "Page " . ($page + 1) . "/{$totalPages}\n";
	
	// Grab page
	$pageGrab = ($page === 0) ? $firstPage : json_decode(@file_get_contents($queryUrl . "&page={$page}"), true);
	
	if (empty($pageGrab['images'])) {
		continue;
	}
	
	foreach ($pageGrab['images'] as $img) {
		$imageID = $img['imageid'];
		$title = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $img['title']));
		
		$saveDir = "{$base_dir}/{$title}";
		if (!file_exists($saveDir)) {
			mkdir($saveDir, 0777, true);
		}
		
		$filePath = "{$saveDir}/{$imageID}.png";
		
		if (file_exists($filePath)) {
			echo "Skipping: {$imageID}\n";
			continue;
		}
		
		if (isset($img['image_files']['full_res'])) {
			echo "Downloading: {$imageID}.";
			$imgFile = @file_get_contents($img['image_files']['full_res']);
			if ($imgFile !== false) {
				file_put_contents($filePath, $imgFile);
				echo "\nDone.\n";
			} else {
				echo "\nFailed...\n";
			}
		}
	}
}

echo "\nCompleted.\n";

?>