<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/mp3player/mass_mp3_upload.aw,v 1.2 2007/12/06 14:33:42 kristo Exp $
// mass_mp3_upload.aw - Impordi minu MP3d 
/*

@classinfo syslog_type=ST_MASS_MP3_UPLOAD relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

@property importfolder type=relpicker reltype=RELTYPE_IMPORTFOLDER field=meta method=serialize
@caption Impordikaust

@property do_import type=checkbox store=no ch_value=1
@caption Impordi

@groupinfo import_list caption="Nimekiri imporditavatest failidest"
@default group=import_list

	@property import_list_table type=table store=no no_caption=1

@reltype IMPORTFOLDER value=1 clid=CL_MENU
@caption Impordi kaust
*/

class mass_mp3_upload extends class_base
{
	function mass_mp3_upload()
	{
		$this->init(array(
			"tpldir" => "applications/mp3player",
			"clid" => CL_MASS_MP3_UPLOAD
		));
		$this->s_upload_dir = aw_ini_get("site_basedir")."/public/upload";
		$this->walk_dir( $this->s_upload_dir, true );
		$this->a_files;
	}
	
	function _set_do_import($arr)
	{
		if ($arr["prop"]["value"]==1)
		{
			$this->do_import($arr);
		}
	}
	
	function do_import($arr)
	{
		classload("applications/mp3player/mp3");
		
		$o =$arr["obj_inst"];
		
		$conns = $o->connections_from(array(
			 	"class" => CL_MENU,
		));
		$conn = array_pop($conns);
		$i_import_parent = $conn->conn["to"];
		
		$a_keys = array_keys($this->a_files);
		for ($i=0;$i<count($a_keys);$i++)
		{
			mp3::new_mp3($i_import_parent, $a_keys[$i], $this->a_files[$a_keys[$i]]["relative_path"]);
		}
	}
	
	function _get_import_list_table($arr)
	{
		$table =& $arr["prop"]["vcl_inst"];
		
		$table->define_field(array(
			"name" => "relative_path",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "left",
		));
		
		$a_keys = array_keys($this->a_files);
		for ($i=0;$i<count($a_keys);$i++)
        {
			$table->define_data(array(
				"relative_path" => $this->a_files[$a_keys[$i]]["relative_path"],
			));
        }
		
		$table->define_pageselector(array(
				"type"=>"lb",
				"records_per_page"=>100,
				"position"=>"both",
		));
	}
	
	// Walk a directory recursivelly, and apply a callback on each file
	function walk_dir( $root, $recursive = true ) 
	{
	    $dh = @opendir( $root );
	    if( false === $dh )
		{
	        return false;
	    }
	    while( $file = readdir( $dh ))
		{
	        if( "." == $file || ".." == $file )
			{
	            continue;
	        }
			$this->handle_file("{$root}/{$file}");
	        if( false !== $recursive && is_dir( "{$root}/{$file}" ))
			{
	            $this->walk_dir( "{$root}/{$file}", $recursive );
	        }
	    }
	    closedir( $dh );
	    return true;
	}
	
	function handle_file( $path )
	{
	    if( !is_dir( $path )) {
			if (end(explode(".", $path))=="mp3")
			{
				$relative_path = str_replace( $this->s_upload_dir."/", "", $path);
				$this->a_files[$path] = array(
					"relative_path"=>$relative_path,
				);
			}
	    }
	}
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
