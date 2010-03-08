<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/calendar_vacancy.aw,v 1.10 2008/11/06 18:51:54 markop Exp $
// calendar_vacancy.aw - Vakants 
/*

@classinfo syslog_type=ST_CALENDAR_VACANCY relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@property start type=datetime_select field=start table=planner
@caption Algab

@property end type=datetime_select table=planner
@caption L&otilde;peb

@property info_on_object type=text store=no
@caption Osalejad

@property morph_to type=text store=no
@caption Muuda

@tableinfo planner index=id master_table=objects master_index=brother_of

@property task_toolbar type=toolbar no_caption=1 store=no group=participants
@caption "Toolbar"

@property participant type=participant_selector store=no group=participants no_caption=1
@caption Osalejad

@property search_contact_company type=textbox store=no group=participants
@caption Organisatsioon

@property search_contact_firstname type=textbox store=no group=participants
@caption Eesnimi

@property search_contact_lastname type=textbox store=no group=participants
@caption Perenimi

@property search_contact_code type=textbox store=no group=participants
@caption Isikukood

@property search_contact_button type=submit store=no group=participants action=search_contacts
@caption Otsi

@property search_contact_results type=table store=no group=participants no_caption=1
@caption Tulemuste tabel

@groupinfo participants caption=Osalejad submit=no

*/

class calendar_vacancy extends class_base
{
	function calendar_vacancy()
	{
		$this->init(array(
			"clid" => CL_CALENDAR_VACANCY
		));
	}

	/**
		@attrib name=reserve_slot all_args="1"

	**/
	function reserve_slot($arr)
	{
		// okey, I have the free time object id
		$vac_obj = new object($arr["id"]);
		$parent = $vac_obj->parent();
		$start = $vac_obj->prop("start");
		$end = $vac_obj->prop("end");


		$new_obj = new object();
		$new_obj->set_parent($parent);
		$new_obj->set_class_id($arr["clid"]);
		$new_obj->set_status(STAT_ACTIVE);
		$new_obj->set_prop("start1",$start);
		$new_obj->set_prop("end",$end);
		$new_obj->set_flag(OBJ_WAS_VACANCY, true);

		$new_obj->save();
		$vac_obj->delete();

		if ($arr["ret_id"])
		{
			return $new_obj->id();
		}


		$pl = get_instance(CL_PLANNER);
		$user_calendar = $arr["cal_id"];

		// 1. get parent
		// 2. get times
		// 3. create a new clid object out of that with same parent and same times
		// 4. display the form to the user
		// 5. BUT. I need to display that in user calendar .. well, actually, I just need
		// 	to create an empty object and then redirect to the calendar of that active user
		//print "creating a new slot, eh<bR>";
		//arr($arr);
		//print "all done<br>";

		$ret_url = $pl->get_event_edit_link(array(
			"cal_id" => $user_calendar,
			"event_id" => $new_obj->id(),
		));

		return $ret_url;

		/*
		$ret_url = $this->mk_my_orb("change",array(
			"id" => $user_calendar,
			"group" => "add_event",
			"event_id" => $event_id,
		),CL_PLANNER);
		*/
		//http://duke.dev.struktuur.ee/automatweb/orb.aw?class=planner&action=change&id=137870&group=add_event&event_id=138595


	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "morph_to":
				$vac = $arr["obj_inst"];
				if (!$vac)
				{
					return PROP_IGNORE;
				}
				$cal_id = 
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("reserve_slot",array(
						"id" => $vac->id(),
						"clid" => CL_CRM_MEETING,
						"cal_id" => $arr["request"]["id"],
					),CL_CALENDAR_VACANCY),
					"caption" => t("Uus kohtumine"),
				))." | ".html::href(array(
					"url" => $this->mk_my_orb("reserve_slot",array(
						"id" => $vac->id(),
						"clid" => CL_CRM_CALL,
						"cal_id" => $arr["request"]["id"],
					),CL_CALENDAR_VACANCY),
					"caption" => t("Uus k&otilde;ne"),
				))." | ".html::href(array(
					"url" => $this->mk_my_orb("reserve_slot",array(
						"id" => $vac->id(),
						"clid" => CL_TASK,
						"cal_id" => $arr["request"]["id"],
					),CL_CALENDAR_VACANCY),
					"caption" => t("Uus toimetus"),
				));
				break;

			case 'info_on_object':
				if(is_object($arr['obj_inst']) && is_oid($arr['obj_inst']->id()))
				{
					$conns = $arr['obj_inst']->connections_to(array(
						'type' => 8,//CRM_PERSON.RELTYPE_PERSON_MEETING==8
					));

					foreach($conns as $conn)
					{
						$obj = $conn->from();
						//isik
						$prop['value'].= html::href(array(
							'url' => html::get_change_url($obj->id()),
							'caption' => $obj->name(),
						));
						//isiku default firma
						if($company = $obj->company())
						{
							$prop['value'] .= " ".html::href(array(
								'url' => html::get_change_url($company->id()),
								'caption' => $company->name(),
							));
						}
						//isiku ametinimetused...
						$conns2 = $obj->connections_from(array(
							'type' => 'RELTYPE_RANK',
						));
						$professions = '';
						foreach($conns2 as $conn2)
						{
							$professions.=', '.$conn2->prop('to.name');
						}
						if(strlen($professions))
						{
							$prop['value'].=$professions;
						}
						//isiku telefonid
						$conns2 = $obj->connections_from(array(
							'type' => 'RELTYPE_PHONE'
						));
						$phones = '';
						foreach($conns2 as $conn2)
						{
							$phones.=', '.$conn2->prop('to.name');
						}
						if(strlen($phones))
						{
							$prop['value'].=$phones;
						}
						//isiku emailid
						$conns2 = $obj->connections_from(array(
							'type' => 'RELTYPE_EMAIL',
						));
						$emails = '';
						foreach($conns2 as $conn2)
						{
							$to_obj = $conn2->to();
							$emails.=', '.$to_obj->prop('mail');
						}
						if(strlen($emails))
						{
							$prop['value'].=$emails;
						}						
						$prop['value'].='<br>';
					}
				}
         break;


			case 'task_toolbar' :
				$tb = &$prop['toolbar'];
				$tb->add_button(array(
					'name' => 'del',
					'img' => 'delete.gif',
					'tooltip' => t('Kustuta valitud'),
					'action' => 'submit_delete_participants_from_calendar',
				));

				$tb->add_separator();

				$tb->add_button(array(
					'name' => 'Search',
					'img' => 'search.gif',
					'tooltip' => t('Otsi'),
					'url' => aw_url_change_var(array(
						'show_search' => 1,
					)),
				));

				$tb->add_button(array(
					'name' => 'save',
					'img' => 'save.gif',
					'tooltip' => t('Salvesta'),
					"action" => "save_participant_search_results"
				));
				$this->return_url=aw_global_get('REQUEST_URI');
				break;
		};
		return $retval;
	}

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
}
?>
