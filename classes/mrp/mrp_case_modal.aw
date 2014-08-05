<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"

	@groupinfo general_details caption="Rekvisiidid" parent=general icon="home"
	@default group=general_details

		@property name type=textbox
		@caption Tellimuse number

		@property comment type=textbox
		@caption Tellimuse nimetus

		@property seller type=textbox
		@caption M&uuml;&uuml;ja

		@property customer type=textbox
		@caption Ostja

@groupinfo components caption="Komponendid" icon="/automatweb/images/icons/32/289.png"
@default group=components

	@property components_toolbar type=toolbar

	@property components_table type=table
	@caption Tellimuse sisu

	#property components_new type=hidden
	
	#property components_new_name type=hidden

@groupinfo preview caption="Eelvaade" icon="/automatweb/images/icons/32/1009.png" load=on_demand
@default group=preview

@groupinfo partners caption="Partnerid" icon="/automatweb/images/icons/32/1809.png"
@default group=partners

	@groupinfo parners_potential caption="V&otilde;imalikud partnerid" parent=partners icon="briefcase"
	@default group=parners_potential

	@groupinfo purchases caption="Ostuajalugu" parent=partners icon="shopping-cart"
	@default group=purchases

@groupinfo clients caption="Kliendid" icon="/automatweb/images/icons/32/1809.png"
@default group=clients

	@groupinfo clients_potential caption="V&otilde;imalikud kliendid" parent=clients icon="briefcase"
	@default group=clients_potential

	@groupinfo clients_history caption="M&uuml;&uuml;giajalugu" parent=clients icon="shopping-cart"
	@default group=clients_history

@groupinfo appendices caption="Dokumendid" icon="/automatweb/images/icons/32/828.png"
@default group=appendices

	@groupinfo appendices_documents caption="Dokumendid" parent=appendices icon="file"
	@default group=appendices_documents
	
	@groupinfo appendices_contracts caption="Lepingud" parent=appendices icon="book"
	@default group=appendices_contracts
	
	@groupinfo appendices_images caption="Pildid" parent=appendices icon="picture"
	@default group=appendices_images
	
	@groupinfo appendices_links caption="Lingid" parent=appendices icon="bookmark"
	@default group=appendices_links
	
	@groupinfo appendices_goals caption="Eesm&auml;rgid" parent=appendices icon="flag"
	@default group=appendices_goals
	
	@groupinfo appendices_risks caption="Riskid" parent=appendices icon="exclamation-sign"
	@default group=appendices_risks
	
	@groupinfo appendices_analysis caption="Anal&uuml;&uuml;sid" parent=appendices icon="eye-open"
	@default group=appendices_analysis
	
	@groupinfo appendices_related caption="Seotud ostud" parent=appendices icon="tags"
	@default group=appendices_related

*/

class mrp_case_modal extends aw_modal {
	
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;TELLIMUS";
	}
	
	protected function _get_seller(&$property) {
		$property["data"] = array("bind" => "value: seller");
	}
	
	protected function _get_customer(&$property) {
		$property["data"] = array("bind" => "value: customer");
	}
	
	protected function _get_name(&$property) {
		$property["data"] = array("bind" => "value: name, valueUpdate: 'afterkeydown'");
	}
	
	protected function _get_comment(&$property) {
		$property["data"] = array("bind" => "value: comment, valueUpdate: 'afterkeydown'");
	}
	
	protected function _get_components_toolbar(&$property) {
		// FIXME: Make a separate class for new toolbar!
		$property["buttons"] = array(
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"data" => array("bind" => "click: addRow"),
				"caption" => html::italic("", "icon-plus")." ".t("Lisa komponent"),
			)),
		);
	}
	
	protected function _get_components_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "components_table",
			"caption" => t("Tellimuse sisu"),
			"reorderable" => true,
			"fields" => array("details", "article", "quantity", "unit", "price", "total"),
			"header" => array(
				"fields" => array(
					"details" => t("Nimetus"),
					"article" => t("Artikkel"),
					"quantity" => t("Kogus"),
					"unit" => t("&Uuml;hik"),
					"price" => t("Hind"),
					"total" => t("Summa"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: rows"),
				"data-row" => array("bind" => "attr: { 'data-id': id }"),
				"fields" => array(
					"details" => array("data" => array("bind" => "text: name")),
					"article" => array("data" => array("bind" => "text: article().name")),
					"quantity" => array("align" => "right", "data" => array("bind" => "text: quantity")),
					"unit" => array("data" => array("bind" => "text: unit() ? unit().name : ''")),
					"price" => array("align" => "right", "data" => array("bind" => "text: price() ? price().toFixed(2) : ''")),
					"total" => array("align" => "right", "data" => array("bind" => "text: total() ? total().toFixed(2) : ''")),
				),
				"expandable" => true,
				"expandable_rows" => array(
					array(
						"details" => html::textbox(array(
							"data" => array("bind" => "value: name, valueUpdate: 'keyup'"),
							"class" => "input-xlarge",
							"placeholder" => t("Pealkiri"),
							"style" => "margin-bottom: 5px",
						)).html::linebreak().html::textbox(array(
							"data" => array("bind" => "value: title"),
							"class" => "input-xlarge",
							"placeholder" => t("Alapealkiri"),
							"style" => "margin-bottom: 5px",
						)).html::linebreak().html::textarea(array(
							"data" => array("bind" => "value: description"),
							"class" => "input-xlarge",
							"rows" => "4",
							"placeholder" => t("Kokkuv&otilde;te"),
						)),
						"article" => html::span(array(
							"data" => array("bind" => "text: article().name"))
						)." ".html::href(array(
							"url" => "javascript:void(0)",
							"caption" => html::italic("", "icon-search"),
							"data" => array("bind" => "click: changeArticle"),
//							"onclick" => 'aw_popup_scroll("' . core::mk_my_orb("do_search", array(
//								"no_submit" => true,
//								"pi" => "components_new",
//								"npi" => "components_new_name",
//								"in_popup" => true,
//								"start_empty" => true,
//								"jcb" => "window.opener.AW.UI.order_management.add_article('+ aData.id +')"
//							), "popup_search") . '", "Otsing", 800, 500)',
						)),
						"quantity" => html::textbox(array(
							"data" => array("bind" => "value: quantity, valueUpdate: 'keyup'"),
							"class" => "input-mini",
							"placeholder" => t("Kogus"),
						)),
						"unit" => html::select(array(
							"data" => array("bind" => "options: \$root.availableUnits, optionsText: 'name', value: unit"),
							"class" => "input-mini",
						)),
						"price" => html::textbox(array(
							"data" => array("bind" => "value: price, valueUpdate: 'keyup'"),
							"class" => "input-mini",
							"placeholder" => t("&Uuml;hiku hind"),
						)),
					),
					array(
						"details" => array(
							"colspan" => 6,
							"value" => "Hinnakomponendid: ".html::linebreak().html::div(array(
								"data" => array("bind" => "priceComponents: price_components")
							)),
						),
					),
				),
			),
			"footer" => array(
				"fields" => array(
					"details" => html::bold(t("SUMMA:")),
					"total" => array(
						"align" => "right",
						"value" => html::span(array("data" => array("bind" => "text: total().toFixed(2)"))),
					),
				)
			),
		);	
	}
	
	protected function _group_preview(&$group) {
		$group["on_demand_url"] = core::mk_my_orb("preview", array(), "mrp_case");
	}
	
	protected function _set_rows($order, $rows) {
		foreach ($rows as $row)
		{
			$job = object_loader::can("", $row["id"]) ? obj($row["id"], array(), mrp_job_obj::CLID) : $order->add_job();
			
			unset($row["id"]);
			foreach ($row as $key => $value) {
				if ($key === "price_components") {
					$job->set_meta("price_components", $value);
				} elseif ($key === "article" || $key === "unit") {
					$value = isset($value["id"]) ? $value["id"] : null;
					$job->set_prop($key, $value);
				} elseif ($key === "ord") {
					$job->set_ord($value);
				} elseif ($key === "name") {
					$job->set_name($value);
				} else {
					// FIXME: Handle better property handling mechanism to check which properties a
					try {
						$job->set_prop($key, $value);
					} catch (exception $e) {
					}
				}
			}
			$job->save();
		}
	}
}
