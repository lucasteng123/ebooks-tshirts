<?php
$methods = array();
$methods['error'] = function($instance){
	echo '{"error": "Something went wrong"}';
};

$methods['run'] = function($instance) {
	//header('Content-Type: text/html; charset=utf-8');
	// Set headers
	// Get tools
	$pdo = $instance->tools['con_manager']->get_connection();
	// Get URL variables
	$r = $instance->route;
	$characterID = $r[0];
	$mode = $r[1];
	
	
	  //===========================\\
	 //|====== GET CHARACTER ======|\\
	//||===========================||\\
	$sql = "SELECT f.front_sprite_filename, s.* FROM characters c
	LEFT JOIN visits v ON v.character_ID=c.HEXid
	LEFT JOIN features f ON v.feature_ID=f.HEXid
	LEFT JOIN stations s ON v.visited_stn=s.station_ID
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

	  //===========================\\
	 //|==== Create  Path List ====|\\
	//||===========================||\\	
	
	switch($mode){
		case "explore":
			echo "<div class=\"col-lg-4 col-lg-offset-2\">
			<img class=\"bigImg\" src=\"http://osc.rtanewmedia.ca/character-image/". $characterID ."/600\"/> </div><div class=\"col-lg-4\"><h3>Where this Bitmorph was created</h3><div id=\"infoBoxContainer\">";
			foreach ($result as $state => $output) {
        			echo "<a href=\"" . $output["link"] . "\"><div class=\"infoBox\"><img src=\"/OSC/img/" . $output["front_sprite_filename"] . "\"><h3>" . $output["name"] . "</h3></div></a>";
			}
			echo  "</div></div>";
			break;
			
		case "tracking":
				echo "<div class=\"col-lg-4\" id=\"trackingBox\"><h3>Where this Bitmorph was created</h3><div id=\"infoBoxContainer\">";
			foreach ($result as $state => $output) {
        			echo "<a href=\"" . $output["link"] . "\"><div class=\"infoBox\"><img src=\"/OSC/img/" . $output["front_sprite_filename"] . "\"><h3>" . $output["name"] . "</h3></div></a>";
			}
			echo  "</div></div>";
	}
};

$page_controller = new Controller($methods);
unset($methods);