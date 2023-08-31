<?php

require_once ('test_common.php');

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;


require_once ('CalculateDueDate.php');

class CalculateDueDateTest extends DataFilesTestCase
{
	##[DataProvider('normalizedDatesProvider')]
	##[TestDox('$a check normalized data report dates within bounds')]
	public function testReportDatesWithinBounds()
	{
		$c = new CalculateDueDate ();

		$c->CalculateDueDate ($start_time, $work_time_hours);
	}
}


