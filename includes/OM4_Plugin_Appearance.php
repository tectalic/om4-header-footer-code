<?php
/*
	Copyright 2012-2024 OM4 (email: info@om4.com.au    web: http://om4.com.au/)

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

/**
 * Base class that can be extended when a plugin needs to add a new Dashboard page to the Appearance menu.
 * Used by the Custom CSS and Custom Header/Footer Code screens.
 */
abstract class OM4_Plugin_Appearance {

	/**
	 * URL to the Custom CSS screen in the WP dashboard
	 *
	 * @var string
	 */
	protected $dashboard_url = '';

	/**
	 * WP capability required in order to access the screen
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * Screen title
	 *
	 * @var string
	 */
	protected $screen_title;

	/**
	 * Screen name
	 *
	 * @var string
	 */
	protected $screen_name;

	/**
	 * Default settings for wp_editor()
	 *
	 * @var array{media_buttons: bool, textarea_rows: int, wpautop: bool, quicktags: bool, tinymce: bool}
	 * */
	protected $wp_editor_defaults = array(
		'media_buttons' => false,
		'textarea_rows' => 10,
		'wpautop'       => false,
		// No Visual Editor.
		'quicktags'     => false,
		// No Visual Editor.
		'tinymce'       => false,

	);

	public function __construct() {

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_meu' ) );
		}

		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 100 );
	}

	/**
	 * URL to the Custom CSS screen in the WP dashboard
	 */
	public function dashboard_url(): string {
		if ( empty( $this->dashboard_url ) ) {
			$this->dashboard_url = admin_url( 'themes.php?page=' . $this->screen_name );
		}
		return $this->dashboard_url;
	}

	/**
	 * The URL used when the saving process succeeds
	 *
	 * @return string
	 */
	protected function dashboard_url_saved() {
		return add_query_arg( 'updated', 'true', $this->dashboard_url() );
	}

	/**
	 * The URL used when the saving process fails
	 *
	 * @return string
	 */
	protected function dashboard_url_saved_error() {
		return add_query_arg( 'updated', 'false', $this->dashboard_url() );
	}

	protected function form_action(): string {
		return admin_url( 'admin-post.php' );
	}

	public function can_access_dashboard_screen(): bool {
		return current_user_can( $this->capability );
	}

	public function admin_bar_menu(): void {

		if ( ! $this->can_access_dashboard_screen() ) {
			return;
		}

		global $wp_admin_bar;
		$args = array(
			'title'  => $this->screen_title,
			'id'     => $this->screen_name,
			'parent' => 'appearance',
			'href'   => $this->dashboard_url(),
		);
		$wp_admin_bar->add_menu( $args );
	}


	public function admin_meu(): void {
		add_theme_page( $this->screen_title, $this->screen_title, $this->capability, $this->screen_name, array( $this, 'dashboard_screen' ) );
	}

	abstract public function dashboard_screen(): void;

	public function add_load_dashboard_page_hook( callable $method ): void {
		add_action( 'load-appearance_page_' . $this->screen_name, $method );
	}
}
