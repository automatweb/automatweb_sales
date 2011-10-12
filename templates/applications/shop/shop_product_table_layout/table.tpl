

<form action="{VAR:baseurl}/index{VAR:ext}" method="GET" name="products_form">


<div class="toimingud">
<table>
<tr>
 	<td><span class="filter_btn" id="filter_btn"><a href="JavaScript: void(0)">Filtreeri</a></span></td>
     <td>Näita</td>
     <td class="product_count"><select name="set_per_page" size="1" onChange="window.location.href='<?php echo aw_ini_get("baseurl").aw_url_change_var("set_per_page", null); ?>&set_per_page='+this.options[this.selectedIndex].value;">
	<option value="20" <?php if ($_GET["set_per_page"] == 20) { echo "SELECTED"; } ?>>20</option>
	<option value="30" <?php if ($_GET["set_per_page"] == 30) { echo "SELECTED"; } ?>>30</option>
	<option value="40" <?php if ($_GET["set_per_page"] == 40) { echo "SELECTED"; } ?>>40</option>
	<option value="50" <?php if ($_GET["set_per_page"] == 50) { echo "SELECTED"; } ?>>50</option>
	<option value="100" <?php if ($_GET["set_per_page"] == 100) { echo "SELECTED"; } ?>>100</option>
</select></td>
     <td>toodet leheküljel</td>
</tr>
</table>
</div>

<!-- SUB: FILTER_CONTENT -->
<div id="ProdCont">

<!-- SUB: HAS_PAGES -->
<div class="lehed">
<table align="center">
<tr>
 	<td class="nav">
	<!-- SUB: PREV_PAGE -->	
<a href="{VAR:page_link}" title="eelmine leht">&laquo;</a>
	<!-- END SUB: PREV_PAGE -->	
</td>
     <td class="border">
		<!-- SUB: PAGE_SEL -->
		<a href="{VAR:page_link}" class="akt">{VAR:page_number}</a>
		<!-- END SUB: PAGE_SEL -->
		<!-- SUB: PAGE -->
		<a href="{VAR:page_link}">{VAR:page_number}</a>
		<!-- END SUB: PAGE -->
</td>
     <td class="nav">
	<!-- SUB: NEXT_PAGE -->
	<a href="{VAR:page_link}" title="järgmine leht">&raquo;</a>
	<!-- END SUB: NEXT_PAGE -->
	</td>
 </tr>
</table>
</div>
<!-- END SUB: HAS_PAGES -->

<!-- SUB: ROW -->
		<!-- SUB: COL -->
		<div class="tooted">
				{VAR:product}
		</div>
		<!-- END SUB: COL -->
<!-- END SUB: ROW -->
<input type="submit" value="Lisa korvi">
{VAR:reforb}
</div>
<!-- END SUB: FILTER_CONTENT -->
















<div class="product_filter_layout product_filter_layout_closed" id="product_filter_layout" style="display: none;">

<div class="tip">Vihje: Kogu tulba valimiseks vajutage selle pealkirjale</div>
	<table>
	<tr>
	<!-- SUB: PROD_FILTER_HEADER -->
		<th width="20%"><a onclick="toggle_checkboxes('f[{VAR:filter_name}]', this)" href="JavaScript: void(0)">{VAR:filter_caption}</a></th>
	<!-- END SUB: PROD_FILTER_HEADER -->
		<th width="20%" class="close_button"><a href="JavaScript: void(0)">Sulge</a> X</th>
	</tr>
	<tr>
	<!-- SUB: PROD_FILTER -->
		<td>
			<!-- SUB: PROD_FILTER_VALUE -->
			<label><input name="f[{VAR:filter_name}][{VAR:filter_value}]" type="checkbox">{VAR:filter_label}</label>
			<!-- END SUB: PROD_FILTER_VALUE -->
		</td>
	<!-- END SUB: PROD_FILTER -->
		<td></td>
	</tr>
	</table>

	<div class="footer"><a href="JavaScript: submit_form();">Filtreeri</a>&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="FancyForm.none()" href="JavaScript: void(0);">Puhasta valikud</a></div>

</div>

</form>

<script type="text/javascript" src="{VAR:baseurl}/js/fancy-form/mootools-release-1.11.js"></script>
<script type="text/javascript" src="{VAR:baseurl}/js/fancy-form/moocheck.js"></script>

<script>


// change product filter coordinates
set_product_filter_coordinates();

function set_product_filter_coordinates()
{
	dims = $("filter_btn").getCoordinates();
	e = $("product_filter_layout");
	e.setStyle('left', dims.left-30+'px');
	e.setStyle('top', dims.top+dims.height+'px');
}

function add_to_params(name)
{
	params = "";
	form = document.products_form;
	for(i = 0; i < form.elements.length; i++)
	{
		el = form.elements[i];
		if (el.name.indexOf(name) == 0)
		{
			if (el.checked)
			{
				params += "&"+el.name+"=1";
			}
		}
	}
	return params;
}

function submit_form()
{
	// oh no you don't submit the form here!
	sisu_el = document.getElementById("ProdCont");
	
	params = "";
	<!-- SUB: PROD_FILTER_HEADER2 -->
		params += add_to_params("f[{VAR:filter_name}]");
	<!-- END SUB: PROD_FILTER_HEADER2 -->

	toggle('product_filter_layout');
	$$("#filter_btn").removeClass("filter_btn_sel");

	url = window.location.href+"&in_filter=1&is_ajax=1"+params;
	url = url.replace (/sptlp=[0-9]*/, "sptlp=0");
	url = url.replace (/#/, "");
	sisu_el.innerHTML = aw_get_url_contents(url);
}

$$('.filter_btn a').addEvent('click', function(e)
{
	e = new Event(e);
	toggle('product_filter_layout');
	
	if ( $("filter_btn").hasClass("filter_btn_sel") )
	{
		$$("#filter_btn").removeClass("filter_btn_sel");
	}
	else
	{
		$$("#filter_btn").addClass("filter_btn_sel")
	}
	e.stop();
});

$$('.product_filter_layout .close_button a').addEvent('click', function(e)
{
	e = new Event(e);	
	toggle('product_filter_layout');
	if ( $("filter_btn").hasClass("filter_btn_sel") )
	{
		$$("#filter_btn").removeClass("filter_btn_sel");
	}
	else
	{
		$$("#filter_btn").addClass("filter_btn_sel")
	}
	e.stop();
});









function toggle_checkboxes(name, that)
{
	if (!that.custom_checked)
	{
		that.custom_checked = true;
		FancyForm.all(name)
	}
	else
	{
		FancyForm.none(name)
		that.custom_checked=false
	}
}

function getE(obj)
{
	return document.getElementById(obj);
}

function toggle(obj) {
	var el = getE(obj);
	el.style.display = (el.style.display != 'none' ? 'none' : '' );
}

</script>
