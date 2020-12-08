<?php

if ( !defined('ABSPATH') ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}


if ( ! class_exists( 'Alquemie_Config_Settings_Page' ) ) :
/* Best Practices Settings Page */
class Alquemie_Config_Settings_Page {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_settings' ) );
		add_action( 'admin_init', array( $this, 'setup_sections' ) );
		add_action( 'admin_init', array( $this, 'setup_fields' ) );
	}
	public function create_settings() {
		$page_title = 'Best Practices Configuration';
		$menu_title = 'Best Practices';
		$capability = 'manage_options';
		$slug = 'bestpractices';
		$callback = array($this, 'settings_content');
		add_options_page($page_title, $menu_title, $capability, $slug, $callback);
	}
	public function settings_content() { ?>
		<div class="wrap">
			<h1>Best Practices Configuration</h1>
			<?php settings_errors(); ?>
			<form method="POST" action="options.php">
				<?php
					settings_fields( 'bestpractices' );
					do_settings_sections( 'bestpractices' );
					submit_button();
				?>
			</form>
		</div> <?php
	}
	public function setup_sections() {
		add_settings_section( 'bestpractices_section', 'Configure WordPress based on the "best practices" as determined by Chris Carrel.  ', array(), 'bestpractices' );
	}
	public function setup_fields() {
		$fields = array(
			array(
				'label' => 'Remove Author URLs',
				'id' => 'alquemie-config-author-url',
				'type' => 'checkbox',
				'section' => 'bestpractices_section',
				'options' => array(
					'Disable' => 'Disable',
				),
				'desc' => 'Checking this box will ENABLE the author URLs for your site',
			),
			array(
				'label' => 'Delay RSS Posts',
				'id' => 'alquemie-config-delay-rss',
				'type' => 'checkbox',
				'section' => 'bestpractices_section',
				'options' => array(
					'Disable' => 'Disable',
				),
				'desc' => 'Add posts to RSS feed immediately',
			),
			array(
				'label' => 'Remove Generator',
				'id' => 'alquemie-config-remove-generator',
				'type' => 'checkbox',
				'section' => 'bestpractices_section',
				'options' => array(
					'Disable' => 'Disable',
				),
				'desc' => 'Add WP Generator back to site <head>',
			),
			array(
				'label' => 'Responsive Embeds',
				'id' => 'alquemie-config-responsive-embeds',
				'type' => 'checkbox',
				'section' => 'bestpractices_section',
				'options' => array(
					'Disable' => 'Disable',
				),
				'desc' => 'Add responsive embeds option to theme',
            ),
            array(
				'label' => 'Welcome Message',
				'id' => 'alquemie-config-welcome-msg',
				'type' => 'text',
				'section' => 'bestpractices_section',
				'placeholder' => 'Logged in as',
			),
			array(
				'label' => 'Admin Footer Message',
				'id' => 'alquemie-config-footer-msg',
				'type' => 'text',
				'section' => 'bestpractices_section',
				'placeholder' => 'Authorized Users Only!',
			),
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'field_callback' ), 'bestpractices', $field['section'], $field );
			register_setting( 'bestpractices', $field['id'] );
		}
	}
	public function field_callback( $field ) {
		$value = get_option( $field['id'] );
		switch ( $field['type'] ) {
				case 'radio':
				case 'checkbox':
					if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
						$options_markup = '';
						$iterator = 0;
						foreach( $field['options'] as $key => $label ) {
							$iterator++;
							$options_markup.= sprintf('<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
							$field['id'],
							$field['type'],
							$key,
							checked($value[array_search($key, $value, true)], $key, false),
							$label,
							$iterator
							);
							}
							printf( '<fieldset>%s</fieldset>',
							$options_markup
							);
					}
					break;
			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$field['placeholder'],
					$value
				);
		}
		if( $desc = $field['desc'] ) {
			printf( '<p class="description">%s </p>', $desc );
		}
	}
}
new Alquemie_Config_Settings_Page();

endif;
