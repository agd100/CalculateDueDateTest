<?php

require_once ('test_common.php');

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;


require_once ('DateUtil.php');

class DateUtilTest extends TestCase
{
	#[DataProvider('datesProvider')]
	#[TestDox('check date parsing $datestr')]
	public function testDateParsing($datestr, $expected_ts)
	{
		$d = DateUtil::_default ();

		$p = $d->parse ($datestr);

		$this->assertSame($p->ts, $expected_ts);
		$this->assertSame($p->formatted, $datestr);
	}


	public static function datesProvider ()
	{
		return [
			'dataset 1' => ['CEST 2023-08-29 14:27', 1693312020],
			'dataset 2' => ['CEST 2023-08-29 16:54', 1693320840],
			'dataset 3' => ['CEST 2023-08-29 17:54', 1693324440],
			'dataset 4' => ['CEST 2023-08-29 19:57', 1693331820],
			'dataset 5' => ['CEST 2023-08-30 07:57', 1693375020],
			'dataset 6' => ['CEST 2023-08-30 10:11', 1693383060],
			'dataset 7' => ['CEST 2023-08-30 13:51', 1693396260],
		];
	}

}

