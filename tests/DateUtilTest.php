<?php

require_once ('test_common.php');

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;


require_once ('DateUtil.php');



class DateUtilTestProtectedTester extends DateUtil
{
	public function _is_after_work ($p)
	{
		return $this->is_after_work ($p);
	}
	public function _is_before_work ($p)
	{
		return $this->is_before_work ($p);
	}
	public function _is_working_day ($p)
	{
		return $this->is_working_day ($p);
	}


	public function _get_open_hour ()
	{
		return $this->get_open_hour ();
	}
	public function _get_open_minute ()
	{
		return $this->get_open_minute ();
	}
	public function _get_close_hour ()
	{
		return $this->get_close_hour ();
	}
	public function _get_close_minute ()
	{
		return $this->get_close_minute ();
	}
	public function _is_saturday_work ($parseddate)
	{
		return $this->is_saturday_work ($parseddate);
	}

	# creates new parseddate instance
	public function _next_working_day_open_time ($parseddate)
	{
		return $this->next_working_day_open_time ($parseddate);
	}

	# creates new parsedate instance
	public function _to_open_time ($parseddate)
	{
		return $this->to_open_time ($parseddate);
	}


}


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


	#[DataProvider('workSatTestProvider')]
	#[TestDox('check date parsing $datestr')]
	public function testProtected ($datestr, $expected)
	{
		$workhours = [9, 0, 17, 0];
		$workingsaturdays = [2023 => [34, 35, 36]];
		$outputseconds = false;
		#$d = DateUtil::_default ();
		#$d = new DateUtil ($workingsaturdays, $workhours, $outputseconds);
		$d = new DateUtilTestProtectedTester ($workingsaturdays, $workhours, $outputseconds);

		$p = $d->parse ($datestr);
		
		$IS_WORKTIME = 0;
		$NEXT_WORKTIME = 1;
		$WEEKNUM = 2;
		$IS_WORKDAY = 3;
		$IS_BEFOREWORK = 4;
		$IS_AFTERWORK = 5;
		$IS_SATURDAYWORK = 6;

		$this->assertSame($p->_weeknum, $expected[$WEEKNUM]);

		#return $this->is_working_day ($p) && !$this->is_before_work ($p) && !$this->is_after_work ($p);
		$is_wd = $d->_is_working_day ($p);
		$is_bw = $d->_is_before_work ($p);
		$is_aw = $d->_is_after_work ($p);

		$is_sw = $d->_is_saturday_work ($p);

		$this->assertSame($is_wd?1:0, $expected[$IS_WORKDAY]);
		$this->assertSame($is_bw?1:0, $expected[$IS_BEFOREWORK]);
		$this->assertSame($is_aw?1:0, $expected[$IS_AFTERWORK]);
		$this->assertSame($is_sw?1:0, $expected[$IS_SATURDAYWORK]);

		$this->assertSame($workhours[0], $d->_get_open_hour());
		$this->assertSame($workhours[1], $d->_get_open_minute());
		$this->assertSame($workhours[2], $d->_get_close_hour());
		$this->assertSame($workhours[3], $d->_get_close_minute());

		if ($expected[$IS_WORKTIME])
		{
			$this->assertTrue($d->is_during_working_hours ($p->formatted));
		}
		else # not worktime
		{
			$this->assertFalse($d->is_during_working_hours ($p->formatted));
		}
	}


	#[DataProvider('workSatTestProvider')]
	#[TestDox('check date parsing $datestr')]
	public function testToWorkHours($datestr, $expected)
	{
		$workhours = [9, 0, 17, 0];
		$workingsaturdays = [2023 => [34, 35, 36]];
		$outputseconds = false;
		#$d = DateUtil::_default ();
		$d = new DateUtil ($workingsaturdays, $workhours, $outputseconds);

		$p = $d->parse ($datestr);

		$wh = $d->to_work_hours ($p->formatted);

		$IS_WORKTIME = 0;
		$NEXT_WORKTIME = 1;
		if ($expected[$IS_WORKTIME])
		{
			$this->assertSame($p->formatted, $wh->formatted);
			$this->assertSame($p->ts, $wh->ts);
		}
		else # not worktime
		{
			$this->assertSame($wh->formatted, $expected[$NEXT_WORKTIME]);
		}
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

	public static function workSatTestProvider ()
	{
		return [
			'dataset 1' => ['CEST 2023-09-01 16:27', [1, '', 35, 1, 0, 0, 1]],
			'dataset 2' => ['CEST 2023-09-01 17:27', [0, 'CEST 2023-09-02 09:00', 35, 1, 0, 1, 1]],
			'dataset 3' => ['CEST 2023-09-02 16:59', [1, '', 35, 1, 0, 0, 1]],
			'dataset 4' => ['CEST 2023-09-02 17:00', [0, 'CEST 2023-09-04 09:00', 35, 1, 0, 1, 1]],
			'dataset 5' => ['CEST 2023-09-03 14:00', [0, 'CEST 2023-09-04 09:00', 35, 0, 0, 0, 1]],
			'dataset 6' => ['CEST 2023-09-15 16:45', [1, '', 37, 1, 0, 0, 0]],
			'dataset 7' => ['CEST 2023-09-15 17:00', [0, 'CEST 2023-09-18 09:00', 37, 1, 0, 1, 0]],
			'dataset 8' => ['CEST 2023-09-18 08:12', [0, 'CEST 2023-09-18 09:00', 38, 1, 1, 0, 0]],
		];
	}
}


