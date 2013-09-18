<?php if (users::is_logged_in()) {
class main_tpl_submenu_hack {
	private static $knockouts = array();
	
	public static function add_knockout ($parent, $id, $name, $url, $comment, $link, $alias, $target, $users_only, $no_menus, $ord, $active = false, $meta = array()) {
		if ($id == aw_session::get("aw_portal_id")) {
			$meta["innernav"] = true;
		}
		self::$knockouts[$id] = array(
			"id" => $id,
			"ord" => $ord,
			"name" => $name,
			"comment" => $comment,
			"alias" => $alias,
			"link" => $link,
			"target" => (bool)$target,
			"users_only" => (bool)$users_only,
			"no_menus" => (bool)$no_menus,
			"url" => $url,
			"parent" => $parent,
			"active" => $active,
			"meta" => $meta
		);
	}
	
	public static function get_knockout () {
		foreach (array_keys(self::$knockouts) as $id) {
			if (isset(self::$knockouts[$id])) {
				self::$knockouts[$id]["children"] = self::grab_children($id);
			}
		}
		return self::$knockouts;
	}
	
	private static function grab_children ($parent) {
		$children = array();
		foreach (self::$knockouts as $id => $data) {
			if ($parent === $data["parent"]) {
				unset(self::$knockouts[$id]);
				$data["children"] = self::grab_children($id);
				$children[] = $data;
			}
		}
		return $children;
	}
}
?>
<a href="#" class="btn nav-edit"><i class="icon-pencil"></i></a>
<?php
$editor = new web_navigation_editor();
echo preg_replace("/{VAR\:prefix}/", "", $editor->render());
?>
<div class="container" style="position: relative;">
	<div style="position: absolute; right: 0;">
		<div class="user-menu">
			<?php if (users::is_logged_in()) { ?>
			<a href="{VAR:baseurl}34729" class="btn"><i class="icon-user"></i> {VAR:user}, {VAR:company}</a>
			<?php } ?>
		</div>
		<div class="user-menu">
			<ul class="nav nav-pills">
				<li class="dropdown">
					<ul id="menu1" class="dropdown-menu" role="menu" data-bind="foreach: children" aria-labelledby="portals">
						<!-- ko if: meta.portal -->
							<li role="presentation"><a role="menuitem" tabindex="-1" data-bind="text: name, attr: { href: url }"></a></li>
						<!-- /ko -->
						<!-- SUB: MENU_VASAK_L1_ITEM -->
						<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, false, array("portal" => true)); ?>
						<?php function menu{VAR:section}(){ ?>
							<!-- SUB: MENU_VASAK_L2_ITEM_SEL -->
							<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true, array("sidenav" => true)); ?>
							<!-- SUB: HAS_SUBITEMS_VASAK_L2_SEL -->
									<!-- SUB: MENU_VASAK_L3_ITEM -->
									<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
										<!-- SUB: MENU_VASAK_L4_ITEM -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
										<!-- END SUB: MENU_VASAK_L4_ITEM -->
										<!-- SUB: MENU_VASAK_L4_ITEM_SEL -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- SUB: MENU_VASAK_L5_ITEM -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM -->
											<!-- SUB: MENU_VASAK_L5_ITEM_SEL -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM_SEL -->
										<!-- END SUB: MENU_VASAK_L4_ITEM_SEL -->
									<!-- END SUB: MENU_VASAK_L3_ITEM -->
									<!-- SUB: MENU_VASAK_L3_ITEM_SEL -->
									<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
										<!-- SUB: MENU_VASAK_L4_ITEM -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
										<!-- END SUB: MENU_VASAK_L4_ITEM -->
										<!-- SUB: MENU_VASAK_L4_ITEM_SEL -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- SUB: MENU_VASAK_L5_ITEM -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM -->
											<!-- SUB: MENU_VASAK_L5_ITEM_SEL -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM_SEL -->
										<!-- END SUB: MENU_VASAK_L4_ITEM_SEL -->
									<!-- END SUB: MENU_VASAK_L3_ITEM_SEL -->
							<!-- END SUB: HAS_SUBITEMS_VASAK_L2_SEL -->
							<!-- END SUB: MENU_VASAK_L2_ITEM_SEL -->
							<!-- SUB: MENU_VASAK_L2_ITEM -->
							<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
							<?php $active = false; ?>
							<?php $has_subitems = false; ?>
							<!-- SUB: HAS_SUBITEMS_VASAK_L2_SEL -->
							<?php $has_subitems = true; ?>
									<!-- SUB: MENU_VASAK_L3_ITEM -->
									<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
										<!-- SUB: MENU_VASAK_L4_ITEM -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- SUB: MENU_VASAK_L5_ITEM -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM -->
											<!-- SUB: MENU_VASAK_L5_ITEM_SEL -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM_SEL -->
										<!-- END SUB: MENU_VASAK_L4_ITEM -->
										<!-- SUB: MENU_VASAK_L4_ITEM_SEL -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- SUB: MENU_VASAK_L5_ITEM -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM -->
											<!-- SUB: MENU_VASAK_L5_ITEM_SEL -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM_SEL -->
										<!-- END SUB: MENU_VASAK_L4_ITEM_SEL -->
									<!-- END SUB: MENU_VASAK_L3_ITEM -->
									<!-- SUB: MENU_VASAK_L3_ITEM_SEL -->
									<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
										<!-- SUB: MENU_VASAK_L4_ITEM -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- SUB: MENU_VASAK_L5_ITEM -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM -->
											<!-- SUB: MENU_VASAK_L5_ITEM_SEL -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM_SEL -->
										<!-- END SUB: MENU_VASAK_L4_ITEM -->
										<!-- SUB: MENU_VASAK_L4_ITEM_SEL -->
										<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- SUB: MENU_VASAK_L5_ITEM -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM -->
											<!-- SUB: MENU_VASAK_L5_ITEM_SEL -->
											<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
											<!-- END SUB: MENU_VASAK_L5_ITEM_SEL -->
										<!-- END SUB: MENU_VASAK_L4_ITEM_SEL -->
									<!-- END SUB: MENU_VASAK_L3_ITEM_SEL -->
							<!-- END SUB: HAS_SUBITEMS_VASAK_L2_SEL -->
							<!-- END SUB: MENU_VASAK_L2_ITEM -->
						<?php } ?>
						<!-- END SUB: MENU_VASAK_L1_ITEM -->
					</ul>
					<a class="dropdown-toggle" id="portals" role="button" data-toggle="dropdown" href="#"><span data-bind="text: portalName"><?php echo aw_session::get("aw_portal_name"); ?></span> <b class="caret"></b></a>
				</li>
				<!-- ko foreach: children -->
					<!-- ko ifnot: meta.portal -->
						<!-- ko if: children().length > 0 -->
							<li class="dropdown" data-bind="css: { active: active }">
								<ul class="dropdown-menu" role="menu" data-bind="attr: { 'aria-labelledby': 'drop' + id }, foreach: children">
									<li><a data-bind="text: name, attr: { href: url }"></a></li>
								</ul>
								<a class="dropdown-toggle" role="button" data-toggle="dropdown" data-bind="attr: { id: 'drop' + id }" href="#">
									<span data-bind="text: name"></span> <b class="caret"></b>
								</a>
							</li>
						<!-- /ko -->
						<!-- ko if: children().length == 0 -->
							<li data-bind="css: { active: active }"><a data-bind="text: name, attr: { href: url }"></a></li>
						<!-- /ko -->
					<!-- /ko -->
				<!-- /ko -->
				<!-- SUB: MENU_PAREM_L1_ITEM_SEL -->
				<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true, array("sidenav" => true)); ?>
				<!-- SUB: HAS_SUBITEMS_PAREM_L1 -->
						<!-- SUB: MENU_PAREM_L2_ITEM -->
						<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
						<!-- END SUB: MENU_PAREM_L2_ITEM -->
						<!-- SUB: MENU_PAREM_L2_ITEM_SEL -->
						<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
						<!-- END SUB: MENU_PAREM_L2_ITEM_SEL -->
				<!-- END SUB: HAS_SUBITEMS_PAREM_L1 -->
				<!-- END SUB: MENU_PAREM_L1_ITEM_SEL -->
				<!-- SUB: MENU_PAREM_L1_ITEM -->
				<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
				<?php $has_subitems = false; ?>
				<!-- SUB: HAS_SUBITEMS_PAREM_L1 -->
				<?php $has_subitems = true; ?>
						<!-- SUB: MENU_PAREM_L2_ITEM -->
						<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}); ?>
						<!-- END SUB: MENU_PAREM_L2_ITEM -->
						<!-- SUB: MENU_PAREM_L2_ITEM_SEL -->
						<?php main_tpl_submenu_hack::add_knockout({VAR:parent}, {VAR:id}, "{VAR:text}", "{VAR:link}", "{VAR:comment}", "{VAR:link_prop}", "{VAR:alias}", {VAR:target_prop}, {VAR:users_only}, {VAR:no_menus}, {VAR:ord}, true); ?>
						<!-- END SUB: MENU_PAREM_L2_ITEM_SEL -->
				<!-- END SUB: HAS_SUBITEMS_PAREM_L1 -->
				<!-- END SUB: MENU_PAREM_L1_ITEM -->
			</ul>
		</div>
	</div>
	<div class="header">
		<div class="row">
			<div class="span12">
				<a href="{VAR:baseurl}"><img src="{VAR:baseurl}img/Small_coat_of_arms_of_Estonia.png" class="pull-left" style="margin-right: 10px;"></a>
				<h1><a href="{VAR:baseurl}" class="undercover">Notarite Koda</a></h1>
				<p class="lead"><a href="{VAR:baseurl}<?php echo aw_session::get("aw_portal_id"); ?>" class="undercover" data-bind="text: portalName"><?php echo aw_session::get("aw_portal_name"); ?></a> <?php echo aw_session::get("aw_portal_comment"); ?></p>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="navbar">
			<div class="navbar-inner">
				<ul class="nav" data-bind="foreach: innernav">
					<li data-bind="css: { active: active, dropdown: children().length > 0 }">
						<!-- ko if: children().length > 0 -->
							<ul class="dropdown-menu max-height-360" role="menu" data-bind="attr: { 'aria-labelledby': 'topdrop' + id }, foreach: children">
								<li><a data-bind="text: name, attr: { href: url }"></a></li>
							</ul>
							<a class="dropdown-toggle" data-bind="attr: { id: 'topdrop' + id }" role="button" data-toggle="dropdown" href="#">
								<span data-bind="text: name"></span> <b class="caret"></b>
							</a>
						<!-- /ko -->
						<!-- ko if: children().length == 0 -->
							<!-- TODO: {VAR:target_prop} -->
							<a data-bind="attr: { href: url }, css: { active: active }" >
								<!-- ko if: id == 34725 -->
								<i class="icon-home"></i>
								<!-- /ko -->
								<!-- ko if: id != 34725 -->
								<span data-bind="text: name"></span>
								<!-- /ko -->
							</a>
						<!-- /ko -->
					</li>
					<?php
						$menu_method = "menu" . aw_session::get("aw_portal_id");
						if (is_callable($menu_method)) {
							$menu_method();
						}
					?>
					<li class="user-button" style="float: right; display: none;">
						<a href="{VAR:baseurl}34729"><i class="icon-user"></i> {VAR:user}</a>
					</li>
				</ul>
			</div>
		</div>
		<ul class="breadcrumb">
			<li><a href="{VAR:baseurl}<?php echo aw_session::get("aw_portal_id"); ?>" data-bind="text: portalName"><?php echo aw_session::get("aw_portal_name"); ?></a> <span class="divider">/</span></li>
			<!-- SUB: YAH_LINK -->
				<?php if ("{VAR:text}" != aw_session::get("aw_portal_name")) { ?>
				<li><a href="{VAR:link}">{VAR:text}</a> <span class="divider">/</span></li>
				<?php } ?>
			<!-- END SUB: YAH_LINK -->
			<!-- SUB: YAH_LINK_END -->
				<?php if ("{VAR:text}" != aw_session::get("aw_portal_name")) { ?>
				<li>{VAR:text}</li>
				<?php } ?>
			<!-- END SUB: YAH_LINK_END -->
		</ul>
	</div>
</div>

<?php function start_spann4(&$span, $extra = "") {
	if ($span === "spann12") {
		$span = "spann8"; ?>
		<div class="spann4" style="margin-left: 0;" <?php echo $extra; ?>>
	<?php }
}
function end_spann4($span) {
	if ($span !== "spann12") { ?>
		</div>
	<?php }
} ?>

<div class="container">
	<?php $span = "spann12"; ?>
	<!-- SUB: LEFTUPPER_PROMO -->
		<?php start_spann4($span); ?>
		<div class="popover popover-promo spann4" style="margin-bottom: 20px;">
			<h4 class="popover-title">{VAR:caption}</h3>
			<div class="popover-content">
				{VAR:content}
				<!-- SUB: HAS_LINK_CAPTION -->
				<small class="pull-right">| <a href="{VAR:url}">{VAR:link_caption}</a></small>
				<!-- END SUB: HAS_LINK_CAPTION -->
				<div class="clearfix"></div>
			</div>
		</div>
	<!-- END SUB: LEFTUPPER_PROMO -->
	<?php start_spann4($span); ?>
	<?php if (ifset($_GET, "class") !== "object_treeview_v2") { ?>
		<script id="notar-sidenav-submenu" type="text/html">
			<li class="smaller darker" data-bind="css: { active: active }">
				<a data-bind="attr: { href: url, title: name }">
					<i class="icon-chevron-right"></i> <span data-bind="text: name"></span>
				</a>
			</li>
			<!-- ko template: { name: 'notar-sidenav-submenu', foreach: active ? children : [] } -->
			<!-- /ko -->
		</script>
        <ul class="nav nav-list notar-sidenav" style="margin-bottom: 20px;" data-bind="visible: sidenav().length > 0, foreach: sidenav">
			<li data-bind="css: { active: active }">
				<a data-bind="attr: { href: url, title: name }">
					<i class="icon-chevron-right"></i> <span data-bind="text: name"></span>
				</a>
			</li>
			<!-- ko template: { name: 'notar-sidenav-submenu', foreach: active ? children : [] } -->
			<!-- /ko -->
		</ul>
	<?php } else { ?>
		<div id="sidenav-treeview" data-bind="visible: sidenav().length > 0, treeview: sidenavForTreeview, treeviewOptions: { draggable: true }" style="margin-bottom: 20px;"></div>
	<?php }?>
	<!-- SUB: LEFTBOTTOM_PROMO -->
		<?php start_spann4($span); ?>
		<div class="popover popover-promo spann4" style="margin-left: 0;">
			<h4 class="popover-title">{VAR:caption}</h3>
			<div class="popover-content">
				{VAR:content}
				<!-- SUB: HAS_LINK_CAPTION -->
				<small class="pull-right">| <a href="{VAR:url}">{VAR:link_caption}</a></small>
				<!-- END SUB: HAS_LINK_CAPTION -->
				<div class="clearfix"></div>
			</div>
		</div>
	<!-- END SUB: LEFTBOTTOM_PROMO -->
	<?php end_spann4($span); ?>
	<!-- SUB: NEWS_PROMO -->
	<div class="popover popover-promo <?php echo $span; ?>" >
		<div class="popover-title">
			<!-- SUB: HAS_LINK_CAPTION -->
			<small class="pull-right">| <a href="{VAR:url}">{VAR:link_caption}</a></small>
			<!-- END SUB: HAS_LINK_CAPTION -->
			<h3 style="margin: 0;">{VAR:caption}</h3>
		</div>
		<div class="popover-content">
			{VAR:content}
		</div>
	</div>
	<!-- END SUB: NEWS_PROMO -->
	<div class="<?php echo $span; ?>">
		{VAR:doc_content}
	</div>
</div>

<footer class="footer">
	<div class="container">
		<p>&copy; Notarite koda 1999 - 2013. Kõik õigused kaitstud.</p>
	</div>
</footer>

<script type="text/javascript">
	var aw_navigation_folders = <?php echo json_encode(main_tpl_submenu_hack::get_knockout()); ?>;
	var aw_navigation_folders_new = {VAR:navigation.json};
	var aw_portal_id = <?php echo aw_session::get("aw_portal_id"); ?>;
</script>
<?php } else { ?>
    <style type="text/css">
      /* Override some defaults */
      html, body {
        background-color: #eee;
      }
      body {
        padding-top: 40px; 
      }
      .container {
        width: 300px;
      }

      /* The white background content wrapper */
      .container > .content {
        background-color: #fff;
        padding: 20px;
        margin: 0 -20px; 
        -webkit-border-radius: 10px 10px 10px 10px;
           -moz-border-radius: 10px 10px 10px 10px;
                border-radius: 10px 10px 10px 10px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
      }

	  .login-form {
		margin-left: 65px;
	  }
	
	  legend {
		margin-right: -50px;
		font-weight: bold;
	  	color: #404040;
	  }

    </style>

  <div class="container">
    <div class="content">
      <div class="row">
        <div class="login-form">
          <h2>Notarite Koda</h2>
          <form action="reforb.aw" method="POST">
            <fieldset>
              <div class="clearfix">
                <input type="text" id="uid" name="uid" placeholder="Kasutajanimi">
              </div>
              <div class="clearfix">
                <input type="password" id="password" name="password" placeholder="Parool">
              </div>
				<input type="hidden" name="class" value="users" />
				<input type="hidden" name="action" value="login" />
				<input type="hidden" name="return_url" value="{VAR:baseurl}" />
              <button type="submit" class="btn primary">Logi sisse</button>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php } ?>