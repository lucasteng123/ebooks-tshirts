<?php

class CharTools {
  public static function random($connection, $limit) {
    $sql = "SELECT HEXid FROM characters ORDER BY rand() limit :limit;";
			// Prepare statement
			$stmt = $connection->prepare($sql);
			$stmt->bindValue("limit",  $limit,  PDO::PARAM_INT );
			// Do the thing
			$stmt->execute();
			// Fetch results into associative array
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				$result[] = $row;
			}
			return $result;
  }
}