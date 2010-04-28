<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/rate/rate.aw,v 1.34 2008/03/31 13:55:34 instrumental Exp $
/*

@classinfo syslog_type=ST_RATE relationmgr=yes maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property top type=textbox size=5 
@caption Mitu topis

@property top_type type=select 
@caption J&auml;rjestatakse

@property objects_from type=select
@caption Objektide piirang

@property objects_from_clid type=select
@caption Vali klass

@property objects_from_folder type=relpicker reltype=RELTYPE_RATE_FOLDER multiple=1
@caption Vali kataloogid

@property objects_from_oid type=relpicker reltype=RELTYPE_RATE_OID
@caption Vali objekt

@property objects_from_rate_scale type=relpicker reltype=RELTYPE_RATE_SCALE automatic=1
@caption Vali skaala

@property url_to type=textbox 
@caption Aadress, kuhu suunata

@reltype RATE_FOLDER value=1 clid=CL_MENU
@caption Hinnatavate objektide kataloog

@reltype RATE_OID value=2
@caption Hinnatav objekt

@reltype RATE_SCALE value=3 clid=CL_RATE_SCALE
@caption Arvestatav skaala

*/
define("OBJECTS_FROM_CLID", 1);
define("OBJECTS_FROM_FOLDER", 2);
define("OBJECTS_FROM_OID", 3);
define("OBJECTS_FROM_RATE_SCALE", 4);

define("ORDER_HIGHEST",1);
define("ORDER_AVERAGE",2);
define("ORDER_VIEWS",3);
define("ORDER_LOWEST_RATE",4);
define("ORDER_LOWEST_VIEWS",5);

define("RATING_AVERAGE", 1);
define("RATING_HIGHEST", 2);
define("RATING_VIEWS", 3);
define("RATING_LOWEST_RATE",4);
define("RATING_LOWEST_VIEWS",5);

class rate extends class_base
{
	const AW_CLID = 189;

	function rate()
	{
		$this->init(array(
			'tpldir' => 'contentmgmt/rate',
			'clid' => CL_RATE
		));
		$this->classes = aw_ini_get("classes");
	}
	
	function get_property(&$arr)
	{
		$prop =& $arr["prop"];
		$myc = null;
		switch($prop['name'])
		{
			case "objects_from":
				$prop['options'] = array(
					OBJECTS_FROM_RATE_SCALE => t("Hindamisskaala j&auml;rgi"),
					OBJECTS_FROM_CLID => t("Klassi j&auml;rgi"),
					OBJECTS_FROM_FOLDER => t("Kataloogi j&auml;rgi"),
					OBJECTS_FROM_OID => t("Objektist"),
				);
				break;

			case "objects_from_clid":
				$myc = is_null($myc) ? OBJECTS_FROM_CLID : $myc;
				$prop['options'] = get_class_picker();
			case "objects_from_folder":
				$myc = is_null($myc) ? OBJECTS_FROM_FOLDER : $myc;
			case "objects_from_oid":
				$myc = is_null($myc) ? OBJECTS_FROM_OID : $myc;
			case "objects_from_rate_scale":
				$myc = is_null($myc) ? OBJECTS_FROM_RATE_SCALE : $myc;
				
				if ($arr['obj_inst']->prop('objects_from') != $myc)
				{
					return PROP_IGNORE;
				}
				break;

			case "top_type":
				$prop['options'] = array(
					ORDER_HIGHEST => t("K&otilde;rgeima hinde j&auml;rgi"),
					ORDER_AVERAGE => t("Keskmise hinde j&auml;rgi"),
					ORDER_VIEWS => t("Vaatamiste j&auml;rgi"),
					ORDER_LOWEST_RATE => t("Madalaima hinde j&auml;rgi"),
					ORDER_LOWEST_VIEWS => t("V&auml;him vaadatud"),
				);
				break;
		}
		return PROP_OK;
	}

	function get_rating_for_object($oid, $type = RATING_AVERAGE, $rate_id = NULL)
	{
		if (!is_oid($oid) || !$this->can("view", $oid))
		{
			return 0;
		}
		
		$rt_limit = "";
		if (!is_null($rate_id) && is_oid($rate_id))
		{
			$rt_limit = ' AND rate_id='.$rate_id;
		}
		else
		{
			$rate_id = 0;
		}
		
		// we need to cache this shit.
		// so, let's make add_rate write it to the object's metadata, in the rate array
		$ob = obj($oid);
		$rts = $ob->meta("__ratings");
		if (!is_array($rts))
		{
			$rts = array();
		}
		if (true || !is_array($rts[$rate_id]))
		{
			$avg = $this->db_fetch_field("SELECT AVG(rating) AS avg FROM ratings WHERE oid = '$oid' $rt_limit", "avg");
			$l_rate = $this->db_fetch_field("SELECT MIN(rating) AS min FROM ratings WHERE oid = '$oid' $rt_limit", "min");
			$max = $this->db_fetch_field("SELECT MAX(rating) AS max FROM ratings WHERE oid = '$oid' $rt_limit", "max");
			$views = $this->db_fetch_field("SELECT hits FROM hits WHERE oid = '$oid'", "hits");
			$rts[$rate_id] = array(
				RATING_AVERAGE => $avg,
				RATING_HIGHEST => $max,
				RATING_VIEWS => $views,
				RATING_LOWEST_RATE => $l_rate,
				RATING_LOWEST_VIEWS => $views
			);
			$ob->set_meta("__ratings",$rts);
			aw_disable_acl();
			if ($ob->parent() && $ob->class_id())
			{
				$ob->save();
			}
			aw_restore_acl();
		}

		if ($type == RATING_AVERAGE)
		{
			return round($rts[$rate_id][$type],2);
		}
		else
		{
			return round($rts[$rate_id][$type]);
		}
		//return number_format((float)$rts[$rate_id][$type],2,".",",");
	}

	/**  
		
		@attrib name=rate params=name nologin="1" default="0"
		
		@param oid required type=int
		@param rate_id optional type=int
		@param return_url optional 
		@param rate required
		@param overwrite_previous optional type=bool
			If overwrite_previous is set, the previous rating by current uid for this oid will be overwritten. It won't take effect unless rate_id is set.
		
		@returns
		
		
		@comment

	**/
	function add_rate($arr)
	{
		extract($arr);
		$ro = aw_global_get("rated_objs");
		if (!is_array($rate))
		{
			$rates = array($rate);
		}
		else
		{
			$rates = $rate;
		}

		if (!is_oid($oid) || !$this->can('view', $oid) || !count($rates))
		{
			header("Location: $return_url");
			die();
		}
		$o = obj($oid);
		$rs = $o->meta("__ratings");


		// CHECK THE KUUKI!!
		$rated_objs = unserialize($_COOKIE["rated_objs"]);
		// Maybe I wanna change my rating for some objs?
		if($overwrite_previous && $rate_id)
		{
			$this->db_query("DELETE FROM ratings WHERE uid = '".aw_global_get("uid")."' AND oid = '$oid' AND rate_id = '$rate_id'");
			unset($rated_objs[$oid]);
		}
		if (!isset($rated_objs[$oid]))
		{
			$rated_objs[$oid] = 1;
			setcookie ("rated_objs", serialize($rated_objs) , time() + 24*3600*1000, "/");


			//if (!isset($ro[$oid]))
			foreach ($rates as $rate_id => $rate)
			{
				if (!is_numeric($rate) || !is_numeric($rate_id))
				{
					continue;
				}

				if (!$rate_id)
				{
					$sc = get_instance(CL_RATE_SCALE);
					$ros = $sc->get_scale_objs_for_obj($oid);
					$rate_id = $ros[0];
				}

				// Update ratings
				$this->db_query("
					INSERT INTO ratings(oid, rating,".($rate_id?' rate_id,':'')." tm, uid, ip) 
					VALUES ($oid,$rate,".($rate_id?"$rate_id,":'').time().",'".aw_global_get("uid")."','".aw_global_get("REMOTE_ADDR")."')
				");

				// Fetch and cache statistics
				$q = "SELECT COUNT(oid) AS total FROM rating_sum WHERE oid = $oid". ($rate_id?" and rate_id = $rate_id":'');
				if($this->db_fetch_field($q, "total") > 0)
				{
					$this->db_query("UPDATE rating_sum SET divider=(divider+1), sum=(sum+".(int)$rate."), avg=(sum/divider) WHERE oid=$oid".($rate_id?" AND rate_id = $rate_id":''));
				}
				else
				{
					$this->db_query("INSERT INTO rating_sum (oid,".($rate_id?" rate_id,":'')." divider, sum, avg) VALUES($oid,".($rate_id?"$rate_id,":'')." 1, $rate, $rate)");
				}
				$ro[$oid] = $rate;

				$stat_query = "SELECT MIN(rating) AS min,MAX(rating) AS max,AVG(rating) AS avg FROM ratings WHERE oid = $oid" . ($rate_id?" AND rate_id = $rate_id":'');
				$this->db_query($stat_query);
				$row = $this->db_next();

				$hits =  $this->db_fetch_field("SELECT hits FROM hits WHERE oid = '$oid'", "hits");
				if (empty($rate_id))
				{
					$rate_id = 0;
				}
				$rs[$rate_id] = array(
					RATING_AVERAGE => $row["avg"],
					RATING_HIGHEST => $row["max"],
					RATING_VIEWS => $hits,
					RATING_LOWEST_VIEWS => $hits,
					RATING_LOWEST_RATE => $row["min"],
				);
			}
			$o->set_meta("__ratings",$rs);
			$o->save();
		}

		if ($arr["no_redir"])
		{
			return true;
		}
		else
		{
			aw_session_set("rated_objs", $ro);
			header("Location: $return_url");
			die();
		};
	}

	/**  
		
		@attrib name=show params=name nologin="1" default="0"
		
		@param id required type=int
		@param from_oid optional type=int
		
		@returns
		
		
		@comment

	**/
	function show($arr)
	{
		extract($arr);
		$override_objects_from = null;
		$override_param = null;
		$this->read_any_template("show.tpl");
		
		$ob = obj($id);

		if (!empty($from_oid))
		{
			// we need to show results in the gallery, read objects from that
			$override_objects_from = OBJECTS_FROM_OID;
			$override_param = $from_oid;
		}

		// get list of all objects that this rating applies to
		$oids = array();
		$ofrom = empty($override_objects_from) ? $ob->prop('objects_from') : $override_objects_from;
		$where = "false"; // Happy default!
		switch($ofrom)
		{
			case OBJECTS_FROM_CLID:
				$param = empty($override_param) ? $ob->prop('objects_from_clid') : $override_param;
				$where = "objects.class_id = " . $param;
			break;
			case OBJECTS_FROM_FOLDER:
				// need to get a list of all folders below that one.
				$mn = array();
				//$pts = new aw_array($ob['meta']['objects_from_folder']);
				$param = empty($override_param) ? $ob->prop('objects_from_folder') : $override_param;
				$_parent_list = new object_list(array(
					"parent" => $param,
					"class_id" => CL_MENU,
				));
				$mn = $_parent_list->ids();
				$where = count($mn) ? "objects.parent IN (".join(",",$mn).")" : "false"; 
			break;
			case OBJECTS_FROM_OID:
				$c_oid = empty($override_param) ? $ob->prop('objects_from_oid') : $override_param;
				$c_obj = obj($c_oid);
				$c_inst = $c_obj->instance();
				if (method_exists($c_inst, "get_contained_objects"))
				{
					$c_objs = $c_inst->get_contained_objects(array(
						"oid" => $c_oid
					));
				}
				else
				{
					$c_objs = array($c_oid => $c_oid);
				}
				$_tar = new aw_array(array_keys($c_objs));
				$where = "objects.oid IN (".$_tar->to_sql().")";
			break;
			case OBJECTS_FROM_RATE_SCALE:
				$sc_id = empty($override_param) ? $ob->prop('objects_from_rate_scale') : $override_param;
				if (is_oid($sc_id) && ($sc = obj($sc_id)) && $sc->class_id() == CL_RATE_SCALE)
				{
					$where = "ratings.rate_id = ".$sc_id;
				}
			break;
			
		}

		// query the max/avg for those. 
		$order = "DESC";
		switch($ob->meta("top_type"))
		{
			case ORDER_HIGHEST:
				$fun = "ROUND(MAX(rating), 1)";
				break;

			case ORDER_AVERAGE:
				$fun = "ROUND(AVG(rating), 1)";
				break;

			case ORDER_VIEWS:
				$fun = "hits.hits";
				break;

			case ORDER_LOWEST_RATE:
				$fun = "ROUND(MIN(rating), 1)";
				$order = "ASC";
				break;

			case ORDER_LOWEST_VIEWS:
				$fun = "hits.hits";
				$order = "ASC";
				break;
		}


		$cnt = 1;

		$sql = "
			SELECT 
				objects.oid as oid, 
				$fun as val ,
				objects.name as name,
				objects.class_id as class_id,
				hits.hits as hits,
				images.file as img_file
			FROM 
				ratings
				JOIN objects ON ratings.oid = objects.oid
				LEFT JOIN g_img_rel ON ratings.oid = g_img_rel.img_id
				LEFT JOIN hits ON hits.oid = ratings.oid
				LEFT JOIN images ON images.id = ratings.oid
			WHERE
				objects.status = 2 AND
				$where
			GROUP BY 
				objects.oid
			ORDER BY val $order
			LIMIT ".(int)($ob->meta('top'))."
		";
		$this->db_query($sql);
		if (!empty($from_oid))
		{
			$imorder = array();
			while($row = $this->db_next())
			{
				$imorder[$row["oid"]] = $row["oid"];
			}
			// Maybe I should split this function in 2 instead,
			// but the mere thought of that makes my head hurt, so I'm
			// not touching this right now.
			return $imorder;
		}
		else
		{
			while ($row = $this->db_next())
			{
				$this->vars(array(
					"oid" => $row["oid"],
					"name" => $row['name'],
					"rating" => $row['val'],
					"view" => $this->_get_link($row, $ob->prop('url_to')),
					"hits" => $row['hits'],
					"place" => $cnt++
				));
				$l .= $this->parse("LINE");
			}
			$this->vars(array(
				"LINE" => $l,
				"name" => $ob->name(),
				"count" => $ob->meta('top'),
				"total" => $this->db_fetch_field("select count(*) as cnt from ratings", "cnt")
			));
			return $this->parse();
		}
	}

	function _get_link($dat, $url = "")
	{
		if ($dat["class_id"] == CL_IMAGE)
		{
			if (!isset($this->img))
			{
				$this->img = get_instance(CL_IMAGE);
			}
			return image::make_img_tag($this->img->get_url($dat['img_file']));
		}
		if (!empty($url))
		{
			return $url.$dat['oid'];
		}
		else
		{
			return $this->mk_my_orb("change", array("id" => $dat["oid"]), basename($this->classes[$dat["class_id"]]["file"]));
		}
	}
	
	/**  
		
		@attrib name=rate_popup params=name nologin="1" default="0"
		
		@param oid required type=int
		@param close optional type=int
		
		@returns
		
		
		@comment
			Content for popup window where you can rate object $oid
			Window closes after voting
	**/
	function rate_popup ($arr)
	{
		if (!empty($arr['close']))
		{
				die("
					<html><body><script language='javascript'>
						window.opener.location.reload();
						window.close();
					</script></body></html>
				");
		}
		$this->read_any_template("popup.tpl");
		// Rating, show form
		$rating_form = "";
		$o = obj($arr['oid']);
		$title = $o->name();
		$have_rating = false;
		$ro = aw_global_get('rated_objs');
	 	
		$scale_inst = get_instance(CL_RATE_SCALE);
		$scales = $scale_inst->get_scale_objs_for_obj($o->id());
		
		if (!is_array($ro) || !isset($ro[$o->id()]))
		{
			foreach ($scales as $scale)
			{
				$scale_values = $scale_inst->_get_scale($scale);
				$scale_obj = obj($scale);
				$this->vars(array(
					'rating_caption' => $scale_obj->name(),
					'rating_value' => '',
				));
				foreach ($scale_values as $num => $txt)
				{
					$this->vars(array(
						'rating_value_name' => 'rate['.$scale_obj->id().']',
						'rating_value_value' => $num,
						'rating_value_caption' => $txt,
					));
					$this->vars_merge(array('rating_value' => $this->parse('rating_value')));
					$have_rating = true;
				}
				$this->vars_merge(array('rating' => $this->parse('rating')));
			}
		}
		
		$rating_form = "";
		if ($have_rating)
		{
			$rating_form = html::submit(array(
				'value' => t("H&auml;&auml;leta"),
			));
			$hiddens = array(
				'return_url' => htmlspecialchars(aw_url_change_var('close', '1')),
				'class' => 'rate',
				'action' => 'rate',
				'oid' => $o->id(),
			);
			foreach ($hiddens as $name => $value)
			{
				$rating_form .= html::hidden(array(
					'name' => $name,
					'value' => $value,
				));
			}
		}	
		$this->vars(array(
			'rating_form_vars' => $rating_form,
			'title' => t('Hinda') . ' - ' .$title,
		));
		return $this->parse();
		
	}

	/**
	@attrib name= api=1 params=name

	@param oid required type=oid

	@param uid required type=string

	@param rate_id optional type=array(oid),oid

	**/
	function obj_rating_by_uid($arr)
	{
		extract($arr);
		$ret = array();

		if(!isset($rate_id))
		{
			$this->db_query("SELECT rate_id, rating FROM ratings WHERE oid = '$oid' AND uid = '$uid'");
			while($row = $this->db_next())
			{
				$ret[$row["rate_id"]] = $row["rating"];
			}
			return $ret;
		}
		else
		if(!is_array($rate_id))
		{
			$rate_ids = array($rate_id);
		}
		else
		{
			$rate_ids = $rate_id;
		}

		foreach($rate_ids as $rate_id)
		{
			$ret[$rate_id] = $this->db_fetch_field("SELECT rating FROM ratings WHERE oid = '$oid' AND uid = '$uid' AND rate_id = '$rate_id' ORDER BY tm DESC LIMIT 1", "rating");
		}

		return $ret;
	}
}
?>
