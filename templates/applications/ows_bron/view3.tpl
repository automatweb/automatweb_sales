	<link rel="stylesheet" type="text/css" media="all" href="RHG Index3_files/_styles_.css" />

	<link rel="stylesheet" type="text/css" media="screen" href="RHG Index3_files/_styles0.css" />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="RHG Index3_files/_styles1.css" />
	<link rel="stylesheet" type="text/css" media="print" href="RHG Index3_files/_styles2.css" />
	<script language="JavaScript1.2" type="text/javascript" src="RHG Index3_files/_scripts.js"></script>
	<script type="text/javascript" src="RHG Index3_files/jquery00.js"></script>
	<script type="text/javascript" src="RHG Index3_files/thickbox.js"></script>
	<link rel="stylesheet" type="text/css" media="screen,projection" href="RHG Index3_files/thickbox.css" />

				<!-- start main columns -->

				<div id="main-wide">
					<div id="front1" class="clear">
						<div id="steps">
							<ul>
								<li><div>1: Basic information</div></li>
								<li><div>2: Room selection</div></li>
								<li class="active"><div>3: Booking details</div></li>
								<li><div>4: Confirmation</div></li>
							</ul>
							<div class="ending"></div>
						</div>
						<div id="front-col1">
							<form action="#">
								<div class="frontblock-2 clear">
									<div class="frontblock-2-a">
										<div class="frontblock-2-b">
											<label>
												Select currency:
												<select>
													<option>�?� EUR</option>
													<option>£ GBP</option>
													<option>$ USD</option>
												</select>
											</label>
										</div>
									</div>
								</div>

								<div class="frontblock-2 clear">
									<div class="frontblock-2-a">
										<div class="frontblock-2-c">
											<p class="heading2">How may<br /><span>we assist you?</span></p>
											<p class="country">
												<b>Estonia</b> +372 1234567<br />
												<img src="RHG Index3_files/flag_est.gif" alt="" /> <img src="RHG Index3_files/flag_eng.gif" alt="" />  <img src="RHG Index3_files/flag_rus.gif" alt="" /> <img src="RHG Index3_files/flag_fin.gif" alt="" />
											</p>
											<p class="country">
												<b>Latvia</b> +371 765431<br />
												<img src="RHG Index3_files/flag_lat.gif" alt="" /> <img src="RHG Index3_files/flag_eng.gif" alt="" />  <img src="RHG Index3_files/flag_rus.gif" alt="" /> 
											</p>
											<p class="country">
												<b>Lithuania</b> +370 1232234<br />
												<img src="RHG Index3_files/flag_lit.gif" alt="" /> <img src="RHG Index3_files/flag_eng.gif" alt="" />  <img src="RHG Index3_files/flag_rus.gif" alt="" /> 
											</p>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div id="front-col5">
							<div class="clear booking9">
								<div id="front-col6">
									<h1>Booking details</h1>
									<div class="booking3 clear">
										<div class="booking3a">
											<div class="booking3b">
												<div class="w100p">
													<table class="booking4">
														<tr>
															<th>Stay dates:</th>
															<td>28.05.07-30.05.07 (2 nights) <a href="#">change</a></td>
														</tr>
														<tr>
															<th>Hotel:</th>
															<td>Reval Hotel Olümpia <a href="#">change</a></td>
														</tr>
														<tr>
															<th>Room(s):</th>
															<td>1 room, 2 adults <a href="#">change</a></td>
														</tr>
														<tr>
															<th>Room type:
																<input type='hidden' id='default_room_type' value="Double room" /> <!-- sama tekst inputis ja td-s -->
															</th>
															<td><div id='room_type'>Double room</div></td>
														</tr>
														<tr>
															<th>My preferences:</th>
															<td class="input">
																<label onmouseover="showTT2(this);" onmouseout="hideTT2(this);" title="Tooltip text"><input type="checkbox" /> Smoking allowed</label>
																<label onmouseover="showTT2(this);" onmouseout="hideTT2(this);" title="Tooltip text"><input type="checkbox" /> Baby-cot</label>
																<label onmouseover="showTT2(this);" onmouseout="hideTT2(this);" title="Tooltip text"><input type="checkbox" /> High-floor</label>
																<label onmouseover="showTT2(this);" onmouseout="hideTT2(this);" title="Tooltip text"><input type="checkbox" /> Low-floor</label>
																<label onmouseover="showTT2(this);" onmouseout="hideTT2(this);" title="Tooltip text"><input type="checkbox" /> Bath</label>
															</td>
														</tr>
														<tr>
															<th>Comments:</th>
															<td>
																<div id="dyn1"><a href="#" onclick="return toggleItems('dyn1','dyn2');">click here to add comments</a></div>
																<div id="dyn2" style="display: none;">
																	<p>Please note that your comment will be visible for front office on you arrival day</p>
																	<textarea rows="5" cols="40" class="w200"></textarea>
																</div>
															</td>
														</tr>
													</table>
													<div id='div_final_price'>
													<table class="booking4">
														<tr class="total">
															<th valign='center'>TOTAL PRICE:</th>
															<td>
																<input type="hidden" id='total_price' value="136.00" /> <!-- hind ilma toa-upgradedeta -->
																<input type="hidden" id='currency' value="EUR"/>
																<span id='final_price'>136.00 EUR</span>
															</td>
														</tr>
													</table>
													</div>
												</div>
											</div>
										</div>
									</div>

									<h2>Contact details</h2>

									<div class="booking5">
										<div class="w100p">
											<p class="booking-firstclient clear"><a href="#"><b>Already Reval Hotel's First Client?</b><br />Please click here to log in and get special rates.</a></p>
											<h3>New user Sign Up</h3>
											<table>
												<tr>
													<th>Email:</th>
													<td><input type="text" class="default" /></td>
													<th>Password:</th>
													<td><input type="text" class="default" /></td>
												</tr>
											</table>
											<table class="check">
												<tr>
													<td><input type="checkbox" /></td>
													<th>Complete your entry profile with a personal password. When you book online again, your details will be entered automatically</th>
												</tr>
											</table>
										</div>
									</div>
									<table class="booking4">
										<tr>
											<th>First name:</th>
											<td class="input"><input type="text" class="default" /></td>
										</tr>
										<tr>
											<th>Last name:</th>
											<td class="input"><input type="text" class="default" /></td>
										</tr>
										<tr>
											<th>Date of birth:</th>
											<td class="input"><input type="text" class="small" /> (DD-MM-YYYY)</td>
										</tr>
										<tr>
											<th>Address line 1:</th>
											<td class="input"><input type="text" class="default" /></td>
										</tr>
										<tr>
											<th>Address line 2:</th>
											<td class="input"><input type="text" class="default" /></td>
										</tr>
										<tr>
											<th>Postal code:</th>
											<td class="input"><input type="text" class="small" /></td>
										</tr>
										<tr>
											<th>City:</th>
											<td class="input"><input type="text" class="default" /></td>
										</tr>
										<tr>
											<th>Country:</th>
											<td class="input"><select class="default"><option><option></select></td>
										</tr>
										<tr>
											<th>Telephone:</th>
											<td class="input">
												<select class="small-1"><option>+372<option></select>
												<input type="text" class="small-2" />
											</td>
										</tr>
										<tr>
											<th>E-mail:</th>
											<td class="input"><input type="text" class="default" /></td>
										</tr>
										<tr>
											<th>&nbsp;</th>
											<td class="input"><label><input type="checkbox" /> Please send me monthly newsletters and specials</label></td>
										</tr>
										<tr>
											<th>&nbsp;</th>
											<td class="input"><label><input type="checkbox" /> I want to participate in Reval's <b class="red">First Client</b> customer programme for best rates, promotions etc.</label></td>
										</tr>
									</table>

								</div>
								<div id="front-col7">
									<h2>Upgrade room</h2>
									<table class="booking8">
										<tr>
											<th class="input">	<!-- toa number või ID või midagi on näiteks 85 -->
												<input type="hidden" class="room_upgrade" value="85" /> 
												<input type="checkbox" name='ch_upgrade_to_room_85' id='ch_upgrade_to_room_85' class="room_upgrade_check" onclick="upgRoom(this);"/>
												<input type="hidden" name="room_price_85" id="room_price_85" value="99"/>	
											</th>
											<th><div id='room_type_85'>Royal Double Deluxe</div></th>
											<th class="price">+£99</th>
										</tr>
										<tr>
											<td colspan="3">
												<div class="clear">	<!-- ad_room_85  - id, mis on sama sama galerii piltidel -->
													<div class="img"><div><a href="http://klient.struktuur.ee/revalhotels/html/revalbooking_HTML_2007_07_12/gfx/tmp22_big.jpg" class="thickbox" rel="ad_room_85"><img src="RHG Index3_files/tmp22000.jpg" alt="Picture 1" /><br /></a></div></div>
													<div class="img"><div><a href="http://klient.struktuur.ee/revalhotels/html/revalbooking_HTML_2007_07_12/gfx/tmp23_big.jpg" class="thickbox" rel="ad_room_85"><img src="RHG Index3_files/tmp23000.jpg" alt="Picture 2" /><br /></a></div></div>
												</div>
												<p>Elegantly designed with stunning views over the Old City and free wireless internet.</p>
											</td>
										</tr>
										<tr>
											<th class="input">	<!-- toa number või ID või midagi on näiteks 86 -->
												<input type="hidden" class="room_upgrade" value="86" /> 
												<input type="checkbox" name='ch_upgrade_to_room_86' id='ch_upgrade_to_room_86' class="room_upgrade_check" onclick="upgRoom(this);"/>
												<input type="hidden" name="room_price_86" id="room_price_86" value="199">	
											</th>
											<th><div id='room_type_86'>Royal Tripple Deluxe</div></th>
											<th class="price">+£199</th>
										</tr>
										<tr>
											<td colspan="3">
												<div class="clear"><!-- ad_room_86  - id, mis on sama sama galerii piltidel -->
													<div class="img"><div><a href="http://klient.struktuur.ee/revalhotels/html/revalbooking_HTML_2007_07_12/gfx/tmp22_big.jpg" class="thickbox" rel="ad_room_86"><img src="RHG Index3_files/tmp22000.jpg" alt="" /><br /></a></div></div>
													<div class="img"><div><a href="http://klient.struktuur.ee/revalhotels/html/revalbooking_HTML_2007_07_12/gfx/tmp23_big.jpg" class="thickbox" rel="ad_room_86"><img src="RHG Index3_files/tmp23000.jpg" alt="" /><br /></a></div></div>
												</div>
												<p>Elegantly designed with stunning views over the Old City and free wireless internet.</p>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<form action="http://klient.struktuur.ee/revalhotels/html/revalbooking_HTML_2007_07_12/4_booking_step4.html">
								<p class="booking3 booking3-special">
									<input type="submit" value="Review Your Reservation" />
								</p>
							</form>
						</div>
					</div>
				</div>