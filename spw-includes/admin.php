<?php
if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

class simple_pw_admin {
	/** 
	* Constructor.
	*
	* @since 2.0.0
	* @package simple_pw_ads
	*/
	function __construct() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'add_admin_init' ) );
	}
	
	/** 
	* Add options page. 'admin_menu' hook.
	*
	* @since 2.0.0
	* @package simple_pw_admin
	*/
	function add_admin_page() {
		add_options_page( 'Simple PW Ads', 'Simple PW Ads', 'manage_options', 'simple_pw_ads', array( __CLASS__, 'options_page' ) );
	}
	
	/** 
	* Register settings. 'admin_init' hook.
	*
	* @since 2.0.0
	* @package simple_pw_admin
	*/
	function add_admin_init() {
		register_setting( 'saved_spw_ads', 'saved_spw_ads', array( __CLASS__, 'validate_ads' ) );
	}
	
	/** 
	* Display the options page.
	*
	* @since 2.0.0
	* @package simple_pw_admin
	*/
	function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to access that page.', 'Get off my lawn!' );
		}
		
		if ( ! defined( 'SPW_DISABLE_REQUIREMENT_CHECK' ) && ! extension_loaded( 'SimpleXML' ) ) {
			$req_errors[] = '<div class="error"><p>REQUIREMENT CHECK: Your host has disabled the SimpleXML extention, a requirement of Simple PW Ads. It is enabled by default in PHP version 5.1.2 and later. Contact your host to have them re-enable it.</p></div>';
		}
		
		if ( ! defined( 'SPW_DISABLE_REQUIREMENT_CHECK' ) && version_compare( get_bloginfo( 'version' ), '3.2' ) <= 0 ) {
			$req_errors[] = '<div class="error"><p>REQUIREMENT CHECK: Your version of WordPress is below 3.2. Simple PW Ads may not operate properly. Upgrade to the latest version of WordPress before using Simple PW Ads.</p></div>';
		}
		
		$options = get_option( 'saved_spw_ads' );
		
		$ad_display = ( isset( $options['ad_display'] ) ) ? $options['ad_display'] : 1;
		$async = ( isset( $options['async'] ) ) ? $options['async'] : 1;
		$pub_id = ( isset( $options['pub_id'] ) ) ? $options['pub_id'] : '';
		
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php esc_html_e( 'Simple PW Ads', 'spw_ads' ); ?></h2>
			<?php
			if ( isset( $req_errors ) ) { 
				foreach( $req_errors as $error ) {
					echo $error;
				}
			}
			?>
			<?php if ( ! empty( $options['error'] ) ) { ?>
				<div id="message" class="error"><p><?php echo esc_html( $options['error'] ); ?></p></div>
			<?php } ?>
			<p><?php esc_html_e( 'Insert your publisher ID/member number. You can find this in your Project Wonderful account by selecting "My account > My profile" and looking directly below your member name: "Member XXXX". Simple PW Ads will download and manage all your ads.', 'spw_ads' ); ?></p>
			<form action="options.php" method="post">
				
				<?php settings_fields( 'saved_spw_ads' ); ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Publisher ID', 'spw_ads' ); ?>:</th>
							<td>
								<input name="saved_spw_ads[pub_id]" size="5" maxlength="5" type="text" value="<?php echo $pub_id; ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Display ads using', 'spw_ads' ); ?>:</th>
							<td>
								<select name="saved_spw_ads[ad_display]">
									<option value="1" <?php selected( $ad_display, 1 ); ?>><?php esc_html_e( 'Standard Code (will not show without JavaScript)', 'spw_ads' ); ?></option>
									<option value="2" <?php selected( $ad_display, 2 ); ?>><?php esc_html_e( 'Advanced Code (will show without JavaScript)', 'spw_ads' ); ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Use asynchronous code (recommended)', 'spw_ads' ); ?>:</th>
							<td>
								<input type="checkbox" name="saved_spw_ads[async]" value="1" <?php checked( $async, 1 ); ?> />
							</td>
						</tr>
						<?php if ( ! empty( $options['last_sync'] ) ) { ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Last successful sync', 'spw_ads' ); ?>:</th>
							<td>
								<?php echo $options['last_sync']; ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<br /><br />
				<?php if ( ! empty( $options['pub_id'] ) && ! empty( $options['ads'] ) ) { ?>
				<table class="widefat">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Ad Number', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Ad ID', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Type Number', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Type', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Dimensions', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Rating', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Category', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Site Name', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'URL', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'PW Page', 'spw_ads' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php esc_html_e( 'Ad Number', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Ad ID', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Type Number', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Type', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Dimensions', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Rating', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Category', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'Site Name', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'URL', 'spw_ads' ); ?></th>
							<th><?php esc_html_e( 'PW Page', 'spw_ads' ); ?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php 
							foreach( $options['ads'] as $count => $ad ) {
								$ad_number = ( isset( $ad['ad_number'] ) ) ? $ad['ad_number'] : $count;
								echo '<tr><td>' . $ad_number . '</td><td>' . $ad['ad'] . '</td><td>' . $ad['number'] . '</td><td>' . $ad['type'] . '</td><td>' . $ad['dimensions'] . '</td><td>' . $ad['rating'] . '</td><td>' . $ad['category'] . '</td><td>' . $ad['sitename'] . '</td><td><a href="' . $ad['url'] . '" target="_blank">' . $ad['url'] . '</a></td><td><a href="https://www.projectwonderful.com/advertisehere.php?id=' . $ad['ad'] . '" target="_blank">PW Page</a></td></tr>';
							} 
						?>
					</tbody>
				</table>
				<?php } ?>
				
				<p class="submit">
					<input name="Submit" type="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Synchronize Ad Data', 'spw_ads'); ?>" />
				</p>
			</form>
		</div>
		<?php
	}
	
	/** 
	* Download and save ad data.
	*
	* @since 3.0.0
	* @package simple_pw_admin
	*
	* @param	str	$pub_id	The Project Wonderful publisher ID.
	* @return	array	Returns and array containing all the values downloaded from Project Wonderful or a smaller array with the index ['error'] set on error.
	*/
	function set_ad_data( $pub_id, $old_options = false ) {
		$options['pub_id'] = $pub_id;
		$options['error'] = false;
		
		if ( $old_options['pub_id'] == $pub_id && ! empty( $old_options ) ) {
			$end_ad = end( $old_options['ads'] );
			$end = ( isset( $end_ad['ad_number'] ) ) ? $end_ad['ad_number'] : 0;
			reset( $old_options['ads'] );
		} else {
			$end = 0;
		}
		
		/* Connect to Project Wonderful and get the Ad data */
		$pw_raw_xml = wp_remote_get( 'http://www.projectwonderful.com/xmlpublisherdata.php?publisher=' . $options['pub_id'] );
		
		/* Some sort of error retireving data */
		if ( is_wp_error( $pw_raw_xml ) ) {
			$options['error'] = 'ERROR: ' . $pw_raw_xml->get_error_message() . ' | Ad data not updated.';
			return $options;
		}
		
		/* Returned something other than 200 */
		if ( $pw_raw_xml['response']['code'] != 200 ) {
			$options['error'] = 'ERROR: Project Wonderful returned a ' . $pw_raw_xml['response']['code'] . ' - ' . $pw_raw_xml['response']['message'] . '. | Ad data not updated.';
			return $options;
		}
		
		/* Check for SimpleXML again */
		if ( extension_loaded( 'SimpleXML' ) ) {
			$pw_xml = new SimpleXMLElement( $pw_raw_xml['body'], null, false, 'http://www.projectwonderful.com/' );
		} else {
			$options['error'] = __( 'The SimpleXML PHP extention has been disabled. It is enabled by default in PHP version 5.1.2 and later. Ask your host to enabled it. | Ad data not updated.', 'spw_ads' );
			return $options;
		}
		
		if ( $pw_xml ) { /* Got and parsed the XML from PW. Time to break it down and save it. */
			$count = 1;
			
			/* Go through each adbox */
			foreach( $pw_xml->adboxes->adbox as $adbox ) {
				$atts = $adbox->attributes();
				/* Save adbox ID */
				$options['ads'][$count]['ad'] = intval( $atts['adboxid'] );
				
				$match = false;
				foreach( $old_options['ads'] as $key => $old_ad ) {
					if ( isset( $old_ad['ad_number'] ) && $old_ad['ad'] == $options['ads'][$count]['ad'] ) {
						$options['ads'][$count]['ad_number'] = $old_ad['ad_number'];
						$match = true;
					}
				}
				
				if ( ! $match ) {
					$options['ads'][$count]['ad_number'] = $end + 1;
					$end++;
				}
				
				/* Save Ad type and type number */
				switch ( (string) $atts['type'] ) {
					case 'button':
						$options['ads'][$count]['number'] = 2;
						$options['ads'][$count]['type'] = 'Button';
						break;
					case 'square':
						$options['ads'][$count]['number'] = 4;
						$options['ads'][$count]['type'] = 'Square';
						break;
					case 'half banner':
						$options['ads'][$count]['number'] = 6;
						$options['ads'][$count]['type'] = 'Half Banner';
						break;
					case 'banner':
						$options['ads'][$count]['number'] = 1;
						$options['ads'][$count]['type'] = 'Banner';
						break;
					case 'rectangle':
						$options['ads'][$count]['number'] = 7;
						$options['ads'][$count]['type'] = 'Rectangle';
						break;
					case 'leaderboard':
						$options['ads'][$count]['number'] = 5;
						$options['ads'][$count]['type'] = 'Leaderboard';
						break;
					case 'skyscraper':
						$options['ads'][$count]['number'] = 3;
						$options['ads'][$count]['type'] = 'Skyscraper';
						break;
				}
				
				/* Save the adox site name, site url, dimentions, rating, and category */
				$options['ads'][$count]['sitename'] = (string) $atts['sitename'];
				$options['ads'][$count]['url'] = (string) $atts['url'];
				$options['ads'][$count]['dimensions'] = (string) $atts['dimensions'];
				$options['ads'][$count]['rating'] = (string) $atts['rating'];
				$options['ads'][$count]['category'] = (string) $atts['category'];
				
				/* Save the adbox tags, standard code, and advanced code */
				$options['ads'][$count]['tags'] = (string) $adbox->tags;
				$options['ads'][$count]['standard'] = (string) $adbox->standardcode;
				$options['ads'][$count]['advancedcode'] = (string) $adbox->advancedcode;
				
				/* Pull out only the <noscript> part of the advanced code for use with asynchronous advanced code */
				$explode = explode( '<noscript>', (string) $options['ads'][$count]['advancedcode'] );
				$explode2 = explode( '</noscript>', $explode[1] );
				$options['ads'][$count]['separate_advanced'] = '<script type="text/javascript"></script><noscript>' . $explode2[0] . '</noscript>';
				
				$count++;
			}
			
			/* No ads for this account */
			if ( $count == 1 ) {
				$options['error'] = __( 'ERROR: No ads found for that publisher ID/member number.', 'spw_ads' );
			}
			
			/* Set the last sync time to right now */
			$options['last_sync'] = current_time( 'mysql' );
		} else { /* Problem parsing the XML */
			$options['error'] = __( 'ERROR: Could not parse xml. | Ad data not updated.', 'spw_ads' );
		}
		
		return $options;
	}
		
	/** 
	* Validates user's input for options page.
	*
	* @since 2.0.0
	* @package simple_pw_admin
	*
	* @param	str	$input	User input.
	* @return	str		Validated input.
	*/
	function validate_ads( $input ) {
		$options = get_option( 'saved_spw_ads' );
		
		if ( empty( $options ) ) {
			$options = array();
		}
		
		if ( empty( $input['pub_id'] ) ) {
			$newinput['pub_id'] = '';
		} else {
			$newinput = $options;
		}
		
		if ( ! empty( $input['pub_id'] ) && strlen( $input['pub_id'] ) <= 5 && is_numeric( $input['pub_id'] ) ) {
			$temp = self::set_ad_data( $input['pub_id'], $options );
			
			if ( $temp['error'] ) {
				$newinput['error'] = $temp['error'];
			} else {
				$newinput = array_merge( $options, $temp );
			}
		}
		
		$newinput['ad_display'] = ( $input['ad_display'] == 1 || $input['ad_display'] == 2 ) ? $input['ad_display'] : 1;
		$newinput['async'] = ( isset( $input['async'] ) && $input['async'] == 1 ) ? 1 : 0;
			
		return $newinput;
	}
}
?>