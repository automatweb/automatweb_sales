<ul class="nav nav-tabs nav-center">
	<li class="active"><a href="#general" data-toggle="tab">&Uuml;ldandmed</a></li>
	<li><a href="#settings" data-toggle="tab">Seadistused</a></li>
</ul>
<div class="tab-content horizontal-padding-15">
	<div class="tab-pane active tabbable tabs-left" id="general">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="file-name">Nimi</label>
				<div class="controls">
					<input type="text" id="file-name" data-bind="value: file().name, valueUpdate: 'afterkeydown'" placeholder="Nimi" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-status-2">Aktiivne</label>
				<div class="controls">
					<input type="radio" id="file-status-2" data-bind="checked: file().status" value="2" /> Jah
					<input type="radio" id="file-status-1" data-bind="checked: file().status" value="1" /> Ei
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-file">Vali fail</label>
				<div class="controls">
					<form method="POST" action="orb.aw?class=file&action=upload" enctype="multipart/form-data">
						<input type="file" id="file-file" name="file" data-bind="upload: file" />
					</form>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-ord">Järjekord</label>
				<div class="controls">
					<input type="text" id="file-ord" data-bind="value: file().ord" placeholder="0" class="input-small" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-comment">Kommentaar</label>
				<div class="controls">
					<textarea rows="3" id="file-comment" data-bind="value: file().comment" placeholder="Kommentaar"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-alias">Alias</label>
				<div class="controls">
					<input type="text" id="file-alias" data-bind="value: file().alias" placeholder="Alias" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-file_url">URL</label>
				<div class="controls">
					<input type="text" id="file-file_url" data-bind="value: file().file_url" placeholder="URL" />
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane tabbable tabs-left" id="settings">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="file-newwindow">Uues aknas</label>
				<div class="controls">
					<input type="checkbox" id="file-newwindow" data-bind="checked: file().newwindow" value="1" /> Uues aknas
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-show_framed">Näita raamis</label>
				<div class="controls">
					<input type="checkbox" id="file-show_framed" data-bind="checked: file().show_framed" value="1" /> Näita raamis
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file-show_icon">Näita ikooni</label>
				<div class="controls">
					<input type="checkbox" id="file-show_icon" data-bind="checked: file().show_icon" value="1" /> Näita ikooni
				</div>
			</div>
		</div>
	</div>
</div>
