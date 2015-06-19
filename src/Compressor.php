<?php
/**
 * Class Compression pages
 * Compress the page on the fly and outputs the result as a percentage of compression
 * There is a check on the installed library gzip or included directive zlib.output_compression
 * Compression support also checked the visitors browser
 *
 * @license Code and contributions have MIT License
 * @link    http://visavi.net
 * @author  Alexander Grigorev <visavi.net@mail.ru>
 * @version 1.0
 */

namespace Visavi;

class Compressor {

	/**
	 * Compression level (0-9)
	 * @var integer
	 */
	public static $level = 5;

	/**
	 * Setting headers and compression on the fly
	 */
	public static function start()
	{
		if (extension_loaded('zlib') &&
			ini_get('zlib.output_compression') != 'On' &&
			ini_get('output_handler') != 'ob_gzhandler' &&
			ini_get('output_handler') != 'zlib.output_compression'
		) {
			$check_compress = self::check_compress();

			if ($check_compress == 'gzip') {
				header("Content-Encoding: gzip");
				ob_start(['self', 'compress_output_gzip']);
			}
			elseif ($check_compress == 'deflate') {
				header("Content-Encoding: deflate");
				ob_start(['self', 'compress_output_deflate']);
			}
		}
	}

	/**
	 * Output of compression
	 * @return float result of the compression percentage
	 */
	public static function result()
	{
		$check_compress = self::check_compress();

		if ($check_compress) {

			$contents = ob_get_contents();
			$gzip_file = strlen($contents);

			if ($check_compress == 'gzip') {
				$gzip_file_out = strlen(self::compress_output_gzip($contents));
			}
			elseif ($check_compress == 'deflate') {
				$gzip_file_out = strlen(self::compress_output_deflate($contents));
			}

			return $gzip_file > $gzip_file_out ? round(100 - 100 / ($gzip_file / $gzip_file_out), 1) : 0;
		}
	}

	/**
	 * Check if the browser supports compression
	 * @return boolean compression is supported
	 */
	protected static function check_compress()
	{
		// Reading the headlines
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			$gzencode = $_SERVER['HTTP_ACCEPT_ENCODING'];
		}
		elseif (isset($_SERVER['HTTP_TE'])) {
			$gzencode = $_SERVER['HTTP_TE'];
		}
		else {
			$gzencode = false;
		}

		// Search support compression titles
		if (strpos($gzencode, 'gzip') !== false) {
			$support = 'gzip';
		}
		elseif (strpos($gzencode, 'deflate') !== false) {
			$support = 'deflate';
		}
		else {
			$support = false;
		}

		return $support;
	}

	/**
	 * Compression gzencode
	 * @param  string $output Data compression.
	 * @return mixed          The compressed string or false if an error occurs
	 */
	protected static function compress_output_gzip($output)
	{
		return gzencode($output, self::$level);
	}

	/**
	 * Compression gzdeflate
	 * @param  [type] $output [description]
	 * @return mixed          The compressed string or false if an error occurs
	 */
	protected static function compress_output_deflate($output)
	{
		return gzdeflate($output, self::$level);
	}
}
