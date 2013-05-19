<ul class="nav nav-tabs nav-center">
	<li class="active"><a href="#general" data-toggle="tab">&Uuml;ldandmed</a></li>
	<li><a href="#settings" data-toggle="tab">Seadistused</a></li>
</ul>
<div class="tab-content horizontal-padding-15">
	<div class="tab-pane active tabbable tabs-left" id="general">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="document-title">Pealkiri</label>
				<div class="controls">
					<input type="text" id="document-title" data-bind="value: document().title, valueUpdate: 'afterkeydown'" placeholder="Nimi" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-status-2">Aktiivne</label>
				<div class="controls">
					<input type="radio" id="document-status-2" data-bind="checked: document().status" value="2" /> Jah
					<input type="radio" id="document-status-1" data-bind="checked: document().status" value="1" /> Ei
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-lead">Sissejuhatus</label>
				<div class="controls">
					<textarea rows="10" id="document-lead" data-bind="value: document().lead" placeholder="Sissejuhatus" class="input-xxlarge"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-content">Sisu</label>
				<div class="controls">
					<textarea rows="30" id="document-content" data-bind="value: document().content" placeholder="Sisu" class="input-xxlarge"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-moreinfo">Toimetamata</label>
				<div class="controls">
					<textarea rows="10" id="document-moreinfo" data-bind="value: document().moreinfo" placeholder="Toimetamata" class="input-xxlarge"></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane tabbable tabs-left" id="settings">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="document-show_title">Näita pealkirja</label>
				<div class="controls">
					<input type="checkbox" id="document-show_title" data-bind="checked: document().show_title" value="1" /> Näita pealkirja
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-showlead">Näita leadi</label>
				<div class="controls">
					<input type="checkbox" id="document-showlead" data-bind="checked: document().showlead" value="1" /> Näita leadi
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-show_modified">Näita muutmise kuupäeva</label>
				<div class="controls">
					<input type="checkbox" id="document-show_modified" data-bind="checked: document().show_modified" value="1" /> Näita muutmise kuupäeva
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-esilehel">Esilehel</label>
				<div class="controls">
					<input type="checkbox" id="document-esilehel" data-bind="checked: document().esilehel" value="1" /> Esilehel
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="document-title_clickable">Pealkiri klikitav</label>
				<div class="controls">
					<input type="checkbox" id="document-title_clickable" data-bind="checked: document().title_clickable" value="1" /> Pealkiri klikitav
				</div>
			</div>
		</div>
	</div>
</div>
