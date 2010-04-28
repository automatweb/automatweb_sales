<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_OFFER_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_crm_offer_type master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer_type
@default group=general

	@property offer_template type=select table=aw_crm_offer_type field=order_type 
	@caption Pakkumise kujundusp&otilde&hi

	@property warehouse type=relpicker table=aw_crm_offer_type datatype=int reltype=RELTYPE_WAREHOUSE field=warehouse
	@caption Ladu

	@property products type=table no_caption=1
	@caption Pakkumises sisalduvad tootekategooriad/kaustad

- pakkumises sisalduvad tootekategooriad/kaustad, mis v6imaldab defineerida, millistest kaustadest tooteid yldse otsitakse pakkumise koostamisel

	@property obligatory_products type=table no_caption=1
	@caption Kohustuslikud tootekategooriad ja nende j&auml;rjekord

- kohustuslikud tootekategooriad ja nende j2rjekord. Saan valida, millised tootekategooriad kuvatakse pakkumise vormi koos tekstiv2ljadega (autocomplete), mis pakuvad antud kategooria tooteid.

	@property free_products type=table no_caption=1
	@caption Vabad tootekategooriad

- vabad tootekategooriad, ehk kategooriad, millest saan j2rjest autocomplete abil tooteid lisada ja iga lisamise j2rel tekib uus rida, uue toote lisamiseks. Toodetele saab m22rata ka koguse, kuvatakse toote vaikimisi hind (saab muuta) ja summa (arvutatakse)

	@property resource_manager type=relpicker table=aw_crm_offer datatype=int reltype=RELTYPE_RESOURCE_MANAGER field=resource_manager
	@caption Ressursihalduskeskkond

- ressursihalduskeskkond, mille ressursse saab paigutada pakkumisse (nii kogus tundides, kui tunnihind on sisestatavad, kuid summa arvutatakse alguses automaatselt ressursi juures oleva tunnihinna alusel)

	@property uservars type=table no_caption=1
	@caption Hulk vabalt defineeritavaid tekstiv&auml;lju

- hulk vabalt defineeritavaid tekstiv2lju, tekstialasid, valikv2lju, millele saab lisaks ehitada v6imaluse, et need on tekstikastid, kuid pakutakse sama muutuja kohal autocomplet'ina. Seda, kas on vaja sellist asja, 8eldakse Pakkumise liigis.


@reltype RELTYPE_WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption File

@reltype RELTYPE_RESOURCE_MANAGER value=2 clid=
@caption Ressursihalduskeskkond

*/

class crm_offer_type extends class_base
{
	const AW_CLID = 1480;

	function crm_offer_type()
	{
		$this->init(array(
			"tpldir" => "crm/crm_offer_type",
			"clid" => CL_CRM_OFFER_TYPE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_offer_type(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
