var contactDetails = new Array();
$("#contact_entry_person_lastname_").focus();


// NAME ELEMENT AUTOCOMPLETE
var options1 = {
	script: optionsUrl,
	varname: "typed_text",
	minchars: 2,
	timeout: 100000,
	delay: 1000,
	width: 300,
	json: true,
	cache: false,
	shownoresults: false,
	callback: getContactDetails
};
var nameAS = new AutoSuggest('contact_entry_person_lastname_', options1);
// END NAME ELEMENT AUTOCOMPLETE



// PHONE ELEMENT AUTOCOMPLETE
var options2 = {
	script: phoneOptionsUrl,
	varname: "typed_text",
	minchars: 2,
	timeout: 100000,
	delay: 1000,
	width: 300,
	json: true,
	cache: false,
	shownoresults: false,
	callback: getContactDetails
};
var phoneAS = new AutoSuggest('contact_entry_person_fake_phone_', options2);
// END PHONE ELEMENT AUTOCOMPLETE


// LEAD SOURCE ELEMENT AUTOCOMPLETE
var options3 = {
	script: leadSourceOptionsUrl,
	varname: "typed_text",
	minchars: 2,
	timeout: 100000,
	delay: 1000,
	width: 300,
	json: true,
	cache: false,
	shownoresults: false,
	callback: setLeadSource
};
var leadSourceAS = new AutoSuggest('contact_entry_lead_source', options3);
// END LEAD SOURCE ELEMENT AUTOCOMPLETE




function loadContactDetails(contactDetails)
{
	if ($("input[name='contact_entry_person[id]']").length > 0)
	{
		el = $("input[name='contact_entry_person[id]']");
		el.attr("value", contactDetails.id);
	}
	else
	{
		// create id hidden input
		el = document.createElement("input");
		el.type = "hidden";
		el.name = "contact_entry_person[id]";
		$("form[name=changeform]").append(el);
		el.value = contactDetails.id;
	}

	contactName = contactDetails["contact_entry_person[firstname]"]["value"] + " " + contactDetails["contact_entry_person[lastname]"]["value"];
	$("div#de_form_box").prev().children("div").eq(0).text(contactEditCaption.replace("%s", contactName));

	// load data to changeform
	if (typeof(contactDetails["contact_entry_person[firstname]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_firstname_').value = contactDetails["contact_entry_person[firstname]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_firstname_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[lastname]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_lastname_').value = contactDetails["contact_entry_person[lastname]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_lastname_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[gender]"]["value"]) != "undefined")
	{
		if (contactDetails["contact_entry_person[gender]"]["value"] == "1")
		{
			document.forms["changeform"]["contact_entry_person[gender]"][0].checked = 1;
		}
		else if (contactDetails["contact_entry_person[gender]"]["value"] == "2")
		{
			document.forms["changeform"]["contact_entry_person[gender]"][1].checked = 1;
		}
	}
	else
	{
		document.forms["changeform"]["contact_entry_person[gender]"][0].checked = 0;
		document.forms["changeform"]["contact_entry_person[gender]"][1].checked = 0;
	}

	if (typeof(contactDetails["contact_entry_person[fake_phone]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_fake_phone_').value = contactDetails["contact_entry_person[fake_phone]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_fake_phone_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[fake_email]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_fake_email_').value = contactDetails["contact_entry_person[fake_email]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_fake_email_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[fake_address_address]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_fake_address_address_').value = contactDetails["contact_entry_person[fake_address_address]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_fake_address_address_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[fake_address_postal_code]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_fake_address_postal_code_').value = contactDetails["contact_entry_person[fake_address_postal_code]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_fake_address_postal_code_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[fake_address_city_relp]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_fake_address_city_relp_').value = contactDetails["contact_entry_person[fake_address_city_relp]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_fake_address_city_relp_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[fake_address_county_relp]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_fake_address_county_relp_').value = contactDetails["contact_entry_person[fake_address_county_relp]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_fake_address_county_relp_').value = "";
	}

	if (typeof(contactDetails["contact_entry_person[fake_address_country_relp]"]["value"]) != "undefined")
	{
		document.getElementById('contact_entry_person_fake_address_country_relp_').value = contactDetails["contact_entry_person[fake_address_country_relp]"]["value"];
	}
	else
	{
		document.getElementById('contact_entry_person_fake_address_country_relp_').value = "";
	}
}


function getContactDetails(obj)
{
	contactDetailsUrl = contactDetailsUrl + "&contact_id=" + obj.id;
	$.getJSON(contactDetailsUrl, {}, loadContactDetails);
}

function setLeadSource(obj)
{
	el = $("input[name='contact_entry_lead_source_oid']");
	el.attr("value", obj.id);
}
