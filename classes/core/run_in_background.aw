<?php

namespace automatweb;

define("BG_OK", 0);
define("BG_DONE", 1);
define("BG_FORCE_CHECKPOINT", 2);



class run_in_background extends class_base
{
	var $lock_file;
	var $stop_file;

	function init($arr)
	{
		parent::init($arr);

		$this->lock_file = aw_ini_get("server.tmpdir")."/bg_run_".get_class($this).".run";
		$this->stop_file = aw_ini_get("server.tmpdir")."/bg_run_".get_class($this).".stop";

		$this->bg_checkpoint_steps = 100 ;
		$this->bg_log_steps = 10;
	}



	/** Composes the background process controls

		@attrib name=bg_run_get_property_control params=name api=1

		@param prop required type=array
			Array with the property information
		@param obj_inst required type=object
			Objects instance which should be run in background

		@returns
			PROP_OK constant

		@comment
			Should be called in get_property() method for a text type property
			Composes links for controlling the background process

	**/
	function bg_run_get_property_control($arr)
	{

		$prop =& $arr["prop"];

		$fn = $this->lock_file.".".$arr["obj_inst"]->id();
		$fn_s = $this->stop_file.".".$arr["obj_inst"]->id();

		if (file_exists($fn_s))
		{
			$prop["value"] = html::href(array(
				"caption" => t("Reset"),
				"url" => $this->mk_my_orb("bg_control", array(
					"id" => $arr["obj_inst"]->id(),
					"do" => "reset",
					"ru" => get_ru()
				))
			));
		}
		else
		if (file_exists($fn))
		{
			$prop["value"] = html::href(array(
				"caption" => t("Peata"),
				"url" => $this->mk_my_orb("bg_control", array(
					"id" => $arr["obj_inst"]->id(),
					"do" => "stop",
					"ru" => get_ru()
				))
			));
		}
		else
		{
			$prop["value"] = html::href(array(
				"caption" => t("K&auml;ivita"),
				"url" => $this->mk_my_orb("bg_control", array(
					"id" => $arr["obj_inst"]->id(),
					"do" => "start",
					"ru" => get_ru()
				))
			));
		}
		return PROP_OK;
	}


	/** Displays the background process status

		@attrib name=bg_run_get_property_status params=name api=1

		@param prop required type=array
			Array with the property information
		@param obj_inst required type=object
			Objects instance which should be run in background

		@returns
			PROP_OK if 'bg_run_log' is present in meta data, PROP_IGNORE if it is not

		@comment
			Should be called in get_property() method for a text type property
			Displays the background process status

	**/
	function bg_run_get_property_status($arr)
	{

		$prop =& $arr["prop"];

		$fn = $this->lock_file.".".$arr["obj_inst"]->id();

		if (file_exists($fn))
		{
			$prop["value"] = nl2br($this->get_file(array("file" => $fn)));
		}

		else
		{
			$v = $arr["obj_inst"]->meta("bg_run_log");
			if ($v != "")
			{
				$prop["value"] = nl2br($v);
				return PROP_OK;
			}

			return PROP_IGNORE;
		}

	}



	/**

		@attrib name=bg_control

		@param id required type=int acl=view
		@param do required

		@param ru optional

	**/
	function bg_control($arr)
	{

		$o = obj($arr["id"]);
		$fn = $this->lock_file.".".$o->id();
		$s = get_instance("scheduler");

		switch($arr["do"])
		{
			case "start":
				$url = $this->mk_my_orb("bg_run", array("id" => $o->id()));
				$s->add(array(
					"event" => $url,
					"time" => time()-1
				));

				$o->set_meta("bg_run_log",t("Protsess k&auml;ivitub hiljemalt kahe minuti p&auml;rast"));
				$o->save();

				break;

			case "stop":

				touch($this->stop_file.".".$o->id());
				$this->put_file(array(
					"file" => $fn,
					"content" => t("Protsess l&otilde;petab t&ouml;&ouml;d")
				));

				break;

			case "reset":
				unlink($this->stop_file.".".$o->id());
				unlink($this->lock_file.".".$o->id());

				$o->set_meta("bg_run_state", "");

				aw_disable_acl();
				$o->save();
				aw_restore_acl();

				break;
		}

		return $arr["ru"];

	}



	/**

		@attrib name=bg_check_scheduler nologin=1

	**/
	function bg_check_scheduler($arr)
	{

		$s = get_instance("scheduler");

		// make a list of all interested parties
		// check if they are in scheduler

		$ol = new object_list(array(
			"class_id" => $this->clid,
			"site_id" => array(),
			"lang_id" => array()
		));

		foreach($ol->arr() as $o)
		{

			echo "object ".$o->name()." <br>";

			$url = $this->mk_my_orb("bg_run", array("id" => $o->id()));

			$s->remove(array(
				"event" => $url
			));

			// here we have to check if the process is in a stopped state, if it is, restart it as soon as possible
			if ($o->meta("bg_run_state") == "started")
			{

				if (!$this->bg_is_running($o))
				{
					echo "process halted, restart immediately <br>";

					// add run scheduler immediately
					$s->add(array(
						"event" => $url,
						"time" => time()
					));

					$o->set_meta("bg_run_log",$o->meta("bg_run_log").t("<br>Protsess j&auml;tkab hiljemalt kahe minuti p&auml;rast"));

					aw_disable_acl();
					$o->save();
					aw_restore_acl();

					// since scheduler is not running, go for it right away
					$this->bg_run(array("id" => $o->id()));
					continue;
				}

			}

			// get the time it should run a
			if ($o->prop("bg_run_always"))
			{
				$s->add(array(
					"event" => $url,
					"time" => time()
				));

				$o->set_meta("bg_run_log",$o->meta("bg_run_log").t("<br>Protsess k&auml;ivitub hiljemalt kahe minuti p&auml;rast"));

				aw_disable_acl();
				$o->save();
				aw_restore_acl();

				echo "process is done, run always set, restart <br>";
			}
			else
			{

				$recur = $o->get_first_obj_by_reltype("RELTYPE_RECURRENCE");

				if ($recur)
				{
					$s->add(array(
						"event" => $url,
						"rep_id" => $recur->id()
					));

					$o->set_meta("bg_run_log",$o->meta("bg_run_log").t("<br>Protsess k&auml;ivitub j&auml;rgmisel kordusel"));

					aw_disable_acl();
					$o->save();
					aw_restore_acl();

					echo "added with repeater ".$recur->id()." <br>";
				}
			}
		}

		echo "add scheduler check at ".date("d.m.Y H:i:s", time() + 5 * 60)." <br />";

		// add scheduler check every 5 min
		$s->add(array(
			"event" => $this->mk_my_orb("bg_check_scheduler", array()),
			"time" => time()+5*60
		));

		echo "all done <br>";
	}

	/**
		@attrib name=bg_run nologin=1

		@param id required type=int
	**/
	function bg_run($arr)
	{
		echo "enter bg_run $arr[id] <br>\n";
		flush();

		aw_set_exec_time(AW_LONG_PROCESS);

		aw_disable_acl();
			$o = obj($arr["id"]);
			aw_switch_user(array("uid" => $o->createdby()));
		aw_restore_acl();

		if ($this->bg_is_running($o))
		{
			echo "process is already running, will not start another thread!<br>";
			return;
		}

		// run init
		if (method_exists($this, "bg_run_init"))
		{
			echo "call bg_run_init <br>\n";
			flush();
			$this->bg_run_init($o);
		}

		echo "after init, state = ".$o->meta("bg_run_state")." <br>\n";
		flush();

		// figure out if this is start or restart
		if ($o->meta("bg_run_state") == "started")
		{
			// and if it is restart, then run restore step
			if (method_exists($this, "bg_run_continue"))
			{
				echo "calling bg_run_continue <br>\n";
				flush();
				$this->bg_run_continue($o);
			}
		}
		else
		{
			// mark state as started
			$o->set_meta("bg_run_state", "started");
			$o->set_meta("bg_run_start", time());

			aw_disable_acl();
			$o->save();
			aw_restore_acl();
		}

		echo "after continue <br>\n";
		flush();

		// get first log entry
		if (method_exists($this, "bg_run_get_log_entry"))
		{

			echo "call get_log_entry <br>\n";
			flush();

			$this->bg_write_log_entry($this->bg_run_get_log_entry($o), $o);

		}

		// run steps until done
		$iter = 0;

		while(true)
		{
			if (file_exists($this->stop_file.".".$o->id()))
			{
				echo "calling halt for stop flag <br>\n";
				flush();

				$this->bg_do_halt($o);
			}

			echo "running step <br>\n";
			flush();

			$res = $this->bg_run_step($o);
			if ($res == BG_DONE)
			{
				echo "recieved done , breaking <br>\n";
				flush();
				break;
			}



			if (++$iter > $this->bg_checkpoint_steps || $res == BG_FORCE_CHECKPOINT)
			{

				if (method_exists($this, "bg_checkpoint"))
				{

					echo "iter = $iter, calling checkpoint <Br>\n";
					flush();

					$this->bg_checkpoint($o);

					aw_disable_acl();

					$o->save();

					aw_restore_acl();

					echo "checkpoint saved <br>\n";
					flush();

				}

				$iter = 0;

			}

			if (++$log_iter > $this->bg_log_steps)
			{
				if (method_exists($this, "bg_run_get_log_entry"))
				{
					echo "calling write log entry <br>\n";
					flush();

					$this->bg_write_log_entry($this->bg_run_get_log_entry($o), $o);
				}

				$log_iter = 0;

			}

		}

		// call finalizer
		if (method_exists($this, "bg_run_finish"))
		{
			echo "calling finalizer <br>\n";
			flush();

			$this->bg_run_finish($o);
		}



		// mark run as done
		$o->set_meta("bg_run_state", "done");

		aw_disable_acl();
		$o->save();
		aw_restore_acl();

		@unlink($this->lock_file.".".$o->id());

		die(t("all done"));

	}

	function bg_is_running($o)
	{
		$fn = $this->lock_file.".".$o->id();

		if (file_exists($fn))
		{
			if (filemtime($fn) > (time()-4*60))
			{
				return true;
			}
			unlink($fn);
		}

		return false;

	}

	function bg_write_log_entry($entry, $o)
	{
		// write status info to lock file
		$f = fopen($this->lock_file.".".$o->id(), "w");

		if ($f)
		{
			fwrite($f, $entry);
			fclose($f);
		}
	}

	function bg_do_halt($o)
	{

		echo "found stop flag, stopping scheduler <br>";
		unlink($this->stop_file.".".$o->id());
		unlink($this->lock_file.".".$o->id());

		$o->set_meta("bg_run_state", "done");

		if (method_exists($this, "bg_halt"))
		{
			$this->bg_halt($o);
		}

		aw_disable_acl();
		$o->save();
		aw_restore_acl();

		die(t("Halt"));
	}
}

?>
