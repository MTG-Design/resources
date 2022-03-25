<?php
	function str_lreplace($search, $replace, $subject) {
	  $pos = strrpos($subject, $search);
	
	  if ($pos !== false) {
	    $subject = substr_replace($subject, $replace, $pos, strlen($search));
	  }

	  return $subject;
	}
	
  include("typesetting/latex.php");
  
  $options = getopt("d:l:m:n:s:chkprv", array("help"));
  
  if (!$options || array_key_exists('h', $options) || array_key_exists('help', $options)) {
	  die("usage: render.php <format> [<options>] <path>\n\nFormats:\n  -l\tLackeyBot JSON format\n  -m\tMTGJSON JSON format (Not finished)\n  -s\tScryfall JSON format (Not finished)\n\nOptions:\n  -c\tRemove copyright line\n  -d\tAdd text for designer field to each card\n  -h\tDisplays this usage description\n  -k\tKeep intermediate and temporary render files\n  -n\tStart rendering at card number\n  -p\tOutput as print ready (With bleed area)\n  -v\tOutput as SVG instead of PDF (No flavor bar or graphics)\n\n");
  }
	
	$pwd = exec('pwd');
	  
  #  -l   LackeyBot JSON 
  #  -m   MTGJSON JSON
  #  -s   Scryfall JSON
  
  $elements = array(
    'LackeyBot' => array(
      'name' => 'fullName',
      'manacost' => 'manaCost',
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
      'number' => 'cardID',
			'watermark' => 'watermark',
    ),
    'Scryfall' => array(
      'name' => 'name',
      'manacost' => 'mana_cost',
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
			'watermark' => 'watermark',
    ),
    'MTGJSON' => array(
      'name' => 'name',
      'manacost' => 'manaCost',
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
      'language' => 'lang',
			'watermark' => 'watermark',
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
	  $source = json_decode(file_get_contents($options['y']), true);
	}
	
  $start = $end = $count = 0;
	  
	if (array_key_exists('n', $options)) {
		$start_range = explode('-', $options['n']);
		$start = $end = $start_range[0];
		if (count($start_range) > 1) {
			$end = $start_range[1];
		}
	} else {
		$start = 0;
		$end = count($source);
	}
		  
  foreach($source as $card) {
    ++$count;
		if ($count < $start) {
			$filenames[] = "";
			continue;
		} else if ($count > $end) {
			continue;
		}
    
    $card = array_merge(json_decode(file_get_contents("typesetting/default_" . strtolower($format) . ".json"), true), $card);
    list($frameImage, $frame_ini) = getFrameImage($card, $format, $elements);
		
		if (array_key_exists('d', $options)) {
			$card[$elements[$format]['designer']] = $options['d'];
		}

    //echo "Frame Image: $frameImage\n";
    //echo "Frame ini File: $frame_ini\n";
    
    $recipe  = "frame/$frameImage.svg";
    $textcfg  = parse_ini_file("typesetting/$frame_ini.ini", true);
    $frame_cfg = parse_ini_file("frame/$frame_ini.ini", true);
		
    echo $card[$elements[$format]['name']] . "\n";
    $filenames[] = "$count {$card[$elements[$format]['name']]}";
    $thisFile = $filenames[$count - 1];
		
		if ($card[$elements[$format]['rarity']] == 'basic land') {
			$card[$elements[$format]['rarity']] = 'land';
		}
		
		// Put symbol into file
		$symbolPath = $pwd . '/symbol/' . $card[$elements[$format]['set']] . '_' . strtoupper($card[$elements[$format]['rarity']])[0];
		
		if (!file_exists("$symbolPath.png")) {
			$symbolPath = $pwd . '/symbol/SET_' . strtoupper($card[$elements[$format]['rarity']])[0];
		}
    list($imgSymbolWidth, $imgSymbolHeight, $imgSymbolType, $imgSymbolAttr) = getimagesize("$symbolPath.png");
		$symbolData = base64_encode(file_get_contents("$symbolPath.png"));
		$newSymbolHeight = 64;
		$imgSymbolHeightRatio = ($newSymbolHeight / $imgSymbolHeight);
		$newSymbolWidth = ($imgSymbolWidth * $imgSymbolHeightRatio);
		
		if (array_key_exists('c', $options)) {
			$textcfg['designer']['y'] -= 10;
		} else {
			$textcfg['designer']['y'] += 8;			
		}
		
    file_put_contents("$pwd/output/$thisFile.tex", createTeX($card, $format, $elements, $textcfg, $newSymbolWidth, $options));
        
    if ($format == 'Scryfall') {
      $thisImage = $card['image_uris']['art_crop'];
    } else {  
      $thisImage = "$pwd/art/" . $card[$elements[$format]['name']];
    }
    
	  if (array_key_exists('v', $options)) {
		  // DVI to SVG export
    	exec("xelatex -interaction=nonstopmode -halt-on-error -output-directory='$pwd/output' -output-driver='xdvipdfmx -z0' -no-pdf --shell-escape \"$thisFile.tex\" && dvisvgm -b papersize -T S0.75 -n -s \"$pwd/output/$thisFile.xdv\" > \"$pwd/output/{$thisFile}_txt.svg\"");
			$preparesvg = file_get_contents("$pwd/output/{$thisFile}_txt.svg");
	    $preparesvg = str_replace("<?xml version='1.0' encoding='UTF-8'?>", "", $preparesvg);
	    $preparesvg = str_lreplace("</svg>", "", file_get_contents("cards/$frameImage.svg")) . $preparesvg . "</svg>";
		} else {
			// PDF export
			exec("xelatex -interaction=nonstopmode -halt-on-error -output-directory='$pwd/output' -output-driver='xdvipdfmx -z0' --shell-escape \"$pwd/output/$thisFile.tex\"");
			
			exec("mv \"$pwd/output/$thisFile.pdf\" \"$pwd/output/{$thisFile}_txt.pdf\"");
			
			$preparesvg = file_get_contents("cards/$frameImage.svg");
		}
		
    $preparesvg = str_lreplace("</svg>", "", $preparesvg) . "<image x=\"" . ($textcfg['symbol']['x'] - $newSymbolWidth) . "\" y=\"" . $textcfg['symbol']['y'] . "\" width=\"$newSymbolWidth\" height=\"$newSymbolHeight\" xlink:href=\"data:image/png;base64," . $symbolData . "\"/></svg>";
					
    if (glob("$thisImage.*")) {
			if (file_exists($thisImage . ".jpg")) { $thisImage .= ".jpg"; }
			else if (file_exists("$thisImage.jpeg")) { $thisImage .= ".jpg"; }
			else if (file_exists("$thisImage.png")) { $thisImage .= ".png"; }
			else { continue; }
				
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
      
      // echo "Width: "  . $imgWidth . "\n";
      // echo "Height: " . $imgHeight . "\n";

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
			
			$preparesvg = str_replace('xlink:href="{$thisArt}"', "x=\"" . ($newX + 3) . "\" y=\"" . ($newY + 3) . "\" width=\"$newWidth\" height=\"$newHeight\" xlink:href=\"data:$imgType;base64,$newImage\"", $preparesvg);
		} else {
			$newImage = base64_encode(file_get_contents('transparent.png'));
			$preparesvg = str_replace('xlink:href="{$thisArt}"', "xlink:href=\"data:$imgType;base64,$newImage\"", $preparesvg);
		}
		
		// Watermark
		$land_types = ['plains', 'island', 'swamp', 'mountain', 'forest', 'wastes'];
		$land_key = ['plains' => 'W', 'island' =>'U', 'swamp' => 'B', 'mountain' => 'R', 'forest' => 'G', 'wastes' => 'C'];
		if (in_array(strtolower($card[$elements[$format]['name']]), $land_types)) {
			$preparesvg = str_replace('<image id="wm" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII="/>', '<image id="wm" width="' . $frame_cfg['watermark']['width'] . '" height="' . $frame_cfg['watermark']['height'] . '" xlink:href="data:image/png;base64,' . base64_encode(file_get_contents($pwd . '/watermark/land/' . $land_key[strtolower($card[$elements[$format]['name']])] . '.png')) . '"/>', $preparesvg);
		}
				
	  if (array_key_exists('p', $options)) {
	  	$preparesvg = str_replace('width="744" height="1039" viewBox="0 0 744 1039" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">', 'width="816" height="1110" viewBox="0 0 816 1110" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="m0 0h816v1110h-816z" fill="#000"/><g transform="translate(37.5,35.5)">', $preparesvg);
			$preparesvg = str_lreplace('</svg>', '</g></svg>', $preparesvg);
			// Move logo up for printing
			$preparesvg = str_replace('"translate(543,990)"', '"translate(543,970)"', $preparesvg);
			$preparesvg = str_replace('"translate(543,1007)"', '"translate(543,990)"', $preparesvg);
			$preparesvg = str_replace('"translate(543,1010)"', '"translate(543,990)"', $preparesvg);
		}
				
    file_put_contents("$pwd/output/$thisFile.svg", $preparesvg);
		
		// echo "Exporting frame to PDF"
    // exec("inkscape -p \"$pwd/output/$thisFile.svg\" --export-pdf-version=1.4 --batch-process -o \"$pwd/output/$thisFile.pdf\" &>/dev/null");
    
		echo "Exporting frame to PNG (800 DPI)\n";
		exec("inkscape -p \"$pwd/output/$thisFile.svg\" --batch-process -d 256 -o \"$pwd/output/$thisFile.png\" &>/dev/null");
		echo "Exporting frame to PNG (300 DPI)\n";
    exec("inkscape -p \"$pwd/output/$thisFile.svg\" --batch-process -d 96 -o \"$pwd/output/$thisFile 300.png\" &>/dev/null");
		
	  if (!array_key_exists('v', $options)) {
	    exec("convert -density 300 -geometry 1984 -transparent white \"$pwd/output/{$thisFile}_txt.pdf\" \"$pwd/output/{$thisFile}_txt.png\"");
	    exec("convert -density 300 -geometry 744 -transparent white \"$pwd/output/{$thisFile}_txt.pdf\" \"$pwd/output/{$thisFile}_txt 300.png\"");
			
			// If it’s PDF, we need to layer the PDF onto the SVG output
			
		  if (array_key_exists('p', $options)) {
        $base  = imagecreatetruecolor(2176, 2960);
				$compo = imagecreatetruecolor(1984, 2771);
			} else {
        $base  = imagecreatetruecolor(1984, 2771);
				$compo = imagecreatetruecolor(1984, 2771);
			}
      imagealphablending($compo, true);
      imagesavealpha($compo, true);
      $base  = imagecreatefrompng("$pwd/output/$thisFile.png");
      $compo = imagecreatefrompng("$pwd/output/{$thisFile}_txt.png");
			
		  if (array_key_exists('p', $options)) {
				imagecopy($base, $compo, 100, 95, 0, 0, imagesx($compo), imagesy($compo));				
			} else {
				imagecopy($base, $compo, 0, 0, 0, 0, imagesx($compo), imagesy($compo));				
			}
			
			imagepng($base, "$pwd/output/$thisFile.png");
      imagedestroy($base);
			
		  if (array_key_exists('p', $options)) {
        $base  = imagecreatetruecolor(816, 1110);
				$compo = imagecreatetruecolor(744, 1039);
			} else {
        $base  = imagecreatetruecolor(744, 1039);
				$compo = imagecreatetruecolor(744, 1039);
			}
      imagealphablending($compo, true);
      imagesavealpha($compo, true);
      $base  = imagecreatefrompng("$pwd/output/$thisFile 300.png");
      $compo = imagecreatefrompng("$pwd/output/{$thisFile}_txt 300.png");
			
		  if (array_key_exists('p', $options)) {
				imagecopy($base, $compo, 38, 36, 0, 0, imagesx($compo), imagesy($compo));				
			} else {
				imagecopy($base, $compo, 0, 0, 0, 0, imagesx($compo), imagesy($compo));				
			}
			
			imagepng($base, "$pwd/output/$thisFile 300.png");
      imagedestroy($base);
		}
		
	  if (!array_key_exists('k', $options)) {
			unlink("$pwd/output/{$thisFile}_txt 300.png");
			unlink("$pwd/output/{$thisFile}_txt.pdf");
			unlink("$pwd/output/{$thisFile}_txt.png");
			unlink("$pwd/output/{$thisFile}.aux");
			unlink("$pwd/output/{$thisFile}.log");
			unlink("$pwd/output/{$thisFile}.svg");
			unlink("$pwd/output/{$thisFile}.tex");
		}
  }
?>