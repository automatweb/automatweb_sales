<?php

class link_fix extends _int_object
{
	private $si;
	private $url = "";

	public function awobj_get_url()
	{
		if ($this->meta("linked_obj") && object_loader::can("view", $this->meta("linked_obj")))
		{
			if (!is_object($this->si))
			{
				$this->si = new site_show();
			}
			$rv = $this->si->make_menu_link(obj($this->meta("linked_obj")));
		}
		else
		{
			$rv = $this->prop("url");
		}

		return $rv;
	}

	public function awobj_set_url($value)
	{
		$this->url = $value;
		return $this->set_prop("url", $value);
	}
}
