<?php

require_once ('common.php');

require_once ('TimeSpender.php');


class UnexpectedStateException extends Exception {}


class DateUtil 
{
	public $work_saturdays;
	public $working_hours; # array with [from_h, from_m, to_h, to_m] members in 24 hour format
	public $output_seconds; # whether to output seconds in formatted dates


	public function __construct ($work_saturdays, $working_hours, $output_seconds)
	{
		$this->work_saturdays = $work_saturdays;
		$this->working_hours = $working_hours;
		$this->output_seconds = $output_seconds;
	}

	protected function get_formatted ($dateTime)
	{
		return $dateTime->format ('e Y-m-d H:i' . ($this->output_seconds ? ':s' : ''));
	}

	public function parse ($src)
	{
		$date = new DateTime ($src);

		$result = new stdClass ();
		$result->ts = (int)$date->format ('U');
		$result->formatted = $this->get_formatted ($date);
		$result->dateTime = $date;
		$result->_hour = (int)$date->format ('H');
		$result->_minute = (int)$date->format ('i');
		$result->_second = (int)$date->format ('s');
		$result->_year = (int)$date->format ('Y');
		$result->_month = (int)$date->format ('m');
		$result->_day = (int)$date->format ('d');
		$result->_dayofweek = (int)$date->format ('w'); # Numeric representation of the day of the week, 0 (for Sunday) through 6 (for Saturday)
		$result->_weeknum = (int)$date->format ('W'); # ISO 8601 week number of year, weeks starting on Monday
		$result->_TZ = $date->format ('e');
		
		return $result;
	}

	public function to_work_hours ($date_str_or_p)
	{
		#$p = $this->parse ($src);
		
		if (is_string ($date_str_or_p))
		{
			$p = $this->parse ($date_str_or_p);
		}
		else
		{
			$p = $date_str_or_p;
		}


		if (!$this->is_working_day ($p) || $this->is_after_work ($p))
		{
			return $this->next_working_day_open_time ($p);
		}

		if ($this->is_before_work ($p))
		{
			return $this->to_open_time ($p);
		}

		return $p;
	}


	# returns a TimeSpender object
	public function remaining_work_time ($p)
	{
		$t = new TimeSpender (0, 0);
		if (!$this->is_during_working_hours ($p))
		{
			return $t;
		}

		$end_dt = clone $p->dateTime;
		$ch = $this->get_close_hour ();
		$cm = $this->get_close_minute ();
		$end_dt->setTime ($ch, $cm);
		$end_p = $this->parse ($this->get_formatted ($end_dt));

		$diff = $p->dateTime->diff ($end_p->dateTime);
		$t->h = $diff->h;
		$t->m = $diff->i;

		return $t;
	}


	public function is_during_working_hours ($date_str_or_p)
	{
		if (is_string ($date_str_or_p))
		{
			$p = $this->parse ($date_str_or_p);
		}
		else
		{
			$p = $date_str_or_p;
		}

		return $this->is_working_day ($p) && !$this->is_before_work ($p) && !$this->is_after_work ($p);
	}


	public function fromDateTime ($dt)
	{
		$p = $this->parse ($this->get_formatted ($dt));
		return $p;
	}


	protected function is_after_work ($p)
	{
		$CLOSE_H = $this->get_close_hour ();
		$CLOSE_M = $this->get_close_minute ();
		$after_work = ($p->_hour > $CLOSE_H) || (($p->_hour == $CLOSE_H) && ($p->_minute >= $CLOSE_M));
		return $after_work;
	}
	protected function is_before_work ($p)
	{
		$OPEN_H = $this->get_open_hour ();
		$OPEN_M = $this->get_open_minute ();
		$before_work = ($p->_hour < $OPEN_H) || (($p->_hour == $OPEN_H) && ($p->_minute < $OPEN_M));
		return $before_work;
	}
	protected function is_working_day ($p)
	{
		$SAT_WORK = $this->is_saturday_work ($p);
		$working_day = ($p->_dayofweek > 0) && ($p->_dayofweek <= ($SAT_WORK ? 6 : 5));
		return $working_day;
	}


	protected function get_open_hour ()
	{
		return $this->working_hours[WORK_OPEN_HOUR];
	}
	protected function get_open_minute ()
	{
		return $this->working_hours[WORK_OPEN_MINUTE];
	}
	protected function get_close_hour ()
	{
		return $this->working_hours[WORK_CLOSE_HOUR];
	}
	protected function get_close_minute ()
	{
		return $this->working_hours[WORK_CLOSE_MINUTE];
	}
	protected function is_saturday_work ($parseddate)
	{
		$p = $parseddate;
		$w = $this->work_saturdays;
		$year = $p->_year;
		$have_year = in_array ($year, array_keys ($w));
		if (!$have_year)
		{
			return false;
		}
		return in_array ($p->_weeknum, $w[$year]);
	}

	# creates new parseddate instance
	protected function next_working_day_open_time ($parseddate)
	{
		$p = $this->parse ($parseddate->formatted); # make a copy
		$oneday = DateInterval::createFromDateString('1 day');
		for ($i = 0, $imax = 100; $i < $imax; $i++)
		{
			$p->dateTime->add ($oneday);
			$p = $this->parse ($this->get_formatted ($p->dateTime)); # make a copy

			if ($this->is_working_day ($p))
			{
				$p = $this->to_open_time ($p); # make a copy
				return $p;
			}
		}
		# should never get here
		throw UnexpectedStateException ("iterated $imax days without finding a working day");
	}

	# creates new parsedate instance
	protected function to_open_time ($parseddate)
	{
		$OPEN_H = $this->get_open_hour ();
		$OPEN_M = $this->get_open_minute ();
		$parseddate->dateTime->setTime ($OPEN_H, $OPEN_M);

		$p = $this->parse ($this->get_formatted ($parseddate->dateTime)); # make a copy

		return $p;
	}

	public static function _default ()
	{
		return new DateUtil (WORKING_SATURDAYS, WORKING_HOURS, false);
	}

}

