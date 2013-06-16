<?php

class period_filter extends aw_template
{
	function init_vcl_property($arr)
	{
		$html = "";
		
		$html .= html::span(array("class" => "awcbPropCaption", "content" => t("K&auml;esolev")));
		$html .= $this->__chooser($arr["property"], "current");
		
		$html .= html::span(array("class" => "awcbPropCaption", "content" => t("M&ouml;&ouml;dunud")));
		$html .= $this->__chooser($arr["property"], "previous");
		
		$html = html::div(array(
			"id" => "{$arr["property"]["name"]}-chooser-group",
			"content" => $html
		));
		
		// TODO: Would be nice to validate JS here.
		$onclick = isset($arr["property"]["onclick"]) ? $arr["property"]["onclick"] : "";
		$html .= <<<SCRIPT
			<script type="text/javascript">
				YUI({ fetchCSS: false }).use('button-group', function(Y) {
					Y.one('body').addClass('yui3-skin-sam');
						var buttonGroupCB = new Y.ButtonGroup({
						srcNode: '#{$arr["property"]["name"]}-chooser-group',
						type: 'radio',
						after: {
							'selectionChange': function(e){
								buttonGroupCB.getButtons().each(function(option) {
									Y.one('#' + option.get('name')).set('checked', false);
									option.removeClass("btn-primary");
								});
								Y.Array.each(buttonGroupCB.getSelectedButtons(), function(option) {
									option.addClass("btn-primary");
									Y.one('#' + option.get('name')).set('checked', true);
								});
								(function(){
									{$onclick}
								})();
							}
						}
					}).render();
				});
			</script>
SCRIPT;
		
		$arr["property"]["type"] = "text";
		$arr["property"]["value"] = $html;
		$arr["property"]["no_caption"] = 1;
		return array(
			$arr["property"]["name"] => $arr["property"]
		);
	}
	
	// FIXME: There's a duplication of code between this class and htmlclient!
	private function __chooser($prop, $period = "current")
	{
		$html = "";
		$options = isset($prop["options"][$period]) ? new aw_array($prop["options"][$period]) : new aw_array();
		foreach($options->get() as $key => $val)
		{
			$caption = $val;
			$checked = !empty($prop["value"][$key]);
			$name = $prop["name"] . "[" . $key . "]";
			$html .= html::button(array(
				"name" => str_replace(array("[", "]"), "_", $name),
				"value" => $caption,
				"disabled" => ifset($prop, "disabled", $key),
				// TODO: Throw exception if "multiple != true" and more than one option selected.
				"class" => $checked ? "btn btn-mini btn-primary yui3-button-selected" : "btn btn-mini",
				"post_append_text" => html::checkbox(array(
					"name" => $prop["name"] . "[" . $key . "]",
					"checked" => $checked,
					"value" => $key,
					"style" => "display: none",
				))
			));
		}
		
		return html::div(array(
			"id" => "{$prop["name"]}-chooser-group-{$period}",
			"content" => $html,
			"padding" => "0 0 2px 0"
		));
	}

	function process_vcl_property($arr)
	{
	}
}
