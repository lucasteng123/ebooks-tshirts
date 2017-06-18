<?php

class ImgTools {
  public static function makeImgGrid($inputImgArray, $gridX, $gridY, $sizeX, $sizeY,  $output) {
	//create image
	$final_image = imagecreatetruecolor($gridX*$sizeX, $gridY*$sizeY);
    imagesavealpha($final_image, true);
	$trans_colour = imagecolorallocatealpha($final_image, 0, 0, 0, 127);
	imagefill($final_image, 0, 0, $trans_colour);
	$i=0;
	for ($x=0; $x < $gridX; $x++) { 
		for ($y=0; $y < $gridY; $y++) { 
			imagecopy($final_image, $inputImgArray[$i],$sizeX*$x,$sizeY*$y,0,0,$sizeX,$sizeY);
			$i = IntTools::constrain($i+1,0,count($inputImgArray));
		}
	}
	return $final_image; 
  }
  
  public static function encodeData($inputImg, $inputData){
	 // echo "why";
	  //echo $inputData;
  	//setup variables
  	$imageX = imagesx($inputImg);
  	$imageY = imagesy($inputImg);
  	$lines = ceil(count($inputData)/$imageX);

  	//setup images
  	$final_image = imagecreatetruecolor($imageX, $imageY+$lines);
    imagesavealpha($final_image, true);
	$trans_colour = imagecolorallocatealpha($final_image, 0, 0, 0, 127);
	$blk_colour = imagecolorallocate($final_image, 0, 0, 0);
	$wht_colour = imagecolorallocate($final_image, 255, 255, 255);
	imagefill($final_image, 0, 0, $trans_colour);

	imagecopy($final_image, $inputImg, 0, 0, 0, 0, $imageX, $imageY);

  	//fill data
  	for ($line=0; $line < $lines; $line++) {
  		for ($data=$line*$imageX; $data < IntTools::constrain(count($inputData), 0, $imageX); $data++) { 
		//echo "why";
  			if($inputData[$data]){
				//echo "yes";
  				imagesetpixel($final_image,($data-($imageX*$line)),($imageY+$line),$wht_colour);
  			} else {
				//echo "no";
  				imagesetpixel($final_image,($data-($imageX*$line)),($imageY+$line),$blk_colour);
  			}
  		}
  	}
  	return($final_image);

  }

  public static function create_char($inputData){
      if (count($inputData) == 1 && $inputData[0]["front_sprite_filename"] != null) {
         $front_image1 = imagecreatefrompng("img/" . $inputData[0]["front_sprite_filename"]);
       $front_depth_image1 = imagecreatefrompng("img/" . $inputData[0]["front_depth_map"]);
       $back_image1 = imagecreatefrompng("img/" . $inputData[0]["back_sprite_filename"]);
       $back_depth_image1 = imagecreatefrompng("img/" . $inputData[0]["back_depth_map"]);
      } elseif (count($inputData) == 2 && $inputData[1]["front_sprite_filename"] != null) {
         $front_image1 = imagecreatefrompng("img/" . $inputData[1]["front_sprite_filename"]);
       $front_depth_image1 = imagecreatefrompng("img/" . $inputData[1]["front_depth_map"]);
       $back_image1 = imagecreatefrompng("img/" . $inputData[1]["back_sprite_filename"]);
       $back_depth_image1 = imagecreatefrompng("img/" . $inputData[1]["back_depth_map"]);
    } elseif (count($inputData) == 3 && $inputData[2]["front_sprite_filename"] != null) {
         $front_image1 = imagecreatefrompng("img/" . $inputData[2]["front_sprite_filename"]);
       $front_depth_image1 = imagecreatefrompng("img/" . $inputData[2]["front_depth_map"]);
       $back_image1 = imagecreatefrompng("img/" . $inputData[2]["back_sprite_filename"]);
       $back_depth_image1 = imagecreatefrompng("img/" . $inputData[2]["back_depth_map"]);
    } elseif (count($inputData) > 3) {
        $front_images = array();
      $front_depth_images = array();
      $back_images = array();
      $back_depth_images = array();
        foreach ($inputData as $state => $file) {
          $front_images[] = imagecreatefrompng("img/" . $file["front_sprite_filename"]);
        $back_images[] = imagecreatefrompng("img/" . $file["back_sprite_filename"]);
        $front_depth_images[] = imagecreatefrompng("img/" . $file["front_depth_map"]);
        $back_depth_images[] = imagecreatefrompng("img/" . $file["back_depth_map"]);
        } 
      imagealphablending($front_images[3], true);
      imagesavealpha($front_images[3], true);
      
      imagealphablending($front_depth_images[3], true);
      imagesavealpha($front_depth_images[3], true);
      
      imagealphablending($back_images[3], true);
      imagesavealpha($back_images[3], true);
      
      imagealphablending($back_depth_images[3], true);
      imagesavealpha($back_depth_images[3], true);
      
      for ($i=4; $i < count($front_images); $i++) { 
        imagecopy($front_images[3], $front_images[$i], 0,0,0,0,50,50);
      }
      for ($i=4; $i < count($front_depth_images); $i++) { 
        imagecopy($front_depth_images[3], $front_depth_images[$i], 0,0,0,0,50,50);
      }
      for ($i=4; $i < count($back_images); $i++) { 
        imagecopy($back_images[3], $back_images[$i], 0,0,0,0,50,50);
      }
      for ($i=4; $i < count($back_depth_images); $i++) { 
        imagecopy($back_depth_images[3], $back_depth_images[$i], 0,0,0,0,50,50);
      }
      
      $front_image1 = $front_images[3];
      imagealphablending($front_image1, true);
      imagesavealpha($front_image1, true);

      $front_depth_image1 = $front_depth_images[3];
      imagealphablending($front_depth_image1, true);
      imagesavealpha($front_depth_image1, true);

      $back_image1 = $back_images[3];
      imagealphablending($back_image1, true);
      imagesavealpha($back_image1, true);

      $back_depth_image1 = $back_depth_images[3];
      imagealphablending($back_depth_image1, true);
      imagesavealpha($back_depth_image1, true);

    } else {
      $front_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($front_image1, true);
      $trans_colour = imagecolorallocatealpha($front_image1, 0, 0, 0, 127);
      imagefill($front_image1, 0, 0, $trans_colour);

      $front_depth_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($front_depth_image1, true);
      $trans_colour = imagecolorallocatealpha($front_depth_image1, 0, 0, 0, 127);
      imagefill($front_depth_image1, 0, 0, $trans_colour);

      $back_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($back_image1, true);
      $trans_colour = imagecolorallocatealpha($back_image1, 0, 0, 0, 127);
      imagefill($back_image1, 0, 0, $trans_colour);

      $back_depth_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($back_depth_image1, true);
      $trans_colour = imagecolorallocatealpha($back_depth_image1, 0, 0, 0, 127);
      imagefill($back_depth_image1, 0, 0, $trans_colour);
    }

    if (count($inputData) == 1 && $inputData[0]["front_anim_sprite_filename"] != null) {
         $front_anim_image1 = imagecreatefrompng("img/" . $inputData[0]["front_anim_sprite_filename"]);
       $front_anim_depth_image1 = imagecreatefrompng("img/" . $inputData[0]["front_anim_depth_map"]);
       $back_anim_image1 = imagecreatefrompng("img/" . $inputData[0]["back_anim_sprite_filename"]);
       $back_anim_depth_image1 = imagecreatefrompng("img/" . $inputData[0]["back_anim_depth_map"]);
      } elseif (count($inputData) == 2 && $inputData[1]["front_anim_sprite_filename"] != null) {
         $front_anim_image1 = imagecreatefrompng("img/" . $inputData[1]["front_anim_sprite_filename"]);
       $front_anim_depth_image1 = imagecreatefrompng("img/" . $inputData[1]["front_anim_depth_map"]);
       $back_anim_image1 = imagecreatefrompng("img/" . $inputData[1]["back_anim_sprite_filename"]);
       $back_anim_depth_image1 = imagecreatefrompng("img/" . $inputData[1]["back_anim_depth_map"]);
    } elseif (count($inputData) == 3 && $inputData[2]["front_anim_sprite_filename"] != null) {
         $front_anim_image1 = imagecreatefrompng("img/" . $inputData[2]["front_anim_sprite_filename"]);
       $front_anim_depth_image1 = imagecreatefrompng("img/" . $inputData[2]["front_anim_depth_map"]);
       $back_anim_image1 = imagecreatefrompng("img/" . $inputData[2]["back_anim_sprite_filename"]);
       $back_anim_depth_image1 = imagecreatefrompng("img/" . $inputData[2]["back_anim_depth_map"]);
    } elseif (count($inputData) > 3) {
        $front_anim_images = array();
      $front_anim_depth_images = array();
      $back_anim_images = array();
      $back_anim_depth_images = array();
        foreach ($inputData as $state => $file) {
          $front_anim_images[] = imagecreatefrompng("img/" . $file["front_anim_sprite_filename"]);
        $back_anim_images[] = imagecreatefrompng("img/" . $file["back_anim_sprite_filename"]);
        $front_anim_depth_images[] = imagecreatefrompng("img/" . $file["front_anim_depth_map"]);
        $back_anim_depth_images[] = imagecreatefrompng("img/" . $file["back_anim_depth_map"]);
        } 
      imagealphablending($front_anim_images[3], true);
      imagesavealpha($front_anim_images[3], true);
      
      imagealphablending($front_anim_depth_images[3], true);
      imagesavealpha($front_anim_depth_images[3], true);
      
      imagealphablending($back_anim_images[3], true);
      imagesavealpha($back_anim_images[3], true);
      
      imagealphablending($back_anim_depth_images[3], true);
      imagesavealpha($back_anim_depth_images[3], true);
      
      for ($i=3; $i < count($front_anim_images); $i++) { 
        imagecopy($front_anim_images[3], $front_anim_images[$i], 0,0,0,0,50,50);
      }
      for ($i=3; $i < count($front_anim_depth_images); $i++) { 
        imagecopy($front_anim_depth_images[3], $front_anim_depth_images[$i], 0,0,0,0,50,50);
      }
      for ($i=3; $i < count($back_anim_images); $i++) { 
        imagecopy($back_anim_images[3], $back_anim_images[$i], 0,0,0,0,50,50);
      }
      for ($i=3; $i < count($back_anim_depth_images); $i++) { 
        imagecopy($back_anim_depth_images[3], $back_anim_depth_images[$i], 0,0,0,0,50,50);
      }
      
      $front_anim_image1 = $front_anim_images[3];
      imagealphablending($front_anim_image1, true);
      imagesavealpha($front_anim_image1, true);

      $front_anim_depth_image1 = $front_anim_depth_images[3];
      imagealphablending($front_anim_depth_image1, true);
      imagesavealpha($front_anim_depth_image1, true);

      $back_anim_image1 = $back_anim_images[3];
      imagealphablending($back_anim_image1, true);
      imagesavealpha($back_anim_image1, true);

      $back_anim_depth_image1 = $back_anim_depth_images[3];
      imagealphablending($back_anim_depth_image1, true);
      imagesavealpha($back_anim_depth_image1, true);

    } else {
      $front_anim_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($front_anim_image1, true);
      $trans_colour = imagecolorallocatealpha($front_anim_image1, 0, 0, 0, 127);
      imagefill($front_anim_image1, 0, 0, $trans_colour);

      $front_anim_depth_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($front_anim_depth_image1, true);
      $trans_colour = imagecolorallocatealpha($front_anim_depth_image1, 0, 0, 0, 127);
      imagefill($front_anim_depth_image1, 0, 0, $trans_colour);

      $back_anim_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($back_anim_image1, true);
      $trans_colour = imagecolorallocatealpha($back_anim_image1, 0, 0, 0, 127);
      imagefill($back_anim_image1, 0, 0, $trans_colour);

      $back_anim_depth_image1 = imagecreatetruecolor(50, 50);
        imagesavealpha($back_anim_depth_image1, true);
      $trans_colour = imagecolorallocatealpha($back_anim_depth_image1, 0, 0, 0, 127);
      imagefill($back_anim_depth_image1, 0, 0, $trans_colour);
    }
    $imgReturn = array("def" => array($front_image1, $front_depth_image1, $back_image1, $back_depth_image1), "anim" => array($front_anim_image1, $front_anim_depth_image1, $back_anim_image1, $back_anim_depth_image1));
    return($imgReturn);
  }
}