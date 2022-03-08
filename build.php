<?php
  function dynamic_sub($match){
    global ${$match[1]};
    return "${$match[1]}";
  }
	
	function cmp($a, $b) {
		return ($a <=> $b);
	}
	
	$options = getopt("p");
	  
  $iniList = array(
		'regular_land_R',
		'regular_blank',
		'regular_creature_legendary_multicolor_R',
		'regular_creature_legendary_multicolor',
		'regular_creature_legendary_R',
		'regular_creature_legendary',
		'regular_creature_multicolor_R',
		'regular_creature_multicolor',
		'regular_creature_R',
		'regular_creature',
		'regular_land_legendary_R',
		'regular_land_legendary',
		'regular_land',
		'regular_legendary_multicolor_R',
		'regular_legendary_multicolor',
		'regular_legendary_R',
		'regular_legendary',
		'regular_multicolor_R',
		'regular_multicolor',
		'regular_planeswalker_DFC_R',
		'regular_planeswalker_DFC',
		'regular_planeswalker_R',
		'regular_planeswalker',
		'regular_planeswalker4_DFC_R',
		'regular_planeswalker4_DFC',
		'regular_planeswalker4_R',
		'regular_planeswalker4',
		'regular_planeswalker5_R',
		'regular_planeswalker5',
		'regular_R',
		'regular_vehicle_legendary_multicolor_R',
		'regular_vehicle_legendary_multicolor',
		'regular_vehicle_legendary_R',
		'regular_vehicle_legendary',
		'regular_vehicle_multicolor_R',
		'regular_vehicle_multicolor',
		'regular_vehicle_R',
		'regular_vehicle',
		'regular',
		'saga_borderless',
		'token_large_creature_R',
		'token_large_creature',
		'token_large_R',
		'token_large',
		'token_medium_creature_old_R',
		'token_medium_creature_old',
		'token_medium_creature_R',
		'token_medium_creature',
		'token_medium_R',
		'token_medium',
		'token_small_creature_legendary_multicolor_promo_R',
		'token_small_creature_legendary_multicolor_promo',
		'token_small_creature_legendary_promo_R',
		'token_small_creature_legendary_promo',
		'token_small_creature_legendary',
		'token_small_creature_R',
		'token_small_creature',
		'token_small_legendary_promo_R',
		'token_small_legendary',
		'token_small_promo_R',
		'token_small_R',
		'token_small'
  );
		      
  foreach($iniList as $inikey) {
		echo "$inikey\n";
		
    foreach (array('A','W','U','B','R','G','M','C','WU','WB','UB','UR','BR','BG','RG','RW','GW','GU') as $colorkey) {
      if ((strpos($inikey, 'multicolor') !== false) && (strlen($colorkey) == 1)) {
        continue;
      }
      
      $COLOR = $colorkey;
      $BORDER_COLOR = 'B'; /* TODO: add other colors */
      $SAVENAME = "cards/{$inikey}_{$colorkey}.svg";
      $PNGNAME = "cards/{$inikey}_{$colorkey}.png";
      $ININAME = "frame/$inikey.ini";
    
      $default_recipe = array(
        'background1' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',
          'index' => 10,
        ),
        'background2' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',
          'index' => 20,
        ),
        'textbackground' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 20,
        ),
        'accent1' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 101,
        ),
        'accent2' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 102,
        ),
        'accent3' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 103,
        ),
        'accent4' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 104,
        ),
        'accent5' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 105,
        ),
        'accent6' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 106,
        ),
        'accent7' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 107,
        ),
        'accent8' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 108,
        ),
        'title' => array(
          'x' => 0,
          'y' => 0,
					'color' => "#000",
					'color_if_white' => "#000",
          'width' => 'auto',
          'height' => 'auto',
          'index' => 300,
        ),
        'typeline' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',
          'index' => 310,
        ),
        'artbox' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 400,
        ),
        'textborder' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 600,
        ),
        'border' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',
          'index' => 900,
        ),
        'blackout' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',
          'index' => 901,
        ),
        'crown-accent' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 905,
        ),
        'crown' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 910,
        ),
        'crown-outline' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 920,
        ),
        'title-crown' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',      
          'index' => 930,
        ),
				'logo' => array(
					'x' => 543,
					'y' => 1006,
					'align' => 'left',
					'file' => 'border/bumper.svg',
					'index' => 990,
				),
      );
    
      $read_recipe = parse_ini_file("$ININAME", true);
			
			unset($read_recipe['meta']);
			unset($read_recipe['defaults']);
			$recipe = array_merge($default_recipe, $read_recipe);
						
		  uasort($recipe, function($a, $b){
				return ((int)$a['index']) <=> ((int)$b['index']);
		  });
			
		  if (array_key_exists('p', $options)) {
				$recipe['logo']['y'] -= 20;
			}
			      
      $svg_header = '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="miter" stroke-miterlimit="3" width="744" height="1039" viewBox="0 0 744 1039" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">';
			
      $defs_filter = '';
      $svg = '';
  
      foreach ($recipe as $key => $value) {    
        if ($key === 'meta')
          continue;
				
				if (!array_key_exists('x', $value)) { $recipe[$key]['x'] = 0; }
				if (!array_key_exists('y', $value)) { $recipe[$key]['y'] = 0; }
				
        if (array_key_exists('file', $value)) {
          // Substitute variables
          $recipe[$key]['file'] = preg_replace_callback("/\{\\\$([\w\d\_]+)\}/", 'dynamic_sub', $recipe[$key]['file']);
										
          $filetype = explode('.', $recipe[$key]['file']);
          $filetype = strtolower($filetype[count($filetype) - 1]);
										
          if (in_array($filetype, array('png', 'jpg', 'jpeg'))) {
            if (strtolower($key) !== 'background12') {
              $svg .= '<image x="' . $recipe[$key]['x'] . '" y="' . $recipe[$key]['y'] . '" width="' . $recipe[$key]['width'] . '" height="' . $recipe[$key]['height'] . '" xlink:href="data:image/' . $filetype . ';base64,' . base64_encode(file_get_contents($recipe[$key]['file'])) . '"/>';
            }
          } else if ($filetype === 'svg') {						
            $file_contents = file_get_contents($recipe[$key]['file']);
            $file_contents = preg_replace('/<svg [^>]+>/', '', $file_contents);
        
            if (isset($recipe[$key]['filter'])) {
              foreach(array('dx', 'dy', 'stdDeviation', 'flood-color', 'flood-opacity') as $k) {
                if (!isset($recipe[$key][$k])) {
                  switch($k) {
                    case 'dx':
                      $recipe[$key][$k] = 0;
                      break;
                    case 'dy':
                      $recipe[$key][$k] = 0;
                      break;
                    case 'stdDeviation':
                      $recipe[$key][$k] = 1;
                      break;
                    case 'flood-color':
                      $recipe[$key][$k] = '#000';
                      break;
                    case 'flood-opacity':
                      $recipe[$key][$k] = 1;
                      break;
                  }
                }
              }
          
              if ($recipe[$key]['filter'] == 'feDropShadow') {
                $defs_filter .= "
                <filter id=\"$key\" x=\"-100%\" y=\"-100%\" width=\"250%\" height=\"250%\">
                  <feGaussianBlur in=\"SourceAlpha\" stdDeviation=\"{$recipe[$key]['stdDeviation']}\"/>
            			<feOffset dx=\"{$recipe[$key]['dx']}\" dy=\"{$recipe[$key]['dy']}\" result=\"offsetblur\"/>
            			<feFlood flood-color=\"{$recipe[$key]['flood-color']}\" flood-opacity=\"{$recipe[$key]['flood-opacity']}\"/>
            			<feComposite in2=\"offsetblur\" operator=\"in\"/>
            			<feMerge>
            				<feMergeNode/>
            				<feMergeNode in=\"SourceGraphic\"/>
            			</feMerge>
                </filter>";
              } else if ($recipe[$key]['filter'] == 'feGaussianBlur') {
                $defs_filter .= "
                <filter id=\"$key\" x=\"-100%\" y=\"-100%\" width=\"250%\" height=\"250%\">
                  <feGaussianBlur in=\"SourceGraphic\" stdDeviation=\"{$recipe[$key]['stdDeviation']}\" result=\"$key\"/>
            			<feMerge>
            				<feMergeNode/>
            				<feMergeNode in=\"$key\"/>
            			</feMerge>
                </filter>";
              }
        
              $file_contents = str_replace('"></svg>', ' style="filter:url(#' . $key . ')">', $file_contents);
              $file_contents = str_replace('/></svg>', ' style="filter:url(#' . $key . ')"/>', $file_contents);
              $file_contents = str_replace('></svg>', '>', $file_contents);
          
            } else {
              $file_contents = str_replace('</svg>', '', $file_contents);
            }
						
						//echo "\n\nThe key is…\n";
						
						if (!array_key_exists('x', $value)) { $recipe[$key]['x'] = 0; }
						if (!array_key_exists('y', $value)) { $recipe[$key]['y'] = 0; }
						if (array_key_exists('align', $value)) {
							if ($recipe[$key]['align'] == 'right') {
								preg_match("/viewbox=\"\s*\d+\s+\d+\s+(\d+)\s+(\d+)\s*\"/", $file_contents, $d);
		            $svg .= '<g transform="translate(' . ($recipe[$key]['x'] - $d[1]) . ',' . $recipe[$key]['y'] . ')">' . $file_contents . '</g>';								
							} else {
		            $svg .= '<g transform="translate(' . $recipe[$key]['x'] . ',' . $recipe[$key]['y'] . ')">' . $file_contents . '</g>';
							}			
						} else {
	            $svg .= '<g transform="translate(' . $recipe[$key]['x'] . ',' . $recipe[$key]['y'] . ')">' . $file_contents . '</g>';
						}
          }
        }
      }
						
			if (strpos($inikey, 'blank') !== false) {
				$svg_header = '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="miter" stroke-miterlimit="3" width="819" height="1114.5" viewBox="0 0 819 1114.5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="m0 0h819v1114.5h-819z" fill="#000"/><g transform="translate(37.5, 37.75)">' . $svg_header;
				$svg .= '</svg></g></svg>';
			} else {
	      $svg .= '</svg>';				
			}
			
      //$svg = str_replace('</g></svg></g></svg>', '</g></g></svg>', $svg);
  
      file_put_contents($SAVENAME, ($svg_header . $defs_filter . $svg));
      // exec("svgexport $SAVENAME $PNGNAME");
    }
  }
?>