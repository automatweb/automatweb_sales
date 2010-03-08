<?php

/*
A special toolbar for object editing views
Contains save and save and close buttons by default
 */
class toolbar_standard_obj extends toolbar
{
	public function __construct($args = array())
	{
		$r = parent::__construct($args);
		$this->add_save_button();
		$this->add_save_close_button();
		return $r;
	}
}

/*
A special toolbar for application object managing views
Contains save, new, delete, cut, copy and paste buttons by default
 */
class toolbar_standard_app extends toolbar
{
	public function __construct($args = array())
	{
		$r = parent::__construct($args);
		$this->add_new_button();
		$this->add_save_button();
		$this->add_delete_button();
		$this->add_cut_button();
		$this->add_copy_button();
		$this->add_paste_button();
		return $r;
	}
}

?>
