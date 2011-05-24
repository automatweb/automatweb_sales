<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	function log(s, i){
		$("#log").append(s + "<br />");
		if (typeof i != "undefined") {
			while (i > 1) {
				$("#log").append("<br />");
				i--;
			}
		}
		$('#log').scroll();
	}

	loadCallback = function(){}
	$("#content").load(function(){
		loadCallback();
	});
	function load(url, callback){
		log("Laadin URLi: " + url);
		loadCallback = callback;
		$("#content").attr("src", "{VAR:load_html_url}&url=" + encodeURIComponent(url));
	}

	sectors = {};
//	companies = {VAR:companies};

//	loadNextCompanyDetails();

	loadSectors();

	log("Alustan importimist...", 2);

	function loadSectors(){
		log("Laen valdkondi...", 2);
		load("{VAR:url}/index.php?leht=9", function(){
			$("#content").contents().find("select[name=valdkond] option").each(function(){
				var option = $(this);
				if (option.val() > 0) {
					sectors[option.val()] = {id: option.val(), name: option.html()};
					log("Leitud valdkond '" + option.html() + "'");
				}
			});
			log("Salvestan leitud valdkondi...");
			$.post("{VAR:save_url}", {sectors: sectors}, function(){
				log("-- Valdkonnad salvestatud!");
			});
			loadNextSubSector();
		});
	}

	function loadNextSubSector(){
		foundSectorToLoad = false;
		for (i in sectors) {
			sector = sectors[i];
			if (typeof sector.subsectors == "undefined") {
				foundSectorToLoad = true;
				sector.subsectors = {};
				log("<br />Laen tegevusalasid valdkonnale '" + sector.name + "'");
				load("{VAR:url}/index.php?leht=9&action=9&valdkond=" + sector.id, function(){
					$("#content").contents().find("select[name=valdkond2] option").each(function(){
						var option = $(this);
						if (option.val() > 0) {
							sector.subsectors[option.val()] = {id: option.val(), name: option.html(), companies: {}, parent: sector.id, companiesLoaded: false};
							log("Leitud tegevusala '" + option.html() + "'");
						}
					});
					log("Salvestan leitud tegevusalasid...");
					$.post("{VAR:save_url}", {sectors: sector.subsectors}, function(){
						log("-- Tegevusalad salvestatud!");
					});
					loadNextSubSector();
				});
				break;
			}
		}
		if (false && !foundSectorToLoad) {
			log("<br />Alustan ettev&otilde;tete andmete p&auml;rimist...", 2);
			loadNextCompanies();
		}
	}

	function loadNextCompanies(){
		foundSectorToLoad = false;
		for (i in sectors) {
			sector = sectors[i];
			for (j in sector.subsectors) {
				subsector = sector.subsectors[j];
				if (!subsector.companiesLoaded) {
					foundSectorToLoad = true;
					log("Laen tegevusala '" + subsector.name + "' ettev&otilde;tted...");
					load("{VAR:url}/index.php?leht=9&action=searchTegevus&valdkond=" + sector.id + "&valdkond2=" + subsector.id, function(){
						loadCompanyNumbers(sector, subsector, 1);
					});
					break;
				}
			}
			if (foundSectorToLoad){
				break;
			}
		}
		if (!foundSectorToLoad){
			log("<br />Alustan ettev&otilde;tete detailandmete p&auml;rimist...", 2);
			loadNextCompanyDetails();
		}
	}

	function loadNextCompanyDetails(){
		foundCompanyToLoad = false;
		for (k in companies) {
			company = companies[k];
//			if (!company.detailsLoaded) {
			if (typeof company.name == "undefined") {
				foundCompanyToLoad = true;
				load("{VAR:url}/index.php?leht=9&rn=" + company.id, function(){
					loadCompanyDetails(company);
				});
				break;
			}
		}

		if (!foundCompanyToLoad) {
			$.post("{VAR:save_url}", {companies: companies}, function(){
				location.reload(true);
			});
			log("<br /><br />VALMIS!", 2);
		}
	}

	function loadNextCompanyDetails_OLD(){
		foundCompanyToLoad = false;
		for (i in sectors) {
			sector = sectors[i];
			for (j in sector.subsectors) {
				subsector = sector.subsectors[j];
				for (k in subsector.companies) {
					company = subsector.companies[k];
					if (!company.detailsLoaded) {
						foundCompanyToLoad = true;
						load("{VAR:url}/index.php?leht=9&rn=" + company.id, function(){
							loadCompanyDetails(company);
						});
						break;
					}
				}
				if (foundCompanyToLoad) {
					break;
				}
			}
			if (foundCompanyToLoad) {
				break;
			}
		}
		if (!foundCompanyToLoad) {
			log("<br /><br />VALMIS!", 2);
		}
	}

	function loadCompanyDetails(company)
	{
		var t = $("#content").contents().find("form[name=otsivorm]").siblings("table");
		company.name = t.find("h1").html();
		t.find("td.firma").each(function(){
			o = $(this);
			if (o.html() == "Ettevõtte&nbsp;juht:&nbsp;") {
				company.director = o.next().html();
			} else if (o.html() == "Aadress:"){
				company.address = o.next().html();
			} else if (o.html() == "Registrikood:"){
				company.regnr = o.next().html();
			} else if (o.html() == "KMKNR:"){
				company.kmknr = o.next().html();
			} else if (o.html() == "Asutatud:"){
				company.established_str = o.next().html();
			} else if (o.html() == "Telefon:"){
				company.phone = o.next().html();
			} else if (o.html() == "Telefon 2:"){
				company.phone2 = o.next().html();
			} else if (o.html() == "Faks:"){
				company.fax = o.next().html();
			} else if (o.html() == "E-mail:"){
				company.email = o.next().html();
			} else if (o.html() == "Koduleht:"){
				company.web = o.next().html();
			} else if (o.html() == "Tegevusala:"){
				company.sectors = [o.next().html().substr(0, 3)];
			} else if (o.html() == "EMTAK:"){
				company.emtak = o.next().html();
			} else if (o.html() == "Lisainfo:"){
				company.info = o.next().html();
			} else {
				company.extra = o.next().html();
			}
		});		

		company.detailsLoaded;

		loadNextCompanyDetails();
	}

	function loadCompanyNumbers(sector, subsector, d){
		var total = $("#content").contents().find("form[name=otsivorm]").siblings("div").html().substr(7) * 1;
		var as = $("#content").contents().find("a[href^='?leht=9&rn=']");
		as.each(function(){
			id = $(this).attr("href").substr(11);
			subsector.companies[id] = {id: id, sectors: [subsector.id], detailsLoaded: false};
		});
		log("Salvestan leitud organisatsioonide IDd...");
		$.post("{VAR:save_url}", {companies: subsector.companies}, function(){
			log("-- Organisatsioonide IDd salvestatud!");
		});
		if (total > 100 && as.size() > 0){
			load("{VAR:url}/index.php?leht=9&action=searchTegevus&valdkond=" + sector.id + "&valdkond2=" + subsector.id + "&disp=" + d, function(){
				loadCompanyNumbers(sector, subsector, d + 1);
			});
		} else {
			subsector.companiesLoaded = true;
			loadNextCompanies();
		}
	}
});
</script>
<style type="text/css">
#log {
	position: absolute;
	top: 0;
	left: 0;
	z-index: 1;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#7F000000,endColorstr=#7F000000)";
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#7F000000,endColorstr=#7F000000);
	color: #00FF00;
	font-family: Courier;
	font-size: 15px;
	overflow: auto;
}
</style>
</head>
<body style="margin: 0; padding: 0; overflow: hidden;">
	<div id="log"></div>
	<iframe id="content" src="{VAR:url}" width="100%" height="100%" style="margin: 0; padding: 0; border: 0;">
		<p>Your browser does not support iframes.</p>
	</iframe>
</body>