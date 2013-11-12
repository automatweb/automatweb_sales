<?php

class alloyui_treeview extends aw_template implements orb_public_interface {
	
	private $id;
	private $root;
	private $items;
	private $draggable = false;
	
	public function __construct() {
		$this->init("vcl/alloyui_treeview");
	}
	
	public function set_id($id) {
		$this->id = $id;
	}
	
	public function set_root($root) {
		$this->root = $root;
	}
	
	public function set_items($items) {
		$this->items = $items;
	}
	
	public function set_draggable($draggable = true) {
		$this->draggable = $draggable;
	}
	
	public function set_get_branch_func($get_branch_func) {
		$this->get_branch_func = $get_branch_func;
	}
	
	public function render() {
		$this->read_template("default.tpl");
		
		$this->vars_safe(array(
			"id" => $this->id,
			"json" => $this->__get_children()
		));
		$this->vars_safe(array(
			"DRAGGABLE" => $this->draggable ? $this->parse("DRAGGABLE") : "",
			"NON-DRAGGABLE" => $this->draggable ? "" : $this->parse("NON-DRAGGABLE")
		));
		
		return $this->parse();
	}
	
	public function json() {
		return $this->__get_children(automatweb::$request->arg("parent"));
	}
	
	private function __get_children($parent = null, $jsonize = true) {
		$items = array();
		
		if ($parent === null && isset($this->root)) {
			$items[] = array(
				"id" => $this->root["id"],
				"label" => html::href(array("url" => $this->root["url"], "caption" => $this->root["name"], "data" => array("node-id" => $this->root["id"]))),
				"children" => $this->__get_children($this->root["id"], false),
				"expanded" => true,
				"leaf" => false,
			);
		} elseif ($parent !== null) {
			foreach ($this->items as $item) {
				if (isset($item["parent"]) && $item["parent"] == $parent) {
					$itemdata = array(
						"id" => $item["id"],
						"label" => html::href(array("url" => $item["url"], "caption" => $item["name"], "data" => array("node-id" => $item["id"]))),
						"expanded" => !empty($_COOKIE["alloyui-treeview-{$this->id}-{$item["id"]}"]),
						"leaf" => false,
					);
					$children = $this->__get_children($item["id"], false);
					if (!empty($children) && !empty($this->get_branch_func)) {
						$itemdata["type"] = "io";
						$itemdata["io"] = $this->__get_io_for_item($item);
					} elseif (!empty($children)) {
						$itemdata["children"] = $children;
					}
					$items[] = $itemdata;
				}
			}
		} else {
			foreach ($this->items as $item) {
				if (empty($item["parent"])) {
					$itemdata = array(
						"id" => $item["id"],
						"label" => html::href(array("url" => $item["url"], "caption" => $item["name"])),
						"expanded" => !empty($_COOKIE["alloyui-treeview-{$this->id}-{$item["id"]}"]),
						"leaf" => false,
					);
					$children = $this->__get_children($item["id"], false);
					if (!empty($children) && !empty($this->get_branch_func)) {
						$itemdata["type"] = "io";
						$itemdata["io"] = $this->__get_io_for_item($item);
					} elseif (!empty($children)) {
						$itemdata["children"] = $children;
					}
					$items[] = $itemdata;
				}
			}
		}
		
		return $jsonize ? json_encode($items) : $items;
	}
	
	private function __get_io_for_item($item) {
		$url = new aw_uri($this->get_branch_func);
		$url->set_arg("parent", $item["id"]);
		return $url->get();
	}
}
