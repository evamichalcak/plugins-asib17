<?php

/**
 * The class that powers the whole NG Vote plugin.
 * Adds all actions and stuff to WordPress.
 *
 * @package   ng-vote
 * @copyright Copyright (c) 2015, Ashley Evans
 * @license   GPL2+
 */
class NG_Vote {

	/**
	 * The single instance of NG_Vote
	 *
	 * @var NG_Vote
	 * @access private
	 * @since  1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 *
	 * @var string
	 * @access public
	 * @since  1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var string
	 * @access public
	 * @since  1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var string
	 * @access public
	 * @since  1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var string
	 * @access public
	 * @since  1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var string
	 * @access public
	 * @since  1.0.0
	 */
	public $assets_url;

	/**
	 * Constructor function.
	 *
	 * @param string $file
	 * @param string $version The plugin version number
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'ng-vote';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		// Add JavaScript to the front-end of the site.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// All ajax.
		$this->ajax();

		// Add admin menu page.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Main NG_Vote Instance
	 *
	 * Ensures only one instance of NG_Vote is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see   NG_Vote()
	 * @return NG_Vote instance
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', $this->_token ), $this->_version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', $this->_token ), $this->_version );
	}

	/**
	 * Adds JavaScript to the front-end of the site.
	 *
	 * @param string $hook
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_assets() {
		// JavaScript
		wp_enqueue_script( $this->_token, $this->assets_url . 'js/ng-vote.js', array( 'jquery' ), $this->_version, true );
		$data = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'cast_tweak_me_vote' )
		);
		wp_localize_script( $this->_token, 'NG_VOTE', $data );
	}

	/**
	 * Holds all ajax actions.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function ajax() {
		add_action( 'wp_ajax_nopriv_ng_cast_vote', array( $this, 'cast_vote' ) );
		add_action( 'wp_ajax_ng_cast_vote', array( $this, 'cast_vote' ) );
	}

	/**
	 * Cast Vote
	 *
	 * Ajax callback for casting a vote.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function cast_vote() {
		// Security check.
		check_ajax_referer( 'cast_tweak_me_vote', 'nonce' );

		// If they've already voted, they can't vote again.
		if ( $this->has_voted() ) {
			wp_send_json_error( __( 'Error: You have already voted! I hope you\'re not trying to cheat because that\'s just lame.', $this->_token ) );
			exit;
		}

		// Get the blog URL they're voting for.
		$blog_url = strip_tags( $_POST['blog_url'] );

		// Get their IP address.
		$ip = $this->get_ip();

		// Add their IP to the array of voted IPs.
		$voted_ips   = get_option( 'ng_voted_ips', array() );
		$voted_ips[] = $ip;
		update_option( 'ng_voted_ips', $voted_ips );

		// Set a cookie.
		setcookie( 'ng_vote_tweak_me', $blog_url, strtotime( 'August 15, 2015' ), COOKIEPATH, COOKIE_DOMAIN, false, false );

		// Update their vote.
		$votes              = get_option( 'ng_votes_tweak_me', array() );
		$number_votes       = array_key_exists( $blog_url, $votes ) ? $votes[ $blog_url ] + 1 : 1;
		$votes[ $blog_url ] = $number_votes;

		// Sort the array from most votes to least votes.
		arsort( $votes );

		// Update the votes in the database.
		update_option( 'ng_votes_tweak_me', $votes );

		wp_send_json_success( sprintf(
			__( 'Your vote has been cast successfully. Thanks! P.S. there are %s votes for this design. Wish it luck! (Yes, you can wish blog designs good luck.)', $this->_token ),
			$number_votes
		) );
	}

	/**
	 * Gets the current user's IP address.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * Whether or not the current user has cast a vote.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return bool True if they've voted.
	 */
	public function has_voted() {
		$ip        = $this->get_ip();
		$voted_ips = get_option( 'ng_voted_ips', array() );

		// If their IP is in the array of voted IPs, they've voted.
		if ( in_array( $ip, $voted_ips ) ) {
			return true;
		}

		// If the cookie is set, they've voted.
		if ( isset( $_COOKIE['ng_vote_tweak_me'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Admin Menu
	 *
	 * Adds a new submenu page under "Tools" that displays the voting results.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'NG Vote Results', $this->_token ),
			__( 'Voting Results', $this->_token ),
			'manage_options',
			'ng-vote-results',
			array( $this, 'admin_menu_callback' )
		);
	}

	/**
	 * Admin Menu Callback
	 *
	 * Renders the admin menu page.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_menu_callback() {
		$votes = get_option( 'ng_votes_tweak_me', array() );
		arsort( $votes );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'NG Vote Results', $this->_token ); ?></h1>

			<table>
				<thead>
				<tr>
					<th><?php esc_html_e( 'Site URL', $this->_token ); ?></th>
					<th><?php esc_html_e( 'Number of Votes', $this->_token ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $votes as $site_url => $number ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( $site_url ); ?>" target="_blank"><?php echo esc_url( $site_url ); ?></a>
						</td>
						<td>
							<?php echo intval( $number ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

}