<div id="contact-address-edit" style="display: none; position: absolute; background: white; width: 500px; border: 1px solid #aaa; border-radius: 0 0 6px 6px; -webkit-border-radius: 0 0 6px 6px; top: 0; left: 300px; border-top: 0;">	
	<div class="modal-body" style="min-height: 300px; padding: 9px;">
		<div class="form-horizontal">
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="contact-address-country">Riik</label>
				<div class="controls">
					<select id="contact-address-country" data-bind="value: address_selected().country">
						<?php foreach ($country_options as $country_option_value => $country_option_caption) { ?>
						<option value="<?php echo $country_option_value ?>"><?php echo $country_option_caption ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">Maakond</label>
				<div class="controls">
					<input type="text" data-bind="value: address_selected().county_caption" data-provide="typeahead" data-address-field="county" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">Linn</label>
				<div class="controls">
					<input type="text" data-bind="value: address_selected().city_caption" data-provide="typeahead" data-address-field="city" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">Vald</label>
				<div class="controls">
					<input type="text" data-bind="value: address_selected().vald_caption" data-provide="typeahead" data-address-field="vald" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">T&auml;nav/K&uuml;la</label>
				<div class="controls">
					<input type="text" data-bind="value: address_selected().street" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">Maja/Korter</label>
				<div class="controls form-inline">
					<input type="text" data-bind="value: address_selected().house" class="input-small" /> -
					<input type="text" data-bind="value: address_selected().apartment" class="input-small" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">Postiindeks</label>
				<div class="controls">
			<input type="text" data-bind="value: address_selected().postal_code" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">Koordinaadid</label>
				<div class="controls form-inline">
					<input type="text" data-bind="value: address_selected().coord_x" placeholder="X" class="input-small" />
					<input type="text" data-bind="value: address_selected().coord_y" placeholder="Y" class="input-small" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">T&auml;psustus</label>
				<div class="controls">
					<input type="text" data-bind="value: address_selected().details" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">&Uuml;ksus</label>
				<div class="controls">
					<select id="contact-address-section" data-bind="options: sections, optionsText: 'name', optionsCaption: ' ', selectedOptions: address_selected().section">
					</select>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="">T&uuml;&uuml;bid</label>
				<div class="controls">
					<select id="contact-address-type" data-bind="selectedOptions: address_selected().type" multiple="multiple">
						<?php foreach ($address_type_options as $address_type_option_value => $address_type_option_caption) { ?>
						<option value="<?php echo $address_type_option_value ?>"><?php echo $address_type_option_caption ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="javascript:void(0)" data-bind="click: resetAddress" class="btn" style="float: left;">Katkesta</a>
		<a href="javascript:void(0)" data-bind="click: saveAddress" class="btn btn-primary" >Salvesta</a>
	</div>
</div>
<div id="contact-opening-hours-edit" style="display: none; position: absolute; background: white; width: 500px; border: 1px solid #aaa; border-radius: 0 0 6px 6px; -webkit-border-radius: 0 0 6px 6px; top: 0; left: 300px; border-top: 0;">
	<div class="modal-body" style="min-height: 300px; padding: 9px;">
		<div class="form-horizontal">
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="contact-opening-hours-days">P&auml;evad</label>
				<div class="controls" data-bind="chooser: opening_hours_selected().days, chooserSettings: { options: { 'E': 'E', 'T': 'T', 'K': 'K', 'N': 'N', 'R': 'R', 'L': 'L', 'P': 'P' }, multiple: true } ">
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="contact-opening-hours-open">Avamisaeg</label>
				<div class="controls" id="contact-opening-hours-open" data-bind="datetimepicker: opening_hours_selected().open, datetimepickerOptions: { pickDate: false }"></div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="contact-opening-hours-close">Sulgemisaeg</label>
				<div class="controls" id="contact-opening-hours-close" data-bind="datetimepicker: opening_hours_selected().close, datetimepickerOptions: { pickDate: false }"></div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="contact-opening-hours-valid_from">Kehtib alates</label>
				<div id="contact-opening-hours-valid_from" class="controls" data-bind="datetimepicker: opening_hours_selected().valid_from, datetimepickerOptions: { pickTime: false }"></div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="contact-opening-hours-valid_to">Kehtib kuni</label>
				<div id="contact-opening-hours-valid_to" class="controls" data-bind="datetimepicker: opening_hours_selected().valid_to, datetimepickerOptions: { pickTime: false }"></div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="javascript:void(0)" data-bind="click: resetOpeningHours" class="btn" style="float: left;">Katkesta</a>
		<a href="javascript:void(0)" data-bind="click: saveOpeningHours" class="btn btn-primary" >Salvesta</a>
	</div>
</div>
<div id="employees-edit" style="display: none; position: absolute; background: white; width: 700px; border: 1px solid #aaa; border-radius: 0 0 6px 6px; -webkit-border-radius: 0 0 6px 6px; top: 0; left: 200px; border-top: 0;">
	<div class="modal-body" style="min-height: 300px; padding: 9px;">
		<div class="form-horizontal">
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="employee-firstname">Eesnimi</label>
				<div class="controls">
					<input type="text" id="employee-firstname" data-bind="value: employee_selected().firstname" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="employee-lastname">Perekonnanimi</label>
				<div class="controls">
					<input type="text" id="employee-lastname" data-bind="value: employee_selected().lastname" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="employee-gender">Sugu</label>
				<div class="controls">
					<select id="employee-gender" data-bind="value: employee_selected().gender">
						<?php foreach ($gender_options as $gender_option_value => $gender_option_caption) { ?>
						<option value="<?php echo $gender_option_value ?>"><?php echo $gender_option_caption ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="employee-email">E-post</label>
				<div class="controls">
					<input type="text" id="employee-email" data-bind="value: employee_selected().email" /></td>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="employee-phone">Telefon</label>
				<div class="controls">
					<input type="text" id="employee-phone" data-bind="value: employee_selected().phone" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="employee-skills">Oskused</label>
				<div class="controls form-inline">
					<select id="employee-skills" data-bind="selectedOptions: employee_selected().skills" multiple="multiple" style="width: 450px;">
						<?php foreach ($skills_options as $skills_option_value => $skills_option_caption) { ?>
						<option value="<?php echo $skills_option_value ?>" data-caption="<?php echo trim(str_replace("&nbsp;", " ", $skills_option_caption)) ?>"><?php echo $skills_option_caption ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="javascript:void(0)" data-bind="click: resetEmployee" class="btn" style="float: left;">Katkesta</a>
		<a href="javascript:void(0)" data-bind="click: saveEmployee" class="btn btn-primary" >Salvesta</a>
	</div>
</div>
