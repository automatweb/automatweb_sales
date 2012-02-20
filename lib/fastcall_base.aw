<?php
// fastcall.aw - this gets loaded instead of aw base classes if we are doing an orb fastcall
// so that initialization would be quick and that no template and database functionality will be present

class aw_template
{
	function init()
	{
		// we have to do at least this, or classes that use $this->cfg
		// (like menuedit->right_frame) wont work
		aw_config_init_class($this);
	}

	function vars()
	{
	}

	function lc_load()
	{
	}
}

class core
{
	function db_init()
	{
	}
}

class class_base extends core
{
	function init()
	{
		aw_config_init_class($this);
	}
}
