<?php
  function dynamic_sub($match){
    global ${$match[1]};
    return "${$match[1]}";
  }
  
  $iniList = array(
    'regular','regular_R',
		'regular_creature','regular_creature_R',
		'regular_land','regular_land_R',
		'regular_multicolor','regular_multicolor_R',
		'regular_vehicle','regular_vehicle_R',
		'regular_vehicle_multicolor','regular_vehicle_multicolor_R',
		'regular_creature_multicolor','regular_creature_multicolor_R',
		'token_medium',
		'regular_creature_legendary_multicolor','regular_creature_legendary_multicolor_R',
		'regular_legendary_multicolor','regular_legendary_multicolor_R',
		'regular_legendary','regular_legendary_R',
		'regular_creature_legendary','regular_creature_legendary_R',
    'regular_land_legendary','regular_land_legendary_R',
		'regular_vehicle_legendary','regular_vehicle_legendary_R',
		'regular_vehicle_legendary_multicolor','regular_vehicle_legendary_multicolor_R',
    'regular_planeswalker_R','regular_planeswalker'
  );
  
  $iniList = array('regular_vehicle','regular_vehicle_R',
		'regular_vehicle_multicolor','regular_vehicle_multicolor_R');
  //$iniList = array('regular','regular_R', 'regular_creature', 'regular_creature_R', 'regular_creature_multicolor', 'regular_creature_multicolor_R');
    
  foreach($iniList as $inikey) {
    foreach (array('A','W','U','B','R','G','M','C','WU','WB','UB','UR','BR','BG','RG','RW','GW','GU') as $colorkey) {
      if ((strpos($inikey, 'multicolor') !== false) && (strlen($colorkey) == 1)) {
        continue;
      }
      
      $COLOR = $colorkey;
      $BORDER_COLOR = 'B'; /* TODO: add other colors */
      $SAVENAME = "cards/{$inikey}_{$colorkey}.svg";
      $PNGNAME = "cards/{$inikey}_{$colorkey}.png";
      $ININAME = "$inikey.ini";
    
      $default_recipe = array(
        'background1' => array(
          'x' => 0,
          'y' => 0,
          'width' => 'auto',
          'height' => 'auto',
          'index' => 10,
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
          'index' => 110,
        ),
        'title' => array(
          'x' => 0,
          'y' => 0,
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
      );
    
      $read_recipe = parse_ini_file("$ININAME", true);
      $recipe_keys = array_keys(array_merge($default_recipe, $read_recipe));
    
      foreach($recipe_keys as $key) {
        if (isset($read_recipe[$key])) {
          $read_keys = array_keys($read_recipe[$key]);
        
          foreach($read_keys as $read_key => $read_value)
          $default_recipe[$key][$read_value] = $read_recipe[$key][$read_value];
        }
      }
    
      $recipe = array();  
      foreach ($default_recipe as $key => $value) {
        $recipe[] = array($key => $value);
      }
      
      $svg_header = '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="miter" stroke-miterlimit="3" width="744" height="1039" viewBox="0 0 744 1039" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">';
  
      $defs_filter = '';
      $svg = '';
  
      for ($i = 0; $i < count($recipe); $i++) {
    
        $key = key($recipe[$i]);
    
        if (strtolower($key) === 'meta')
          continue;
    
        $section = $recipe[$i][$key];
    
        if (isset($section['file'])) {
          // Substitute variables
          $section['file'] = preg_replace_callback("/\{\\\$([\w\d\_]+)\}/", 'dynamic_sub', $section['file']);
            
          $filetype = explode('.', $section['file']);
          $filetype = strtolower($filetype[count($filetype) - 1]);
          if (in_array($filetype, array('png', 'jpg', 'jpeg'))) {
            if (strtolower($key) !== 'background12') {
              $svg .= '<image x="' . $section['x'] . '" y="' . $section['y'] . '" width="' . $section['width'] . '" height="' . $section['height'] . '" xlink:href="data:image/' . $filetype . ';base64,' . base64_encode(file_get_contents($section['file'])) . '"/>';
            }
          } else if ($filetype === 'svg') {
            $file_contents = file_get_contents($section['file']);
            $file_contents = preg_replace('/<svg [^>]+>/', '', $file_contents);
        
            if (isset($section['filter'])) {
              foreach(array('dx', 'dy', 'stdDeviation', 'flood-color', 'flood-opacity') as $k) {
                if (!isset($section[$k])) {
                  switch($k) {
                    case 'dx':
                      $section[$k] = 0;
                      break;
                    case 'dy':
                      $section[$k] = 0;
                      break;
                    case 'stdDeviation':
                      $section[$k] = 1;
                      break;
                    case 'flood-color':
                      $section[$k] = '#000';
                      break;
                    case 'flood-opacity':
                      $section[$k] = 1;
                      break;
                  }
                }
              }
          
              if ($section['filter'] == 'feDropShadow') {
                $defs_filter .= "
                  <filter id=\"$key\" x=\"-100%\" y=\"-100%\" width=\"250%\" height=\"250%\">
                    <feGaussianBlur in=\"SourceAlpha\" stdDeviation=\"{$section['stdDeviation']}\"/>
              			<feOffset dx=\"{$section['dx']}\" dy=\"{$section['dy']}\" result=\"offsetblur\"/>
              			<feFlood flood-color=\"{$section['flood-color']}\" flood-opacity=\"{$section['flood-opacity']}\"/>
              			<feComposite in2=\"offsetblur\" operator=\"in\"/>
              			<feMerge>
              				<feMergeNode/>
              				<feMergeNode in=\"SourceGraphic\"/>
              			</feMerge>
                  </filter>";
               } else if ($section['filter'] == 'feGaussianBlur') {
          
          
              $defs_filter .= "
                <filter id=\"$key\" x=\"-100%\" y=\"-100%\" width=\"250%\" height=\"250%\">
                  <feGaussianBlur in=\"SourceAlpha\" stdDeviation=\"{$section['stdDeviation']}\"/>
                </filter>";
              }
        
              $file_contents = str_replace('"></svg>', ' style="filter:url(#' . $key . ')">', $file_contents);
              $file_contents = str_replace('/></svg>', ' style="filter:url(#' . $key . ')"/>', $file_contents);
              $file_contents = str_replace('></svg>', '>', $file_contents);
          
            } else {
              $file_contents = str_replace('</svg>', '', $file_contents);
            }
        
            $svg .= '<g transform="translate(' . $section['x'] . ',' . $section['y'] . ')">' . $file_contents . '</g>';
          }
        }
      }
  
      $svg .= '</svg>';
      $svg = str_replace('</g></svg></g></svg>', '</g></g></svg>', $svg);
  
      file_put_contents($SAVENAME, ($svg_header . $defs_filter . $svg));
      // exec("svgexport $SAVENAME $PNGNAME");
    }
  }
?>