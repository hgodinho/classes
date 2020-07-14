<?php
/**
 * Classe HGod_Admin
 *
 * @package hgod/classes
 * @author Hgodinho <hnrq.godinho@gmail.com>
 */

/**
 * HGod_Admin
 */
class HGod_Admin {
	/**
	 * Text Domain
	 *
	 * @var string
	 */
	private $txt_domain;

	/**
	 * Admin Options
	 *
	 * @var array
	 */
	private $admin = array();

	/**
	 * Menu Options
	 *
	 * @var array
	 */
	private $menu = array();

	/**
	 * Submenu Options
	 *
	 * @var array
	 */
	private $submenus = array();

	/**
	 * Settings
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Settings Sections
	 *
	 * @var array
	 */
	private $sections = array();

	/**
	 * Construtor
	 *
	 * @param array $args | Options Array.
	 */
	public function __construct( $args ) {
		if ( is_array( $args ) ) {
			if ( count( array_values( $args ) ) > 0 && count( array_values( $args ) ) < 2 ) {
				$parsed_admin = $this->parse_args( $args[0] );
				array_push( $this->admin, $parsed_admin );
			} else {
				foreach ( $args as $arg ) {
					$parsed_admin = $this->parse_args( $arg );
					array_push( $this->admin, $parsed_admin );
				}
			}
		}
		$count = count( array_values( $this->admin ) );
		for ( $i = 0; $i < $count; $i++ ) {
			$this->admin[ $i ]['index'] = $i;
		}

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'init_settings' ) );
	}

	/**
	 * Parse Args
	 *
	 * @param array $args | Options Array to be parsed.
	 * @return array $parsed_args | Parsed Options Array.
	 */
	public function parse_args( $args ) {
		$txt_domain          = $args['txt_domain'];
		$this->txt_domain    = $txt_domain;
		$admin_menu          = $args['admin_menu'];
		$admin_menu_defaults = array(
			'title'      => '',
			'menu_title' => '',
			'capability' => 'edit_posts',
			'menu_slug'  => '',
			'callback'   => '',
			'icon_url'   => '',
			'position'   => null,
		);
		$parsed_admin_menu   = wp_parse_args( $admin_menu, $admin_menu_defaults );
		$this->menu          = $parsed_admin_menu;

		$admin_submenu = $args['admin_submenu'];
		$submenus      = array();
		if ( is_array( $admin_submenu ) ) {
			if ( count( array_values( $admin_submenu ) ) > 0 && count( array_values( $admin_submenu ) ) < 2 ) {
				$parsed_admin_submenu = $this->parse_submenu( $admin_submenu[0] );
				array_push( $submenus, $parsed_admin_submenu );
			} else {
				foreach ( $admin_submenu as $submenu ) {
					$parsed_admin_submenu = $this->parse_submenu( $submenu );
					array_push( $submenus, $parsed_admin_submenu );
				}
			}
			$this->submenus = $submenus;
		}

		$admin_settings = $args['settings'];
		$settings       = array();
		if ( is_array( $admin_settings ) ) {
			if ( count( array_values( $admin_settings ) ) > 0 && count( array_values( $admin_settings ) ) < 2 ) {
				$setting               = $admin_settings[0];
				$setting['txt_domain'] = $txt_domain;
				$parsed_admin_settings = $this->parse_settings( $setting );
				array_push( $settings, $parsed_admin_settings );
			} else {
				foreach ( $admin_settings as $setting ) {
					$setting['txt_domain'] = $txt_domain;
					$parsed_admin_settings = $this->parse_settings( $setting );
					array_push( $settings, $parsed_admin_settings );
				}
			}
			$this->settings = $settings;
		}

		$parsed_args = array(
			'admin_menu'    => $parsed_admin_menu,
			'admin_submenu' => $this->submenus,
			'settings'      => $this->settings,
			'txt_domain'    => $txt_domain,
		);
		return $parsed_args;
	}

	/**
	 * Parse Settings
	 *
	 * @param array $admin_settings | Admin Settings to be parsed.
	 * @return array $parsed_admin_settings | Parsed Admin Settings.
	 */
	public function parse_settings( $admin_settings ) {
		$option_group      = $admin_settings['option_group'];
		$option_name       = $admin_settings['option_name'];
		$settings_sections = $admin_settings['sections'];
		$sections          = array();
		if ( is_array( $settings_sections ) ) {
			if ( count( array_values( $settings_sections ) ) > 0 && count( array_values( $settings_sections ) ) < 2 ) {
				$section        = $settings_sections[0];
				$parsed_section = $this->parse_section( $section );
				array_push( $sections, $parsed_section );
			} else {
				foreach ( $settings_sections as $section ) {
					$parsed_section = $this->parse_section( $section );
					array_push( $sections, $parsed_section );
				}
			}
			$this->sections = $sections;
		}
		$parsed_admin_settings = array(
			'option_group' => $option_group,
			'option_name'  => $option_name,
			'sections'     => $sections,
		);
		return $parsed_admin_settings;
	}

	/**
	 * Parse Section
	 *
	 * @param array $section | Settings Sections to be parsed.
	 * @return array $parsed_section | Parsed Settings Sections.
	 */
	public function parse_section( $section ) {
		$section_fields = $section['fields'];
		$fields         = array();
		if ( is_array( $section_fields ) ) {
			if ( count( array_values( $section_fields ) ) > 0 && count( array_values( $section_fields ) ) < 2 ) {
				$field        = $section_fields[0];
				$parsed_field = $this->parse_field( $section_fields );
				array_push( $fields, $parsed_field );
			} else {
				foreach ( $section_fields as $field ) {
					$parsed_field = $this->parse_field( $field );
					array_push( $fields, $parsed_field );
				}
			}
			$this->fields = $fields;
		}

		$section_default = array(
			'id'       => '',
			'title'    => '',
			'callback' => '',
			'page'     => '',
			'fields'   => array(
				$fields,
			),
		);
		$parsed_section  = wp_parse_args( $section, $section_default );
		return $parsed_section;
	}

	/**
	 * Parse Fields
	 *
	 * @param array $field | Field to be parsed.
	 * @return array $parsed_field | Parsed Field.
	 */
	public function parse_field( $field ) {
		$field_defaults = array(
			'id'       => '',
			'title'    => '',
			'callback' => '',
			'page'     => '',
			'section'  => '',
		);
		$parsed_field   = wp_parse_args( $field, $field_defaults );
		return $parsed_field;
	}

	/**
	 * Parse Submenu
	 *
	 * @param array $admin_submenu | Admin Submenu to be parsed.
	 * @return array $parsed_admin_submenu | Parsed Admin Submenu.
	 */
	public function parse_submenu( $admin_submenu ) {
		$admin_submenu_defaults = array(
			'parent_slug' => $this->menu['menu_slug'],
			'page_title'  => '',
			'menu_title'  => '',
			'capability'  => 'edit_posts',
			'menu_slug'   => '',
			'callback'    => '',
			'position'    => null,
		);
		$parsed_admin_submenu   = wp_parse_args( $admin_submenu, $admin_submenu_defaults );
		return $parsed_admin_submenu;
	}

	/**
	 * Settings Init
	 *
	 * @return void
	 */
	public function init_settings() {
		$settings = $this->settings;
		if ( is_array( $settings ) ) {
			$count = count( array_values( $settings ) );
			if ( $count > 0 && $count < 2 ) {
				$setting = $settings[0];
				register_setting(
					$setting['option_group'],
					$setting['option_name']
				);
				$this->loop_sections( $setting['sections'] );
			} else {
				foreach ( $settings as $setting ) {
					register_setting(
						$setting['option_group'],
						$setting['option_name']
					);
					$this->loop_sections( $setting['sections'] );
				}
			}
		}
	}

	/**
	 * Add Admin Menu
	 *
	 * @return void
	 */
	public function admin_menu() {
		$txt_domain = $this->txt_domain;
		if ( isset( $this->admin ) ) {
			if ( is_array( $this->admin ) ) {
				$index = 0;
				$count = count( array_values( $this->admin ) );
				if ( $count > 0 && $count < 2 ) {
					$admin                         = $this->admin[ $index ];
					$admin_menu                    = $admin['admin_menu'];
					$admin_submenu                 = $admin['admin_submenu'];
					$menu_page                     = add_menu_page(
						$admin_menu['title'],
						$admin_menu['menu_title'],
						$admin_menu['capability'],
						$admin_menu['menu_slug'],
						$admin_menu['callback'],
						$admin_menu['icon_url'],
						$admin_menu['position']
					);
					$this->admin[ $index ]['hook'] = $menu_page;
					$this->loop_submenu( $admin_submenu );
				} else {
					foreach ( $this->admin as $admin ) {
						$admin_menu                    = $admin['admin_menu'];
						$admin_submenu                 = $admin['admin_submenu'];
						$menu_page                     = add_menu_page(
							$admin_menu['title'],
							$admin_menu['menu_title'],
							$admin_menu['capability'],
							$admin_menu['menu_slug'],
							$admin_menu['callback'],
							$admin_menu['icon_url'],
							$admin_menu['position']
						);
						$this->admin[ $index ]['hook'] = $menu_page;
						$this->loop_submenu( $admin_submenu );
						$index++;
					}
				}
			}
		}
	}

	/**
	 * Loop Submenu
	 *
	 * @param array $args | Submenu parameters Array.
	 * @return void
	 */
	public function loop_submenu( $args ) {
		if ( is_array( $args ) ) {
			$index = 0;
			$count = count( array_values( $args ) );
			if ( $count > 0 && $count < 2 ) {
				$submenu          = $args[ $index ];
				$submenu_page     = add_submenu_page(
					$submenu['parent_slug'],
					$submenu['page_title'],
					$submenu['menu_title'],
					$submenu['capability'],
					$submenu['menu_slug'],
					$submenu['callback'],
					$submenu['position']
				);
				$submenu['index'] = $index;
				$submenu['hook']  = $submenu_page;
			} else {
				foreach ( $args as $submenu ) {
					$submenu_page     = add_submenu_page(
						$submenu['parent_slug'],
						$submenu['page_title'],
						$submenu['menu_title'],
						$submenu['capability'],
						$submenu['menu_slug'],
						$submenu['callback'],
						$submenu['position']
					);
					$submenu['index'] = $index;
					$submenu['hook']  = $submenu_page;
					$index++;
				}
			}
		}
	}

	/**
	 * Loop Settings Sections
	 *
	 * @param array $args | Sections parameters Array.
	 * @return void
	 */
	public function loop_sections( $args ) {
		if ( is_array( $args ) ) {
			$count = count( array_values( $args ) );
			if ( $count > 0 && $count < 2 ) {
				$section = $args[0];
				add_settings_section(
					$section['id'],
					$section['title'],
					$section['callback'],
					$section['page']
				);
				$fields = $section['fields'];
				$this->loop_fields( $fields );
			} else {
				foreach ( $args as $section ) {
					add_settings_section(
						$section['id'],
						$section['title'],
						$section['callback'],
						$section['page']
					);
					$fields = $section['fields'];
					$this->loop_fields( $fields );
				}
			}
		}
	}

	/**
	 * Loop Fields
	 *
	 * @param array $args | Fields parameters Array.
	 * @return void
	 */
	public function loop_fields( $args ) {
		if ( is_array( $args ) ) {
			$count = count( array_values( $args ) );
			if ( $count > 0 && $count < 2 ) {
				$field = $args[0];
				add_settings_field(
					$field['id'],
					$field['title'],
					$field['callback'],
					$field['page'],
					$field['section']
				);
			} else {
				foreach ( $args as $field ) {
					add_settings_field(
						$field['id'],
						$field['title'],
						$field['callback'],
						$field['page'],
						$field['section']
					);
				}
			}
		}
	}

}
