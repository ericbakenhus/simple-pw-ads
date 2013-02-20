<?php

if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

if ( ! class_exists( 'simple_pw_ads' ) ) {
	/** 
	* Main class for plugin.
	* 
	* @since 1.1.0
	* @package simple_pw_ads
	*/
	class simple_pw_ads {
		static $spw_add_script;
		
		/** 
		* Constructor.
		*
		* @since 2.0.0
		* @package simple_pw_ads
		*/
		public function __construct() {
			add_action( 'init', array( __CLASS__, 'init' ) );
			add_action( 'wp_footer', array( __CLASS__, 'print_script' ) );
			
			/* Widget */
			add_action( 'widgets_init', array( __CLASS__, 'load_widget' ) );
			
			/* Shortcode */
			add_shortcode( 'spw_ad', array( __CLASS__, 'spw_ad_shortcode_handler' ) );
		}
		
		/** 
		* 'init' hook. Only used for localization purposes now.
		* 
		* @since 1.0.0
		* @package simple_pw_ads
		*/
		public function init() {
			load_plugin_textdomain( 'spw_ads', false, plugin_dir_path( __FILE__ ) . 'lang' );
		}

		/** 
		* Print the PW JavaScript in the footer if the variable $spw_add_script is set and the asyncronous code is wanted. 'wp_footer' hook.
		* 
		* @since 1.0.0
		* @package simple_pw_ads
		*
		* @return			Returns before printing script if variable $spw_add_script is false.
		*/
		public function print_script() {
			if ( empty( self::$spw_add_script ) ) {
				self::$spw_add_script['async_printed'] = false;
				return;
			}
			
			$options = get_option( 'saved_spw_ads' );
			
			if ( $options[ 'async' ] ) {
				self::$spw_add_script['async_printed'] = true;
				?>
				<!-- Simple PW Ads Project Wonderful async code -->
				<script type="text/javascript">
					(function(){function pw_load(){
						if(arguments.callee.z)return;else arguments.callee.z=true;
						var d=document;var s=d.createElement('script');
						var x=d.getElementsByTagName('script')[0];
						s.type='text/javascript';s.async=true;
						s.src='//www.projectwonderful.com/pwa.js';
						x.parentNode.insertBefore(s,x);}
						if (window.attachEvent){
							window.attachEvent('DOMContentLoaded',pw_load);
							window.attachEvent('onload',pw_load);}
						else{
							window.addEventListener('DOMContentLoaded',pw_load,false);
							window.addEventListener('load',pw_load,false);}})();
				</script>
				<!-- End Simple PW Ads Project Wonderful async code -->
				<?php
			} else {
				self::$spw_add_script['async_printed'] = false;
			}
		}
		
		/** 
		* Register the widget. 'widgets_init' hook.
		* 
		* @since 1.0.0
		* @package simple_pw_ads
		*/
		public function load_widget() {
			register_widget( 'spw_widget' );
		}
		
		/** 
		* Shortcode handler.
		*
		* Returns the result of spw_ad_func().
		*
		* @since 1.0.0
		* @package simple_pw_ads
		*
		* @param	arr	$atts	Should only be the Project Wonderful ad number with underscores.
		* @return	str		The Project Wonderful HTML code or blank on validation error.
		*/
		public function spw_ad_shortcode_handler( $atts ) {
			$params = shortcode_atts( array( 'ad' => false, 'managed_ad' => false ), $atts );
		
			$ad = $params['ad'];
			$managed = $params['managed_ad'];
			
			if ( ! empty( $managed ) ) {
				return self::spw_ad_func( $managed );
			} elseif ( ! empty( $ad ) ) {
				return self::spw_ad_func( $ad . '_' . 1 );
			}
			
			return '<!--' . esc_html__( 'Malformed managed Simple PW Ads shortcode', 'spw_ads' ) . '-->';
		}
		
		/** 
		* Base function for retrieving the ad code.
		*
		* Tries to validate that the given number is in the proper format.
		* Sets the global variable $spw_add_script to proper values.
		* TODO: This section needs cleaning, documentation, and parting out into separate functions.
		*
		* @since 1.0.0
		* @package simple_pw_ads
		*
		* @param	str	$ad	The Project Wonderful ad number with underscores.
		* @return	str		The Project Wonderful HTML code or blank on validation error.
		*/
		public function spw_ad_func( $ad ) {
			$output = '<!--' . esc_html__( 'Simple PW Ads Error', 'spw_ads' ) . '-->';
			$options = get_option( 'saved_spw_ads' );
			
			if ( ! empty( $options['ads'] ) ) {
				$ads = $options['ads'];
			} else {
				return '<!--' . esc_html__( 'There are no ads. Check the Simple PW Ads management page.', 'spw_ads' ) . '-->';
			}
			
			$test_first = current( $ads );
			$test_end = end( $ads );
			reset( $ads );
			$using_ad_numbers = ( isset( $test_first['ad_number'] ) ) ? true : false;
			$number_ads = ( $using_ad_numbers ) ? $test_end['ad_number'] : count( $ads );
			
			if ( empty( $ad ) ) {
				/* ...wha? */
				return '<!--' . esc_html__( 'Empty call to Simple PW Ads', 'spw_ads' ) . '-->';
			} elseif ( strpos( $ad, '_' ) !== false ) { /* Does it look like legacy code? */
				/* Yes. Parse it. */
				$separate = explode( '_', $ad, 2 );
				if ( is_numeric( $separate[0] ) && strlen( $separate[0] ) <= 6 ) { /* Does it match legacy ad code? */
					/* Yes. Check if the ad exists. */
					$found = false;
					foreach( $ads as $key => $an_ad ) {
						if ( $an_ad['ad'] == $separate[0] ) {
							/* Exists. Set that ad. */
							$found = true;
							if ( $using_ad_numbers ) {
								$ad = $an_ad['ad_number'];
							} else {
								$ad = $key;
							}
						}
					}
						
					if ( ! $found ) {
						/* Doesn't exist. Print Error */
						return '<!--' . esc_html__( 'No such legacy ad', 'spw_ads' ) . '-->';
					}
				} else {
					/* Doesn't parse as legacy code. Print Error */
					return '<!--' . esc_html__( 'Malformed call to Simple PW Ads', 'spw_ads' ) . '-->';
				}
			} else { /* One number/character */
				if ( ! is_numeric( $ad ) ) {
					return '<!--' . esc_html__( 'That is not a number', 'spw_ads' ) . '-->';
				}
				
				if ( $ad > $number_ads || $ad < 1 ) {
					return '<!--' . esc_html__( 'That is not a valid ad number', 'spw_ads' ) . '-->';
				}
			}
			/* $ad is now certain to be set, and contain a single valid number */
			
			if ( $using_ad_numbers ) {
				$got_it = false;
				foreach( $ads as $an_ad ) {
					if ( $an_ad['ad_number'] == $ad ) {
						$the_ad = $an_ad;
						$got_it = true;
					}
				}
				
				if ( ! $got_it ) {
					return '<!--' . esc_html__( 'No such ad', 'spw_ads' ) . '-->';
				}				
			} else {
				$the_ad = $ads[$ad];
			}
			
			if ( $options[ 'async' ] ) {
				if ( empty( self::$spw_add_script[ $ad ] ) || intval( self::$spw_add_script[ $ad ] ) <= 3 ) {
					self::$spw_add_script[ $ad ] = ( empty( self::$spw_add_script[ $ad ] ) ) ? 1 : self::$spw_add_script[ $ad ] + 1;
					if ( $options['ad_display'] == 1 ) {
						$output = '<!-- Project Wonderful Ad Box Code --><div id="pw_adbox_' . $the_ad['ad'] . '_' . $the_ad['number'] . '_' . ( self::$spw_add_script[ $ad ] - 1 ) . '" class="spw_ad"></div><!-- End Project Wonderful Ad Box Code -->';
					} else {
						$output = '<!-- Project Wonderful Ad Box Code --><div id="pw_adbox_' . $the_ad['ad'] . '_' . $the_ad['number'] . '_' . ( self::$spw_add_script[ $ad ] - 1 ) . '" class="spw_ad"></div>' . $the_ad['separate_advanced'] . '<!-- End Project Wonderful Ad Box Code -->';
					}
				} else {
					$output = '<!--' . esc_html__( 'Too many PW ads', 'spw_ads' ) . '-->';
				}
			} else {
				if ( empty( self::$spw_add_script ) || ! in_array( $ad, self::$spw_add_script ) ) {
					if ( $options['ad_display'] == 1 ) {
						$output = $the_ad['standard'];
					} else {
						$output = $the_ad['advancedcode'];
					}
					array_push( self::$spw_add_script, $ad );
				} else {
					$output = '<!--' . esc_html__( 'Cannot call same ad twice if not async', 'spw_ads' ) . '-->';
				}
			}
			
			return $output;
		}
		
		/** 
		* Check if the Project Wonderful async code has been printed in the footer.
		*
		* Only use this with or after the wp_footer hook. If used with wp_footer, set priority to anything greater than 10.
		* Check if $options['async'] is "1" in the option "saved_spw_ads" if you need to determine this before wp_footer.
		*
		* @since 3.0.0
		* @package simple_pw_ads
		*
		* @return	bool	If the asynchronous code has been printed in the footer.
		*/
		public function is_async_printed() {
			return self::$spw_add_script['async_printed'];
		}
		
	} /* simple_pw_ads class */
	
} /* if statement - class exists */