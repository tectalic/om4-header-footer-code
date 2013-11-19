<?php
/*
Plugin Name: OM4 Header/Footer Code
Plugin URI: http://om4.com.au/wordpress-plugins/
Description: Use the WordPress dashboard to add custom HTML code to the head section or closing body section. Ensures jQuery is always available in the frontend.
Version: 1.0.1
Author: OM4
Author URI: http://om4.com.au/
Text Domain: om4-header-footer-code
Git URI: https://github.com/OM4/om4-header-footer-code
Git Branch: release
License: GPLv2 or later
*/

/*

   Copyright 2012-2013 OM4 (email: info@om4.com.au    web: http://om4.com.au/)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( ! class_exists( 'OM4_Plugin_Appearance' ) )
	require_once('includes/OM4_Plugin_Appearance.php');


/**
 * Custom Header/Footer Code implementation:
 * - Adds Dashboard -> Appearance -> Header & Footer screen, which is accessible to any WordPress Administrator with the unfiltered_html capability.
 *
 * Should work with OM4 Theme, any WooTheme, and (hopefully) any other WordPress theme.
 */
class OM4_Header_Footer extends OM4_Plugin_Appearance {

	public function __construct() {

		$this->screen_title = 'Header & Footer Code';
		$this->screen_name = 'headerfooter';

		if ( is_admin() ) {
			add_action( 'admin_post_update_header_footer_code', array($this, 'DashboardScreenSave') );
		}

		add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts') );

		add_action( 'wp_head', array($this, 'MaybeDisplayHeaderCode'), $this->GetHeaderCodePriority() );
		add_action( 'wp_footer', array($this, 'MaybeDisplayFooterCode'), 9999999 ); // Big number so it is right before the </body> tag

		parent::__construct();
	}

	/**
	 * Always include jQuery in the frontend
	 */
	function wp_enqueue_scripts() {
		wp_enqueue_script('jquery');
	}

	/**
	 * Determine whether the currently logged in user has access to the custom header/footer script fields.
	 *
	 * This is defined as anyone who has access the manage_options and the unfiltered_html capabilities. In other words:
	 * - for Standalone Sites, any Administrator users
	 * - for Multisite, any Administrator user who (is a super admin or has a KSES Exemption configured via Network Admin -> Settings -> KSES Exemptions )
	 *
	 * @return bool
	 */
	public function CanManageCode() {
		return $this->CanAccessDashboardScreen() && current_user_can('unfiltered_html');
	}

	public function GetHeaderCode() {
		return get_option('om4_custom_header_code', '');
	}

	public function SetHeaderCode( $code ) {
		return update_option('om4_custom_header_code', $code);
	}

	function GetHeaderCodePriority() {
		return intval( get_option('om4_custom_header_code_priority', 10) );
	}

	function SetHeaderCodePriority( $priority ) {
		return update_option('om4_custom_header_code_priority', intval($priority) );
	}

	function MaybeDisplayHeaderCode() {
			$text = $this->GetHeaderCode();
			if ($text) echo "\n$text\n";
	}

	public function GetFooterCode() {
		return get_option('om4_custom_footer_code', '');
	}

	public function SetFooterCode( $code ) {
		return update_option('om4_custom_footer_code', $code);
	}

	function MaybeDisplayFooterCode() {
			$text = $this->GetFooterCode();
			if ($text) echo "\n$text\n";
	}

	public function DashboardScreen(){
		?>
		<div class='wrap'>
			<div id="om4-header">
				<h2>Header and Footer Code/Script</h2>
				<?php
				if ( !$this->CanManageCode() ) {
					echo '<div class="error"><p>You do not have permission to access this feature.</p></div></div></div>';
					return;
				}

				if ( isset($_GET['updated']) ) {
					echo '<div id="message" class="updated"><p>Header/Footer Code saved.</p></div>';
				}

				?>
				<form action="<?php echo $this->FormAction(); ?>" method="post">
				<h4>Custom Header Code/Script</h4>
				<p>This can be used to add any custom HTML inside the <code>&lt;head&gt;</code> section of every page. For example, an external JavaScript file can be referenced here using a <code>&lt;script&gt;</code> tag.</p>
				<?php

				wp_editor( $this->GetHeaderCode(), 'headercode', $this->wp_editor_defaults );
				?>
				<h4>Custom Header Code/Script Priority</h4>
				<p>The priority below lets you specify where in the <code>&lt;head&gt;</code> section the <em>Custom Header Code/Script</em> will be output.<br />
							 A small number will cause the code to be output closer to the opening <code>&lt;head&gt;</code> tag, whereas a large number will cause the code to be output closer to the closing <code>&lt;/head&gt;</code> tag.</p>

				<p>
					<label for="header_code_priority">Custom Header Code/Script Priority:</label>
					<input name="header_code_priority" type="text" id="header_code_priority" value="<?php esc_attr_e( $this->GetHeaderCodePriority() ); ?>" />
				</p>
				<h4>Custom Footer Code/Script</h4>
						<p>This can be used to add any custom HTML just before the <code>&lt;/body&gt;</code> tag of every page. For example, an external JavaScript file can be referenced here using a <code>&lt;script&gt;</code> tag.</p>
				<?php
				wp_editor( $this->GetFooterCode(), 'footercode', $this->wp_editor_defaults );
				?>
				<input type="hidden" name="action" value="update_header_footer_code" />
				<?php
				wp_nonce_field('update_header_footer_code');
				?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Handler that saves the dashboard screen's options/values, then redirects back to the Dashboard Screen
	 */
	public function DashboardScreenSave() {

		$url = $this->DashboardURL();

		if ( $this->CanManageCode() ) {

			check_admin_referer('update_header_footer_code');

			$this->SetHeaderCode( stripslashes($_POST['headercode']) );

			if ( !isset($_POST['header_code_priority']) || !strlen($_POST['header_code_priority']) )
				$_POST['header_code_priority'] = 10;

			$this->SetHeaderCodePriority( $_POST['header_code_priority'] );

			$this->SetFooterCode( stripslashes($_POST['footercode']) );

			$url = $this->DashboardURLSaved();

		}

		wp_redirect( $url );
		exit;
	}

}


global $om4_header_footer;
$om4_header_footer = new OM4_Header_Footer();

/** BEGIN GLOBAL FUNCTIONS - these are used outside of this plugin file **/

function om4_get_custom_header_code() {
	global $om4_header_footer;
	return $om4_header_footer->GetHeaderCode();
}

function om4_get_custom_footer_code() {
	global $om4_header_footer;
	return $om4_header_footer->GetFooterCode();
}

function om4_can_edit_custom_script_code() {
	global $om4_header_footer;
	return $om4_header_footer->CanManageCode();
}

/** END GLOBAL FUNCTIONS **/