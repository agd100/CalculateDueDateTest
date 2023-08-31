<?php

require_once ('test_common.php');

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;


require_once ('InputParser.php');

class InputParserTest extends DataFilesTestCase
{
	#[DataProvider('dataFileProvider')]
	#[TestDox('$a check data has "issues" field')]
	public function testHasIssuesField($datafile)
	{
		$p = new InputParser ();
		$data = $p->parseInputFile ($datafile);
		$has = obj_key_exists ($data, 'issues');

		$this->assertTrue($has);
	}
	#[DataProvider('dataFileProvider')]
	#[TestDox('$a check number of items is correct')]
	public function testNumberofItems($datafile)
	{
		$p = new InputParser ();
		$data = $p->parseInputFile ($datafile);
		$issues = $data->issues;
		$n = count ($issues);

		$data_test = json_decode (file_get_contents ($datafile));
		$n_test = count ($data_test->issues);

		$this->assertSame($n_test, $n);
	}

}


