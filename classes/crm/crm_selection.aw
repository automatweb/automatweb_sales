<?php
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_selection.aw,v 1.23 2008/01/31 13:54:16 kristo Exp $
/*
@classinfo relationmgr=yes syslog_type=ST_CRM_SELECTION maintainer=markop
@default table=objects
@default group=general

@default field=meta
@default method=serialize

@property template type=select
@caption Näitamise template

property active_selection type=textbox group=selectione

@property forms type=relpicker multiple=1 reltype=RELTYPE_BACKFORMS2
@caption Tagasiside vormid

@default group=contents
@groupinfo contents submit=no caption="Objektid"

@property selection_toolbar type=toolbar store=no no_caption=1
@caption Objektide toolbar

@property selection_objects type=table store=no no_caption=1
@caption Objektid

@default group=preview
@groupinfo preview caption="Näita" submit=no
@property contents type=callback callback=show_selection

@reltype BACKFORMS2 value=1 clid=CL_PILOT
@caption Tagasisidevorm

@reltype RELATED_SELECTIONS value=2 clid=CL_CRM_SELECTION
@caption Seotud valimid


*/

/*
CREATE TABLE `selection` (
  `oid` int(11) NOT NULL default '0',
  `object` int(11) NOT NULL default '0',
  `jrk` int(11) default NULL,
  `status` tinyint(4) default NULL,
  UNIQUE KEY `oid` (`oid`,`object`)
) TYPE=MyISAM;

*/
class crm_selection extends class_base
{
	var $selections_reltype;
	
	function crm_selection()
	{
		$this->init(array(
			'clid' => CL_CRM_SELECTION,
			'tpldir' => 'selection',
		));
		$this->selections_reltype = RELATED_SELECTIONS;
	}


	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;

		// mis FUCK sellega toimub?
		if (!isset($this->selection_args))
		{
			$this->selection_args = $args;
		}

		switch($data["name"])
		{
			case 'template':
				$tpls = $this->get_directory(array('dir' => $this->cfg['tpldir'].'/selection/templs/'));
				$data['options'] = $tpls;
				break;

			case 'active_selection':
				$retval = PROP_IGNORE;
				break;

			case "selection_toolbar":
				$this->gen_selection_toolbar($arr);
				break;

			case "selection_objects":
				$this->gen_object_table($arr);
				break;


		}
		return  $retval;
	}
	
	function set_property($arr)
	{
		$data = &$arr["prop"];
		$form = &$arr["request"];
		$retval = PROP_OK;
		// see on küll täiesti vale koht selleks, damn I need to add a lot to the 
		// classbase documentation
		if (isset($form['del']))
		{
			$this->remove_objects_from_selection($arr['obj_inst']->id(),$form['sel']);
		}
		switch($data['name'])
		{

		};
		return $retval;
	}

	function show_selection($args)
	{
		$retval = $this->show(array(
			"id" => $args["obj_inst"]->id(),
		));
		$nodes = array();
		$nodes[] = array(
			"value" => $retval,
		);
		return $nodes;
	}

	////
	// !Generates a list of objects in a selection
	// id - id of the selection
	function gen_object_table($arr)
	{
		$objects = $this->get_selection($arr["obj_inst"]->id());
		$t = &$arr["prop"]["vcl_inst"];

		// I need a way to let the table know that the incoming data is already 
		// sorted.

		$t->set_default_sortby('name');

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('nimi'),
			'sortable' => '1',
			'callback' => array(&$this, 'callb_name'),
			'callb_pass_row' => true,
		));

		$t->define_field(array(
			'name' => 'jrk',
			'caption' => t('jrk'),
			'width' => '20',
			'sortable' => '1',
			'numeric' => 1,
			'callback' => array(&$this, 'callb_jrk'),
			'callb_pass_row' => true,
		));
		$t->define_field(array(
			'name' => 'active',
			'caption' => "<a href='javascript:selall(\"status\")' title='muuda kõikide objektide aktiivsust'>aktiivne</a>",
			'width' => '20',
			'callback' => array(&$this, 'callb_active'),
			'callb_pass_row' => true,
		));

		$t->define_field(array(
			'name' => 'class_id',
			'caption' => t('tüüp'),
			'sortable' => '1',
		));

		$t->define_field(array(
			'name' => 'comment',
			'caption' => t('kommentaar'),
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
		));

		$clss = aw_ini_get("classes");
		if (is_array($objects))
		{
			foreach ($objects as $object)
			{
				if (!$this->can("view", $object["object"]))
				{
					continue;
				};
				$item = new object($object["object"]);
				$t->define_data(array(
					"id" => $item->id(),
					"name" => $item->name(),
					"comment" => $item->comment(),
					"status" => $object["status"],
					"jrk" => $object["jrk"],
					"clid" => $item->class_id(),
					"class_id" => $clss[$item->class_id()]["name"],
				));
			}
		}
	}

	function callb_name($arr)
	{
		return html::href(array(
			'caption' => $arr['name'],
			'url' => $this->mk_my_orb('change', array(
					'id' => $arr["id"],
					'return_url' => get_ru(),
				),$arr['clid']),
		));
	}


	function callb_jrk($arr)
	{
		return  html::textbox(array(
			'size' => 4,
			'maxlength' => 4,
			'name' => 'jrk['.$arr["id"].']',
			'value' => (int)$arr['jrk'],
		));
	}

	function callb_active($arr)
	{
		return html::checkbox(array(
			'size' => 4,
			'maxlength' => 4,
			'name' => 'status['.$arr["id"].']',
			'value' => 1,
			'checked' => ((int)$arr['status']==1)
		));
	}
	
	/**  
		
		@attrib name=navigate
		
		@returns
		
		@comment navigates to another selection

	**/
	function navigate($arr)
	{
		if (is_oid($arr["target_selection"]))
		{
			$tid = $arr["target_selection"];
		}
		else
		{
			$tid = $arr["id"];
		};
		return $this->mk_my_orb("change",array("id" => $tid,"group" => $arr["group"]));
	}
	
	/**  
		
		@attrib name=copy_objects 
		
		@returns
		
		@comment copies objects from one selection to another

	**/
	function copy_objects($arr)
	{
		if (is_oid($arr["target_selection"]) && is_array($arr["sel"]))
		{
			$target_selection = $arr["target_selection"];
			$target_obj = new object($target_selection);

			print t("Objektide kopeerimine valimisse " . $target_obj->name() . "<bR>");

			foreach($arr["sel"] as $key => $val)
			{
				$tmp = new object($val);
				print $tmp->name() . "<br>";
				$values[]='('.$target_selection.','.$key.')';
			}
			$this->db_query("insert into selection(oid,object) values ".implode(',',$values));

		}
		return $this->mk_my_orb("change",array("id" => $arr["id"],"group" => $arr["group"]));
	}
	
	/**  
		
		@attrib name=move_objects 
		
		@returns
		
		@comment moves objects from one selection to another

	**/
	function move_objects($arr)
	{
		if (is_oid($arr["target_selection"]) && is_array($arr["sel"]))
		{
			$target_selection = $arr["target_selection"];
			$target_obj = new object($target_selection);

			print t("Objektide liigutamine valimisse " . $target_obj->name() . "<bR>");

			$source_ids = array();

			foreach($arr["sel"] as $key => $val)
			{
				$tmp = new object($val);
				$source_ids[] = $val;
				print $tmp->name() . "<br>";
				$values[]='('.$target_selection.','.$key.')';
			}
			$this->db_query("DELETE FROM selection WHERE oid = '$arr[id]' AND object IN (" . join(",",$source_ids) . ")");
			$this->db_query("insert into selection(oid,object) values ".implode(',',$values));

		}
		return $this->mk_my_orb("change",array("id" => $arr["id"],"group" => $arr["group"]));
	}
	
	
	/**  
		
		@attrib name=delete_from_selection params=name 
		
		@param id required
		@param return_url required
		
		@returns
		
		
		@comment

	**/
	function delete_from_selection($arr)
	{
		if (is_array($arr['sel']))
		{
			$this->remove_objects_from_selection($arr['id'],$arr['sel']);
		}
		return $this->mk_my_orb("change",array("id" => $arr["id"],"group" => $arr["group"]));
	}

	/**  
		
		@attrib name=save_selection params=name 
		
		@param id required
		@param return_url required
		
		@returns
		
		
		@comment

	**/
	function save_selection($arr)
	{
		if(is_array($arr['jrk']))
		{
			$sel = $this->get_selection($args['id']);

			foreach($arr['jrk'] as $key => $val)
			{
				if (($sel[$key]['jrk'] != $key) || ((int)$sel['status'][$key] != (int)$arr['status'][$key]))
				{
					$q = 'UPDATE selection SET jrk="'.$val.'" , status="'.$arr['status'][$key].'" WHERE oid='.$arr['id'].' AND object='.$key;
					$this->db_query($q);
				}
			}
		}
		return $this->mk_my_orb("change",array("id" => $arr["id"],"group" => $arr["group"]));
	}


	/**  
		
		@attrib name=add_to_selection params=name 
		
		@param id required
		@param return_url required
		
		@returns
		
		
		@comment

	**/
	function add_to_selection($args)
	{
		// see on siis mingi sitt, mis teeb kas uue selektsiooni või liigutab asju 
		// ühest kohast teise .. geezas christ.
		//$uri = $args['return_url'];
		if ($args['add_to_selection'])
		{
			if (is_array($args['sel']))
			{
				$this->set_selection($args['add_to_selection'], $args['sel'],false);
			}

		};
	}

	function gen_selection_toolbar($arr)
	{
		$toolbar = &$arr["prop"]["toolbar"];

		$ops = array();

		$conns = $arr["obj_inst"]->connections_from(array(
			"class" => CL_CRM_SELECTION,
		));

		foreach($conns as $conn)
		{
			$ops[$conn->prop("to")] = $conn->prop("to.name");
		};
		
		$parent = $arr["obj_inst"]->parent();
		
		$REQUEST_URI = aw_global_get("REQUEST_URI");

		$users = get_instance("users");

		asort($ops);
		
		$str .= html::select(array(
			'name' => 'target_selection',
			'options' => array(" - vali valim - ") + $ops,
			'selected' => $selected,
		));

		$toolbar->add_cdata($str);

		$toolbar->add_button(array(
			"name" => 'go_move',
			"tooltip" => t("Liiguta"),
			"action" => "move_objects",
			"img" => "import.gif",
		));
		
		$toolbar->add_button(array(
			"name" => 'go_copy',
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri"),
			"action" => "copy_objects",
		));

		$toolbar->add_button(array(
			"name" => "navigate",
			"tooltip" => t('aktiveeri'),
			"img" => "edit.gif",
			"action" => "navigate",
		));

		$toolbar->add_separator();

		$toolbar->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "save_selection",
			"img" => "save.gif",
		));

		$toolbar->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta valitud objektid valimist"),
			"confirm" => t("Kustutada valitud objektid sellest valimist?"),
			"img" => "delete.gif",
			"action" => "delete_from_selection",
		));

		$str = "";
		$str .= $this->get_file(array("file" => $this->cfg['tpldir'].'/kliendibaas/selall.script'));
		$toolbar->add_cdata($str);
	}

	function get_selection($oid, $activs_only = false)
	{
		if (!isset($oid))
		{
			return array();
		}

		if ($activs_only)
		{
			$q = 'select * from selection where status="1" and oid="'.$oid.'"';
		}
		else
		{
			$q = 'select * from selection where oid="'.$oid.'" order by jrk';
		}

		$this->db_query($q);
		while ($row = $this->db_next())
		{
			$arr[$row['object']] = $row;
		}
		return $arr;
	}

	function set_selection($oid,$arr,$replace=true)
	{
		if (count($arr)>0)
		{
			foreach($arr as $key => $val)
			{
				$values[]='('.$oid.','.$key.')';
			}

			$q ="delete from selection where oid='$oid' and object in (".implode(' , ',array_keys($arr)).")";
			$this->db_query($q);

			// ja siis lisame uued
			return $this->db_query("insert into selection(oid,object) values ".implode(',',$values));
		}
	}

	////
	// !oid - selection id
	// objects - array of object id's to be removed:
	function remove_objects_from_selection($oid,$arr=array())
	{
		$items = new aw_array($arr);
		$this->db_query("DELETE FROM selection WHERE oid = '$oid' AND object IN (" . $items->to_sql() . ")");
	}


	function cmp_obj($a, $b)
	{
		if ($a[$this->sortby] == $b[$this->sortby]) return 0;
		return ($a[$this->sortby] > $b[$this->sortby]) ? +1 : -1;
	}

	/** Displays active items from selection using a template 
		
		@attrib name=show params=name 
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function show($arr)
	{
		$obj = new object($arr["id"]);
		$arr = $this->get_selection($obj->id(),"active");
		if ("" == $obj->prop("template"))
		{
			return t('templiit määramata');
		}

		$this->tpl_init("selection/templs");
		$this->read_template($obj->prop("template"));

		$str = "";

		if (is_array($arr))
		{

			$this->default_forms = $obj->prop("forms");
			//sorteerime jrk järgi
			$this->sortby = 'jrk';
			uasort($arr, array ($this, 'cmp_obj'));

			foreach ($arr as $key => $val)
			{
				$item = new object($val["object"]);
				// figure out which class processes the aliases.. dunno, really
				$inst = $item->instance();

				if (method_exists($inst,'show_in_selection'))
				{
					$inst->default_forms = $this->default_forms;
					$str .= $inst->show_in_selection(array(
						"id" => $item->id(),
					));
				}
				else
				{
					$str .= $this->show_in_selection(array(
						"id" => $item->id(),
					));
				}

			}
		}
		else
		{
			$str = t(' valim tühi, või objekte pole aktiivseks tehtud');
		}
		return $str;
	}

	function show_in_selection($args)
	{
		$forms = "";
		$tagasisidevormid = "";
		if (is_array($this->default_forms))
		{
			foreach($this->default_forms as $val)
			{
				$form = new object($val);
				$tagasisidevormid .= html::href(array(
				'target' => $form->meta('open_in_window') != "" ? '_blank' : NULL,
				'caption' => $form->name(), 'url' => $this->mk_my_orb('form', array(
					'id' => $form->id(),
					'feedback' => $args['id'],
					),'pilot_object'))).'<br />';
			}
		}
		$obj = new object($args["id"]);
		$this->vars(array(
			"name" => $obj->name(),
			"parent" => $obj->parent(),
			"id" => $obj->id(),
		));
		$this->vars(array(
			"object" => $this->parse("object"),
		));
		return $this->parse();
	}


	function parse_alias($args)
	{
		extract($args);
		return $this->show(array('id' => $alias['target']));
	}
}
?>
