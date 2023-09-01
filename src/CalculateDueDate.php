<?php

require_once ('common.php');

require_once ('DateUtil.php');

require_once ('TimeSpender.php');


define ('MAX_WORK_DAYS_ON_TASK_ALLOWED', 100);

class MaxWorkdaysExceededException extends Exception {}

class CalculateDueDate
{
	# returns a formatted date string
	public function CalculateDueDate ($start_time_str, $work_hours)
	{
		$d = DateUtil::_default ();

		$t = new TimeSpender ($work_hours, 0);

		$p = $d->parse ($start_time_str);

		for ($i = 0, $imax = MAX_WORK_DAYS_ON_TASK_ALLOWED; $t->nonzero () && ($i < $imax); $i++)
		{
			$today_t = $d->remaining_work_time ($p);

			if ($today_t->zero ())
			{
				# find next work day
				$p = $d->to_work_hours ($p);
				
				$today_t = $d->remaining_work_time ($p);
				assert ($today_t->nonzero ()); # it should never be zero here
			}

			$spent_t = $t->spend_at_most ($today_t);

			#print ("$start_time_str, $work_hours, $i, SPENT_T = $spent_t->h, $spent_t->m, TODAY_T = $today_t->h, $today_t->m, T = $t->h, $t->m\n");

			$spent_di = $spent_t->toDateInterval ();

			$new_date = clone $p->dateTime;
			$new_date->add ($spent_di);
			$p = $d->fromDateTime ($new_date);

			if ($t->zero ())
			{
				# all work time spent
				return $p->formatted;
			}
		}
		# should not get here, but it's possible with very large tasks
		throw new MaxWorkdaysExceededException ("reached the max work days limit at $i");
	}
}

