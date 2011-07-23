/**
 * Internationalization: Estonian language
 *
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.et.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.et.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.et = {
		controls: {
			"Bold": "Paks kiri",
			"Colorpicker": "V�rvivalija",
			"Copy": "Kopeeri",
			"Create link": "Lisa link",
			"Cut": "L�ika",
			"Decrease font size": "V�henda teksti suurust",
			"Fullscreen": "T�isekraanvaade",
			"Header 1": "Pealkiri 1",
			"Header 2": "Pealkiri 2",
			"Header 3": "Pealkiri 3",
			"Header 4": "Pealkiri 4",
			"Header 5": "Pealkiri 5",
			"Header 6": "Pealkiri 6",
			"View source code": "L�htekood",
			"Increase font size": "Suurenda teksti",
			"Indent": "Suurenda reataanet",
			"Insert Horizontal Rule": "Lisa horisontaaljoon",
			"Insert image": "Lisa pilt",
			"Insert Ordered List": "Lisa nummerdatud nimekiri",
			"Insert table": "Lisa tabel",
			"Insert Unordered List": "Lisa nimekiri",
			"Italic": "Kaldkiri",
			"Justify Center": "Keskelejoondus",
			"Justify Full": "T�isjoondus",
			"Justify Left": "Vasakjoondus",
			"Justify Right": "Paremjoondus",
			"Left to Right": "Vasakult-paremale tekst",
			"Outdent": "V�henda reataanet",
			"Paste": "Kleebi",
			"Redo": "Ennista tegevus",
			"Remove formatting": "Eemalda vormindus",
			"Right to Left": "Paremalt-vasakule tekst",
			"Strike-through": "L�bikriipsutatud",
			"Subscript": "Alaindeks",
			"Superscript": "�laindeks",
			"Underline": "Allajoonitud",
			"Undo": "V�ta tegevus tagasi"
		},

		messages: {
			"Select the text you wish to link": "Vali enne tekst mida linkida",
		},

		dialogs: {
			// for all
			"Apply": "Rakenda",
			"Cancel": "Katkesta",

			colorpicker: {
				"Colorpicker": "V�rvivalija",
				"Color": "V�rv"
			},

			image: {
				"Insert Image": "Lisa pilt",
				"Preview": "Eelvaade",
				"URL": "URL",
				"Title": "Pealkiri",
				"Description": "Kirjeldus",
				"Width": "Laius",
				"Height": "K�rgus",
				"Original W x H": "Algne laius x k�rgus",
				"Float": "Liiguta",
				"None": "Vaikimisi asukoht",
				"Left": "Vasakule",
				"Right": "Paremale"
			},

			link: {
				"Insert Link": "Lisa link",
				"Link URL": "Lingi URL",
				"Link Title": "Kirjeldus",
				"Link Target": "Avamiskoht (target)",
				"Select the text you wish to link": "Vali enne tekst mida linkida"
			},

			table: {
				"Insert table": "Lisa tabel",
				"Count of columns": "Veerge",
				"Count of rows": "Ridu"
			}
		}
	};
})(jQuery);
