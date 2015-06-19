<?php
/**
 * Класс компрессии страниц
 * Сжимает страницы на лету и выводит результат сжатия в процентах
 * Имеется проверка на установленную библиотеку gzip или включенную директиву zlib.output_compression
 * Также проверяется поддержка сжатия браузером у посетителей
 *
 * @license Code and contributions have MIT License
 * @link    http://visavi.net
 * @author  Alexander Grigorev <visavi.net@mail.ru>
 * @version 1.0
 */

namespace Visavi;

class Compressor {

	/**
	 * Уровень сжатия (0-9)
	 * @var integer
	 */
	public $level;

	/**
	 * Установка заголовков и сжатие на лету
	 * @return string сжатые данные
	 */
	public static function start($level = 5)
	{
		if (extension_loaded('zlib') &&
			ini_get('zlib.output_compression') != 'On' &&
			ini_get('output_handler') != 'ob_gzhandler' &&
			ini_get('output_handler') != 'zlib.output_compression'
		) {
			$check_compress = self::check_compress();
			if ($check_compress == 'gzip')
			{
				header("Content-Encoding: gzip");
				ob_start(["Compressor", "compress_output_gzip"]);
			}
			elseif ($check_compress == 'deflate')
			{
				header("Content-Encoding: deflate");
				ob_start(["Compressor", "compress_output_deflate"]);
			}
		}
	}

	/* Вывод результатов сжатия */
	/**
	 * Вывод результатов сжатия
	 * @return float результат сжатия в процентах
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

			return $compression = round(100 - (100 / ($gzip_file / $gzip_file_out)), 1);
		}
	}

	/**
	 * Проверка поддерживает ли браузер сжатие
	 * @return boolean поддерживается ли сжатие
	 */
	protected static function check_compress()
	{
		// Чтение заголовков
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			$gzencode = $_SERVER['HTTP_ACCEPT_ENCODING'];
		}
		elseif (isset($_SERVER['HTTP_TE'])) {
			$gzencode = $_SERVER['HTTP_TE'];
		}
		else {
			$gzencode = false;
		}

		// Поиск поддержки сжатия в заголовках
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

	/* Сжатие gzencode */
	public static function compress_output_gzip($output)
	{
		return gzencode($output, 5);
	}

	/* Сжатие gzdeflate */
	public static function compress_output_deflate($output)
	{
		return gzdeflate($output, 5);
	}
}
