<ul class="nav nav-tabs nav-center">
	<li class="active"><a href="#general" data-toggle="tab">&Uuml;ldandmed</a></li>
</ul>
<div class="tab-content horizontal-padding-15">
	<div class="tab-pane active tabbable tabs-left" id="general">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="link-name">Nimi</label>
				<div class="controls">
					<input type="text" id="link-name" data-bind="value: link().name, valueUpdate: 'afterkeydown'" placeholder="Nimi">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="link-comment">Kommentaar</label>
				<div class="controls">
					<textarea rows="3" id="link-comment" data-bind="value: link().comment" placeholder="Kommentaar"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="link-alt">Alt-tekst</label>
				<div class="controls">
					<input type="text" id="link-alt" data-bind="value: link().alt" placeholder="Alt">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="link-url">URL</label>
				<div class="controls">
					<input type="text" id="link-url" data-bind="value: link().url" placeholder="http://">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="link-newwindow">Uues aknas</label>
				<div class="controls">
					<input type="checkbox" id="link-newwindow" data-bind="checked: link().newwindow" />
				</div>
			</div>
		</div>
	</div>
</div>
