<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/forum/forum_topic.aw,v 1.30 2009/02/02 12:02:55 dragut Exp $
// forum_comment.aw - foorumi kommentaar
/*
@classinfo relationmgr=yes syslog_type=ST_FORUM_TOPIC no_status=1 maintainer=dragut

@tableinfo forum_topics index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property name type=textbox
	@caption Pealkiri

	@property active type=chooser table=forum_topics field=active
	@caption Aktiivne

	@property comment type=textarea
	@caption Sisu

	@property author_name type=textbox table=forum_topics field=author_name
	@caption Autori nimi

	@property author_email type=textbox table=forum_topics field=author_email
	@caption Autori e-post

	@property ip type=textbox table=forum_topics field=ip
	@caption IP

	@property locked type=checkbox ch_value=1 table=forum_topics field=locked
	@caption Teema lukus
	@comment Lukus teemale uusi kommentaare lisada ei saa

	@property answers_to_mail type=checkbox ch_value=1 store=no table=forum_topics field=answers_to_mail
	@caption Soovin vastuseid e-postiga

	@property image type=releditor reltype=RELTYPE_FORUM_IMAGE rel_id=first use_form=emb table=forum_topics field=image
	@caption Pilt

	@property image_verification type=text store=no
	@caption Kontrollkood

@groupinfo subscribers caption="Mailinglist"

	@property subscribers_editor type=releditor store=no mode=manager reltype=RELTYPE_SUBSCRIBER props=mail,name group=subscribers no_caption=1


@reltype SUBSCRIBER value=1 clid=CL_ML_MEMBER
@caption Tellija

@reltype FORUM_IMAGE value=2 clid=CL_IMAGE
@caption Pilt

*/


class forum_topic extends class_base
{
	const AW_CLID = 34;

	function forum_topic()
	{
		$this->init(array(
			"tpldir" => "forum",
			"clid" => CL_MSGBOARD_TOPIC,
		));
	}

	function callback_post_save($arr)
	{

		if($arr["request"]["answers_to_mail"] && is_email($arr["request"]["author_email"]))
		{

			$mail_addres = new object();
			//It fucking sucks, i have to save this object twice, to set mail property
			// no, you really don't - terryf
			$mail_addres->set_name($arr["request"]["author_name"]);
			$mail_addres->set_parent($arr["obj_inst"]->id());
			$mail_addres->set_class_id(CL_ML_MEMBER);
			$mail_addres->set_prop("mail", $arr["request"]["author_email"]);
			$mail_addres->save();

			$arr["obj_inst"]->connect(array(
				"to" => $mail_addres->id(),
				"reltype" => "RELTYPE_SUBSCRIBER",
			));
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "active":
				$prop["options"] = array(1 => "Jah", 0 => "Ei");
				break;
		}
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$required_fields = $arr['request']['required_fields'];
		switch($prop["name"])
		{
			case "ip":
				if (empty($prop["value"]))
				{
					$prop["value"] = aw_global_get("REMOTE_ADDR");
				};
				break;
			case "name":
				if (empty($prop["value"]) && !empty($required_fields['name']))
				{
					$prop["error"] = $prop["caption"] . " ei tohi olla t&uuml;hi!";
					$retval = PROP_FATAL_ERROR;
				};
				break;
			case "author_name":
				if (empty($prop["value"]) && !empty($required_fields['pname']))
				{
					$prop["error"] = $prop["caption"] . " ei tohi olla t&uuml;hi!";
					$retval = PROP_FATAL_ERROR;
				};
				break;
			case "author_email":
				if (!is_email($prop["value"]) && !empty($required_fields['uemail']))
				{
					$prop["error"] = $prop["caption"] . " ei ole korrektne!";
					$retval = PROP_FATAL_ERROR;
				};

				break;
			case "comment":
				if (empty($prop["value"]) && !empty($required_fields['commtext']))
				{
					$prop["error"] = $prop["caption"] . " ei tohi olla t&uuml;hi!";
					$retval = PROP_FATAL_ERROR;
				};

				// there has to be comment field in topic adding ... everything else seems to be optional
				// so i put this ip2country check here:
				if ($this->can('view', $arr['request']['forum_id']))
				{
					$forum_obj = new object($arr['request']['forum_id']);
					$uid = aw_global_get('uid');
					if ( $forum_obj->prop('limit_anonymous_posting') && empty($uid) )
					{
						$ip_locator = get_instance('core/util/ip_locator/ip_locator');
						$country = $ip_locator->search($_SERVER['REMOTE_ADDR']);
						if ($country['country_code3'] != 'EST')
						{       $prop['error'] = t('Anon&uuml;&uuml;mselt saab postitada ainult Eesti IP-lt');
							$retval = PROP_FATAL_ERROR;
						}

					}
				}
				break;
			case 'image_verification':
				if ($this->can('view', $arr['request']['forum_id']))
				{
					$forum_obj = new object($arr['request']['forum_id']);
					if ($forum_obj->prop('use_image_verification'))
					{
						$image_verification_inst = get_instance('core/util/image_verification/image_verification');
						if ( !$image_verification_inst->validate($arr['request']['ver_code']) )
						{
							$prop['error'] = t('Sisestatud kontrollnumber on vale!');
							$retval = PROP_FATAL_ERROR;
						}
					}
				}
				break;

		}
		return $retval;
	}

	////
	// !Well. Mails all the subscribers of a topic
	// id - id of the topic object
	// forum_id - id of the forum object
	// subject - subject of the message
	// message - contents of the message
	// topic_url - url to topic where comment was added
	function mail_subscribers($args = array())
	{
		$forum_obj = &obj($args["forum_id"]);
		$topic_obj = &obj($args["id"]);

		if($forum_obj->prop("mail_subject"))
		{
			$subject = $forum_obj->prop("mail_subject");
		}
		else
		{
			$subject = $topic_obj->name();
		}

		if($forum_obj->prop("mail_address") || $forum_obj->prop("mail_from"))
		{
			$from = "From:".$forum_obj->prop("mail_from")."<".$forum_obj->prop("mail_address").">\n";
		}
		else
		{
			$from = "From: automatweb@automatweb.com\n";
		}

		// composing the message:
		$message = $args['title']."\n\n";
		$message .= $args['message']."\n\n";
		$message .= $args['topic_url'];

		$targets = array();
		$targets = $topic_obj->connections_from(array(
			"type" => "RELTYPE_SUBSCRIBER",
		));
		$targets += $forum_obj->connections_from(array(
			'type' => 'RELTYPE_EMAIL'
		));
		foreach($targets as $target)
		{
			$target_obj = $target->to();
			send_mail($target_obj->prop("mail"),$subject,$message,$from);
		};

	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		
		if($tbl == "forum_topics")
		{
			if($field=="")
			{
				$this->db_query("CREATE TABLE forum_topics (`aw_oid` int primary key, `active` int, `locked` int, `answers_to_mail` int, `image` int, `author_name` varchar(200), `author_email` varchar(200))");
				$ol = new object_list(array(
					"class_id" => CL_MSGBOARD_TOPIC,
				));
				foreach($ol->arr() as $o)
				{
					$this->db_query("INSERT INTO forum_topics(`aw_oid`,`active`, `locked`, `answers_to_mail`, `image`, `author_name`, `author_email`) VALUES('".$o->id()."','".$o->meta("active")."','".$o->meta("locked")."','".$o->meta("answers_to_mail")."','".$o->meta("image")."','".$o->meta("author_name")."','".$o->meta("author_email")."')");
				}
				return true;
						
			}
			switch($field)
			{
				case "active":
				case "locked":
				case "answers_to_mail":
				case "image":
				case "jrk":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "int",
					));
					return true;
					break;
				case "author_name":
				case "author_email":
				case "ip":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "varchar(200)"
					));	
					return true;
					break;
			}
		}
	}
}
?>
