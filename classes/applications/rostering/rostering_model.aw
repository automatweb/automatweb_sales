<?php
/*
@classinfo maintainer=voldemar
*/
class rostering_model extends core
{
	function rostering_model()
	{
		classload("core/date/date_calc");
		$this->init();
	}

	/** returns array of work cycle objects for the given person
		@attrib api=1 params=pos
		@param scenario required type=object
			The scenario for which to return the cycles

		@param person required type=object
			The person to get the cycles for

		@returns
			Array of work cycle objects for the person in the scenario
	**/
	function get_work_cycles_for_person($scenario, $person)
	{
		// read person's co, section, profession
		// check from top to bottom for that scenario, where cycles are set
	}

	/** returns array of settings for the given person
		@attrib api=1 params=pos
		@param scenario required type=object
			The scenario for which to return the cycles

		@param person required type=object
			The person to get the cycles for

		@returns
			Array of settings for the person in the given scenario. Array contains:
				work_hrs_per_week - max number of work hours per week to plan
				no_plan_night - do not plan this person for night-shifts
				max_overtime - maximum number of overtime hours per month
				free_days_after_night_shift - number of free days to plan after the person has worked at night
	**/
	function get_settings_for_person($scenario, $person)
	{
		// read person's co, section, profession
		// check from top to bottom for that scenario, where cycles are set
	}

	/** returns array of work items data for the given person
		@attrib api=1 params=pos

		@param person required type=object 
			The person to return the work times for

		@param from required type=int
			Timestamp for start of period to return work hours for

		@param to required type=int
			Timestam pfor end pf period to return work hrs for

		@returns
			Array of work items for the given period.
			Each work item is an array containing:
				start - timestamp for start if work
				end - timestamp for end of work
				skill - the skill required for the job
				workplace - where the work will take place
				shift - the shift that the work is part of
				cycle - the work cycle that the work is part of for the person

	**/
	function get_schedule_for_person($person, $from, $to)
	{
		// return 5 random work times in the period

		$skill_list = new object_list(array(
			"class_id" => CL_PERSON_SKILL,
			"lang_id" => array(),
			"site_id" => array()
		));
		$skill_ids = $skill_list->ids();

		$workplace_list = new object_list(array(
			"class_id" => CL_ROSTERING_WORKPLACE,
			"lang_id" => array(),
			"site_id" => array()
		));
		$workplace_ids = $workplace_list->ids();

		$shift_list = new object_list(array(
			"class_id" => CL_ROSTERING_SHIFT,
			"lang_id" => array(),
			"site_id" => array()
		));
		$shift_ids = $shift_list->ids();

		$cycle_list = new object_list(array(
			"class_id" => CL_PERSON_WORK_CYCLE,
			"lang_id" => array(i),
			"site_id" => array()
		));
		$cycle_ids = $cycle_list->ids();

		static $times_taken;
		if (!is_array($times_taken))
		{
			$times_taken = array();
		}

		$rv = array();
		for($i = 0; $i < 3; $i++)
		{
			$cnt = 0;
			do {
				$start = rand($from, $to);
				$end = $start + rand(1,8) * 3600;
				$overlap = false;
				foreach(safe_array($times_taken[$person->id()]) as $tse)
				{
					$tmp = timespans_overlap($start, $end, $tse["start"], $tse["end"]);
					$overlap |= $tmp;
				}
			} while($overlap && ++$cnt < 10);
			$times_taken[$person->id()][] = array("start" => $start, "end" => $end);

			$rv[] = array(
				"start" => $start,
				"end" => $end,
				"skill" => $skill_ids[array_rand($skill_ids)],
				"workplace" => $workplace_ids[array_rand($workplace_ids)],
				"shift" => $shift_ids[array_rand($shift_ids)],
				"cycle" => $cycle_ids[array_rand($cycle_ids)],
			);
		}

		usort($rv, create_function('$a,$b', 'return $a["start"] - $b["start"];'));
		return $rv;
	}
}
?>
