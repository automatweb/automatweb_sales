	<link rel="stylesheet" type="text/css" media="all" href="{VAR:baseurl}/img/reval/_styles_.css" />

	<link rel="stylesheet" type="text/css" media="screen" href="{VAR:baseurl}/img/reval/_styles0.css" />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="{VAR:baseurl}/img/reval/_styles1.css" />
	<link rel="stylesheet" type="text/css" media="print" href="{VAR:baseurl}/img/reval/_styles2.css" />
	<script language="JavaScript1.2" type="text/javascript" src="{VAR:baseurl}/img/reval/_scripts.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/img/reval/jquery00.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/img/reval/thickbox.js"></script>
	<link rel="stylesheet" type="text/css" media="screen,projection" href="{VAR:baseurl}/img/reval/thickbox.css" />

				<div id="main-wide">
					<div id="front1" class="clear">
						<div id="steps">
							<ul>
								<li><div>1: Basic information</div></li>
								<li class="active"><div>2: Room selection</div></li>
								<li><div>3: Booking details</div></li>
								<li><div>4: Confirmation</div></li>
							</ul>
							<div class="ending"></div>
						</div>
						<div id="front-col1">
							<form action="index.aw">
								<div class="frontblock-2 clear">
									<div class="frontblock-2-a">
										<div class="frontblock-2-b">
											<label>
												Select currency:
												<select>
													<option>?‚¬ EUR</option>
													<option>?£ GBP</option>
													<option>$ USD</option>
												</select>
											</label>
										</div>
									</div>
								</div>

								<div class="frontblock-1-header"><p>Modify Your Search</p></div>
								<div class="frontblock-1 clear">
									<div class="frontblock-1-a">
										<div class="frontblock-1-b">
											<p><label for="i_location">Location / Hotel</label></p>
											<p class="input">
												<select class="default" name="i_location" id="i_location">
													<optgroup label="Estonia, Tallinn">
														<option>Reval Hotel Ol?¼mpia</option>
														<option>Reval Hotel Central</option>
														<option>Reval Park Hotel & Casino</option>
														<option>Reval Inn Tallinn</option>
													</optgroup>
													<optgroup label="Latvia, Riga">
														<option>Reval Hotel Latvija</option>
														<option>Reval Hotel RÄ«dzene</option>
													</optgroup>
													<optgroup label="Lithuania, Vilnius">
														<option>Reval Hotel Lietuva</option>
														<option>Reval Inn Vilnius</option>
													</optgroup>
													<optgroup label="Lithuania, Klaipeda">
														<option>Reval Inn Klaipeda</option>
													</optgroup>
												</select>
											</p>
											<p><label for="i_checkin">Check-in</label></p>
											<p class="input">
												<input type="text" class="date" value="12.12.2007" id="i_checkin" name="i_checkin" />
												<a href="#" onclick="return tmpShowCal(this);"><img src="{VAR:baseurl}/img/reval/ico_calendar.gif" alt="Open calendar" class="ico" /></a>
												<span class="dayname">TUE</span>
											</p>
											<p><label for="i_checkout">Check-out</label></p>
											<p class="input">
												<input type="text" class="date" value="12.12.2007" id="i_checkout" name="i_checkout" />
												<a href="#" onclick="return tmpShowCal(this);"><img src="{VAR:baseurl}/img/reval/ico_calendar.gif" alt="Open calendar" class="ico" /></a>
												<span class="dayname">TUE</span>
											</p>
											<p>
												<label for="i_rooms">No. of rooms:</label>
												<select class="bold" id="i_rooms" name="i_rooms" onchange='chRooms(this);'>
													<option value='1'>1</option>
													<option value='2'>2</option>
													<option value='3'>3</option>
													<option value='4'>4</option>
												</select>
											</p>
											<p class="group"><a href="#">Group booking form</a></p>

											<div class="room" id='room1'>
												<div class="room-a">
													<div class="room-1 clear">
														<label>
															Adults:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
														<label>
															<img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Example tooltip1" onmouseover="showTT2(this);" onmouseout="hideTT2(this);" />
															Children:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
													</div>
												</div>
											</div>
											<div class="room" id='room2' style='display:none;'>
												<div class="room-a">
													<div class="room-2 clear">
														<label>
															Adults:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
														<label>
															<img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Example tooltip1" onmouseover="showTT2(this);" onmouseout="hideTT2(this);" />
															Children:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
													</div>
												</div>
											</div>
											<div class="room" id='room3' style='display:none;'>
												<div class="room-a">
													<div class="room-3 clear">
														<label>
															Adults:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
														<label>
															<img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Example tooltip1" onmouseover="showTT2(this);" onmouseout="hideTT2(this);" />
															Children:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
													</div>
												</div>
											</div>
											<div class="room" id='room4' style='display:none;'>
												<div class="room-a">
													<div class="room-4 clear">
														<label>
															Adults:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
														<label>
															<img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Example tooltip1" onmouseover="showTT2(this);" onmouseout="hideTT2(this);" />
															Children:
															<select class="bold">
																<option>1</option>
																<option>2</option>
																<option>3</option>
																<option>4</option>
															</select>
														</label>
													</div>
												</div>
											</div>
											<p class="button clear"><span><span><input type="submit" value="Modify"></span></span></p>
										</div>
									</div>
								</div>

								<div class="frontblock-2 clear">
									<div class="frontblock-2-a">
										<div class="frontblock-2-c">
											<p class="heading2">How may<br /><span>we assist you?</span></p>
											<p class="country">
												<b>Estonia</b> +372 1234567<br />
												<img src="{VAR:baseurl}/img/reval/flag_est.gif" alt="" /> <img src="{VAR:baseurl}/img/reval/flag_eng.gif" alt="" />  <img src="{VAR:baseurl}/img/reval/flag_rus.gif" alt="" /> <img src="{VAR:baseurl}/img/reval/flag_fin.gif" alt="" />
											</p>
											<p class="country">
												<b>Latvia</b> +371 765431<br />
												<img src="{VAR:baseurl}/img/reval/flag_lat.gif" alt="" /> <img src="{VAR:baseurl}/img/reval/flag_eng.gif" alt="" />  <img src="{VAR:baseurl}/img/reval/flag_rus.gif" alt="" /> 
											</p>
											<p class="country">
												<b>Lithuania</b> +370 1232234<br />
												<img src="{VAR:baseurl}/img/reval/flag_lit.gif" alt="" /> <img src="{VAR:baseurl}/img/reval/flag_eng.gif" alt="" />  <img src="{VAR:baseurl}/img/reval/flag_rus.gif" alt="" /> 
											</p>
										</div>
									</div>
								</div>
								{VAR:reforb1}
							</form>
							<!-- calendar example -->
							<div class="calendar2" id="calendar2">
								<div class="a">
									<table>
										<tr>
											<th><a href="#"><img src="{VAR:baseurl}/img/reval/ico_left.gif" alt="Previous month" /></a></th>
											<th colspan="4"><div class="month">February 2007</div></th>
											<th><a href="#"><img src="{VAR:baseurl}/img/reval/ico_right.gif" alt="Next month" /></a></th>
											<th><a href="#" onclick="return tmpHideCal();"><img src="{VAR:baseurl}/img/reval/ico_close.gif" alt="Close" /></a></th>
										</tr>
										<tr>
											<th><div>Mon</div></th>
											<th><div>Tue</div></th>
											<th><div>Wed</div></th>
											<th><div>Thu</div></th>
											<th><div>Fri</div></th>
											<th class="weekend"><div>Sat</div></th>
											<th class="weekend"><div>Sun</div></th>
										</tr>
										<tr>
											<td><a href="#" class="high">1</a></th>
											<td><a href="#" class="medium">2</a></th>
											<td><div class="na">3</div></th>
											<td><a href="#">4</a></th>
											<td><a href="#">5</a></th>
											<td class="weekend"><a href="#">6</a></th>
											<td class="weekend"><a href="#">7</a></th>
										</tr>
										<tr>
											<td><a href="#">8</a></th>
											<td><a href="#">9</a></th>
											<td><a href="#">10</a></th>
											<td><a href="#" class="medium">11</a></th>
											<td><a href="#" class="medium">12</a></th>
											<td class="weekend"><a href="#" class="medium">13</a></th>
											<td class="weekend"><a href="#">14</a></th>
										</tr>
										<tr>
											<td><a href="#">15</a></th>
											<td><a href="#">16</a></th>
											<td><a href="#">17</a></th>
											<td><a href="#">18</a></th>
											<td><a href="#" class="high">19</a></th>
											<td class="weekend"><a href="#" class="high">20</a></th>
											<td class="weekend"><a href="#">21</a></th>
										</tr>
										<tr>
											<td><a href="#">22</a></th>
											<td><a href="#">23</a></th>
											<td><a href="#">24</a></th>
											<td><a href="#">25</a></th>
											<td><a href="#">26</a></th>
											<td class="weekend"><a href="#">27</a></th>
											<td class="weekend"><a href="#">28</a></th>
										</tr>
										<tr>
											<td><a href="#">29</a></th>
											<td><a href="#">30</a></th>
											<td><a href="#">31</a></th>
											<td><div>&nbsp;</div></th>
											<td><div>&nbsp;</div></th>
											<td><div>&nbsp;</div></th>
											<td><div>&nbsp;</div></th>
										</tr>
									</table>
									<dl class="legend clear">
										<dt>Rates</dt>
										<dd class="high">High</dd>
										<dd class="medium">Medium</dd>
										<dd class="low">Low</dd>
										<dd class="na">N/A</dd>
									</ul>
								</div>
							</div>
							<!-- / calendar example -->
								<!-- tooltip -->
								<div class="tooltip2" id="tooltip2">
									<p id='tooltip2Cont'></p>
									<div class="ending"></div>
								</div>
								<!-- / tooltip -->
						</div>
						<div id="front-col5">
							<div class="booking2 clear">
								<div class="booking2-col1">
									<div class="a"><div class="b"><a href="{VAR:HotelUrl}" class='thickbox'><img src="{VAR:HotelPic}" alt=""/></a></div></div>
								</div>
								<div class="booking2-col2">
									<h1>{VAR:HotelName}</h1>
									<div class="pad">
										<p><b>{VAR:HotelDesc}</b></p>
										<p>{VAR:HotelAddress}<br />Ph: + {VAR:HotelPhone}</p>
										<p><a href="{VAR:HotelUrl}"><img src="{VAR:baseurl}/img/reval/ico_more.gif" alt="" />Hotel homepage</a> &nbsp; <a href="{VAR:HotelMap}" target="_blank"><img src="{VAR:baseurl}/img/reval/ico_more.gif" alt="" />Hotel Map</a></p>
									</div>
								</div>
								<div class="booking2-col3">
									<h2>Hotel amenities</h2>
									<ul class="clear">
										<!-- SUB: HotelAmenities -->
										<li><div>{VAR:Item}</div></li>
										<!-- END SUB: HotelAmenities -->
									</ul>
								</div>
							</div>

							<form action="index.aw">
								<table class="booking1">
									<!-- SUB: RateList -->
									<tr>
										<th colspan="2"><b class="red">{VAR:Name}</b> {VAR: Title}</th>
										<th class="right">Offer type</th>
										<th><b>Avg</b></th>
										<th><b>Total</b></th>
										<th class="last center"><b>BOOK</b></th>
									</tr>
									<tr>
										<td rowspan="3" class="images">
											<div class="clear">
												<div class="img"><div><a href="{VAR:Slideshow}" class="thickbox" rel="ad_room_71"><img src="{VAR:Pic}" alt="" /><br /></a></div></div>
											</div>
										</td>
										<td rowspan="3" class="main">{VAR:Note} <a href="popup_roominfo.html?TB_iframe=true&height=500&width=400" class='thickbox' title='More'><img src="{VAR:baseurl}/img/reval/ico_more.gif" alt="" />More</a></td>
										<td class="nowrap right"><a href="popup_rateinfo.html?TB_iframe=true&height=400&width=600" class='thickbox' title='Room rate information'><img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Room rate information" /> Refundable</a></td>
										<td>?£99</td>
										<td>?£299</td>
										<td class="input center"><input type="radio" name="radio1" /></td>
									</tr>
									<tr>
										<td class="nowrap right"><a href="popup_rateinfo.html?TB_iframe=true&height=400&width=600" class='thickbox' title='Room rate information'><img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Room rate information" /> Non-refundable</a></td>
										<td>?£99</td>
										<td>?£299</td>
										<td class="input center"><input type="radio" name="radio1" /></td>
									</tr>
									<tr class="special">
										<td class="nowrap right"><a href="popup_rateinfo.html?TB_iframe=true&height=400&width=600" class='thickbox' title='Room rate information'><img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Room rate information" /> Family package</a></td>
										<td>?£99</td>
										<td>?£299</td>
										<td class="input center"><input type="radio" name="radio1" /></td>
									</tr>
									<tr>
										<td class="separator" colspan="5">&nbsp;</td>
									</tr>
									<!-- END SUB: RateList -->
								</table>
								<div class="separator1"></div>
								<p class="terms"><img src="{VAR:baseurl}/img/reval/ico_more.gif" alt="" /> Please read our <a href="popup_termsandconditions.html?TB_iframe=true&height=400&width=600" class='thickbox' title='Terms and Conditions'>Terms and conditions</a></p>
								<p class="booking3">
									<input type="submit" value="Select and continue" />
								</p>
								{VAR:reforb2}
							</form>
						</div>
					</div>
				</div>