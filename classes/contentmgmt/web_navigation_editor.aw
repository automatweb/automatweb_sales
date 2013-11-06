<?php
/*

@property editor type=text no_caption=1

*/

class web_navigation_editor extends aw_modal {
	
	protected function get_title() {
		return t("Kodulehe struktuuri haldamine");
	}
	
	protected function _get_editor(&$property) {
		$statusOptions = str_replace("\"", "'", json_encode(object::get_status_names()));
		$property["value"] = <<<SCRIPT
		
<style type="text/css">
ol.sortable, ol.sortable ol {
	list-style-type: none;
}
ol.sortable li {
	margin: 5px 0 0 0;
}

ol.sortable li div.sortable-item {
	border: 1px solid #d4d4d4;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	border-color: #D4D4D4 #D4D4D4 #BCBCBC;
	padding: 6px;
	margin: 0;
}

ol.sortable li div i.icon-move {
	cursor: move;
}
ol.sortable .placeholder {
	outline: 1px dashed #4183C4;
	/*-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	margin: -1px;*/
}
</style>
		
<script id="editor-nested-sortable" type="text/html">
	<li data-bind="attr: { 'data-id': id }">
        <div class="sortable-item">
			<i class="icon-move"></i>
			<a href="#" onclick="if ($(this).children('i').hasClass('icon-chevron-down')) { $(this).siblings('div').slideDown(); $(this).children('i').removeClass('icon-chevron-down').addClass('icon-chevron-up'); } else { $(this).siblings('div').slideUp(); $(this).children('i').removeClass('icon-chevron-up').addClass('icon-chevron-down') }"><i class="icon-chevron-down"></i></a>
			<input type="text" data-bind="value: name, valueUpdate:'afterkeydown'" class="input-large" />
			<span class="pull-right">
				<a data-bind="click: remove" title="Kustuta" class="btn"><i class="icon-trash"></i></a>
				<a data-bind="click: newSibling" title="Lisa naaberkaust" class="btn"><i class="icon-plus"></i></a>
				<a data-bind="click: newChild" title="Lisa alamkaust" class="btn"><i class="icon-plus-sign"></i></a>
			</span>
			<span class="pull-right" style="margin-top: 4px;" data-bind="chooser: status, chooserOptions: {$statusOptions}"></span>
			<div style="display: none; margin-top: 5px;" class="row-fluid">
				<div class="span6" style="padding-left: 36px">
					<input type="text" class="input-large" data-bind="value: alias" placeholder="Alias" style="margin-bottom: 5px" />
					<input type="text" class="input-large" data-bind="value: comment" placeholder="Kommentaar" style="margin-bottom: 5px" />
					<input type="text" class="input-large" data-bind="value: link" placeholder="Link"/>
				</div>
				<div class="span6">
					<input type="checkbox" data-bind="checked: target" /> Ava uues aknas <br />
					<input type="checkbox" data-bind="checked: users_only" /> Ainult sisselogitud kasutajatele
				</div>
			</div>
		</div>
        <ol>
            <!-- ko template: { name: 'editor-nested-sortable', foreach: children } -->
			<!-- /ko -->
        </ol>
    </li>
</script>
<ol class="sortable">
    <!-- ko foreach: menus -->
		<!-- ko template: { name: 'editor-nested-sortable', foreach: \$parent[\$data]().children } -->
		<!-- /ko -->
	<!-- /ko -->
</ol>

<script type="text/javascript" src="/js/jquery/jquery.mjs.nestedSortable.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var x = $('.sortable').nestedSortable({
		handle: 'i.icon-move',
		items: 'li',
		toleranceElement: '> .sortable-item',
		forcePlaceholderSize: true,
		helper:	'clone',
		items: 'li',
		opacity: .6,
		placeholder: 'placeholder',
		protectRoot: true,
		update: function (event, ui) {
			AW.UI.web_navigation_editor.update(event, ui);
		},
	});
});
</script>
		
SCRIPT;
	}
	
	protected function _get_editor_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "editor",
			"caption" => t(""),
			"reorderable" => true,
			"reorderable-handle" => "i.icon-move",
			"reorderable-start" => "AW.UI.web_navigation_editor.start",
			"reorderable-update" => "AW.UI.web_navigation_editor.update",
			"fields" => array("drag", "name", "actions"),
			"header" => array(
				"fields" => array(
					"drag" => t(""),
					"name" => t("Kaust"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: folders"),
				"data-row" => array("bind" => "attr: { 'data-id': id } "),
				"fields" => array(
					"drag" => html::italic("", "icon-move"),
					"name" => array(
						"data" => array("bind" => "style: { 'padding-left': (depth() * 24 + 8) + 'px' }"),
						"value" => html::textbox(array(
							"data" => array("bind" => "value: name, valueUpdate:'afterkeydown'"),
							"class" => "input-large"
						))
					),
					"actions" => html::href(array(
						"url" => "#",
						"data" => array("bind" => "click: remove"),
						"class" => "btn",
						"caption" => html::italic("", "icon-trash"),
						"title" => t("Kustuta")
					))." ".html::href(array(
						"url" => "#",
						"data" => array("bind" => "click: newSibling"),
						"class" => "btn",
						"caption" => html::italic("", "icon-plus"),
						"title" => t("Lisa naaberkaust")
					))." ".html::href(array(
						"url" => "#",
						"data" => array("bind" => "click: newChild"),
						"class" => "btn",
						"caption" => html::italic("", "icon-plus-sign"),
						"title" => t("Lisa alamkaust")
					)),
				),
				"expandable" => true,
				"expandable_rows" => array(
					array(
						// kommentaar; lingi omadus (saab kŠsitsi anda lingi aadressi); ava uues aknas; alias; kuva ainult sisseloginud kasutajale.
						"name" => html::textbox(array(
							"data" => array("bind" => "value: alias"),
							"class" => "input-large",
							"placeholder" => t("Alias"),
							"style" => "margin-bottom: 5px",
						)).html::linebreak().html::textarea(array(
							"data" => array("bind" => "value: comment"),
							"class" => "input-large",
							"rows" => 5,
							"placeholder" => t("Kommentaar"),
							"style" => "margin-bottom: 5px",
						)).html::linebreak().html::textbox(array(
							"data" => array("bind" => "value: link"),
							"class" => "input-large",
							"placeholder" => t("Link"),
							"style" => "margin-bottom: 5px",
						)),
						"actions" => html::checkbox(array(
							"data" => array("bind" => "checked: target"),
							"caption" => t("Ava uues aknas"),
						)).html::linebreak().html::checkbox(array(
							"data" => array("bind" => "checked: users_only"),
							"caption" => t("Ainult sisselogitud kasutajatele"),
						)),
					)
				)
			),
		);
	}
	
	/**
		@attrib name=save
	**/
	public function save ($arr = array()) {
		$data = automatweb::$request->arg_isset("data") ? json_decode(automatweb::$request->arg("data"), true) : array();
		$deleted = automatweb::$request->arg_isset("removed") ? json_decode(automatweb::$request->arg("removed"), true) : array();
		$this->__handle_folders($data);
		$this->__remove_folders($deleted);
		exit;
	}
	
	private function __remove_folders ($folders) {
		foreach ($folders as $folder) {
			if (object_loader::can("", $folder["id"])) {
				$folder = obj($folder["id"]);
				$folder->delete();
			}
		}
	}
	
	private function __handle_folders ($folders, $parent = null) {
		foreach ($folders as $folder) {
			$id = $this->__save_folder($folder, $parent);
			if (isset($folder["children"])) {
				$this->__handle_folders($folder["children"], $id);
			}
		}
	}
	
	private function __save_folder ($data, $parent) {
		$folder = obj(object_loader::can("", $data["id"]) ? $data["id"] : null, array(), menu_obj::CLID);
		if (isset($data["parent"]) && !object_loader::can("", $parent)) {
			$parent = $data["parent"];
		}
		if (object_loader::can("", $parent)) {
			$folder->set_parent($parent);
		}
		$folder->set_name($data["name"]);
		foreach (array("alias", "comment", "target", "link", "users_only") as $key) {
			if (isset($data[$key]) && $folder->is_property($key)) {
				if ("target" === $key || "users_only" === $key) {
					$data[$key] = $data[$key] === "true";
				}
				$folder->set_prop($key, $data[$key]);
			}
		}
		$folder->set_ord($data["ord"]);
		if ($data["ord"] == object::STAT_ACTIVE || $data["ord"] == object::STAT_NOTACTIVE) {
			$folder->set_status($data["ord"]);
		}
		return $folder->save();
	}
}