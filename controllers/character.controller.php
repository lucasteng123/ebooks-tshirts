<?php
$methods = array();
$methods['error'] = function($instance){
	echo '{"error": "Something went wrong"}';
};

$methods['run'] = function($instance) {
	// Set headers
	//header('Content-Type: image/png');
	
	// Get tools
	$pdo = $instance->tools['con_manager']->get_connection();
	
	// Get URL variables
	$r = $instance->route;
	$mode = $r[0];
	if(count($r) > 1){
		$charid = $r[1];	
	}
	switch ($r[0]){
		case "latest":
			$sql = "SELECT character_ID from scans order by id desc limit 1";
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				$result[] = $row;
			}
			echo($result[0]["character_ID"]);
			break;
			
			
		case "fix":
			$sql = "SELECT * from visits where feature_ID IS NULL;";
			// Prepare statement
			$stmt = $pdo->prepare($sql);
			// Do the thing
			$stmt->execute();
			// Fetch results into associative array
			$result = array();
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				$result[] = $row;
			}
			$sql = "delete from visits where feature_ID IS NULL";
			// Prepare statement
			$stmt = $pdo->prepare($sql);
			// Do the thing
			$stmt->execute();
			echo "fixed the database, removed " . count($result) . " records";
			if(count($result) > 0){
				print_r($result);
			}
		break;
		
		case "random":
			$randomChar = CharTools::random($pdo,1);
			echo $randomChar[0];
		break;
		
		case "grid":
			$randomChars = CharTools::random($pdo,100);
			$chars = 0;
			while($chars < 1500){
				foreach ($randomChars as $char) {
					echo "<img class=\"gridimg fadeIn\" id=\"".$chars."\" src = \" http://osc.rtanewmedia.ca/character-image/" . $char["HEXid"]."/50\" />";
					$chars++;
				}
			}
		
		break;
		
		case "generate":
			$randomStrings = explode("\n",file_get_contents("https://www.random.org/strings/?num=100&len=6&digits=on&upperalpha=on&loweralpha=on&unique=on&format=plain&rnd=new"));
			$urls = array("hum", "liv", "inn", "spa", "sci");
			foreach($randomStrings as $charid){
				for ($i=0; $i<5; $i++){
					file_get_contents("http://osc.rtanewmedia.ca/character-update/" . $charid . "/" . $urls[rand(0,4)]);
				}
			}
			
		break;

		case "all-assets":
			//header('Content-Type: image/png');
			$sql = "SELECT * from features;";
			// Prepare statement
			$stmt = $pdo->prepare($sql);
			// Do the thing
			$stmt->execute();
			// Fetch results into associative array
			$result = array();
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				$result[] = $row;
			}
			imagepng(ImgTools::create_char($result));
		break;
		case "stats":
			$sql = "SELECT c.*, v.*, f.* FROM characters c
			LEFT JOIN visits v ON v.character_ID=c.HEXid
			LEFT JOIN features f ON v.feature_ID=f.HEXid
			WHERE c.HEXid=:charid LIMIT 6
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindValue("charid",  $charid,  PDO::PARAM_STR );
			$stmt->execute();
			$result = array();
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				$result[] = $row;
			}
			echo json_encode($result);
		break;
		case "remove":
			$sql = "DELETE from visits where character_id = :charID;
			DELETE from scans where character_ID=:charID;
			DELETE from characters where HEXid=:charID;
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindValue("charID", $charid, PDO::PARAM_STR );
			$stmt->execute();
			echo "removed " . $charid . " from the database";
		break;
		case "reset":
			$sql = "DELETE from visits;
			DELETE from scans;
			DELETE from characters;
			";
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			echo "removed from the database";
		break;
		
	}
	 
};

$page_controller = new Controller($methods);
unset($methods);
