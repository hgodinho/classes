<?php
/**
 * Adiciona as Custom Taxonomies
 *
 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
 * @package hgod/classes
 * @author hgodinho <hnrq.godinho@gmail.com>
 */

/**
 * HGod_Tax
 */
class HGod_Tax {

	/**
	 * Taxonomies
	 *
	 * @var array | Taxonomies options array.
	 */
	private $taxonomies = array();

	/**
	 * Constrututor
	 *
	 * @param array $args | Taxonomies options array.
	 */
	public function __construct( $args ) {
		if ( is_array( $args ) ) {
			$count = count( array_values( $args ) );
			if ( $count > 0 && $count < 2 ) {
				$tax        = $args[0];
				$parsed_tax = $this->parse_args( $tax );
				array_push( $this->taxonomies, $parsed_tax );
			} else {
				foreach ( $args as $tax ) {
					$parsed_tax = $this->parse_args( $tax );
					array_push( $this->taxonomies, $parsed_tax );
				}
			}
		}
		add_action( 'init', array( $this, 'registra_taxonomia' ) );
	}


	/**
	 * Registra Taxonomia
	 *
	 * @return (WP_Taxonomy|WP_Error) The registered taxonomy object on success, WP_Error object on failure.
	 */
	public function registra_taxonomia() {
		if ( is_array( $this->taxonomies ) ) {
			$taxonomies = $this->taxonomies;
			$count      = count( array_values( $taxonomies ) );
			if ( $count > 0 && $count < 2 ) {
				$taxonomy   = $taxonomies[0];
				$name       = $taxonomy['name'];
				$post_types = $taxonomy['post_types'];
				$args       = $taxonomy['args'];
				$register   = register_taxonomy( $name, $post_types, $args );
				if ( is_wp_error( $register ) ) {
					HGodBee::hb_var_dump( $register, __CLASS__, __METHOD__, __LINE__, true );
				}
			} else {
				foreach ( $taxonomies as $taxonomy ) {
					$name       = $taxonomy['name'];
					$post_types = $taxonomy['post_types'];
					$args       = $taxonomy['args'];
					$register   = register_taxonomy( $name, $post_types, $args );
					if ( is_wp_error( $register ) ) {
						HGodBee::hb_var_dump( $register, __CLASS__, __METHOD__, __LINE__, true );
					}
				}
			}
		} return $register;
	}


	/**
	 * Arruma o array para passar para o método registra_taxonomia()
	 *
	 * @param array $args | Options Taxonomy Array.
	 * @return array $parsed_tax | Parsed Options Taxonomy Array.
	 */
	public function parse_args( $args ) {
		$name       = $args['name'];
		$post_types = $args['post_types'];
		$labels     = $args['labels'];
		$tax_args   = $args['args'];

		$default_args = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
		);
		$parsed_args  = wp_parse_args( $tax_args, $default_args );
		$parsed_tax   = array(
			'name'       => $name,
			'post_types' => $post_types,
			'args'       => $parsed_args,
		);
		return $parsed_tax;
	}
}
<?php
/**
 * Adiciona as Custom Taxonomies
 *
 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
 * @package hgod/classes
 * @author hgodinho <hnrq.godinho@gmail.com>
 */

/**
 * HGod_Tax
 */
class HGod_Tax {

	/**
	 * Taxonomies
	 *
	 * @var array | Taxonomies options array.
	 */
	private $taxonomies = array();

	/**
	 * Constrututor
	 *
	 * @param array $args | Taxonomies options array.
	 */
	public function __construct( $args ) {
		if ( is_array( $args ) ) {
			$count = count( array_values( $args ) );
			if ( $count > 0 && $count < 2 ) {
				$tax        = $args[0];
				$parsed_tax = $this->parse_args( $tax );
				array_push( $this->taxonomies, $parsed_tax );
			} else {
				foreach ( $args as $tax ) {
					$parsed_tax = $this->parse_args( $tax );
					array_push( $this->taxonomies, $parsed_tax );
				}
			}
		}
		add_action( 'init', array( $this, 'registra_taxonomia' ) );
	}


	/**
	 * Registra Taxonomia
	 *
	 * @return (WP_Taxonomy|WP_Error) The registered taxonomy object on success, WP_Error object on failure.
	 */
	public function registra_taxonomia() {
		if ( is_array( $this->taxonomies ) ) {
			$taxonomies = $this->taxonomies;
			$count      = count( array_values( $taxonomies ) );
			if ( $count > 0 && $count < 2 ) {
				$taxonomy   = $taxonomies[0];
				$name       = $taxonomy['name'];
				$post_types = $taxonomy['post_types'];
				$args       = $taxonomy['args'];
				$register   = register_taxonomy( $name, $post_types, $args );
				if ( is_wp_error( $register ) ) {
					HGodBee::hb_var_dump( $register, __CLASS__, __METHOD__, __LINE__, true );
				}
			} else {
				foreach ( $taxonomies as $taxonomy ) {
					$name       = $taxonomy['name'];
					$post_types = $taxonomy['post_types'];
					$args       = $taxonomy['args'];
					$register   = register_taxonomy( $name, $post_types, $args );
					if ( is_wp_error( $register ) ) {
						HGodBee::hb_var_dump( $register, __CLASS__, __METHOD__, __LINE__, true );
					}
				}
			}
		} return $register;
	}


	/**
	 * Arruma o array para passar para o método registra_taxonomia()
	 *
	 * @param array $args | Options Taxonomy Array.
	 * @return array $parsed_tax | Parsed Options Taxonomy Array.
	 */
	public function parse_args( $args ) {
		$name       = $args['name'];
		$post_types = $args['post_types'];
		$labels     = $args['labels'];
		$tax_args   = $args['args'];

		$default_args = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
		);
		$parsed_args  = wp_parse_args( $tax_args, $default_args );
		$parsed_tax   = array(
			'name'       => $name,
			'post_types' => $post_types,
			'args'       => $parsed_args,
		);
		return $parsed_tax;
	}
}
