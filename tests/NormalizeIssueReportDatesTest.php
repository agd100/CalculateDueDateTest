<?php

require_once ('test_common.php');

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use PHPUnit\Framework\TestCase;


require_once ('InputParser.php');
require_once ('NormalizeIssueReportDates.php');
require_once ('DateUtil.php');

class NormalizeIssueReportDatesTest extends DataFilesTestCase
{
	#[DataProvider('dataFileProvider')]
	#[TestDox('$a check normalized data has "issues" field')]
	public function testHasIssuesField($datafile)
	{
		$p = new InputParser ();
		$data = $p->parseInputFile ($datafile);

		$n = new NormalizeIssueReportDates ();
		$data = $n->normalize_dates ($data);

		$this->assertTrue (obj_has_key ($data, 'issues'));	
	}
	#[DataProvider('dataFileProvider')]
	#[TestDox('$a check normalized data has same number of items as the input data')]
	public function testSameNumberofItems($datafile)
	{
		$p = new InputParser ();
		$data = $p->parseInputFile ($datafile);

		$n = new NormalizeIssueReportDates ();
		$n_data = $n->normalize_dates ($data);

		$this->assertSame (count ($data->issues), count ($data->issues));	
	}
	#[DataProvider('normalizedDatesProvider')]
	#[TestDox('$a check normalized data report dates within bounds')]
	public function testReportDatesWithinBounds($issue)
	{
		$dates = DateUtil::_default ();

		$this->assertTrue (obj_has_key ($issue, 'report_date'));	
		$report_date = $issue->report_date;
		$report_time_ok = $dates->is_during_working_hours ($report_date);
		$this->assertTrue ($report_time_ok);
	}


	public static function normalizedDatesProvider ()
	{
		$alldatasets = self::dataFileProvider (); 
		$filenames = array_values ($alldatasets);
		$firstdataset = array_shift ($filenames);
		$datafile = $firstdataset[0];

		$p = new InputParser ();
		$data = $p->parseInputFile ($datafile);

		$n = new NormalizeIssueReportDates ();
		$data = $n->normalize_dates ($data);

		$dates_ds = array ();
		$i = -1;
		foreach ($data->issues as $issue)
		{
			$i++;
			$key = 'data with normalized dates from ' . $datafile . ", #$i";
			$dates_ds[$key] = array ($issue);
		}

		return $dates_ds;
	}


}


