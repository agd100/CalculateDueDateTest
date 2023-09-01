<?php


class HourSplitException extends Exception {}
class TimeSpendException extends Exception {}

class TimeSpender
{
	public $h, $m;

	public function __construct ($h, $m)
	{
		$this->h = $h;
		$this->m = $m;
	}

	public function splitHour ()
	{
		if ($this->h < 1)
		{
			throw new HourSplitException ();
		}
		$this->h--;
		$this->m += 60;
	}

	public function spendHour ($n)
	{
		if ($this->h < $n)
		{
			throw new TimeSpendException ();
		}
		$this->h -= $n;
	}

	public function spendMinute ($n)
	{
		if ($this->m < $n)
		{
			throw new TimeSpendException ();
		}
		$this->m -= $n;
	}

	public function zero ()
	{
		return !$this->nonzero ();
	}

	public function nonzero ()
	{
		return ($this->h > 0) || ($this->m > 0);
	}

	public function haveminutes ()
	{
		return ($this->m > 0);
	}

	public function havehours ()
	{
		return ($this->h > 0);
	}

	public function spend_at_most ($t)
	{
		# convert hours to minutes if needed
		if (($t->m > $this->m) && ($this->h > 0))
		{
			$this->splitHour ();
		}

		# yeah needs a refactoring
		$s_h = min ($this->h, $t->h);
		$s_m = $t->h > $this->h ?
			$this->m # spend all available minutes
			:
			min ($this->m, $t->m) # spend no more than specified minutes by $t->m
		;

		$this->spendHour ($s_h);
		$this->spendMinute ($s_m);

		$spent_t = new TimeSpender ($s_h, $s_m);
		return $spent_t;
	}

	public function toDateInterval ()
	{
		return DateInterval::createFromDateString ("$this->h hours + $this->m minutes");
	}
}

