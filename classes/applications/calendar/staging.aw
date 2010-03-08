<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/staging.aw,v 1.25 2009/01/20 15:25:16 instrumental Exp $
// staging.aw - Lavastus
/*

@classinfo syslog_type=ST_STAGING relationmgr=yes maintainer=dragut

@default table=objects
@default group=general

@property start1 type=datetime_select field=start table=planner
@caption Algab

@property end type=datetime_select field=end table=planner
@caption L&otilde;ppeb

@property img type=releditor reltype=RELTYPE_PICTURE use_form=emb rel_id=first
@caption Pilt

@property utextarea1 type=textarea cols=90 rows=10 trans=1 table=planner
@caption Kirjeldus

// seems i don't need those rules here anymore
default field=meta
default method=serialize


@property utextbox1 type=textbox table=planner
@caption Userdefined textbox 1

@property utextbox2 type=textbox table=planner
@caption Userdefined textbox 2

@property utextbox3 type=textbox table=planner
@caption Userdefined textbox 3

@property utextbox4 type=textbox table=planner
@caption Userdefined textbox 4

@property utextbox5 type=textbox table=planner
@caption Userdefined textbox 5

@property utextbox6 type=textbox table=planner
@caption Userdefined textbox 6

@property utextbox7 type=textbox table=planner
@caption Userdefined textbox 7

@property utextbox8 type=textbox table=planner
@caption Userdefined textbox 8

// saving the place and price property values to planner table right now
// to utextbox9 and utextbox10 so i can save those utextbox properties above
// to utextbox1-utextbox8 fields in planner table
// actually i think there should be separate table for stagings
// possible ToDo while creating the WhereToGo module

@layout place_box type=hbox
@caption Toimumispaik

	@property place type=select parent=place_box field=utextbox9 table=planner
	@caption Vali toimumispaik

	@property new_place type=textbox parent=place_box store=no
	@caption Uus toimumispaik

@property price type=textbox field=utextbox10 table=planner
@caption Hind

@property aliasmanager type=aliasmgr
@caption Seostehaldur

@property project_selector type=project_selector store=no group=projects all_projects=1
@caption Projektid

@property trans type=translator store=no group=trans props=name,utextarea1,comment
@caption Tlkimine

@property times type=callback store=no callback=callback_gen_times group=times
@caption Ajad

@tableinfo planner index=id master_table=objects master_index=brother_of

@reltype PICTURE value=1 clid=CL_IMAGE
@caption Pilt

// copies are individual objects, they have no relation to the original
// after they are created - besides that connection - which - is used
// to overwrite the copied events
@reltype COPY value=2 clid=CL_STAGING
@caption Koopia

@reltype DOCUMENT value=3 clid=CL_DOCUMENT
@caption Dokument

@groupinfo projects caption="Projektid"
@groupinfo trans caption="T&otilde;lkimine"
@groupinfo times caption="Ajad"

@classinfo trans=1

*/

class staging extends class_base
{
	function staging()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/staging",
			"clid" => CL_STAGING
		));
	}


	function callback_gen_times($arr)
	{
		$rv = array();
		$prefix = $arr["prop"]["name"];
		// ja see asi siin peab arvestama tehtud seoseid
		$o = $arr["obj_inst"];
		$o = $o->get_original();

		// check if this is a copy and then show connections from the original object
		$copy_conns = $o->connections_to(array(
			"type" => 2 // "RELTYPE_COPY",
		));

		if (sizeof($copy_conns) > 0)
		{
			$first = reset($copy_conns);
			$o = $first->from();
		};

		$start1 = $o->prop("start1");
		$conns = $o->connections_from(array(
			"type" => 2, //"RELTYPE_COPY",
		));

		/*
		$rv["active_" . $prefix] = array(
			"type" => "datetime_select",
			"name" => $prefix . "[active]",
			"value" => $o->prop("start1"),
			"caption" => t("Aktiivne objekt"),
		);
		*/
		$rv["orig_id_" . $prefix] = array(
			"type" => "text",
			"name" => $prefix . "[orig_id]",
			"value" => html::href(array(
				"url" => $this->mk_my_orb("change",array("id" => $o->id())),
				"caption" => $o->id(),
			)),
			"caption" => t("Originaalobjekti ID"),
		);

		$rv["active_" . $prefix] = array(
			"type" => "text",
			"name" => $prefix . "[active]",
			"value" => date("d.m.Y H:i",$o->prop("start1")),
			"caption" => t("Originaalobjekt"),
		);

//		$empty_slots = 75;
		foreach($conns as $conn)
		{
			$to = $conn->to();
			$id = $to->id();
			$caption = t("Koopia");
			if ($id == $arr["obj_inst"]->id())
			{
				$caption .= t(" (Aktiivne)");
			};

			$rv["existing_" . $prefix . $id] = array(
				"type" => "datetime_select",
				"name" => $prefix . "[existing][" . $id . "]",
				"caption" => $caption,
				"group" => $arr["prop"]["group"],
				"value" => $to->prop("start1"),
				"day" => "text",
				"month" => "text",
			);

			$rv["existing_old_" . $prefix . $id] = array(
				"type" => "hidden",
				"name" => $prefix . "[existing_old][" . $id . "]",
				"group" => $arr["prop"]["group"],
				"value" => $to->prop("start1"),
			);
//			$empty_slots--;
		}

		$rv["sbx"] = array(
			"name" => "sbx",
			"type" => "text",
			"subtitle" => 1,
			"caption" => t("Uued"),
		);

//		for ($i = 1; $i <= $empty_slots; $i++)
		for ($i = 1; $i <= 25; $i++)
		{
			// esimese v22rtus peab olema eventi enda alguse aeg
			$rv["new_" . $prefix . $i] = array(
				"type" => "datetime_select",
				"name" => $prefix . "[new][" . $i . "]",
				"caption" => sprintf(t("Uus %s"), $i),
				"group" => $arr["prop"]["group"],
				"value" => $start1,
				"day" => "text",
				"month" => "text",
			);

			$rv["newx_" . $prefix . $i] = array(
				"type" => "checkbox",
				"name" => $prefix . "[newx][" . $i . "]",
				"caption" => sprintf(t("Tee s&uuml;ndmus %s"), $i),
				"group" => $arr["prop"]["group"],
				"ch_value" => 1,
			);
		};
		return $rv;
	}



	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "times":
				$this->create_copies($arr);
				break;
			case "new_place":

				if (!empty($prop['value']))
				{
					$places_parent = aw_ini_get("staging.places_metamgr_oid");
					if (is_oid($places_parent) && $this->can("add", $places_parent))
					{
						$place = new object(array(
							"class_id" => CL_META,
							"parent" => $places_parent,
							"name" => $prop['value'],
						));
						$place->save();
					}
				}

		}
		return $retval;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "place":
				$places_parent = aw_ini_get("staging.places_metamgr_oid");
				$meta_objects = new object_list(array(
					"class_id" => CL_META,
					"parent" => $places_parent,
				));
				$places = array();
				foreach($meta_objects->arr() as $meta_object)
				{
					$prop['options'][$meta_object->id()] = $meta_object->name();
				}
				break;
		};
		return $retval;
	}

	/**
		@attrib name=fixxer all_args="1"
		@param group optional acl="edit,view"
		@param group2 required type="int"
	**/
	function fixxer($arr)
	{
		print "fixing events, eh?";
		$ol = new object_list(array(
			"class_id" => CL_STAGING,
		));
		print "<pre>";
		foreach($ol->arr() as $o)
		{
			if ($o->prop("start1") == 0)
			{
				// trying to fix the thing
				$conns = $o->connections_from(array(
					"type" => RELTYPE_ORIGINAL,
				));
				printf("%s\t\t\t%s\t%s\t%s\t%s\n",$o->name(),$o->lang(),$o->prop("start1"),$o->id(),$o->brother_of());
				if (sizeof($conns) > 0)
				{
					$first = reset($conns);
					$fo = $first->to();
					if ($fo->prop("start1") != 0)
					{
						$o->set_prop("start1",$fo->prop("start1"));
						$o->save();
					};
					/*
					print "has " . sizeof($conns) . " translations<br>";
					print "rs = " . $fo->prop("start1");
					*/
				};
			};
		};
		print "</pre>";
		die();
	}


	function create_copies($arr)
	{
		$times = $arr["prop"]["value"];

		load_vcl("date_edit");
		$de = new date_edit();

		$o = $arr["obj_inst"];
		$o = $o->get_original();

		// check if this is a copy and then show connections from the original object
		$copy_conns = $o->connections_to(array(
			"type" => 2 //"RELTYPE_COPY",
		));

		if (sizeof($copy_conns) > 0)
		{
			$first = reset($copy_conns);
			$o = $first->from();
		};

		// see raisk v&otilde;tab jah originaalist

		$brother_list = new object_list(array(
			"brother_of" => $o->id(),
		));

		$o_id = $o->id();
		$original_parent = $o->parent();

		// nii aga iga venna kohta mul vaja teada t6lgete id-sid no less.

		$blist = array();
		$xblist = array();
		foreach($brother_list->arr() as $brother)
		{
			$bparent = $brother->parent();

			if ($brother->id() == $o_id)
			{
				$original_parent = $brother->parent();
			};

			$xblist[$bparent] = $this->_get_translations_for($bparent);
			// xblist annab mulle ainult selle info kuhu alla vennad teha.
			// t6lked tuleb ikka igast koopiast eraldi rajada

			$blist[$bparent] = 1;
		};


		// siin loome (mitte ei uuenda) koopiaid - koopia tegemisel
		// 1. teha uus objekt
		// 2. kanda info yle
		// 3. teha t6lgete objektid
		// 4. kanda info yle

		// update times of existing objects
		if (is_array($times["existing"]))
		{
			$ext = $times["existing"];
			foreach($ext as $obj_id => $date_data)
			{
				$ts = $de->get_timestamp($date_data);
				if($ts === (int)$times["existing_old"][$obj_id])
				{
					continue;
				}

				$obj_copy = new object($obj_id);
				$obj_copy->set_prop("start1",$ts);

				$obj_copy->save();


				if ($obj_id == $arr["obj_inst"]->id())
				{
					$arr["obj_inst"]->set_prop("start1",$ts);
				};


			};
		}

		$news = $times["newx"];
		if (!is_array($news))
		{
			$news = array();
		};


		if (is_array($times["new"]))
		{
			$nw = $times["new"];
			foreach($nw as $idx => $date_data)
			{
				$ts = $de->get_timestamp($date_data);
				if ($news[$idx])
				{
					// loome uue koopia objekti
					//parent j22b samaks

					$new_obj = $o;


					$new_obj->set_prop("start1",$ts);
					$new_obj->save_new();



					// connection from original to the copy so that the original
					// can later manage copies
					$o->connect(array(
						"to" => $new_obj->id(),
						"reltype" => 2 //"RELTYPE_COPY",
					));

					foreach($xblist as $orig_brother_id => $items)
					{
						// loome vennad sinna kuhu vaja
						$new_obj->create_brother($orig_brother_id);
					};

				};


			};

		};

		// start time of the original object is also shown in that form so
		// update this as well
		/*
		$valx = $de->get_timestamp($times["active"]);
		$o->set_prop("start1",$valx);
		*/


	}

	function _get_translations_for($id,$ids = false)
	{
		$obj = new object($id);

		$tr_conns = $obj->connections_from(array(
			"type" => RELTYPE_TRANSLATION,
		));

		$rv = array();

		foreach($tr_conns as $tr_conn)
		{
			$tr_obj = $tr_conn->to();
			$argstr = $ids ? $tr_obj->lang_id() : $tr_obj->lang();
			$rv[$argstr] = $tr_obj->id();
		};


		return $rv;
	}


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

	/**
		@attrib name=fixxer2
	**/
	function fixer($arr)
	{
		$sql = "select objects.oid,name from objects left join planner on (objects.brother_of = planner.id) where planner.start >= 1125179999";
		$this->db_query($sql);
		$queries = array();
		while($row = $this->db_next())
		{
	//		arr($row);
			$queries[] = "UPDATE objects SET status = 0 WHERE oid = " . $row["oid"];
		}
	//	arr($queries);

		foreach($queries as $query)
		{
			$this->db_query($query);
		};



		print "<h1>all done</h1>";


		//arr($queries);
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

	// how do I get the mass entering of events to work?

	// 1. how do I save those objects so that they show up as normal events
	// in the calendar

	// 2. if I edit one of those cloned objects then should the other events also change?

	// 3. perhaps I should get the recurrency working properly .. this would allow me
	// to create better .. oh yes .. I think that is the solution


	// I should just be able to enter custom dates and be done with it .. YES!
}
?>
