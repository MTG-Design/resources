<?php
	function str_lreplace($search, $replace, $subject) {
	  $pos = strrpos($subject, $search);
	
	  if ($pos !== false) {
	    $subject = substr_replace($subject, $replace, $pos, strlen($search));
	  }

	  return $subject;
	}
	
	function sortMana($mana) {
		$mv = 0;
		$color = 'C';
		$mana = str_split($mana);
		
		foreach ($mana as $elem) {
			switch ($elem) {
				case 'W':  $mv += 1; break;
				case 'U':  $mv += 2; break;
				case 'B':  $mv += 4; break;
				case 'R':  $mv += 8; break;
				case 'G':  $mv +=16; break;
			}
		}
		
		switch ($mv) {
			case 0:  $color = 'C'; break;
			case 1:  $color = 'W'; break;
			case 2:  $color = 'U'; break;
			case 3:  $color = 'WU'; break;
			case 4:  $color = 'B'; break;
			case 5:  $color = 'WB'; break;
			case 6:  $color = 'UB'; break;
			case 7:  $color = 'WUB'; break;
			case 8:  $color = 'R'; break;
			case 9:  $color = 'RW'; break;
			case 10: $color = 'UR'; break;
			case 11: $color = 'URW'; break;
			case 12: $color = 'BR'; break;
			case 13: $color = 'RWB'; break;
			case 14: $color = 'UBR'; break;
			case 15: $color = 'WUBR'; break;
			case 16: $color = 'G'; break;
			case 17: $color = 'GW'; break;
			case 18: $color = 'GU'; break;
			case 19: $color = 'GWU'; break;
			case 20: $color = 'BG'; break;
			case 21: $color = 'WBG'; break;
			case 22: $color = 'BGU'; break;
			case 23: $color = 'GWUB'; break;
			case 24: $color = 'RG'; break;
			case 25: $color = 'RGW'; break;
			case 26: $color = 'GUR'; break;
			case 27: $color = 'RGWU'; break;
			case 28: $color = 'BRG'; break;
			case 29: $color = 'BRGW'; break;
			case 30: $color = 'UBRG'; break;
			case 31: $color = 'WUBRG'; break;
		}
		return $color;
	}	
	
  include("typesetting/latex.php");
  
  $options = getopt("a:d:e:f:i:l:m:n:s:t:u:x:z:bchkprv", array("help", "for-sale", "logo"));
  
  if (!$options || array_key_exists('h', $options) || array_key_exists('help', $options)) {
	  die("usage: render.php <format> [<options>] <path>\n\nFormats:\n  -l\tLackeyBot JSON format\n  -m\tMTGJSON JSON format (Not finished)\n  -s\tScryfall JSON format (Not finished)\n\nOptions:\n  -a\tArtist override\n  -b\tBorderless promo frame\n  -c\tRemove copyright line\n  -d\tAdd text for designer field to each card\n  -e\tSet override\n  -h\tDisplays this usage description\n  -k\tKeep intermediate and temporary render files\n  -n\tRender a specific card or range of cards (using a hyphen)\n  -p\tOutput as print ready (With bleed area)\n  -r\tDon’t print reminder text\n  -t\tShadow text on promo cards\n  -u\tCard number override\n-v  Output as SVG instead of PDF (No flavor bar or graphics)\n  -z\tMax text size override (an integer, 18-38)\n\n");
  }
	
	$pwd = exec('pwd');
	
	$early_abort = false;
	  
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
      'color' => 'colors',
      'types' => 'type_line',
      'text' => 'oracle_text',
      'flavor' => 'flavor_text',
      'rarity' => 'rarity',
      'power' => 'power',
      'toughness' => 'toughness',
      'loyalty' => 'loyalty',
      'artist' => 'artist',
      'set' => 'set',
      'language' => 'lang',
			'number' => 'collector_number',
			'watermark' => 'watermark',
      'designer' => 'designer',
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
		if (file_exists(getcwd() . "/" . $options['s'] . ".json")) {
		  $source = json_decode(file_get_contents(getcwd() . "/" . $options['s'] . ".json"), true);
		} else {
			$source = json_decode(file_get_contents('https://api.scryfall.com/cards/search?q=' . rawurlencode($options['s'])));
			$source = ((array)$source)['data'];
		}
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
		$end = count((array)$source);
	}
		
  foreach($source as $card) {
    ++$count;
		if ($count < $start) {
			$filenames[] = "";
			continue;
		} else if ($count > $end) {
			continue;
		}
						
		if (array_key_exists('s', $options)) {
			if (is_string($card)) {
				$card = $source;
				$early_abort = true;
				//echo "Early Abort: $early_abort\n"
			}
	    $card = array_merge(json_decode(file_get_contents("typesetting/default_" . strtolower($format) . ".json"), true), ((array)$card));			
			if (count($card['colors']) == 1) {
				$card[$elements[$format]['color']] = $card['colors'][0];
			} else if (count($card['colors']) == 2) {
				$card[$elements[$format]['color']] = sortMana(implode('', $card['colors']));
			} else {
				$card[$elements[$format]['color']] = 'M';
			}
		} else {
	    $card = array_merge(json_decode(file_get_contents("typesetting/default_" . strtolower($format) . ".json"), true), $card);			
		}
		
		if (array_key_exists('r', $options)) {
			$card[$elements[$format]['text']] = preg_replace('/\([^\)]+\)/', '', $card[$elements[$format]['text']]);
		}
		
		if (array_key_exists('t', $options)) {
			$card['shadowText'] = true;
		} else {
			$card['shadowText'] = false;
		}
		
		if (array_key_exists('b', $options)) {
			$card['promo'] = true;
		} else {
			$card['promo'] = false;
		}
							
    list($frameImage, $frame_ini) = getFrameImage($card, $format, $elements);
		    
    $recipe  = "frame/$frameImage.svg";
    $textcfg  = parse_ini_file("typesetting/$frame_ini.ini", true);
    $frame_cfg = parse_ini_file("frame/$frame_ini.ini", true);
		
    $filenames[] = "$count {$card[$elements[$format]['name']]}";
    $thisFile = $filenames[$count - 1];
		
		if (array_key_exists('a', $options)) { $card[$elements[$format]['artist']] = $options['a']; }
		if (array_key_exists('d', $options)) { $card[$elements[$format]['designer']] = $options['d']; }
		if (array_key_exists('f', $options)) { $card[$elements[$format]['flavor']] = $options['f']; }
		if (array_key_exists('e', $options)) { $card[$elements[$format]['set']] = strtoupper($options['e']); }
		if (array_key_exists('z', $options)) { $textcfg['textbox']['size'] = $options['z']; }

		if ($card[$elements[$format]['rarity']] == 'basic land') {
			$card[$elements[$format]['rarity']] = 'land';
		}
				
		// Put symbol into file
		
		if ($card[$elements[$format]['set']]) {
			$symbolPath = $pwd . '/symbol/' . strtoupper($card[$elements[$format]['set']]) . '_' . strtoupper($card[$elements[$format]['rarity']])[0];			
		} else {
			$card[$elements[$format]['set']] = 'SET';
			$symbolPath = $pwd . '/symbol/SET_' . strtoupper($card[$elements[$format]['rarity']])[0];
		}
		
		if (!file_exists("$symbolPath.png")) {
			$symbolPath = $pwd . '/symbol/SET_' . strtoupper($card[$elements[$format]['rarity']])[0];
			if (!file_exists("$symbolPath.png")) {
				$symbolPath = $pwd . '/symbol/SET_C'; 
			}
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
		
		if (array_key_exists('u', $options)) {
			$card[$elements[$format]['number']] = $options['u'];
		}
		
		$TeX = createTeX($card, $format, $elements, $textcfg, $newSymbolWidth, $options);

		if (array_key_exists('v', $options)) {
			//$TeX = preg_replace('/\\\includegraphics[^\}]+\}/', '\{\color{blue}·}', $TeX);
		}
		
    file_put_contents("$pwd/output/$thisFile.tex", $TeX);
		
        
    if ($format == 'Scryfall') {
			$thisImage = "$pwd/art/" . $card[$elements[$format]['name']];
			if (!glob("$thisImage.*")) {
				$art_crop = (array)array($card['image_uris'])[0];
				$art_crop = $art_crop['art_crop'];
				copy($art_crop, "$thisImage.jpg");
			}
    } else {
			if (count($card['notes']) && array_key_exists('image', $card['notes'][0])) {
	      $thisImage = "$pwd/art/" . $card['notes'][0]['image'];				
			} else {
	      $thisImage = "$pwd/art/" . $card[$elements[$format]['name']];				
			}
    }
    
	  if (array_key_exists('v', $options)) {
		  // DVI to SVG export
    	exec("xelatex -interaction=nonstopmode -halt-on-error -output-directory='$pwd/output' -output-driver='xdvipdfmx -z0' -no-pdf --shell-escape \"$thisFile.tex\" && dvisvgm -B png -b papersize -T S0.75 --no-fonts=1 -s \"$pwd/output/$thisFile.xdv\" > \"$pwd/output/{$thisFile}_txt.svg\"");
			$preparesvg = file_get_contents("$pwd/output/{$thisFile}_txt.svg");
	    $preparesvg = str_replace("<?xml version='1.0' encoding='UTF-8'?>", "", $preparesvg);
	    $preparesvg = str_lreplace("</svg>", "", file_get_contents("cards/$frameImage.svg")) . $preparesvg . "</svg>";
			exec("inkscape -p \"$pwd/output/$thisFile.svg\" --batch-process -d 256 -o \"$pwd/output/$thisFile.pdf\" &>/dev/null");
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
			
			$image_offset = [0, 0];

		  if (array_key_exists('i', $options)) {
				$io = explode(',', $options['i']);
				$image_offset[0] = $io[0];
				$image_offset[1] = $io[1];
			}
			
			if (array_key_exists('b', $options) && array_key_exists('p', $options)) {
				$frame_cfg['art']['width']  += 72 + $image_offset[0];
				$frame_cfg['art']['height'] += 72 + $image_offset[1];
			}

      $artboxRatio = $frame_cfg['art']['width'] / $frame_cfg['art']['height'];
      $thisArtRatio = $imgWidth / $imgHeight;
			
			// echo "Art Box Ratio: $artboxRatio \n";
			// echo "This Art Ratio: $imgWidth / $imgHeight; \n";
			    
      // Greater width, larger ratio
      // Taller height, smaller ratio
			
			$newImage = base64_encode(file_get_contents($thisImage));
			
			if ($thisArtRatio > $artboxRatio) {
				// Wider
				$newWidth  = ($frame_cfg['art']['height'] / $imgHeight * $imgWidth);
				$newHeight = $frame_cfg['art']['height'];
				$newX = (($newWidth - $frame_cfg['art']['width']) / -2) + $image_offset[0];
				$newY = 0;
			} else {
				// Taller
				$newWidth  = $frame_cfg['art']['width'];
				$newHeight = ($frame_cfg['art']['width'] / $imgWidth * $imgHeight);
				$newX = 0;
				$newY = (($newHeight - $frame_cfg['art']['height']) / -2) + $image_offset[1];
			}
			
			if (!$card['promo']) {
				$newX += 3;
				$newY += 3;
			}
			
			$preparesvg = str_replace('xlink:href="{$thisArt}"', "x=\"" . $newX . "\" y=\"" . $newY . "\" width=\"$newWidth\" height=\"$newHeight\" xlink:href=\"data:$imgType;base64,$newImage\"", $preparesvg);
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
			if ($card['promo']) {
		  	$preparesvg = str_replace('width="744" height="1039" viewBox="0 0 744 1039" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">', 'width="816" height="1110" viewBox="0 0 816 1110" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">', $preparesvg);
				$preparesvg = str_replace('<mask id="artmask"><path d="m0 0h744v959h-744z"', '<mask id="artmask"><path d="m0 0h816v1031h-816z"', $preparesvg);
				$preparesvg = preg_replace('/mask="url\(#artmask\)"\/>\s*<\/g>/', 'mask="url(#artmask)"/><g transform="translate(37.5,35.5)">', $preparesvg);
				$preparesvg = str_lreplace('</svg>', '</g></svg>', $preparesvg);
			} else {
		  	$preparesvg = str_replace('width="744" height="1039" viewBox="0 0 744 1039" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">', 'width="816" height="1110" viewBox="0 0 816 1110" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="m0 0h816v1110h-816z"/><g transform="translate(37.5,35.5)">', $preparesvg);
			}
			$preparesvg = str_lreplace('</svg>', '</g></svg>', $preparesvg);

			// Move logo up for printing
		}
		
		if (array_key_exists('c', $options)) {
			$preparesvg = str_replace('"translate(543,990)"',  '"translate(543,970)"', $preparesvg);
			$preparesvg = str_replace('"translate(543,1006)"', '"translate(543,986)"', $preparesvg);
			$preparesvg = str_replace('"translate(543,1007)"', '"translate(543,986)"', $preparesvg);
			$preparesvg = str_replace('"translate(543,1010)"', '"translate(543,990)"', $preparesvg);
		}
				
    file_put_contents("$pwd/output/$thisFile.svg", $preparesvg);
		
    
	  if (array_key_exists('x', $options)) {
			echo "Exporting frame to PNG (1600 DPI)\n";
			exec("inkscape -p \"$pwd/output/$thisFile.svg\" --batch-process -d 512 -o \"$pwd/output/$thisFile.png\" &>/dev/null");
		} else {
			echo "Exporting frame to PNG (800 DPI)\n";
			exec("inkscape -p \"$pwd/output/$thisFile.svg\" --batch-process -d 256 -o \"$pwd/output/$thisFile.png\" &>/dev/null");
		// echo "Exporting frame to PNG (300 DPI)\n";
    // exec("inkscape -p \"$pwd/output/$thisFile.svg\" --batch-process -d 96 -o \"$pwd/output/$thisFile 300.png\" &>/dev/null");
		}
		
		$density = 800;
		$final_width = 1984;
		$final_height = 2771;
		
	  if (array_key_exists('x', $options)) {
			$density = $options['x'];
			$final_width = int(2.48 * $options['x']);
			$final_height = int(3.46375 * $options['x']);
		}
		
	  if (!array_key_exists('v', $options)) {
	    exec("convert -density $density -geometry $final_width -transparent white \"$pwd/output/{$thisFile}_txt.pdf\" \"$pwd/output/{$thisFile}_txt.png\"");
	    // exec("convert -density 300 -geometry 744 -transparent white \"$pwd/output/{$thisFile}_txt.pdf\" \"$pwd/output/{$thisFile}_txt 300.png\"");
			
			// If it’s PDF, we need to layer the PDF onto the SVG output
			
			if (array_key_exists('p', $options)) {
	      $base  = imagecreatetruecolor($final_width + int($density * .24), $final_height + int($density * .24));
				$compo = imagecreatetruecolor($final_width, $final_height);
			} else {
        $base  = imagecreatetruecolor($final_width, $final_height);
				$compo = imagecreatetruecolor($final_width, $final_height);
			}
			
      imagealphablending($compo, true);
      imagesavealpha($compo, true);
      $base  = imagecreatefrompng("$pwd/output/$thisFile.png");
      $compo = imagecreatefrompng("$pwd/output/{$thisFile}_txt.png");
			
		  if (array_key_exists('p', $options)) {
				imagecopy($base, $compo, ($density / 3), (($density / 3) * .95), 0, 0, imagesx($compo), imagesy($compo));				
			} else {
				imagecopy($base, $compo, 0, 0, 0, 0, imagesx($compo), imagesy($compo));				
			}
			
			imagepng($base, "$pwd/output/$thisFile.png");
      imagedestroy($base);
			
			/*
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
			*/
			  
		} else {
			echo "Exporting frame to PDF\n";
	    exec("inkscape -p \"$pwd/output/$thisFile.svg\" --export-pdf-version=1.4 --batch-process -o \"$pwd/output/$thisFile.pdf\" &>/dev/null");
		}
		
	  if (!array_key_exists('k', $options)) {
			// unlink("$pwd/output/{$thisFile}_txt 300.png");
			
			foreach (["_txt.pdf", "_txt.png", "_txt.svg", ".aux", ".log", ".tex", ".xdv"] as $tempext) {
				if (file_exists("$pwd/output/$thisFile" . $tempext)) {
					unlink("$pwd/output/$thisFile" . $tempext);
				}
			}			
		}
		
		if ($early_abort) {
			die;
		}
  }
?>