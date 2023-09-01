<?php

require_once ('InputParser.php');
require_once ('NormalizeIssueReportDates.php');
require_once ('DateUtil.php');
require_once ('CalculateDueDate.php');


function get_issues_with_report_dates_normalized ($datafile)
{
	$p = new InputParser ();
	$data = $p->parseInputFile ($datafile);
	
	$n = new NormalizeIssueReportDates ();
	$data = $n->normalize_dates ($data);
	
	return $data;
}


$data = get_issues_with_report_dates_normalized ('SAMPLE_DATA.json');


$c = new CalculateDueDate ();

$n = 0;
foreach ($data->issues as $issue)
{
	$n++;

	$start_time_str = $issue->report_date;
	$work_time_hours = (int)$issue->solvetime;

	$end_time_str = $c->CalculateDueDate ($start_time_str, $work_time_hours);

	print ("Issue $n: $issue->title\n");
	print ("$issue->author_name <$issue->author_email>\n");
	print ("$issue->report_date [Work Time: $issue->solvetime hours]\n");
	print ("Due Date: $end_time_str\n\n");
}


