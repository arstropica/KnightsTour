# KnightsTour
Simple approach to solving the knight's tour problem based on Warnsdorff’s algorithm for minimum accessibility.

## Installation

- Clone into web root parent directory.
- Point web document root to `./public` folder.
- Run `composer dump-autoload` in project root.
- In `./dev`, run `npm install`, then `bower install && grunt` to finish loading dependencies and build assets.


## To Run

#### From Console

x: starting x coordinate (i.e. `a` or `1`). Defaults to random. 

y: starting y coordinate (i.e. `1`). Defaults to random. 

size: board size (squared). Defaults to `8`

output: output results to console. Defaults to `1`

`php -f index.php x y size output`

#### From Browser

- map `./public` folder to web root.
- Go to root url in your browser.
- Submit coordinates and size values, or leave blank for defaults.


## Benchmark

![Benchmark Results](https://raw.githubusercontent.com/arstropica/KnightsTour/master/public/img/benchmark.jpg)

## To Do

- Implement a prediction method for identifying closed tours