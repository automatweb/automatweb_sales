<?php
/*
@classinfo  maintainer=kristo
*/

// package manager server

class packagemgr_server extends class_base
{
	function packagemgr()
	{
		$this->init("packagemgr");
	}

	/** shows the user a list 
		
		@attrib name=list params=name default="1"
		
		
		@returns
		
		
		@comment

	**/
	function orb_list($arr)
	{
		extract($arr);

		$tb = get_instance("vcl/toolbar");
		$tb->add_button(array(
			'name' => 'new',
			'tooltip' => t('Lisa uus'),
			'url' => $this->mk_my_orb("new_package"),
			'img' => 'new.gif'
		));

		$t = new aw_table(array("layout" => "generic"));
		$t->define_field(array(
			"caption" => t("ID"),
			"name" => "id",
			"sortable" => 1
		));
		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name",
			"sortable" => 1
		));
		$t->define_field(array(
			"caption" => t("Viimane versioon"),
			"name" => "latest_version",
			"sortable" => 1
		));
		$t->define_field(array(
			"caption" => t("Muuda"),
			"name" => "change",
		));

		$this->db_query("
			SELECT 
				pk_list.name AS name,
				pk_list.id AS id

			FROM 
				pk_list
				LEFT JOIN pk_revisions ON pk_revisions.pk_id = pk_list.id
		");
		while ($row = $this->db_next())
		{
			$row["change"] = html::href(array(
				"url" => $this->mk_my_orb("change", array("id" => $row["id"])),
				"caption" => t("Muuda")
			));
			$t->define_data($row);
		}

		$t->set_default_sortby("name");
		$t->sort_by();

		return $tb->get_toolbar().$t->draw();
	}

	/**  
		
		@attrib name=new_package params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function new_package($arr)
	{
		extract($arr);
		$this->read_template("new.tpl");

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_new")
		));
		return $this->parse();
	}
}
?>
