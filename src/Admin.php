<?php
/**
 * Admin
 *
 * @package hgod/hgwputils
 * @author Henrique Godinho <ola@hgod.in>
 */

namespace HGWPUtils;

/**
 * Classe Admin
 */
class Admin {
	/**
	 * Admin Options
	 *
	 * @var array $admin | Admin
	 */
	private $admin = array();

	/**
	 * Menu Options
	 *
	 * @var array $menu | Menu
	 */
	public $menu = array();

	/**
	 * Submenu Options
	 *
	 * @var array $submenu | Submenu
	 */
	private $submenus = array();

	/**
	 * Returned Submenu
	 *
	 * @var array $returned_submenu | Returned Submenu
	 */
	private $returned_submenu = array();

	/**
	 * Settings
	 *
	 * @var array $settings | Settings
	 */
	private $settings = array();

	/**
	 * Settings Sections
	 *
	 * @var array $sections | Sections
	 */
	private $sections = array();

	/**
	 * Constructor
	 *
	 * @param array $menu | Menu.
	 * @param array $submenus | Submenus.
	 * @param array $settings | Settings.
	 */
	public function __construct( $menu = array(), $submenus = array(), $settings = array() ) {
		$this->parse_menu( $menu );
		if ( ! empty( $submenus ) ) {
			$this->parse_submenus( $submenus );
		}
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		if ( ! empty( $settings ) ) {
			$this->parse_settings( $settings );
			add_action( 'admin_init', array( $this, 'init_settings' ) );
		}
	}

	/**
	 * Parse Menu
	 *
	 * @param array $menu | Menu.
	 * @return void
	 */
	public function parse_menu( $menu ) {
		$admin_menu          = $menu;
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
	}

	/**
	 * Parse Submenus
	 *
	 * @param array $submenus | Submenus.
	 * @return void
	 */
	public function parse_submenus( $submenus ) {
		$parsed_submenus = array();
		foreach ( $submenus as $submenu ) {
			$submenu_defaults     = array(
				'parent_slug' => $this->menu['menu_slug'],
				'page_title'  => '',
				'menu_title'  => '',
				'capability'  => 'edit_posts',
				'menu_slug'   => '',
				'callback'    => '',
				'position'    => null,
			);
			$parsed_admin_submenu = wp_parse_args( $submenu, $submenu_defaults );
			array_push( $parsed_submenus, $parsed_admin_submenu );
		}
		$this->submenus = $parsed_submenus;
	}

	/**
	 * Parse Settings
	 *
	 * @param array $settings | Settings.
	 * @return void
	 */
	public function parse_settings( $settings ) {
		$option_group      = $settings['option_group'];
		$option_name       = $settings['option_name'];
		$settings_sections = $settings['sections'];
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
		$this->settings        = $parsed_admin_settings;
	}

	/**
	 * Parse Args
	 *
	 * @param array $args | Options Array to be parsed.
	 * @return array $parsed_args | Parsed Options Array.
	 * @deprecated 0.3
	 */
	public function parse_args( $args ) {
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
				$parsed_admin_settings = $this->parse_settings( $setting );
				array_push( $settings, $parsed_admin_settings );
			} else {
				foreach ( $admin_settings as $setting ) {
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
		);
		return $parsed_args;
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
	 * Settings Init
	 *
	 * @return void
	 */
	public function init_settings() {
		$settings = $this->settings;
		register_setting(
			$settings['option_group'],
			$settings['option_name']
		);
		$this->loop_sections( $settings['sections'] );
	}

	/**
	 * Add Admin Menu
	 *
	 * @return void
	 */
	public function admin_menu() {
		if ( isset( $this->menu ) && isset( $this->submenus ) ) {
			$menu         = $this->menu;
			$submenus     = $this->submenus;
			$menu_page    = add_menu_page(
				$menu['title'],
				$menu['menu_title'],
				$menu['capability'],
				$menu['menu_slug'],
				$menu['callback'],
				$menu['icon_url'],
				$menu['position']
			);
			$menu['hook'] = $menu_page;
			$this->loop_submenu( $submenus );
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
				array_push( $this->returned_submenu, $submenu );
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
					array_push( $this->returned_submenu, $submenu );
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
