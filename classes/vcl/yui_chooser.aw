<?php

class yui_chooser extends aw_template
{
	private $name;
	private $value;
	private $disabled;
	private $orient;
	private $indented;
	private $html = "";
	
	function init_vcl_property($arr)
	{
		$prop = $arr["property"];
		$this->name = $prop["name"];
		$this->value = ifset($prop, "value");
		$this->disabled = ifset($prop, "disabled");
		$this->orient = ifset($prop, "orient");
		$this->indented = !empty($prop["indented"]);
		
		$this->html = html::div(array(
			"id" => "{$prop["name"]}-chooser-group",
			"content" => $this->__insert_level_of_options(isset($prop["options"]) ? $prop["options"] : null)
		));
		
		$chooser_type = !empty($prop["multiple"]) ? "checkbox" : "radio";
		// TODO: Would be nice to validate JS here.
		$onclick = isset($prop["onclick"]) ? $prop["onclick"] : "";
		$this->html .= <<<SCRIPT
			<script type="text/javascript">
				YUI({ fetchCSS: false }).use('button-group', function(Y) {
					var buttonGroupCB = new Y.ButtonGroup({
						srcNode: '#{$prop["name"]}-chooser-group',
						type: '{$chooser_type}',
						after: {
							'selectionChange': function(e){
								buttonGroupCB.getButtons().each(function(option) {
									if (option.get('type') === 'button') {
										console.log(option.get('name'), Y.one('#' + option.get('name')));
										Y.one('#' + option.get('name')).set('checked', false);
										option.removeClass("btn-primary");
									}
								});
								Y.Array.each(buttonGroupCB.getSelectedButtons(), function(option) {
									if (option.get('type') === 'button') {
										option.addClass("btn-primary");
										console.log(option.get('name'), Y.one('#' + option.get('name')));
										Y.one('#' + option.get('name')).set('checked', true);
									}
								});
								{$onclick}
							}
						}
					}).render();
				});
			</script>
SCRIPT;

		$prop["type"] = "text";
		$prop["value"] = $this->html;

		return array(
			$this->name => $prop
		);
	}
	
	private function __insert_level_of_options($options, $level = 0)
	{
		$options = !empty($options) ? new aw_array($options) : new aw_array();

		$html = "";
		foreach($options->get() as $key => $option)
		{
			$option = !is_array($option) ? array("caption" => $option) : $option;
			$caption = $option["caption"];
			$name = $this->name . "[" . $key . "]";
			$html .= html::button(array(
				"name" => str_replace(array("[", "]"), "_", $name),
				"value" => $caption,
				"disabled" => !empty($this->disabled[$key]),
				// TODO: Throw exception if "multiple != true" and more than one option selected.
				"class" => !empty($this->value[$key]) ? "btn btn-mini btn-primary yui3-button-selected" : "btn btn-mini",
				"post_append_text" => html::checkbox(array(
					"name" => $this->name . "[" . $key . "]",
					"checked" => !empty($this->value[$key]),
					"value" => $key,
					"style" => "display: none",
				))
			));
			if ($this->orient === "vertical")
			{
				$html .= html::linebreak();
			}
			if (!empty($option["suboptions"]))
			{
				$html .= $this->__insert_level_of_options($option["suboptions"], $level + 1);
			}
		}
		
		return $this->indented && $level ? html::div(array(
			"padding" => $this->indented ? "0 0 0 10px" : null,
			"content" => $html,
		)) : $html;
	}
}
?>