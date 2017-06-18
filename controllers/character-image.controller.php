<?php
$methods = array();
$methods['error'] = function($instance){
	echo "False";

};

$methods['run'] = function($instance) {
	// Set headers
	$valid = false;
	// Get tools
	$pdo = $instance->tools['con_manager']->get_connection();
	// Get URL variables
	$r = $instance->route;
	$characterID = $r[0];
	$img_size = IntTools::constrain(intval($r[1]),50,3800);
	
	  //===========================\\
	 //|====== GET CHARACTER ======|\\
	//||===========================||\\
	$sql = "SELECT v.current_state, f.front_sprite_filename FROM characters c
	LEFT JOIN visits v ON v.character_ID=c.HEXid
	LEFT JOIN features f ON v.feature_ID=f.HEXid
	WHERE c.HEXid=:charid LIMIT 6
	";
	// Prepare statement
	$stmt = $pdo->prepare($sql);
	// Bind values
	$stmt->bindValue("charid",  $characterID,  PDO::PARAM_STR );
	// Do the thing
	$stmt->execute();
	// Fetch results into associative array
	$images = array();
	$result = array();
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		$result[] = $row;
	}
	if(count($result) != 0){
		header('Content-Type: image/png');
		$valid = true;
	}

	  //===========================\\
	 //|====== CREATE  IMAGE ======|\\
	//||===========================||\\
	//create blank image to put it in
 	if ($valid) {
 		# code...
 	
	 	$final_image = imagecreatetruecolor($img_size, $img_size);
	    imagesavealpha($final_image, true);

	    $trans_colour = imagecolorallocatealpha($final_image, 0, 0, 0, 127);
	    imagefill($final_image, 0, 0, $trans_colour);  
	    if (count($result) == 1) {
	    	 imagecopyresized($final_image, imagecreatefrompng("img/" . $result[0]["front_sprite_filename"]), 0, 0, 0, 0, $img_size, $img_size, 50, 50);
	    } elseif (count($result) == 2) {
	    	 imagecopyresized($final_image, imagecreatefrompng("img/" . $result[1]["front_sprite_filename"]), 0, 0, 0, 0, $img_size, $img_size, 50, 50);
		} elseif (count($result) == 3) {
	    	 imagecopyresized($final_image, imagecreatefrompng("img/" . $result[2]["front_sprite_filename"]), 0, 0, 0, 0, $img_size, $img_size, 50, 50);
		} elseif (count($result) > 3) {
		    $images = array();
		    foreach ($result as $state => $file) {
		    	$images[] = imagecreatefrompng("img/" . $file["front_sprite_filename"]);
		    }	
			imagealphablending($images[3], true);
			imagesavealpha($images[3], true);
			for ($i=3; $i < count($images); $i++) { 
				imagecopy($images[3], $images[$i], 0,0,0,0,50,50);
			}
			imagecopyresized($final_image, $images[3], 0, 0, 0, 0, $img_size, $img_size, 50, 50);
		} else {
			
		}
		imagepng($final_image);


	} else {
		echo "invalid";
	}
};

$page_controller = new Controller($methods);
unset($methods);
