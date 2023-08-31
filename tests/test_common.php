<?php

require_once ('../tools/phpunit.phar');

define ('SRCDIR', '../src');



use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;


# thanks!! https://stackoverflow.com/a/44442822
function obj_has_key ($obj, $key)
{
	return array_key_exists ($key, get_object_vars ($obj));
}
function obj_key_exists ($obj, $key) { return obj_has_key ($obj, $key); }


abstract class DataFilesTestCase extends TestCase
{
	public static function dataFileProvider ()
	{
		return [
			'dataset 1' => [SRCDIR . '/SAMPLE_DATA.json']
		];
	}	

}

