<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/sql_filter.aw,v 1.2 2008/01/31 13:54:34 kristo Exp $
/*
@classinfo  maintainer=kristo
*/
class sql_filter extends aw_template 
{
	function sql_filter()
	{
		$this->tables=array();

		$this->joinnames=array("and"=>"ja","or"=>"vıi");
		$this->init("automatweb");
	}



	// filtrite stuff algab siit
	// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

	function set_data($arr)
	{
		$this->tables=$arr;

		//Build reverse arrays for finding fake names from real names
		//And fill in missing "real" keys, if a field/table has no fake name
		$this->reverse=array();
		if (is_array($this->tables))
		foreach ($this->tables as $tfakename => $tdata)
		{
			if (!isset($tdata["real"]))
			{
				$tdata["real"]=$this->tables[$tfakename]["real"]=$tfakename;
			};
			$a=array();
			// reverse fields to real => fake too

			if (is_array($tdata["fields"]))
			foreach($tdata["fields"] as $ffakename => $fdata)
			{
				if (!isset($fdata["real"]))
				{
					$fdata["real"]=$this->tables[$tfakename]["fields"][$ffakename]["real"]=$ffakename;
				};

				$a["fields"][$fdata["real"]]["ref"]=&$this->tables[$tfakename]["fields"][$ffakename];
				$a["fields"][$fdata["real"]]["fake"]=$ffakename;
			};

			$a["fake"]=$tfakename;
			$this->reverse[$tdata["real"]]=$a;
		};
	}


	function set_filter($arr)
	{
		$this->filter=$arr;
	}

	function get_filter()
	{
		return $this->filter;
	}
	
	////
	//! N‰itab filtri muutmise akent
	function do_filter_edit($arr)
	{
		extract($arr);
		if (isset($filter))
		{
			$this->set_filter($filter);
		};
		if ($GLOBALS["shit"])
		{
			echo("<textarea cols=80 rows=40>");
			print_r($this->filter);
			echo("</textarea>");
		};
		
		$this->read_template("sql_filter.tpl");

		$this->vars(array(
			"header" => $header
		));
		$optionvarr=array();//it stores a javascript array for every tablefield
		$optionkarr=array();
		$fieldarr=array();//Contains all fields for field selection box
		$fieldtypearr=array();//Contains all field types

		if (is_array($this->tables))
		foreach($this->tables as $tfakename => $tdata)// for each table do
		{
			if (is_array($tdata) && is_array($tdata["fields"])) // for each field in table do
			foreach($tdata["fields"] as $ffakename => $fdata)
			{
				if (is_array($fdata["select"])) // if this field has predefined options to choose from...
				{
					$sisuv="'".join("','",array_values($fdata["select"]))."'";
					$sisuk="'".join("','",array_keys($fdata["select"]))."'";
				}
				else 
				{
					$sisuv=$sisuk="";
				};
				$optionvarr[]="new Array($sisuv)";
				$optionkarr[]="new Array($sisuk)";
				$fieldarr[$tdata["real"].".".$fdata["real"]]="$tfakename.$ffakename";
				$fieldtypearr[]=$fdata["type"];
			};
		};

		load_vcl("date_edit");
		$date_edit = new date_edit(time());
		$date_edit->configure(array(
			"day" => "",
			"month" => "",
			"year" => "",
			"hour" => "",
			"minute" => "",
			"classid" => "small_button",
			));

		$tingimused="";
		for ($i=0; $i < (int)$this->filter["nump"]; $i++)
		{
			$part=$this->filter["p$i"];

			//extract realtable.realfield to faketable.fakefield
			list($realtable,$realfield)=explode(".",$part["field"]);
			
			$faketable=$this->reverse[$realtable]["fake"];
			$fakefield=$this->reverse[$realtable]["fields"][$realfield]["fake"];
			//echo("explode $realtable,$realfield =>$faketable.$fakefield<br />");//dbg
			
			$fakeval=$filter["p$i"]["val"];
			if (is_array($this->tables[$faketable]["fields"][$fakefield]["select"]) &&
				isset($this->tables[$faketable]["fields"][$fakefield]["select"][$fakeval]))
			{
				$fakeval=$this->tables[$faketable]["fields"][$fakefield]["select"][$fakeval];
			};
			

			if ($is_change_part && ($i==$change_part))
			{
				//mida on vaja saada siis yhe filtri osa editimiseks?
				//fake v2ljanime mis on siis default valitud selectis
				//fake v22rtust, data tuleb kuidagi konvertida
				//v6rdlust
				
				$change_p_val=$fakeval;
				// konverteeri date tyyp, et n2itaks valitud aega
				//echo("type=".$this->tables[$faketable]["fields"][$fakefield]["type"]);
				if ($this->tables[$faketable]["fields"][$fakefield]["type"] == 2)
				{
					$change_p_val="_date";
					$selecteddate=$fakeval;
				};
				//echo("change_p_val=$change_p_val<br />");//dbg
				$rclass="ftitle2";
				
			} else
			{
				$rclass="title";
			};
			//echo("join=".$filter["p$i"]["join"]."<br />");//dbg

			$this->vars(array(
				"tid" => $i,
				"ignchecked" => $part["ign"]?"checked":"",
				"rclass" => $rclass,
				"join" => $this->joinnames[$filter["p$i"]["join"]],
				"modifylink" => $this->mk_my_orb($reforb_edit_func,array_merge($reforb_arr,array("is_change_part"=>1,"change_part"=>$i)),$reforb_class),
				"sql" => "$faketable.$fakefield"." ".$filter["p$i"]["op"]." '$fakeval'"
				));
			$tingimused.=$this->parse("tingimused");
		};

		if (!$reforb)
		{
			$reforb=$this->mk_reforb("submit_filter_edit", array("id" => $id,"selt"=>" "));
		};

		// Vot nii, siin vaata kas on tegemist uue filtri osa lisamisega vıi mıne vana muutmisega hoopiski

		if ($is_change_part)
		{
			$this->vars(array("change_part" => $change_part));
			$apart=$this->filter["p$change_part"];
			//print_r($apart);//dbg
			$lisa=$this->parse("MUUDA");
			
			$c_opand=$apart["join"]=="and"?" checked ":"";
			$c_opor=$apart["join"]=="or"?" checked ":"";
			$buttoncaption="Salvesta";
			$change_p_op=$apart["op"];
			//echo("change_p_op=$change_p_op<br />");//dbg
			//echo("<pre>");print_r($fieldarr);echo("</pre>");//dbg
			$change_p_field=$apart["field"];

			// otsi valitud v2lja indeks
			$fieldarr_vals=array_keys($fieldarr);
			
			$change_p_fieldnum=0;
			for ($j;$j<sizeof($fieldarr);$j++)
			{
				if ($fieldarr_vals[$j]==$change_p_field)
				{
					$change_p_fieldnum=$j;
					break;
				};
			};

			$addpars="";
		} 
		else
		{
			$addpars=$this->parse("addpars");
			$lisa=$this->parse("LISA");
			$c_opand=" checked ";
			$c_opor="";
			$buttoncaption="Lisa";
			$change_p_op=$change_p_val=$change_p_field="";
			$change_p_fieldnum="0";
			$selecteddate=time();
		};

		$ol = new object_list(array(
			"class_id" => CL_FORM_OUTPUT,
			"site_id" => array(),
			"lang_id" => array()
		));
		$lll = array("" => "") + $ol->names();

		$this->vars(array(
			"fieldlist" => $this->picker($change_p_field,$fieldarr),
			"sql" => $this->filter_to_sql(array("noeval"=>1,"fake"=>1)),
			"ftypes" => join(",",$fieldtypearr),
			"foptions" => join(",",$optionvarr),
			"foptionsk" => join(",",$optionkarr),
			"tingimused" => $tingimused,
			"selectedval" =>$change_p_val,
			"selectedexpr" =>$change_p_op,
			"change_p_fieldnum" =>$change_p_fieldnum,
			"LISA" => $lisa,
			"addpars" => $addpars,
			"MUUDA" => "",
			"c_opand"=>$c_opand,
			"c_opor" => $c_opor,
			"buttoncaption"=>$buttoncaption,
			"name" => $this->filter["name"],
			"dedit" => $date_edit->gen_edit_form("dateval",$selecteddate),
			"reforb" => $arr["reforb"] ? $arr["reforb"] : $this->mk_my_orb($reforb_func,$reforb_arr,$reforb_class),
			"ops" => $this->picker($this->filter["filter_op"], $lll)
		));
		
		
		return $this->parse();
	}



	function do_submit_filter_edit($arr)
	{
		extract($arr);
		if (isset($filter))
		{
			$this->set_filter($filter);
		};
		//Votnii, siin tuleb seda j‰rjekorra muutust teha
		
		if (is_array($j2rjekord))
		{
			$newarr=array();
			foreach ($j2rjekord as $vanajrk => $uusjrk)
			{
				$newarr["p$uusjrk"]=$this->filter["p$vanajrk"];
			};
			$this->filter=array_merge($this->filter,$newarr);
		};

		for ($i=0; $i<$this->filter["nump"] ;$i++)
		{
			if ($ign[$i])
			{
				$this->filter["p$i"]["ign"]=1;
			} 
			else
			{
				$this->filter["p$i"]["ign"]="";
			};
		};
		$this->filter["name"]=$name;
		$this->filter["filter_op"]=$filter_op;
		//echo("filter=<pre>");print_r($this->filter);echo("</pre>");//dbg
	
		return $this->filter;
	}

	//liigutab filtri tingimust yles/alla
	function do_filter_edit_move($arr)
	{
		extract($arr);
		if (isset($filter))
		{
			$this->set_filter($filter);
		};
	
		if (!($selt==0 && $delta==-1) && !($selt==$this->filter["nump"]-1 && $delta==1))
		{
			$_1="p$selt";
			$_2="p".($selt+$delta);

			$save=$this->filter[$_1];//vaheta
			$this->filter[$_1]=$this->filter[$_2];
			$this->filter[$_2]=$save;
		};

		return $this->filter;
	}


	// filtrile yhe tingimuse lisamine
	function do_filter_edit_add($arr)
	{
		extract($arr);
		//print_r($arr);//dbg
		if (isset($filter))
		{
			$this->set_filter($filter);
		};
		if (!is_array($sel) || !sizeof($sel))
		{
			$sel[0]=0;
		};
		$piir=$sel[0];
		if ($addpos == "before")
		{
				$piir--;
		};
		
		if (($value=$val)=="_date")//date gets special handling
		{
			if ($dsval!="")//kasutaja tahab kasutada hetkega
			{
				$value='@t'.$dsplusminus.$dsval.$dsyhik;
			} else
			{
				$value=mktime($dateval["hour"], $dateval["minute"],00, $dateval["month"],$dateval["day"],$dateval["year"]);
			};
		};

		if (substr($fie,0,8)=="%virtual")
		{
			$expr="=";
		};
		if ($selt=="right")
		{
			$newp=array(
				"field" => ")",
				"join" => $expr);
		} 
		else
		if ($selt=="left")
		{
			$newp=array(
				"field" => "(",
				"join" => $expr);
		} 
		else
		{
			$newp=array(
				"field" => $fie,
				"op" => $expr,
				"join" => $op,
				"val" => $value,
				"type" => $type
			);
		};

		//echo("newp=<pre>");print_r($newp);echo("</pre>");//dbg
		//echo("filter=<pre>");print_r($filter);echo("</pre>");//dbg
		$nf=array();
		$j=0;
		//echo("piir=$piir");//dbg

		if ($is_change_part)
		{
			$nf["p$change_part"]=$newp;
			/*echo("filtri osa muutmise submit: $change_part,<pre>");//dbg
			print_r($newp);//dbg
			echo("</pre>");//dbg*/
		} 
		else
		{
			//Kui tahetakse lisada esimese ette, siis tee seda
			if ($piir==-1)
			{
					$nf["p$j"]=$newp;
					$j++;
			};
			//Muidu liiguta kıik filtri osad uude arraysse aga vahele pista uus osa
			for ($i=0; $i<(int)$this->filter["nump"] ;$i++)
			{
				$nf["p$j"]=$this->filter["p$i"];
				$j++;
				if ($i==$piir)
				{
					$nf["p$j"]=$newp;
					$j++;
				};
			};
			//Aga kui osasid ¸ldse enne ei olnud, siis pane uus esimeseks
			if (!(int)$this->filter["nump"])
			{
				$nf["p0"]=$newp;
			};

			$nf["nump"]=(int)$this->filter["nump"]+1;//Suurenda osade arvu
		};
		//0 1 2 3 |4| 5 6 7
		//echo("nf=<pre>");print_r($nf);echo("</pre>");//dbg
		$this->filter=array_merge($this->filter,$nf);
		
		return $this->filter;
	}

	// filtrile yhe tingimuse kustutamine
	function do_filter_edit_del($arr)
	{
		extract($arr);
		if (isset($filter))
		{
			$this->set_filter($filter);
		};
		if (!is_array($sel) || !sizeof($sel))
		{
			return $this->filter;
		};
		$sel=array_flip($sel);

		$nf=array();
		$j=0;
		//Liiguta allesj‰‰nud osad uude arraysse
		for ($i=0; $i<(int)$this->filter["nump"] ;$i++)
		{
			if (!isset($sel[$i]))
			{
				$nf["p$j"]=$this->filter["p$i"];
				$j++;
			};
		};
		//viimased, mis yle j‰id, vıta ‰ra. krdi keemia
		for ($i=0;$i<sizeof($sel);$i++)
		{
			unset($this->filter["p".($j+$i)]);
		}
		
		//V‰henda osade arvu
		$nf["nump"]=(int)$this->filter["nump"]-sizeof($sel);
		//0 1 2 3 |4| 5 6 7
		//echo("nf=<pre>");print_r($nf);echo("</pre>");//dbg
		$this->filter=array_merge($this->filter,$nf);

		return $this->filter;
	}



	//tagastab  WHERE osa queryst, mis vastab mingile filtrile
	function filter_to_sql($arr=array())//noeval kas evalida @uid ja @t... ,fake kas kasutada fake v‰lju, do_ign kas eemaldada t¸hjad ign flagiga osad
	{
		extract($arr);
		if (isset($filter))
		{
			$this->set_filter($filter);
		};

		$xlate=array(" "=>"_","'"=>"_",";"=>"_");
		
		$w="";
		$fi=$this->filter;

		$this->used_tables=array();//contains all tables used in where part

		for ($i=0; $i<(int)$fi["nump"]; $i++)
		{
			$f=$fi["p$i"];

			$fjoin=$fake?$this->joinnames[$f["join"]]:$f["join"];
			if ($f["field"]=="(")
			{
				$w.=(!$w? $fake?"kus":"WHERE" : $fjoin )." left";
				
			} 
			else
			if ($f["field"]==")")
			{
				$w.=" ) ";
			} 
			else
			{
				if ($do_ign && $f["ign"] && ($f["val"]=="" || !isset($f["val"]) || !$f["user_dta"]) )
				{
					continue;
				};
				// vaheta v‰lja v‰‰rtused @uid ja @t+365d
				if (!$noeval && substr($f["val"],0,2)=="@t")
				{
					switch (substr($f["val"],strlen($f["val"])-1,1))
					{
						default:
						case "h": 
							$mul=60*60;
							break;
						case "m":
							$mul=60;
							break;
						case "d":
							$mul=60*60*24;
							break;
					};
					// ntx $t-24h t‰hendab 24 tundi tagasi ja $t+30m t‰henab 30 minuti p‰rast
					$q2='$val=time() '.substr($f["val"],2,1).((int)substr($f["val"],3,strlen($f["val"])-4)).'*'.$mul.";";
					eval($q2);
				} 
				elseif (!$noeval && $f["val"]=="@uid")
				{
					$val=aw_global_get("uid");
				} 
				else
				{
					$val=strtr($f["val"],array("'"=>"\'"));
				};

				//Translate stuff into hopefully-(non-sql-capable-person)-readable form
				$fakeval=$val;
				$fakejoin=strtr($f["join"],$xlate);
				list($realtable,$realfield)=explode(".",$f["field"]);
				$this->used_tables[$realtable]=1;

				$faketable=$realtable;
				$fakefield=$realfield;
				if ($fake)
				{
					$faketable=$this->reverse[$realtable]["fake"];
					$fakefield=$this->reverse[$realtable]["fields"][$realfield]["fake"];
					
					if (is_array($this->tables[$faketable]["fields"][$fakefield]["select"]) &&
						isset($this->tables[$faketable]["fields"][$fakefield]["select"][$fakeval]))
					{
						$fakeval=$this->tables[$faketable]["fields"][$fakefield]["select"][$fakeval];
					};
					$fakejoin=$this->joinnames[$fakejoin];
				};
				if ($f["op"]=="LIKE" || $f["op"]=="NOT LIKE")
				{
					$fakeval="%$fakeval%";
				};

				if ($fakeval != '')
				{
					// siin vaatab et kui v6rdlus on > < >= <= siis ei pane '' ymber valuele
					// vıi kui on m‰‰ratud masterarrays et ei panda siis ka ei panda
					if (($f["op"]=="LIKE" || $f["op"]=="NOT LIKE" || $f["op"]=="=" ||$f["op"]=="!=") && (!$f["noqm"]))
					{
						$fakeval="'$fakeval'";
					} else 
					if ($fakeval[0]!="@") //this stuff is for bugtrack @uid ja @t+24h et saaks teha n‰iteks et n‰ita viimase n‰dala bugisid
					{
						$fakeval+=0;
					}
					$w.=" ".(!$w? $fake?"kus":"WHERE" : $fakejoin )." $faketable.$fakefield ".($f["op"] == "NOT LIKE" ? $f["op"] : strtr($f["op"],$xlate))." $fakeval";
				}
			};
		};
		$this->used_tables=array_keys($this->used_tables);
		return $w;
	}
}
?>
