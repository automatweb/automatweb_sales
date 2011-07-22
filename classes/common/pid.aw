<?php

/*
Class for working with personal identification numbers (PID-s). Loading and common usage:

	$pid = new pid_et("45503034582");
	$gender = $pid->gender();

*/

class pid
{
	const GENDER_FEMALE = 2;
	const GENDER_MALE = 3;

	protected $pid; // actual pid string
	protected $data = array(); // pid type/country specific data
	protected $is_valid; // boolean when checked/validated, null by default

	function __construct ($pid = null, $errors = false)
	{
		if (isset($pid))
		{
			$this->set($pid, $errors);
		}
	}

	/**
		@attrib name=get api=1 params=pos
		@returns string PID data. NULL if not defined.
	**/
	function get ()
	{
		return $this->pid;
	}

	/**
		@attrib name=set api=1 params=pos
		@param pid required type=string
		@param errors optional type=bool default=false
		@errors
			throws awex_pid_invalid subclasses when $errors parameter is TRUE
	**/
	function set ($pid, $errors = false)
	{
		$this->pid = (string) $pid;
		$this->parse($errors);
	}

	/**
		@attrib name=is_valid api=1 params=pos
		@returns TRUE/FALSE whether loaded PID is valid.
	**/
	function is_valid ()
	{
		return $this->is_valid;
	}

	function __toString()
	{
		return $this->get();
	}

	protected function parse($errors)
	{
	}
}

class pid_et extends pid
{
	/*
		@attrib name=gender api=1 params=pos
		@param male_value optional type=bool default=false
		@param female_value optional type=bool default=false
		@returns pid::GENDER_FEMALE for female pid::GENDER_MALE for male by default. If $female_value and/or $male_value parameters specified, returns these values correspondingly.
		@errors
			throws awex_pid_na if no gender info retrieved
	*/
	function gender($male_value = self::GENDER_MALE, $female_value = self::GENDER_FEMALE)
	{
		if (!isset($this->data["gender"]))
		{
			throw new awex_pid_na("Gender not retireved");
		}
		$gender = $this->data["gender"] === self::GENDER_MALE ? $male_value : $female_value;
		return $gender;
	}

	/**
		@attrib name=birth_date api=1 params=pos
		@param format optional type=string default=UNIXTIMESTAMP
		@returns birth date in requested format.
		@errors
			throws awex_pid_na if no birth date info retrieved
	**/
	function birth_date($format = "UNIXTIMESTAMP")
	{
		if (!isset($this->data["birth_date"]))
		{
			throw new awex_pid_na("Birth date not retireved");
		}
		return $this->data["birth_date"];
	}

	// parses pid according to Estonian personal identification number standard EVS 1990:585.
	protected function parse($errors)
	{
		$this->is_valid = true;
		$this->data = array();
		$pid = $this->pid;

		if (strlen ($pid) != 11)
		{
			$this->is_valid = false;

			if ($errors)
			{
				throw new awex_pid_length();
			}
		}

		$quotient = 10;
		$step = 0;
		$check = FALSE;

		while (10 == $quotient and $step < 3 and !$check)
		{
			$order = 0;
			$multiplier = 1 + $step;
			$sum = NULL;

			while ($order < 10)
			{
				$sum += (int) $pid{$order} * $multiplier;
				$order++;
				$multiplier++;

				if (10 == $multiplier)
				{
					$multiplier = 1;
				}
			}

			$step += 2;
			$quotient = $sum%11;

			if ($quotient == (int) $pid{10})
			{
				$check = TRUE;
			}
		}

		if (!$check)
		{
			$this->is_valid = false;

			if ($errors)
			{
				throw new awex_pid_checksum();
			}
		}

		$pid_1 = (int) substr ($pid, 0, 1);
		$pid_day = (int) substr ($pid, 5, 2);
		$pid_month = (int) substr ($pid, 3, 2);
		$pid_year = (int) substr ($pid, 1, 2);

		switch ($pid_1)
		{
			case 1: // 1800–1899 male;
				$pid_year += 1800;
				$this->data["gender"] = self::GENDER_MALE;
				break;

			case 2: // 1800–1899  female;
				$pid_year += 1800;
				$this->data["gender"] = self::GENDER_FEMALE;
				break;

			case 3: // 1900–1999  male;
				$pid_year += 1900;
				$this->data["gender"] = self::GENDER_MALE;
				break;

			case 4: // 1900–1999  female;
				$pid_year += 1900;
				$this->data["gender"] = self::GENDER_FEMALE;
				break;

			case 5: // 2000–2099  male;
				$pid_year += 2000;
				$this->data["gender"] = self::GENDER_MALE;
				break;

			case 6: // 2000–2099  female;
				$pid_year += 2000;
				$this->data["gender"] = self::GENDER_FEMALE;
				break;
		}

		if (checkdate ($pid_month, $pid_day, $pid_year))
		{
			$this->data["birth_date"] = mktime (0, 0, 0, $pid_month, $pid_day, $pid_year);
		}
		else
		{
			$this->is_valid = false;

			if ($errors)
			{
				throw new awex_pid_birthdate();
			}
		}
	}
}

/** Generic pid exception **/
class awex_pid extends aw_exception {}

/** Indicates that requested data element is not defined or retrieved **/
class awex_pid_na extends awex_pid {}

/** Invalid pid **/
class awex_pid_invalid extends awex_pid {}

/** Invalid pid length **/
class awex_pid_length extends awex_pid_invalid
{
	protected $message = "Invalid length";
}

/** Invalid birthdate in pid **/
class awex_pid_birthdate extends awex_pid_invalid
{
	protected $message = "Invalid birthdate info";
}

/** Invalid pid checksum **/
class awex_pid_checksum extends awex_pid_invalid
{
	protected $message = "Checksum incorrect";
}
