# Template resources for custom cards
This repository contains the building blocks for the template resources in use for the next version of MTG.Design, an online website for creating custom *Magic: The Gathering* cards.

Whenever possible, everything is designed to be vectorized to ensure the highest quality, with the exception of most background textures. When adding content, priority should always be given to SVG images.

Resources are split into individual components, the recipes how how to build the cards, and the PHP and LaTeX rendering files. Components include items such as accents, backgrounds, boxes, borders, and shadows. Recipes indicate which components to use, what effects to apply, and where to place them on a card. Rendering files composite the graphics with LaTeX text.

Currently, the components and recipes are included.

Some rendering support has been enabled, but it is unfinished.

## Requirements
* PHP 8.1 or newer
* XeLaTeX to render cards
* Inkscape in order to save from SVG to other formats
* ImageMagick to save from PDF to PNG

## How to use
`build.php` builds the `.ini` recipe files into SVG or PNG images. (To export to PNG, `inkscape` is recommended for proper SVG export. (Not all graphics applications fully implement the SVG 1.1 specification.)

`render.php` renders the text using XeLaTeX. You must install it on your system if you want to render text on cards. 300 and 800 dpi outputs in PNG will be available, as well as an SVG file (currently does not include text).

```
usage: render.php <format> [<options>] <path>

Formats:
  -l	LackeyBot JSON format
  -m	MTGJSON JSON format (Not finished)
  -s	Scryfall JSON format (Not finished)

Options:
  -c	Remove copyright line
  -d	Add text for designer field to each card
  -h	Displays this usage description
  -k	Keep intermediate and temporary render files
  -n	Start rendering at nth card
  -p	Output as print ready (With bleed area)
  -v	Output as SVG instead of PDF (No flavor bar or graphics)
```

### Formats
Only specify one format when using the renderer.

Only the LackeyBot JSON format has been tested. (A converter exists to convert from MSE2 to LackeyBot JSON.) Other formats will be included in future updates.

### Options
`-c`	will render the card without any copyright line.  
`-d`	can be used to add designer credit to a card. By default, this appears in the bottom right corner, to the left of the copyright logo.  
`-h`	shows the usage description. (A full help page will be added in the future.)  
`-k`	keeps all intermediate render files. This includes the `.tex`, `.aux`, and `.log` LaTeX files, `.pdf` and `.png` files used for text and card images.  
`-n`	renders the card number, or range of cards (use a hyphen). 
`-p`	outputs with a larger bleed area around the border. This is helpful for people who are printing cards for personal use.  
`-v`	outputs as SVG. Currently, this doesn not include the flavor bar, and potentially other graphics.  

### Rendering

XeLaTeX is required to run the renderer. You can download it as part of TeXLive.

The following LaTeX packages are also required:

```
  textpos
  calc
  xcolor
  fontspec
  lmodern
  hyphenat
  enumitem
  graphicx
  setspace
```

`/frames` includes the `.ini` recipes for how to draw the card, and where.
`/typesetting` includes the text `.ini` recipes for how to set the type, and where to place it.

You can add new types of cards by creating a new recipe file. (Guidance and help on how to write recipe files will be added in the future.)

`/art` is where the renderer will look for art. JPEG and PNG formats are supported. The art’s file name should be the same as the card’s name.

`/symbol` is where set symbols are stored. PNGs are required. The file name should contain the set code in uppercase, followed by an underscore, and the card’s rarity letter abbreviation, such as `SET_C`, `SET_U`, `SET_R` and `SET_M`.

### Fonts

`/fonts` is where fonts are stored. They are not distributed as part of this project. Should you have the appropriate font files, or care to substitute with other files, put them here.

These are the file names that the renderer will look for:

```
Beleren2016-Bold.ttf
BelerenSC.ttf
Gotham-Light.otf
Gotham-Medium.otf
MTG2016.ttf
PlantinMTPro.otf
PlantinMTPro-Bold.otf
PlantinMTPro-BoldItalic.otf
PlantinMTPro-Italic.otf
```

`MTG2016.ttf` is a font file that has been heavily customized and it not distributable. (Future updates will try to find a fallback method.)

## Contribute
MTG.Design is being actively worked on, with frames being uploaded only when they are considered ready. If you have components or recipes to add, please file a pull request and they will be reviewed.

You can donate to MTG.Design using [Patreon](https://www.patreon.com/mtgdotdesign).

## About
MTG.Design was created using the MIT license by [ancestral](https://github.com/ancestral) of Spellshapers, LLC.

Images in `/background`, `/title`, `/typeline` and `/stamp` may contain trademarks and be copyright of Wizards of the Coast, LLC, a subsidiary of Hasbro, Inc.

MTG.Design is not affiliated with, endorsed, sponsored, or specifically approved by Wizards of the Coast LLC.
