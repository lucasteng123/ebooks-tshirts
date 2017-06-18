<?php
$methods = array();
$methods['error'] = function($instance){
	echo '{"error": "Something went wrong"}';
};

$methods['run'] = function($instance) {
	//header('Content-Type: text/html; charset=utf-8');
	// Set headers
	header('Content-Type: image/jpeg');
	// Get tools
	$pdo = $instance->tools['con_manager']->get_connection();
	// Get URL variables
	$r = $instance->route;
	$characterID = $r[0];
	
	  //===========================\\
	 //|====== GET CHARACTER ======|\\
	//||===========================||\\
	$sql = "SELECT v.current_state, f.front_sprite_filename FROM characters c
	LEFT JOIN visits v ON v.character_ID=c.HEXid
	LEFT JOIN features f ON v.feature_ID=f.HEXid
	WHERE c.HEXid=:charid LIMIT 5
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

	  //===========================\\
	 //|====== CREATE  IMAGE ======|\\
	//||===========================||\\
	//create blank image to put it in
 	$final_image = imagecreatetruecolor(2550, 3300);
    imagesavealpha($final_image, true);

    $trans_colour = imagecolorallocate($final_image, 255, 255, 255);
    imagefill($final_image, 0, 0, $trans_colour);
    if (count($result) == 1) {
    	 imagecopyresized($final_image, imagecreatefrompng("img/" . $result[0]["front_sprite_filename"]), 0, 0, 0, 0, 2500, 2500, 50, 50);
    } elseif (count($result) == 2) {
    	 imagecopyresized($final_image, imagecreatefrompng("img/" . $result[1]["front_sprite_filename"]), 0, 0, 0, 0, 2500, 2500, 50, 50);
	} elseif (count($result) > 2) {
	    $images = array();
	    foreach ($result as $state => $file) {
	    	$images[] = imagecreatefrompng("img/" . $file["front_sprite_filename"]);
	    }	
		imagealphablending($images[2], true);
		imagesavealpha($images[2], true);
		for ($i=3; $i < count($images); $i++) { 
			imagecopy($images[2], $images[$i], 0,0,0,0,50,50);
		}
		$charimg = imagecreatefrompng("http://osc.rtanewmedia.ca/character-image/" . $characterID . "/2500");
		$rtalogo = imagecreatefromjpeg("img/rta.jpg");
		
		imagecopyresized($final_image, $rtalogo, 100, 3000, 0,0,202,202,402,402);
		imagecopy($final_image, $charimg, 0, 0, 0,0,2500,2500);
		//imagecopy($final_image, $osclogo, 602, 2800, 0,0,330,110);
		
	} else {
		
	}
	imagejpeg($final_image);
	
};

$page_controller = new Controller($methods);
unset($methods);