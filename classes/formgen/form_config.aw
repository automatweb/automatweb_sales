<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_config.aw,v 1.5 2008/01/31 13:54:33 kristo Exp $
// form_config.aw - FormGen configuration
/*
@classinfo  maintainer=kristo
*/

classload("formgen/form_base");
class form_config extends form_base
{
	function form_config()
	{
		$this->form_base();
		$this->tpl_init("forms/configure");
	}

	/**  
		
		@attrib name=config params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function config()
	{
		$this->read_template("config.tpl");

		$co = get_instance("config");
		$this->mk_path(0,"FormGen configuration");
		$_typs = $co->get_simple_config("form::element_types");
		$_styps = $co->get_simple_config("form::element_subtypes");
		$typs = aw_unserialize($_typs);
		$styps = aw_unserialize($_styps);

		$fo = get_instance("formgen/form_element");
		$atyps = $fo->get_all_types();
		$astyps = $fo->get_all_subtypes();

		if (!is_array($typs))
		{
			$typs = $atyps;
		}

		if (!is_array($styps))
		{
			$styps = $astyps;
		}

		foreach($atyps as $type => $typename)
		{
			$this->vars(array(
				"type" => $type,
				"type_name" => $typename,
				"type_check" => checked($typs[$type] != "")
			));
			
			$stp = "";
			// some element types don't have subtypes - duke
			if (is_array($astyps[$type]))
			{
				foreach($astyps[$type] as $st => $stname)
				{
					if ($st != "")
					{
						$this->vars(array(
							"subtype" => $st,
							"subtype_name" => $stname,
							"subtype_check" => checked($styps[$type][$st] != "")
						));
						$stp.=$this->parse("SUBTYPE");
					}
				}
			};
			$this->vars(array(
				"SUBTYPE" => $stp
			));
			$tp.=$this->parse("TYPE");
		}
		$this->vars(array(
			"TYPE" => $tp,
			"reforb" => $this->mk_reforb("submit", array())
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit($arr)
	{
		extract($arr);
		$co = get_instance("config");

		$fo = get_instance("formgen/form_element");
		$all_types = $fo->get_all_types();
		$all_subtypes = $fo->get_all_subtypes();

		$ts = array();
		$sts = array();
		if (is_array($types))
		{
			foreach($types as $typ => $one)
			{
				if ($one == 1)
				{
					$ts[$typ] = $all_types[$typ];
					$sts[$typ] = array("" => "");
					if (is_array($subtypes[$typ]))
					{
						foreach($subtypes[$typ] as $st => $one)
						{
							if ($one == 1)
							{
								$sts[$typ][$st] = $all_subtypes[$typ][$st];
							}
						}
					}
				}
			}
		}

		$types = aw_serialize($ts);
		$subtypes = aw_serialize($sts);

		$types = $this->quote($types);
		$subtypes = $this->quote($subtypes);

		$co->set_simple_config("form::element_types",$types);
		$co->set_simple_config("form::element_subtypes",$subtypes);

		return $this->mk_my_orb("config", array());
	}
}

?>
