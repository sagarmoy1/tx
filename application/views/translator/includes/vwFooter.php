<!-- basic scripts -->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='<?php echo HTTP_JS_PATH; ?>jquery.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
        <script type="text/javascript">
         window.jQuery || document.write("<script src='<?php echo HTTP_JS_PATH; ?>jquery1x.js'>"+"<"+"/script>");
        </script>
        <![endif]-->
        
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='<?php echo HTTP_JS_PATH; ?>jquery.mobile.custom.js'>"+"<"+"/script>");
		</script>
		<script src="<?php echo HTTP_JS_PATH; ?>bootstrap.js"></script>
		
        <!-- page specific plugin scripts -->
		<!--<script src="<?php echo HTTP_JS_PATH; ?>dataTables/jquery.dataTables.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>dataTables/jquery.dataTables.bootstrap.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>dataTables/extensions/TableTools/js/dataTables.tableTools.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>dataTables/extensions/ColVis/js/dataTables.colVis.js"></script>-->

		<!--[if lte IE 8]>
		  <script src="<?php echo HTTP_JS_PATH; ?>excanvas.js"></script>
		<![endif]-->
		<script src="<?php echo HTTP_JS_PATH; ?>jquery-ui.custom.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>jquery.ui.touch-punch.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>jquery.easypiechart.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>jquery.sparkline.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>flot/jquery.flot.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>flot/jquery.flot.pie.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>flot/jquery.flot.resize.js"></script>

		<!-- ace scripts -->
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.scroller.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.colorpicker.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.fileinput.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.typeahead.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.wysiwyg.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.spinner.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.treeview.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.wizard.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.aside.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.ajax-content.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.touch-drag.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.sidebar.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.sidebar-scroll-1.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.submenu-hover.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.widget-box.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.settings.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.settings-rtl.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.settings-skin.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.widget-on-reload.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.searchbox-autocomplete.js"></script>



		<!-- the following scripts are used in demo only for onpage help and you don't need them -->
		<link rel="stylesheet" href="<?php echo HTTP_CSS_PATH; ?>ace.onpage-help.css" />
		<link rel="stylesheet" href="<?php echo HTTP_DOCS_PATH_ADMIN; ?>assets/js/themes/sunburst.css" />

		<script type="text/javascript"> ace.vars['base'] = '..'; </script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/elements.onpage-help.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>ace/ace.onpage-help.js"></script>
		<script src="<?php echo HTTP_JS_PATH; ?>js/rainbow.js"></script>
		<script src="<?php echo HTTP_DOCS_PATH_ADMIN; ?>assets/js/language/generic.js"></script>
		<script src="<?php echo HTTP_DOCS_PATH_ADMIN; ?>assets/js/language/html.js"></script>
		<script src="<?php echo HTTP_DOCS_PATH_ADMIN; ?>assets/js/language/css.js"></script>
		<script src="<?php echo HTTP_DOCS_PATH_ADMIN; ?>assets/js/language/javascript.js"></script>
        
        
        <div class="footer">
				<div class="footer-inner">
					<!-- #section:basics/footer -->
					<div class="footer-content">
						<span class="bigger-120">
							<span class="blue bolder"><?php echo SITE_NAME; ?></span>
							Application &copy; 2015-2016
						</span>

						&nbsp; &nbsp;
						<span class="action-buttons">
							<a href="#">
								<i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
							</a>

							<a href="#">
								<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
							</a>

							<a href="#">
								<i class="ace-icon fa fa-rss-square orange bigger-150"></i>
							</a>
						</span>
					</div>

					<!-- /section:basics/footer -->
				</div>
			</div>

            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
            </a>

  </body>
</html>