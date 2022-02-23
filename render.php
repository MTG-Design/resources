<?php
  include("typesetting/latex.php");
  
  $options = getopt("l:m:n:s:cp");
  
  if (!$options) {
	  die("usage: render.php <format> <path>\n\nFormats:\n  -l\tLackeyBot JSON format\n  -m\tMTGJSON JSON format\n  -n\tStart rendering at card number\n  -s\tScryfall JSON format\n\n");
  }
	
	$pwd = exec('pwd');
	  
  #  -l   LackeyBot JSON 
  #  -m   MTGJSON JSON
  #  -s   Scryfall JSON
  
  $elements = array(
    'LackeyBot' => array(
      'name' => 'fullName',
      'manaCost' => 'manaCost',
      'color' => 'color',
      'types' => 'typeLine',
      'text' => 'rulesText',
      'flavor' => 'flavorText',
      'rarity' => 'rarity',
      'power' => 'power',
      'toughness' => 'toughness',
      'loyalty' => 'loyalty',
      'artist' => 'artist',
      'designer' => 'designer',
      'set' => 'setID',
      'number' => 'cardID'
    ),
    'Scryfall' => array(
      'name' => 'name',
      'manaCost' => 'mana_cost',
      'color' => 'color',
      'types' => 'type_line',
      'text' => 'oracle_text',
      'flavor' => 'flavor_text',
      'rarity' => 'rarity',
      'power' => 'power',
      'toughness' => 'toughness',
      'loyalty' => 'loyalty',
      'artist' => 'artist',
      'set' => 'setCode',
      'language' => 'lang',
    ),
    'MTGJSON' => array(
      'name' => 'name',
      'manaCost' => 'manaCost',
      'color' => 'colors',
      'types' => 'type',
      'text' => 'text',
      'flavor' => 'flavorText',
      'rarity' => 'rarity',
      'power' => 'power',
      'toughness' => 'toughness',
      'loyalty' => 'loyalty',
      'artist' => 'artist',
      'set' => 'set',
      'language' => 'lang'
    )
  );
    
  if (array_key_exists('l', $options)) {
    $format = 'LackeyBot';
	  $source = json_decode(file_get_contents($options['l']), true);
  } else if (array_key_exists('m', $options)) {
    $format = 'MTGJSON';
	  $source = json_decode(file_get_contents($options['m']), true);
  } else {
    $format = 'Scryfall';
	  $source = json_decode(file_get_contents($options['s']), true);
	}

  $start = $count = 0;
	
  if (array_key_exists('n', $options)) {
	  $start = $options['n'];
	}
  
  foreach($source as $card) {
    ++$count;
		if ($count < $start) {
			$filenames[] = "";
			continue;
		}
    
    $card = array_merge(json_decode(file_get_contents("typesetting/default_" . strtolower($format) . ".json"), true), $card);
    list($frameImage, $frame_ini) = getFrameImage($card, $format, $elements);

    echo "Frame Image: $frameImage\n";
    echo "Frame ini File: $frame_ini\n";
    
    $recipe  = "frame/$frameImage.svg";
    $textcfg  = parse_ini_file("typesetting/$frame_ini.ini", true);
    $frame_cfg = parse_ini_file("frame/$frame_ini.ini", true);
  
    echo $card[$elements[$format]['name']] . "\n" . $card[$elements[$format]['types']] . "\n";
    $filenames[] = "$count {$card[$elements[$format]['name']]}";
    $thisFile = $filenames[$count - 1];
  
    file_put_contents("output/$thisFile.tex", createTeX($card, $format, $elements, $textcfg, $options));
        
    if ($format == 'Scryfall') {
      $thisImage = $card['image_uris']['art_crop'];
    } else {  
      $thisImage = "$pwd/art/" . $card[$elements[$format]['name']] . ".jpg";
    }
    
    exec("xelatex -interaction=nonstopmode -halt-on-error -output-directory='output' -output-driver='xdvipdfmx -z0' -no-pdf --shell-escape \"$thisFile.tex\" && dvisvgm -b papersize -T S0.75 -n -s \"output/$thisFile.xdv\" > \"output/{$thisFile}_txt.svg\"");
  	
    $preparesvg = file_get_contents("output/{$thisFile}_txt.svg");
    $preparesvg = str_replace("<?xml version='1.0' encoding='UTF-8'?>", "", $preparesvg);
    $preparesvg = str_replace("</svg>", "", file_get_contents("cards/$frameImage.svg")) . $preparesvg . "</svg>";
		
    if (file_exists($thisImage)) {
			echo "Found image: $thisImage\n";
      list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize($thisImage);
      
      $imgType = image_type_to_mime_type($imgType);
    
      switch ($imgType) {
        case 'image/png':
          $imgExtension = 'PNG';
          break;
        case 'image/jpg':
        case 'image/jpeg':
        default:
          $imgExtension = 'JPEG';
      }
      
      echo "Width: "  . $imgWidth . "\n";
      echo "Height: " . $imgHeight . "\n";

      $artboxRatio = $frame_cfg['art']['width'] / $frame_cfg['art']['height'];
      $thisArtRatio = $imgWidth / $imgHeight;
    
      // Greater width, larger ratio
      // Taller height, smaller ratio
			
			$newImage = base64_encode(file_get_contents($thisImage));
			
			if ($thisArtRatio > $artboxRatio) {
				// Wider
				$newWidth  = ($frame_cfg['art']['height'] / $imgHeight * $imgWidth);
				$newHeight = $frame_cfg['art']['height'];
				$newX = -($newWidth - $frame_cfg['art']['width']) / 2;
				$newY = 0;
			} else {
				// Taller
				$newWidth  = $frame_cfg['art']['width'];
				$newHeight = ($frame_cfg['art']['width'] / $imgWidth * $imgHeight);
				$newX = 0;
				$newY = -($newHeight - $frame_cfg['art']['height']) / 2;
			}
			
			$preparesvg = str_replace('xlink:href="{$thisArt}"', "x=\"$newX\" y=\"$newY\" width=\"$newWidth\" height=\"$newHeight\" xlink:href=\"data:$imgType;base64,$newImage\"", $preparesvg);
		} else {
			$newImage = base64_encode(file_get_contents('transparent.png'));
			$preparesvg = str_replace('xlink:href="{$thisArt}"', "xlink:href=\"data:$imgType;base64,$newImage\"", $preparesvg);
		}
		
	  //if (array_key_exists('p', $options)) {
	  	$preparesvg = str_replace('width="744" height="1039" viewBox="0 0 744 1039" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">', 'width="816" height="1110" viewBox="0 0 816 1110" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="m0 0h816v1110h-816z" fill="#000"/><g transform="translate(37.5,35.5)">', $preparesvg);
			$preparesvg = preg_replace('/<\/svg>$/', '</g></svg>', $preparesvg);
			// Move logo up for printing
			$preparesvg = str_replace('"translate(543,1006)"', '"translate(543,986)"', $preparesvg);
			//}
		
    file_put_contents("output/$thisFile.svg", $preparesvg);
    
    exec("inkscape -p \"output/$thisFile.svg\" --export-pdf-version=1.4 --batch-process -o \"output/$thisFile.pdf\"");
    exec("inkscape -p \"output/$thisFile.svg\" --batch-process -d 256 -o \"output/$thisFile.png\"");
		
  }
?>

<!-- ?php
  
    if (strpos('Token', $types) !== 0) {
      $frame_cfg = 'token';
      $textcfg  = 'token_small';
    }
  
    $recipeIngredients = array('type', 'legendary', 'promo', 'isRare', 'color');
    $recipe = "$frame";
  
    for ($i = 0; $i < 3; $i++) {
      if ($$recipeIngredients[$i]) {
        $recipe = "_" . $$recipeIngredients[$i];
      }
    }
    
    $recipe  = "frame/$frame$type$legendary$promo$color.svg";
    $frame_cfg = parse_ini_file("frame/$frame_cfg.ini", true);  
    $textcfg  = parse_ini_file("typesetting/$textcfg.ini", true);
    
    echo $card[$elements[$format]['name']] . "\n" . $card[$elements[$format]['types']] . "\n";
    $filenames[] = "$count {$card[$elements[$format]['name']]}";
    $thisFile = $filenames[$count - 1];
    
    file_put_contents("$thisFile.tex", createTeX($card, $textcfg));
    
    $thisArt = '';

 */
?>