<?php

require_once ('test_common.php');

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;

require_once ('NormalizeIssueReportDatesTest.php');

require_once ('DateUtil.php');
require_once ('CalculateDueDate.php');

class CalculateDueDateTest extends DataFilesTestCase
{
	#[DataProvider('workTimeDatesProvider')]
	#[TestDox('$a check calculated due date is as expected')]
	#public function testCalculateDueDate($issue)
	public function testCalculateDueDate($start_time_str, $work_time_hours, $expected_end_time_str)
	{
		$c = new CalculateDueDate ();

		#$d = DateUtil::_default ();
		#$start_time_str = $issue->report_date;
		#$work_time_hours = (int)$issue->solvetime;
		#print ("START_TIME: $start_time_str, WORK_TIME_HOURS: $work_time_hours\n");

		$end_time_str = $c->CalculateDueDate ($start_time_str, $work_time_hours);

		$this->assertSame ($end_time_str, $expected_end_time_str);
	}


	public static function workTimeDatesProvider ()
	{
		return [
			'dataset 1' => ['CEST 2023-08-29 14:27', 16, 'CEST 2023-08-31 14:27'],
			'dataset 2' => ['CEST 2023-08-29 16:54', 34, 'CEST 2023-09-04 10:54'],
			'dataset 3' => ['CEST 2023-08-30 09:00', 12, 'CEST 2023-08-31 13:00'],
			'dataset 4' => ['CEST 2023-08-30 09:00', 6,  'CEST 2023-08-30 15:00'],
			'dataset 5' => ['CEST 2023-08-30 09:00', 6,  'CEST 2023-08-30 15:00'],
			'dataset 6' => ['CEST 2023-08-30 10:11', 16, 'CEST 2023-09-01 10:11'],
			'dataset 7' => ['CEST 2023-08-30 13:51', 8,  'CEST 2023-08-31 13:51'],
		];
	}
}


