<ul class="nav nav-tabs nav-center">
	<li class="active"><a href="#general" data-toggle="tab">&Uuml;ldandmed</a></li>
</ul>
<div class="tab-content horizontal-padding-15">
	<div class="tab-pane active tabbable tabs-left" id="general">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="menu-name">Nimi</label>
				<div class="controls">
					<input type="text" id="menu-name" data-bind="value: menu().name, valueUpdate: 'afterkeydown'" placeholder="Nimi">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="menu-comment">Kommentaar</label>
				<div class="controls">
					<textarea rows="3" id="menu-comment" data-bind="value: menu().comment" placeholder="Kommentaar"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="menu-status-2">Aktiivne</label>
				<div class="controls">
					<input type="radio" id="menu-status-2" data-bind="checked: menu().status" value="2" /> Jah
					<input type="radio" id="menu-status-1" data-bind="checked: menu().status" value="1" /> Ei
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="menu-status_recursive">Aktiveeri/deaktiveeri ka alamkaustad</label>
				<div class="controls">
					<input type="checkbox" id="menu-status_recursive" data-bind="checked: menu().status_recursive" value="1" /> Uues aknas
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="menu-alias">Alias</label>
				<div class="controls">
					<textarea rows="3" id="menu-alias" data-bind="value: menu().alias" placeholder="Alias"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="menu-ord">Järjekord</label>
				<div class="controls">
					<textarea rows="3" id="menu-ord" data-bind="value: menu().ord" placeholder="0"></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
