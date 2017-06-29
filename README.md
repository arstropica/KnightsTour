# KnightsTour
Simple brute-force approach to the knight's tour problem

## Installation

- Clone into web root parent directory.
- Point web document root to `./public` folder.
- Run `composer dump-autoload` in project root.
- In `./dev`, run `npm install`, then `bower install && grunt` to finish loading dependencies and build assets.


## To Run

#### From Console

x: starting x coordinate. Defaults to `a` or `1`

y: starting y coordinate. Defaults to `1`

size: board size (squared). Defaults to `8`

`php -f index.php x y size`

#### From Browser

- map `./public` folder to web root.
- Go to root url in your browser.
- Submit coordinates and size values, or leave blank for defaults.