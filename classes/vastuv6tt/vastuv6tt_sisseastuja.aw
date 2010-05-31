<?php

namespace automatweb;
// vastuv6tt_sisseastuja.aw - Sisseastuja
/*

@classinfo syslog_type=ST_VASTUV6TT_SISSEASTUJA relationmgr=yes maintainer=voldemar
@tableinfo vastuv6tt_sisseastuja index=oid master_table=objects master_index=oid

@groupinfo grp_sisseastuja_avaldused caption="Avaldused ja hinded"
@groupinfo grp_sisseastuja_andmed caption="Andmed"
@groupinfo grp_sisseastuja_katsetulemused caption="Katsetulemused"


@default table=vastuv6tt_sisseastuja
@default group=general


@property oppetase type=hidden
@property kustutatud type=hidden

@property title1 type=text subtitle=1 store=no
@property title2 type=text subtitle=1 store=no
@property title3 type=text subtitle=1 store=no
@property title4 type=text subtitle=1 store=no
@property title5 type=text subtitle=1 store=no
@property title6 type=text subtitle=1 store=no
@property title7 type=text subtitle=1 store=no
@property title8 type=text subtitle=1 store=no


@default group=grp_sisseastuja_avaldused

@property andmete_kinnitus type=checkbox table=objects field=meta method=serialize ch_value=1
@caption Kinnitan andmed (peale andmete kinnitamist ja seej&auml;rel salvestamist ei saa neid piiratud &otilde;igustega kasutaja enam muuta.)



//nimi
@property isik_firstname type=textbox maxlength=50
@caption Eesnimi

@property isik_lastname type=textbox maxlength=50
@caption Perekonnanimi

//// AVALDUSED
@property keskkond_tagasilink type=text subtitle=1 store=no

@property avaldused_toolbar type=toolbar no_caption=1 store=no
@property avaldused_toolbar_laud type=toolbar no_caption=1 store=no
@property avaldused_tbl type=table no_caption=1 store=no
@property avaldused_tbl_laud type=table no_caption=1 store=no

@property avaldus_title type=text subtitle=1 store=no

// @property sisseastumisavaldused_b type=releditor reltype=RELTYPE_AVALDUS store=no props=eriala,eriala_b,oppevorm,oppetase,eelistus,sisseastuja_nr,isik_firstname,isik_lastname no_caption=1

// @property sisseastumisavaldused_m type=releditor reltype=RELTYPE_AVALDUS store=no props=eriala,eriala_m,oppevorm,oppetase,eelistus,sisseastuja_nr,isik_firstname,isik_lastname no_caption=1

@property sisseastumisavaldused type=releditor reltype=RELTYPE_AVALDUS store=no props=eriala,oppevorm,oppetase,eelistus,sisseastuja_nr,isik_firstname,isik_lastname no_caption=1

// konkursilehed
@property konkursilehed_tbl type=table no_caption=1 store=no
@property konkursileht_prinditud type=hidden table=objects field=meta method=serialize


@property nimi_title type=text subtitle=1 store=no

@property oppekeel_sep type=text subtitle=1 store=no
@property oppekeel type=hidden
@property oppekeel_title type=text store=no
@caption &otilde;ppekeel keskkoolis
@property oppekeel_sel type=chooser parent=oppekeel_title no_caption=1 store=no
@property oppekeel_txt type=textbox parent=oppekeel_title store=no
@caption Muu

@property synniaeg type=hidden table=vastuv6tt_sisseastuja field=synniaeg


// HINDED

//// RIIGIEKSAMITE HINDED

@property riigieksamid_title type=text subtitle=1 store=no
@caption Riigieksamite hinded


@property ex_kirjand type=text store=no
@caption Kirjand
@property ex_kirjand_aasta type=select parent=ex_kirjand datatype=int
@caption aasta
@property ex_kirjand_hinne type=textbox parent=ex_kirjand size=3 datatype=int
@caption hinne

@property ex_ingl type=text store=no
@caption Inglise keel
@property ex_ingl_aasta type=select parent=ex_ingl datatype=int
@caption aasta
@property ex_ingl_hinne type=textbox parent=ex_ingl size=3 datatype=int
@caption hinne

@property ex_sks type=text store=no
@caption Saksa keel
@property ex_sks_aasta type=select parent=ex_sks datatype=int
@caption aasta
@property ex_sks_hinne type=textbox parent=ex_sks size=3 datatype=int
@caption hinne

@property ex_pr type=text store=no
@caption Prantsuse keel
@property ex_pr_aasta type=select parent=ex_pr datatype=int
@caption aasta
@property ex_pr_hinne type=textbox parent=ex_pr size=3 datatype=int
@caption hinne

@property ex_eesti type=text store=no
@caption Eesti keel teise keelena
@property ex_eesti_aasta type=select parent=ex_eesti datatype=int
@caption aasta
@property ex_eesti_hinne type=textbox parent=ex_eesti size=3 datatype=int
@caption hinne

@property ex_vene type=text store=no
@caption Vene keel v&otilde;&otilde;rkeelena
@property ex_vene_aasta type=select parent=ex_vene datatype=int
@caption aasta
@property ex_vene_hinne type=textbox parent=ex_vene size=3 datatype=int
@caption hinne

@property ex_yhisk type=text store=no
@caption &uuml;hiskonnapetus
@property ex_yhisk_aasta type=select parent=ex_yhisk datatype=int
@caption aasta
@property ex_yhisk_hinne type=textbox parent=ex_yhisk size=3 datatype=int
@caption hinne

@property ex_ajalugu type=text store=no
@caption Ajalugu
@property ex_ajalugu_aasta type=select parent=ex_ajalugu datatype=int
@caption aasta
@property ex_ajalugu_hinne type=textbox parent=ex_ajalugu size=3 datatype=int
@caption hinne

@property ex_bio type=text store=no
@caption Bioloogia
@property ex_bio_aasta type=select parent=ex_bio datatype=int
@caption aasta
@property ex_bio_hinne type=textbox parent=ex_bio size=3 datatype=int
@caption hinne

@property ex_fyysika type=text store=no
@caption F&uuml;&uuml;sika
@property ex_fyysika_aasta type=select parent=ex_fyysika datatype=int
@caption aasta
@property ex_fyysika_hinne type=textbox parent=ex_fyysika size=3 datatype=int
@caption hinne

@property ex_keemia type=text store=no
@caption Keemia
@property ex_keemia_aasta type=select parent=ex_keemia datatype=int
@caption aasta
@property ex_keemia_hinne type=textbox parent=ex_keemia size=3 datatype=int
@caption hinne

@property ex_mat type=text store=no
@caption Matemaatika
@property ex_mat_aasta type=select parent=ex_mat datatype=int
@caption aasta
@property ex_mat_hinne type=textbox parent=ex_mat size=3 datatype=int
@caption hinne

@property ex_geo type=text store=no
@caption Geograafia
@property ex_geo_aasta type=select parent=ex_geo datatype=int
@caption aasta
@property ex_geo_hinne type=textbox parent=ex_geo size=3 datatype=int
@caption hinne

//// keskhinne
@property keskhinne type=hidden
@property keskhinne_text type=text store=no
@caption Keskmine hinne

//// KESKKOOLI HINDED
@property hinded_title_b type=text subtitle=1 store=no
@caption Hinded keskharidust t&otilde;endaval dokumendil

@property hinded_title_m type=text subtitle=1 store=no
@caption Hinded akadeemilisel &otilde;iendil

@property hinnete_arvud type=text store=no
@caption Hinnete arvud

@property hinnete_arvud2 type=text store=no
@caption &nbsp;

@property kk_hinne_5 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption viisi
@property kk_hinne_4 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption neljasid
@property kk_hinne_3 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption kolmesid
@property kk_hinne_2 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption kahtesid

//// K6RGKOOLI HINDED

@property ak_hinne_5 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption 5
@property ak_hinne_4 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption 4
@property ak_hinne_3 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption 3
@property ak_hinne_2 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption 2
@property ak_hinne_1 type=textbox parent=hinnete_arvud size=2 datatype=int
@caption 1

@property ak_hinne_a type=textbox parent=hinnete_arvud2 size=2 datatype=int
@caption A
@property ak_hinne_b type=textbox parent=hinnete_arvud2 size=2 datatype=int
@caption B
@property ak_hinne_c type=textbox parent=hinnete_arvud2 size=2 datatype=int
@caption C
@property ak_hinne_d type=textbox parent=hinnete_arvud2 size=2 datatype=int
@caption D
@property ak_hinne_e type=textbox parent=hinnete_arvud2 size=2 datatype=int
@caption E

@property ak_hinne_l6put88 type=textbox size=2
@caption L&otilde;put&ouml;&ouml;/eksami hinne




@default group=grp_sisseastuja_andmed


@property sisseastuja_ylevaade type=text no_caption=1 store=no wrapchildren=1 editonly=1

//// SISSEASTUJA INFO
@property sisseastuja_title type=text subtitle=1 store=no
@caption Sisseastuja info

@property v66rkeel type=select
@caption Kohustuslik v&otilde;&otilde;rkeel

@property sisseastuja_nr type=hidden
@property sisseastuja_nr_title type=text store=no
@caption Sisseastuja number

@property tulemuste_teavitusviis type=chooser
@caption Konkursitulemuste teavitamise viis


//// ISIKUANDMED
@property isik_title type=text subtitle=1 store=no
@caption Isikuandmed

@property isik_firstname2 type=textbox maxlength=50 store=no
@caption Eesnimi

@property isik_lastname2 type=textbox maxlength=50 store=no
@caption Perekonnanimi

@property isik_personal_id type=textbox
@caption Isikukood

@property isik_gender type=chooser
@caption Sugu

@property isik_birthday type=date_select year_from=1930 year_to=1988 save_format=iso8601

@caption S&uuml;nniaeg

@property isik_social_status type=chooser default=Vallaline
@caption Perekonnaseis




@property emails type=textbox
@caption E-posti aadressid

@property phones type=textbox
@caption Telefoninumbrid


@property t88koht type=textarea
@caption Sisseastuja t&ouml;&ouml;koht, amet
@property t88tel type=textbox
@caption Telefon t&ouml;&ouml;kohas
@property laste_arv type=textbox size=2 datatype=int
@caption Laste arv
@property elukoht type=chooser multiple=0
@caption Alaline elukoht
@property kodakondsus type=select
@caption Kodakondsus
@property elamisluba type=select
@caption Elamisluba
@property elukohamaa type=select
@caption Elukohamaa

@property haridus_kood type=textbox size=3 default=310 datatype=int
@caption Eelnevalt omandatud haridusastme kood
@property haridus_aasta type=textbox size=4 datatype=int
@caption Eelmise haridusasutuse l&otilde;petamise aasta
@property haridus_v2lismaal type=select
@caption Haridus omandatud v&auml;lismaal
@property haridus_medal type=select
@caption L&otilde;petatud medaliga

@property haridus_k6rgkool type=textbox
@caption Eelnevalt l&otilde;petatud k&otilde;rgkool

@property haridus_kool_eriala type=textbox
@caption L&otilde;petatud eriala

@property kool_title type=text subtitle=1 store=no
@caption Kool, mille l&otilde;putunnistus esitatud
@property haridus_kool_tyyp type=select
@caption Kooli t&uuml;&uuml;p
@property haridus_kool_aasta type=textbox size=4 datatype=int
@caption L&otilde;petamise aasta
@property haridus_kool_kood type=textbox size=4 datatype=int
@caption Kooli kood
@property haridus_kool_6ppevorm type=select
@caption &otilde;ppevorm koolis

// aadress


@property aadress_kood type=textbox size=2 datatype=int
@caption Maakonna/linna kood
@property aadress_t2nav type=textbox
@caption T&auml;nav, maja, korter/k&uuml;la
@property aadress_sjsk type=textbox
@caption Sidejaoskond
@property aadress_indeks type=textbox
@caption Indeks

@property aadress_linn type=hidden
@property aadress_linn_title type=text store=no
@caption Linn
@property aadress_linn_sel type=select store=no no_caption=1 parent=aadress_linn_title
@property aadress_linn_txt type=textbox store=no parent=aadress_linn_title
@caption Muu

@property aadress_maakond type=hidden
@property aadress_maakond_title type=text store=no
@caption Maakond
@property aadress_maakond_sel type=select parent=aadress_maakond_title no_caption=1 store=no
@property aadress_maakond_txt type=textbox parent=aadress_maakond_title store=no
@caption Muu



// pere
@property pere_isa type=text subtitle=1 store=no
@caption Isa
@property pere_isa_nimi type=textbox
@caption Nimi
@property pere_isa_tel type=textbox
@caption Telefon
@property pere_isa_aadress type=textarea
@caption Aadress

@property pere_ema type=text subtitle=1 store=no
@caption Ema
@property pere_ema_nimi type=textbox
@caption Nimi
@property pere_ema_tel type=textbox
@caption Telefon
@property pere_ema_aadress type=textarea
@caption Aadress

@property pere_abikaasa type=text subtitle=1 store=no
@caption Abikaasa
@property pere_abikaasa_nimi type=textbox
@caption Nimi
@property pere_abikaasa_tel type=textbox
@caption Telefon
@property pere_abikaasa_aadress type=textarea
@caption Aadress



//// KATSETULEMUSED
@property katsed_title type=text subtitle=1 store=no
@caption Sisseastumiskatsete tulemused

@property tulemus_ek type=textbox size=3 datatype=int
@caption Eesti keele test

@property tulemus_kk type=textbox size=3 datatype=int
@caption Maastikuarhitektuuri joonistuseksam

@property tulemus_vk type=textbox size=3 datatype=int
@caption Maastikuarhitektuuri erialatest

// @property tulemus_vl type=textbox size=3 datatype=int
// @caption Liha- ja piimatehnoloogia eriala vestlus

@property tulemus_vm type=textbox size=3 datatype=int
@caption Maastikukaitse- ja hoolduse eriala vestlus

// @property tulemus_vv type=textbox size=3 datatype=int
// @caption Veterinaarmeditsiini eriala vestlus

// @property tulemus_vr type=textbox size=3 datatype=int
// @caption Rakendush&uuml;drobioloogia eriala vestlus



// --------------- RELTYPES ---------------------

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Isikuandmed

@reltype AVALDUS value=2 clid=CL_VASTUV6TT_AVALDUS
@caption Avaldus

*/

/*

CREATE TABLE `vastuv6tt_sisseastuja` (
	`oid` int(11) NOT NULL default '0',
	`kustutatud` int(1) NOT NULL default '0',

	`sisseastuja_nr` int(4) ZEROFILL NOT NULL default '0' AUTO_INCREMENT,
	`oppetase` enum ('B','M','D','O','A') default 'B',
	`v66rkeel` enum ('I','S','V') default 'I',
	`oppekeel` varchar(100) default NULL,
	`t88koht` text default NULL,
	`t88tel` tinytext default NULL,
	`laste_arv` int(2) unsigned default NULL,
	`elukoht` enum ('L','M') default 'L',
	`kodakondsus` varchar(200) default 'Eesti',
	`elamisluba` enum ('A','T') default 'A',
	`elukohamaa` varchar(200) default NULL,
	!!!`tulemuste_teavitusviis` char(1) default NULL,

	`isik_firstname` varchar(50) default NULL,
	`isik_lastname` varchar(50) default NULL,
	`isik_gender` varchar(10) default NULL,
	`isik_personal_id` varchar(20) default NULL,
	`isik_birthday` varchar(20) default NULL,
	`isik_social_status` varchar(20) default NULL,
	`phones` varchar(50) default NULL,
	`emails` varchar(120) default NULL,

	`aadress_kood` int(2) unsigned default NULL,
	`aadress_t2nav` text default NULL,
	`aadress_sjsk` varchar(200) default NULL,
	`aadress_indeks` varchar(20) default NULL,
	`aadress_linn` text default NULL,
	`aadress_maakond` text default NULL,

	`haridus_kood` int(3) unsigned default NULL,
	`haridus_aasta` int(4) unsigned default NULL,
	`haridus_k6rgkool` varchar(200) default NULL,
	`haridus_v2lismaal` enum ('E','J') default 'E',
	`haridus_medal` enum ('E','M','H','K') default 'E',
	`haridus_kool_tyyp` enum ('G','KU','KK','T') default 'KK',
	`haridus_kool_aasta` int(4) unsigned default NULL,
	`haridus_kool_kood` int(4) unsigned default NULL,
	`haridus_kool_6ppevorm` enum ('P','O','K','E') default 'P',
	!!!`haridus_kool_eriala` varchar(40) default NULL,

	`pere_isa_nimi` varchar(255) default NULL,
	`pere_isa_tel` varchar(40) default NULL,
	`pere_isa_aadress` text default NULL,
	`pere_ema_nimi` varchar(255) default NULL,
	`pere_ema_tel` varchar(40) default NULL,
	`pere_ema_aadress` text default NULL,
	`pere_abikaasa_nimi` varchar(255) default NULL,
	`pere_abikaasa_tel` varchar(40) default NULL,
	`pere_abikaasa_aadress` text default NULL,

	`ex_kirjand_aasta` int(4) unsigned default NULL,
	`ex_kirjand_hinne` int(3) unsigned default NULL,
	`ex_ingl_aasta` int(4) unsigned default NULL,
	`ex_ingl_hinne` int(3) unsigned default NULL,
	`ex_sks_aasta` int(4) unsigned default NULL,
	`ex_sks_hinne` int(3) unsigned default NULL,
	`ex_pr_aasta` int(4) unsigned default NULL,
	`ex_pr_hinne` int(3) unsigned default NULL,
	`ex_eesti_aasta` int(4) unsigned default NULL,
	`ex_eesti_hinne` int(3) unsigned default NULL,
	`ex_vene_aasta` int(4) unsigned default NULL,
	`ex_vene_hinne` int(3) unsigned default NULL,
	`ex_yhisk_aasta` int(4) unsigned default NULL,
	`ex_yhisk_hinne` int(3) unsigned default NULL,
	`ex_ajalugu_aasta` int(4) unsigned default NULL,
	`ex_ajalugu_hinne` int(3) unsigned default NULL,
	`ex_bio_aasta` int(4) unsigned default NULL,
	`ex_bio_hinne` int(3) unsigned default NULL,
	`ex_fyysika_aasta` int(4) unsigned default NULL,
	`ex_fyysika_hinne` int(3) unsigned default NULL,
	`ex_keemia_aasta` int(4) unsigned default NULL,
	`ex_keemia_hinne` int(3) unsigned default NULL,
	`ex_mat_aasta` int(4) unsigned default NULL,
	`ex_mat_hinne` int(3) unsigned default NULL,
	`ex_geo_aasta` int(4) unsigned default NULL,
	`ex_geo_hinne` int(3) unsigned default NULL,

	`keskhinne` float(6) unsigned default NULL,
	`kk_hinne_5` int(3) unsigned default NULL,
	`kk_hinne_4` int(3) unsigned default NULL,
	`kk_hinne_3` int(3) unsigned default NULL,
	`kk_hinne_2` int(3) unsigned default NULL,
	`ak_hinne_5` int(3) unsigned default NULL,
	`ak_hinne_4` int(3) unsigned default NULL,
	`ak_hinne_3` int(3) unsigned default NULL,
	`ak_hinne_2` int(3) unsigned default NULL,
	`ak_hinne_1` int(3) unsigned default NULL,
	`ak_hinne_a` int(3) unsigned default NULL,
	`ak_hinne_b` int(3) unsigned default NULL,
	`ak_hinne_c` int(3) unsigned default NULL,
	`ak_hinne_d` int(3) unsigned default NULL,
	`ak_hinne_e` int(3) unsigned default NULL,
	!!!`ak_hinne_l6put88` int(3) unsigned default NULL,

	`tulemus_ek` int(3) unsigned default NULL,
	`tulemus_kk` int(3) unsigned default NULL,
	`tulemus_vk` int(3) unsigned default NULL,
	`tulemus_vl` int(3) unsigned default NULL,
	`tulemus_vm` int(3) unsigned default NULL,
	`tulemus_vv` int(3) unsigned default NULL,
	`tulemus_vr` int(3) unsigned default NULL,

	PRIMARY KEY  (`oid`),
	KEY  `sisseastuja_nr` (`sisseastuja_nr`),
	UNIQUE KEY `oid` (`oid`)
) TYPE=MyISAM;

*/

class vastuv6tt_sisseastuja extends class_base
{
	const AW_CLID = 338;

	function vastuv6tt_sisseastuja()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "vastuv6tt",
			"clid" => CL_VASTUV6TT_SISSEASTUJA
		));
	}

	function callback_on_load($arr)
	{
		// bakalaureuse ja kraadi6ppe erinevad vormid
		$sisseastuja = obj ($arr["request"]["id"]);
		foreach ($sisseastuja->connections_to() as $connection)
		{
			$clid = $connection->prop("from.class_id");

			if ($clid == CL_VASTUV6TT_KESKKOND)
			{
				$keskkond = obj($connection->prop("from"));
				break;
			}
		}

		if (!$keskkond)
		{
			return;
		}

		switch ($sisseastuja->prop("oppetase"))
		{
			case "B":
				if ($sisseastuja->prop("andmete_kinnitus"))
				{
					$seadete_haldur = $keskkond->prop ("bakalaureuse_seaded_piiratud");
				}
				else
				{
					$seadete_haldur = $keskkond->prop ("bakalaureuse_seaded");
				}
			break;

			case "M":
				if ($sisseastuja->prop("andmete_kinnitus"))
				{
					$seadete_haldur = $keskkond->prop ("magistri_seaded_piiratud");
				}
				else
				{
					$seadete_haldur = $keskkond->prop ("magistri_seaded");
				}
			break;

			case "A":
				if ($sisseastuja->prop("andmete_kinnitus"))
				{
					$seadete_haldur = $keskkond->prop ("magistri_seaded32_piiratud");
				}
				else
				{
					$seadete_haldur = $keskkond->prop ("magistri_seaded32");
				}
			break;

			case "D":
				if ($sisseastuja->prop("andmete_kinnitus"))
				{
					$seadete_haldur = $keskkond->prop ("doktori_seaded_piiratud");
				}
				else
				{
					$seadete_haldur = $keskkond->prop ("doktori_seaded");
				}
			break;

			case "O":
				if ($sisseastuja->prop("andmete_kinnitus"))
				{
					$seadete_haldur = $keskkond->prop ("opetaja_seaded_piiratud");
				}
				else
				{
					$seadete_haldur = $keskkond->prop ("opetaja_seaded");
				}
			break;
		}

		aw_session_set ("vastuv6tt_oppetase", $sisseastuja->prop("oppetase"));
		$this->cfgmanager = $seadete_haldur;
		// END bakalaureuse ja kraadi6ppe erinevad vormid
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$sisseastuja =& $arr['obj_inst'];
		$retval = PROP_OK;
		$oppetase = $sisseastuja->prop("oppetase");
		$oppetase_l = $this->get_oppetase_l ($oppetase);
		$vastuv6tt_keskkond = get_instance (CL_VASTUV6TT_KESKKOND);

		foreach ($sisseastuja->connections_to() as $connection)
		{
			$clid = $connection->prop("from.class_id");

			if ($clid == CL_VASTUV6TT_KESKKOND)
			{
				$keskkond = obj($connection->prop("from"));
				break;
			}
		}

		if ( (is_numeric ($arr["request"]["sisseastumisavaldused"])) || (is_numeric ($arr["request"]["sisseastumisavaldused_m"])) )
		{
			if ( ($data["name"] == "sisseastumisavaldused") || ($data["name"] == "sisseastumisavaldused_m") || ($data["name"] == "avaldus_title") || ($data["name"] == "avaldused_tbl") )
			{
				$releditor_avaldus_id = $arr["request"][$data["name"]];
				$this->avaldused_editor = "2";
			}
			else
			{
				return PROP_IGNORE;
			}
		}
		elseif ( ($arr["request"]["sisseastumisavaldused"] == "new") || ($arr["request"]["sisseastumisavaldused_m"] == "new") )
		{
			if ( ($data["name"] == "sisseastumisavaldused") || ($data["name"] == "sisseastumisavaldused_m") || ($data["name"] == "avaldus_title") || ($data["name"] == "avaldused_tbl") )
			{
				$releditor_avaldus_id = "new";
				$this->avaldused_editor = "1";
			}
			else
			{
				return PROP_IGNORE;
			}
		}
		else
		{
			$this->avaldused_editor = "0";
			$releditor_avaldus_id = false;
		}

		if ($this->avaldused_editor)
		{
			$avaldused = array ();

			foreach ($sisseastuja->connections_from(array("type" => RELTYPE_AVALDUS)) as $connection)
			{
				$avaldus = $connection->to();
				$avaldused[] = $avaldus->id();
			}

			$avaldused = implode ("|", $avaldused);
			aw_session_set ("sisseastuja_avaldused_ids", $avaldused);
		}


		switch($data["name"])
		{
				case "synniaeg":
					list($year,$month,$day) = explode("-",$arr["obj_inst"]->prop("isik_birthday"));
				    $data["value"] = sprintf("%d.%d.%d",$day,$month,$year);
					#$data["value"] = date("d.m.Y", $arr["obj_inst"]->prop("isik_birthday"));
					break;

				case "sisseastuja_nr_title":
					$data["value"] = sprintf("%04d", $arr["obj_inst"]->prop("sisseastuja_nr"));
					break;

				case "avaldus_title":
				if (!$releditor_avaldus_id)
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					if (  (is_numeric ($arr["request"]["sisseastumisavaldused"])) || (is_numeric ($arr["request"]["sisseastumisavaldused_m"]))  )
					{
						$data["value"] = "Muuda avaldust";
					}
					else
					{
						$data["value"] = "Vali eriala";
					}
				}
				break;

 			case "nimi_title":
				$data["value"] = "Sisseastuja " . $sisseastuja->prop("isik_firstname") . " " . $sisseastuja->prop("isik_lastname") . " (nr: " . sprintf("%04d",$sisseastuja->prop("sisseastuja_nr")) . ", isikukood: " . $sisseastuja->prop("isik_personal_id") . ")";
				break;

			case "tulemuste_teavitusviis":
				$options = array(
					"P" => "Kirjaga",
					"E" => "E-postiga",
				);
				$data["options"] = $options;
				break;

 			case "keskkond_tagasilink":
				$returnURI = $this->mk_my_orb("change", array(
					"id" => $keskkond->id (),
					"group" => "grp_sisseastuja",
				), "vastuv6tt_keskkond");
				$data["value"] = '<span style="display: block; text-align: right; font-size: 16px; padding-right: 5px;"><a href="' . $returnURI . '">Tagasi uue sisseastuja lisamisele</a></span>';
				break;

			case "oppetase":
				$data["options"] = $vastuv6tt_keskkond->get_trans("oppetase");
				break;

			case "ex_kirjand_aasta":
			case "ex_ingl_aasta":
			case "ex_sks_aasta":
			case "ex_pr_aasta":
			case "ex_eesti_aasta":
			case "ex_vene_aasta":
			case "ex_yhisk_aasta":
			case "ex_ajalugu_aasta":
			case "ex_bio_aasta":
			case "ex_fyysika_aasta":
			case "ex_keemia_aasta":
			case "ex_mat_aasta":
			case "ex_geo_aasta":
				for ($aasta = date("Y"); $aasta > 1996; $aasta--)
				{
					$data["options"][$aasta] = $aasta;
				}
				break;


			case "keskhinne_text":
				$data["value"] = ( $sisseastuja->prop("keskhinne") ? $sisseastuja->prop("keskhinne") : "M&auml;&auml;ramata" );
				break;

			case "oppekeel_sep":
				$data["value"] = "&nbsp;";
				break;

			case "oppekeel_sel":
				$oppekeel = $sisseastuja->prop("oppekeel");
				$options = array(
					"Eesti" => "Eesti",
					"Vene" => "Vene",
				);
				$data["options"] = $options;

				if (in_array ($oppekeel, $options))
				{
					$data["value"] = $oppekeel;
				}
				else
				{
					$data["value"] = "";
				}
				break;

			case "oppekeel_txt":
				$oppekeel = $sisseastuja->prop("oppekeel");
				$options = array(
					"Eesti" => "Eesti",
					"Vene" => "Vene",
				);

				if (in_array ($oppekeel, $options))
				{
					$data["value"] = "";
				}
				else
				{
					$data["value"] = $oppekeel;
				}
				break;

			case "isik_firstname2":
				$data["value"] = $sisseastuja->prop('isik_firstname');
				break;

			case "isik_lastname2":
				$data["value"] = $sisseastuja->prop('isik_lastname');
				break;

			case "isik_social_status":
				$data["options"] = $vastuv6tt_keskkond->get_trans("social_status");
				break;

			case "isik_gender":
				$data["options"] = array(
					"1" => "mees",
					"2" => "naine",
				);
				break;

			case "v66rkeel":
				$data["options"] = $vastuv6tt_keskkond->get_trans("v66rkeel");
				break;

			case "elukoht":
				$data["options"] = $vastuv6tt_keskkond->get_trans("elukoht");
				break;

			case "kodakondsus":
				$options = array(
					"Eesti" => "Eesti",
					"Kodakondsuseta" => "Kodakondsuseta",
				);
				$options =  $options + $this->kohad("riigid");
				$data["options"] = $options;
				break;

			case "elamisluba":
				$data["options"] = $vastuv6tt_keskkond->get_trans("elamisluba");
				break;

			case "elukohamaa":
				$options = array(
					"-" => "",
				);
				$options =  $options + $this->kohad("riigid");
				$data["options"] = $options;
				break;


//aadress
			case "aadress_maakond_sel":
				$maakond = $sisseastuja->prop("aadress_maakond");
				$options = $this->kohad("maakonnad");

				if (in_array ($maakond, $options))
				{
					$data["value"] = $maakond;
				}
				else
				{
					$data["value"] = "";
				}

				$data["options"] = array ("-" => "") + $options;
				break;

			case "aadress_maakond_txt":
				$maakond = $sisseastuja->prop("aadress_maakond");
				$options = $this->kohad("maakonnad");

				if ( (in_array ($maakond, $options)) || ($maakond == "-") )
				{
					$data["value"] = "";
				}
				else
				{
					$data["value"] = $maakond;
				}
				break;

			case "aadress_linn_sel":
				$linn = $sisseastuja->prop("aadress_linn");
				$options = $this->kohad("linnad");

				if (in_array ($linn, $options))
				{
					$data["value"] = $linn;
				}
				else
				{
					$data["value"] = "";
				}

				$data["options"] = array ("-" => "") + $options;
				break;

			case "aadress_linn_txt":
				$linn = $sisseastuja->prop("aadress_linn");
				$options = $this->kohad("linnad");

				if ( (in_array ($linn, $options)) || ($linn == "-") )
				{
					$data["value"] = "";
				}
				else
				{
					$data["value"] = $linn;
				}
			break;
//END aadress


//haridus
			case "haridus_v2lismaal":
				$data["options"] = $vastuv6tt_keskkond->get_trans("haridus_v2lismaal");
				break;

			case "haridus_medal":
				$data["options"] = $vastuv6tt_keskkond->get_trans("haridus_medal");
				break;

			case "haridus_kool_tyyp":
				$data["options"] = $vastuv6tt_keskkond->get_trans("haridus_kool_tyyp");
				break;

			case "haridus_kool_6ppevorm":
				$data["options"] = $vastuv6tt_keskkond->get_trans("haridus_kool_6ppevorm");
				break;
// END haridus

			case "sisseastumisavaldused":
			case "sisseastumisavaldused_m":

				// uued avaldused 6igesse kohta
				foreach ($sisseastuja->connections_to() as $connection)
				{
					if ($connection->prop("from.class_id") == CL_VASTUV6TT_KESKKOND)
					{
						$keskkond = obj ($connection->prop("from"));
						break;
					}
				}

				$avalduste_kaust = $keskkond->prop ("avalduste_kaust");

				foreach ($sisseastuja->connections_from(array("type" => RELTYPE_AVALDUS)) as $connection)
				{
					$avaldus = $connection->to();

					if ($avaldus->parent() != $avalduste_kaust)
					{
						$avaldus->set_parent($avalduste_kaust);
						$avaldus->save();
					}
				}
				// END uued avaldused 6igesse kohta

				if (!$releditor_avaldus_id)
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					$data["rel_id"] = $releditor_avaldus_id;
				}
			break;


			case "avaldused_toolbar_laud":
				$url = $this->mk_my_orb("change", array(
						"id" => $sisseastuja->id(),
						"group" => "grp_sisseastuja_avaldused",
						// "sisseastumisavaldused_" . $oppetase_l => "new",
						"sisseastumisavaldused" => "new",
					)
				);
				$toolbar = &$data["toolbar"];
				$toolbar->add_button(array(
					"name" => "new",
					"img" => "new.gif",
					"tooltip" => t("Lisa uus avaldus"),
					"url" => $url,
				));
			break;

			case "avaldused_toolbar":
				$url = $this->mk_my_orb(
				"change", array(
										"id" => $sisseastuja->id(),
										"group" => "grp_sisseastuja_avaldused",
										// "sisseastumisavaldused_" . $oppetase_l => "new",
										"sisseastumisavaldused" => "new",
									)
				);
				$toolbar = &$data["toolbar"];
				$toolbar->add_button(array(
					"name" => "new",
					"img" => "new.gif",
					"tooltip" => t("Lisa uus avaldus"),
					"url" => $url,
				));

				$toolbar->add_button(array(
					"name" => "delete",
					"img" => "delete.gif",
					"confirm" => t("Kustutada?"),
					"tooltip" => t("Kustuta valitud avaldus(ed)"),
					"action" => "kustuta_avaldus",
				));
			break;


			case "konkursilehed_tbl":
				$arr["sisseastuja_id"] = $sisseastuja->id();
				$prinditavad_konkursilehed = $this->vajalikud_konkursilehed($arr);
				$table =& $arr["prop"]["vcl_inst"];

				if (current ($prinditavad_konkursilehed))
				{
					$table->define_field(array(
						"name" => "nimi",
						"caption" => t("Vastuv&otilde;tukatse"),
					));

					$table->define_field(array(
						"name" => "printed",
						"caption" => t("Prinditud"),
					));

					$table->define_field(array(
						"name" => "print",
						"caption" => t("Prindi"),
					));

					$prinditud_konkursilehed = $sisseastuja->prop("konkursileht_prinditud");
					$prinditud_konkursilehed = explode ("|", $prinditud_konkursilehed);

					foreach ($prinditavad_konkursilehed as $avaldus_idx => $konkursileht)
					{
						$printed = ( in_array($konkursileht, $prinditud_konkursilehed) ? "&#0149;" : "" );
						$avaldus_id = substr ($avaldus_idx, 0, -1);
						$url = $this->mk_my_orb("print", array(
							"katse" => $konkursileht,
							"avaldus_id" => $avaldus_id,
							"sisseastuja_id" => $sisseastuja->id(),
							"keskkond_id" => $keskkond->id(),
							)
						);

						$table->define_data(array(
							"nimi" => $vastuv6tt_keskkond->get_trans ("katse", $konkursileht),
							"printed" => $printed,
							"print" => html::href(array(
										"caption" => t("Prindi konkursileht"),
										"url" => $url,
										)
							),
						));
					}
				}
				else
				{
					$table->define_field(array(
						"name" => "nimi",
						"caption" => t("Valitud erialadel sisseastumiskatseid pole."),
					));
				}
			break;


			case "avaldused_tbl":
			case "avaldused_tbl_laud":
				$table =& $arr["prop"]["vcl_inst"];

				$table->define_field(array(
					"name" => "eriala",
					"caption" => t("Eriala"),
					"sortable" => 1
				));

				$table->define_field(array(
					"name" => "oppevorm",
					"caption" => t("&otilde;ppevorm"),
					"sortable" => 1
				));

				$table->define_field(array(
					"name" => "eelistus",
					"caption" => t("Eelistus"),
					"sortable" => 1
				));

				$table->define_field(array(
					"name" => "print",
					"caption" => t("T&otilde;end"),
					"sortable" => 1
				));

				$table->define_chooser(array(
					"name" => "sel",
					"field" => "from",
				));

				foreach ($sisseastuja->connections_from(array("type" => RELTYPE_AVALDUS)) as $connection)
				{
					$avaldus = $connection->to();
					$eelistus = ( ($avaldus->prop("eelistus") == 1) ? "&#0149;" : NULL );
					$print_url = $this->mk_my_orb(	"print", array(
							"avaldus_id" => $avaldus->id(),
							"sisseastuja_id" => $sisseastuja->id()
						), "vastuv6tt_avaldus"
					);
					$print_link = html::href(array(
							"caption" => t("Prindi kandideerimist&otilde;end"),
							"url" => $print_url,
						)
					);
					$prindi_t6end = $sisseastuja->prop("andmete_kinnitus") ? $print_link : "Andmed kinnitamata.";
					$eriala = $vastuv6tt_keskkond->get_trans ("eriala_" . strtolower($oppetase), $avaldus->prop("eriala"));

					if ($data["name"] == "avaldused_tbl")
					{
						$eriala = html::href(array(
							"caption" => $vastuv6tt_keskkond->get_trans ("eriala_" . strtolower($oppetase), $avaldus->prop("eriala")),
							"url" => $this->mk_my_orb(
								"change", array(
										"id" => $sisseastuja->id(),
										"group" => "grp_sisseastuja_avaldused",
										// "sisseastumisavaldused_" . $oppetase_l => $avaldus->id(),
										"sisseastumisavaldused" => $avaldus->id(),
									),
								"vastuv6tt_sisseastuja"
								),
							)
						);
					}

					$table->define_data(array(
						"eriala" => $eriala,
						"print" => $prindi_t6end,
						"oppevorm" => $vastuv6tt_keskkond->get_trans ("oppevorm", $avaldus->prop("oppevorm")),
						"eelistus" => $eelistus,
						"from" => $connection->id(),
						// "_active" => ($arr["request"]["sisseastumisavaldused_" . $oppetase_l] == $connection->prop("to")),
						"_active" => ($arr["request"]["sisseastumisavaldused"] == $connection->prop("to")),
					));
				}

				$table->set_default_sortby("oppevorm");
				$table->sort_by();
				break;

			case "sisseastuja_ylevaade":
				$data["value"] = $this->show (array ("id" => $sisseastuja->id()));
			break;
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$sisseastuja =& $arr['obj_inst'];
		$retval = PROP_OK;

		if ( ($arr["request"]["avaldused_editor"] == "1") || ($arr["request"]["avaldused_editor"] == "2") )
		{
			if ( ($data["name"] == "sisseastumisavaldused") || ($data["name"] == "sisseastumisavaldused_m") )
			{
				$oppetase = $sisseastuja->prop("oppetase");

				// if (($oppetase == "B") && ($data["name"] == "sisseastumisavaldused_m"))
				// {
					// return PROP_IGNORE;
				// }
				// if (($oppetase != "B") && ($data["name"] == "sisseastumisavaldused_b"))
				// {
					// return PROP_IGNORE;
				// }

				$isikukood = $sisseastuja->prop("isik_personal_id");
				$oppetase_l = $this->get_oppetase_l ($oppetase);
				$arr["sisseastumisavaldused_reled_name"] = $data["name"];
				$avaldused = $this->avaldused($arr);
				$oppevorm = $data["value"]['oppevorm'];
				$eriala = $data["value"]['eriala'];
				$eelistus = $data["value"]['eelistus'];
				$data["value"]['sisseastuja_nr'] = $sisseastuja->prop("sisseastuja_nr");
				$data["value"]['oppetase'] = $sisseastuja->prop("oppetase");
				$data["value"]['isik_firstname'] = $sisseastuja->prop("isik_firstname");
				$data["value"]['isik_lastname'] = $sisseastuja->prop("isik_lastname");
				$this->update_konkursipunktid == "1";

				if ($avaldused['kohad'][$oppevorm] >= 2)
				{
					echo "Etten&auml;htud avalduste arv on t&auml;is. &uuml;he &otilde;ppevormi kohta saab esitada kuni kaks avaldust.<br>";
					//$data["error"] = "Etten&auml;htud avalduste arv on t&auml;is. &uuml;he &otilde;ppevormi kohta saab esitada kuni kaks avaldust. ";
					return PROP_FATAL_ERROR;
				}

				if ( ($avaldused['erialad'][$eriala] == $oppevorm) && ($arr["request"]["avaldused_editor"] == "1") )
				{
					echo "Sellele erialale on valitud &otilde;ppevormis juba avaldus esitatud.<br>";
					//$data["error"] = "Sellele erialale on valitud &otilde;ppevormis juba avaldus esitatud. ";
					return PROP_FATAL_ERROR;
				}

				if (($avaldused['kohad'][$oppevorm] == 1) && ($eelistus != 1) && ($eelistus !== "0") )
				{
					echo "Valitud &otilde;ppevormis on juba esitatud avaldus, seega tuleks m&auml;&auml;rata ka eelistatav eriala.<br>";
					//$data["error"] = "Valitud &otilde;ppevormis on juba esitatud avaldus, seega tuleks m&auml;&auml;rata ka eelistatav eriala. ";
					return PROP_FATAL_ERROR;
				}

				if (!$avaldused['kohad'][$oppevorm])
				{
					$data["value"]['eelistus'] = 0;
				}

				if ($eelistus === "0")
				{
					foreach ($sisseastuja->connections_from(array("type" => RELTYPE_AVALDUS)) as $connection)
					{
						$avaldus = $connection->to();
						if ($avaldus->prop("oppevorm") == $oppevorm)
						{
							if ($avaldus->id() != $data["value"]["id"])
							{
								$avaldus->set_prop("eelistus", 1);
								$avaldus->save();
							}
							else
							{
								$avaldus->set_prop("eelistus", 0);
								$avaldus->save();
							}
						}
					}
					$data["value"]['eelistus'] = 0;
				}
				else
				if ($eelistus == 1)
				{
					foreach ($sisseastuja->connections_from(array("type" => RELTYPE_AVALDUS)) as $connection)
					{
						$avaldus = $connection->to();
						if ($avaldus->prop("oppevorm") == $oppevorm )
						{
							if ($avaldus->id() != $data["value"]["id"])
							{
								$avaldus->set_prop("eelistus", 0);
								$avaldus->save();
							}
							else
							{
								$avaldus->set_prop("eelistus", 1);
								$avaldus->save();
							}
						}
					}
					$data["value"]['eelistus'] = 1;
				}

			}
			else
			{
				return PROP_IGNORE;
			}
		}

		switch($data["name"])
		{
			case "andmete_kinnitus":
				if (!(   ( ($arr["request"]["isik_firstname"]) && ($arr["request"]["isik_lastname"]) && ( !($sisseastuja->prop('oppetase')=="B")  ||  ($arr["request"]["oppekeel_txt"] || $arr["request"]["oppekeel_sel"]) )    )  ||  ( ($sisseastuja->prop('isik_firstname')) && ($sisseastuja->prop('isik_lastname')) && (!($sisseastuja->prop('oppetase')=="B")  || $sisseastuja->prop('oppekeel')) )   ))
				{
					return PROP_ERROR;
					$data["error"] = "Nimi v&otilde;i &otilde;ppekeel sisestamata. ";
				}
				break;

			case "ex_kirjand_hinne":
			case "ex_ingl_hinne":
			case "ex_sks_hinne":
			case "ex_pr_hinne":
			case "ex_eesti_hinne":
			case "ex_vene_hinne":
			case "ex_yhisk_hinne":
			case "ex_ajalugu_hinne":
			case "ex_bio_hinne":
			case "ex_fyysika_hinne":
			case "ex_keemia_hinne":
			case "ex_mat_hinne":
			case "ex_geo_hinne":
				if (($data["name"] == "ex_kirjand_hinne") && ($arr["request"]["ex_kirjand_aasta"] <= 2000))
				{
					$max = 10;
				}
				else
				{
					$max = 100;
				}

				if ($data["value"] > $max)
				{
					$data["error"] = "Riigieksami hinne pole lubatud piirides. ";
					echo "Riigieksami hinne pole lubatud piirides.<br>";
					return PROP_ERROR;
				}

				$this->update_konkursipunktid = "1";
				break;

			case "isik_firstname":
			case "isik_firstname2":
				// setlocale (LC_CTYPE, "et_ET");
				$eesnimi = $data["value"];
				$isik_names = explode ("-", $eesnimi);

				foreach ($isik_names as $key => $isik_name)
				{
					$isik_name = trim ($isik_name);
					$isik_names[$key] = $this->aw_strtoupper ($isik_name{0}) . (substr ($this->aw_strtolower ($isik_name), 1));
					$isik_names2 = explode (" ", $isik_name);

					foreach ($isik_names2 as $key2 =>  $isik_name2)
					{
						$isik_name2 = trim ($isik_name2);
						$isik_names2[$key2] = $this->aw_strtoupper ($isik_name2{0}) . (substr ($this->aw_strtolower ($isik_name2), 1));
					}

					$isik_names[$key] = implode (" ", $isik_names2);
				}

				$eesnimi = implode ("-", $isik_names);
				$data["value"] = $eesnimi;

				if ($data['name'] == 'isik_firstname2')
				{
					$sisseastuja->set_prop('isik_firstname', $data['value']);
				}
				break;

			case "isik_lastname":
			case "isik_lastname2":
				$eesnimi_prop = ($data['name'] == 'isik_lastname2') ? 'isik_firstname2' : 'isik_firstname';
				// setlocale (LC_CTYPE, "et_ET");
				$perenimi = $data["value"];
				$isik_names = explode ("-", $perenimi);

				foreach ($isik_names as $key => $isik_name)
				{
					$isik_name = trim ($isik_name);
					$isik_names[$key] = $this->aw_strtoupper ($isik_name{0}) . (substr ($this->aw_strtolower ($isik_name), 1));
					$isik_names2 = explode (" ", $isik_name);

					foreach ($isik_names2 as $key2 =>  $isik_name2)
					{
						$isik_name2 = trim ($isik_name2);
						$isik_names2[$key2] = $this->aw_strtoupper ($isik_name2{0}) . (substr ($this->aw_strtolower ($isik_name2), 1));
					}

					$isik_names[$key] = implode (" ", $isik_names2);
				}

				$perenimi = implode ("-", $isik_names);
				$data["value"] = $perenimi;


				// eesnimi
				$eesnimi = $arr["request"][$eesnimi_prop];
				$isik_names = explode ("-", $eesnimi);

				foreach ($isik_names as $key => $isik_name)
				{
					$isik_name = trim ($isik_name);
					$isik_names[$key] = $this->aw_strtoupper ($isik_name{0}) . (substr ($this->aw_strtolower ($isik_name), 1));
					$isik_names2 = explode (" ", $isik_name);

					foreach ($isik_names2 as $key2 =>  $isik_name2)
					{
						$isik_name2 = trim ($isik_name2);
						$isik_names2[$key2] = $this->aw_strtoupper ($isik_name2{0}) . (substr ($this->aw_strtolower ($isik_name2), 1));
					}

					$isik_names[$key] = implode (" ", $isik_names2);
				}

				$eesnimi = implode ("-", $isik_names);
				// END eesnimi

				$sisseastuja->set_name("Sisseastuja - " . $eesnimi . " " . $perenimi . " - " . sprintf("%04d", $sisseastuja->prop("sisseastuja_nr")));

				if ($data['name'] == 'isik_lastname2')
				{
					$sisseastuja->set_prop('isik_lastname', $data['value']);
				}
				break;
/*
			case "isik_birthday":
				if ($arr["request"]["kodakondsus"] == "Eesti")
				{
					$pid_error = $this->pid_error($arr);

					if ($pid_error)
					{
						$data["error"] = $pid_error;
						return PROP_ERROR;
					}
				}
				break;
*/
			case "elukohamaa":
				if ( ($arr["request"]["kodakondsus"] != "Eesti") && ($data["value"] == "-") )
				{
					$data["error"] = "Elukohamaa sisestamata. Mitte-eesti kodanikel n&otilde;utav.";
					return PROP_ERROR;
				}
			break;

			case "elamisluba":
				if ( ($arr["request"]["kodakondsus"] != "Eesti") && ($data["value"] == "-") )
				{
					$data["error"] = "Elamisloa liik sisestamata. Mitte-eesti kodanikel n&otilde;utav.";
					return PROP_ERROR;
				}
			break;

			case "oppekeel":
				if ($arr["request"]["oppekeel_txt"])
				{
					$data["value"] = $arr["request"]["oppekeel_txt"];
				}
				else
				{
					$data["value"] = $arr["request"]["oppekeel_sel"];
				}
			break;

			case "aadress_maakond":
				if ($arr["request"]["aadress_maakond_txt"])
				{
					$data["value"] = $arr["request"]["aadress_maakond_txt"];
				}
				else
				{
					$data["value"] = $arr["request"]["aadress_maakond_sel"];
				}
			break;

			case "aadress_linn":
				if ($arr["request"]["aadress_linn_txt"])
				{
					$data["value"] = $arr["request"]["aadress_linn_txt"];
				}
				else
				{
					$data["value"] = $arr["request"]["aadress_linn_sel"];
				}
			break;

			case "keskhinne":
				$oppetase = $sisseastuja->prop("oppetase");

				switch ($oppetase)
				{
					case "B":
						$arr['hindetyyp'] = "kk";
					break;

					case "M":
					case "A":
					case "D":
					case "O":
						$arr['hindetyyp'] = "ak";
					break;
				}

				$data["value"] = $this->arvuta_keskhinne($arr);
				$this->update_konkursipunktid = "1";
			break;
		}

		return $retval;
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	/**
		@attrib name=show
		@param id required type=int
	**/
	function show($arr)
	{
		$sisseastuja = obj($arr["id"]);
		$template = "sisseastuja_ylevaade_" . strtolower ($sisseastuja->prop ("oppetase")) . ".html";
		$vastuv6tt_keskkond = get_instance (CL_VASTUV6TT_KESKKOND);

		$this->read_template($template);
		$this->vars(array(
			"firstname_show" => $sisseastuja->prop ("isik_firstname"),
			"lastname_show" => $sisseastuja->prop ("isik_lastname"),
			"ex_kirjand_hinne_show" => $sisseastuja->prop ("ex_kirjand_hinne"),
			"ex_kirjand_aasta_show" => $sisseastuja->prop ("ex_kirjand_aasta"),
			"ex_ingl_hinne_show" => $sisseastuja->prop ("ex_ingl_hinne"),
			"ex_ingl_aasta_show" => $sisseastuja->prop ("ex_ingl_aasta"),
			"ex_sks_hinne_show" => $sisseastuja->prop ("ex_sks_hinne"),
			"ex_sks_aasta_show" => $sisseastuja->prop ("ex_sks_aasta"),
			"ex_pr_hinne_show" => $sisseastuja->prop ("ex_pr_hinne"),
			"ex_pr_aasta_show" => $sisseastuja->prop ("ex_pr_aasta"),
			"ex_eesti_hinne_show" => $sisseastuja->prop ("ex_eesti_hinne"),
			"ex_eesti_aasta_show" => $sisseastuja->prop ("ex_eesti_aasta"),
			"ex_vene_hinne_show" => $sisseastuja->prop ("ex_vene_hinne"),
			"ex_vene_aasta_show" => $sisseastuja->prop ("ex_vene_aasta"),
			"ex_yhisk_hinne_show" => $sisseastuja->prop ("ex_yhisk_hinne"),
			"ex_yhisk_aasta_show" => $sisseastuja->prop ("ex_yhisk_aasta"),
			"ex_ajalugu_hinne_show" => $sisseastuja->prop ("ex_ajalugu_hinne"),
			"ex_ajalugu_aasta_show" => $sisseastuja->prop ("ex_ajalugu_aasta"),
			"ex_bio_hinne_show" => $sisseastuja->prop ("ex_bio_hinne"),
			"ex_bio_aasta_show" => $sisseastuja->prop ("ex_bio_aasta"),
			"ex_fyysika_hinne_show" => $sisseastuja->prop ("ex_fyysika_hinne"),
			"ex_fyysika_aasta_show" => $sisseastuja->prop ("ex_fyysika_aasta"),
			"ex_keemia_hinne_show" => $sisseastuja->prop ("ex_keemia_hinne"),
			"ex_keemia_aasta_show" => $sisseastuja->prop ("ex_keemia_aasta"),
			"ex_mat_hinne_show" => $sisseastuja->prop ("ex_mat_hinne"),
			"ex_mat_aasta_show" => $sisseastuja->prop ("ex_mat_aasta"),
			"ex_geo_hinne_show" => $sisseastuja->prop ("ex_geo_hinne"),
			"ex_geo_aasta_show" => $sisseastuja->prop ("ex_geo_aasta"),
			"oppekeel_show" => $sisseastuja->prop ("oppekeel"),
			"kk_hinne_5_show" => $sisseastuja->prop ("kk_hinne_5"),
			"kk_hinne_4_show" => $sisseastuja->prop ("kk_hinne_4"),
			"kk_hinne_3_show" => $sisseastuja->prop ("kk_hinne_3"),
			"kk_hinne_2_show" => $sisseastuja->prop ("kk_hinne_2"),
			"ak_hinne_5_show" => $sisseastuja->prop ("ak_hinne_5"),
			"ak_hinne_4_show" => $sisseastuja->prop ("ak_hinne_4"),
			"ak_hinne_3_show" => $sisseastuja->prop ("ak_hinne_3"),
			"ak_hinne_2_show" => $sisseastuja->prop ("ak_hinne_2"),
			"ak_hinne_1_show" => $sisseastuja->prop ("ak_hinne_1"),
			"ak_hinne_a_show" => $sisseastuja->prop ("ak_hinne_a"),
			"ak_hinne_b_show" => $sisseastuja->prop ("ak_hinne_b"),
			"ak_hinne_c_show" => $sisseastuja->prop ("ak_hinne_c"),
			"ak_hinne_d_show" => $sisseastuja->prop ("ak_hinne_d"),
			"ak_hinne_e_show" => $sisseastuja->prop ("ak_hinne_e"),
			"ak_hinne_l6put88_show" => $sisseastuja->prop ("ak_hinne_l6put88"),
			"keskhinne_show" => $sisseastuja->prop ("keskhinne"),
			"sisseastuja_nr_show" => sprintf("%04d", $sisseastuja->prop ("sisseastuja_nr")),
			"oppetase_show" => $vastuv6tt_keskkond->get_trans ("oppetase", $sisseastuja->prop ("oppetase")),
			"kodakondsus_show" => $sisseastuja->prop ("kodakondsus"),
			"elamisluba_show" => $vastuv6tt_keskkond->get_trans ("elamisluba", $sisseastuja->prop ("elamisluba")),
			"elukohamaa_show" => $sisseastuja->prop ("elukohamaa"),
			"gender_show" => $vastuv6tt_keskkond->get_trans ("gender", $sisseastuja->prop ("isik_gender")),
			"personal_id_show" => $sisseastuja->prop ("isik_personal_id"),
			"birthday_show" => get_lc_date ($sisseastuja->prop ("isik_birthday"), LC_DATE_FORMAT_SHORT_FULLYEAR ),
			"social_status_show" => $vastuv6tt_keskkond->get_trans ("social_status", $sisseastuja->prop ("isik_social_status")),
			"laste_arv_show" => $sisseastuja->prop ("laste_arv"),
			"elukoht_show" => $vastuv6tt_keskkond->get_trans ("elukoht", $sisseastuja->prop ("elukoht")),
			"haridus_kood_show" => $sisseastuja->prop ("haridus_kood"),
			"haridus_k6rgkool_show" => $sisseastuja->prop ("haridus_k6rgkool"),
			"haridus_aasta_show" => $sisseastuja->prop ("haridus_aasta"),
			"haridus_v2lismaal_show" => $vastuv6tt_keskkond->get_trans ("haridus_v2lismaal", $sisseastuja->prop ("haridus_v2lismaal")),
			"haridus_medal_show" => $vastuv6tt_keskkond->get_trans ("haridus_medal", $sisseastuja->prop ("haridus_medal")),
			"haridus_kool_tyyp_show" => $vastuv6tt_keskkond->get_trans ("haridus_kool_tyyp", $sisseastuja->prop ("haridus_kool_tyyp")),
			"haridus_kool_aasta_show" => $sisseastuja->prop ("haridus_kool_aasta"),
			"haridus_kool_kood_show" => $sisseastuja->prop ("haridus_kool_kood"),
			"haridus_kool_6ppevorm_show" => $vastuv6tt_keskkond->get_trans ("haridus_kool_6ppevorm", $sisseastuja->prop ("haridus_kool_6ppevorm")),
			"v66rkeel_show" => $vastuv6tt_keskkond->get_trans ("v66rkeel", $sisseastuja->prop ("v66rkeel")),
			"aadress_kood_show" => $sisseastuja->prop ("aadress_kood"),
			"aadress_t2nav_show" => $sisseastuja->prop ("aadress_t2nav"),
			"aadress_sjsk_show" => $sisseastuja->prop ("aadress_sjsk"),
			"aadress_indeks_show" => $sisseastuja->prop ("aadress_indeks"),
			"aadress_linn_show" => $sisseastuja->prop ("aadress_linn"),
			"aadress_maakond_show" => $sisseastuja->prop ("aadress_maakond"),
			"phone_show" => $sisseastuja->prop ("phones"),
			"email_show" => $sisseastuja->prop ("emails"),
			"pere_isa_nimi_show" => $sisseastuja->prop ("pere_isa_nimi"),
			"pere_isa_tel_show" => $sisseastuja->prop ("pere_isa_tel"),
			"pere_isa_aadress_show" => $sisseastuja->prop ("pere_isa_aadress"),
			"pere_ema_nimi_show" => $sisseastuja->prop ("pere_ema_nimi"),
			"pere_ema_tel_show" => $sisseastuja->prop ("pere_ema_tel"),
			"pere_ema_aadress_show" => $sisseastuja->prop ("pere_ema_aadress"),
			"pere_abikaasa_nimi_show" => $sisseastuja->prop ("pere_abikaasa_nimi"),
			"pere_abikaasa_tel_show" => $sisseastuja->prop ("pere_abikaasa_tel"),
			"pere_abikaasa_aadress_show" => $sisseastuja->prop ("pere_abikaasa_aadress"),
			"t88koht_show" => $sisseastuja->prop ("t88koht"),
			"t88tel_show" => $sisseastuja->prop ("t88tel"),
			"tulemus_ek_show" => $sisseastuja->prop ("tulemus_ek"),
			"tulemus_kk_show" => $sisseastuja->prop ("tulemus_kk"),
			"tulemus_vk_show" => $sisseastuja->prop ("tulemus_vk"),
			"tulemus_vl_show" => $sisseastuja->prop ("tulemus_vl"),
			"tulemus_vm_show" => $sisseastuja->prop ("tulemus_vm"),
			"tulemus_vv_show" => $sisseastuja->prop ("tulemus_vv"),
			"tulemus_vr_show" => $sisseastuja->prop ("tulemus_vr"),
		));

		return $this->parse();
	}

	function callback_pre_edit($arr)
	{
		if (aw_global_get ("sisseastuja_error"))
		{
			aw_session_del ("sisseastuja_error");
		}
	}

	function callback_mod_reforb(&$arr)
	{
		if ($this->avaldused_editor)
		{
			$arr["avaldused_editor"] = $this->avaldused_editor;
		}
	}

	function callback_post_save($arr)
	{
		$sisseastuja =& $arr['obj_inst'];

		if (!$sisseastuja->prop("sisseastuja_nr"))
		{
			$nr = $this->db_fetch_field("SELECT max(sisseastuja_nr) as nr FROM vastuv6tt_sisseastuja", "nr")+1;
			$sisseastuja->set_prop("sisseastuja_nr", $nr);
		}

		if ($sisseastuja->prop("sisseastuja_nr") != $sisseastuja->prop("sisseastuja_nr_title"))
		{
			$sisseastuja->set_prop("sisseastuja_nr_title", $sisseastuja->prop("sisseastuja_nr"));
		}

		list($year,$month,$day) = explode("-",$arr["obj_inst"]->prop("isik_birthday"));
		#$data["value"] = sprintf("%d.%d.%d",$day,$month,$year);

		#$sisseastuja->set_prop("synniaeg",date("d.m.Y", $arr["obj_inst"]->prop("isik_birthday")));
		$sisseastuja->set_prop("synniaeg",sprintf("%d.%d.%d",$day,$month,$year));
		$sisseastuja->save();

		$connections = $sisseastuja->connections_from(array ("type" => RELTYPE_PERSON, "class_id" => CL_CRM_PERSON));

		$connection = current ($connections);
		if ($connection)
		{
		$isik = $connection->to();
		$this->avaldused_editor = "0";

		if ($this->sisseastuja_error)
		{
			aw_session_set ("sisseastuja_error", $this->sisseastuja_error);
			$this->sisseastuja_error = false;
		}

		$isik_props =  array (
			//"isik_firstname",
			//"isik_lastname",
			//"isik_gender",
			//"isik_personal_id",
			//"isik_social_status",
		);

		foreach ($isik_props as $name)
		{
			$value = $sisseastuja->prop($name);
			$isik_propname = substr ($name, 5);
			$isik->set_prop($isik_propname, $value);
		}

		$isik->save();

		}

			$connections = $sisseastuja->connections_from(array ("type" => RELTYPE_AVALDUS, "class_id" => CL_VASTUV6TT_AVALDUS));
			foreach ($connections as $connection)
			{
				$avaldus = $connection->to();

				$ai = $avaldus->instance();
				$ai->konkursipunktid(array(
						"avaldus_id" => $avaldus->id(),
						"sisseastuja_id" => $sisseastuja->id(),
						"konkursipunktid_final" => 1,
				));
				/*$this->do_orb_method_call(array(
					"action" => "konkursipunktid",
					"class" => "vastuv6tt_avaldus",
					"params" => array(
						"avaldus_id" => $avaldus->id(),
						"sisseastuja_id" => $sisseastuja->id(),
						"konkursipunktid_final" => 1,
					)
				));*/

				$avaldus->set_prop("oppetase", $sisseastuja->prop("oppetase"));
				$avaldus->set_prop("isik_lastname", $sisseastuja->prop("isik_lastname"));
				$avaldus->set_prop("isik_firstname", $sisseastuja->prop("isik_firstname"));
				$avaldus->set_prop("sisseastuja_nr", $sisseastuja->prop("sisseastuja_nr"));
				$avaldus->set_prop("sisseastuja_kood", $avaldus->prop("eriala") . $avaldus->prop("oppevorm") . $sisseastuja->prop("oppetase").sprintf("%04d", $sisseastuja->prop ("sisseastuja_nr")));
				$avaldus->save();

		}
	}



	// CUSTOM FUNCTIONS

	function arvuta_keskhinne($arr)
	{
		$hindeid = 0;
		$hinnete_summa = 0;
		$ak_trans = array (
			"1" => "e",
			"2" => "d",
			"3" => "c",
			"4" => "b",
			"5" => "a",
		);

		switch ($arr['hindetyyp'])
		{
			case "kk":
				for ($i = 2; $i <= 5; $i++)
				{
					$hinnete_summa += $i * $arr["request"]["kk_hinne_" . $i];
					$hindeid += $arr["request"]["kk_hinne_" . $i];
				}
			break;

			case "ak":
				for ($i = 1; $i <= 5; $i++)
				{
					$hinnete_summa += $i * $arr["request"]["ak_hinne_" . $i];
					$hindeid += $arr["request"]["ak_hinne_" . $i];

					$hinnete_summa += $i * $arr["request"]["ak_hinne_" . $ak_trans[$i]];
					$hindeid += $arr["request"]["ak_hinne_" . $ak_trans[$i]];
				}
			break;
		}

		$keskhinne = $hindeid ? ($hinnete_summa/$hindeid) : 0;
		$keskhinne = round ($keskhinne, 2);
		return $keskhinne;
	}

	function avaldused($arr)
	{
		$erialad = array ();
		$kohad = array ();
		$avaldused = aw_global_get("sisseastuja_avaldused_ids") ? explode ("|", aw_global_get("sisseastuja_avaldused_ids")) : array ();
		aw_session_del ("sisseastuja_avaldused_ids");

		foreach ($avaldused as $avaldus_id)
		{
			$avaldus = obj ($avaldus_id);

			if (($arr["request"]["avaldused_editor"] == "2") && ($avaldus->id() == $arr["request"][$arr["sisseastumisavaldused_reled_name"]]["id"]))
			{
				continue;
			}

			$erialad[$avaldus->prop("eriala")] = $avaldus->prop("oppevorm");
			$kohad[] = $avaldus->prop("oppevorm");
		}
		$kohad = array_count_values ($kohad);
		$avaldused = array ("kohad" => $kohad, "erialad" => $erialad);
		return $avaldused;
	}


/**
    @attrib name=kustuta_avaldus
**/
	function kustuta_avaldus($arr)
	{
		foreach ($arr["sel"] as $selected)
		{
			$connection=new connection($selected);
			$to = $connection->to();
			$to->set_prop("kustutatud", 1);
			$to->save();
			$connection->delete();
			$to->delete();
		}

		$returnURI = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => "grp_sisseastuja_avaldused",
		), "vastuv6tt_sisseastuja");

		return $returnURI;
	}

	function vajalikud_konkursilehed($arr)
	{
		$sisseastuja = obj($arr["sisseastuja_id"]);
		$erialad = array ();
		$vajalikud_katsed = array ();

		if ($sisseastuja->prop("oppetase") != "B")
		{
			return array ();
		}

		foreach ($sisseastuja->connections_from(array("type" => RELTYPE_AVALDUS)) as $connection)
		{
			$avaldus = $connection->to();
			$oppevorm = $avaldus->prop("oppevorm");

			if ($oppevorm != "K")
			{
				$erialad[$avaldus->id()] = $avaldus->prop("eriala");
			}
		}

		$erialad = array_unique ($erialad);

		foreach ($erialad as $avaldus_id => $eriala)
		{
			switch ($eriala)
			{
				case "AR":
					$vajalikud_katsed[$avaldus_id . "1"] = "KK";
					$vajalikud_katsed[$avaldus_id . "2"] = "VK";
				break;

				case "MH":
					$vajalikud_katsed[$avaldus_id . "1"] = "VM";
				break;

				// case "LP":
					// $vajalikud_katsed[$avaldus_id . "1"] = "VL";
				// break;

				// case "VM":
					// $vajalikud_katsed[$avaldus_id . "1"] = "VV";
				// break;

				// case "RB":
					// $vajalikud_katsed[$avaldus_id . "1"] = "VR";
				// break;
			}

			if ($sisseastuja->prop("oppekeel") != "Eesti")
			{
				$vajalikud_katsed[$avaldus_id . "0"] = "EK";
			}
		}

		$vajalikud_konkursilehed = array_unique ($vajalikud_katsed);
		return $vajalikud_konkursilehed;
	}

	/**

		@attrib name=print params=name

		@param sisseastuja_id required type=int
		@param avaldus_id required type=int
		@param keskkond_id required type=int
		@param katse required

		@returns

	**/
	function prindi_konkursileht($arr)
	{
		$vastuv6tt_keskkond = get_instance (CL_VASTUV6TT_KESKKOND);
		$keskkond = obj($arr["keskkond_id"]);
		$template = "konkursilehed.html";
		$konkursilehed = "";
		$sisseastuja = obj($arr["sisseastuja_id"]);
		$avaldus = obj($arr["avaldus_id"]);
		$oppetase_l = $this->get_oppetase_l ($sisseastuja->prop("oppetase"));
		$katse = $arr["katse"];
		$katse_l = strtolower($katse);
		$prinditud_konkursilehed = $sisseastuja->prop("konkursileht_prinditud");
		$prinditud_konkursilehed = explode ("|", $prinditud_konkursilehed);
		$prinditud_konkursilehed[] = $katse;
		$prinditud_konkursilehed = array_unique ($prinditud_konkursilehed);
		$prinditud_konkursilehed = implode ("|", $prinditud_konkursilehed);
		$sisseastuja->set_prop("konkursileht_prinditud", $prinditud_konkursilehed);
		$sisseastuja->save();
		$sisseastuja_return_url = $this->mk_my_orb("change", array(
			"group" => "grp_sisseastuja_avaldused",
			"id" => $arr["sisseastuja_id"],
			), "vastuv6tt_sisseastuja"
		);

		$this->read_template($template);
		$this->vars(array(
			"sisseastuja_nr" => $avaldus->prop("eriala") . $avaldus->prop("oppevorm") . $sisseastuja->prop("oppetase") . sprintf("%04d", $sisseastuja->prop("sisseastuja_nr")),
			"eesnimi" => $sisseastuja->prop("isik_firstname"),
			"eriala" => $vastuv6tt_keskkond->get_trans("eriala_" . strtolower($sisseastuja->prop("oppetase")), $avaldus->prop("eriala")),
			"perenimi" => $sisseastuja->prop("isik_lastname"),
			"katse_nimi" => $vastuv6tt_keskkond->get_trans ("katse", $katse),
			"sisseastuja_return_url" => $sisseastuja_return_url,
			"toimumisaeg_koht" => $keskkond->prop ("katse_" . $katse_l . "_aegkoht"),
		));
		return $this->parse();
	}

	function get_oppetase_l ($oppetase)
	{
		switch ($oppetase)
		{
			case "B":
				$oppetase_l = "b";
			break;

			case "M":
			case "A":
			case "D":
			case "O":
				$oppetase_l = "m";
			break;
		}

		return $oppetase_l;
	}
/*
	function pid_error($arr)
	{
		$pid = $arr["request"]["isik_personal_id"];
		$day = $arr["request"]["isik_birthday"]["day"];
		$month = $arr["request"]["isik_birthday"]["month"];
		$year = $arr["request"]["isik_birthday"]["year"];
		$pid_day = substr ($pid, 5, 2);
		$pid_month = substr ($pid, 3, 2);
		$pid_year = substr ($pid, 1, 2);
		$pid_year1 = "20" . $pid_year;
		$pid_year2 = "19" . $pid_year;

		$error = false;

		if ((!checkdate ($pid_month, $pid_day, $pid_year1)) && (!checkdate ($pid_month, $pid_day, $pid_year2)))
		{
			return "Isikukood ei vasta Eesti isikukoodi standardile. ";
		}

		if (is_numeric ($day) && is_numeric ($month) && is_numeric ($year))
		{
			if ( ($day != $pid_day) || ($month != $pid_month) || (($year != $pid_year1) && ($year != $pid_year2)) )
			{
				return "Isikukood ja sisestatud s&uuml;nniaeg ei &uuml;hti. ";
			}
		}

		return $error;
	}
*/
	function kohad($type)
	{
		switch ($type)
		{
			case "riigid":
				$kohad = array (
					"Eesti" => "Eesti",
					"Soome" => "Soome",
					"L&auml;ti" => "L&auml;ti",
					"Venemaa" => "Venemaa",
					"Ukraina" => "Ukraina",
					"Valgevene" => "Valgevene",
					"Afganistan" => "Afganistan",
					"Albaania" => "Albaania",
					"Alzheeria" => "Alzheeria",
					"Ameerika &uuml;hendriigid" => "Ameerika &uuml;hendriigid",
					"Andorra" => "Andorra",
					"Angola" => "Angola",
					"Antigua ja Barbuda" => "Antigua ja Barbuda",
					"Araabia &uuml;hendemiraadid" => "Araabia &uuml;hendemiraadid",
					"Argentina" => "Argentina",
					"Armeenia" => "Armeenia",
					"Aserbaidzhaan" => "Aserbaidzhaan",
					"Austraalia" => "Austraalia",
					"Austria" => "Austria",
					"Bahama" => "Bahama",
					"Bahrein" => "Bahrein",
					"Bangladesh" => "Bangladesh",
					"Barbados" => "Barbados",
					"Belau" => "Belau",
					"Belgia" => "Belgia",
					"Belize" => "Belize",
					"Benin" => "Benin",
					"Bhutan" => "Bhutan",
					"Boliivia" => "Boliivia",
					"Bosnia ja Hertsegoviina" => "Bosnia ja Hertsegoviina",
					"Botswana" => "Botswana",
					"Brasiilia" => "Brasiilia",
					"Brunei" => "Brunei",
					"Bulgaaria" => "Bulgaaria",
					"Burkina Faso" => "Burkina Faso",
					"Burundi" => "Burundi",
					"Cabo Verde" => "Cabo Verde",
					"Colombia" => "Colombia",
					"Costa Rica" => "Costa Rica",
					"Côte d'Ivoire" => "Côte d'Ivoire",
					"Djibouti" => "Djibouti",
					"Dominica" => "Dominica",
					"Dominikaani Vabariik" => "Dominikaani Vabariik",
					"Ecuador" => "Ecuador",
					"Egiptus" => "Egiptus",
					"Ekvatoriaal-Guinea" => "Ekvatoriaal-Guinea",
					"El Salvador" => "El Salvador",
					"Eritrea" => "Eritrea",
					"Etioopia" => "Etioopia",
					"Fidzhi" => "Fidzhi",
					"Filipiinid" => "Filipiinid",
					"Gabon" => "Gabon",
					"Gambia" => "Gambia",
					"Ghana" => "Ghana",
					"Grenada" => "Grenada",
					"Gruusia" => "Gruusia",
					"Guatemala" => "Guatemala",
					"Guinea" => "Guinea",
					"Guinea-Bissau" => "Guinea-Bissau",
					"Guyana" => "Guyana",
					"Haiti" => "Haiti",
					"Hiina" => "Hiina",
					"Hispaania" => "Hispaania",
					"Holland" => "Holland",
					"Honduras" => "Honduras",
					"Horvaatia" => "Horvaatia",
					"Ida-Timor" => "Ida-Timor",
					"Iirimaa" => "Iirimaa",
					"Iisrael" => "Iisrael",
					"India" => "India",
					"Indoneesia" => "Indoneesia",
					"Iraak" => "Iraak",
					"Iraan" => "Iraan",
					"Island" => "Island",
					"Itaalia" => "Itaalia",
					"Jaapan" => "Jaapan",
					"Jamaica" => "Jamaica",
					"Jeemen" => "Jeemen",
					"Jordaania" => "Jordaania",
					"Kambodzha" => "Kambodzha",
					"Kamerun" => "Kamerun",
					"Kanada" => "Kanada",
					"Kasahstan" => "Kasahstan",
					"Katar" => "Katar",
					"Kenya" => "Kenya",
					"Kesk-Aafrika Vabariik" => "Kesk-Aafrika Vabariik",
					"Kiribati" => "Kiribati",
					"Komoorid" => "Komoorid",
					"Kongo DV" => "Kongo DV",
					"Kongo Vabariik" => "Kongo Vabariik",
					"K&otilde;rg&otilde;zstan" => "K&otilde;rg&otilde;zstan",
					"Kreeka" => "Kreeka",
					"K&uuml;pros" => "K&uuml;pros",
					"Kuuba" => "Kuuba",
					"Kuveit" => "Kuveit",
					"Laos" => "Laos",
					"Leedu" => "Leedu",
					"Lesotho" => "Lesotho",
					"Libeeria" => "Libeeria",
					"Liechtenstein" => "Liechtenstein",
					"Liibanon" => "Liibanon",
					"Liiba" => "Liiba",
					"L&otilde;una-Aafrika Vabariik" => "L&otilde;una-Aafrika Vabariik",
					"L&otilde;una-Korea" => "L&otilde;una-Korea",
					"Luksemburg" => "Luksemburg",
					"Madagaskar" => "Madagaskar",
					"Makedoonia" => "Makedoonia",
					"Malaisia" => "Malaisia",
					"Malawi" => "Malawi",
					"Maldiivid" => "Maldiivid",
					"Mali" => "Mali",
					"Malta" => "Malta",
					"Maroko" => "Maroko",
					"Marshalli Saared" => "Marshalli Saared",
					"Mauritaania" => "Mauritaania",
					"Mauritius" => "Mauritius",
					"Mehhiko" => "Mehhiko",
					"Mikroneesia" => "Mikroneesia",
					"Moldova" => "Moldova",
					"Monaco" => "Monaco",
					"Mongoolia" => "Mongoolia",
					"Mosambiik" => "Mosambiik",
					"Myanmar" => "Myanmar",
					"Namiibia" => "Namiibia",
					"Nauru" => "Nauru",
					"Nepal" => "Nepal",
					"Nicaragua" => "Nicaragua",
					"Nigeeria" => "Nigeeria",
					"Niger" => "Niger",
					"Norra" => "Norra",
					"Omaan" => "Omaan",
					"Paapua Uus-Guinea" => "Paapua Uus-Guinea",
					"Pakistan" => "Pakistan",
					"Palestiina okupeeritud ala" => "Palestiina okupeeritud ala",
					"Panama" => "Panama",
					"Paraguay" => "Paraguay",
					"Peruu" => "Peruu",
					"P&otilde;hja-Korea" => "P&otilde;hja-Korea",
					"Poola" => "Poola",
					"Portugal" => "Portugal",
					"Prantsusmaa" => "Prantsusmaa",
					"Rootsi" => "Rootsi",
					"Rumeenia" => "Rumeenia",
					"Rwanda" => "Rwanda",
					"Shveits" => "Shveits",
					"Saalomoni Saared" => "Saalomoni Saared",
					"Saint Kitts ja Nevis" => "Saint Kitts ja Nevis",
					"Saint Lucia" => "Saint Lucia",
					"Saint Vincent" => "Saint Vincent",
					"Saksamaa" => "Saksamaa",
					"Sambia" => "Sambia",
					"Samoa" => "Samoa",
					"San Marino" => "San Marino",
					"São Tomé ja Príncipe" => "São Tomé ja Príncipe",
					"Saudi Araabia" => "Saudi Araabia",
					"Seishellid" => "Seishellid",
					"Senegal" => "Senegal",
					"Serbia ja Montenegro" => "Serbia ja Montenegro",
					"Sierra Leone" => "Sierra Leone",
					"Singapur" => "Singapur",
					"Slovakkia" => "Slovakkia",
					"Sloveenia" => "Sloveenia",
					"Somaalia" => "Somaalia",
					"Sri Lanka" => "Sri Lanka",
					"Sudaan" => "Sudaan",
					"Suriname" => "Suriname",
					"Suurbritannia" => "Suurbritannia",
					"S&uuml;&uuml;ria" => "S&uuml;&uuml;ria",
					"Svaasimaa" => "Svaasimaa",
					"Taani" => "Taani",
					"Tadzhikistan" => "Tadzhikistan",
					"Tai" => "Tai",
					"Tansaania" => "Tansaania",
					"Togo" => "Togo",
					"Tonga" => "Tonga",
					"Trinidad ja Tobago" => "Trinidad ja Tobago",
					"Tshaad" => "Tshaad",
					"Tshehhi" => "Tshehhi",
					"Tshiili" => "Tshiili",
					"Tuneesia" => "Tuneesia",
					"Trgi" => "Trgi",
					"T&uuml;rkmenistan" => "T&uuml;rkmenistan",
					"Tuvalu" => "Tuvalu",
					"Uganda" => "Uganda",
					"Ungari" => "Ungari",
					"Uruguay" => "Uruguay",
					"Usbekistan" => "Usbekistan",
					"Uus-Meremaa" => "Uus-Meremaa",
					"Vanuatu" => "Vanuatu",
					"Vatikan" => "Vatikan",
					"Venezuela" => "Venezuela",
					"Vietnam" => "Vietnam",
					"Zimbabwe" => "Zimbabwe",
				);
			break;

			case "linnad":
				$kohad = array(
					"Abja-Paluoja" => "Abja-Paluoja",
					"Elva" => "Elva",
					"Haapsalu" => "Haapsalu",
					"J&otilde;geva" => "J&otilde;geva",
					"J&otilde;hvi" => "J&otilde;hvi",
					"Kallaste" => "Kallaste",
					"Karksi-Nuia" => "Karksi-Nuia",
					"Kehra" => "Kehra",
					"Keila" => "Keila",
					"Kilingi-N&otilde;mme" => "Kilingi-N&otilde;mme",
					"Kivili" => "Kivili",
					"Kohtla-J&auml;rve" => "Kohtla-J&auml;rve",
					"Kunda" => "Kunda",
					"Kuressaare" => "Kuressaare",
					"K&auml;rdla" => "K&auml;rdla",
					"Loksa" => "Loksa",
					"Maardu" => "Maardu",
					"Mustvee" => "Mustvee",
					"M&otilde;isakla" => "M&otilde;isakla",
					"Narva" => "Narva",
					"Narva-J&otilde;esuu" => "Narva-J&otilde;esuu",
					"Otep&auml;&auml;" => "Otep&auml;&auml;",
					"Paide" => "Paide",
					"Paldiski" => "Paldiski",
					"P&auml;rnu" => "P&auml;rnu",
					"P&otilde;ltsamaa" => "P&otilde;ltsamaa",
					"P&otilde;lva" => "P&otilde;lva",
					"P&uuml;ssi" => "P&uuml;ssi",
					"Rakvere" => "Rakvere",
					"Rapla" => "Rapla",
					"R&auml;pina" => "R&auml;pina",
					"Saue" => "Saue",
					"Sillam&auml;e" => "Sillam&auml;e",
					"Sindi" => "Sindi",
					"Suure-Jaani" => "Suure-Jaani",
					"Tallinn" => "Tallinn",
					"Tamsalu" => "Tamsalu",
					"Tapa" => "Tapa",
					"Tartu" => "Tartu",
					"T&otilde;rva" => "T&otilde;rva",
					"T&uuml;ri" => "T&uuml;ri",
					"Valga" => "Valga",
					"Viljandi" => "Viljandi",
					"V&otilde;hma" => "V&otilde;hma",
					"V&otilde;ru" => "V&otilde;ru",
				);
			break;

			case "maakonnad":
				$kohad = array(
					"Harjumaa" => "Harjumaa",
					"Hiiumaa" => "Hiiumaa",
					"Ida-Virumaa" => "Ida-Virumaa",
					"J&auml;rvamaa" => "J&auml;rvamaa",
					"J&otilde;gevamaa" => "J&otilde;gevamaa",
					"L&auml;&auml;ne-Virumaa" => "L&auml;&auml;ne-Virumaa",
					"L&auml;&auml;nemaa" => "L&auml;&auml;nemaa",
					"P&otilde;lvamaa" => "P&otilde;lvamaa",
					"P&auml;rnumaa" => "P&auml;rnumaa",
					"Raplamaa" => "Raplamaa",
					"Saaremaa" => "Saaremaa",
					"Tartumaa" => "Tartumaa",
					"Valgamaa" => "Valgamaa",
					"Viljandimaa" => "Viljandimaa",
					"V&otilde;rumaa" => "V&otilde;rumaa",
				);
			break;
		}

		return $kohad;
	}

	function aw_strtoupper($string)
	{
		$wrongLetters= chr(231) . chr(132) . chr(129) . chr(148) . chr(167) . chr(199) . chr(138) . chr(198);
		$rightLetters  = chr(230) . chr(142) . chr(154) . chr(153) . chr(166) . chr(230) . chr(154) . chr(230);

		$UpperCase = "";
		$UpperCase = strtoupper($string);
		return strtr( $UpperCase, $wrongLetters, $rightLetters);
	}

	function aw_strtolower($string)
	{
		$wrongLetters = chr(230) . chr(142) . chr(154) . chr(153) . chr(166) . chr(158);
		$rightLetters = chr(231) . chr(132) . chr(129) . chr(148) . chr(167) . chr(132);

		$LowerCase = "";
		$LowerCase = strtolower($string);
		return strtr( $LowerCase, $wrongLetters, $rightLetters);
	}
}

?>
