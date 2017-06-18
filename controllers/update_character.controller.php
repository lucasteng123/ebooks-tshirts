<?php

$methods = array();

$methods['error'] = function($instance){
	echo '{"error": "Something went wrong"}';
};

$methods['run'] = function($instance) {
	// Set headers
	header('Content-Type: application/json');

	// Get tools
	$pdo = $instance->tools['con_manager']->get_connection();
	
	// Get URL variables
	$r = $instance->route;
	$characterID = $r[0]; // TODO: check valid string
	$stationID   = $r[1]; // TODO: check valid string
	
	// ===     Sanitize the input     === \\ 
	
	
	// === Generate current character === \\

	// detect if there is a character already with this ID
	$sql = "SELECT * FROM characters c
	WHERE c.HEXid=:charid";
	// Prepare statement
	$stmt = $pdo->prepare($sql);
	// Bind values
	$stmt->bindValue("charid",  $characterID,  PDO::PARAM_STR );
	$stmt->execute();
	// Fetch results into associative array
	$result = array();
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		$result[] = $row;
	}
	//if there is no character with this ID, create it, and make an egg and blob
	if(count($result) != 1){
		//Get an egg to assign to this character
		$getEgg = "SELECT f.HEXid, f.colorVar FROM features f
		WHERE f.station_ID=:STNid and f.stage=0 order by rand() limit 1";
		$getEgg_stmt = $pdo->prepare($getEgg);
		$getEgg_stmt->bindValue("stnID",  $stationID,  PDO::PARAM_STR );
		$getEgg_stmt->execute();
		$result = array();
		if ( $row = $getEgg_stmt->fetch(PDO::FETCH_ASSOC) ) {
			$colorVar = $row["colorVar"];
			$eggID = $row["HEXid"];
		}
		if ($stationID != "hum"){
			$colorVar = rand(0,1);
		}		
		$insert_character = "INSERT INTO characters (HEXid, date_created, color_variant, primary_station, current_state) VALUES (:HXid, now(), :colorVar, :pristn, 0)";
		$stmt = $pdo->prepare($insert_character);
		// Bind variables
		$stmt->bindValue("HXid", $characterID, PDO::PARAM_STR );
		$stmt->bindValue("colorVar", $colorVar, PDO::PARAM_STR );
		$stmt->bindValue("pristn", $stationID, PDO::PARAM_STR );
		// Insert the row
		$stmt->execute();
		
		$insert_egg="INSERT INTO visits(character_ID, feature_ID, current_state, date_posted) VALUES (:HXid, :FTid, :state, now())";
		$stmt = $pdo->prepare($insert_egg);
		// Bind variables
		$stmt->bindValue("HXid", $characterID, PDO::PARAM_STR );
		$stmt->bindValue("FTid", $eggID, PDO::PARAM_STR );
		$stmt->bindValue("state", 0, PDO::PARAM_STR );
		// Insert the row
		$stmt->execute();
		
		
		//info variables
		$current_state = 0;
		$primary_station = $stationID;
		
	} else {
		//get info
		$current_state = $result[0]["current_state"];
		$colorVar = $result[0]["color_variant"];
		$primary_station = $result[0]["primary_station"];
	}




	// === Modify character for visit === ///
	if($current_state <= 5){
		$sql = "SELECT f.HEXid FROM features f
		WHERE f.station_id=:stnID AND f.stage=:state ORDER BY rand()
		limit 1
		";
		// Prepare statement
		$stmt = $pdo->prepare($sql);
		// Bind values
		$stmt->bindValue("stnID",  $stationID,  PDO::PARAM_STR );
		$stmt->bindValue("state", $current_state, PDO::PARAM_INT);
		$stmt->execute();
		// Fetch results into associative array
		$feature_ID = null;
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$feature_ID = $row["HEXid"];
		} else {
			$methods["error"];
		}

		$sql = "INSERT INTO visits (character_ID, feature_ID, current_state, date_posted)
		VALUES (:charid, :featid, :state, now())";
		$stmt = $pdo->prepare($sql);
		// Bind variables
		$stmt->bindValue("charid", $characterID, PDO::PARAM_STR);
		$stmt->bindValue("featid", $feature_ID,  PDO::PARAM_STR);
		$stmt->bindValue("state", $current_state,  PDO::PARAM_STR);
		// Insert the row
		$stmt->execute();
		// Get the id of what we just inserted
		$idInserted = $pdo->lastInsertId();
	}


	//get result from db
	$sql = "SELECT c.HEXid, v.current_state, f.sprite_filename FROM characters c
	LEFT JOIN visits v ON v.character_ID=c.HEXid
	LEFT JOIN features f ON v.feature_ID=f.HEXid
	WHERE c.HEXid=:charid
	";
	// Prepare statement
	$stmt = $pdo->prepare($sql);
	// Bind values
	$stmt->bindValue("charid",  $characterID,  PDO::PARAM_STR );
	// Do the thing
	$stmt->execute();
	// Fetch results into associative array
	$result = array();
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		$result[] = $row;
	}


	// Print results to a temporary file for debugging
	ob_start();
		print_r($result);
	file_put_contents("output.txt", ob_get_clean());
	ob_get_clean();

	// make JSON object of result, and print that
	echo json_encode( $result );
};

$page_controller = new Controller($methods);
unset($methods);
