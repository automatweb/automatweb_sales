<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/mailinglist/ml_list_conf.aw,v 1.3 2007/11/23 10:58:51 markop Exp $
// ml_list_conf.aw - List configuration
// right now this is not used anywhere, but maybe we need something like this in the future,
// so I'm leaving this in the tree -- duke

/*
@classinfo maintainer=markop
	@default table=objects
	@default field=meta
	@default method=serialize
	@default group=general
	
	@property folders type=select multiple=1 size=20
	@caption Root kataloogid

*/

class ml_list_conf extends class_base
{
	const AW_CLID = 133;

	function ml_list_conf()
	{
		$this->init(array(
			"clid" => CL_ML_LIST_CONF,
		));
	}

	function get_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "folders":
				$data["options"] = $this->get_menu_list();
				break;


		}
		return $retval;
	}

	function set_property($args = array())
	{
                $data = &$args["prop"];
                $retval = PROP_OK;
                switch($data["name"])
                {
		};
		return $retval;
	}
};
?>
