<?php
// treeview.aw - tree generator
/*

	@classinfo relationmgr=yes

	@default table=objects
	@default group=general

	@property root type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
	@caption Root objekt

	@property rootcaption type=textbox field=meta method=serialize
	@caption Root objekti nimi

	@property icon_root type=relpicker reltype=RELTYPE_ICON field=meta method=serialize
	@caption Root objekti ikoon

	@property treetype type=select field=meta method=serialize
	@caption Puu t&uuml;&uuml;p

	@property icon_folder_open type=relpicker reltype=RELTYPE_ICON field=meta method=serialize
	@caption Lahtise kausta ikoon

	@property icon_folder_closed type=relpicker reltype=RELTYPE_ICON field=meta method=serialize
	@caption Kinnise kausta ikoon

	@reltype FOLDER value=1 clid=CL_MENU
	@caption root kataloog

	@reltype ICON value=2 clid=CL_ICON
	@caption ikoon
*/
//DEPRECATED constants
define("TREE_HTML", 1); define("TREE_DHTML", 3); define("TREE_DHTML_WITH_CHECKBOXES", 4); define("TREE_DHTML_WITH_BUTTONS", 5); define("LOAD_ON_DEMAND",1); define("PERSIST_STATE",2); define("DATA_IN_PLACE",3);

class treeview extends class_base
{
/*
vars
$config
$clidlist
$arr
$cfg
$r_path = array();
$level = 0;
has_root = false;
tree_dat = array()
auto_open = false
items = array()
tree_type = TREE_DHTML
item_name_length
tree_id
get_branch_func
branch
features = array()
*/

	const TYPE_HTML = 1;
	const TYPE_DHTML = 3;
	const TYPE_DHTML_WITH_CHECKBOXES = 4;
	const TYPE_DHTML_WITH_BUTTONS = 5;
	const TYPE_JS = 6;

	// does this tree type support loading branches on-demand?
	const LOAD_ON_DEMAND = 1;

	// does this tree type support persist state (using cookies)
	const PERSIST_STATE = 2;

	// for load on demand, to show that subelemenets are loaded and no load-on-demand is used anymore
	const DATA_IN_PLACE = 3;

	var $only_one_level_opened = 0;
	var $level;
	var $selected_item;
	private $rootnode = 0;
	private $first_level_menu_is_last;
	private $untitled_text = "[untitled]";

	private $js_tree_data_source_url; //aw_uri object

	//////////////TODO: m22rata skoop
	var $auto_open_tmp;
	var $clidlist;
	var $ic;
	var $arr = array();
	var $items = array();
	var $itemdata = array();
	var $config = array();
	var $features = array();
	//////////////

	function treeview($args = array())
	{
		$this->init(array(
			"tpldir" => "treeview",
			"clid" => CL_TREEVIEW
		));
		$this->untitled_text = t("[nimetu]");
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		switch($data["name"])
		{
			case "treetype":
				$data["options"] = array("" => "--vali--","dhtml" => "DHTML (Ftiens)");
				break;
		}
	}

	function init_vcl_property($arr)
	{
		$pr = $arr["property"];
		$this->start_tree(array(
			"type" => self::TYPE_DHTML,
			"tree_id" => $pr["name"], // what if there are multiple trees
			"persist_state" => 1,
			"item_name_length" => isset($pr["item_name_length"]) ? $pr["item_name_length"] : null
		));
		$pr["vcl_inst"] = $this;
		return array($pr["name"] => $pr);
	}

	function get_html()
	{
		$rv = $this->finalize_tree();
		return $rv;
	}

	/**
		@attrib api=1 params=pos
		@param type type=int
			One of treeview::TYPE_... constants
		@returns void
		@errors
			throws awex_param_type if type parameter value is incorrect
	**/
	public function set_type($type)
	{
		if (
			self::TYPE_DHTML !== $type and
			self::TYPE_DHTML_WITH_BUTTONS !== $type and
			self::TYPE_DHTML_WITH_CHECKBOXES !== $type and
			self::TYPE_HTML !== $type and
			self::TYPE_JS !== $type
		)
		{
			throw new awex_param_type("Invalid tree type '{$type}'");
		}

		$this->tree_type = $type;
	}

	////
	// !Generates a tree. Should be used from _inside_ the code, because
	// this can accept arguments that could be harmful when used through ORB
	function generate($args = array())
	{
		// generates the tree
		extract($args);
		$root = $args["config"]["root"];
		$this->config = $args["config"];

		$rootobj = obj($root);
		$treetype = $rootobj->meta("treetype");
		if (!empty($treetype))
		{
			$type = $treetype;
		}
		else
		{
			$type = "dhtml";
		}

		$this->read_template("ftiens.tpl");
		$this->clidlist = (is_array($args["config"]["clid"])) ? $args["config"]["clid"] : CL_MENU;


		// I need a way to display all kind of documents here, and not only
		// menus. So, how on earth am I going to do that.

		// if the caller specified clid list, then we list all of those objects,
		// if not, then only menus

		// listib koik menyyd ja paigutab need arraysse

		// objektipuu
		$this->rec_tree($root);

		$tr = $this->generate_tree($root);

		$icon_root = $rootobj->meta("icon_root");

		$this->vars(array(
			"TREE" => $tr,
			"DOC" => "",
			"root" => $root,
			"linktarget" => isset($args["linktarget"]) ? $args["linktarget"] : "",
			"shownode" => isset($args["shownode"]) ? $args["shownode"] : "",
			"rootname" => $rootobj->meta("rootcaption"),
			"rooturl" => $this->do_item_link($rootobj),
			"icon_root" => !empty($icon_root) ? $this->mk_my_orb("show",array("id" => $icon_root),"icons") : "/automatweb/images/aw_ikoon.gif",
		));

		$retval = $this->parse();
		return $retval;
	}

	/** Public/ORB interface
		@attrib name=show params=name default=0
		@param id required type=int
	**/
	function show($args)
	{
		extract($args);
		$obj = obj($id);
		return $this->generate(array(
			"config" => $obj->meta(),
		));
	}

	function rec_tree($parent)
	{
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => $this->clidlist
		));
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$row = $o->fetch();
			$row["name"] = str_replace("\"","&quot;", $row["name"]);
			$this->arr[$row["parent"]][] = $row;
			$this->rec_tree($row["oid"]);
		}

		return;
	}

	function generate_tree($parent)
	{
		if (!is_array($this->arr[$parent]))
		{
			return;
		}

		$ret = "";
		reset($this->arr[$parent]);
		while (list(,$row) = each($this->arr[$parent]))
		{
			// tshekime et kas menyyl on submenyysid
			// kui on, siis n2itame alati
			// kui pole, siis tshekime et kas n2idatakse perioodilisi dokke
			// kui n2idatakse ja menyy on perioodiline, siis n2itame menyyd
			// kui pole perioodiline siis ei n2ita
			if (isset($this->arr[$row["oid"]]) && is_array($this->arr[$row["oid"]]))
			{
				$sub = $this->generate_tree($row["oid"]);;
			}
			else
			{
				$sub = "";
			}

			$icon_url = ($row["class_id"] == CL_MENU) ? "" : icons::get_icon_url($row["class_id"],"");
			$url = $this->do_item_link($row);
			$this->vars(array(
				"name" => $row["name"],
				"id" => $row["oid"],
				"parent" => $row["parent"],
				"iconurl" => $icon_url,
				"url" => $url,
				"targetframe" => "right",
			));
			if ($sub == "")
			{
				$ret.=$this->parse("DOC");
			}
			else
			{
				$ret.=$this->parse("TREE").$sub;
			}
		}
		return $ret;
	}

	function do_item_link($row)
	{
		if (isset($row["link"]) && $row["link"])
		{
			$url = $row["link"];
		}
		else
		{
			$url = aw_ini_get("baseurl") . $row["oid"];
		};
		return $url;
	}

	public function set_root_name($name)
	{
		$this->has_root = true;
		$this->tree_dat["root_name"] = $name;
	}

	public function set_root_icon($name)
	{
		$this->has_root = true;
		$this->tree_dat["root_icon"] = $name;
	}

	public function set_root_url($name)
	{
		$this->has_root = true;
		$this->tree_dat["root_url"] = $name;
	}

	/**
		@attrib api=1 params=pos
		@param uri type=aw_uri
		@returns void
		@errors none
	**/
	public function set_data_source_url(aw_uri $uri)
	{
		$this->js_tree_data_source_url = $uri;
	}


	/** Initializes tree

		@attrib name=start_tree params=name api=1

		@param root_name [optional|required] type=string
			Root menu name

		@param root_url [optional|required] type=string
			Root menu url

		@param root_icon optional type=string
			Root menu icon

		@param has_root optional type=bool default=false
			Whether to draw to the root node. trees that load branches on demand don't need to draw the rootnode for branches.

		@param tree_id optional type=string default=false
			Set to an unique id, if you want the tree to persist it's state.

		@param type optional type=int default=TREE_DHTML
			Tree type. [TREE_HTML|TREE_DHTML|TREE_DHTML_WITH_CHECKBOXES|TREE_DHTML_WITH_BUTTONS]

		@param persist_state optional type=bool
			Tries to remember tree state in cookie.

		@param separator optional type=string default=","
			String separator to use for separating checked node id-s, applies when type is TREE_DHTML_WITH_CHECKBOXES or TREE_DHTML_WITH_BUTTONS.

		@param checked_nodes optional type=array
			Tree node id-s that are checked initially, applies when type is TREE_DHTML_WITH_CHECKBOXES.

		@param checkbox_data_var optional type=string default=$tree_id
			Name for variable that will contain posted data of what was checked/unchecked. applicable only when tree type is TREE_DHTML_WITH_CHECKBOXES or TREE_DHTML_WITH_BUTTONS.

		@param data_in_place optional type=int
			For load on demand tree, if this is set to '1', no load on demand is used this point forward for that branch. [1|0]

		@param open_path optional type=array

		@param get_branch_func optional type=string default=false

		@param branch optional type=bool default=false

		@param item_name_length optional type=int default=false
			Maximum length of the item name.

		@examples

		$t = new treeview();
		$t->start_tree(array(
			'type' => TREE_DHTML,
			'root_name' => 'some_tree',
			'tree_id' => 'foobar',
			'persist_state' => true,
		));

		$t->add_item(0, array(
			"id" => 2,
			"name" => 'Foo',
			"url" => $this->mk_my_orb("do_something",array())
		));

		$t->add_item(2, array(
			"id" => 3,
			"name" => 'Foo',
			"url" => $this->mk_my_orb("do_something",array())
		));

		echo $t->finalize_tree();

	**/
	function start_tree($arr)
	{
		$this->auto_open = ( isset($arr["open_path"]) && is_array( $arr["open_path"] ) && count( $arr["open_path"] ) ) ? $arr["open_path"] : false;
		$this->items = array();
		$this->tree_type = empty($arr["type"]) ? self::TYPE_DHTML : $arr["type"];
		$this->tree_dat = $arr;
		$this->item_name_length = empty($arr["item_name_length"]) ? false : $arr["item_name_length"];
		$this->has_root = empty($arr["has_root"]) ? false : $arr["has_root"];
		$this->tree_id = empty($arr["tree_id"]) ? false : $arr["tree_id"];
		$this->get_branch_func = empty($arr["get_branch_func"]) ? false : $arr["get_branch_func"];
		$this->branch = empty($arr["branch"]) ? false : true;
		$this->root_id = isset($arr["root_id"]) ? trim($arr["root_id"]) : null;

		if(($this->tree_type == self::TYPE_DHTML) && !empty($this->get_branch_func) && !empty($arr["data_in_place"]))
		{
			$this->set_feature(self::DATA_IN_PLACE);
		}

		if ($this->tree_type == self::TYPE_DHTML or $this->tree_type == self::TYPE_DHTML_WITH_CHECKBOXES or $this->tree_type == self::TYPE_DHTML_WITH_BUTTONS)
		{
			if (!empty($this->get_branch_func))
			{
				$this->features[self::LOAD_ON_DEMAND] = 1;
			}

			if (!empty($this->tree_id) && !empty($arr["persist_state"]))
			{
				$this->features[self::PERSIST_STATE] = 1;
			}
		}

		if ($this->tree_type == self::TYPE_DHTML_WITH_CHECKBOXES)
		{
			$this->separator = empty($arr["separator"]) ? "," : $arr["separator"];
			$this->checked_nodes = isset($arr["checked_nodes"]) ? $arr["checked_nodes"] : array();
			$this->checkbox_data_var = empty ($arr["checkbox_data_var"]) ? $arr["tree_id"] : $arr["checkbox_data_var"];
		}
		elseif ($this->tree_type == self::TYPE_DHTML_WITH_BUTTONS)
		{
			$this->separator = empty($arr["separator"]) ? "," : $arr["separator"];
			$this->checkbox_data_var = empty ($arr["checkbox_data_var"]) ? $arr["tree_id"] : $arr["checkbox_data_var"];
		}

		$this->open_nodes = array();
	}

	public function set_branch_func($fc)
	{
		$this->get_branch_func = $fc;
		if (($this->tree_type == TREE_DHTML or $this->tree_type == TREE_DHTML_WITH_CHECKBOXES or $this->tree_type == TREE_DHTML_WITH_BUTTONS) && !empty($this->get_branch_func))
		{
			$this->features[self::LOAD_ON_DEMAND] = 1;
		}
	}

	function set_feature($feat, $val = 1)
	{
		$this->features[$feat] = $val;
	}

	function has_feature($feature)
	{
		return isset($this->features[$feature]) ? 1 : 0;
	}

	/** Adds item to the tree

		@attrib name=add_item params=pos api=1

		@param parent required type=string
			The parent of the item to be added

		@param item required type=array
			Array of item data:
				id - id of the item
				name - the name of the item
				url - the link for the item
				iconurl - the url of the icon
				target - the target frame of the link
				checkbox_status - 1|0 i.e. checked or unchecked. applicable only when dhtml tree with checkboxes is used

		@examples
			#start_tree

	**/
	function add_item($parent, $item)
	{
		// dhtml tree (sometimes) needs to know information about
		// a specific node and for this it needs to access
		// that node directly.
		if($this->item_name_length)
		{
			$item["name"]= substr($item["name"], 0, $this->item_name_length).(strlen($item["name"]) > 20 ? "..." : "");
		}

		if (!isset($item["id"]) )
		{
			$item["id"] = uniqid("aw_treeview_item_");
		}

		$this->itemdata[$item["id"]] = $item;
		$this->items[$parent][] = &$this->itemdata[$item["id"]];
		if (!empty($item["is_open"]))
		{
			$this->open_nodes[] = $item["id"];
		}
	}

	/** Returns the array with the item ids in tree
		@attrib name=get_item_ids params=name api=1
		@returns
			Array with item ids in tree

	**/
	function get_item_ids()
	{
		return array_keys($this->itemdata);
	}

	/** Returns the data of an item in tree
		@attrib name=get_item params=pos api=1
		@param id required type=string
			The key (id) of an item
		@returns
			Array with item data

	**/
	function get_item($id)
	{
		return $this->itemdata[$id];
	}

	function set_item($data)
	{
		return $this->itemdata[$data["id"]] = $data;
	}

	/** Removes an item from the tree
		@attrib name=remove_item params=pos api=1
		@param id required type=string
			The key (id) of an item
	**/
	function remove_item($id)
	{
		unset($this->itemdata[$id]);
		foreach($this->items as $k => $d)
		{
			foreach($d as $k2 => $v)
			{
				if ($v["id"] == $id)
				{
					unset($this->items[$k][$k2]);
					return;
				}
			}
		}
	}

	/** Sets the selcted element in the tree
		@attrib params=pos api=1
		@param id type=string
			The key (id) of an item
	**/
	public function set_selected_item($id)
	{
		$this->selected_item = $id;
	}

	/** Checks if a node have children or not
		@attrib params=pos api=1
		@param id required type=string
			The key (id) of an item
		@returns
			Boolean true if item exists
			Boolean false if item doesn't exists

	**/
	public function node_has_children($id)
	{
		return is_array($this->items[$id]) && sizeof($this->items[$id]) > 0;
	}

	////
	// !draws the tree
	// rootnode - from which node should drawing start (defaults to 0)

	/** Draws the tree
		@attrib params=name api=1
		@param rootnode type=string default=0
			From which node should drawing start (defaults to 0)

		@returns string
			Parsed tree
		@examples
			#start_tree

	**/
	function finalize_tree($arr = array())
	{
		if (!empty($arr["rootnode"]))
		{
			$this->rootnode = $arr["rootnode"];
		}

		if ($this->tree_type == self::TYPE_HTML)
		{
			$rendered_tree = $this->html_finalize_tree();
		}
		elseif (self::TYPE_JS === $this->tree_type)
		{
			$rendered_tree = $this->js_finalize_tree();
		}
		elseif ($this->tree_type == TREE_DHTML)
		{
			$rendered_tree = $this->dhtml_finalize_tree();
		}
		elseif ($this->tree_type == TREE_DHTML_WITH_CHECKBOXES)
		{
			$rendered_tree = $this->dhtml_checkboxes_finalize_tree ();
		}
		elseif ($this->tree_type == TREE_DHTML_WITH_BUTTONS)
		{
			$rendered_tree = $this->dhtml_buttons_finalize_tree ();
		}

		return $rendered_tree;
	}

	private function html_finalize_tree()
	{
		$this->read_template("html_tree.tpl");
		$ml = array();
		$this->draw_html_tree($this->rootnode, $ml);

		$this->vars(array(
			"colspan" => 10
		));
		return $this->parse("TREE_BEGIN").implode("\n", $ml).$this->parse("TREE_END");
	}

	private function js_finalize_tree()
	{
		active_page_data::load_javascript("jquery/plugins/jquery.cookie.js");
		active_page_data::load_javascript("jquery/plugins/jsTree/jquery.jstree.js");
		$this->read_template("js_tree.tpl");
		$this->vars(array(
			"selected_item" => str_replace("'", "\\'", $this->selected_item),
			"tree_id" => $this->tree_id,
			"data_source_url" => $this->js_tree_data_source_url->get(),
		));
		return $this->parse();
	}

	private function dhtml_finalize_tree()
	{
		$level = 0;
		$this->rv = "";
		$this->set_parse_method("eval");
		$this->read_template("dhtml_tree.tpl");


		$this->r_path = array();
		// now figure out the paths to selected nodse
		// ja nagu sellest veel kyllalt poleks .. I can have multiple opened nodes. yees!
		if ($this->has_feature(PERSIST_STATE) && !$this->has_feature(LOAD_ON_DEMAND))
		{
			$opened_nodes = explode("^",isset($_COOKIE[$this->tree_id]) ? $_COOKIE[$this->tree_id] : "");
			$r_path = array();
			foreach($opened_nodes as $open_node)
			{
				$rp = $this->_get_r_path($open_node);
				$r_path = array_merge($r_path,$rp);
			};
			$this->r_path = array_unique($r_path);
		}

		if (sizeof($this->open_nodes) > 0)
		{
			$this->r_path = $this->r_path + $this->open_nodes;
		}

		$t = new languages();

		$level = (!empty($_REQUEST["called_by_js"]) and isset($_COOKIE[$this->tree_id."_level"])) ? $_COOKIE[$this->tree_id."_level"] : 1;
		if(!strlen($this->auto_open))
		{
			$tri = isset($_COOKIE[$this->tree_id]) ? $_COOKIE[$this->tree_id] : null;
			$this->auto_open = is_array(explode("^",$tri)) ? join(",",map("'%s'",explode("^",$tri))) : "";
		}
		else
		{
			foreach($this->auto_open as $item)
			{
				$this->auto_open_tmp .= ",'".$item."'";
			}
			$this->auto_open = "''".$this->auto_open_tmp;
		}
		$tree_nums = aw_global_get("dhtml_tree_count");
		if(empty($tree_nums[$this->tree_id]))
		{
			$tree_nums[$this->tree_id] = empty($_GET["tree_num"]) ? count($tree_nums) + 1 : $_GET["tree_num"];
		}
		aw_global_set("dhtml_tree_count", $tree_nums);
		$this->vars(array(
			"target" => isset($this->tree_dat["url_target"]) ? $this->tree_dat["url_target"] : null,
			"open_nodes" => $this->auto_open,
			"level" => $level,
			"load_auto" => isset($_REQUEST["load_auto"])?$_REQUEST["load_auto"]:"true",
			"tree_id" => $this->tree_id,
			"tree_num" => $tree_nums[$this->tree_id],
			"charset" => $t->get_charset()
		));

		$rv = $this->draw_dhtml_tree($this->rootnode);

		$root = "";
		if ($this->has_root)
		{
			if(empty($this->tree_dat["root_name"]))
			{
				$this->tree_dat["root_name"] = "";
			}

			if(empty($this->tree_dat["root_url"]))
			{
				$this->tree_dat["root_url"] = "";
			}

			$this->vars(array(
				"rootname" => $this->tree_dat["root_name"],
				"rooturl" => $this->tree_dat["root_url"],
				"icon_root" => !empty($this->tree_dat["root_icon"]) ? $this->tree_dat["root_icon"] : "/automatweb/images/aw_ikoon.gif",
			));

			if ($this->get_branch_func)
			{
				$this->vars(array(
					"get_branch_func" => $this->get_branch_func,
				));
			};

			$root .= $this->parse("HAS_ROOT");
		}

		// so, by default all items below the second level are hidden, but I should be able to
		// make them visible based on my selected item. .. oh god, this is SO going to be not
		// fun

		// so, how do I figure out the path to the root node .. and if I do, then that's the
		// same thing I'll have to give as an argument when using the on-demand feature


		$this->vars(array(
			"TREE_NODE" => $rv,
			"HAS_ROOT" => $root,
			"persist_state" => $this->has_feature(PERSIST_STATE),
			'only_one_level_opened' => $this->only_one_level_opened,
		));

		return $this->parse();
	}

	private function dhtml_checkboxes_finalize_tree()
	{
		$level = 0;
		$this->rv = "";
		$this->set_parse_method("eval");
		$this->read_template("dhtml_checkboxes_tree.tpl");

		$this->r_path = array();
		// now figure out the paths to selected nodes

		// ja nagu sellest veel kyllalt poleks .. I can have multiple opened nodes. yees!
		if ($this->has_feature(PERSIST_STATE))
		{
			$opened_nodes = isset($_COOKIE[$this->tree_id]) ? explode("^", $_COOKIE[$this->tree_id]) : array();
			$r_path = array();
			foreach($opened_nodes as $open_node)
			{
				$rp = $this->_get_r_path($open_node);
				$r_path = array_merge($r_path,$rp);
			};
			$this->r_path = array_unique($r_path);
		};

		$t = get_instance("languages");
		$checked_nodes = is_array ($this->checked_nodes) ? implode ($this->separator, $this->checked_nodes) : "";

		$this->vars (array(
			"target" => $this->tree_dat["url_target"],
			"open_nodes" => is_array($opened_nodes) ? join(",",map("'%s'",$opened_nodes)) : "",
			"tree_id" => $this->tree_id,
			"charset" => $t->get_charset(),
			"separator" => $this->separator,
			"checked_nodes" => $checked_nodes,
			"checkbox_data_var" => $this->checkbox_data_var,
		));

		$rv = $this->draw_dhtml_tree_with_checkboxes ($this->rootnode);

		$root = "";
		if ($this->has_root)
		{
			$this->vars(array(
				"rootname" => $this->tree_dat["root_name"],
				"rooturl" => $this->tree_dat["root_url"],
				"icon_root" => ($this->tree_dat["root_icon"] != "" ) ? $this->tree_dat["root_icon"] : "/automatweb/images/aw_ikoon.gif",
			));
			if ($this->get_branch_func)
			{
				$this->vars(array(
					"get_branch_func" => $this->get_branch_func,
				));
			};
			$root .= $this->parse("HAS_ROOT");
		};

		// so, by default all items below the second level are hidden, but I should be able to
		// make them visible based on my selected item. .. oh god, this is SO going to be not
		// fun

		// so, how do I figure out the path to the root node .. and if I do, then that's the
		// same thing I'll have to give as an argument when using the on-demand feature

		$this->vars(array(
			"TREE_NODE" => $rv,
			"HAS_ROOT" => $root,
			"persist_state" => $this->has_feature(PERSIST_STATE),
			'only_one_level_opened' => $this->only_one_level_opened,
		));

		return $this->parse();
	}

	private function dhtml_buttons_finalize_tree()
	{
		$level = 0;
		$this->rv = "";
		$this->set_parse_method("eval");
		$this->read_template("dhtml_buttons_tree.tpl");

		$this->r_path = array();
		// now figure out the paths to selected nodes

		// ja nagu sellest veel kyllalt poleks .. I can have multiple opened nodes. yees!
		if ($this->has_feature(PERSIST_STATE) && !$this->has_feature(LOAD_ON_DEMAND))
		{
			$opened_nodes = empty($_COOKIE[$this->tree_id]) ? array() : explode("^", $_COOKIE[$this->tree_id]);
			$r_path = array();

			foreach($opened_nodes as $open_node)
			{
				$rp = $this->_get_r_path($open_node);
				$r_path = array_merge($r_path,$rp);
			}

			$this->r_path = array_unique($r_path);
		}
		else
		{
			$opened_nodes = array();
		}

		$t = new languages();
		$this->vars (array(
			"target" => empty($this->tree_dat["url_target"]) ? "" : $this->tree_dat["url_target"],
			"open_nodes" => count($opened_nodes) ? join(",",map("'%s'",$opened_nodes)) : "",
			"tree_id" => $this->tree_id,
			"charset" => $t->get_charset(),
			"separator" => $this->separator,
			"checkbox_data_var" => $this->checkbox_data_var,
		));

		$rv = $this->draw_dhtml_tree_with_buttons ($this->rootnode);

		$root = "";
		if ($this->has_root)
		{
			$this->vars(array(
				"rootname" => $this->tree_dat["root_name"],
				"rooturl" => $this->tree_dat["root_url"],
				"icon_root" => ($this->tree_dat["root_icon"] != "" ) ? $this->tree_dat["root_icon"] : "/automatweb/images/aw_ikoon.gif",
			));
			if ($this->get_branch_func)
			{
				$this->vars(array(
					"get_branch_func" => $this->get_branch_func,
				));
			};
			$root .= $this->parse("HAS_ROOT");
		};

		$this->vars(array(
			"TREE_NODE" => $rv,
			"HAS_ROOT" => $root,
			"persist_state" => $this->has_feature(PERSIST_STATE),
			'only_one_level_opened' => $this->only_one_level_opened,
		));

		return $this->parse();
	}

	// figures out the path from an item to the root of the tree
	private function _get_r_path($id)
	{
		$rpath = array();
		if (!isset($this->itemdata[$id]))
		{
			return $rpath;
		}
		$item = $this->itemdata[$id];
		while(!empty($item))
		{
			if (!isset($item["parent"]))
			{
				$item["parent"] = null;
			}
			$rpath[] = $item["id"];
			$item = in_array($item["parent"],$rpath) ? false : (isset($this->itemdata[$item["parent"]]) ? $this->itemdata[$item["parent"]] : null);
		};
		return $rpath;
	}

	private function draw_dhtml_tree ($parent)
	{
		$data = isset($this->items[$parent]) ? $this->items[$parent] : null;

		if (!is_array($data))
		{
			return "";
		}

		$this->level++;
		$result = "";

		foreach($data as $item)
		{
			$subres = $this->draw_dhtml_tree($item["id"]);
			// subress will be empty string, if draw_dhtml_tree finds no
			// elements under the requested node

			$in_path = in_array($item["id"],$this->r_path);

			if (!empty($item["iconurl"]))
			{
				$iconurl = $item["iconurl"];
			}
			elseif ($in_path)
			{
				// XXX: make it possible to set open/closed icons from the code
				$iconurl = aw_ini_get("baseurl") . "automatweb/images/open_folder.gif";
			}
			else
			{
				$iconurl = aw_ini_get("baseurl") . "automatweb/images/closed_folder.gif";
			}

			if(!isset($item['url']) && isset($item['reload']))
			{
				$item["onClick"] = html::handle_reload($item["reload"]);
				$item["url"] = "javascript:void(0)";
			}

			$name = empty($item["name"]) ? $this->untitled_text : $item["name"];
			if ($item["id"] === $this->selected_item)
			{
				// XXX: Might want to move this into the template
				$name = "<strong>$name</strong>";
				$this->vars(array(
					"selected" => "class=\"nodetext_selected\""
				));
			}
			else
			{
				$this->vars(array(
					"selected" => ""
				));
			}

			$url_target = !isset($item["url_target"]) ? (isset($this->tree_dat["url_target"]) ? $this->tree_dat["url_target"] : null) : $item["url_target"];

			$has_data = "0";
			if($this->has_feature(DATA_IN_PLACE) == 1)
			{
				$has_data = "1";
			}

			$oncl = "";
			if (!empty($item["onClick"]))
			{
				 $oncl="onclick=\"".$item["onClick"]."\"";
			}
			$this->vars(array(
				"name" => $name,
				"id" => $item["id"],
				"has_data" => $has_data,
				"iconurl" => $iconurl,
				"url" => isset($item["url"]) ? $item["url"] : "",
				"onClick" => $oncl,
				// spacer is only used for purely aesthetic reasons - to make
				// source of the page look better
				"spacer" => str_repeat("    ",$this->level),
				"menu_level" => $this->level,
				"target" => $url_target,
				"alt" => isset($item["alt"]) ? $item["alt"] : null
			));

			if (empty($subres))
			{
				// fill them with emptyness
				$this->vars(array(
					"SUB_NODES" => "",
				));

				$tpl = "SINGLE_NODE";
			}
			else
			{
				$this->vars(array(
					"SINGLE_NODE" => $subres,
					"display" => $in_path ? "block" : "none",
					"data_loaded" => $in_path ? "true" : "false",
					"node_image" => $in_path ? aw_ini_get("baseurl") . "automatweb/images/minusnode.gif" : aw_ini_get("baseurl") . "automatweb/images/plusnode.gif",
					"menu_level" => $this->level,
				));
				$tmp = $this->parse("SUB_NODES");
				$this->vars(array(
					"SUB_NODES" => $tmp,
				));

				$tpl = "TREE_NODE";
			};

			$result .= $this->parse($tpl);

		}
		$this->level--;
		return $result;

	}

	private function draw_dhtml_tree_with_checkboxes ($parent)
	{
		$data = isset($this->items[$parent]) ? $this->items[$parent] : null;

		if (!is_array($data))
		{
			return "";
		};

		$this->level++;
		$result = "";

		foreach($data as $item)
		{
			$subres = $this->draw_dhtml_tree_with_checkboxes ($item["id"]);

			if (isset($item["iconurl"]))
			{
				$iconurl = $item["iconurl"];
			}
			elseif (in_array($item["id"],$this->r_path))
			{
				$iconurl = aw_ini_get("baseurl") . "automatweb/images/open_folder.gif";
			}
			else
			{
				$iconurl = aw_ini_get("baseurl") . "automatweb/images/closed_folder.gif";
			};

			$name = empty($item["name"]) ? $this->untitled_text : $item["name"];
			if ($item["id"] == $this->selected_item)
			{
				$name = "<strong>$name</strong>";
			}

			$checkbox_status = "undefined";

			if (isset($item["checkbox"]) and ($item["checkbox"] === 0 or $item["checkbox"] === 1))
			{
				if ( (is_array ($this->checked_nodes)) and (in_array ($item["id"], $this->checked_nodes)) )
				{
					$checkbox_status = "checked";
					array_push ($this->checked_nodes, $item["id"]);
				}
				else
				{
					$checkbox_status = "unchecked";
					$keys = array_keys ($this->checked_nodes, $item["id"]);

					foreach ($keys as $key)
					{
						unset ($this->checked_nodes[$key]);
					}
				}
			}
			else
			{
				if ( (is_array ($this->checked_nodes)) and (in_array ($item["id"], $this->checked_nodes)) )
				{
					$checkbox_status = "checked";
				}
				else
				{
					$checkbox_status = "unchecked";
				}
			}

			$this->vars(array(
				"name" => $name,
				"id" => $item["id"],
				"iconurl" => $iconurl,
				"url" => $item["url"],
				// spacer is only used for purely aesthetic reasons - to make
				// source of the page look better
				"spacer" => str_repeat("    ",$this->level),
				'menu_level' => $this->level,
				"checkbox_status" => $checkbox_status,
			));


			if (empty($subres))
			{
				// fill them with emptyness
				$this->vars(array(
					"SUB_NODES" => "",
				));

				if ($checkbox_status === "undefined")
				{
					$tpl = "SINGLE_NODE";
				}
				else
				{
					$tpl = "SINGLE_NODE_CHECKBOX";
				}
			}
			else
			{
				$this->vars(array(
					"SINGLE_NODE" => $subres,
					"display" => in_array($item["id"],$this->r_path) ? "block" : "none",
					"data_loaded" => in_array($item["id"],$this->r_path) ? "true" : "false",
					"node_image" => in_array($item["id"],$this->r_path) ? aw_ini_get("baseurl") . "automatweb/images/minusnode.gif" : aw_ini_get("baseurl") . "automatweb/images/plusnode.gif",
					'menu_level' => $this->level,
				));
				$tmp = $this->parse("SUB_NODES");
				$this->vars(array(
					"SUB_NODES" => $tmp,
				));

				$tpl = "TREE_NODE";
			}

			$result .= $this->parse($tpl);

		}
		$this->level--;
		return $result;

	}

	private function draw_dhtml_tree_with_buttons ($parent = 0)
	{
		$data = isset($this->items[$parent]) ? $this->items[$parent] : array();

		$this->level++;
		$result = "";

		foreach($data as $item)
		{
			$subres = $this->draw_dhtml_tree_with_buttons ($item["id"]);

			if (isset($item["iconurl"]))
			{
				$iconurl = $item["iconurl"];
			}
			elseif (in_array($item["id"],$this->r_path))
			{
				$iconurl = aw_ini_get("baseurl") . "automatweb/images/open_folder.gif";
			}
			else
			{
				$iconurl = aw_ini_get("baseurl") . "automatweb/images/closed_folder.gif";
			}

			$name = empty($item["name"]) ? $this->untitled_text : $item["name"];
			if ($item["id"] === $this->selected_item)
			{
				$name = "<strong>$name</strong>";
			}

			$checkbox_status = "undefined";

			if ($item["checkbox"] == "button")
			{
				$checkbox_status = "button";
			}

			$this->vars(array(
				"name" => $name,
				"id" => $item["id"],
				"iconurl" => $iconurl,
				"url" => $item["url"],
				// spacer is only used for purely aesthetic reasons - to make
				// source of the page look better
				"spacer" => str_repeat ("    ", $this->level),
				'menu_level' => $this->level,
				"checkbox_status" => $checkbox_status,
			));


			if (empty($subres))
			{
				// fill them with emptyness
				$this->vars(array(
					"SUB_NODES" => "",
				));

				if ($checkbox_status == "button")
				{
					$tpl = "SINGLE_NODE_BUTTON";
				}
				else
				{
					$tpl = "SINGLE_NODE";
				}
			}
			else
			{
				$this->vars(array(
					"SINGLE_NODE" => $subres,
					"display" => in_array($item["id"],$this->r_path) ? "block" : "none",
					"data_loaded" => in_array($item["id"],$this->r_path) ? "true" : "false",
					"node_image" => in_array($item["id"],$this->r_path) ? aw_ini_get("baseurl") . "automatweb/images/minusnode.gif" : aw_ini_get("baseurl") . "automatweb/images/plusnode.gif",
					'menu_level' => $this->level,
				));
				$tmp = $this->parse("SUB_NODES");
				$this->vars(array(
					"SUB_NODES" => $tmp,
				));

				$tpl = "TREE_NODE";
			}

			$result .= $this->parse($tpl);
		}

		$this->level--;
		return $result;
	}

	private function draw_html_tree($parent, &$ml)
	{
		$this->level++;
		$data = array();
		$ids = new aw_array();
		$counts = array();

		// get all menus for this level
		if (is_array($this->items[$parent]))
		{
			$data = $this->items[$parent];
		}

		foreach($data as $row)
		{
			$counts[$row["id"]] = count($this->items[$row["id"]]);
		}

		$num = 0;
		$cnt = count($data);
		foreach($data as $row)
		{
			if ($cnt-1 == $num && $this->level == 1)
			{
				$this->first_level_menu_is_last = true;
			}
			else
			if ($this->level == 1)
			{
				$this->first_level_menu_is_last = false;
			}

			$this->vars(array(
				"link" => $row["url"],
				"name" => $row["name"],
				"section" => $row['id']
			));
			$this->vars($row["data"]);

			$sel = "";
			if ($this->selected_item == $row['id'])
			{
				$sel = "_SEL";
			}
			if ($counts[$row['id']])
			{
				$ms = $this->parse("MENU".$sel);
			}
			else
			{
				$ms = $this->parse("MENU_NOSUBS".$sel);
			}

			if ($this->level > 1)
			{
				$ms .= $this->parse("INFO");
			}

			// if the first level menu on this line is the last in it's level, then the first image must be empty
			if ($this->level == 1)
			{
				$str = "";
			}
			else
			if ($this->first_level_menu_is_last)
			{
				$str = $this->parse("FTV_BLANK");
			}
			else
			{
				$str = $this->parse("FTV_VERTLINE");
			}

			if ($counts[$row['id']])
			{
				$str .= str_repeat($this->parse("FTV_VERTLINE"), max(0,$this->level-2));
				if ($cnt-1 == $num)
				{
					$str.= $this->parse("FTV_PLASTNODE");
				}
				else
				{
					if (isset($this->items[$row['id']]))
					{
						$str.= $this->parse("FTV_MNODE");
					}
					else
					{

						$str.= $this->parse("FTV_PNODE");
					}
				}
			}
			else
			{
				$str .= str_repeat($this->parse("FTV_VERTLINE"), max(0,$this->level-2));
				if ($cnt-1 == $num)
				{
					$str.= $this->parse("FTV_LASTNODE");
				}
				else
				{
					$str.= $this->parse("FTV_NODE");
				}
			}

			$this->vars(array(
				"str" => $str,
				"colspan" => (10-$this->level),
				"ms" => $ms
			));
			$ml[] = $this->parse("FTV_ITEM");

			// now check if this menu is in the oc for the active menu
			// and if so, then recurse to the next level
			if (isset($this->items[$row["id"]]))
			{
				$this->draw_html_tree($row['id'], $ml);
			}
			$num++;
		}
		$this->level--;
	}

	/** Takes an object_tree and returns a treeview
		@attrib name=tree_from_objects param=name api=1

		@param tree_opts required type=array
			Options to pass to the treeview constructor
		@param root_item required type=object
			Object instance that contains the root item
		@param ot required type=object
			Object_tree instance that contains the needed objects
		@param no_urls optional type=bool
			If set, urls for nodes won't be generated
		@param target_url optional type=string
			Url for link of menu items
		@param var required type=string
			Variable name. Links in the tree will be made with aw_url_change_var($var, $item->id(), $url) - the $var variable will contain the active tree item
		@param node_actions optional type=array
			This is for specifying different actions for different classes. ( array( clid => "action_name" ) )
		@param checkbox_class_filter optional type=array
			Array of class id-s, objects of these classes will have checkboxed/buttoned tree nodes. Applicable only when tree type is TREE_DHTML_WITH_CHECKBOXES or TREE_DHTML_WITH_BUTTONS.
		@param no_root_item optional type=bool
			If true, the single root item is not inserted into the tree
		@param item_name_props optional type=array
			Property names by class to be used for tree item visible name. Format: array($clid => "property_name"). Default is object name.
		@param checked_nodes optional type=array
			Tree node id-s that are checked initially, applies when type is TREE_DHTML_WITH_CHECKBOXES.

		@returns
			Treeview object
		@comment

		@examples

	**/
	public static function tree_from_objects($arr)
	{
		$node_actions = null;
		$item_name_props = null;
		$show_num_child = null;
		$add_change_url = null;
		extract($arr);
		$tv = get_instance(CL_TREEVIEW);
		$aw_classes = get_class_picker (array ("field" => "def"));
		$item_name_props = (array) ifset($arr, "item_name_props");

		if (!isset($target_url))
		{
			$target_url = null;
		}

		$class_id = $arr["root_item"]->class_id ();
		$class_name = strtolower (substr ($aw_classes[$class_id], 3));

		if ( (is_array ($node_actions)) and !empty($node_actions[$class_id]))
		{
			$tree_opts["root_url"] = $tv->mk_my_orb ($node_actions[$class_id], array(
				"id" => $o->id (),
				"return_url" => get_ru(),
			), $class_name);
		}
		else
		{
			$tree_opts["root_url"] = aw_url_change_var ($var, $arr["root_item"]->id(), $target_url);
		}

		$class_id = $root_item->class_id ();
		$class_name = strtolower (substr ($aw_classes[$class_id], 3));

		if ( (is_array ($node_actions)) and !empty($node_actions[$class_id]) )
		{
			$url = $tv->mk_my_orb ($node_actions[$class_id], array(
				"id" => $o->id (),
				"return_url" => get_ru(),
			), $class_name);
			$use_reload = false;
		}
		else
		{
			if(!empty($reload))
			{
				$reload["params"][$var] = $root_item->id();
				$use_reload = true;
			}
			else
			{
				$url = aw_url_change_var (array(
					$var => $root_item->id(),
					"ft_page" => NULL,		// The tree branch should always link to the first page of the table. -kaarel
				), false, $target_url);
				$use_reload = false;
			}
		}

		$tv->start_tree($tree_opts);
		if (!ifset($arr, "no_root_item"))
		{
			if (array_key_exists($root_item->class_id(), $item_name_props))
			{
				if ($root_item->is_property($item_name_props[$root_item->class_id()]))
				{
					$nm = parse_obj_name($root_item->prop($item_name_props[$root_item->class_id()]));
				}
			}
			else
			{
				$nm = parse_obj_name($root_item->trans_get_val("name"));
			}

			if ($var && ifset($_GET, $var) == "")
			{
//				$nm = "<b>".$nm."</b>";
				$tv->set_selected_item($root_item->id());
			}

			$item = array(
				"name" => $nm,
				"id" => $root_item->id(),
			);

			if($use_reload)
			{
				$item["reload"] = $reload;
			}
			else
			{
				$item["url"] = $url;
			}
			$tv->add_item(0, $item);
		}

		$ol = $ot->to_list();
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if (array_key_exists($o->class_id(), $item_name_props))
			{
				if ($o->is_property($item_name_props[$o->class_id()]))
				{
					$oname = parse_obj_name($o->prop($item_name_props[$o->class_id()]));
				}
			}
			else
			{
				$oname = parse_obj_name($o->trans_get_val("name"));
			}

			$oid = $o->id();
			$class_id = $o->class_id();

			if ($var && ifset($_GET, $var) == $oid)
			{
//				$oname = "<b>".$oname."</b>";
				$tv->set_selected_item($oid);
			}

			if (
				($tv->tree_type == TREE_DHTML_WITH_CHECKBOXES) and
				(
					!isset($arr["checkbox_class_filter"]) or
					is_array ($arr["checkbox_class_filter"]) and in_array ($class_id, $arr["checkbox_class_filter"])
				))
			{
				if (isset($checked_nodes) and is_array ($checked_nodes) and in_array ($oid, $checked_nodes))
				{
					$checkbox_status = 1;
				}
				else
				{
					$checkbox_status = 0;
				}
			}
			elseif ( ($tv->tree_type == TREE_DHTML_WITH_BUTTONS) and is_array ($arr["checkbox_class_filter"]) and in_array ($class_id, $arr["checkbox_class_filter"]) )
			{
				$checkbox_status = "button";
			}
			else
			{
				$checkbox_status = "undefined";
			}

			$class_name = strtolower (substr ($aw_classes[$class_id], 3));

			if ( (is_array ($node_actions)) and !empty($node_actions[$class_id]) )
			{
				$url = html::get_change_url($oid, array("return_url" => get_ru()));
				$use_reload = false;
			}
			else
			{
				if(!empty($reload))
				{
					$reload["params"][$var] = $oid;
					$use_reload = true;
				}
				else
				{
					$url = aw_url_change_var (array(
						$var => $oid,
						"ft_page" => NULL,		// The tree branch should always link to the first page of the table. -kaarel
					), false, $target_url);
					$use_reload = false;
				}
			}

			$parent = $o->parent();
			if (ifset($arr, "no_root_item") && $parent == $root_item->id())
			{
				$parent = 0;
			}
			if (!ifset($arr, "icon"))
			{
				$icon = (($class_id == CL_MENU) ? NULL : icons::get_icon_url($class_id,""));
			}
			else
			{
				$icon = $arr["icon"];
			}
			$num_child = 0;
			$item = array(
				"name" => $oname.($show_num_child ? " (".$num_child.")" : "").($add_change_url ? html::obj_change_url($oid, t(" (M)")) : ""),
				"id" => $oid,
				"iconurl" => $icon,
				"checkbox_status" => $checkbox_status,
			);
			if($use_reload)
			{
				$item["reload"] = $reload;
			}
			else
			{
				$item["url"] = $url;
			}
			$tv->add_item($parent, $item);
		}
		return $tv;
	}

	/** Sets that only one tree depth is opened at a time
		@attrib params=pos api=1
		@param value required type=int
			If set to 1, then only one tree depth is opened at a time
	**/
	public function set_only_one_level_opened($value)
	{
		$this->only_one_level_opened = $value;
	}

	/** Sets the rootnode for the tree
		@attrib api=1 params=pos
	**/
	public function set_rootnode($rn)
	{
		$this->rootnode = $rn;
	}
}
