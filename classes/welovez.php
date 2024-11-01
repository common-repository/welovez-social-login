<?php
// Abort if this file is called directly
if ( ! defined( 'WELOVEZ_PATH' ) ) {
	die;
}
/**
 * Class WeLovez
 *
 * This class creates a very simple Options page
 */
class WeLovez{

    /**
     * WeLovez | Being Social constructor.
     */
    public function __construct()
    {
		add_shortcode( 'wowonder_login', array($this, 'wowonderlogin') );
		add_shortcode( 'wowonder_share', array($this, 'welovezShare') );        
		add_action( 'wp_enqueue_scripts', array($this, 'addButtonCSS'));
		// Callback URL
        add_action( 'init', array($this, 'apiCallback'));
    }

	/**
	* The frontend security nonce
	*
	* @var string
	*/
	
	private $access_token;	

	/**
	 * Returns the WoWonder credentials as an array containing app_id and app_secret
	 *
	 * @return array
	 */
	static function getCredentials() {
	   return get_option( 'welovez_login', array() );
	}	

	/**
	 * Render the shortcode [welovez_login]
	 *
	 * It displays our Login / Register button
	 */
	public function wowonderlogin($atts) {
		// get the users entered shortcode attributes
			$atts = shortcode_atts(array(
				'button_type' => 'icon',
				'loggedin_text' => 'You are already logged in!',
			), $atts);
		// No need for the button if the user is already logged
		if(is_user_logged_in()) {
			if (isset($atts['loggedin_text'])) {
				$html = '<p>'. $atts['loggedin_text'] .'</p>';
				return $html; // Write it down as user already logged in.
			} else {
				return; // exit as user already logged in.
			}
		}
			//return;
		// Different labels according to whether the user is allowed to register or not
		if (get_option( 'users_can_register' )) {
			$button_label = __('Login or Register with WoWonder', 'welovez');
		} else {
			$button_label = __('Login with WoWonder', 'welovez');
		}

		if ($atts['button_type'] == 'wrapper') {
			  // Button markup				
			$logo = WELOVEZ_URL. '/assets/images/button-wrapper.png';
			$html = '<div id="welovez-wrapper">';
			$html .= '<a href="'.$this->getLoginUrl().'" class="btn" id="welovez-button">'. $logo . $button_label .'</a>';
			$html .= '</div>';	  
		} else if ($atts['button_type'] == 'icon') {	
			$logo = WELOVEZ_URL. '/assets/images/welovez.png';
			ob_start();
			?>				
			<div class="content-share__item lovez buzz-share-button">
				<button class="welovez-share" style="background: url(<?php echo $logo?>) no-repeat;" onclick="location.href='<?php echo $this->getLoginUrl()?>';">
				</button>
			</div>
			<?php
			$html = ob_get_clean();			
		}
		// Write it down
		return $html;
	}
	public function welovezShare() {
		$full_url= get_permalink(get_the_ID());	
		$credentials = self::getCredentials();
		$web_url = (isset($credentials['website_url']) && !empty($credentials['website_url'])) ? $credentials['website_url'] : '';
		$logo = WELOVEZ_URL. '/assets/images/welovez.png';		
		ob_start();
		?>
			<div class="content-share__item lovez buzz-share-button">
				<button class="welovez-share" style="background: url(<?php echo $logo?>) no-repeat;" onclick="window.open('<?php echo $web_url ?>sharer?url=<?php echo $full_url ?>', 'Share on Welovez', 'height=600,width=800');">
				</button >
			</div>
		<?php
		$contents = ob_get_clean();
		return $contents;
	}
	/**
	 * Login URL to wowonder API
	 *
	 * @return string
	 */
	private function getLoginUrl() {
		$credentials = self::getCredentials();
		$web_url = (isset($credentials['website_url']) && !empty($credentials['website_url'])) ? $credentials['website_url'] : '';

	   // Only if we have some credentials, ideally an Exception would be thrown here
	   if(!isset($credentials['app_id']))
		  return null;


		$url = $web_url . 'oauth?app_id=' .$credentials['app_id'];

		return esc_url($url);

	}
	/**
	 * Get user details through the WoWonder API
	 *
	 * @link https://demo.wowonder.com/developers
	 */
	private function getUserDetails($welovez)
	{  
			$credentials = self::getCredentials();
			$web_url = (isset($credentials['website_url']) && !empty($credentials['website_url'])) ? $credentials['website_url'] : '';
			$type = "get_user_data"; // or posts_data
			//$response = file_get_contents("{$web_url}app_api?access_token={$welovez}&type={$type}");
			$response = wp_remote_get("{$web_url}app_api?access_token={$welovez}&type={$type}");
			$body = wp_remote_retrieve_body( $response );
				   
		return $body;

	}
	/**
	 * Login an user to WordPress
	 *
	 * @link https://codex.wordpress.org/Function_Reference/get_users
	 * @return bool|void
	 */
	private function loginUser($welovez_user) {

		$credentials = self::getCredentials();
		$redirect_user = (isset($credentials['redirect_user']) && !empty($credentials['redirect_user'])) ? $credentials['redirect_user'] : '';
		
		// We look for the `welovez_login_email` to see if there is any match
		$wp_users = get_users(array(
			'meta_key'     => 'welovez_login_email',
			'meta_value'   => $welovez_user['email'],
			'number'       => 1,
			'count_total'  => false,
			'fields'       => 'email',
		));

		if(empty($wp_users[0])) {
			return false;
		}

		// Log the user ?
		wp_set_auth_cookie( $wp_users[0] );
		if ($redirect_user) {
			wp_safe_redirect( $redirect_user );
			exit();
		} else {
			wp_safe_redirect( site_url() );
			exit();
		}

	}
	/**
	 * Create a new WordPress account using WoWonder Details
	 */
	private function createUser($welovez_user) {
		
		$credentials = self::getCredentials();
		$redirect_user = (isset($credentials['redirect_user']) && !empty($credentials['redirect_user'])) ? $credentials['redirect_user'] : '';
		
		// Create an username
		$welovezemail = $welovez_user['email'];
		$parts = explode("@", $welovezemail);
		$username = $parts[0];
		//check if the username already exists
		$exist_username = username_exists( $username );
		if ( $exist_username ) {
			$username = $parts[0] . '_' . date("YmdHms");
		}
		// Creating our user
		$new_user = wp_create_user($username, wp_generate_password(), $welovez_user['email']);

		if(is_wp_error($new_user)) {
		   
		    echo "Error while creating user!";
			if ($redirect_user) {
				wp_safe_redirect( $redirect_user );
				exit();
			} else {
				wp_safe_redirect( site_url() );
				exit();
			}
	   }
		// Setting the meta
		update_user_meta( $new_user, 'first_name', $welovez_user['first_name'] );
		update_user_meta( $new_user, 'last_name', $welovez_user['last_name'] );
		update_user_meta( $new_user, 'welovez_login_email', $welovez_user['email'] );

		// Log the user ?
		wp_set_auth_cookie( $new_user );

	}
	public function apiCallback() {
		if ( isset($_GET['code']) ) {	
			$credentials = self::getCredentials();
			$found = sanitize_text_field($_GET['code']);    
			// Only if we have some credentials, ideally an Exception would be thrown here    
			if( !isset($credentials['app_id']) || !isset($credentials['app_secret']) ) {
				exit();
			} else {
				$web_url = (isset($credentials['website_url']) && !empty($credentials['website_url'])) ? $credentials['website_url'] : '';
				$redirected_url = (isset($credentials['redirect_user']) && !empty($credentials['redirect_user'])) ? $credentials['redirect_user'] : '';
				$get_welovez_data = wp_remote_get("{$web_url}authorize?app_id={$credentials['app_id']}&app_secret={$credentials['app_secret']}&code={$found}");
				$body = wp_remote_retrieve_body( $get_welovez_data );
				
				
				$json = json_decode($body, true);
				if (!empty($json['access_token'])) {
					$access_token = $json['access_token']; // your access token
					$wl_data = $this->getUserDetails($access_token);
					$enc_wl_data=json_decode($wl_data, true);
					$welovez_details=$enc_wl_data['user_data'];
				// We first try to login the user
					$this->loginUser($welovez_details);

				// Otherwise, we create a new account
					$this->createUser($welovez_details);

				// Redirect the user succesful login
					if ($redirected_url) {
						wp_safe_redirect( $redirected_url );
						exit();
					} else {
						wp_safe_redirect( site_url() );
						exit();
					}
				}
			}
		} else {
			return null;
		}		
	}
	public function addButtonCSS() {
		wp_enqueue_style( 'welovez-button', WELOVEZ_URL. '/assets/css/button-style.css' );
	}
}
/*
 * Starts our plugins!
 */
 new WeLovez();