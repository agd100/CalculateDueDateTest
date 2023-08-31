<?php

require_once ('DateUtil.php');


class NormalizeIssueReportDates 
{
	public function normalize_dates ($srcdata)
	{
		$result = new stdClass ();
		$result->issues = array ();

		$d = DateUtil::_default ();

		foreach ($srcdata->issues as $issue)
		{
			$newissue = clone $issue;
			$newissue->report_date = $d->to_work_hours ($issue->date)->formatted;
			array_push ($result->issues, $newissue);
		}

		return $result;
	}
}

