<div id="front-col1"><link rel="stylesheet" type="text/css" media="all" href="{VAR:baseurl}/img/reval/_styles_.css" />

	<link rel="stylesheet" type="text/css" media="screen" href="{VAR:baseurl}/img/reval/_styles0.css" />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="{VAR:baseurl}/img/reval/_styles1.css" />
	<link rel="stylesheet" type="text/css" media="print" href="{VAR:baseurl}/img/reval/_styles2.css" />
	<script language="JavaScript1.2" type="text/javascript" src="{VAR:baseurl}/img/reval/_scripts.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/img/reval/jquery00.js"></script>

	<link rel="shortcut icon" type="image/ico" href="{VAR:baseurl}/img/reval/favicon0.ico" />

	<!-- calendar -->
	<link href="{VAR:baseurl}/img/reval/calendar.css" rel="stylesheet" type="text/css" media="screen, projection" />
	<script type="text/javascript" src="{VAR:baseurl}/img/reval/calendar.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/img/reval/calendas.js"></script>
	<script type="text/javascript" src="{VAR:baseurl}/img/reval/calendat.js"></script>
	<!-- /calendar -->
	<!-- flash activate -->
	<script src="{VAR:baseurl}/img/reval/AC_RunAc.js" type="text/javascript"></script>
	<script src="{VAR:baseurl}/img/reval/AC_Activ.js" type="text/javascript"></script>
								<p class="img-bestprice"><img src="{VAR:baseurl}/img/reval/label_be.gif" alt="Best price guarantee" /></p>
								<p class="heading1">Booking</p>
								<ul id="fronttabs">
									<li id="fronttabs1" class="active"><div><a href="#" onclick="return switchTabs('fronttabs','fronttabsc','1');">Rooms</a></div></li>
									<li id="fronttabs2"><div><a href="#" onclick="return switchTabs('fronttabs','fronttabsc','2');">Other services</a></div></li>
								</ul>
								<form action="index.aw">
									<div class="frontblock-1 clear">
										<div class="frontblock-1-a">
											<div class="frontblock-1-b" id="fronttabsc">
												<div id="fronttabsc1">
													<p><label for="i_location">Location / Hotel</label></p>
													<p class="input">
														<select class="default" name="i_location" id="i_location">
															<optgroup label="Estonia, Tallinn">
																<option value="27">Reval Hotel Ol&uuml;mpia</option>
																<option value="37">Reval Hotel Central</option>
																<option value="39">Reval Park Hotel & Casino</option>
																<option value="38">Reval Inn Tallinn</option>
															</optgroup>
															<optgroup label="Latvia, Riga">
																<option value="40">Reval Hotel Latvija</option>
																<option value="41">Reval Hotel Ridzene</option>
															</optgroup>
															<optgroup label="Lithuania, Vilnius">
																<option value="42">Reval Hotel Lietuva</option>
																<option value="42">Reval Hotel Lietuva</option>
																<option value="17969">Reval Inn Vilnius</option>
															</optgroup>
															<optgroup label="Lithuania, Klaipeda">
																<option value="17971">Reval Inn Klaipeda</option>
																<option value="18941">Reval Hotels Elizabete</option>
															</optgroup>
														</select>
													</p>
													<p><label for="i_checkin">Check-in</label></p>
													<p class="input">
														<input type="text" class="date" value="{VAR:currentdate}" id="i_checkin" name="i_checkin" onfocus="return tmpShowCal(this);" onblur="tmpHideCal(this);" />
														<a href="#" onclick="return tmpShowCal(this);"><img src="{VAR:baseurl}/img/reval/ico_cale.gif" alt="Open calendar" class="ico" /></a>
														<span class="dayname">TUE</span>
													</p>
													<p><label for="i_checkout">Check-out</label></p>
													<p class="input">
														<input type="text" class="date" value="{VAR:currentdate}" id="i_checkout" name="i_checkout" onfocus="return tmpShowCal(this);" onblur="tmpHideCal(this);"/>
														<a href="#" onclick="return tmpShowCal(this);"><img src="{VAR:baseurl}/img/reval/ico_cale.gif" alt="Open calendar" class="ico" /></a>
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
																<label class="error">
																	Adults:
																	<select class="bold">
																		<option>1</option>
																		<option selected="selected">2</option>
																		<option>3</option>
																		<option>4</option>
																	</select>
																</label>
																<label class="error">
																	<img src="{VAR:baseurl}/img/reval/ico_info.gif" alt="Example tooltip1" onmouseover="showTT2(this);" onmouseout="hideTT2(this);" />
																	Children:
																	<select class="bold">
																		<option>1</option>
																		<option>2</option>
																		<option selected="selected">3</option>
																		<option>4</option>
																	</select>
																</label>
																<p class="error">Maximum number of persons per room is 4. Please review</p>

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

													<p><label for="i_promo">I have a promotional code:</label></p>
													<p class="input"><input type="text" class="date" id="i_promo" name="i_promo" /></p>
													<p class="button clear"><span><span><input type="submit" value="Check availability"></span></span></p>
													<p class="cancel"><a href="#">Cancel my reservation</a></p>
												</div>
												<div id="fronttabsc2" style="display: none;">
													<p><img src="gfx/ico_sauna.gif" alt="" title="" /></p>
													<p>
														<select class="default">
															<option>- Choose Sauna -</option>
															<option>Reval Hotel Olümpia Sauna</option>
															<option>Reval Hotel Olümpia Infrared Sauna</option>
															<option>Reval Hotel Central Sauna</option>
														</select>
													</p>

													<p><img src="gfx/ico_booking.gif" alt="" title="" /></p>
													<p>
														<select class="default">
															<option>- Choose Restaurant -</option>
															<option>Reval Hotel Olümpia</option>
														</select>
													</p>

													<p><img src="gfx/ico_solarium.gif" alt="" title="" /></p>
													<p>
														<select class="default">
															<option>- Choose Solarium -</option>
															<option>Reval Hotel Olümpia</option>
														</select>
													</p>

													<p><img src="gfx/ico_cakes.gif" alt="" title="" /></p>
													<p>
														<select class="default">
															<option>- Choose Cakes -</option>
															<option>Reval Hotel Olümpia</option>
														</select>
													</p>
												</div>
											</div>
											<div class="login" onmouseover="showTT2(this);" onmouseout="hideTT2(this);" title="Tooltip text">
												<label>
													Log in:
													<select>
														<option></option>
														<option>Firct client</option>
														<option>Something else</option>
													</select>
												</label>
											</div>
										</div>
									</div>
									{VAR:reforb}
								</form>
								<!-- calendar example -->
								<div class="calendar2" id="calendar2">
									<div class="a">
										<table>
											<tr>
												<th><a href="#"><img src="gfx/ico_left.gif" alt="Previous month" /></a></th>
												<th colspan="4"><div class="month">February 2007</div></th>
												<th><a href="#"><img src="gfx/ico_right.gif" alt="Next month" /></a></th>
												<th><a href="#" onclick="return tmpHideCal();"><img src="gfx/ico_close.gif" alt="Close" /></a></th>
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
												<td><a href="#" class="low">4</a></th>
												<td><a href="#" class="low">5</a></th>
												<td class="weekend"><a href="#" class="low">6</a></th>
												<td class="weekend"><a href="#" class="low">7</a></th>
											</tr>
											<tr>
												<td><a href="#" class="low">8</a></th>
												<td><a href="#" class="low">9</a></th>
												<td><a href="#" class="low">10</a></th>
												<td><a href="#" class="medium">11</a></th>
												<td><a href="#" class="medium">12</a></th>
												<td class="weekend"><a href="#" class="medium">13</a></th>
												<td class="weekend"><a href="#" class="low">14</a></th>
											</tr>
											<tr>
												<td><a href="#" class="low">15</a></th>
												<td><a href="#" class="low">16</a></th>
												<td><a href="#" class="low">17</a></th>
												<td><a href="#" class="low">18</a></th>
												<td><a href="#" class="high">19</a></th>
												<td class="weekend"><a href="#" class="high">20</a></th>
												<td class="weekend"><a href="#" class="low">21</a></th>
											</tr>
											<tr>
												<td><a href="#" class="low">22</a></th>
												<td><a href="#" class="low">23</a></th>
												<td><a href="#" class="low">24</a></th>
												<td><a href="#" class="low">25</a></th>
												<td><a href="#" class="low">26</a></th>
												<td class="weekend"><a href="#" class="low">27</a></th>
												<td class="weekend"><a href="#" class="low">28</a></th>
											</tr>
											<tr>
												<td><a href="#" class="low">29</a></th>
												<td><a href="#" class="low">30</a></th>
												<td><a href="#" class="low">31</a></th>
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