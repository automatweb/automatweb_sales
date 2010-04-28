<?php

// klass taimerite jaoks

namespace automatweb;

class aw_timer
{
	var $timers; // siin sailitame koiki taimereid
	var $counters;

	function aw_timer($precision = 4)
	{
		// precision - mitu kohta peale koma
		$this->precision = $precision;
		$this->timers = array();
		$this->counters = array();
		$this->start("__global");
		lc_load("definition");
	}

	function count($name)
	{
		if (isset($this->counters[$name]))
		{
			$this->counters[$name]++;
		}
		else
		{
			$this->counters[$name] = 1;
		};
	}

	function start($name)
	{
		// kui sellenimeline taimer on olemas
		if (isset($this->timers[$name]))
		{
			if ($this->timers[$name]["running"])
			{
				// kui taimer juba k2ib, siis me ei tee midagi
				return true;
			}
			else
			{
				if (!is_array($this->timers[$name]))
				{
					$this->timers[$name] = array();
				}
				$this->timers[$name]["started"] = $this->get_time();
				$this->timers[$name]["running"] = 1;
			};
		// sellist taimerit pole olemas
		}
		else
		{
			$this->timers[$name]["running"] = 1;
			$this->timers[$name]["started"] = $this->get_time();
			$this->timers[$name]["elapsed"] = 0;
		};
	}

	function stop($name)
	{
		if (empty($this->timers[$name]))
		{
			return false;
		};

		if ($this->timers[$name]["running"])
		{
			$this->timers[$name]["elapsed"] += ($this->get_time() - $this->timers[$name]["started"]);
			$this->timers[$name]["running"] = 0;
		}
		else
		{
			return false;
		};
	}

	function elapsed($name)
	{
		if (empty($this->timers[$name]))
		{
			return false;
		}

		if ($this->timers[$name]["running"])
		{
			$this->timers[$name]["elapsed"] += ($this->get_time() - $this->timers[$name]["started"]);
			$this->timers[$name]["running"] = 0;
			return ($this->get_time() - $this->timers[$name]["started"]);
		}
		else
		{
			return false;
		};
	}

	function get($name)
	{
		if (empty($this->timers[$name]))
		{
			return false;
		};
		if ($this->timers[$name]["running"])
		{
			return ($this->get_time() - $this->timers[$name]["started"]);
		}
		else
		{
			return false;
		};
	}

	// peatab koik taimerid ja tagastab nad arrays
	//  $arr[taimerinimi] = kulutatud_aeg
	function summaries()
	{
		krsort($this->timers, SORT_NUMERIC);
		while(list($timer,) = each($this->timers))
		{
			$this->stop($timer);
		};
		reset($this->timers);
		$retval = array();
		$fstr = "%0." . $this->precision . "f";

		$total = $this->timers["__global"]["elapsed"];
		while(list($timer,$val) = each($this->timers))
		{
			$retval[$timer] = sprintf($fstr,$val["elapsed"]);
			if (!empty($total))
			{
				$retval[$timer] .= sprintf(" (%0.2f%%)",$val["elapsed"] * 100 / $total);
			};
		};
		// sort timers by time desc
		arsort($retval, SORT_NUMERIC);

		$tmp = array();
		if (is_array($this->counters))
		{
			reset($this->counters);
			while(list($counter,$value) = each($this->counters))
			{
				$xval = "counter_" . $counter;
				$tmp[$xval] = $value;
			};
		};
		arsort($tmp);

		return $retval + $tmp;
	}

	// tagastab aja epohhi algusest sekundites
	function get_time()
	{
		list($micro,$sec) = explode(" ",microtime());
		return ((float)$sec + (float)$micro);
	}

	// kas taimer t88tab?
	function is_running($name)
	{
		return ($this->timers[$name]["running"] == 1);
	}
}
?>
