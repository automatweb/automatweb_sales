<?php
// $Header: /home/cvs/automatweb_dev/classes/plugins/plugin_loader.aw,v 1.3 2008/01/31 13:55:03 kristo Exp $
// base for all the plugins
// provides methods for loading different plugins and retrieving data from them
/*
@classinfo  maintainer=kristo
*/
class plugin_loader extends aw_template
{
	function plugin_loader()
	{
		$this->init("");
	}

	////
	// !Tries to load plugins by a category
	// category(string) - category (name of the directory)
	// plugins(array) - list of plugins that should be loaded
	// method(string) - name of the method to invoke for each plugin
	// args(array) - args that will be passed to -method-
	function load_by_category($args = array())
	{
		$plugin_path = "plugins/" . $args["category"];
		$method = $args["method"];
		$plugindata = array();
		foreach($args["plugins"] as $plg_name)
                {
			$fqpath = aw_ini_get("basedir") . "/classes/${plugin_path}/${plg_name}." . aw_ini_get("ext");
			if (file_exists($fqpath))
			{
				$plg = get_instance("$plugin_path/$plg_name");
				if (method_exists($plg,$method))
				{
					$plugindata[$plg_name] = $plg->$method($args["args"][$plg_name]);
				};
			};
		};
		return $plugindata;
	}
}
?>
