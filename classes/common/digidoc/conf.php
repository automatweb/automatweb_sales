<?php
/*
@classinfo  maintainer=tarvo
*/
//define('PEAR_PATH', dirname(__FILE__).'/inc/'); // Kasutamaks süsteemseid PEARi mooduleid, väärtusta see '' 
define('PEAR_PATH', aw_ini_get("basedir").'/addons/pear/');
//define('PEAR_PATH', '');
require_once PEAR_PATH.'SOAP/Client.php';
/**
 * XML_Unserializer
 *
 * kasutusel SOAP Serveri saadetud xml-vastuste töötlemiseks
 */
require_once PEAR_PATH.'XML/Unserializer.php';

/**
 * WSDL faili asukoht
 */

//define('DD_WSDL', 'https://www.sk.ee:8090/?wsdl');
#define('DD_WSDL', 'https://www.sk.ee:8097/?wsdl');

# see on 6ige!
define('DD_WSDL', 'https://digidocservice.sk.ee/?wsdl'); // some sort of a new service?????

//define('DD_WSDL', 'https://linux.test.sk.sise:8092/?wsdl');
//define('DD_WSDL', 'http://www.openxades.org/cgi-bin/ocsp.cgi');
//define('DD_WSDL', 'https://www.openxades.org/cgi-bin/ocsp.cgi');
//define('DD_WSDL', 'http://linux.test.sk.sise:8098/?wsdl');
//define('DD_WSDL', 'https://www.openxades.org:8443/?wsdl');

//define('DD_SERVER_CA_FILE', aw_ini_get("basedir").'/classes/common/digidoc/service_certs.pem');

/**
 * Kohapeal hoitavate failide kaust (lõppeb /-ga):
 */
define('DD_FILES', aw_ini_get("basedir").'/classes/common/digidoc/data/');
define('DATA_ROOT', aw_ini_get("basedir").'/classes/common/digidoc/data/');

/**#@+
 * Serveriga ühenduse loomiseks vajalik parameeter
 */
define('DD_PROXY_HOST', '');
define('DD_PROXY_PORT', '');
define('DD_PROXY_USER', '');
define('DD_PROXY_PASS', '');
define('DD_TIMEOUT', '900000');
/**#@-*/

/**
 * WSDL classi lokaalse faili nimi
 *
 * Selles hoitakse WSDL-i alusel genereeritud PHP classi, 
 * et ei peaks iga kord seda serverist uuesti pärima.
 * Kui WSDL faili aadressi muuta, tuleb ka see fail ära kustutada, kuna 
 * selles hoitakse ka serveri aadressi, mis pärast muutmist enam ei ühti
 * õige aadressiga!
 */
//define('DD_WSDL_FILE', aw_ini_get("basedir").'/classes/common/digidoc/wsdl.class.php');
define('DD_WSDL_FILE', aw_ini_get("basedir").'/classes/common/digidoc/data/wsdl.class.php');

/**
 * Failide yleslaadimise kaust
 * vaikimisi on klass-faili kaustas paiknev kaust data/
 */
define('DD_UPLOAD_DIR', './data/');

/**
 * Vaikimis kasutatav keel
 * Võimalikud väärtused: EST / ENG / RUS
 */
define('DD_DEF_LANG', 'EST');

define('LOCAL_FILES', FALSE);

?>
