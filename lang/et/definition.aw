<?php
// kasutusel otsingus
define("LC_OBJECTS_ALL","Kõik");
define("LC_NO_DEFAULT_GROUP","Teil on default grupp puudu, palun teatage sellest veast kohe info@struktuur.ee");
define("LC_BANNER","Banner");

define("LC_CONFIG_SITE","Saidi config");
define("LC_CONFIG_CHOOSE_USER","<a href='%s'>Saidi config</a> / Vali kasutaja liitumisform");

define("LC_CORE_GET_FILE_NO_NAME","get_file was called without filename");
define("LC_CORE_PUT_FILE_NO_NAME","put_file was called without filename");

define("LC_DOCUMENT_PHOTO","Fotod");
define("LC_DOCUMENT_KEYWORD","Votmesonad");
define("LC_DOCUMENT_SHOW_LEAD","Näita leadi");
define("LC_DOCUMENT_FRONTPAGE","Esilehel");
define("LC_DOCUMENT_FORUM","Foorum");
//- hm, nendega tuleb midagi kavalat v2lja m6elda, nii ei saa seda igaljuhul teha, 
// samas need peax olema erinevates keeltes. dumdudum. ei teagi kohe mis teha. 
define("LC_DOCUMENT_CURRENT_TIME","#current_time#");

//document.aw
define("LC_DOCUMENT_ART","Artikkel saidilt ");
define("LC_DOCUMENT_ART_FROM_NADAL","Artikkel saidilt www.nadal.ee");
define("LC_DOCUMENT_BROD_DOC","Vennasta dokumente");


//class from.aw
define("LC_FORM_CHANGE_FOLDERS", "Muuda katalooge");
define("LC_FORM_CHANGE_FORM","Muuda formi");
define("LC_FORM_ALL_ELEMENTS","K&otilde;ik elemendid");
define("LC_FORM_CHANGE_SETTINGS","Muuda settinguid");
define("LC_FORM_ADD_FORM","Lisa form");
define("LC_FORM_CHANGE_FORM_CHOOSE_EL_LOC","'>Muuda formi</a> / Vali elemendi asukoht");

//form_actions.aw
define("LC_FORM_ACTIONS_FORM_ACTIONS","Formi actionid");
define("LC_FORM_ACTIONS_ADD_ACTIONS","'>Formi actionid</a> / Lisa action");
define("LC_FORM_ACTIONS_FORM_ACTIONS_CHANGE_ACTION","'>Formi actionid</a> / Muuda actionit");

//form_base.aw
define("LC_FORM_BASE_ORDER_FROM_AW","Tellimus AutomatWebist");
define("LC_FORM_BASE_USER","\n\nKasutaja ");
define("LC_FORM_BASE_INFO"," info:\n\n");

//form_cell.aw
define("LC_FORM_CELL_CHANGE_FORM_CHANGE_CELL","'>Muuda formi</a> / Muuda celli");
define("LC_FORM_CELL_CHANGE_FROM_ADD_ELEMENT","'>Muuda formi</a> / Lisa element");

//form_chain.aw
define("LC_FORM_CHAIN_ADD_WREATH","Lisa formi p&auml;rg");
define("LC_FORM_CHAIN_CHANGE_WREATH","Muuda p&auml;rga");
define("LC_FORM_CHAIN_CHANGE_WREATH_INPUT","'>Muuda p&auml;rga</a> / Sisestused");


//FROM_ENTRY.AW
define("LC_FORM_ENTRY_CHANGE_ENTRY","Muuda formi sisestust");

//form_import
define("LC_FORM_IMPORT_NOT_FILE_SELECTED","Te ei valinud faili!");
define("LC_FORM_IMPORT_CHAIN_ELS","<a href='%s'>Muuda p&auml;rga</a> / Vali elemendid");

//form_output.aw
define("LC_FORM_OUTPUT_ADD_OUT_STYLE","Lisa v&auml;ljundi stiil");
define("LC_FORM_OUTPUT_CHANGE_OUT_STYLE","Muuda v&auml;ljundi stiili");
define("LC_FORM_OUTPUT_OUTPUT_ADMIN","<a href='%s'>Muuda v&auml;jundit</a> / Adminni");
define("LC_FORM_OUTPUT_CHANGED_STYLE","Muutis outputi stiili %s");
define("LC_FORM_OUTPUT_CHANGE_OUTPUT_ADMIN","<a href='%s'>Muuda v&auml;jundit</a> / <a href='%s'>Adminni</a> / Muuda celli");

//form_table.aw
define("LC_GALLERY_ADD_GAL","Lisa galerii");
define("LC_GALLERY_CHANGE_GAL","Muuda galeriid");

//keywords.aw
define("LC_KEYWORDS_ERR_NO_DEFAULT","ERR: listi %s jaoks pole default meili määratud<br>");
define("LC_KEYWORDS_CHANGES_SAVED","Muudatused on salvestatud");
define("LC_KEYWORDS_NAME","Nimi: %s\n");
define("LC_KEYWORDS_ADDRESS","Aadress: %s\n");
define("LC_KEYWORDS_AUTOMAG_LIST","automaagiliselt loodud list");

//menuedit.aw
define("LC_MENUEDIT_TRIED_ACCESS","üritas accessida objekti id-ga '%s'. Kräkkimiskatse?");
define("LC_MENUEDIT_TRIED_ACCESS2","üritas accessida olematut objekti id-ga '%s'. Suunati esilehele");
define("LC_MENUEDIT_CLIENT","Klient");
define("LC_MENUEDIT_SECTION","Sektsioon");
define("LC_MENUEDIT_ADMINN_MENU","Adminni menyy");
define("LC_MENUEDIT_CATALOG","Kataloog");
define("LC_MENUEDIT_PMETHOD","Avalik meetod");
define("LC_MENUEDIT_IMPORT_MENU","Impordi men&uuml;&uuml;sid");

//mysql.aw
define("LC_MYSQL_ERROR_QUERY","Vigane päring");

define("LC_PLANNER_DAY"," Päev ");
define("LC_PLANNER_WEEK"," Nädal ");
define("LC_PLANNER_MONTH"," Kuu ");

//search_conf.aw
define("LC_SEARCH_CONF_SOME_WORD","mõni sõna");
define("LC_SEARCH_CONF_ALL_WORDS","kõiki sõnu");
define("LC_SEARCH_CONF_PHRASE","fraas");
define("LC_SEARCH_CONF_IN_TITLE","pealkirjas ");
define("LC_SEARCH_CONF_OR"," või ");
define("LC_SEARCH_CONF_AND"," ja ");
define("LC_SEARCH_CONF_IN_SUBJECT","sisus ");
define("LC_SEARCH_CONF_WITH_KEYWORD"," keywordidega ");
define("LC_SEARCH_CONF_LOOK_ANSWER","Otsis %s alt, %s , vastuseks saadi %s dokumenti");
define("LC_SEARCH_CONF_FROM_STRING"," stringist '%s'");

//stat.aw
define("LC_STAT_LOOKS_BY_HOURS","Vaatamised tundide lõikes");
define("LC_STAT_LOOKS_BY_DAYS","Vaatamised päevade lõikes");

//style.aw
define("LC_STYLE_TABLE_STYLE","Tabeli stiil");
define("LC_STYLE_CELL_STYLE","Celli stiil");
define("LC_STYLE_ELEMENT_STYLE","Elemendi stiil");
define("LC_STYLE_STYLES","Stiilid");
define("LC_STYLE_CHANGE_STYLE","Muuda stiili");
define("LC_STYLE_ADD_STYLE","Lisa stiil");

//syslog.aw
define("LC_SYSLOG_ALL","K&otilde;ik");

//table.aw
define("LC_TABLE_CHANGE_TABLE","Muuda tabelit");
define("LC_TABLE_DEVIDE_CELL_HOR","Jaga cell pooleks horisontaalselt");
define("LC_TABLE_DEVIDE_CELL_VER","Jaga cell pooleks vertikaalselt");
define("LC_TABLE_DELETE_UPPER_CELL","Kustuta &uuml;lemine  cell");
define("LC_TABLE_DELETE_LEFT_CELL","Kustuta vasak cell");
define("LC_TABLE_DELETE_RIGHT_CELL","Kustuta parem cell");
define("LC_TABLE_DELETE_LOWER_CELL","Kustuta alumine cell");
define("LC_TABLE_ADD_TABLE","</a> / Lisa tabel");

//USERS.AW
define("LC_USERS_USERS","'>Kasutajad</a>");
define("LC_USERS_PASSW_NOT_SAME","Paroolid peavad olema samad!");

//vars.aw
define("LC_LOGIN_NAME","Nimi:");
define("LC_LOGIN_PASS","Parool:");
?>