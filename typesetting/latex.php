<?php

function fixContextualAlternates($theText)
{
	return preg_replace('/([fhkmn])(?=[^\sFJgjpy])/', '\noalt{$1}', $theText);
}

function getFrameImage($cardData, $fmt, $els)
{
  $color = 'C';
  $frameImage = 'regular';
  $frameIni = 'regular';
  
	echo "Format: $fmt\n";
	
  if ($fmt == 'LackeyBot') {
    $cardData['color'] = trim(preg_replace('/[\{\}]/', '', $cardData[$els[$fmt]['color']]));
    switch ($cardData['color']) {
      case 'White': $color = 'W';
      break;
      case 'Blue':  $color = 'U';
      break;
      case 'Black': $color = 'B';
      break;
      case 'Red':   $color = 'R';
      break;
      case 'Green': $color = 'G';
      break;
      default:      $color = 'C';
    }
		echo "Color: $color\n";
  }
    
  if (stripos($cardData[$els[$fmt]['types']], 'Token') !== false) {
    //echo "Is there text? " . strlen(trim($cardData[$els[$fmt]['text']])) . "\n";
    if (trim($cardData[$els[$fmt]['text']])) {
      $frameImage = 'token_medium';
      $frameIni = 'token_medium';
    } else {
      $frameImage = 'token_small';
      $frameIni = 'token_small';
    }
  }
  
  $isVehicle = $isLand = $isPlaneswalker = $isLegendary = $isMulticolor = $isPromo = $isCreature = $isRare = false;
  
  if (stripos($cardData[$els[$fmt]['types']], 'Legendary') !== false) {
    $isLegendary = 'legendary';
  }

  if (stripos($cardData[$els[$fmt]['types']], 'Creature') !== false) {
    $isCreature = 'creature';
    $frameIni .= '_creature';
  }

  if (stripos($cardData[$els[$fmt]['types']], 'Vehicle') !== false) {
    $isVehicle = 'vehicle';
  }

  if (stripos($cardData[$els[$fmt]['types']], 'Land') !== false) {
    $isVehicle = 'land';
  }

  if (stripos($cardData[$els[$fmt]['types']], 'Planeswalker') !== false) {
    $isPlaneswalker = 'planeswalker';
    $frameIni = 'planeswalker';
  }
  
  if (in_array(strtoupper($cardData[$els[$fmt]['rarity']][0]), array('R', 'M', 'P', 'S'))) {
    $isRare = 'R';
  }
  
  $recipeIngredients = array('isVehicle', 'isLand', 'isLegendary', 'isMulticolor', 'isPromo', 'isCreature', 'isRare', 'color');

  for ($i = 0; $i < count($recipeIngredients); $i++) {
    if (${$recipeIngredients[$i]}) {
      $frameImage .= "_" . ${$recipeIngredients[$i]};
    }
  }
  
  return array($frameImage, $frameIni);
}

function sanitizeText($string)
{
	//$string = str_replace('\\', '\textbackslash ', $string);
	$string = preg_replace('/(?=[\&\%\$\#\_\{\}])/', '\\', $string);
	$string = str_replace('~', '\textasciitilde ', $string);
	$string = str_replace('^', '\textasciicircum ', $string);

	return trim($string);
}

function substituteRules($rules)
{
	//$rules = str_replace('\\\\', '\n', $rules);
	$rules = str_replace("'", "’", $rules);
	$rules = sanitizeText($rules);
	$rules = str_replace("\n", '\vspace{.5\baselineskip}\newline ', $rules);
	$rules = preg_replace('/\(([\S^\)][^\)]*[\S^\)])\)/', '\emph{($1)}' , $rules);
	$rules = preg_replace('/\*([^\*]+)\*/', '\emph{$1}', $rules);
	
	return trim($rules);
}

function substituteFlavor($flavor)
{
	//$flavor = str_replace('\\\\', '\n', $flavor);
	$flavor = sanitizeText($flavor);
	$flavor = str_replace("\n", '\newline ', $flavor);
	$flavor = preg_replace('/\*([^\*]+)\*/', '\emph{$1}', trim($flavor));

	return $flavor;
}


function createMana($mana)
{
	$mana_array = preg_split("/[\{\}\(\)\[\]]+/", $mana, 0, PREG_SPLIT_NO_EMPTY);
	$mana_tex = '';
	
	foreach ($mana_array as $mana_element) {
		switch ($mana_element) {
			case '0': case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9': case '10': case '11': case '12': case '13': case '14': case '15': case '16': case '17': case '18': case '19': case '20': case 'C': case 'S': case 'T':
				        $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{genericmana}{O}' . $mana_element; break;
      case 'W': $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{whitemana}{O}W'; break;
      case 'U': $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{bluemana}{O}U'; break;
      case 'B': $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{blackmana}{O}B'; break;
      case 'R': $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{redmana}{O}R'; break;
      case 'G': $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{greenmana}{O}G'; break;
      case 'C': $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{colorlessmana}{O}C'; break;
      case 'S': $mana_tex .= '\fontsize{44pt}{44pt}\mana\selectfont\textcolor{black}{\raisebox{-3.5pt}{O}}\kern 1pt\textcolor{colorlessmana}{O}S'; break;
/*
			case '{W/P}'
			case '{W/U}'
			case '{W/B}'
			case '{W/U/P}'
			case '{W/B/P}'
			*/			
		}
	}
	return $mana_tex;
}

function createTeX($cardData, $fmt, $els, $cfg, $opt)
{
	$pwd = exec('pwd');
	
	$mana_tex = createMana($cardData[$els[$fmt]['manacost']]);
	
  $name   = preg_replace('/([fhkmn]) /', '\alt{' . "$1" . '} ', $cardData[$els[$fmt]['name']] . ' ');
	$name   = fixContextualAlternates($cardData[$els[$fmt]['name']]);

  $name   = str_replace("'", '’', $name);
	$types  = preg_replace('/([fhkmn]) /', '\alt{' . "$1" . '} ', $cardData[$els[$fmt]['types']] . ' ');
	$types  = fixContextualAlternates($cardData[$els[$fmt]['types']]);
	
  $set    = strtoupper($cardData[$els[$fmt]['set']]);
  
  $rarity = (strtoupper($cardData[$els[$fmt]['rarity']][0]));
  
/*  if ($cardData['collector_total']) {
    $cnline = $cardData[$els[$fmt]['number'] . '\kern .25em/\kern .25em ' . $cardData[$els[$fmt]['collector_total'];
  } else {
*/  $cnline = $cardData[$els[$fmt]['number']];
//}

  $cardData['setSymbolWidth'] = 40;
  
  if ($cfg['title']['align'] !== 'center') {
    $cfg['title']['align'] = 'flushleft';
  }
	
	$titleColor = 'black';
	if ($cfg['title']['color']) {
		$titleColor = $cfg['title']['color'];		
	}
	
	$titleColorIfWhite = $titleColor;
	
	if (trim(preg_replace('/[\{\}]/', '', $cardData[$els[$fmt]['color']]))[0] == 'W') {
		if ($cfg['title']['color_if_white']) {
			$titleColor = $cfg['title']['color_if_white'];
		}
	}

	if ($titleColor == '#000') {
		$titleColor = 'black';
	} else if (($titleColor == '#fff') || ($titleColor == 'white')) {
		$titleColor = 'offwhite';
	}
		
	$text = trim(substituteRules($cardData[$els[$fmt]['text']]));
	$flavor = trim(substituteFlavor($cardData[$els[$fmt]['flavor']]));
		
  $buffer = '\documentclass{article}
  \usepackage[absolute]{textpos}
  \usepackage{calc}
  \usepackage{xcolor}
  \usepackage{fontspec}
  \usepackage{lmodern}
  \usepackage[none]{hyphenat}
  \usepackage{enumitem}
  \usepackage{graphicx}
  \usepackage{setspace}

  \usepackage[
    paperwidth=744bp,
    paperheight=1039bp,
    tmargin=0bp,
    rmargin=0bp,
    lmargin=0bp,
    bmargin=0bp
  ]{geometry}

  \newfontfamily\beleren[
    Path = fonts/ ,
    UprightFont = *-Bold,
    BoldFont = *-Bold,
    ItalicFont = *-Bold,
    Extension = .ttf,
    PunctuationSpace = 0,
		Contextuals=WordFinal,
		Ligatures = Common
  ]{Beleren2016}

  \newfontfamily\belerensmallcaps[
    Path = fonts/ ,
    UprightFont = *-Bold,
    BoldFont = *-Bold,
    ItalicFont = *-Bold,
    Extension = .ttf,
    PunctuationSpace = 0
  ]{BelerenSC}
  
  \newfontfamily\gotham[
    Path = fonts/ ,
    UprightFont = *-Medium,
    UprightFeatures = {Ligatures = NoCommon},
    Extension = .otf,
    PunctuationSpace = 0
  ]{Gotham}

  \newfontfamily\gothamlight[
    Path = fonts/ ,
    UprightFont = *-Light,
    UprightFeatures = {Ligatures = NoCommon},
    Extension = .otf,
    PunctuationSpace = 0
  ]{Gotham}

  \newfontfamily\mana[
    Path = fonts/ ,
    UprightFont = *,
    Extension = .ttf,
    PunctuationSpace = 0
  ]{MTG2016}

  \newfontfamily\plantin[
    Path = fonts/ ,
    BoldFont = *-Bold,
    ItalicFont = *-Italic,
    UprightFeatures = {Ligatures = NoCommon},
    BoldFeatures = {Ligatures = NoCommon},
    ItalicFeatures = {Ligatures = NoCommon},
    Extension = .otf,
    PunctuationSpace = 0
  ]{PlantinMTPro}

   \definecolor{genericmana}   {RGB} {215, 208, 205}
   \definecolor{colorlessmana} {RGB} {215, 208, 205}
   \definecolor{whitemana}     {RGB} {254, 253, 223}
   \definecolor{bluemana}      {RGB} {186, 231, 251}
   \definecolor{blackmana}     {RGB} {215, 208, 205}
   \definecolor{redmana}       {RGB} {250, 186, 159}
   \definecolor{greenmana}     {RGB} {171, 221, 189}
   \definecolor{blackborder}   {RGB} { 35,  31,  32}
   \definecolor{darkblackmana} {RGB} {143, 138, 135}
   \definecolor{goldmana}      {RGB} {246, 210,  98}
   \definecolor{offwhite}      {RGB} {254, 254, 254}
  
  % 241, 243, 236
  %  13, 112, 181
  %  33,  31,  26
  % 228,  46,  27
  %  16, 112,  50

  \pagenumbering{gobble}
  \parindent=0pt

	\newcommand{\alt}[1]{\begingroup\addfontfeature{RawFeature=+fina}#1\addfontfeature{RawFeature=-fina}\endgroup}
	\newcommand{\noalt}[1]{\begingroup\addfontfeature{RawFeature=-fina}#1\addfontfeature{RawFeature=+fina}\endgroup}
  
	\color{black}

  \begin{document}
  \newlength{\titlelength}
  \newlength{\manalength}

  \begin{textblock*}{' . ($cfg['manacost']['x'] - $cfg['title']['x']) . 'bp}(' . ($cfg['title']['x']) . 'bp, ' . $cfg['title']['y'] . 'bp)
  \fontsize{44pt}{44pt}
  \mana
	\color{' . $titleColor . '}
  \selectfont
  \settowidth{\manalength}{' . $mana_tex . '\strut}

  \\' . str_replace(' ', '', strtolower($cfg['title']['font'])) . '
  \count255 = ' . $cfg['title']['size'] . '

  \loop
  \fontsize{\count255 pt}{\count255 pt}
  \selectfont
  \settowidth{\titlelength}{' . $name . '\strut}
  \addtolength{\titlelength}{\manalength}
  \ifdim \titlelength >' . ($cfg['manacost']['x'] - $cfg['title']['x']) . 'bp
  \advance \count255 by -1
  \repeat
  
  
  \ifnum\count255 >23
  \begin{' . $cfg['title']['align'] . '}' . 
  $name . '\strut
  \end{' . $cfg['title']['align'] . '}
  \else
  \relax  
  \fi
	\end{textblock*}
	';
	
	if ($cfg['manacost']['align'] == 'right') {
		$buffer .= '\begin{textblock*}{' . $cfg['manacost']['width'] . 'bp}(' . ($cfg['manacost']['x'] - $cfg['manacost']['width']) . 'bp, ' . $cfg['manacost']['y'] . 'bp)
	\begin{flushright}
	';
	} else {
		$buffer .= '\begin{textblock*}{' . $cfg['manacost']['width'] . 'bp}(' . $cfg['manacost']['x'] . 'bp, ' . $cfg['manacost']['y'] . 'bp)
	\begin{flushleft}
	';
	}
	
  $buffer .= $mana_tex . '\strut
	';
	if ($cfg['manacost']['align'] == 'right') {
	  $buffer .= '\end{flushright}
  ';
	} else {
	  $buffer .= '\end{flushleft}
  ';
	}
  $buffer .= '\end{textblock*}

  \begin{textblock*}{' . ($cfg['symbol']['x'] - $cfg['typeline']['x'] - $cardData['setSymbolWidth']) . 'bp}(' . $cfg['typeline']['x'] . 'bp, ' . $cfg['typeline']['y'] . 'bp)
  \beleren
  \selectfont
  \count255 = ' . $cfg['typeline']['size'] . '
  \loop
  \fontsize{\the\dimexpr(\the\count255 pt)}{' . $cfg['typeline']['size'] . 'pt}
  \selectfont
  \settowidth{\titlelength}{' . $types . '\strut}
  \ifdim \titlelength >' . ($cfg['symbol']['x'] - $cfg['typeline']['x'] - $cardData['setSymbolWidth']) . 'bp
  \advance \count255 by -1
  \repeat
  
  ' . $types . '\strut
  \end{textblock*}
	
	% Set Symbol
	\begin{textblock*}{' . $cardData['setSymbolWidth'] . 'bp}(' . $cfg['typeline']['x'] . 'bp, ' . $cfg['typeline']['y'] . 'bp)
	% \includegraphics{' . $pwd . '/symbol/' . $cardData[$els[$fmt]['set']] . '_' . strtoupper($cardData[$els[$fmt]['rarity']])[0] . '.png} 
	\end{textblock*}

  % Rules and Flavor Text
  \newlength{\rulesheight}
  \count255 = ' . (array_key_exists('size', $cfg['textbox']) ? $cfg['textbox']['size'] : $cfg['defaults']['size']) . '
  \plantin
  \selectfont
  \setstretch{0.97}

  \loop
  \fontsize{\count255 pt}{\count255 pt}
  \selectfont
  \settototalheight{\rulesheight}{\parbox{' . $cfg['textbox']['width'] . 'bp}{\begin{flushleft}';
  
  if ($text && $flavor) {
    $buffer .= $text . '\relax\newline\vspace{-.2em}\includegraphics[scale=0.375]{' . $pwd . '/typesetting/flavorbar.png}\newline ' . $flavor;
	} else if ($text && !$flavor) {
		$buffer .= $text;
  } else if (!$text && $flavor) {
  	$buffer .= $flavor;
  }
  
  $buffer .= '\relax
  \end{flushleft}}}
  \ifdim \rulesheight>' . $cfg['textbox']['height'] . 'bp
  \advance \count255 by -1
  \repeat

  \begin{textblock*}{' . $cfg['textbox']['width'] . 'bp}[0, 0.5](' . $cfg['textbox']['x'] . 'bp, ' . ($cfg['textbox']['y'] + ($cfg['textbox']['height'] / 2)) . 'bp)

  \fontsize{\count255 pt}{\count255 pt}
  \plantin
  \selectfont
	';
	
	$textBegin = '\begin{flushleft}';
	$textEnd   = '\end{flushleft}';
	
	if ($cfg['textbox']['align'] == "center") {
	  $textBegin = '\begin{center}';
		$textEnd   = '\end{center}';
	}
	
  $buffer .= $textBegin;
  
  if ($text && $flavor) {
    $buffer .= $text . '\relax\newline\vspace{-.2em}\includegraphics[scale=0.375]{' . $pwd . '/typesetting/flavorbar.png}\newline ' . $flavor;
	} else if ($text && !$flavor) {
		$buffer .= $text;
  } else if (!$text && $flavor) {
  	$buffer .= $flavor;
  }
  
  $buffer .= $textEnd . '
  \end{textblock*}
  \setstretch{1.0}';
  
  if (array_key_exists('power', $cardData) && $cardData['power']) {
    $buffer .=
  '
  % Power & Toughness
  \begin{textblock*}{144bp}(' . $cfg['pt']['x'] . 'bp, ' . $cfg['pt']['y'] . 'bp)
  \begin{center}
  \fontsize{39pt}{39pt}
  \beleren
  \selectfont
  ' . $cardData[$els[$fmt]['power']] . '/' . $cardData[$els[$fmt]['toughness']] . '
  \end{center}
  \end{textblock*}
  ';
  }

  $buffer .=
  '
  % Artist
  \begin{textblock*}{444bp}(' . $cfg['artist']['x'] . 'bp, ' . $cfg['artist']['y'] . 'bp)
  \begin{flushleft}
  \fontsize{21pt}{21pt}
  \mana
  \color{offwhite}
  \selectfont
  a
  \belerensmallcaps
  \fontsize{18.5pt}{21pt}
  \selectfont
  \kern-7bp
  ' . sanitizeText($cardData[$els[$fmt]['artist']]) . '
  \end{flushleft}
  \end{textblock*}

  % Designer credit
  \begin{textblock*}{360bp}(' . ($cfg['designer']['x'] - 392) . 'bp, ' . ($cfg['designer']['y']) . 'bp)
  \begin{flushright}
  \fontsize{17pt}{20pt}
  \belerensmallcaps
  \color{offwhite}
  \selectfont
  ' . $cardData[$els[$fmt]['designer']] . '\strut
  \end{flushright}
  \end{textblock*}
  ';
		
  if (!array_key_exists('c', $opt)) {
		$buffer .= '
  % Copyright
  \begin{textblock*}{360bp}(' . ($cfg['copyright']['x'] - 360) . 'bp, ' . ($cfg['copyright']['y']) . 'bp)
  \begin{flushright}
  \fontsize{17pt}{20pt}
  \plantin
  \color{offwhite}
  \selectfont
  \texttrademark\ \&\ \copyright\ 2022\,Wizards of the Coast
  \end{flushright}
  \end{textblock*}
  ';
	}
	
	$buffer .= '
  % Collector’s Info
  \begin{textblock*}{360bp}(' . $cfg['cn']['x'] . 'bp, ' . $cfg['cn']['y'] . 'bp)
  \begin{flushleft}
  \fontsize{17pt}{20pt}
  \gotham
  \color{offwhite}
  \selectfont
  \addfontfeature{LetterSpace=8.0}
  ' . $cnline . '\strut
  \addfontfeature{LetterSpace=0.0}
  \end{flushleft}
  \end{textblock*}
  
  % Set Code and Language
  \begin{textblock*}{200bp}(' . $cfg['set']['x'] . 'bp, ' . $cfg['set']['y'] . 'bp)
  \begin{flushleft}
  \fontsize{17pt}{20pt}
  \gotham
  \color{offwhite}
  \addfontfeature{LetterSpace=8.0}
  \selectfont
  ' . $set . ' • EN\strut
  \end{flushleft}
  \end{textblock*}
  
  % Rarity
  \begin{textblock*}{20bp}(' . $cfg['rarity']['x'] . 'bp, ' . $cfg['rarity']['y'] . 'bp)
  \begin{flushleft}
  \fontsize{17pt}{20pt}
  \gotham
  \color{offwhite}
  \selectfont
  ' . $rarity . '\strut
  \end{flushleft}
  \end{textblock*}
  
  % Not For Sale
  \begin{textblock*}{200bp}(' . $cfg['notforsale']['x'] . 'bp, ' . $cfg['notforsale']['y'] . 'bp)
  \begin{flushleft}
  \fontsize{17pt}{20pt}
  \gotham
  \color{offwhite}
  \selectfont
  Not For Sale\strut
  \end{flushleft}
  \end{textblock*}
  \end{document}';
  
  return $buffer;
}
?>