<?php
$base_url = "https://mars.nasa.gov/rss/api/?feed=raw_images&category=mars2020&feedtype=json";
$url = ($base_url."&num=100");
// Camera Search options and translations:
// Descent Stage:
// |EDL_RDCAM - Rover Down-Look Camera
// |EDL_RUCAM - Rover Up-Look Camera
// |EDL_DDCAM - Descent Stage Down-Look Camera
// |EDL_PUCAM2 - Parachute Up-Look Camera B
// |EDL_PUCAM1 - Parachute Up-Look Camera A
// Science Cameras:
// |MCZ_RIGHT - Mastcam-Z-Right
// |MCZ_LEFT - Mastcam-Z-Left
// |REAR_HAZCAM_RIGHT - Rear Hazcam Right
// |REAR_HAZCAM_LEFT - Rear Hazcam Left
// |FRONT_HAZCAM_RIGHT_B - Front Hazcam Right
// |FRONT_HAZCAM_LEFT_B - Front Hazcam Left
// |NAVCAM_RIGHT - Navigation Camera Right
// |NAVCAM_LEFT - Navigation Camera Left
// Process Setting
// &extended=product_type::raw - Raw images
// &extended=product_type::color - Color-Processed

// Setting up the search and raw config
// Search
//sol - will make this cleaner when i have more time
// set up easy args first.
foreach ($argv as $value){
	preg_match("/[0-9]+/i", $value, $matches);
	if (isset($matches['1'])){
		$sol = $matches['1'];
	};
	preg_match("/raw/i", $value, $matches);
	if (isset($matches['1'])){
		$rawmode = $matches['1'];
	};
	preg_match("/color/i", $value, $matches);
	if (isset($matches['1'])){
		$rawmode = $matches['1'];
	};
};
if (isset($sol)){
	print("Sol: " . $sol . "\n");
} else {
	// grab latest SOL from json
	$getSOL = (json_decode(file_get_contents($url),True)['images']['0']['sol']);
	print("getSOL: ". $getSOL . "\n");
	$sol = $getSOL;
};
//https://www.w3schools.com/Php/func_array_in_array.asp
$possible_searches = array("RDLC - Rover Down-Look Camera", "RULC - Rover Up-Look Camera", "DDLC - Descent Stage Down-Look Camera", "PULCB - Parachute Up-Look Camera B", "PULCA - Parachute Up-Look Camera A", "MZR - Mastcam-Z-Right", "MZL - Mastcam-Z-Left",  "RHR - Rear Hazcam Right", "RHL - Rear Hazcam Left", "FHR - Front Hazcam Right", "FHL - Front Hazcam Left", "NCR - Navigation Camera Right", "NCL - Navigation Camera Left");
// probably a better way to do this ...
$clean_searches = array("RDLC", "RULC", "DDLC", "PULCB", "PULCA", "MZR", "MZL",  "RHR", "RHL", "FHR", "FHL", "NCR", "NCL");
foreach ($argv as $value){
	if (in_array($value, $clean_searches)){
		$search = $value;
	};
};
if (isset($search)){
	print("Search: ". $search . "\n");
} else{
	echo "Invalid Selection, Spaced arguments need commenting with quotations\r\n";
	echo "Valid options are:\r\n\r\n";
	foreach ($possible_searches as $key => $val){
		echo $possible_searches[$key]."\r\n";
	};
	exit("Need Camera Arguments.\n");
};
/* if (isset($argv['2'])){
	if ($argv['2'] == "raw" || $argv['2'] == "color"){
				$rawmode = $argv['2'];
*/
if (isset($rawmode)){
	print("image mode: ". $rawmode . "\n");
} else{
		echo "Invalid raw setting, options are:\r\nraw, color\n";
		echo "Choosing Raw mode automatically in 5s\n";
		sleep(5);
		$rawmode = 'raw';
//} else{
};

// // // // //
if (isset($url)){
	if ($url != ""){
		$perpage = (json_decode(file_get_contents($url),True)['per_page']);
		$totalimg = (json_decode(file_get_contents($url),True)['total_results']);
		$pgcount = (floor($totalimg / $perpage));
	};
};
//echo $pgcount;
$currentpg = "0";
if (isset($pgcount)){
	if (!file_exists('images/')) {
		mkdir('images/', 0777, true);
	};
	if(isset($search)){
		// Because I am still learning PHP, I shall now conduct 13 str_replace operations... sorry.
		$search = (str_replace("RDLC", "|EDL_RDCAM", $search));
		$search = (str_replace("RULC", "|EDL_RUCAM", $search));
		$search = (str_replace("DDLC", "|EDL_DDCAM", $search));
		$search = (str_replace("PULCB", "|EDL_PUCAM2", $search));
		$search = (str_replace("RULCA", "|EDL_PUCAM1", $search));
		$search = (str_replace("MZR", "|MCZ_RIGHT", $search));
		$search = (str_replace("MZL", "|MCZ_LEFT", $search));
		$search = (str_replace("RHR", "|REAR_HAZCAM_RIGHT", $search));
		$search = (str_replace("RHL", "|REAR_HAZCAM_LEFT", $search));
		$search = (str_replace("FHR", "|FRONT_HAZCAM_RIGHT", $search));
		$search = (str_replace("FHL", "|FRONT_HAZCAM_LEFT", $search));
		$search = (str_replace("NCR", "|NAVCAM_RIGHT", $search));
		$search = (str_replace("NCL", "|NAVCAM_LEFT", $search));
	};
	if (! isset($sol)){
		$sol = "";
	};
	while ($currentpg != $pgcount){
		if(isset($search)){
			$url = ($base_url."&num=100&page=".$currentpg."&search=".$search."&extended=product_type::".$rawmode."&sol=".$sol."&extended=sample_type::full,");
		} else{
			$url = ($base_url."&num=100&page=".$currentpg."&extended=product_type::".$rawmode."&sol=".$sol."&extended=sample_type::full,");
		};
		$grab = (json_decode(file_get_contents($url),True)['images']);
		$solCheck = preg_match("/".$sol."/i", $grab['0']['sol']);
		if (isset($solCheck)){
			print("There is at least one image with the selected SOL ". $sol .". Downloading");
		} else {
			exit("There are no images for selected SOL" . $sol);
		};
		foreach ($grab as $key => $val) {
			$folder_name = ($grab[$key]['title']);
			// https://stackoverflow.com/a/2303377
			$folder_name = (preg_replace("/[^A-Za-z0-9 ]/", '', $folder_name));
			// https://stackoverflow.com/questions/12704613/php-str-replace-replace-spaces-with-underscores
			$folder_name = (preg_replace('/\s+/', '_', $folder_name));
			if (!file_exists('images/'.$folder_name)) {
				 mkdir('images/'.$folder_name, 0777, true);
			};
//			https://stackoverflow.com/a/3938551
			if (!file_exists("images/".$folder_name."/".$grab[$key]['imageid'].".png")){
				echo "Getting ".$grab[$key]['imageid']." from ".$grab[$key]['title']."\r\n";
				file_put_contents("images/".$folder_name."/".$grab[$key]['imageid'].".png", fopen($grab[$key]['image_files']['full_res'], 'rb'));
				echo "\r\n";
			};
		};
		$currentpg = ($currentpg + 1);
	};
};
?>