<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_jah.aw,v 1.7 2008/02/17 21:13:01 kristo Exp $
// expp_jah.aw - Expp JAH 
/*

@classinfo syslog_type=ST_EXPP_JAH relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_jah extends class_base implements main_subtemplate_handler
{
	const AW_CLID = 1015;

	
	var $yahLinks = '';
	var $yahEnd = '';
	var $yahTingimused = '';
	var $tingimusedURL = 0;
	var $tingmusedText = '';

	function expp_jah() {
		$this->init(array(
			"tpldir" => "automatweb/menuedit",
			"clid" => CL_EXPP_JAH
		));
	}

	function show($arr) {
		$retHTML = '';
		return $retHTML;
	}

	function parseLinks($arr) {
		$last_link = '';
		$count = count( $GLOBALS['jahX'] );
		if( $count == 0 ) return;
		$count--;

		$this->read_template("main.tpl");
		for( $i = 0; $i < $count; $i++ ) {
			$last_link .= '/'.$GLOBALS['jahX'][$i]['link'];
			if ( $i == 0 ) continue;
			$this->vars( array(
					'link' => $last_link,
					'text' => $GLOBALS['jahX'][$i]['text'],
				));
			$this->yahLinks .= $this->parse( 'JAH_LINK' );
		}
		$last_link .= '/'.$GLOBALS['jahX'][$count]['link'];
		$this->vars( array(
				'link' => $last_link,
				'text' => $GLOBALS['jahX'][$count]['text'],
			));
		$this->yahEnd = $this->parse( 'JAH_LINK_END' );
		
		if ( $this->tingimusedURL > 0 && !empty( $this->tingmusedText )) {
			$this->vars( array(
					'link' => $this->tingimusedURL,
					'text' => $this->tingmusedText,
				));
			$this->yahTingimused = $this->parse( 'JAH_TINGIMUSED' );
		}
	}

	function on_get_subtemplate_content($arr) {
		$this->parseLinks( $arr );
		$arr["inst"]->vars(array(
			"JAH_LINK" => $this->yahLinks,
			"JAH_LINK_END" => $this->yahEnd,
			"JAH_TINGIMUSED" => $this->yahTingimused,
		));
	}
	
	function addLink( $arr ) {
		$GLOBALS['jahX'][] = $arr;
		return $this->getURL();
	}
	
	function getURL() {
		$retVal = '/';
		foreach( $GLOBALS['jahX'] as $val ) {
			$retVal .= $val['link'].'/';
		}
		return $retVal;
	}
}
?>
