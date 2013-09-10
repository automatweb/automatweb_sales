<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"

	@groupinfo details caption="&Uuml;ldandmed" icon="home" parent=general
	@default group=details

		@property name type=textbox
		@caption Nimi
	
		@property comment type=textarea
		@caption Tutvustus
	
		@property view_mode type=chooser
		@caption Kuvamise viis

	@groupinfo folders caption="Piltide kataloogid" icon="folder-close" parent=general
	@default group=folders
	
		@property folders_toolbar type=toolbar
	
		@property folders_table type=table
		@caption Piltide kataloogid

	@groupinfo settings caption="Seaded" icon="wrench" parent=general
	@default group=settings

@groupinfo management caption="Haldus" icon="/automatweb/images/icons/32/289.png" load=on_demand
@default group=management

	@property images_toolbar type=toolbar

	@property images_table type=table reorderable=true
	@caption Pildid

*/

class mini_gallery_modal extends aw_modal {
	
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;GALERII";
	}
	
	protected function get_save_method() {
		return "AW.UI.admin_if.save";
	}
	
	protected function _get_view_mode(&$property) {
		$property["data"] = array(
			"bind" => "chooser: view_mode, chooserOptions: " . str_replace("\"", "'", json_encode(mini_gallery_obj::get_view_mode_names())),
		);
	}
	
	protected function _get_folders_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "folders_table",
			"caption" => t("Piltide kataloogid"),
			"fields" => array("name", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: folder"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: name")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeFolder"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_folders_toolbar(&$property) {
		$property["buttons"] = array(
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"data" => array("bind" => "click: createFolder"),
				"caption" => html::italic("", "icon-plus")." ".t("Lisa uus kaust"),
			)),
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"data" => array("bind" => "click: selectFolders"),
				"caption" => html::italic("", "icon-search")." ".t("Otsi olemasolev kaust"),
			)),
		);
	}
	
	protected function _group_management(&$group) {
		$group["on_demand_click"] = "loadImages";
	}
	
	protected function _get_images_toolbar(&$property) {
		$property["buttons"] = array(
			html::span(array(
				"class" => "btn fileinput-button",
				"data" => array("bind" => "fileupload: { url: '/automatweb/orb.aw?class=file&action=upload', addHandler: addImage}"),
				"content" => html::italic("", "icon-plus")." ".html::span(array("content" => "Lisa pilte"))
			)),
		);
	}
	
	protected function _get_images_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "images_table",
			"caption" => t("Pildid"),
			"reorderable" => true,
//			"reorderable-update" => "AW.UI.",
			"fields" => array("thumbnail", "name", "parent", "size", "created", "actions"),
			"header" => array(
				"fields" => array(
					"thumbnail" => "",
					"name" => t("Nimi"),
					"parent" => t("Kaust"),
					"size" => t("Faili suurus"),
					"created" => t("Lisatud"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: images"),
				"data-row" => array("bind" => "attr: { 'data-id': id }"),
				"fields" => array(
					"thumbnail" => html::img(array(
						"src" => "dummy-src",
						"data" => array("bind" => "attr: { 'src': url }"),
						"style" => "max-height: 60px; max-width: 80px;",
					)),
					"name" => html::textbox(array(
						"data" => array("bind" => "value: name")
					)).html::linebreak().html::div(array(
						"data" => array("bind" => "visible: inProgress"),
						"class" => "progress progress-striped active",
						"style" => "width: 164px; margin-top: 5px;",
						"content" => html::div(array(
							"class" => "bar",
							"data" => array("bind" => "style: { 'width': progress() + '%' }"),
						)),
					)),
					"parent" => html::select(array(
						"data" => array("bind" => "options: \$parent.folder, optionsText: 'name', optionsValue: 'id', value: parent")
					)),
					"size" => array(
						"data" => array("bind" => "text: AW.util.formatFileSize(size())")
					),
					"created" => array(
						"data" => array("bind" => "text: AW.util.formatTimestamp(created())")
					),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeImage"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _reload_images ($object) {
		$images = array();
		foreach ($object->get_images()->arr() as $image) {
			$images[] = $image->json(false);
		}
		return $images;
	}
	
	protected function _set_folder ($gallery, $folders) {
		$folder_ids = array();
		if (is_array($folders)) {	
			foreach ($folders as $folder_data) {
				$folder_ids[] = $folder_data["id"];
			}
		}
		$gallery->set_prop("folder", $folder_ids);
	}
	
	protected function _set_images ($gallery, $images) {
		if (is_array($images)) {	
			foreach ($images as $image_data) {
				$this->_save($image_data["parent"], image_obj::CLID, $image_data);
			}
		}
	}
}
