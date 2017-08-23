<?php
/**
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */

namespace WPJSFSP\Admin;

use WPJSFSP\Conditions;
use WPJSFSP\Helpers;
use WPJSFSP\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	public static $notices = array();

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_pages' ), 999 );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
		add_action( 'admin_init', array( __CLASS__, 'save' ) );
	}

	/**
	 * Register admin options pages.
	 */
	public static function register_pages() {
		add_options_page( __( 'WPJSFSP', 'store-site-functionality' ), __( 'WPJSFSP Settings', 'wp-js-form-sample-plugin' ), 'manage_options', 'wpjsfsp-settings', array( '\WPJSFSP\Admin\Settings', 'page' ) );
	}

	// display default admin notice
	public static function notices() {

		if ( isset( $_GET['success'] ) && get_option( 'wpjsfsp_settings_admin_notice' ) ) {
			self::$notices[] = array(
				'type'    => $_GET['success'] ? 'success' : 'error',
				'message' => get_option( 'wpjsfsp_settings_admin_notice' ),
			);

			delete_option( 'wpjsfsp_settings_admin_notice' );
		}

		if ( ! empty( self::$notices ) ) {
			foreach ( self::$notices as $notice ) { ?>
				<div class="notice notice-<?php esc_attr_e( $notice['type'] ); ?> is-dismissible">
					<p><strong><?php esc_html_e( $notice['message'] ); ?></strong></p>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'wp-js-form-sample-plugin' ); ?></span>
					</button>
				</div>
			<?php }
		}
	}


	public static function save() {
		if ( ! empty( $_POST['wpjsfsp_settings'] ) && empty( $_POST['wpjsfsp_license_activate'] ) && empty( $_POST['wpjsfsp_license_deactivate'] ) ) {

			if ( ! isset( $_POST['wpjsfsp_settings_nonce'] ) || ! wp_verify_nonce( $_POST['wpjsfsp_settings_nonce'], basename( __FILE__ ) ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$settings = self::sanitize_settings( $_POST['wpjsfsp_settings'] );

			if ( Options::update_all( $settings ) ) {
				self::$notices[] = array(
					'type'    => 'success',
					'message' => __( 'Settings saved successfully!', 'wp-js-form-sample-plugin' ),
				);
			} else {
				self::$notices[] = array(
					'type'    => 'error',
					'message' => __( 'There must have been an error, settings not saved successfully!', 'wp-js-form-sample-plugin' ),
				);
			}
		}

	}

	/**
	 * Render settings page with tabs.
	 */
	public static function page() {

		$settings = Options::get_all();

		if ( empty( $settings ) ) {
			$settings = self::defaults();
		}

		?>

		<div class="wrap">
			<h1><?php echo _e( 'WPJSFSP Settings', 'wp-js-form-sample-plugin' ); ?></h1>

			<p>Please <a href="https://github.com/danieliser/WP-JS-Form-Sample-Plugin" target="_blank">star this repo</a> if you decide to use it in your project or just think its awesome. If you want to contribute feel free to submit changes.</p>

			<form id="wpjsfsp-settings" method="post" action="">

				<?php wp_nonce_field( basename( __FILE__ ), 'wpjsfsp_settings_nonce' ); ?>

				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-1">
						<div id="post-body-content">

							<script type="text/javascript">
                                window.wpjsfsp_settings_editor = <?php echo json_encode( apply_filters( 'wpjsfsp_settings_editor_args', array(
									'form_args'             => array(
										'id'       => 'wpjsfsp-settings',
										'tabs'     => self::tabs(),
										'sections' => self::sections(),
										'fields'   => self::fields(),
										'maintabs' => array(
											'vertial' => true,
										),
										'subtabs'  => array(),
									),
									'active_tab'            => self::get_active_tab(),
									'active_section'        => self::get_active_section(),
									'conditions'            => Conditions::instance()->get_conditions(),
									'conditions_selectlist' => Conditions::instance()->dropdown_list(),
									'current_values'        => self::parse_values( $settings ),
								) ) ); ?>;

                                jQuery(document)
                                    .ready(function () {
                                        var $container = jQuery('#wpjsfsp-settings-container'),
                                            args       = wpjsfsp_settings_editor.form_args || {},
                                            values     = wpjsfsp_settings_editor.current_values || {};

                                        if ($container.length) {
                                            WPJSFSP.forms.render(args, values, $container);
                                        }
                                    });
							</script>

							<div id="wpjsfsp-settings-container" class="wpjsfsp-settings-container"></div>

							<button class="button-primary wpjsfsp-submit button"><?php _e( 'Save', 'wp-js-form-sample-plugin' ); ?></button>

						</div>
					</div>
					<br class="clear" />
				</div>
			</form>
		</div>

		<?php
	}

	/**
	 * List of tabs & labels for the settings panel.
	 *
	 * @return array
	 */
	public static function tabs() {
		return apply_filters( 'wpjsfsp_settings_tabs', array(
			'features' => __( 'Features', 'wp-js-form-sample-plugin' ),
			'fields'   => __( 'Field Types', 'wp-js-form-sample-plugin' ),
		) );
	}

	/**
	 * List of tabs & labels for the settings panel.
	 *
	 * @return array
	 */
	public static function sections() {
		return apply_filters( 'wpjsfsp_settings_tab_sections', array(
			'features' => array(
				'general'      => __( 'General', 'wp-js-form-sample-plugin' ),
				'dependencies' => __( 'Field Dependencies', 'wp-js-form-sample-plugin' ),
				'inlinedocs'   => __( 'Inline Documentation', 'wp-js-form-sample-plugin' ),
			),
			'fields'   => array(
				'general'    => __( 'General', 'wp-js-form-sample-plugin' ),
				'html5'      => __( 'HTML5', 'wp-js-form-sample-plugin' ),
				'custom'     => __( 'Custom', 'wp-js-form-sample-plugin' ),
				'conditions' => __( 'Conditions', 'wp-js-form-sample-plugin' ),
				'misc'       => __( 'Misc', 'wp-js-form-sample-plugin' ),
			),
		) );
	}

	/**
	 * Returns array of wpjsfsp settings fields.
	 *
	 * @return mixed
	 */
	public static function fields() {

		static $tabs;

		if ( ! isset( $tabs ) ) {
			$tabs = apply_filters( 'wpjsfsp_settings_fields', array(
				'features' => array(
					'general'      => array(
						'general_desc' => array(
							'type'    => 'html',
							'content' => '<p>Having built many interfaces in WordPress over the past 7 years I recently built this as a quick start to any project when admin forms are needed. It is built mostly in JavaScript but fields are easily passed from PHP as in this example. Features are too many to name but include:</p><ul class="ul-disc">
<li>Easy form / settings management. Add fields with a few extra lines in an array.</li>
<li>If you already manage fields in an array (EDD, Popup Maker) you can easily port to this new rendering method.</li>
<li>Lots of powerful custom fields including post type search fields, link pickers, license keys,  color pickers, rangesliders & even a boolean based targerting/conditions manager.</li>
<li>Field dependency management, inline documentation & great default styles mean you spend less time crafting forms & more time creating awesome features.</li>
</ul>',
						),
						'tabs_heading' => array(
							'type' => 'heading',
							'desc' => 'Tabbed Forms',
						),
						'tabs_desc'    => array(
							'type'    => 'html',
							'content' => '<p>Besides offering field management, this library offers multiple variations of tabs, subtabs & sections to organize your forms.</p><ul class="ul-disc">
<li>Tabs & Subtabs can be set as vertical, horizontal or link tabs.</li>
<li>Styled to match the WordPress Admin interface, but can easily be customized to match your existing setup.</li>
<li>Forms can be organized with just fields, tabs of fields or tabs of subtabs of fields etc.</li>
</ul>',
						),
						'credit_heading' => array(
							'type' => 'heading',
							'desc' => 'Credits',
						),
						'credit_desc'  => array(
							'type'    => 'html',
							'content' => '<p>This is the culmination of work put in over the last year to build or rebuild the interfaces of several plugins including:</p><ul class="ul-disc">
<li><a target="blank" href="https://twitter.com/daniel_iser">@daniel_iser</a></li>
<li><a target="blank" href="https://wordpress.com/plugins/popup-maker/">Popup Maker</a></li>
<li><a target="blank" href="https://useahoy.com/">Ahoy!</a></li>
</ul>',
						),
					),
					'dependencies' => array(
						'heading'                  => array(
							'type' => 'heading',
							'desc' => 'Dependency handling',
						),
						'dependency_desc'          => array(
							'type'    => 'html',
							'content' => '<p>One of the biggest headaches in form development is conditional fields. We have simplified this with built in dependency handling right where its needed, in the field definitions. Here is an example of how they look & work.</p>' . "<pre>'dependencies' => array(
	'field1' => array( 'value1', 'value2' ), // Field is set to either of these values.
	'field2' => true, // Checkbox checked.
),</pre>",
						),
						'chosen_field_type'        => array(
							'label'   => 'Field Type',
							'type'    => 'radio',
							'options' => array(
								'text'       => 'Text Field',
								'select'     => 'Select Field',
								'multicheck' => 'Multi Check',
							),
						),
						'chosen_field_text_select' => array(
							'type'         => 'html',
							'content'      => '<p>This will only show if you chose Text or Select and demonstrates a field can check for multiple values.</p>',
							'dependencies' => array(
								'chosen_field_type' => array( 'text', 'select' ),
							),
						),
						'chosen_field_text'        => array(
							'label'        => 'Field Type: Text',
							'type'         => 'text',
							'dependencies' => array(
								'chosen_field_type' => 'text',
							),
						),
						'chosen_field_select'      => array(
							'label'        => 'Field Type: Select',
							'type'         => 'select',
							'options'      => array(
								'option1' => 'Option 1',
								'option2' => 'Option 2',
							),
							'dependencies' => array(
								'chosen_field_type' => 'select',
							),
						),
						'chosen_field_multicheck'  => array(
							'label'        => 'Field Type: Multicheck',
							'type'         => 'multicheck',
							'options'      => array(
								'option1' => 'Option 1',
								'option2' => 'Option 2',
							),
							'dependencies' => array(
								'chosen_field_type' => 'multicheck',
							),
						),
					),
					'inlinedocs'   => array(
						'doclink' => array(
							'label'   => 'Built in doc linking per field',
							'type'    => 'text',
							'doclink' => 'https://github.com/danieliser/WP-JS-Form-Sample-Plugin',
						),
					),
				),
				'fields'   => array(
					'general'    => array(
						'text'       => array(
							'type'  => 'text',
							'label' => 'Text Field',
						),
						'password'   => array(
							'type'  => 'password',
							'label' => 'Password Field',
							'std'   => '',
						),
						'select'     => array(
							'type'    => 'select',
							'label'   => 'Select Fields',
							'options' => array(
								'option1' => 'Option 1',
								'option2' => 'Option 2',
							),
						),
						'radio'      => array(
							'type'    => 'radio',
							'label'   => 'Radio Fields',
							'options' => array(
								'option1' => 'Option 1',
								'option2' => 'Option 2',
							),
						),
						'checkbox'   => array(
							'type'  => 'checkbox',
							'label' => 'Checkbox Field',
						),
						'multicheck' => array(
							'type'    => 'multicheck',
							'label'   => 'Multicheck Field',
							'options' => array(
								'option1' => 'Option 1',
								'option2' => 'Option 2',
							),
						),
						'textarea'   => array(
							'type'  => 'textarea',
							'label' => 'Textarea Field',
						),
						'hidden'     => array(
							'type'  => 'hidden',
							'label' => 'Hidden Field, can you find it?',
						),
					),
					'html5'      => array(
						'range'  => array(
							'type'  => 'range',
							'label' => 'Range Field',
						),
						'search' => array(
							'type'  => 'search',
							'label' => 'Search Field',
						),
						'number' => array(
							'type'  => 'number',
							'label' => 'Number Field',
						),
						'email'  => array(
							'type'  => 'email',
							'label' => 'Email Field',
						),
						'url'    => array(
							'type'  => 'url',
							'label' => 'URL Field',
						),
						'tel'    => array(
							'type'  => 'tel',
							'label' => 'Telephone Field',
						),
					),
					'custom'     => array(
						'select2' => array(
							'select2' => true,
							'type' => 'select',
							'label' => 'Smart Select Field (select2)',
							'options' => array(
								'option1' => 'Option 1',
								'option2' => 'Option 2',
								'option3' => 'Option 3',
							),
						),
						'postselect' => array(
							'type' => 'postselect',
							'post_type' => 'post',
							'multiple' => true,
							'as_array' => true,
							'label' => 'Post Type Search (post_type: post)',
						),
						'taxonomyselect' => array(
							'type' => 'taxonomyselect',
							'taxonomy' => 'category',
							'multiple' => true,
							'as_array' => true,
							'label' => 'Taxonomy Search (taxonomy: category)',
						),
						'license_key' => array(
							'type'  => 'license_key',
							'label' => 'License Key Field',
						),
						'link'        => array(
							'type'  => 'link',
							'label' => 'Text Field',
						),
						'rangeslider' => array(
							'type'  => 'rangeslider',
							'label' => 'Rangeslider Field',
							'std'   => 50,
						),
						'color'       => array(
							'type'  => 'color',
							'label' => 'Color Field',
						),
						'measure'     => array(
							'type'  => 'measure',
							'label' => 'Measure Field',
							'std'   => '50%',
						),
						/**
						 * 'datetime'      => array(
						 * 'type'  => 'datetime',
						 * 'label' => 'Date/Time Field',
						 * ),
						 * 'datetimerange' => array(
						 * 'type'  => 'datetimerange',
						 * 'label' => 'Date/Time Range Field',
						 * ),
						 * 'editor'        => array(
						 * 'type'  => 'editor',
						 * 'label' => 'Editor Field',
						 * ),
						 */
					),
					'conditions' => array(
						'conditions' => array(
							'type' => 'conditions',
							'std'  => array(),
						),
					),
					'misc'       => array(
						'heading'    => array(
							'type' => 'heading',
							'desc' => 'Heading Field',
						),
						'html'       => array(
							'type'    => 'html',
							'label'   => 'Static HTML Field',
							'content' => '<p>This is an <strong>html field type</strong> with custom content.</p><p>Below is a separator without any description.</p>',
						),
						'separator'  => array(
							'type'  => 'separator',
							'label' => '',
						),
						'separator2' => array(
							'type'          => 'separator',
							'desc'          => 'Separator with description on top',
							'desc_position' => 'top',
						),
						'html2'      => array(
							'type'    => 'html',
							'content' => '<p>This is only here to make the separator fields above & below more apparent.</p>',
						),

						'separator3' => array(
							'type' => 'separator',
							'desc' => 'Separator with description on bottom',
						),
					),
				),
			) );

			foreach ( $tabs as $tab_id => $sections ) {

				foreach ( $sections as $section_id => $fields ) {

					if ( self::is_field( $fields ) ) {
						// Allow for flat tabs with no sections.
						$section_id = 'main';
						$fields     = array(
							$section_id => $fields,
						);
					}

					foreach ( $fields as $field_id => $field ) {
						if ( ! is_array( $field ) || ! self::is_field( $field ) ) {
							continue;
						}

						if ( empty( $field['id'] ) ) {
							$field['id'] = $field_id;
						}
						if ( empty( $field['name'] ) ) {
							$field['name'] = 'wpjsfsp_settings[' . $field_id . ']';
						}

						$tabs[ $tab_id ][ $section_id ][ $field_id ] = wp_parse_args( $field, array(
							'section'       => 'main',
							'type'          => 'text',
							'id'            => null,
							'label'         => '',
							'desc'          => '',
							'name'          => null,
							'templ_name'    => null,
							'size'          => 'regular',
							'options'       => array(),
							'std'           => null,
							'rows'          => 5,
							'cols'          => 50,
							'min'           => 0,
							'max'           => 50,
							'force_minmax'  => false,
							'step'          => 1,
							'select2'       => null,
							'object_type'   => 'post_type',
							'object_key'    => 'post',
							'post_type'     => null,
							'taxonomy'      => null,
							'multiple'      => null,
							'as_array'      => false,
							'placeholder'   => null,
							'checkbox_val'  => 1,
							'allow_blank'   => true,
							'readonly'      => false,
							'required'      => false,
							'disabled'      => false,
							'hook'          => null,
							'unit'          => __( 'ms', 'wp-js-form-sample-plugin' ),
							'desc_position' => 'bottom',
							'units'         => array(
								'px'  => 'px',
								'%'   => '%',
								'em'  => 'em',
								'rem' => 'rem',
							),
							'priority'      => null,
							'doclink'       => '',
							'button_type'   => 'submit',
							'class'         => '',
						) );
					}
				}
			}
		}

		return $tabs;
	}

	/**
	 * Parse values for form rendering.
	 *
	 * Add additional data for license_key fields, split the measure fields etc.
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public static function parse_values( $settings ) {

		foreach ( $settings as $key => $value ) {
			$field = self::get_field( $key );


			if ( $field ) {

				/**
				 * Process fields with specific types.
				 */
				switch ( $field['type'] ) {
					case 'measure':
						break;

					case 'license_key':
						// You will need to integrate this to your licensing system
						$settings[ $key ] = array(
							'key'      => $value,
							'status'   => 'empty',
							'messages' => array(),
							'expires'  => '',
							'classes'  => '',
						);
						break;
				}

			}
		}

		return $settings;
	}

	public static function get_active_tab() {
		$tabs = self::tabs();

		return isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_text_field( $_GET['tab'] ) : key( $tabs );
	}

	public static function get_active_section() {
		$active_tab = self::get_active_tab();
		$sections   = self::sections();

		$tab_sections = ! empty( $sections[ $active_tab ] ) ? $sections[ $active_tab ] : false;

		if ( ! $tab_sections ) {
			return false;
		}

		return isset( $_GET['section'] ) && array_key_exists( $_GET['section'], $tab_sections ) ? sanitize_text_field( $_GET['section'] ) : key( $tab_sections );
	}

	public static function get_field( $id ) {
		$tabs = self::fields();

		foreach ( $tabs as $tab => $sections ) {

			if ( self::is_field( $sections ) ) {
				$sections = array(
					'main' => array(
						$tab => $sections,
					),
				);
			}

			foreach ( $sections as $section => $fields ) {

				foreach ( $fields as $key => $args ) {
					if ( $key == $id ) {
						return $args;
					}
				}
			}
		}

		return false;
	}

	public static function sanitize_settings( $settings = array() ) {

		foreach ( $settings as $key => $value ) {
			$field = self::get_field( $key );

			if ( $field ) {

				switch ( $field['type'] ) {
					case 'measure':
						$settings[ $key ] .= $settings[ $key . '_unit' ];
						break;

					case 'license_key':
						// Activate / deactivate license keys maybe?
						break;
				}
			} else {
				// Some custom field types include multiple additional fields that do not need to be saved, strip out any non-whitelisted fields.
				unset( $settings[ $key ] );
			}
		}

		return $settings;
	}

	/**
	 * @return array
	 */
	public static function defaults() {
		$tabs = self::fields();

		$defaults = array();

		foreach ( $tabs as $section_id => $fields ) {
			foreach ( $fields as $key => $field ) {
				$defaults[ $key ] = isset( $field['std'] ) ? $field['std'] : null;
			}
		}

		return $defaults;
	}

	/**
	 * Checks if an array is a field.
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function is_field( $array = array() ) {
		$field_tests = array(
			isset( $array['id'] ),
			isset( $array['label'] ),
			isset( $array['type'] ),
			isset( $array['options'] ),
			isset( $array['desc'] ),
		);

		return in_array( true, $field_tests );
	}

	/**
	 * Checks if an array is a section.
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function is_section( $array = array() ) {
		return ! self::is_field( $array );
	}

	/**
	 * @param array $meta
	 *
	 * @return array
	 */
	public static function sanitize_objects( $meta = array() ) {
		if ( ! empty( $meta ) ) {

			foreach ( $meta as $key => $value ) {

				if ( is_string( $value ) ) {
					try {
						$value = json_decode( stripslashes( $value ) );
					} catch ( \Exception $e ) {
					};
				}

				$meta[ $key ] = Helpers::object_to_array( $value );
			}
		}

		return $meta;
	}

}
