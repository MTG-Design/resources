# Template resources for custom cards
This repository contains the building blocks for the template resources in use for the next version of MTG.Design, an online website for creating custom *Magic: The Gathering cards.*

Whenever possible, everything is designed to be vectorized to ensure the highest quality, with the exception of most background textures. When adding content, priority should always be given to SVG images.

Resources are split into individual components, the recipes how how to build the cards, and the PHP and LaTeX rendering files. Components include items such as accents, backgrounds, boxes, borders, and shadows. Recipes indicate which components to use, what effects to apply, and where to place them on a card. Rendering files composite the graphics with LaTeX text.

Currently, the components and recipes are included, with the rendering instructions to be included at a later date.

## How to use
`build.php` builds the `.ini` recipe files into SVG or PNG images. (To export directly to PNG, `svgexport` is required.)

You can add new types of card by creating a new recipe file. (Guidance and help on how to write recipe files will be added in the future.)

## Contribute
MTG.Design is being actively worked on, with frames being uploaded only when they are considered ready. If you have components or recipes to add, please file a pull request and they will be reviewed.

You can donate to MTG.Design using [Patreon](https://www.patreon.com/mtgdotdesign).

## About
MTG.Design was created using the MIT license by [ancestral](https://github.com/ancestral) of Spellshapers, LLC.

MTG.Design is not affiliated with, endorsed, sponsored, or specifically approved by Wizards of the Coast LLC.
