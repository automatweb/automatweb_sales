<?php

namespace automatweb;

class translator extends  core
{
	function translator()
	{
		$this->init("");
	}

	function init_vcl_property($arr)
	{
		$prop = &$arr["property"];

		aw_global_set("output_charset","UTF-8");

		$o = $arr["obj_inst"]->get_original();
		$t_conns = $o->connections_from(array(
			"type" => RELTYPE_ORIGINAL,
		));
		obj_set_opt("no_auto_translation",1);
		if (0 < sizeof($t_conns))
		{
			$conn = reset($t_conns);
			$o = $conn->to();
		};
		obj_set_opt("no_auto_translation",0);
		$this->obj = $o;

		// siin on vaja originaal sisse lugeda


		$i = $this->obj->instance();

		$rv = array();
		$l = new languages();
                $langinfo = $l->get_list(array(
                        "key" => "acceptlang",
                        "all_data" => true,
                ));

		// XXX: be more intelligent and retrieve all properties with trans=1
                $tprop = $prop["props"];
		if (!is_array($tprop))
		{
			$tprop = array($tprop);
		};

                $props = $i->get_property_group(array());

                $translatable = array();
                foreach($props as $key => $val)
                {
                        if (in_array($key,$tprop))
                        {
                                $translatable[$key] = $val;
                        };
                };

                $prefix = $arr["property"]["name"];


                $act_lang = $o->lang();

                $conns = $o->connections_from(array(
                        "type" => RELTYPE_TRANSLATION,
                ));

                $translated = array();
                $translated[$act_lang] = $o;

                obj_set_opt("no_auto_translation", 1);

                foreach($conns as $conn)
                {
                        $to = $conn->to();
                        $translated[$to->lang()] = $to;
                };

                foreach($langinfo as $langdata)
                {
                        $lid = $langdata["id"];
                        $l_accept = $langdata["acceptlang"];
			$current_charset = $langdata["charset"];
                        $rv["cap_$lid"] = array(
                                "name" => "cap_$lid",
                                "type" => "text",
                                "subtitle" => 1,
                                "caption" => $langdata["name"],
                        );

                        $current_translation = $translated[$l_accept];

                        foreach($translatable as $key => $val)
                        {
                                $elname = $val["name"];
				$value = ($current_translation) ? $current_translation->prop($elname) : "";
				if (!empty($current_charset))
				{
					//print "converting $value from $current_charset to UTF-8<br>";
					$value = iconv($current_charset,"UTF-8",$value);
					//print "result = ";
					//print $value;
				};
                                $rv["${prefix}_${lid}_" . $elname] = array(
                                        "name" => "${prefix}[$l_accept][" . $elname . "]",
                                        "type" => $val["type"],
                                        "caption" => $val["caption"],
                                        "cols" => $val["cols"],
                                        "rows" => $val["rows"],
                                        "value" => $value,
                                );
                        };


			// new translations should be default be active
			$rv["act_$lid"] = array(
				"name" => "trans[" . $l_accept . "][status]",
				"type" => "checkbox",
				"ch_value" => STAT_ACTIVE,
				"value" => ($current_translation) ? $current_translation->status() == STAT_ACTIVE : 1,
				"caption" => t("Aktiivne"),
			);
                };

                obj_set_opt("no_auto_translation", 0);
		return $rv;
	}

	function process_vcl_property($arr)
	{
		$eldata = $arr["prop"]["value"];
                $o = $arr["obj_inst"];
		$orig = $o->get_original();

		$t_conns = $orig->connections_from(array(
			"type" => RELTYPE_ORIGINAL,
		));
		obj_set_opt("no_auto_translation",1);
		if (0 < sizeof($t_conns))
		{
			$conn = reset($t_conns);
			$orig = $conn->to();
		};
		//obj_set_opt("no_auto_translation",0);

		$prnt = new object($orig->parent());

		$brothers = new object_list(array(
			"brother_of" => $orig->id(),
		));

		// figure out under the parents for translations for the the current object
		$o = $orig;

		$brotlist = array();
		$parents = array();

		// then, how the heck do

		foreach($brothers->arr() as $brot)
		{
			$prt = new object($brot->parent());
			$_trans = $this->_get_translations_for($prt->id());

			/*print "prid = " . $prt->id() . "<br>";
			print "translations ";
			arr($_trans);
			*/

			$brotlist[$brot->parent()] = 1;

			// aga üks neist on originaal. Mida ma sellega peale hakkan?
			if (sizeof($_trans) > 0)
			{
				$parents[$brot->parent()] = $_trans;
			};
		};

		// brotlist contains all the id-s of all brothers of this object
                obj_set_opt("no_auto_translation", 1);

		//arr($brotlist);
		//exit;

		//arr($brotlist);


		// now, lets figure out the id-s of project translations, so I can create translated
		// objects under correct parents
		$tr_conns = $prnt->connections_from(array(
			"type" => RELTYPE_TRANSLATION,
		));

		// 1. create a list of all brothers this object has
		//	I need the real id-s of the parents, which means working
		//	around the storage

		// 2. create a list of all translations this object has
		// 3. create new translation objects if missing
		// 4. create brothers from those translation objects where needed


		// tr_parents contains the id-s of all parents, sorted by language
		$tr_parents = array();

		foreach($tr_conns as $tr_conn)
		{
                        $to = $tr_conn->to();
			$tr_parents[$to->lang()] = $to->id();
		};

                $tr_conns = $o->connections_from(array(
                        "type" => RELTYPE_TRANSLATION,
                ));

		// translated contains the id-s of all already existing translations of current object
                $translated = array();

                foreach($tr_conns as $tr_conn)
                {
                        $to = $tr_conn->to();
                        $translated[$to->lang()] = $to;
                };

		/*
		print "<h1>";
		arr($tr_parents);
		print "</h1>";
		print "<h1>";
		arr($translated);
		print "</h1>";
		arr($tr_parents);
		arr($translated);
		*/

		obj_set_opt("no_auto_translation", 1);

                $act_lang = $o->lang();
                $o->set_flag(OBJ_HAS_TRANSLATION,OBJ_HAS_TRANSLATION);

		//arr($eldata);

		$l = new languages();
                $langinfo = $l->get_list(array(
                        "key" => "acceptlang",
                        "all_data" => true,
                ));

		// oot, see asi ei tõlgi ju ometi koopiaid ka ära? oh fuck küll, miks see nii raske peab olema? :(

		foreach($eldata as $lang => $lang_data)
		{
			$curr_lang = $langinfo[$lang];
                        if ($lang == $act_lang)
                        {
                                foreach($lang_data as $prop_key => $prop_val)
                                {
					if (!empty($curr_lang["charset"]))
					{
						//print "converting $prop_val to " . $curr_lang["charset"] . "<br>";
						$prop_val = iconv("UTF-8",$curr_lang["charset"] . "//TRANSLIT",$prop_val);
						//print "result = ";
						//print $prop_val;
					};
                                       	$o->set_prop($prop_key,$prop_val);

                                };
                        }
                        else
                        {
                                if (!$translated[$lang])
                                {
					$new = true;
                                        $clone = new object($o->properties());
					if ($tr_parents[$lang])
					{
						$clone->set_parent($tr_parents[$lang]);
					}
					else
					if ($translated[$lang])
					{
						$clone->set_parent($translated[$lang]->prop("to"));
					}
                                }
                                else
                                {
                                        $clone = new object($translated[$lang]);
					$new = false;
                                };

                                $fields_with_values = 0;

                                foreach($lang_data as $prop_key => $prop_val)
                                {
					// okey then .. this takes care of'
                                        if ($prop_val)
                                        {
                                                $fields_with_values++;
                                        };
                                        //print "setting $prop_key to $prop_val on ".$clone->id()."<br>";

					// vat see on see koht .. kus mul on vaja teostada konvertimine. Selleks aga on mul
					// vaja teada parajasti kehtiva keele charsetti
					if (!empty($curr_lang["charset"]))
					{
						//print "converting $prop_val to " . $curr_lang["charset"] . "<br>";
						$prop_val = iconv("UTF-8",$curr_lang["charset"] . "//TRANSLIT",$prop_val);
						//print "result = ";
						//print "<br>";
						//print $prop_val;
					};
                                        $clone->set_prop($prop_key,$prop_val);
                                };

                                // ignore empty data
                                if (0 == $fields_with_values)
                                {
                                        continue;
                                };

			        if ($translated[$lang])
                                {
                                        $clone->save();
                                }
                                else
                                {
                                        $clone->set_lang($lang);

                                        $clone->set_flag(OBJ_HAS_TRANSLATION,OBJ_HAS_TRANSLATION);
                                        $clone->save_new();

                                        $o->connect(array(
                                                "to" => $clone->id(),
                                                "reltype" => RELTYPE_TRANSLATION,
                                        ));

                                        $clone->connect(array(
                                                "to" => $o->id(),
                                                "reltype" => RELTYPE_ORIGINAL,
                                        ));
                                };

				if ($new)
				{
					foreach($brotlist as $brot => $savi)
					{
						// so I get the real object
						//print "bx = $brot<br>";
						$bof = new object($brot);
						$create_parent = $bof->id();
						if ($parents[$brot][$lang])
						{
							$create_parent = $parents[$brot][$lang];
							/*arr($lang);
							arr($parents[$brot]);
							*/
						};
						//print "createing under $create_parent<br>";
						//print "creating brother under " . $bof->id() . "<br>";
						$clid = $clone->create_brother($create_parent);
						$clone_obj = new object($clid);
						//print "god id" . $clone_obj->id() . "<br>";
						$clone_obj->set_lang($lang);
						$clone_obj->save();
						//*/

					};
				};
                        };
                };

		$o->save();

	}

	function _get_translations_for($id)
	{
		$obj = new object($id);
                obj_set_opt("no_auto_translation", 1);

		$tr_conns = $obj->connections_from(array(
			"type" => RELTYPE_TRANSLATION,
		));

		$rv = array();

		foreach($tr_conns as $tr_conn)
		{
			$tr_obj = $tr_conn->to();
			$rv[$tr_obj->lang()] = $tr_obj->id();
		};

		obj_set_opt("no_auto_translation",0);

		return $rv;
	}
};
?>
