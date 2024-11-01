<?php
// Abort if this file is called directly
if ( ! defined( 'WELOVEZ_PATH' ) ) {
	die;
}

/**
 * Class WeLovezAdmin
 *
 * This class creates Welovez Settings page
 */
class WeLovezAdmin 
{
 
	/**
	* The admin security nonce
	*
	* @var string
	*/
	private $_nonce = 'welovez_admin';
 
	/**
	* WeLovezAdmin constructor.
	*/
	public function __construct() 
	{
		add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
		add_action( 'wp_ajax_welovez_admin_settings', array( $this, 'WeLovezAdminSettings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'addAdminScripts' ) );
	 
	 }
	 /**
	 * Adds WeLovez to WordPress Admin Sidebar Menu
	 */
	public function addAdminMenu() {
		$icon = WELOVEZ_URL . '/assets/images/welovez-icon.png';
		add_menu_page(
			__( 'WeLovez Social', 'welovez' ),
			__( 'WeLovez Social', 'welovez' ),
			'manage_options',
			'welovez_login',
			array( $this, 'adminlayout' ),
			$icon
		);
	}
	/**
	 * Outputs the Admin Dashboard layout
	 */
	public function adminlayout() {
		$icon1 = '<img src="'. WELOVEZ_URL. '/assets/images/welovez.png" alt="WeLovez logo">';
		$btwrapper = '<img src="'. WELOVEZ_URL. '/assets/images/button-wrapper.png" alt="WeLovez logo">';
		$welovez_settings = WeLovez::getCredentials();
		$welovez_app_id = (isset($welovez_settings['app_id']) && !empty($welovez_settings['app_id'])) ? $welovez_settings['app_id'] : '';
		$welovez_app_secret = (isset($welovez_settings['app_secret']) && !empty($welovez_settings['app_secret'])) ? $welovez_settings['app_secret'] : '';
		$welovez_domain_url = (isset($welovez_settings['website_url']) && !empty($welovez_settings['website_url'])) ? $welovez_settings['website_url'] : '';
		$redirected_url = (isset($welovez_settings['redirect_user']) && !empty($welovez_settings['redirect_user'])) ? $welovez_settings['redirect_user'] : '';
		?>
		<div class="wrap">			
				<h2><?php _e( 'Settings - WeLovez Social Plugin', 'welovez'); ?></h2>
				<img src="<?php echo WELOVEZ_URL; ?>assets/images/banner.png" style="height: 170px;">
				<h2 class="nav-tab-wrapper">
					<a href="#welovez_button_settings" class="nav-tab" id="welovez_button_settings-tab"><?php _e( 'LOGIN-SHARE BUTTONS', 'welovez'); ?></a>
					<a href="#welovez_documentation" class="nav-tab" id="welovez_documentation-tab"><?php _e( 'DOCUMENTATION - APP CREATION', 'welovez'); ?></a>
					<a href="#welovez_help" class="nav-tab" id="welovez_help-tab"><?php _e( 'Help & Support', 'welovez'); ?></a>
				</h2>
				<div class="updated welovez-message" style="display:none;"></div>
				<div id="welovez" class="metabox-holder">
					<div id="welovez_button_settings" class="group" style="display: block;">
						<div class="inside">
							<div class="wrap welovez-performance">			
								<div class="tabs-holder">
									<div class="tab-nav">
										<input type="hidden" value="" id="tba"></a>
										<ul class="">
											<li class="active-tab" data-tabid="app-tab">
												<img src="<?php echo WELOVEZ_URL; ?>assets/images/app-setup.png" style="float: right;" width="40">
												<span><?php _e('APP SETUP', 'welovez'); ?></span>
												<p class="margin0">
													<medium><?php _e('Wowonder app connection', 'welovez'); ?></medium>
												</p>
											</li>
											<li class="" data-tabid="login-style-tab">
												<img src="<?php echo WELOVEZ_URL; ?>assets/images/login-setup.png" style="float: right;" width="40">
												<span><?php _e('Login Button', 'welovez'); ?></span>
												<p class="margin0">
													<medium><?php _e('Login button usage', 'welovez'); ?></medium>
												</p>
											</li>
											<li class="" data-tabid="share-style-tab">
												<img src="<?php echo WELOVEZ_URL; ?>assets/images/share-setup.png" style="float: right;" width="40">
												<span><?php _e('Share Button', 'welovez'); ?></span>
												<p class="margin0">
													<medium><?php _e('Share button usage', 'welovez'); ?></medium>
												</p>
											</li>
										</ul>
									</div>
									<div class="content-tab">
										<div class="single-tab" id="app-tab" style="display: block;">
											<div class="row">
												<div class="col-md-12 welovez__section app-section">
													<h3><?php _e('SETUP LOGIN TO WOWONDER WEBSITE APP', 'welovez' ); ?></h3>
													<div class="form-group">
														<b><label><?php _e( 'WoWonder Website URL', 'welovez' ); ?></label></b><span style="color:red;vertical-align: bottom;">important</span>
														<input class="form-control" id="welovez-website-url" placeholder="<?php _e('Complete URL here like:', 'welovez'); ?> https://demo.wowonder.com" value="<?php echo $welovez_domain_url; ?>" style="margin-top:2px"/>
														<label><?php _e('Your wowonder website full URL', 'welovez'); ?></label>
														<br/><br/>
														<b><label><?php _e( 'WoWonder App ID', 'welovez' ); ?></label></b><span style="color:red;vertical-align: bottom;margin-left:2px;">important</span>
														<input class="form-control" id="welovez-app-id" placeholder="337da31ab60e22fd6f0d" value="<?php echo $welovez_app_id;?>" style="margin-top:2px"/>
														<label><?php _e('Enter Wowonder login app id', 'welovez' ); ?></label>
														<br/><br/>
														<b><label><?php _e( 'WoWonder App Secret', 'welovez' ); ?></label></b><span style="color:red;vertical-align: bottom;margin-left:2px;">important</span>
														<input class="form-control" id="welovez-app-secret" placeholder="99434e0fe90205bf0257103f6a18063f1fef5ca" value="<?php echo $welovez_app_secret; ?>" style="margin-top:2px"/>
														<label><?php _e('Enter Wowonder login app secret', 'welovez' ); ?></label>
														<br/><br/>
														<b><label><?php _e( 'Redirect User After Login', 'welovez' ); ?></label></b>
														<input class="form-control" id="welovez-redirect-user" placeholder="<?php echo site_url(); ?> " value="<?php echo $redirected_url; ?>" style="margin-top:2px"/>
														<label><?php _e('Enter any page url where user will be redirected after login using wowonder website.', 'welovez' ); ?></label>
														<br/><br/>
													</div>
													<br/>
													<div class="form-group">
														<button class="button welovez-btn" id="welovez-details"><?php _e( 'Save Changes', 'welovez' ); ?></button>
													</div>												
												</div>
											</div>
										</div>	
										<div class="single-tab" id="login-style-tab" style="display: none;">
											<div class="row">
												<div class="col-md-12 welovez__section login-style-tab-section">
													<h3><?php _e( 'Login Button Usage Shortcodes:', 'welovez' ); ?></h3>												
													<div class="form-group">								
														<div class="row">
															<div class="col-md-6">
																<h4><?php _e( 'Icon Type:', 'welovez' ); ?></h4>
																<code>[wowonder_login button_type="icon"]</code>
															</div>
															<div class="col-md-6">
																<h4><?php _e( 'Icon Type Preview:', 'welovez' ); ?></h4>
																<?php echo $icon1; ?>
															</div>
														</div>
													</div>
													<div class="form-group">								
														<div class="row">
															<div class="col-md-6">
																<h4><?php _e( 'Wrapper Type:', 'welovez' ); ?></h4>
																<code>[wowonder_login button_type="wrapper"]</code>
															</div>
															<div class="col-md-6">
																<h4><?php _e( 'Wrapper Type Preview:', 'welovez' ); ?></h4>
																<?php echo $btwrapper; ?>
															</div>
														</div>
													</div>
													<div class="form-group">
														<br><h3><?php _e( 'Additional Shortcode Parameters:', 'welovez' ); ?></h3>
														<code>loggedin_text="<?php _e( 'You are already logged in.', 'welovez' );?>"</code>	<br><br><br>
														<h4><?php _e( 'Logged In Text Example: ', 'welovez' ); ?></h4>
														<code>[wowonder_login button_type="icon" loggedin_text="<?php _e( 'You are already logged in.', 'welovez' );?>"]</code>						
													</div>
													<br>						
												</div>
											</div>
										</div>
										<div class="single-tab" id="share-style-tab" style="display: none;">
											<div class="row">
												<div class="col-md-12 welovez__section share-style-tab-section">
													<h3><?php _e( 'Share Button Usage Shortcodes:', 'welovez' ); ?></h3>												
													<div class="form-group">								
														<div class="row">
															<div class="col-md-6">
																<h4><?php _e( 'Shortcode:', 'welovez' ); ?></h4>
																<code>[wowonder_share]</code>
															</div>
															<div class="col-md-6">
																<h4><?php _e( 'Preview:', 'welovez' ); ?></h4>
																<?php echo $icon1; ?>
															</div>									
														</div>
													</div>
													<br/>				
												</div>
											</div>
										</div>
									</div>									
								</div>
							</div>
						</div>
					</div>
					<div id="welovez_documentation" class="group" style="display: block;">
						<div class="inside">
							<div class="wrap welovez-performance">			
								<div class="tabs-holder">								
									<div class="content-tab">
										<div class="single-tab" id="documentation-tab" style="display: block;">
											<div class="row">
												<div class="col-lg-12 welovez__section app-section">
													<h3><?php _e('Create new login app at your wowonder powered website by going to developers page > create new app.<br />', 'welovez' ); ?></h3>
													<div class="form-group">		
														<span style="color:red;vertical-align: bottom;"><?php _e('Copy below callback URL and paste this to both fields "App Website" & "App callback url" by going to APP SETTINGS tanb of your wowonder login app', 'welovez'); ?></span></br>
														<code style="margin-top:2px"><?php echo site_url(); ?></code></br>					
													</div>
													<p><?php _e('See the screen shot below.', 'welovez' ); ?></p>
													<?php $icon = WELOVEZ_URL . '/assets/images/welovez_wordpress_app_integration.png'; ?>
													<img src="<?php echo $icon; ?>" style="margin-left: auto;margin-right: auto;display: block;">
														
													<h3><?php _e( 'Enter app descriptions and set app logo and hit SAVE button', 'welovez' ); ?></h3>
													<h2><?php _e( 'Now go to app Keys tab and get the keys.', 'welovez' ); ?><h2>
													<?php $icon_keys = WELOVEZ_URL . '/assets/images/app_keys.png'; ?>
													<img src="<?php echo $icon_keys; ?>" style="margin-left: auto;margin-right: auto;display: block;">
													<h2><?php _e( 'Paste the keys to this plugin APP SETUP and you are done.', 'welovez' ); ?></h2>
												</div>
											</div>
										</div>									
									</div>									
								</div>
							</div>
						</div>
					</div>
					<div id="welovez_help" class="group" style="display: block;">
						<div class="inside">
							<div class="wrap welovez-performance">			
								<div class="tabs-holder">								
									<div class="content-tab">
										<div class="single-tab" id="help-tab" style="display: block;">
											<div class="row">
												<div class="col-lg-12 welovez__section help-section">
													<h3><?php _e('Help & Support', 'welovez' ); ?></h3>
													
													<h3><?php _e('Are you looking for any kind of support?', 'welovez' ); ?></h3>
													
													<h3><?php _e( 'Please contact me by creating a support ticket at wordpress plugin page.', 'welovez' ); ?></h3>
													
													<h3><?php _e('Or you want any additional features?', 'welovez' ); ?></h3>
														
													<h3><?php _e( 'Please contact me by clicking REDLIONSTECH at wordpress plugin page.', 'welovez' ); ?></h3>
													
													<h3><?php _e( 'Or you can donate me a cup of coffee. Send me your donations.', 'welovez' ); ?><h3>
													
													<h3><?php _e( 'Thank you for using my plugin. Please <a href="https://login.wordpress.org/?redirect_to=https%3A%2F%2Fwordpress.org%2Fsupport%2Fplugin%2Fwelovez-social-login%2Freviews%2F">RATE</a> on wordpress page that helps a lot.', 'welovez' ); ?></h3>					
												</div>
											</div>
										</div>									
									</div>									
								</div>
							</div>
						</div>
					</div>	
				</div>			
			</div>
			<script>
					jQuery(document).ready(function($) {					
						$('.group').hide();
						var activetab = '';
						if (typeof(localStorage) != 'undefined' ) {
							activetab = localStorage.getItem("activetab");
						}

						//if url has section id as hash then set it as active or override the current local storage value
						if(window.location.hash){
							activetab = window.location.hash;
							if (typeof(localStorage) != 'undefined' ) {
								localStorage.setItem("activetab", activetab);
							}
						}

						if (activetab != '' && $(activetab).length ) {
							$(activetab).fadeIn();
						} else {
							$('.group:first').fadeIn();
						}
						$('.group .collapsed').each(function(){
							$(this).find('input:checked').parent().parent().parent().nextAll().each(
							function(){
								if ($(this).hasClass('last')) {
									$(this).removeClass('hidden');
									return false;
								}
								$(this).filter('.hidden').removeClass('hidden');
							});
						});

						if (activetab != '' && $(activetab + '-tab').length ) {
							$(activetab + '-tab').addClass('nav-tab-active');
						}
						else {
							$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
						}
						$('.nav-tab-wrapper a').click(function(evt) {
							$('.nav-tab-wrapper a').removeClass('nav-tab-active');
							$(this).addClass('nav-tab-active').blur();
							var clicked_group = $(this).attr('href');
							if (typeof(localStorage) != 'undefined' ) {
								localStorage.setItem("activetab", $(this).attr('href'));
							}
							$('.group').hide();
							$(clicked_group).fadeIn();
							evt.preventDefault();
						});
					});
			</script>
		<?php

	}
	/**
	 * Adds Admin Scripts for the Ajax call
	 */
	public function addAdminScripts() {
		$page = isset($_GET['page']);
		if ($page == 'welovez_login') {
			wp_register_style( 'welovez_admin_css', WELOVEZ_URL. '/assets/css/welovez-admin.css' );
			wp_enqueue_style( 'welovez_admin_css' );

			wp_enqueue_script( 'welovez-admin', WELOVEZ_URL. '/assets/js/admin.js', array(), 1.0 );

			$admin_options = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'_nonce'   => wp_create_nonce( $this->_nonce ),
			);
			wp_localize_script( 'welovez-admin', 'welovez_admin', $admin_options );
		}

	}
	/**
	 * Callback for the Ajax request
	 *
	 * Updates the Wowonder App ID and App Secret options
	 */
	public function WeLovezAdminSettings() {

		if ( wp_verify_nonce( sanitize_text_field($_POST['security']), $this->_nonce ) === false ) {
			die( 'Invalid Request!' );
		}
		$wappid = sanitize_text_field($_POST['app_id']);
		$wappsct = sanitize_text_field($_POST['app_secret']);
		$wbt = sanitize_text_field($_POST['redirect_user']);
		$wurl = esc_url(($_POST['website_url']));
			update_option( 'welovez_login', array(
				'website_url' => $wurl,
				'app_id'     => $wappid,
				'app_secret' => $wappsct,
				'redirect_user' => $wbt,
			) );
			echo _e('Settings Saved!', 'welovez');
			die();
	}

}
 
/*
 * Starts our admin class!
 */
new WeLovezAdmin();