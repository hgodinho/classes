<?php
/**
 * HG Util
 *
 * @package hgod/hgwp_utils
 * @author Henrique Godinho <ola@hgod.in>
 */

/**
 * Classe HG_Utils
 *
 * Define métodos úteis para serem usados ao longo do projeto.
 */
class HGWP_Utils {
	/**
	 * Válida se o post existe no cpt via slug
	 *
	 * @param array $post_name Slug do post.
	 * @param array $post_type Nome do CPT.
	 * @return boolean
	 */
	public static function the_slug_exists($post_name, $post_type) {
		global $wpdb;
		if ($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "' AND post_type = '" . $post_type . "'", 'ARRAY_A')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retorna string cortada por knife
	 *
	 * @param string $string
	 * @param string $knife
	 * @return void
	 */
	public static function substringfy($string, $knife, $pos = 'end' ) {
		$knife_cut = strpos($string, $knife);
		if ( 'end' === $pos ) {
			return substr($string, 0, $knife_cut);
		} 
		elseif ( 'start' === $pos ) {
			return substr($string, $knife_cut);
		}
		else {
			HG_Utils::special_var_dump('passar end ou start no 3º parâmetro', __CLASS__, __METHOD__, __LINE__, true);
		}
	}
	/**
	 * file Data
	 *
	 * @param string $file | File handler.
	 * @return array $plugin_data | Array with plugin data presents on file header.
	 */
	public static function file_data($file) {
		$file        = dirname(plugin_dir_path(__FILE__)) . '/' . $file;
		$plugin_data = get_file_data(
			$file,
			array(
				'Plugin Name' => 'Plugin Name',
				'Plugin Uri'  => 'Plugin Uri',
				'Description' => 'Description',
				'Version'     => 'Version',
				'Author'      => 'Author',
				'Author Uri'  => 'Author Uri',
				'Text Domain' => 'Text Domain',
				'Prefix'      => 'Prefix',
			)
		);
		return $plugin_data;
	}

	/**
	 * Var Dump especial que cita a classe, método, linha
	 * e `die()` o WordPress por padão
	 *
	 * @param mixed   $var | Valor a ser debugado.
	 * @param string  $class | __CLASS__.
	 * @param string  $method | __METHOD__.
	 * @param string  $line | __LINE__.
	 * @param boolean $die | Die WordPress on true - defaults to true.
	 * **obs:** para esse método funcionar mais fluidamente é recomendável criar
	 * snippet de código do chamado para o método no vscode passando as
	 * constantes mágicas:
	 *
	 * - `__CLASS__`,
	 * - `__METHOD__`,
	 * - `__LINE__`.
	 *
	 * @see https://www.php.net/manual/pt_BR/language.constants.predefined.php
	 *
	 * @example
	 * {
	 *     "Var Dump Especial": {
	 *    "scope": "php",
	 *    "prefix": "dump",
	 *    "body": [
	 *        "HGodBee::hb_var_dump(${1:any}, __CLASS__, __METHOD__, __LINE__, ${3:true})"
	 *    ],
	 *    "description": "Var dump especial."
	 *  },
	 * }
	 *
	 * @echo mixed
	 */
	public static function special_var_dump($var, $class, $method, $line, $die = true) {
		if (true === $die) {
			$wp = 'muerto.';
		} else {
			$wp = 'vivito.';}
		echo '<p><strong>Class: ' . $class . ' | ';
		echo 'Method: ' . $method . ' | ';
		echo 'Line: ' . $line . ' | ';
		echo 'WordPress: ' . $wp;
		echo '</strong></p>';
		var_dump($var);
		echo '<p><strong>var_dump stop</strong></p>';
		if (true === $die) {
			wp_die();
		}
	}

	/**
	 * Log especial
	 */
	public static function special_log($msg, $title = 'log', $class, $method, $line) {
		$date = date('d.m.Y h:i:s');
		if (is_bool($msg)) {
			$msg = print($msg);
		} else {
			$msg = print_r($msg, true);
		}
		$log = '[' . $date . '] - ' . $method . ' @linha-' . $line . ' | ' . $title . "\n" . $msg . "\n";
		HG_Utils::file_force_contents(dirname(plugin_dir_path(__FILE__), 1) . '/log/debug.log', $log);
	}

	/**
	 * File force contents
	 * 
	 * cria a pasta se não houver.
	 *
	 * @param string $dir
	 * @param mixed $contents
	 * @return void
	 */
	public static function file_force_contents($dir, $contents) {
		$parts = explode('/', $dir);
		$file  = array_pop($parts);
		$dir   = '';
		foreach ($parts as $part) {
			if (!is_dir($dir .= "/$part")) {
				mkdir($dir);
			}
		}
		file_put_contents("$dir/$file", $contents, FILE_APPEND);
	}

	/**
	 * Apaga o log
	 *
	 * @return void
	 */
	public static function erase_special_log() {
		$log = file_put_contents(dirname(plugin_dir_path(__FILE__)) . '/log/debug.log', '');
		if (false === $log) {
			return false;
		} else {
			return true;
		}
	}

	
}
