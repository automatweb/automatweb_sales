<?php

class bugtrack extends core
{
	function bugtrack()
	{
		$this->init();
	}

        /**

                @attrib name=add_error params=name nologin="1" default="0"

                @param site_url required
                @param err_type required
                @param err_msg required
                @param err_content required
                @param err_uid required

                @returns


                @comment

        **/
        function add_error($arr)
        {
                $this->quote($arr);
                extract($arr);

		$this->db_query("INSERT INTO bugtrack_errors (type_id,message,site,content,tm,err_uid)
				VALUES ('$err_type','$err_msg','$site_url','$err_content','".time()."','$err_uid')");
		die();
	}
}

