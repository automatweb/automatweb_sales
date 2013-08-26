<?php

class bootstrap_table extends aw_template implements orb_public_interface {
	
	private $id;
	private $caption;
	
	private $chooser;
	private $fields;
	private $content;
	
	private $reorderable = true;
	
	private $header_height = 1;
	
	public function __construct() {
		$this->init("vcl/bootstrap_table");
	}
	
	public function set_chooser($chooser) {
		$chooser["align"] = "center";
		$this->chooser = $chooser;
	}
	
	public function set_id($id) {
		$this->id = $id;
	}
	
	public function set_caption($caption) {
		$this->caption = $caption;
	}
	
	public function set_reorderable($reorderable) {
		$this->reorderable = $reorderable;
	}
	
	public function set_fields($fields) {
		$this->fields = array();
		foreach ($fields as $field) {
			$field["child_level"] = false;
			$field["child_count"] = 0;
			$this->fields[$field["name"]] = $field;
		}
		foreach ($this->fields as $field_id => $field) {
			if (isset($field["parent"]) and isset($this->fields[$field["parent"]])) {
				$this->fields[$field_id]["child_level"] = 1;
				$this->fields[$field["parent"]]["child_count"]++;
			}
		}
		foreach ($this->fields as $field_id => $field) {
			$this->fields[$field_id]["child_level"] = $this->__calculate_child_level($field);
			$this->header_height = max($this->header_height, $this->fields[$field_id]["child_level"] + 1);
		}
	}
	
	private function __calculate_child_level($field) {
		return $field["child_level"] > 0 ? $this->__calculate_child_level($this->fields[$field["parent"]]) + 1 : 0;
	}
	
	public function set_content($content) {
		$this->content = $content;
	}
	
	public function render() {
		$this->read_template("default.tpl");
		
		$this->vars_safe(array(
			"id" => $this->id,
			"caption" => $this->caption,
			"ROW" => "",
			"HEADER" => $this->render_header(),
			"BODY" => $this->render_body(),
			"FOOTER" => $this->render_footer(),
		));
		if ($this->reorderable) {
			$this->vars(array(
				"on-reorderable-update" => core::mk_my_orb("save_reordering", array(), "bootstrap_table"),
			));
			
			$this->vars_safe(array(
				"REORDERABLE" => $this->parse("REORDERABLE"),
			));
		} else {
			$this->vars_safe(array(
				"REORDERABLE" => "",
			));
		}
		
		return $this->parse();
	}
	
	private function render_header() {
		$ROWS = "";
		
		for ($i = 0; $i < $this->header_height; $i++) {
			$ROWS .= $this->render_row("th", $this->fields, "caption", $i);
		}
		
		$this->vars(array(
			"HEADER.ROWS" => $ROWS,
		));
		
		return $this->parse("HEADER");
	}
	
	private function render_body() {
		
		$ROWS = "";
		foreach ($this->content as $content) {
			$ROWS .= $this->render_row("td", $content);
		}
		
		$this->vars(array(
			"BODY.ROWS" => $ROWS,
		));
		
		return $this->parse("BODY");
	}
	
	private function render_footer() {
		return $this->parse("FOOTER");
	}
	
	private function render_row($field_tag, $data, $data_key = null, $hack = 0) {
		$FIELDS = "";
		
		if (!empty($this->chooser) && $hack === 0) {
			
			if ($field_tag === "td") {
				$value = html::checkbox(array(
					"name" => "sel[{$data[$this->chooser["field"]]}]",
					"value" => $data[$this->chooser["field"]],
				));
			} else {
				$value = html::href(array(
					"caption" => t("Vali"),
					"onclick" => "AW.UI.table.chooser.toggle(this)",
					"url" => "#",
				));
			}
			
			$this->vars(array(
				"field.tag" => $field_tag,
				"field.value" => $value,
				"field.style" => $this->compose_style($this->chooser),
				"field.class" => $this->compose_class($this->chooser),
				"field.span" => $field_tag === "th" ? 'rowspan="' . ($this->header_height) . '"' : "",
			));
			
			$FIELDS .= $this->parse("ROW.FIELD");
		}
		
		foreach ($this->fields as $field) {
			
			$value = null;
			if (isset($data[$field["name"]])) {
				$value = isset($data_key) ? (isset($data[$field["name"]][$data_key]) ? $data[$field["name"]][$data_key] : null) : $data[$field["name"]];
			}
			
			$this->vars(array(
				"field.tag" => $field_tag,
				"field.value" => $value,
				"field.style" => $this->compose_style($field),
				"field.class" => $this->compose_class($field),
			));
			
			// FIXME!
			if ($field_tag === "th") {
				if ($hack != $data[$field["name"]]["child_level"]) {
					continue;
				}
				$span = "";
				if ($data[$field["name"]]["child_count"] === 0) {
					$span .= 'rowspan="' . ($this->header_height - $hack) . '"';
				} elseif ($data[$field["name"]]["child_count"] > 1) {
					$span .= "colspan=\"{$data[$field["name"]]["child_count"]}\"";
				}
				$this->vars(array(
					"field.span" => $span,
				));
			}
			
			$FIELDS .= $this->parse("ROW.FIELD");
		}
		// FIXME: Bit of a hack for getting data-object-id
		if (!empty($this->chooser) && $hack === 0 && isset($data[$this->chooser["field"]])) {
			$object_id = $data[$this->chooser["field"]];
		} else {
			$object_id = null;
		}
		$this->vars(array(
			"row.data" => "data-object-id=\"{$object_id}\"",
			"ROW.FIELD" => $FIELDS,
		));
		
		return $this->parse("ROW");
	}
	
	private function compose_style($field) {
		$styles = array();
		if (!empty($field["width"])) {
			$styles[] = "width: {$field["width"]}px";
		}
		if (!empty($field["align"])) {
			$styles[] = "text-align: {$field["align"]}";
		}
		$styles[] = "vertical-align: middle";
		return !empty($styles) ? "style=\"" . implode("; ", $styles) . "\"" : "";
	}
	
	private function compose_class($field) {
//		return !empty($field["align"]) ? "class=\"text-{$field["align"]}\"" : "";
		return "";
	}
	
	/**
		@attrib name=save_reordering
	**/
	public static function save_reordering () {
		$data = automatweb::$request->arg_isset("order") ? automatweb::$request->arg("order") : array();
		usort($data, array("self", "__reordering_compare"));
		foreach ($data as $item) {
			if (object_loader::can("", $item["id"])) {
				$o = obj($item["id"]);
				$o->set_ord($item["index"] * 10);
				$o->save();
			}
		}
		exit;
	}
	
	protected static function __reordering_compare ($a, $b) {
		return $a["index"] - $b["index"];
	}
}
