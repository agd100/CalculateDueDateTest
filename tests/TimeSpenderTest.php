<?php

require_once ('test_common.php');

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;


require_once ('TimeSpender.php');

class TimeSpenderTest extends DataFilesTestCase
{
	##[DataProvider('dataFileProvider')]
	##[TestDox('$a check data has "issues" field')]
	public function testInit()
	{
		$h = 16;
		$m = 30;

		$t = new TimeSpender ($h, $m);

		$this->assertSame($t->h, $h, $t->m, $m);
	}

	public function testSplitHour()
	{
		$h = 1;
		$m = 30;

		$t = new TimeSpender ($h, $m);

		$t->splitHour ();
		$h--; # is 0 now
		$m += 60;

		$this->assertSame($t->h, $h, $t->m, $m);

		$this->expectException (HourSplitException::class);
		$t->splitHour (); # should throw exception
	}

	public function testSpendHours ()
	{
		$h = 1;
		$m = 30;

		$t = new TimeSpender ($h, $m);

		$this->assertTrue ($t->havehours ());

		$t->spendHour (1);
		$h--; # is 0 now

		$this->assertFalse ($t->havehours ());

		$this->assertTrue ($t->nonzero ()); # t is more than zero (30 minutes still remain)
		$this->assertFalse ($t->zero ()); # t is more than zero (30 minutes still remain)
		$this->assertTrue ($t->haveminutes ()); # (30 minutes still remain)
		
		$this->assertSame($t->h, $h);

		$this->expectException (TimeSpendException::class);
		$t->spendHour (1); # should throw exception
	}

	public function testSpendMinutes ()
	{
		$h = 0;
		$m = 1;

		$t = new TimeSpender ($h, $m);

		$t->spendMinute (1);
		$m--; # is 0 now

		$this->assertFalse ($t->nonzero ()); # t is at zero now
		$this->assertFalse ($t->haveminutes ()); # 0 minutes remain
		
		$this->assertSame ($t->m, $m);

		$this->expectException (TimeSpendException::class);
		$t->spendMinute (1); # should throw exception
	}

}


