<?php

class search_filter extends aw_template
{
        function search_filter()
        {
                $this->init("automatweb/filter");
                lc_load("definition");
                $this->sql_filter=get_instance("formgen/sql_filter");
        }


	/**

		@attrib name=new params=name default="0"

		@param parent required acl="add"

		@returns


		@comment

	**/
        function orb_new($arr)
        {
                is_array($arr)? extract($arr) : $parent=$arr;

                $this->mk_path($parent,"Lisa Filter");
                $this->read_template("new_filter.tpl");

                $this->fb = get_instance("formgen/form_base");
                $formlist = $this->fb->get_list(FTYPE_ENTRY,false,true);

				$ol = new object_list(array(
					"class_id" => CL_FORM_CHAIN
				));

                $chainlist = $ol->names();
                $this->vars(array(
					"formlist"=>$this->picker("",$formlist),
					"chainlist"=>$this->picker("",$chainlist),
					"reforb" => $this->mk_reforb("submit_new",array("parent" => $parent))
				));

                return $this->parse();
        }

	/**

		@attrib name=submit_new params=name default="0"


		@returns


		@comment

	**/
        function orb_submit_new($arr)
        {
                is_array($arr)? extract($arr) : $parent=$arr;

				$o = obj();
				$o->set_parent($parent);
				$o->set_name($name);
				$o->set_class_id(CL_SEARCH_FILTER);
				$o->set_comment($comment);
                $id = $o->save();

                $this->data=array(
                        "type" => $type,
                        "output_id" => 0,
                        "stat_id" => 0,
                        "stat_show" => 0,
                        "stat_data" => array(),
                );

                // Nii nüüd tuleb vaatada kas keegi tahab otsida mitmest pärjast.
                if (count($target_id_c)<2)
                {
                        $this->data["target_id"]=$type=="form"?$target_id_f:$target_id_c[0];
                }
                else
                {
                        $this->data["target_id"]=serialize($target_id_c);
                        $this->data["multchain"]=1;
                };

                $this->obj_set_meta(array("oid"=>$id,"meta"=>array("data"=>$this->data)));
                return $this->mk_my_orb("change",array("id" => $id,"parent" => $parent));
        }


	/**

		@attrib name=submit_change params=name default="0"


		@returns


		@comment

	**/
        function orb_submit_change($arr)
        {
                extract($arr);
                $this->id=$id;
                $arr["filter"]=$this->__load_filter();

                $this->filter=$this->sql_filter->do_submit_filter_edit($arr);

                $this->__save_filter();
                return $this->mk_my_orb("change",array("id" => $id));
        }

	/**

		@attrib name=change params=name default="0"

		@param id required acl="view;edit"
		@param parent optional
		@param change_part optional
		@param is_change_part optional

		@returns


		@comment

	**/
        function orb_change($arr)
        {
                is_array($arr)? extract($arr) : $parent=$arr;
                $this->id=$id;
                $this->db_query("SELECT name,parent FROM objects WHERE oid='$this->id'");
                $r=$this->db_next();
                $parent=$r["parent"];
                $name=$r["name"];

                $this->mk_path($parent,"Filter");

                $this->__load_data();
                $this->__load_filter();
                $this->filter["name"]=$name;
                $this->build_master_array();
                $this->sql_filter->set_data($this->master_array);

                //echo($this->sql_filter->filter_to_sql(array("filter"=>$this->filter)));//dbg
                return $this->make_upper_menu($arr,"change").$this->sql_filter->do_filter_edit(array(
                "filter"=>$this->filter,
                "is_change_part"=>$is_change_part,
                "change_part" => $change_part,
                "reforb_func" => "submit_change",
                "reforb_edit_func" => "change",
                "reforb_class" => "search_filter",
                "reforb_arr" => array("id" => $id),
                "reforb"=>$this->mk_reforb("submit_change",array("id" => $id))
                ));

        }

        function __load_data()
        {
                $kala=$this->obj_get_meta(array("oid" => $this->id));
                $this->data=$kala["data"];
                //echo("loaddata<pre>");print_r($this->data);echo("</pre>");
        }

        function __save_data()
        {
                $this->obj_set_meta(array("oid" => $this->id,"meta"=>array("data"=>$this->data)));
        }

        function __load_filter()
        {
                $kala=$this->obj_get_meta(array("oid" => $this->id));
                $this->filter=$kala["filter"];
                if ($GLOBALS["shit"])
                {
                        echo("<textarea cols=80 rows=40>");
                        print_r($this->filter);
                        echo("</textarea>");
                };
                if (!is_array($this->filter))
                {
                        $this->filter=array();
                };
                return $this->filter;
        }

        function __save_filter()
        {
                $arr=array("filter"=>$this->filter);
                $this->obj_set_meta(array("oid" => $this->id,"meta" => $arr));
        }

        function build_master_array()
        {
                $this->master_array=array(
                        ""=>array(
                                "real"=>"%virtual",
                                "fields"=>array(
                                        "täistekst"=>array(
                                                "real"=>"%täistekst",
                                                "type"=>0,
                                        )
                                )
                        ));

                $formids=array();
                if ($this->data["type"]=="form")
                {
                        $formids=explode(",",$this->data["target_id"]);
                        if (!is_array($formids))
                        {
                                $formids=array();
                        };
                }
                else
                if ($this->data["type"]=="chain")
                {
                        if ($this->data["multchain"])
                        {
                                $idin=join(",", utf_unserialize($this->data["target_id"]));
                        }
                        else
                        {
                                $idin=(int)$this->data["target_id"];
                        };
                        $this->db_query("SELECT form_id,chain_id FROM form2chain WHERE chain_id IN ($idin)");
                        while ($r= $this->db_next())
                        {
                                if ($r["form_id"])
                                {
                                        $formids[]=$r["form_id"];
                                        // Okei, edaspidi kuskil läheb meil vaja teada et mis formid on mis pärgadest pärit
                                        // Sest lõpuks peame tegema n otsingut järjest tegelikult kus n= count chain_ids
                                        // Ja igas otsingus tuleb kasutada ainult selle pärjaga seotud tingimusi
                                        // Kurat, see on nõme
                                        // Ja maeitea mis siis veel on kui sama form on mitmes pärjas millest otsitakse.blah.
                                        $form2chain[$r["form_id"]]=$r["chain_id"];
                                }
                        };

                        // right. if we are doing the click-on-word-in-result-set search we need to search only from the chain that the search-from
                        // form is in. so we check it here and remove all other chains from the search array
                        if ($GLOBALS["search_form"])
                        {
                                // find the chain the form is in.
                                $_chid = $form2chain[$GLOBALS["search_form"]];

                                // now remove all other forms
                                $_f2c = array();
                                foreach($form2chain as $fid => $chid)
                                {
                                        if ($chid == $_chid)
                                        {
                                                $_f2c[$fid] = $chid;
                                        }
                                }
                                $form2chain = $_f2c;
                                $this->data["multchain"] = false;
                                $this->data["target_id"] = $_chid;
                        }
                };

                $this->form=get_instance(CL_FORM);

                //Okay, let's build the array
                foreach ($formids as $k => $fid)
                {
                        $fid=(int)$fid;
                        if (!$fid)
                        {
                                continue;
                        }

                        $this->db_query("SELECT objects.name FROM objects WHERE objects.oid='$fid'");
                        $r=$this->db_next();

                        $formname=str_replace(" ","_",$r["name"]);
                        $content=$this->form->get_form_elements(array("id" => $fid,"key" => "id"));
                        #echo("form title=$formname<br /><pre>");print_r($content);echo("</pre>");//dbg

                        $arr=array();
                        // Kui on mitmest pärjast otsing siis paneme real table nime ette *chain_id* siis saab
                        // Selle järgi pärast filtreerida osasid kuna see liigub edasi filtri datasse

                        if ($this->data["multchain"])
                        {
                                $arr["real"]="*".$form2chain[$fid]."*form_".$fid."_entries";
                        }
                        else
                        {
                                $arr["real"]="form_".$fid."_entries";
                        };

                        //echo("content=><pre>");print_r($content);echo("</pre><br />");//dbg
                        foreach($content as $f_id => $edata)
                        {
                                $fieldname=$edata["name"];
                                $create=1;
                                if ($GLOBALS["dbg_cbox"]) {echo("$fieldname=><pre>");print_r($edata);echo("</pre><br />");};//dbg
                                switch ($edata["type"])
                                {
                                        case "button"://Don't let these suckers in!
                                                $create=0;
                                                break;

                                        case "radiobutton":

                                                $arr["fields"][$edata["name"]]["type"]=0;//string
                                                $arr["fields"][$edata["name"]]["select"][$edata["text"]]=$edata["text"];
                                                $arr["fields"][$edata["name"]]["select"][" (X) "]=" (X) ";
                                                $arr["fields"][$edata["name"]]["select"][" (-) "]=" (-) ";
                                                $arr["fields"][$edata["name"]]["select"][""]="";
                                                break;


                                        case "checkbox":
                                                $arr["fields"][$edata["name"]]["type"]=0;//string
                                                $arr["fields"][$edata["name"]]["select"][$edata["text"]]=$edata["text"];
                                                $arr["fields"][$edata["name"]]["select"]["(X) "]="(X) ";// kristo koodis on kala sees
                                                $arr["fields"][$edata["name"]]["select"][" (-) "]=" (-) ";
                                                $arr["fields"][$edata["name"]]["select"][""]="";

                                        case "listbox":
                                                $arr["fields"][$edata["name"]]["type"]=0;//string
                                                $arr["fields"][$edata["name"]]["select"][0]="";
                                                if (is_array($edata["lb_items"]))
                                                {
                                                        foreach($edata["lb_items"] as $number => $lbval)
                                                        {
                                                                if ($lbval)//miskid tyhjad valikud tekivad
                                                                        $arr["fields"][$edata["name"]]["select"][$lbval]=$lbval;
                                                        };
                                                }
                                                break;

                                        case "date":
                                                $arr["fields"][$edata["name"]]["type"]=2;//date
                                                $arr["fields"][$edata["name"]]["real"]="el_$f_id";
                                                $arr["fields"][$edata["name"]]["noqm"]=1;// ei pane väärtusele ' ümber
                                                $create=0;
                                                break;

                                        default:
                                                $create=1;
                                                $arr["fields"][$edata["name"]]["type"]=0;//string
                                                break;
                                };
                                if ($create)
                                {
                                        $arr["fields"][$edata["name"]]["real"]="ev_$f_id";
                                };

                        };

                        $this->master_array[$formname]=$arr;
                };//of ($formids as $k => $fid)

                //if ($GLOBALS["dbg_ft"]){echo("<pre>");print_r($this->master_array);echo("</pre>");};//dbg
        }

        function make_upper_menu($arr,$action)
        {
                extract($arr);
                $a="<table border=0 cellpadding=2 cellspacing=1 bgcolor=#CCCCCC><tr>";
                $b=($action=="change")?0:1;
                $a.="<td bgcolor=#EEEEEE>".($b?"<a href='".$this->mk_my_orb("change",array("id"=>$id))."'>":"")."Tingimused".($b?"</a>":"")."</td>";

                $b=($action=="output")?0:1;
                $a.="<td bgcolor=#EEEEEE>".($b?"<a href='".$this->mk_my_orb("output",array("id"=>$id))."'>":"")."Väljund".($b?"</a>":"")."</td>";

                $b=($action=="statdata")?0:1;
                $a.="<td bgcolor=#EEEEEE>".($b?"<a href='".$this->mk_my_orb("statdata",array("id"=>$id))."'>":"")."Statandmed".($b?"</a>":"")."</td>";

                $b=($action=="stat")?0:1;
                $a.="<td bgcolor=#EEEEEE>".($b?"<a href='".$this->mk_my_orb("stat",array("id"=>$id))."'>":"")."Stattabel".($b?"</a>":"")."</td>";

                $b=($action=="search")?0:1;
                $a.="<td bgcolor=#EEEEEE>".($b?"<a href='".$this->mk_my_orb("search",array("id"=>$id))."'>":"")."Otsi".($b?"</a>":"")."</td>";
                $a.="</tr></table>";
                return $a;
        }

        // see on selline func mis kudagi petab 2ra kliendi
        // See vist on obsolete kuna nüüd saab niigi otsinguformi filtriga siduda??
        // -->
	/**

		@attrib name=tf_fulltext_search params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_totally_fake_fulltext_search($arr)
        {
                extract($arr);
                $html="<form action='orb.".$this->cfg["ext"]."' method='get'>
                        <input type='text' name='true_fulltext' value='' class='small_button'>
                        <input type='submit' value='Otsi' class='small_button'>
                        <input type='hidden' name='class' value='search_filter'>
                        <input type='hidden' name='id' value='$id'>
                        <input type='hidden' name='action' value='do_tf_fulltext_search'>
                        </form>
                        ";
                return $html;
        }

	/**

		@attrib name=do_tf_fulltext_search params=name default="0"

		@param id required
		@param true_fulltext optional

		@returns


		@comment

	**/
        function orb_do_totally_fake_fulltext_search($arr)
        {
                extract($arr);
                //okei, siin nüüd muudame ära ainsa filtri osa teksti ja otsime stuffi
                $this->id=$id;
                $this->__load_filter();
                //print_r($this->filter);//dbg
                $this->filter["p0"]["val"]=$true_fulltext;
                $this->__save_filter();

                $arr["no_menu"]=1;
                $arr["j2ta_see_form_sinna_yles"]=1;
                return $this->orb_search($arr);
        }
        // <--

	/**

		@attrib name=stat_select_submit params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_stat_select_submit($arr)
        {
                extract($arr);
                $this->id=$filter_id;
                $this->__load_data();
                $this->data["stat_id"]=$selected_table;
                $this->data["stat_pix"]=(int)$this->data["stat_pix"];
                $this->__save_data();
                return $this->mk_my_orb("stat",array("id" => $filter_id));
        }

	/**

		@attrib name=stat_new_submit params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_stat_new_submit($arr)
        {
                extract($arr);
                $this->id=$filter_id;
                $this->__load_data();

                $tbl=get_instance(CL_TABLE);

                $arr["is_filter"]=1;
                $arr["filter"]=$this->id;
                $tbl->submit_add($arr);
                $this->data["stat_id"]=$tbl->id;
                //echo("id=".$tbl->id);
                //$this->data["stat_show"]=1;
                $this->data["stat_pix"]=(int)$this->data["stat_pix"];

                $this->__save_data();

                return $this->mk_my_orb("stat",array("id" => $filter_id));
        }

        // Sellega valitakse statistika andmed ehk funktsioonid siis
	/**

		@attrib name=statdata params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_statdata($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->db_query("SELECT name,parent FROM objects WHERE oid='$this->id'");
                $r=$this->db_next();
                $parent=$r["parent"];
                $name=$r["name"];

                $this->read_template("statdata.tpl");

                $this->__load_data();
                $this->build_master_array();

                $fieldarr=array();
                if (is_array($this->master_array))
                foreach($this->master_array as $tfakename => $tdata)// for each table do
                {
                        if ($tfakename && is_array($tdata) && is_array($tdata["fields"])) // for each field in table do
                        foreach($tdata["fields"] as $ffakename => $fdata)
                        {
                                $fieldarr[$tdata["real"].".".$fdata["real"]]="$tfakename.$ffakename";
                        };
                };


                $statd="";
                $fields=$this->picker("",$fieldarr);

                if (is_array($this->data["statdata"]))
                foreach($this->data["statdata"] as $alias => $sd)
                {
                        $this->vars(array(
                                "alias" => "#$alias",
                                "nr" => $alias,
                                "display" => $sd["display"],
                                ));
                        $statd.=$this->parse("statd");
                };

                $this->vars(array(
                        "chkstat_show"=> $this->data["stat_show"]?"checked":"",
                        "stat_pix" => $this->data["stat_pix"],
                        "statd" => $statd,
                        "fields" => $fields,
                        "reforb" => $this->mk_reforb("submit_statdata",array("id"=>$id)),
                        ));

                $this->mk_path($parent,"Filter");
                $legend=$this->_make_legend("Funktioonid:",array(
                        "sum"=>"summeerib väärtused tulbas",
                        "avg"=>"arvutab tulba väärtustest kesmise",
                        "min"=>"leiab väikseima väärtuse tulbas",
                        "max"=>"leiab suurima väärtuse tulbas",
                ));
                return $this->make_upper_menu($arr,"statdata").$this->parse().$legend;
        }

	/**

		@attrib name=submit_statdata params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_submit_statdata($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->__load_data();
                $this->data["stat_pix"]=$stat_pix;
                $this->data["stat_show"]=$stat_show;
                //echo("enne:<pre>");print_r($this->data);echo("</pre>");
                if ($subaction=="addpart")
                {
                        $arr2["func"]=$func;

                        list($rtable,$rfield)=explode(".",$field);
                        $this->build_master_array();

                        //tra, vastupidi on :)
                        if (is_array($this->master_array))
                        foreach($this->master_array as $tfakename => $tdata)// for each table do
                        {
                                if ($tdata["real"]==$rtable && is_array($tdata["fields"])) // for each field in table do
                                {
                                        foreach($tdata["fields"] as $ffakename => $fdata)
                                        if ($fdata["real"]==$rfield)
                                        {
                                                $ffield=$ffakename;
                                                break;
                                        };
                                        $ftable=$tfakename;
                                };
                        };
                        $arr2["field"]=$rfield;
                        $arr2["table"]=$rtable;
                        $arr2["display"]="$func($ftable.$ffield)";
                        //print_r($arr2);//dbg
                        $this->data["statdata"][]=$arr2;

                }
                else
                if ($subaction=="delpart")
                {
                        if (is_array($sel))
                        foreach($sel as $nr)
                        {
                                unset($this->data["statdata"][$nr]);
                        };
                };
                //echo("<pre>");print_r($this->data);echo("</pre>");
                $this->__save_data();
                return $this->mk_my_orb("statdata",array("id"=>$id));
        }

	/**

		@attrib name=stat params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_stat($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->db_query("SELECT name,parent FROM objects WHERE oid='$this->id'");
                $r=$this->db_next();
                $parent=$r["parent"];
                $name=$r["name"];

                $this->__load_data();

                $tbl=get_instance(CL_TABLE);
                if ($this->data["stat_id"])
                {
                        $parse="<div><IFRAME SRC='".$this->mk_my_orb("change",array(
                                "id"=>$this->data["stat_id"],
                                "is_filter"=>1,
                                "filter"=>$id,
                                ),"table")."' Style='width:100%;height:800;margin-left:-5;margin-top:0;' frameborder=0 id='ifr'></iframe></div>";
                }
                else
                {
                        // siia ka valik juba olemasoleva valimiseks
                        $parse2=$tbl->add(array("parent" => $parent,"name" => "stat_for_$name"));

                        $parse2=preg_replace("/name='class' value='(.+?)'/","name='class' value='search_filter'",$parse2);
                        $parse2=preg_replace("/name='action' value='(.+?)'/","name='action' value='stat_new_submit'",$parse2);
                        $parse2=preg_replace("/<input type='hidden' name='reforb'/","<input type='hidden' name='filter_id' value='$id'><input type='hidden' name='reforb'",$parse2);


						$ol = new object_list(array(
							"class_id" => CL_TABLE
						));
						$tables = $ol->names();

                        $this->read_template("selstattable.tpl");
                        $this->vars(array(
                                "statsel"=>$this->picker("",$tables),
                                "newtable"=>$parse2,
                                "reforb"=> $this->mk_reforb("stat_select_submit",array("filter_id"=>$id)),
                        ));
                        $parse=$this->parse();
                };

                $this->mk_path($parent,"Filter");
                return $this->make_upper_menu($arr,"stat").$parse;
        }


	/**

		@attrib name=search params=name default="0"

		@param id required
		@param get_csv_file optional
		@param no_menu optional

		@returns


		@comment

	**/
        function orb_search($arr)
        {
                extract($arr);
                if (isset($filter_id))
                {
                        $id=$filter_id;
                };
                $this->id=$id;
                $this->db_query("SELECT name,parent FROM objects WHERE oid='$this->id'");
                $r=$this->db_next();
                $parent=$r["parent"];
                $name=$r["name"];
                $this->__load_data();

                if (!$no_menu)
                $this->mk_path($parent,"Filter");

                if (!$this->data["output_id"])
                {
                        return $this->make_upper_menu($arr,"search")."Väljundi tabelit pole veel määratud, vajuta 'väljund' lingile!";
                };
                if (!$dont_load_filter)
                {
                        $this->__load_filter();
                }

                $this->build_master_array();
                $this->sql_filter->set_data($this->master_array);

                if ($arr["do_ign"])
                {
                        $this->do_ign=1;
                }

                //siin tuleb stuffi näidata
                $this->ft=get_instance(CL_FORM_TABLE);
                $table_id=$this->data["output_id"];
                $this->ft->start_table($table_id);

                $stats=array();//statistika avaldiste väärtused
                $num_rec_found=0;

                //Nii kuidas teha asja nii et töötaks chainide korral, formide korral ja mitme chaini korral??

                if ($this->data["type"]=="chain")
                {
                        // kui on mitu chaini siis tuleb siin SEDA andmete näitamise osa korrata 1x iga chaini matchide jaoks
                        if ($GLOBALS["dbg_ft"]) echo "multchain=[".$this->data["multchain"]."]<br />";
                        if (!$this->data["multchain"])
                        {
                                $chainids[]=$this->data["target_id"];//All is good & easy
                        }
                        else
                        {
                                $chainids=utf_unserialize($this->data["target_id"]);
                        };
                        // et asi puusse ei paneks kuna ka mitte millestki võib midagi otsida eksole
                        if (is_array($chainids))
                        {
                                foreach ($chainids as $chain_id) // This is THE loop
                                {
                                        if ($GLOBALS["dbg_ft"]) echo "[searching in $chain_id]<br />";
                                        $eids = $this->perform_search($this->data["multchain"]?$chain_id:array());// Limit conditions to chain $chain_id

                                        $this->ft->load_chain($chain_id);

                                        if ($GLOBALS["dbg_ft"]) {echo "eids=", var_dump($eids), "<br />";};//dbg

                                        $tbls = "";
                                        $joins = "";
                                        reset($this->ft->chain["forms"]);
                                        list($fid,) = each($this->ft->chain["forms"]);
                                        while(list($ch_fid,) = each($this->ft->chain["forms"]))
                                        {
                                                if ($ch_fid != $fid)
                                                {
                                                        $tbls.=",form_".$ch_fid."_entries.*";
                                                        $joins.=" LEFT JOIN form_".$ch_fid."_entries ON form_".$ch_fid."_entries.chain_id = form_".$fid."_entries.chain_id ";
                                                }
                                        }

                                        $eids = join(",", $eids);
                                        // temporary workaround selle topelt entryte kala jaoks, kuigi see
                                        // kuradi distinct() peaks hoopis seda tegema vist

                                        $used_eids=array();
                                        if ($eids != "")
                                        {
                                                $q = "SELECT distinct(form_".$fid."_entries.id) as entry_id, form_".$fid."_entries.chain_id as chain_entry_id, form_".$fid."_entries.* $tbls FROM form_".$fid."_entries LEFT JOIN objects ON objects.oid = form_".$fid."_entries.id $joins WHERE objects.status != 0 AND form_".$fid."_entries.chain_id in ($eids)";
                                                 if ($GLOBALS["dbg_ft"]) echo "q = $q <br />";//dbg
                                                $this->db_query($q);
                                                while ($row = $this->db_next())
                                                {
                                                        if ($used_eids[$row["chain_entry_id"]])
                                                        {
                                                                continue;
                                                        }
                                                        $used_eids[$row["chain_entry_id"]]=1;

                                                        if ($GLOBALS["dbg_ft"]) echo "nr= $num_rec_found eid = ", $row["entry_id"], " ch_eid = ", $row["chain_entry_id"], "<br />";//dbg
                                                        $num_rec_found++;
                                                        if ($this->data["stat_show"] && $this->data["stat_id"] && is_array($this->data["statdata"]))
                                                        {
                                                                foreach($this->data["statdata"] as $alias2 => $statd2)
                                                                {

                                                                        $v2=$row[$statd2["field"]];
                                                                        //echo($statd2["field"]." = ".$v2."<br />");//dbg
                                                                        switch($statd2["func"])
                                                                        {
                                                                                case "sum":
                                                                                        $stats[$alias2]["val"]+=$v2;
                                                                                        break;
                                                                                case "min":
                                                                                        if ($stats[$alias2]["val"]=="")
                                                                                                $stats[$alias2]["val"]=$v2;
                                                                                        if ($v2<$stats[$alias2]["val"])
                                                                                                $stats[$alias2]["val"]=$v2;
                                                                                        break;
                                                                                case "max":
                                                                                        if ($v2>$stats[$alias2]["val"])
                                                                                                $stats[$alias2]["val"]=$v2;
                                                                                        break;
                                                                                case "avg":
                                                                                        $stats[$alias2]["sum"]+=$v2;
                                                                                        $stats[$alias2]["num"]++;
                                                                                        break;
                                                                        };
                                                                };
                                                        };
                                                        $this->ft->row_data($row,$fid,$GLOBALS["section"], $this->filter["filter_op"], $chain_id,$row["chain_entry_id"] );
                                                };
                                        };
                                }; // of foreach ($chainids as $chain_id)
                        }
                }
                else
                {
                        //tavaline yhest formist otsimine oli hoopis

                        $fid=$this->data["target_id"];

                        $eids = $this->perform_search();
                        $eids=join(",",$eids);
                        if ($eids != "")
                        {
                                $q="SELECT * FROM form_".$fid."_entries,objects WHERE objects.status != 0 and objects.oid = '$fid' AND form_".$fid."_entries.id in ($eids)";
                                $this->db_query($q);
                                while ($row = $this->db_next())
                                {
                                        $num_rec_found++;
                                        if ($this->data["stat_show"] && $this->data["stat_id"] && is_array($this->data["statdata"]))
                                        {
                                                foreach($this->data["statdata"] as $alias2 => $statd2)
                                                {

                                                        $v2=$row[$statd2["field"]];
                                                        //echo($statd2["field"]." = ".$v2." ++".$statd2["func"]."<br />");//dbg

                                                        switch($statd2["func"])
                                                        {
                                                                case "sum":
                                                                        $stats[$alias2]["val"]+=$v2;
                                                                        break;
                                                                case "min":
                                                                        if ($stats[$alias2]["val"]=="")
                                                                                $stats[$alias2]["val"]=$v2;
                                                                        if ($v2<$stats[$alias2]["val"])
                                                                                $stats[$alias2]["val"]=$v2;
                                                                        break;
                                                                case "max":
                                                                        if ($v2>$stats[$alias2]["val"])
                                                                                $stats[$alias2]["val"]=$v2;
                                                                        break;
                                                                case "avg":
                                                                        $stats[$alias2]["sum"]+=$v2;
                                                                        $stats[$alias2]["num"]++;
                                                                        break;
                                                        };
                                                };
                                        };
                                        $this->ft->row_data($row,$fid,$GLOBALS["section"], $this->filter["filter_op"], 0,0);
                                }
                        };
                };


                $parse="";
                // See siin on miskise ymber nurga fulltext searchi jaoks mis kaob varsti ära kui asi tööle hakkab
                if ($j2ta_see_form_sinna_yles)
                {
                        $parse.="<form action='orb.aw' method='get'>
                        <input type='text' name='true_fulltext' value='' class='small_button'>
                        <input type='submit' value='Otsi' class='small_button'>
                        <input type='hidden' name='class' value='search_filter'>
                        <input type='hidden' name='id' value='$id'>
                        <input type='hidden' name='action' value='do_tf_fulltext_search'>
                        </form>
                        ";
                };

                $parse.="Otsingu tulemusena leiti ".(int)$num_rec_found.((int)$num_rec_found==1?" kirje":" kirjet");
                //siin teeb lingi csv outputile
                if ($this_page)
                {
                        $parse.="&nbsp;&nbsp;<a href='".$this_page."&get_csv_file=1' target=_blank>CSV</a><br />";
                }
                else
                {
                        $parse.="&nbsp;&nbsp;<a href='".$this->mk_my_orb("search",array("id"=>$id,"get_csv_file"=>1))."' target=_blank>CSV</a><br />";
                };

                $parse.= $this->ft->finalize_table(array("no_form_tags" => $no_form_tags));

                // Siin on juba joonistatud nüüd see andmete osa siis
                if ($GLOBALS["get_csv_file"])
                {
                        header('Content-type: Application/Octet-stream"');
                        header('Content-disposition: root_access; filename="csv_output_'.$id.'.csv"');
                        print $this->ft->t->get_csv_file();
                        die();
                };


                // Siin hakkab näitama statistika tabelit all
                if ($this->data["stat_show"] && $this->data["stat_id"])
                {
                        $tbl=get_instance(CL_FORM_TABLE);
                        // tee veel avg funktsioonid korda, sest neil tuleb lõpus summa / ridade arv
                        $tbl->fl_external=array();

                        if (is_array($this->data["statdata"]))// Check for loony
                        foreach($this->data["statdata"] as $alias2 => $statd2)
                        {
                                if ($statd2["func"] == "avg")
                                        $stats[$alias2]["val"]=$stats[$alias2]["num"]?$stats[$alias2]["sum"]/$stats[$alias2]["num"]:0;
                                $tbl->fl_external[$alias2]=$stats[$alias2]["val"];
                        };
                        // Tee eralduseks tabel vahele form tabelile ja stat tabelile
                        if ($this->data["stat_pix"])
                        {
                                $parse.="<table border=0 cellpadding=0 cellspacing=0 height='".$this->data["stat_pix"]."' Style='height:".$this->data["stat_pix"]."px'><tr><td></td></tr></table>";
                        };
                        $parse.=$tbl->show(array("id" => $this->data["stat_id"],"is_filter" => 1));
                };
                if (!$no_menu && !$GLOBALS["section"])
                {
                        $parse=$this->make_upper_menu($arr,"search").$parse;
                };
                return $parse;
        }

	/**

		@attrib name=submit_select_forms params=name default="0"


		@returns


		@comment

	**/
        function orb_submit_select_forms($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->__load_data();
                //print_r($selected_forms);//dbg
                if (!is_array($selected_forms))
                {
                        $selected_forms=array();
                };
                $this->data["selected_forms"]=$this->binhex(serialize(array_flip($selected_forms)));
                $this->__save_data();
                return $this->mk_my_orb("output",array("id" => $id));
        }

	/**

		@attrib name=submit_select_fields params=name default="0"


		@returns


		@comment

	**/
        function orb_submit_select_fields($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->__load_data();
                //print_r($selected_forms);//dbg
                if (!is_array($selected_fields))
                {
                        $selected_fields=array();
                };
                $this->data["selected_fields"]=$this->binhex(serialize(array_flip($selected_fields)));
                $this->__save_data();
                return $this->mk_my_orb("output",array("id" => $id));
        }

	/**

		@attrib name=output_use params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_output_use($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->db_query("SELECT name,parent FROM objects WHERE oid='$this->id'");
                $r=$this->db_next();
                $parent=$r["parent"];

				$ol = new object_list(array(
					"class_id" => CL_FORM_TABLE
				));
				$obj_arr = $ol->names();

                $this->read_template("select_formtable.tpl");
                $this->vars(array(
                        "obj_list" => $this->picker("",$obj_arr),
                        "reforb" => $this->mk_reforb("submit_output_use",array("id"=>$id)),
                ));
                $this->mk_path($parent,"Filter");
                return $this->make_upper_menu($arr,"").$this->parse();
        }

	/**

		@attrib name=submit_output_use params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_submit_output_use($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->__load_data();
                $this->data["output_id"]=$use_ft;

                // Get rid of this s.it
                unset($this->data["selected_forms"]);
                unset($this->data["selected_fields"]);
                $this->__save_data();
                return $this->mk_my_orb("output",array("id"=>$id));
        }

        // Jube! siin tuleb ymber teha kudagi
	/**

		@attrib name=output params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_output($arr)
        {
                extract($arr);
                $this->id=$id;
                $this->db_query("SELECT name,parent FROM objects WHERE oid='$this->id'");
                $r=$this->db_next();
                $parent=$r["parent"];
                $name=$r["name"];

                $this->ft=get_instance(CL_FORM_TABLE);

                $this->__load_data();
                $this->__load_filter();

                $this->build_master_array();
                $this->sql_filter->set_data($this->master_array);

                // Kui pole veel formi tabelit tehtud siis tee valmis
                if (!$this->data["output_id"])
                {
                        if ($this->data["selected_forms"] || $this->data["type"]!="chain")
                        {
                                //If we are in field selection phase
                                if (!$this->data["selected_fields"])
                                {
                                        $this->read_template("select_fields.tpl");
                                        $selforms=utf_unserialize($this->hexbin($this->data["selected_forms"]));
                                        $field_arr=array();
                                        foreach($this->master_array as $faketname => $fdata)
                                        {
                                                // Näita ainult nende formide välju mis valiti
                                                if ($faketname && is_array($fdata["fields"]) &&
                                                        (isset($selforms[$faketname]) || $this->data["type"]!="chain"))
                                                {
                                                        foreach($fdata["fields"] as $finame => $fidata)
                                                        {
                                                                $field_arr["$faketname.$finame"]="$faketname.$finame";
                                                        };
                                                };

                                        };
                                        $this->vars(array(
                                                "field_list" => $this->multiple_option_list("",$field_arr),
                                                "ln_use_existing" => $this->mk_my_orb("output_use",array("id"=>$id)),
                                                "reforb" => $this->mk_reforb("submit_select_fields",array("id"=>$id)),
                                                ));
                                        $this->mk_path($parent,"Filter");
                                        return $this->make_upper_menu($arr,"output").$this->parse();

                                }
                                else
                                {
                                        //echo("oki, hakkan uut tegema");//dbg
                                        $this->data["selected_forms"]=utf_unserialize($this->hexbin($this->data["selected_forms"]));
                                        //echo("self=<pre>");print_r($this->data["selected_forms"]);echo("</pre>");//dbg
                                        $num_cols=0;
                                        $form_ids=array();
                                        $names=array();
                                        $columns=array();
                                        $sortable=array();
                                        $this->data["selected_fields"]=utf_unserialize($this->hexbin($this->data["selected_fields"]));

                                        if (is_array($this->master_array))
                                        foreach($this->master_array as $faketname => $tdata)
                                        {
                                                //echo("f=$faketname<br />");//dbg
                                                if ($faketname &&  (isset($this->data["selected_forms"][$faketname]) || $this->data["type"]!="chain"))
                                                {
                                                        //echo("on olemas<br />");

                                                        list($a_,$form_id,$b_)=explode("_",$tdata["real"]);
                                                        $form_ids[]=$form_id;
                                                        //echo("faketname=$faketname form_id=$form_id<br />");//dbg
                                                        if (is_array($tdata["fields"]))
                                                        foreach($tdata["fields"] as $fakefname => $fdata)
                                                        {
                                                                // CHeck if the field is selected

                                                                if (isset($this->data["selected_fields"]["$faketname.$fakefname"]))
                                                                {
                                                                        //echo("field=$fakefname<br />");//dbg
                                                                        list($a_,$fieldid)=explode("_",$fdata["real"]);

                                                                        $names[$num_cols][1]=$fakefname;
                                                                        $sortable[$num_cols]=1;
                                                                        $columns[$num_cols][]=$fieldid;
                                                                        $num_cols++;
                                                                }
                                                        };
                                                };
                                        };

                                        $arr=array(
                                                "name" => "output_for_".$name,
                                                "parent" => $parent,
                                                "comment" => "$id_$name",
                                                "num_cols" => $num_cols,
                                                "forms" => $form_ids
                                        );
                                        //echo("arr=<pre>");print_r($arr);echo("</pre>");//dbg
                                        $this->ft->submit($arr);
                                        $this->data["output_id"]=$this->ft->id;
                                        // Get rid of this s.it
                                        unset($this->data["selected_forms"]);
                                        unset($this->data["selected_fields"]);
                                        $this->__save_data();

                                        //echo("second phase columns=<pre>");print_r($columns);echo("</pre>");//dbg

                                        $arr=array_merge($arr,array(
                                                "id" => $this->data["output_id"],
                                                "columns" => $columns,
                                                "names" => $names,
                                                "sortable" => $sortable,
                                        ));
                                        //echo("arr=<pre>");print_r($arr);echo("</pre>");//dbg
                                        $this->ft->submit($arr);
                                        //echo("f table id=".$this->data["output_id"]);//dbg
                                };
                        }
                        else
                        {
                                //Vot siin tuleb nyyd valida formid, mida kasutada outputis
                                $this->read_template("select_forms.tpl");

                                $form_arr=array();
                                foreach($this->master_array as $faketname => $fdata)
                                {
                                        if ($faketname)
                                        {
                                                $form_arr[$faketname]=$faketname;
                                        }
                                };
                                $this->vars(array(
                                        "form_list"=> $this->multiple_option_list("",$form_arr),
                                        "ln_use_existing" => $this->mk_my_orb("output_use",array("id"=>$id)),
                                        "reforb" => $this->mk_reforb("submit_select_forms",array("id"=>$id)),
                                ));
                                $this->mk_path($parent,"Filter");
                                return $this->make_upper_menu($arr,"output").$this->parse();
                        };
                }

                $cparse=$this->ft->new_change_cols(array("id" => $this->data["output_id"]));
                $cparse=preg_replace("/name='class' value='(.+?)'/","name='class' value='search_filter'",$cparse);
                $cparse=preg_replace("/name='action' value='(.+?)'/","name='action' value='output_submit'",$cparse);
                $cparse=preg_replace("/<input type='hidden' name='reforb'/","<input type='hidden' name='filter_id' value='$id'><input type='hidden' name='reforb'",$cparse);

                $cparse="<a href='".$this->mk_my_orb("output_use",array("id"=>$id))."' class='fgtext'>Kasuta olemasolevat formitabelit</a>".$cparse;

                $this->mk_path($parent,"Filter");

                return $this->make_upper_menu($arr,"output").$cparse;
        }

	/**

		@attrib name=output_submit params=name default="0"

		@param id required

		@returns


		@comment

	**/
        function orb_output_submit($arr)
        {
                extract($arr);
                $this->ft=get_instance(CL_FORM_TABLE);
                $this->ft->submit($arr);

                return $this->mk_my_orb("output",array("id" => $filter_id));
        }

        // selle jaoks peab olema tehtud build_master_array ja load_filter
        function perform_search($limit_to_chain=array())        //miski eleet nipp see array()
        {
                $this->matches=array();

                //Okei, kõigepealt küsi sql filtri käest sql päringu where osa
                // Ja limiteeri tingimused ise ära limit_to_chain kuna sqlfilter ei tohi teada midagi mingitest chainidest ega formidest
                if (!is_array($limit_to_chain))
                {
                        $ft=Array();
                        $numpee=0;
                        for ($pee=0;$pee<$this->filter["nump"];$pee++)
                        {
                                $vchain=explode("*",$this->filter["p$pee"]["field"]);
                                if ($GLOBALS["dbg_ft"]) {echo("vchain=<pre>");print_r($vchain);echo("</pre><br />");};

                                // count($vchain)<3 is for fulltextsearch field witch doesnt exist really
                                if ($vchain[1]==$limit_to_chain || count($vchain)<3)
                                {
                                        $ft["p$numpee"]=$this->filter["p$pee"];
                                        $ft["p$numpee"]["field"]=count($vchain)<3?$this->filter["p$pee"]["field"]:$vchain[2];//remove chain info
                                        $numpee++;
                                };
                        };
                        $ft["nump"]=$numpee;
                        if ($GLOBALS["dbg_ft"]) {echo("!FILTERED!<pre>"); print_r($ft);echo("<pre>END LIMIT");};
                }
                else
                {
                        $ft=$this->filter;
                };
                $sqlw=$this->sql_filter->filter_to_sql(array(
                        "filter" => $ft,
                        "do_ign" => $this->do_ign?1:0
                ));

                // uh, yeah. yeah. I know - kinda ugly hack but damn this thing is kinda complicated and I'm in a hurry
                if ($GLOBALS["search_el"] != "" && $GLOBALS["search_val"] != "")
                {
                        $sqlw = "WHERE form_".$GLOBALS["search_form"]."_entries.ev_".$GLOBALS["search_el"]." = '".$GLOBALS["search_val"]."'";
                        // phuk. here we must somehow exclude all other chains from the search besides the one the element is in
                        // how the hell do we do that?

                        // look at that line above: if ($vchain[1]==$limit_to_chain || count($vchain)<3)
                }

                if ($GLOBALS["dbg_ft"]) echo "<textarea cols=100 rows=1>$sqlw</textarea><br />";


                //Nii, nüüd tuleb see täistekstotsing ringi vahetada
                $fulltextsearch=array();
                $used_tables=array();
                //echo("master_Array=<pre>");print_r($this->master_array);echo("</pre>");//dbg

                if ($GLOBALS["dbg_ft"]) {echo "limit_to_chain=",var_dump($limit_to_chain);};
                if (is_array($this->master_array))
                foreach ($this->master_array as $fakefname => $fdata)
                {
                        if ($fakefname)
                        {
                                $realfname=$fdata["real"];
                                if ($GLOBALS["dbg_ft"]) echo("realfname=$realfname fakefname=$fakefname<br />");
                                if ($realfname[0]=="*")
                                {
                                        $vdata=explode("*",$realfname);//remove chain info
                                        $realfname=$vdata[2];
                                };
                                if (is_array($limit_to_chain) || $vdata[1]==$limit_to_chain)
                                {
                                        if ($GLOBALS["dbg_ft"]) echo("fakefname=$fakefname realfname=$realfname<br />");//dbg
                                        $used_tables[$realfname]=1;

                                        if (is_array($fdata["fields"]))
                                        foreach ($fdata["fields"] as $fakeename => $edata)
                                        {
                                                $fulltextsearch[]=$realfname.".".$edata["real"]." LIKE '%\\1%'";
                                        };
                                };
                        };
                };

                $ftsstring=join(" or ",$fulltextsearch);
                //print_r($fulltextsearch);//dbg
                //echo("ftsstring=$ftsstring<br />");//dbg
                //echo("sqlw=$sqlw<br />");//dbg
                $sqlw=preg_replace("/%virtual.%täistekst = '(.*?)'/","($ftsstring)",$sqlw);
                //echo("sqlw=$sqlw<br />");//dbg

                if ($this->data["type"]=="chain")
                {
                // Oki, seda saab siiski teha yhe queryga:)
                // nimelt tuleb chaini puhul kõik üksikud form_baah_entries joinida nii et
                // select id from form_chain_entries where chain_id='$target_id' and form_baah_entries.
                // chain_id=form_chain_entries.id and baah.. and form_111_entries.chain_id=form_chain_entries.id
                // ja siis pärast näitamisel valida lihtsalt ids form_chain_entries tablast ja seal on juba kirjas
                // mis entry iga formi kohta käib. blaaah indeed.

                        $leftjoin=Array();
                        foreach(array_keys($used_tables) as $tbl)
                        {
                                $leftjoin[]=" LEFT JOIN $tbl ON $tbl.chain_id=form_chain_entries.id ";
                        };
                        //$sql="SELECT form_chain_entries.id FROM form_chain_entries, ".join(",",array_keys($used_tables))." $sqlw AND form_chain_entries.chain_id='".$this->data["target_id"]."' AND ".join(" AND ",$jointofce);
                        $sqlw=$sqlw?$sqlw." AND ":" WHERE ";
                        $targetchain=is_array($limit_to_chain)?$this->data["target_id"]:$limit_to_chain;
                        $sql="SELECT DISTINCT(form_chain_entries.id) as id FROM form_chain_entries".join(" ",$leftjoin)." $sqlw form_chain_entries.chain_id='$targetchain'";

                }
                else
                {
					// get the first table from the join list and join objtable on that
					list($first_tbl,) = each($used_tables);
					$sql="
						SELECT DISTINCT(id) AS id
						FROM ".join(",",array_keys($used_tables))."
							LEFT JOIN objects ON objects.oid = $first_tbl.id
						$sqlw AND objects.status != 0
					";
                };
                if ($GLOBALS["dbg_ft"]) echo "<textarea cols=100 rows=10>$sql</textarea><br />";

                $this->db_query($sql);
                $matches=array();
                while ($r=$this->db_next())
                {
                        $matches[]=$r["id"];
                };
                if ($GLOBALS["dbg_ft"]) {echo "<pre>", var_dump($matches),"</pre><br />";};//dbg
                return $matches;
        }


	/**

		@attrib name=filter_edit_change_part params=name default="0"


		@returns


		@comment

	**/
        function orb_filter_edit_change_part($arr)
        {
                extract($arr);
                if (!is_array($sel))
                {
                        return $this->mk_my_orb("change",array("id" => $id));
                };
                $chgnum=$sel[0];
                return $this->mk_my_orb("change",array("change_part"=> $chgnum,"is_change_part"=>1, "id" => $id));
        }

	/**

		@attrib name=filter_edit_add params=name default="0"


		@returns


		@comment

	**/
        function orb_filter_edit_add($arr)
        {
                extract($arr);

                $this->id=$id;

                $arr["filter"]=$this->__load_filter();

                $this->filter=$this->sql_filter->do_filter_edit_add($arr);

                $this->__save_filter();

                return $this->mk_my_orb("change",array("id" => $id));
        }

        // filtrile yhe tingimuse kustutamine
	/**

		@attrib name=filter_edit_del params=name default="0"


		@returns


		@comment

	**/
        function orb_filter_edit_del($arr)
        {
                extract($arr);
                $this->id=$id;
                $arr["filter"]=$this->__load_filter();

                $this->filter=$this->sql_filter->do_filter_edit_del($arr);

                $this->__save_filter();

                return $this->mk_my_orb("change",array("id" => $id));
        }

        function _make_legend($title,$content)
        {
                $a="<font face='Verdana,Arial,Helvetica,sans-serif' size='-1'><strong>$title</strong></font>".
                        "<table border=0 cellspacing=1 cellpadding=2 bgcolor='#CCCCCC'>";
                if (is_array($content))
                foreach($content as $key => $val)
                {
                        $a.="<tr><td class='ftitle2'>$key</td><td class='fcaption2'>$val</td></tr>";
                };
                $a.="</table>";
                return $a;
        }
}
