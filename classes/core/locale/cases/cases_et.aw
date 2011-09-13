<?php

class awlc_cases_et implements awlc_cases
{
	protected static $vowels = array(// ascii indices
		65, //Latin capital letter A
		69, //Latin capital letter E
		73, //Latin capital letter I
		79, //Latin capital letter O
		85, //Latin capital letter U
		89, //Latin capital letter Y
		97, //Latin small letter A
		101, //Latin small letter E
		105, //Latin small letter I
		111, //Latin small letter O
		117, //Latin small letter U
		121, //Latin small letter Y
		192, //Latin capital A, grave accent
		193, //Latin capital A, acute
		194, //Latin capital A, circumflex
		195, //Latin capital A, tilde
		196, //Latin capital A, diaeresis/umlaut
		197, //Latin capital A, ring
		200, //Latin capital E, grave accent
		201, //Latin capital E, acute accent
		202, //Latin capital E, circumflex
		203, //Latin capital E, diaeresis/umlaut
		204, //Latin capital I, grave accent
		205, //Latin capital I, acute accent
		206, //Latin capital I, circumflex
		207, //Latin capital I, diaeresis/umlaut
		210, //Latin capital O, grave accent
		211, //Latin capital O, acute accent
		212, //Latin capital O, circumflex
		213, //Latin capital O, tilde
		214, //Latin capital O, diaeresis/umlaut
		216, //Latin capital O, slash
		217, //Latin capital U, grave accent
		218, //Latin capital U, acute accent
		219, //Latin capital U, circumflex
		220, //Latin capital U, diaeresis/umlaut
		221, //Latin capital Y, acute accent
		224, //Latin small A, grave accent
		225, //Latin small A, acute accent
		226, //Latin small A, circumflex
		227, //Latin small A, tilde
		228, //Latin small A, diaeresis/umlaut
		229, //Latin small A, ring
		232, //Latin small E, grave accent
		233, //Latin small E, acute accent
		234, //Latin small E, circumflex
		235, //Latin small E, diaeresis/umlaut
		236, //Latin small I, grave accent
		237, //Latin small I, acute accent
		238, //Latin small I, circumflex
		239, //Latin small I, diaeresis/umlaut
		242, //Latin small O, grave accent
		243, //Latin small O, acute accent
		244, //Latin small O, circumflex
		245, //Latin small O, tilde
		246, //Latin small O, diaeresis/umlaut
		248, //Latin small O, slash
		249, //Latin small U, grave accent
		250, //Latin small U, acute accent
		251, //Latin small U, circumflex
		252, //Latin small U, diaeresis/umlaut
		253, //Latin small Y, acute accent
		255 //Latin small Y, diaeresis/umlaut
	);

	public static function get_genitive_for_name($name)
	{
		$last_letter = ord(substr($name, -1));
		if (!in_array($last_letter, self::$vowels))
		{
			$genitive = $name . "'i";
		}
		else
		{
			$genitive = $name;
		}
		return $genitive;
	}
}
