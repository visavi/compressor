# Data compression and result output compression

[![Latest Stable Version](https://poser.pugx.org/visavi/compressor/v/stable)](https://packagist.org/packages/visavi/compressor)
[![Total Downloads](https://poser.pugx.org/visavi/compressor/downloads)](https://packagist.org/packages/visavi/compressor)
[![Latest Unstable Version](https://poser.pugx.org/visavi/compressor/v/unstable)](https://packagist.org/packages/visavi/compressor)
[![License](https://poser.pugx.org/visavi/compressor/license)](https://packagist.org/packages/visavi/compressor)

Basic useful feature list:

 * Compress the page on the fly and outputs the result as a percentage of compression
 * There is a check on the installed library gzip or included directive zlib.output_compression
 * Specifies the gzip compression is turned on by default on the server
 * Compression support also checked the visitors browser
 * 3 compression method (gzip, x-gzip, deflate), depending on which method of operating the user's browser

```php
<?php
// Enabling data compression
Visavi\Compressor::start();

// Getting the results of the compression, output compression percentage of the data (not a mandatory method call)
echo Visavi\Compressor::result().'%';
```

### Installing

```
composer require visavi/compressor
```

### License

The class is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
