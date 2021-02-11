<?php
/**
 * Adiciona Custom-post types
 *
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 * @package hgod/hgwputils
 * @author Henrique Godinho <ola@hgod.in>
 */

namespace HGWPUtils;

/**
 * Cpt
 */
class Cpt {

	/**
	 * Post Types
	 *
	 * @var array
	 */
	public $postTypes = array();

	/**
	 * Construct
	 *
	 * @param array   $args | CPT options.
	 * @param boolean $init | Init.
	 */
	public function __construct( $args, $init = true ) {
		if ( is_array( $args ) ) {
			$count = count( array_values( $args ) );
			if ( $count > 0 && $count < 2 ) {
				$postType   = $args[0];
				$parsed_post = $this->parseArgs( $postType );
				array_push( $this->postTypes, $parsed_post );
			} else {
				foreach ( $args as $postType ) {
					$parsed_post = $this->parseArgs( $postType );
					array_push( $this->postTypes, $parsed_post );
				}
			}
		}
		if ( $init ) {
			add_action( 'init', array( $this, 'registraPost' ) );
		}
	}

	/**
	 * Regitra Custom post-type
	 *
	 * @return (WP_Post_Type|WP_Error) The registered post type object on success, WP_Error object on failure.
	 */
	public function registraPost() {
		$postTypes = $this->postTypes;

		if ( is_array( $postTypes ) ) {
			$count = count( array_values( $postTypes ) );
			if ( $count > 0 && $count < 2 ) {
				$postType = $postTypes[0];
				$name      = $postType['name'];
				$args      = $postType['args'];
				$register  = register_post_type( $name, $args );
				if ( is_wp_error( $register ) ) {
					HGWP_Extras::special_var_dump( $register, __CLASS__, __METHOD__, __LINE__, true );
				}
			} else {
				foreach ( $postTypes as $postType ) {
					$name = $postType['name'];
					$args = $postType['args'];
					if ( is_string( $name ) && is_array( $args ) ) {
						$register = register_post_type( $name, $args );
					}
					if ( is_wp_error( $register ) ) {
						HGWP_Extras::special_var_dump( $register, __CLASS__, __METHOD__, __LINE__, false );
					}
				}
			}
			return $register;
		}
	}

	/**
	 * Arruma o array para passar para o método resgistra_post()
	 *
	 * @param array $postType | Options Array.
	 * @return array $parsedPostType | Parsed Options Array.
	 */
	public function parseArgs( $postType ) {
		$name   = $postType['name'];
		$args   = $postType['args'];
		$labels = $postType['args']['labels'];

		$default_args = array(
			'label'               => 'Post Type',
			'description'         => 'Post Type Description',
			'labels'              => $labels,
			'supports'            => false,
			'taxonomies'          => array( 'category', 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		$parsed_args  = wp_parse_args( $args, $default_args );

		$parsedPostType = array(
			'name' => $name,
			'args' => $parsed_args,
		);

		return $parsedPostType;
	}
}
