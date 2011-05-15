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
		loadCallback = callback;
		$("#content").attr("src", "{VAR:load_html_url}&url=" + encodeURIComponent(url));
	}

	sectors = {};

	log("Alustan importimist...", 2);

	log("Laen valdkondi...", 2);
	load("{VAR:url}\index.php?leht=9", function(){
		$("#content").contents().find("select[name=valdkond] option").each(function(){
			var option = $(this);
			if (option.val() > 0) {
				sectors[option.val()] = {id: option.val(), name: option.html()};
				log("Leitud valdkond '" + option.html() + "'");
			}
		});
		loadNextSubSector();
	});

	function loadNextSubSector(){
		foundSectorToLoad = false;
		for (i in sectors) {
			sector = sectors[i];
			if (typeof sector.subsectors == "undefined") {
				foundSectorToLoad = true;
				sector.subsectors = [];
				log("<br />Laen tegevusalasid valdkonnale '" + sector.name + "'");
				load("{VAR:url}\index.php?leht=9&action=9&valdkond=" + sector.id, function(){
					$("#content").contents().find("select[name=valdkond2] option").each(function(){
						var option = $(this);
						if (option.val() > 0) {
							sector.subsectors[option.val()] = {id: option.val(), name: option.html(), companies: [], companiesLoaded: false};
							log("Leitud tegevusala '" + option.html() + "'");
						}
					});
					loadNextSubSector();
				});
				break;
			}
		}
		if (!foundSectorToLoad) {
			log("<br />Alustan ettev&otilde;tete andmete p&auml;rimist...", 2);
			loadNextCompanies();
		}
	}

	function loadNextCompanies(){
		for (i in sectors) {
			sector = sectors[i];
			for (j in sector.subsectors) {
				subsector = sector.subsectors[j];
				if (!subsector.companiesLoaded) {
					load("{VAR:url}\index.php?leht=9&action=searchTegevus&valdkond=" + sector.id + "&valdkond2=" + subsector.id, function(){
						loadCompanyNumbers(sector, subsector, 1);
					});
					break;
				}
			}
		}
	}

	function loadCompanyNumbers(sector, subsector, d){
		as = $("#content").contents().find("a[href^='?leht=9&rn=']");
		log(as.size());
		if (as.size() > 0) {
			as.each(function(){
				id = $(this).attr("href").substr(11);
				subsector.companies[id] = id;
				log(id);
			});

			load("{VAR:url}\index.php?leht=9&action=searchTegevus&valdkond=" + sector.id + "&valdkond2=" + subsector.id + "&disp=" + d, function(){
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