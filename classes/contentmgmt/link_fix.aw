<?php
/*
@classinfo maintainer=robert
*/
class link_fix extends _int_object
{
	private $si;

	function prop($k)
	{
		$rv = parent::prop($k);
		if ($k == "url" && $this->meta("linked_obj") && $GLOBALS["object_loader"]->can("view", $this->meta("linked_obj")))
		{
			if (!is_object($this->si))
			{
				$this->si = get_instance("contentmgmt/site_show");
			}
			$rv = $this->si->make_menu_link(obj($this->meta("linked_obj")));
		}
		return $rv;
	}

	function set_prop($var, $val)
	{
		if($var == "url")
		{
			$this->url = $url;
		}
		return parent::set_prop($var, $val);
	}
}
?>
