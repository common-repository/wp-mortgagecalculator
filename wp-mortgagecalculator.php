<?php
/*
Plugin Name: WP MortgageCalculator
Plugin URI:  http://www.wpmortgagecalculator.com
Description:  WP Mortgage Calculator is a professional-grade, highly-customizable mortgage loan calculator for real estate sites, financial sites, and realtors who want to give their users the ability to calculate mortgage payments, interest, and other factors associated with buying a home or property.

Version: 1.0
Author:  browndoginteractive
Author URI:  http://www.browndoginteractive.com
License: GPL
*/

class MortgageCalc extends WP_Widget {
	/** constructor */
	function MortgageCalc() {
		parent::WP_Widget( 'MortgageCalc', 'WP Mortgage Calculator', array( 'description' => 'Professional-grade, highly-customizable mortgage loan calculator.' ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		?>
		<style type="text/css">
			div.mc-footer a{color: <?php echo get_option('mc_footer_color')?>; text-decoration: none;}
			#calculator_widget .mc-body ul.mc-tabs {list-style-type: none; list-style-image: none;}
		</style>
		<div style="margin: 2px; padding: 0;"></div>
		<script type="text/javascript" src="<?php echo $x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__)); ?>mlcalc.js"> </script>
		<script type="text/javascript"> 
		jQuery(function() { jQuery("#calculator_widget").MortgageCalculator({ mode: "widget", animate: "<?php echo $instance['animate']?>", title: "<?php echo $instance['title']?>", footer: '', intro: "", <?php echo get_option('calculator_code')?>});}); 
	</script>
	<div id="calculator_widget" style="margin: 0 auto;"></div>
		<?php echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['animate'] = isset( $new_instance[ 'animate' ] );
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$animate = esc_attr( $instance[ 'animate' ] );
		}
		else {
			$title = __( 'New title', 'text_domain' );
			$animate = '1';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('animate'); ?>"><?php _e('Animate'); ?></label> 
			<input id="<?php echo $this->get_field_id('animate'); ?>" type="checkbox" name="<?php echo $this->get_field_name('animate'); ?>" <?php checked(isset($animate) ? $animate : 0); ?>/>
		</p>

		<?php
	}

} // class MCalc_Widget
// register MCalc_Widget
add_action( 'widgets_init', 'mcalc_widget_init' );
add_action( 'wp_head', 'add_script');
function add_script(){
		echo '<script language="javascript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"> </script>';
}
function mcalc_widget_init() {
	register_widget("MortgageCalc");
	add_option('calculator_code', "colors: { main: { text: '#000', back: '#eef2fd', border: '#39c' },title: { text: '#39c', back: null },	tab: { text: '#39c', back: '#fff', border: '#39c' }, pane: { text: '#000', back: '#fff', border: '#39c' }, input: { text: '#000', back: '#fff', border: '#aaa', error: '#fcc' }, button: { text: '#fff', back: '#46a026', border: '#1f731a' }, nav: { text: '#fff', back: '#39c', border: '#39c' },	table: { text: '#000', back: '#fff', border: '#aaa' }, footer: { text: '#999', back: null },link: { text: '#39c' }, values: { payment: '#6a9f35', taxes: '#0584af', insurance: '#ff9300', total: '#eee', principal: '#6a9f35', extra: '#90d948', interest: '#f00', balance: '#333' }},
				fields: { principal: { enabled: true, type: 'input', label: 'Principal', desc: 'The total value of the loan (in dollars)' },
					interest: { enabled: true, type: 'input', label: 'Interest Rate|Interest', desc: 'The annual interest rate of the loan', suffix: '%' },
					term: { enabled: true, type: 'select', label: 'Term', desc: 'The term of the loan (in years)', suffix: 'yrs' },
					down: { enabled: false, type: 'input', label: 'Down Payment|Down', desc: 'The down payment on the loan (in dollars)' },
					taxes: { enabled: 'null', type: 'input', label: 'Property Taxes|Taxes', desc: 'The annual property taxes (in dollars)', suffix: '/yr' },
					insurance: { enabled: 'null', type: 'input', label: 'Insurance', desc: 'The annual insurance (in dollars)', suffix: '/yr' },
					pmi: { enabled: false, type: 'input', label: 'PMI', desc: 'The monthly private mortgage insurance (in dollars)', suffix: '/mo' },
					extra: { enabled: false, type: 'input', label: 'Extra Payment|Extra', desc: 'Extra to pay to the principal per month (in dollars)', suffix: '/mo' }},
				defaults: { principal: null, interest: null, term: null, down: null, taxes: 1.5, insurance: 0.5, pmi: null, extra:null}, bounds: {
					min: { principal: 0, interest: 0, term: 15, down: null, taxes: null, insurance: null, pmi: null, extra:null},
					max: { principal: 15000000, interest: 15, term: 40, down: null, taxes: null, insurance: null, pmi: null, extra:null},
					step: { principal: null, interest: null, term: 5, down: null, taxes: null, insurance: null, pmi: null, extra:null}}"
	);
}
/**
 * MLCalc Admin Menu
 */
add_action('admin_menu', 'mcalc_create_menu');

function mcalc_create_menu() {
	add_menu_page('Mortgage Loan Calculator Settings', 'Mortgage Calc', 'administrator', __FILE__, 'mcalc_settings_page');
	add_action( 'admin_init', 'register_settings' );
}

function register_settings() {
	register_setting( 'mcalc-settings-group', 'calculator_code' );
	register_setting( 'mcalc-settings-group', 'mc_footer_color' );
}

function mcalc_settings_page() {
	?>
	
	<script type="text/javascript">
		function clean_it(t)
		{		
			return t.replace(/'/gi, "&#39;");;
		}

      jQuery(function() 
      {    		
		var w = 400, w2 = 720, w3 = 230;        
		var n = null, defaults =	{	<?php echo get_option('calculator_code')?>		};
		
				jQuery('#general-mode').val(defaults.mode);
				jQuery('#general-animate').attr('checked', defaults.animate);
				jQuery('#general-title').val(defaults.title || '');
				jQuery('#general-footer').val(defaults.footer || '');
				jQuery('#general-intro').val(defaults.intro || '');
				
				var f = jQuery('#fields'), i = 0;
				jQuery.each(defaults.fields, function(k, v)
				{
				  var r = jQuery('<tr/>').appendTo(f);
					jQuery('<td/>').text(k == 'pmi' ? 'PMI': k.substr(0, 1).toUpperCase() + k.substr(1)).appendTo(r);
				  	if (v.enabled == "null")jQuery('<td/>').append(i < 3 ? '&nbsp;': jQuery('<input type="checkbox"/>').attr('id', 'field-' + k + '-enabled').attr('checked', true)).css('text-align', 'center').appendTo(r);
				  	else jQuery('<td/>').append(i < 3 ? '&nbsp;': jQuery('<input type="checkbox"/>').attr('id', 'field-' + k + '-enabled')).css('text-align', 'center').appendTo(r);
				  	jQuery('<td/>').append(jQuery('<select/>').attr('id', 'field-' + k + '-type').append(jQuery('<option/>').text('Textbox').val('input')).append(jQuery('<option/>').text('Dropdown').val('select')).width(92).change(function(e) { var s = jQuery(e.target).val() != 'select'; jQuery('#default-' + k + '-step').attr('disabled', s); }).val(v.type)).appendTo(r);
				  	jQuery('<td/>').append(jQuery('<input type="text"/>').attr('id', 'field-' + k + '-desc').width(300).val(v.desc)).appendTo(r);
				  	i++;
				});
				
				var d = jQuery('#defaults');
				jQuery.each(defaults.defaults, function(k, v)
				{
				  var r = jQuery('<tr/>').appendTo(d), min = defaults.bounds.min, max = defaults.bounds.max, step = defaults.bounds.step, s = jQuery('#field-' + k + '-type').val() != 'select'
					
					
					jQuery('<td/>').text(k == 'pmi' ? 'PMI': k.substr(0, 1).toUpperCase() + k.substr(1)).appendTo(r);
				  jQuery('<td/>').append(jQuery('<input type="text"/>').attr('id', 'default-' + k + '-value').width(100).val(v == null ? '': v)).appendTo(r);
				  jQuery('<td/>').append(jQuery('<input type="text"/>').attr('id', 'default-' + k + '-min').width(100).val(min[k] == null ? '': min[k])).appendTo(r);
				  jQuery('<td/>').append(jQuery('<input type="text"/>').attr('id', 'default-' + k + '-max').width(100).val(max[k] == null ? '': max[k])).appendTo(r);
				  jQuery('<td/>').append(jQuery('<input type="text"/>').attr('id', 'default-' + k + '-step').width(100).attr('disabled', s).val(step[k] == null ? '': step[k])).appendTo(r);
				});
				
				var c = jQuery('#colors');
				jQuery.each(defaults.colors, function(k, v)
				{
				  var r = jQuery('<tr/>').appendTo(c);
					jQuery('<td/>').html(k.substr(0, 1).toUpperCase() + k.substr(1)).appendTo(r);
					r = jQuery('<td/>').appendTo(r);
					
					var t = jQuery('<table/>').attr({ cellspacing: 0, cellpadding: 0 }).appendTo(r);
				  r = jQuery('<tr/>').appendTo(t);
					jQuery.each(v, function(k2, v2)
					{
						jQuery('<th/>').css({ 'text-align': 'center', 'font-size': '8pt' }).width(50).text(k2.substr(0, 1).toUpperCase() + k2.substr(1)).appendTo(r);
					});
					
					r = jQuery('<tr/>').appendTo(t);
					jQuery.each(v, function(k2, v2)
					{
					  var cp = jQuery('<div/>').addClass('color').attr('id', 'color-' + k + '-' + k2).data('color', v2 || '').css({ background: v2 || '', margin: '0 auto' });
						jQuery('<td/>').append(cp).appendTo(r);
						
						cp.ColorPicker(
						{
							color: v2 || '',
							onShow: function(p) { jQuery(p).fadeIn(250); return false; },
							onHide: function(p) { jQuery(p).fadeOut(250); return false; },
							onChange: function(hsb, hex, rgb) { cp.data('color', '#' + hex).css('background', '#' + hex); }
						});
				
					});
				});
				
				jQuery('input[name=generate]').click(function()
				{
				  var s = {};
					s.mode = jQuery('#general-mode').val();
					s.title = jQuery('#general-title').val();
					s.animate = jQuery('#general-animate').attr('checked');
					s.footer = jQuery('#general-footer').val();
					s.intro = jQuery('#general-intro').val();
					s.logo= {path: jQuery('#general-logopath').val(), url: jQuery('#general-logourl').val()};
					
					var f = s.fields = {};
					jQuery.each(defaults.fields, function(k, v)
					{
					  var f2 = f[k] = {};
						f2.enabled = jQuery('#field-' + k + '-enabled').attr('checked');
						f2.type = jQuery('#field-' + k + '-type').val();
						f2.desc = jQuery('#field-' + k + '-desc').val();
					});
					
					var d = s.defaults = {}, b = s.bounds = {}, min = b.min = {}, max = b.max = {}, step = b.step = {};
					jQuery.each(defaults.defaults, function(k, v)
					{
					  d[k] = jQuery('#default-' + k + '-value').val();
					  max[k] = jQuery('#default-' + k + '-max').val();
					  step[k] = jQuery('#default-' + k + '-step').val();		
					});
					
					var c = s.colors = {};
					jQuery.each(defaults.colors, function(k, v)
					{
					  var c2 = c[k] = {};
						jQuery.each(v, function(k2, v2) { c2[k2] = jQuery('#color-' + k + '-' + k2).data('color'); });
					});
					
					/********************/
					var code = '';
					/*code += ' mode: "' + s.mode + '",';
					code += ' animate: "' + s.animate + '",';
					code += ' title: "' + clean_it(s.title) + '",';
					code += ' footer: "' + clean_it(s.footer) + '",';
					code += ' intro: "' + clean_it(s.intro) + '",';
					code += ' logo: {path: "' + s.logo.path + '", url: "' + s.logo.url + '"},';*/
					code += ' fields: { ';
					var fieldsText = new Array('principal', 'interest', 'term', 'down', 'taxes', 'insurance', 'pmi', 'extra');
					var fieldsCount = 0;
					var fieldsFirst = true;
					jQuery.each(s.fields, function(k, v)
					{
						if(fieldsCount > 0) code += ', ';
						checked = jQuery('#field-' + k + '-enabled').attr('checked');
						//alert( checked);
						if (checked != 'checked' && checked != true){
							code += fieldsText[fieldsCount] + ': { enabled: false';
						}else code += fieldsText[fieldsCount] + ': { enabled: "null"';
						code += ', type: "' + jQuery('#field-' + k + '-type').val();
						code += '", desc: "' + clean_it(jQuery('#field-' + k + '-desc').val());
						code += '"} ';	
						fieldsCount++;
					});
					code += ' }, defaults: { ';	
					fieldsCount = 0;
					jQuery.each(defaults.defaults, function(k, v)
					{
						if(jQuery('#default-' + k + '-value').val() != '')
						{
							if(!fieldsFirst) code += ', ';
							code += fieldsText[fieldsCount] + ": " + jQuery('#default-' + k + '-value').val();
							fieldsFirst = false;					
						}else{
							if(!fieldsFirst) code += ', ';
							code += fieldsText[fieldsCount] + ": " + 'null';
							fieldsFirst = false;					
						}
						fieldsCount++;
					});
					code += " }, bounds: { min: { ";
					fieldsCount = 0;
					fieldsFirst = true;
					jQuery.each(defaults.defaults, function(k, v)
					{
						if(jQuery('#default-' + k + '-min').val() != '')
						{
							if(!fieldsFirst) code += ', ';
							code += fieldsText[fieldsCount] + ": " + jQuery('#default-' + k + '-min').val();	
							fieldsFirst = false;
						}
						fieldsCount++;
					});
					code += " }, max: { ";
					fieldsCount = 0;
					fieldsFirst = true;
					jQuery.each(defaults.defaults, function(k, v)
					{
						if(jQuery('#default-' + k + '-max').val() != '')
						{
							if(!fieldsFirst) code += ', ';
							code += fieldsText[fieldsCount] + ": " + jQuery('#default-' + k + '-max').val();	
							fieldsFirst = false;
						}
							fieldsCount++;
					});
					code += " }, step: { ";
					fieldsCount = 0;
					fieldsFirst = true;
					jQuery.each(defaults.defaults, function(k, v)
					{
						if(jQuery('#default-' + k + '-step').val() != '')
						{
							if(!fieldsFirst) { code += ', '; }
							code += fieldsText[fieldsCount] + ": " + jQuery('#default-' + k + '-step').val();	
							fieldsFirst = false;
						}
						fieldsCount++;
					});
					code += "} }, colors:";
					code += "	{";
						code += "main: { text: '#000', back: '#eef2fd', border: '#39c' },";
						code += "title: { text: '#39c', back: null },";
						code += "tab: { text: '#39c', back: '#fff', border: '#39c' },";
						code += "pane: { text: '#000', back: '#fff', border: '#39c' },";
						code += "input: { text: '#000', back: '#fff', border: '#aaa', error: '#fcc' },";
						code += "button: { text: '#fff', back: '#46a026', border: '#1f731a' },";
						code += "nav: { text: '#fff', back: '#39c', border: '#39c' },";
						code += "table: { text: '#000', back: '#fff', border: '#aaa' },";
						code += "footer: { text: '#999', back: null },";
						code += "link: { text: '#39c' },";
						code += "values: { payment: '#6a9f35', taxes: '#0584af', insurance: '#ff9300', total: '#eee', principal: '#6a9f35', extra: '#90d948', interest: '#f00', balance: '#333' }";
						code += "} ";
						//code += '}); });';
						//code += ;
					/********************/
					
					if(s.mode == "widget") { jQuery("#calc_size").text("180"); }
					else if(s.mode == "normal") { jQuery("#calc_size").text("685"); }
					
					jQuery("#calculator_code").val(code);
					jQuery("#mc_footer_color").val(jQuery('#color-footer-text').data('color'));
					//jQuery('#calculator').empty().attr('class', '').MortgageCalculator(s);
					
					//jQuery('#settings').hide();
					//jQuery('#viewer').show();
				});
				
				
        jQuery('.table th').css({ 'vertical-align': 'middle' });
        jQuery('.table td').css({ 'vertical-align': 'middle' });
				
        jQuery('.table tr:last-child > td').css({ 'border-bottom': '1px solid #808080' });
        jQuery('.table tr > td:last-child').css({ 'border-right': '1px solid #808080' });
        jQuery('.table tr:nth-child(2) > td:first-child').css({ '-moz-border-radius-topleft': '4px', '-webkit-border-top-left-radius': '4px' });
        jQuery('.table tr:nth-child(2) > td:last-child').css({ '-moz-border-radius-topright': '4px', '-webkit-border-top-right-radius': '4px' });
        jQuery('.table tr:last-child > td:first-child').css({ '-moz-border-radius-bottomleft': '4px', '-webkit-border-bottom-left-radius': '4px' });
        jQuery('.table tr:last-child > td:last-child').css({ '-moz-border-radius-bottomright': '4px', '-webkit-border-bottom-right-radius': '4px' });
        jQuery('.table .table td').css({ 'background': '#f9f9f9' });
				
				jQuery('.table table:not(.table)').find('td').css({ 'border': '0', 'padding': '0' });
				jQuery('.table table:not(.table)').find('th').css({ 'border': '0', 'padding': '0' });
				
      });
    
</script>
	<script type="text/javascript" src="<?php echo $x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));?>colorpicker.js"></script>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));?>colorpicker.css" />
	<style type="text/css">
		.table { table-layout: fixed; width: 100%; margin: 10px 0 0 0;}
		.table th { padding: 2px 8px; }
		.table td { padding: 6px 8px; background: #fff; border: 1px solid #bdbdbd;  border-bottom: 0; border-right: 0; vertical-align: top; }
		#colors table td { padding: 0; background: #fff; border: 0; vertical-align: top; }
		.color { width: 25px; height: 25px; border: 2px solid #ccc; -moz-border-radius: 4px; -webkit-border-radius: 4x; }
	</style>
	<div class="wrap">
		<form method="post" action="options.php">
		<?php settings_fields('mcalc-settings-group'); ?>
			<div style="text-align: left;" id="settings">
				<div>
					<div style="width: 640px; border: 2px solid rgb(0, 0, 0); float: left; text-align: left; padding: 10px; margin: 10px 0px 5px;" id="howto">
						<span style="color: rgb(1, 125, 187); font-size: 19px; display: block; margin-bottom: 4px; font-weight: bold;">WP Mortgage Calculator Lite</span>
						<p style="float: left; padding: 0px; margin: 0px;">
						<span style="font-weight: bold; margin-bottom: 6px; display: block;">Follow these simple directions for help in creating your custom calculator.</span>
						</p>
						<ol style="margin-left: 40px; clear: left;">
							<li style="margin-bottom: 4px;">Decide which 'Fields' you want to include in your calculator.  Each field corresponds with a function on your calculator and can be defined as a 'Textbox' to allow users to pick any value or a 'Dropdown' to provide generic values.</li>
							<li style="margin-bottom: 4px;">Should you choose to add or change drop downs, you can tailor the increments to fit your audience in the 'Defaults/Bounds' section.</li>
							<li style="margin-bottom: 4px;">Click 'Save Settings' and go to Widgets page. Drag 'Mortgage Loan Widget' to any of your sidebars choose settings you like and it's done!</li>
						</ol>
						<br/>
						<span style="color: rgb(1, 125, 187); font-size: 19px; display: block; margin-bottom: 4px; font-weight: bold;">Or WP Mortgage Calculator Full</span>
						<ol style="margin-left: 40px; clear: left;">
							<li style="margin-bottom: 4px;">You can customize any color of the calculator in the 'Colors' section to fit in your design.</li>
							<li style="margin-bottom: 4px;">You can use "Normal" mode which is more powerful!</li>
							<li style="margin-bottom: 4px;">You can use shortcode [mcalc] to embed Mortgage Calc to your post or page. If you want set Title or Intro use this parametrs: [mcalc title="Your Title" intro="This is your Intro"].</li>
							<li style="margin-bottom: 4px;"></li>
						</ol>
						<a style="font-weight: bold; margin-bottom: 6px; margin-left: 24px; display: block; font-size: 14px;" href="http://www.wpmortgagecalculator.com" title="Get Full Version" target="_blank">I Want The Full Version!</a>
					</div>
				</div>
				<div style="clear: left;">
				
				<h2>Fields:</h2>
					<table style="width: 660px;" cellspacing="0" cellpadding="0" class="table" id="fields">
						<col width="130"><col width="80"><col width="110"><col>
						<tbody>
							<tr><th style="vertical-align: middle;">Name</th><th align="center" style="vertical-align: middle;">Enabled</th><th style="vertical-align: middle;">Type</th><th style="vertical-align: middle;">Description</th></tr>
					</tbody>
				</table>
				
				
				<h2>Defaults/Limits:</h2>
				<p style="width: 660px;">Note that in order to use a drop-down list for a field, you must specify the minimum, maximum, and step values.</p>
				<table style="width: 660px;" cellspacing="0" cellpadding="0" class="table" id="defaults">
					<col width="130"><col><col><col><col>
					<tbody>
						<tr>
							<th style="vertical-align: middle;">Name</th><th style="vertical-align: middle;">Value</th><th style="vertical-align: middle;">Minimum</th><th style="vertical-align: middle;">Maximum</th><th style="vertical-align: middle;">Step</th>
						</tr>
					</tbody>
				</table>

				
				</div>
			</div>
			<input type="hidden" id="calculator_code" name="calculator_code"></input>
			<input type="hidden" id="mc_footer_color" name="mc_footer_color"></input>
			<p class="submit">
				<input type="submit" class="button-primary" name="generate" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php }
?>