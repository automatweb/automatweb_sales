<?php

class customer_import_obj extends _int_object
{
	const CLID = 1541;

////////////////////////////////////////////////// process management support

	function getpidinfo($pid, $ps_opt="aux")
	{
		$ps=shell_exec("ps ".$ps_opt."p ".$pid);
		$ps=explode("\n", $ps);

		if(count($ps) < 2)
		{
			return false;
		}

		foreach($ps as $key=>$val)
		{
			$ps[$key]=explode(" ", ereg_replace(" +", " ", trim($ps[$key])));
		}

		foreach($ps[0] as $key=>$val)
		{
			$pidinfo[$val] = $ps[1][$key];
			unset($ps[1][$key]);
		}

		if(is_array($ps[1]))
		{
			$pidinfo[$val].=" ".implode(" ", $ps[1]);
		}

		if ($pidinfo["PID"] == null)
		{
			return false;
		}
		return $pidinfo;
	}

	private function do_process_switch()
	{
		// save state
		$this->_save_state();

		// begin new process and tell it to restore state
		$this->_spawn_resume_process();

		// redirect status scoreboard to new process
			// actually this should happen automatically?

		// kill this process
		die();
	}

	private function _save_state()
	{

	}

	private function _resume_state()
	{

	}

	private function _spawn_resume_process()
	{
		$i = get_instance("customer_import");
		$url = $i->mk_my_orb("run_backgrounded", array("act" => "resume_customer_import", "id" => $this->id()), "customer_import");
		$url = str_replace("/automatweb", "", $url);
		$h = new http;
		$h->get($url);
	}

	public function resume_from_process_switch()
	{
		sleep(2);
		// get old state
		$old_state = $this->_get_saved_state();
		$this->cover_customer_import_state($old_state);
	}

	function _get_saved_state()
	{
		// read from file
		list($tm, $pid, $status) = $this->full_import_status("customer");
		return $status;
	}

	static private function _status_fn($type, $wh_id = "")
	{
		return aw_ini_get("server.tmpdir")."/aw_cust_imp_".aw_ini_get("site_id")."_".$type."_".$wh_id;
	}

	function import_is_running($type, $wh_id = null)
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			list($start_time, $pid, $state) = explode("\n", file_get_contents($tf));
			if (($pd = $this->getpidinfo($pid)) === false)
			{
				$this->write_import_end_log_entry($type, t("Staatuse kontrollis avastati protsessi kadumine"), false, $wh_id);
				unlink($tf);
				return false;
			}
			return $pid;
		}
		return false;
	}

	function get_import_log($type, $wh_id = "")
	{
		return $this->meta("import_log_".$type."_".$wh_id);
	}

	function import_status($type, $wh_id = "")
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			list($pid, $state, $count) = explode("\n", file_get_contents($tf));
			return $state;
		}
		return "Viga";
	}

	function full_import_status($type, $wh_id = "")
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			return explode("\n", file_get_contents($tf), 7);
		}
		return "Viga";
	}

	function import_count($type, $wh_id = "")
	{
		$tf = self::_status_fn($type, $wh_id);
		if (file_exists($tf))
		{
			list($pid, $state, $count) = explode("\n", file_get_contents($tf));
			return $count;
		}
		return "Viga";
	}

	function _int_stop($type, $wh_id = "")
	{
		$this->_update_status($type, warehouse_import_if::STATE_FINISHING, $wh_id);

		$sf = self::_status_fn($type, $wh_id);
		unlink($sf);
		if (file_exists($sf."_stop_flag"))
		{
			unlink($sf."_stop_flag");
		}
	}

	function _end_import_from_flag($type, $wh_id = "")
	{
		$this->write_import_end_log_entry($type, t("Kasutaja n&otilde;udis protsessi peatamist manuaalselt"), false, $wh_id);
		$this->_int_stop($type);
	}

	function _end_import($type, $wh_id = "")
	{
		$this->write_import_end_log_entry($type, t("L&otilde;ppes edukalt"), true, $wh_id);
		$this->_int_stop($type, $wh_id);
	}

	function reset_import($type, $wh_id = "")
	{
		$this->write_import_end_log_entry($type, t("Kasutaja resettis protsessi manuaalselt"), false, $wh_id);
		$sf = self::_status_fn($type, $wh_id);
		if (file_exists($sf))
		{
			unlink($sf);
		}
		if (file_exists($sf."_stop_flag"))
		{
			unlink($sf."_stop_flag");
		}
	}

	function stop_import($type, $wh_id = "")
	{
		if ($this->import_is_running($type, $wh_id))
		{
			$tf = self::_status_fn($type, $wh_id)."_stop_flag";
			touch($tf);
		}
	}

	function need_to_stop_now($type, $wh_id = "")
	{
		if ($this->import_is_running($type, $wh_id))
		{
			$tf = self::_status_fn($type, $wh_id)."_stop_flag";
			if (file_exists($tf))
			{
				//unlink($tf);
				return true;
			}
		}
		return false;
	}

	function _start_import($type, $wh_id = "")
	{
		$this->_update_status($type, warehouse_import_if::STATE_PREPARING, $wh_id);
	}

	function _update_status($type, $status, $wh_id = null, $count = null, $total = null, $info = null)
	{
		$tf = self::_status_fn($type, $wh_id);
		if (!file_exists($tf))
		{
			$start_time = time();
		}
		else
		{
			list($start_time, $t1, $t2, $t3, $t4, $t5, $t6) = explode("\n", file_get_contents($tf), 7);
			if ($count === null)
			{
				$count = $t4;
			}
			if ($total === null)
			{
				$total = $t5;
			}
			if ($info === null)
			{
				$info = $t6;
			}
		}
		$f = fopen($tf, "w");
		fwrite($f, $start_time."\n".getmypid()."\n".$status."\n".$wh_id."\n".$count."\n".$total."\n".$info);
		fclose($f);
	}


	function write_import_end_log_entry($type, $reason, $success = true, $wh_id = null)
	{
		// need to reload meta from database
		parent::__construct($GLOBALS["object_loader"]->ds->get_objdata($this->id()));

		$typedata = $this->meta("import_log_".$type."_".$wh_id);
		if (!is_array($typedata))
		{
			$typedata = array();
		}
		if (count($typedata) > 9)
		{
			// cut off from the end
			array_pop($typedata);
		}

		$s = $this->full_import_status($type, $wh_id);

		array_unshift($typedata, array(
			"finish_tm" => time(),
			"full_status" => $s,
			"reason" => $reason,
			"success" => $success
		));
		$this->set_meta("import_log_".$type."_".$wh_id, $typedata);
		$this->save();
	}

	function start_customer_import()
	{
		get_instance("users")->login(array("uid" => "kristo", "password" => "jobu13"));
		aw_set_exec_time(AW_LONG_PROCESS);
		while (ob_get_level()) { ob_end_clean(); }
		$this->_start_import("customer");

		$this->cover_customer_import_state(customer_import_datasource::STATE_STARTING);
		// finish
		$this->_end_import("customer");


		$i = get_instance($this->prop("data_source"));

//		$this->_categories($i);
	//	$this->_customers($i);

		$this->_persons($i);
die("catz done");
//		$this->_users($i);

	}

	private 		$lut = array(
			customer_import_datasource::STATE_STARTING => "_start",
			customer_import_datasource::STATE_PROCESS_CATEGORY_XML => "_categories",
			customer_import_datasource::STATE_PROCESS_CUSTOMER_XML => "_customers",
			customer_import_datasource::STATE_PROCESS_PERSON_XML => "_persons",
			customer_import_datasource::STATE_PROCESS_USER_XML => "_users",
			customer_import_datasource::STATE_FINISHING => "_finish"
		);


	private function _state_mapper($state)
	{
		return $this->lut[$state];
	}

	function _get_next_state($old_state)
	{
		foreach($this->lut as $state => $d)
		{
			if ($state > $old_state)
			{
				return $state;
			}
		}
		return null;
	}

	function cover_customer_import_state($old_state)
	{
echo "enter cover state $old_state <br>";
		// get processor function for next state
		$next = $this->_get_next_state($old_state);
echo "next = $next <br>";
		if ($next === null)
		{
			return;
		}
		$func = $this->_state_mapper($next);
echo "func = $func <br>";
		// run it
		$i = get_instance($this->prop("data_source"));
		$this->$func($i);
		// switch process
echo "switching process <br>";
		$this->do_process_switch();
	}


	private function _finish($i)
	{
		$this->_end_import("customer");
		die("all done");
	}

	private function _users($i)
	{
		// status fetch xml
		$this->_update_status("customer", customer_import_datasource::STATE_FETCH_USER_XML);
		$xml = $i->get_user_list_xml();

//		$sx = new SimpleXMLElement($xml);
//		$total = count($sx->user);

		$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_USER_XML, null, 0, $total);
return;
		// process
		$this->_do_customer_import_process_users($sx);
	}

	private function _do_customer_import_process_users($sx)
	{
		$cur_list = $this->_list_current_users();
		$total = count($sx->user);
		$counter = 0;
		foreach($sx->user as $cat)
		{
			$ext_id = (string)$cat->extern_id;
			if (isset($cur_list[$ext_id]))
			{
				// update existing
				$this->_update_existing_user($cat, $cur_list[$ext_id]);
				unset($cur_list[$ext_id]);
			}
			else
			{
				// add new
				$this->_add_new_user($cat);
			}

			if ((++$counter % 10) == 1)
			{
				$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_USER_XML, null, $counter, $total);
				if ($this->need_to_stop_now("customer"))
				{
					$this->_end_import_from_flag("customer");
					die("stopped for flag");
				}
			}
		}

		foreach($cur_list as $ext_id => $cat)
		{
			$this->_delete_unused_user($cat);
		}
	}

	private function _list_current_users()
	{
		$ol = new object_list(array(
			"class_id" => CL_USER,
			"lang_id" => array(),
			"site_id" => array()
		));
		$d = array();
		foreach($ol->arr() as $o)
		{
			$d[$o->uid] = $o;
		}
		return $d;
	}

	private function _update_existing_user($external, $aw)
	{
		// check if different
		$mod = false;
		foreach($external as $key => $value)
		{
			if ($aw->$key != $value)
			{
				$mod = true;
				$aw->set_prop($key, $value);
			}
		}

		if ($mod)
		{
			$aw->save();
		}
	}

	private function _add_new_user($external)
	{
		$u = get_instance("core/users/user");
		$aw = $u->add_user(array(
			"uid" => (string)$external->uid,
			"email" => (string)$external->email,
			"password" => (string)$external->password,
			"real_name" => (string)$external->real_name,
			"person" => $this->_resolve_user_person($external)
		));
		$this->_update_existing_user($external, $aw);
	}

	private function _resolve_user_person($external)
	{
		$ext_id = (string)$external->person_external_id;
		if (trim($ext_id) != "")
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"external_id" => $ext_id
			));
			if ($ol->count())
			{
				return $ol->begin()->id();
			}
		}
		return null;
	}

	private function _delete_unused_user($aw)
	{
		// TODO: implement
	}

	private function _persons($i)
	{
		// status fetch xml
		$this->_update_status("customer", customer_import_datasource::STATE_FETCH_PERSON_XML);
		$xml = $i->get_person_list_xml();

		$sx = new SimpleXMLElement($xml);
		$total = count($sx->person);

		$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_PERSON_XML, null, 0, $total);

		// process
		$this->_do_customer_import_process_persons($sx);
	}

	private function _do_customer_import_process_persons($sx)
	{
		$cur_list = $this->_list_current_persons();
		$total = count($sx->person);

		$existing_customers = $this->_list_current_customers();

		$counter = 0;
		foreach($sx->person as $cat)
		{
			$ext_id = (string)$cat->external_id;
			if (isset($cur_list[$ext_id]))
			{
				// update existing
echo "upd existing ".((string)$cat->name)." <br>";
				$this->_update_existing_person($cat, $cur_list[$ext_id], false, $existing_customers);
				unset($cur_list[$ext_id]);
			}
			else
			{
				// add new
echo "add new ".((string)$cat->name)." <br>";
				$this->_add_new_person($cat, $existing_customers);
			}

			if ((++$counter % 10) == 1)
			{
				$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_PERSON_XML, null, $counter, $total);
				if ($this->need_to_stop_now("customer"))
				{
					$this->_end_import_from_flag("customer");
					die("stopped for flag");
				}
			}
//die("uandun");
		}

		foreach($cur_list as $ext_id => $cat)
		{
			$this->_delete_unused_person($cat);
		}
	}

	private function _list_current_persons()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			"site_id" => array()
		));
		$d = array();
		foreach($ol->arr() as $o)
		{
//echo dbg::dump($o->external_id);
			if ($o->external_id != "")
			{
				$d[$o->external_id] = $o;
			}
		}
//echo (dbg::dump($d));
		return $d;
	}

	private function _update_existing_person($external, $aw, $mod = false, $existing_customers)
	{
		// check if different
		foreach($external as $key => $value)
		{
			$value = (string)$value;
//echo "key = $key , val = $value <br>";
			if ($aw->is_property($key) && $aw->$key != $value)
			{
				$mod = true;
				$aw->set_prop($key, html_entity_decode($value, ENT_COMPAT, aw_global_get("charset")));
			}
		}

		if ($mod)
		{
			$aw->save();
		}

		// connect to company
		if (isset($existing_customers[(string)$external->company_external_id]))
		{
			$co = $existing_customers[(string)$external->company_external_id];
			$co->add_employees(array("id" => $aw->id()));
echo "added to co ".dbg::dump($co->name)." <br>";
		}
echo "updated ".html::obj_change_url($aw)." <br>";
	}

	private function _add_new_person($external, $exc)
	{
		$aw = obj();
		$aw->set_class_id(CL_CRM_PERSON);
		$aw->set_parent($this->id());
		$aw->save();
		$this->_update_existing_person($external, $aw, true, $exc);
	}

	private function _delete_unused_person($aw)
	{
		// TODO: implement
echo "delete ".$aw->name()." <br>";
		$aw->delete();
	}


	private function _customers($i)
	{

		// status fetch xml
		$this->_update_status("customer", customer_import_datasource::STATE_FETCH_CUSTOMER_XML);
		$xml = $i->get_customer_list_xml();

		//die("<pre>".htmlentities($xml));
		$sx = new SimpleXMLElement($xml);
		$total = count($sx->customer);

		$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_CUSTOMER_XML, null, 0, $total);

		// process
		$this->_do_customer_import_process_customers($sx);
	}

	private function _do_customer_import_process_customers($sx)
	{
		$cur_list = $this->_list_current_customers();
		$total = count($sx->customer);
		$counter = 0;

		$existing_cats = $this->_list_current_categories();
//echo dbg::dump($cur_list);
//die(dbg::dump($cur_list));
//die(dbg::dump($sx));
		foreach($sx->customer as $cat)
		{
			$ext_id = (string)$cat->extern_id;
//echo "extid = ".dbg::dump($cat)." <br>";
			if (isset($cur_list[$ext_id]))
			{
				// update existing
echo "upd existing ".dbg::dump($cat->name)." <br>";
				$this->_update_existing_customer($cat, $cur_list[$ext_id], false, $existing_cats);
				unset($cur_list[$ext_id]);
			}
			else
			{
				// add new
echo "add new ".dbg::dump($cat->name)." <br>";
				$this->_add_new_customer($cat, $existing_cats);
			}

			if ((++$counter % 10) == 1)
			{
				$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_CUSTOMER_XML, null, $counter, $total);
				if ($this->need_to_stop_now("customer"))
				{
					$this->_end_import_from_flag("customer");
					die("stopped for flag");
				}
			}
		}

		foreach($cur_list as $ext_id => $cat)
		{
echo "delete unused ".dbg::dump($cat)." <br>";
			$this->_delete_unused_customer($cat);
		}
	}

	private function _list_current_customers()
	{
		$co = obj($this->prop("company"));
		$d = array();
		foreach($co->get_customers_by_customer_data_objs()->arr() as $cust_co)
		{
			$d[$cust_co->extern_id] = $cust_co;
		}
		return $d;
	}

	private function _update_existing_customer($external, $aw, $mod = false, $existing_cats)
	{
		// check if different
		foreach($external as $key => $value)
		{
			$value = (string)$value;
			if ($aw->is_property($key) && $aw->$key != $value)
			{
				$mod = true;
				$aw->set_prop($key, html_entity_decode($value, ENT_COMPAT, aw_global_get("charset")));
			}
		}

		if ($mod)
		{
			$aw->save();
		}

		// categories
		foreach($external->categories->category as $cat)
		{
//			echo "category = ".(dbg::dump());
			// get cat by ext id
			$cato = $existing_cats[(string)$cat];
//echo "cat id = ".((string)$cat)." o = ".dbg::dump($cato)." <br>";
			if ($cato)
			{
				$cato->connect(array(
					"to" => $aw->id(),
					"type" => "RELTYPE_CUSTOMER"
				));
			}
		}
echo "updated ".html::obj_change_url($aw)." <br>";
	}

	private function _add_new_customer($external, $existing_cats)
	{
		$co = obj($this->prop("company"));
		//$aw = obj($co->add_customer((string)$external->name));
		$aw = obj();
		$aw->set_class_id(CL_CRM_COMPANY);
		$aw->set_parent($co->id());
		$this->_update_existing_customer($external, $aw, true, $existing_cats);
		$aw->find_customer_relation($co, true);
	}

	private function _delete_unused_customer($aw)
	{
		// TODO: implement
	}

	private function _categories($i)
	{
		// status fetch xml
		$this->_update_status("customer", customer_import_datasource::STATE_FETCH_CATEGORY_XML);
		$xml = $i->get_category_list_xml();

		$sx = new SimpleXMLElement($xml);
		$total = count($sx->category);

		$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_CATEGORY_XML, null, 0, $total);

		// process
		$this->_do_customer_import_process_categories($sx);
	}

	private function _do_customer_import_process_categories($sx)
	{
//automatweb::$instance->mode(automatweb::MODE_REASONABLE);
		$cur_list = $this->_list_current_categories();
echo "git existing as ".dbg::dump($cur_list)." <br>";
		$total = count($sx->category);
		$counter = 0;

		foreach($sx->category as $cat)
		{
			$ext_id = (string)$cat->extern_id;
			if (isset($cur_list[$ext_id]))
			{
				// update existing
echo "update existing ".dbg::dump($cat)." <br>";
				$this->_update_existing_cat($cat, $cur_list[$ext_id]);
				unset($cur_list[$ext_id]);
			}
			else
			{
				// add new
echo "add new ".dbg::dump($cat)." <br>";
				$this->_add_new_cat($cat);
			}

			if ((++$counter % 10) == 1)
			{
				$this->_update_status("customer", customer_import_datasource::STATE_PROCESS_CATEGORY_XML, null, $counter, $total);
				if ($this->need_to_stop_now("customer"))
				{
					$this->_end_import_from_flag("customer");
					die("stopped for flag");
				}
			}
		}

		foreach($cur_list as $ext_id => $cat)
		{
echo "delete ".dbg::dump($cat)." <br>";
			$this->_delete_unused_cat($cat);
		}
	}

	private function _list_current_categories()
	{
		$co = obj($this->prop("company"));
		$existing = array();
		foreach($co->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$t = $c->to();
			$existing[$t->prop("extern_id")] = $t;
		}
		return $existing;
	}

	private function _update_existing_cat($external, $aw, $mod = false)
	{
		// check if different
		foreach($external as $key => $value)
		{
			$value = (string)$value;
			if ($aw->$key != $value)
			{
				$mod = true;
				$aw->set_prop($key, $value);
			}
		}

		if ($mod)
		{
			$aw->save();
		}
	}

	private function _add_new_cat($external)
	{
		$aw = obj();
		$aw->set_class_id(CL_CRM_CATEGORY);
		$aw->set_parent($this->prop("company"));
		$this->_update_existing_cat($external, $aw, true);

		obj($this->prop("company"))->connect(array(
			"to" => $aw->id(),
			"type" => "RELTYPE_CATEGORY"
		));
	}

	private function _delete_unused_cat($aw)
	{
		// TODO: implement
	}
}

?>
