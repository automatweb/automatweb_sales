<style type="text/css">
.modal-body {
	padding-left: 0;
	padding-right: 0;
}
.horizontal-padding-15 {
	padding-left: 15px;
	padding-right: 15px;
}
</style>
<ul class="nav nav-tabs" id="myTab">
	<li style="text-align: center" class="active"><a href="#general" data-toggle="tab"><img src="/automatweb/images/icons/32/1808.png" border="0"><br />&Uuml;ldandmed</a></li>
	<li style="text-align: center"><a href="#contact" data-toggle="tab"><img src="/automatweb/images/icons/32/223.png" border="0"><br />Kontaktid</a></li>
	<li style="text-align: center"><a href="#employees" data-toggle="tab"><img src="/automatweb/images/icons/32/1809.png" border="0"><br />T&ouml;&ouml;tajad</a></li>
<!--	<li style="text-align: center"><a href="#messages" data-toggle="tab"><img src="/automatweb/images/icons/32/1009.png" border="0"><br />Tellimused</a></li> -->
<!--	<li style="text-align: center"><a href="#settings" data-toggle="tab"><img src="/automatweb/images/icons/32/1009.png" border="0"><br />Arved</a></li> -->
<!--	<li style="text-align: center"><a href="#returns" data-toggle="tab"><img src="/automatweb/images/icons/32/1057.png" border="0"><br />Tagastused</a></li> -->
</ul>
<div id="contact-address-edit" style="display: none; position: absolute; background: white; width: 500px; border: 1px solid #aaa; border-radius: 0 0 6px 6px; -webkit-border-radius: 0 0 6px 6px; top: 0; left: 300px; border-top: 0;">	
	<div class="modal-body" style="min-height: 300px; padding: 9px;">
		<div class="form-horizontal">
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">Riik</label>
				<div class="controls">
					<select id="contact-address-country" data-bind="value: customer().address_selected().country">
						<?php foreach ($country_options as $country_option_value => $country_option_caption) { ?>
						<option value="<?php echo $country_option_value ?>"><?php echo $country_option_caption ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">Maakond</label>
				<div class="controls">
					<input type="text" data-bind="value: customer().address_selected().county_caption" data-provide="typeahead" data-address-field="county" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">Linn</label>
				<div class="controls">
					<input type="text" data-bind="value: customer().address_selected().city_caption" data-provide="typeahead" data-address-field="city" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">Vald</label>
				<div class="controls">
					<input type="text" data-bind="value: customer().address_selected().vald_caption" data-provide="typeahead" data-address-field="vald" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">T&auml;nav/K&uuml;la</label>
				<div class="controls">
					<input type="text" data-bind="value: customer().address_selected().street" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">Maja/Korter</label>
				<div class="controls form-inline">
					<input type="text" data-bind="value: customer().address_selected().house" class="input-small" /> -
					<input type="text" data-bind="value: customer().address_selected().apartment" class="input-small" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">Postiindeks</label>
				<div class="controls">
			<input type="text" data-bind="value: customer().address_selected().postal_code" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">Koordinaadid</label>
				<div class="controls form-inline">
					<input type="text" data-bind="value: customer().address_selected().coord_x" placeholder="X" class="input-small" />
					<input type="text" data-bind="value: customer().address_selected().coord_y" placeholder="Y" class="input-small" />
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 10px">
				<label class="control-label" for="customer-name">T&auml;psustus</label>
				<div class="controls">
					<input type="text" data-bind="value: customer().address_selected().details" />
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a href="javascript:void(0)" data-bind="click: customer().resetAddress" class="btn" style="float: left;">Katkesta</a>
		<a href="javascript:void(0)" data-bind="click: customer().saveAddress" class="btn btn-primary" >Salvesta</a>
	</div>
</div>
<div class="tab-content horizontal-padding-15">
	<div class="tab-pane active tabbable tabs-left" id="general">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-details" data-toggle="tab"><i class="icon-home"></i> Rekvisiidid</a></li>
			<li><a href="#general-parties" data-toggle="tab"><i class="icon-th-large"></i> Osapooled</a></li>
			<li><a href="#general-people" data-toggle="tab"><i class="icon-user"></i> V&otilde;tmeisikud</a></li>
			<li><a href="#general-owners" data-toggle="tab"><i class="icon-lock"></i> Omanikud</a></li>
			<li><a href="#general-bank-details" data-toggle="tab"><i class="icon-briefcase"></i> Pangarekvisiidid</a></li>
		</ul>   
		<div class="tab-content">
			<div class="tab-pane active" id="general-details">
				<div class="form-horizontal">
					<div class="control-group">
						<label class="control-label" for="customer-name">Nimi</label>
						<div class="controls">
							<input type="text" id="customer-name" data-bind="value: customer().name, valueUpdate: 'afterkeydown'" placeholder="Nimi">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="customer-ettevotlusvorm">&Otilde;iguslik vorm</label>
						<div class="controls">
							<select id="customer-ettevotlusvorm">
								<?php foreach ($corporate_form_options as $corporate_form_option_id => $corporate_form_option_name) { ?>
								<option value="<?php echo $corporate_form_option_id ?>"><?php echo $corporate_form_option_name ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="customer-tax_nr">KM kood</label>
						<div class="controls">
							<input type="text" id="customer-tax_nr" data-bind="value: customer().tax_nr" placeholder="KM kood">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="customer-reg_nr">Registrikood</label>
						<div class="controls">
							<input type="text" id="customer-reg_nr" data-bind="value: customer().reg_nr" placeholder="Registrikood">
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane active" id="general-parties"></div>
			<div class="tab-pane active" id="general-people"></div>
			<div class="tab-pane active" id="general-owners"></div>
			<div class="tab-pane active" id="general-bank-details"></div>
		</div>
	</div>
	<div class="tab-pane tabbable tabs-left" id="contact">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#contact-email" data-toggle="tab"><i class="icon-envelope"></i> E-postiaadressid</a></li>
			<li><a href="#contact-phone" data-toggle="tab"><i class="icon-book"></i> Telefoninumbrid</a></li>
			<li><a href="#contact-address" data-toggle="tab"><i class="icon-map-marker"></i> Postiaadressid</a></li>
		</ul>   
		<div class="tab-content">
			<div class="tab-pane active" id="contact-email">
				<h4>E-postiaadressid</h4>
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>E-post</th>
							<th>T&uuml;&uuml;p</th>
							<th>Valikud</th>
						</tr>
					</thead>
					<tbody data-bind="foreach: customer().emails">
						<tr>
							<td data-bind="text: mail"></td>
							<td data-bind="text: contact_type_caption"></td>
							<td>
								<a href="javascript:void(0)" data-bind="click: $root.customer().editEmail"><i class="icon-pencil"></i></a> &nbsp;
								<a href="javascript:void(0)" data-bind="click: $root.customer().removeEmail"><i class="icon-remove"></i></a>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td><input type="text" data-bind="value: customer().email_selected().mail" /></td>
							<td>
								<select id="contact-email-contact_type" data-bind="value: customer().email_selected().contact_type">
									<?php foreach ($email_type_options as $email_type_option_value => $email_type_option_caption) { ?>
									<option value="<?php echo $email_type_option_value ?>"><?php echo $email_type_option_caption ?></option>
									<?php } ?>
								</select>
							</td>
							<td>
								<a href="javascript:void(0)" data-bind="click: customer().saveEmail"><i class="icon-ok"></i></a> &nbsp;
								<a href="javascript:void(0)" data-bind="click: customer().resetEmail"><i class="icon-remove"></i></a>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="tab-pane" id="contact-phone">
				<h4>Telefoninumbrid</h4>
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>Telefoninumber</th>
							<th>T&uuml;&uuml;p</th>
							<th>Valikud</th>
						</tr>
					</thead>
					<tbody data-bind="foreach: customer().phones">
						<tr>
							<td data-bind="text: name"></td>
							<td data-bind="text: type_caption"></td>
							<td>
								<a href="javascript:void(0)" data-bind="click: $root.customer().editPhone"><i class="icon-pencil"></i></a> &nbsp;
								<a href="javascript:void(0)" data-bind="click: $root.customer().removePhone"><i class="icon-remove"></i></a>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td><input type="text" data-bind="value: customer().phone_selected().name" /></td>
							<td>
								<select id="contact-phone-type" data-bind="value: customer().phone_selected().type">
									<?php foreach ($phone_type_options as $phone_type_option_value => $phone_type_option_caption) { ?>
									<option value="<?php echo $phone_type_option_value ?>"><?php echo $phone_type_option_caption ?></option>
									<?php } ?>
								</select>
							</td>
							<td>
								<a href="javascript:void(0)" data-bind="click: customer().savePhone"><i class="icon-ok"></i></a> &nbsp;
								<a href="javascript:void(0)" data-bind="click: customer().resetPhone"><i class="icon-remove"></i></a>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="tab-pane" id="contact-address">
				<h4>Postiaadressid</h4>
				<table class="table table-hover table-condensed form-inline">
					<thead>
						<tr>
							<th>Riik</th>
							<th>Maakond</th>
							<th>Linn</th>
							<th>Vald</th>
							<th>T&auml;nav/k&uuml;la</th>
							<th>Maja/Korter</th>
							<th>Postiindeks</th>
							<th>Koordinaadid</th>
							<th>T&auml;psustus</th>
							<th>Valikud</th>
						</tr>
					</thead>
					<tbody data-bind="foreach: customer().addresses">
						<tr>
							<td data-bind="text: country_caption"></td>
							<td data-bind="text: county_caption"></td>
							<td data-bind="text: city_caption"></td>
							<td data-bind="text: vald_caption"></td>
							<td data-bind="text: street"></td>
							<td data-bind="text: (house() ? house() : '') + (apartment() ? (' - ' + apartment()) : '')"></td>
							<td data-bind="text: postal_code"></td>
							<td data-bind="text: (coord_x() ? coord_x() : '') + (coord_y() ? (', ' + coord_y()) : '')"></td>
							<td data-bind="text: details"></td>
							<td>
								<a href="javascript:void(0)" data-bind="click: $root.customer().editAddress"><i class="icon-pencil"></i></a> &nbsp;
								<a href="javascript:void(0)" data-bind="click: $root.customer().removeAddress"><i class="icon-remove"></i></a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="employees">
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th>Nimi</th>
					<th>Sugu</th>
					<th>E-post</th>
					<th>Telefon</th>
					<th>Oskused</th>
					<th>Valikud</th>
				</tr>
			</thead>
			<tbody data-bind="foreach: customer().employees">
				<tr>
					<td data-bind="text: name"></td>
					<td data-bind="text: gender_caption"></td>
					<td data-bind="text: email"></td>
					<td data-bind="text: phone"></td>
					<td data-bind="text: skills_caption"></td>
					<td>
						<a href="javascript:void(0)" data-bind="click: $root.customer().editEmployee"><i class="icon-pencil"></i></a> &nbsp;
						<a href="javascript:void(0)" data-bind="click: $root.customer().removeEmployee"><i class="icon-remove"></i></a>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td><input type="text" data-bind="value: customer().employee_selected().name" class="input-medium" /></td>
					<td>
						<select id="contact-employee-gender" data-bind="value: customer().employee_selected().gender" class="input-small">
							<?php foreach ($gender_options as $gender_option_value => $gender_option_caption) { ?>
							<option value="<?php echo $gender_option_value ?>"><?php echo $gender_option_caption ?></option>
							<?php } ?>
						</select>
					</td>
					<td><input type="text" data-bind="value: customer().employee_selected().email" class="input-medium" /></td>
					<td><input type="text" data-bind="value: customer().employee_selected().phone" class="input-medium" /></td>
					<td>
						<select id="contact-employee-skills" data-bind="selectedOptions: customer().employee_selected().skills" multiple="multiple">
							<?php foreach ($skills_options as $skills_option_value => $skills_option_caption) { ?>
							<option value="<?php echo $skills_option_value ?>" data-caption="<?php echo trim(str_replace("&nbsp;", " ", $skills_option_caption)) ?>"><?php echo $skills_option_caption ?></option>
							<?php } ?>
						</select>
					</td>
					<td>
						<a href="javascript:void(0)" data-bind="click: customer().saveEmployee"><i class="icon-ok"></i></a> &nbsp;
						<a href="javascript:void(0)" data-bind="click: customer().resetEmployee"><i class="icon-remove"></i></a>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
<!--	<div class="tab-pane" id="messages"></div> -->
<!--	<div class="tab-pane" id="settings"></div> -->
<!--	<div class="tab-pane" id="returns"></div> -->
</div>
