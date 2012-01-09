<?php
/*
Plugin Name: Simple PW Ads
Plugin URI: http://interruptedreality.com/plugins/simple-pw-ads/
Description: Adds easy ways to insert Project Wonderful ads into your site.
Version: 3.0.2
Author: Big Bagel
Author URI: http://interruptedreality.com
License: GPL2
	
Copyright 2011  Eric B.  (email : bigbagel@interruptedreality.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
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
		function __construct() {
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
		function init() {
			load_plugin_textdomain( 'spw_ads', false, plugin_dir_path( __FILE__ ) . 'spw-lang' );
		}

		/** 
		* Print the PW JavaScript in the footer if the variable $spw_add_script is set and the asyncronous code is wanted. 'wp_footer' hook.
		* 
		* @since 1.0.0
		* @package simple_pw_ads
		*
		* @return			Returns before printing script if variable $spw_add_script is false.
		*/
		function print_script() {
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
		function load_widget() {
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
		function spw_ad_shortcode_handler( $atts ) {
			global $spw_ads_instance;
			$params = shortcode_atts( array( 'ad' => false, 'managed_ad' => false ), $atts );
		
			$ad = $params['ad'];
			$managed = $params['managed_ad'];
			
			if ( ! empty( $managed ) ) {
				return $spw_ads_instance->spw_ad_func( $managed );
			} elseif ( ! empty( $ad ) ) {
				return $spw_ads_instance->spw_ad_func( $ad . '_' . 1 );
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
					self::$spw_add_script[] = $ad;
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

if ( class_exists( 'simple_pw_ads' ) ) {
		global $spw_ads_instance;
	
		$spw_ads_instance = new simple_pw_ads();
	
		if ( is_admin() && isset( $spw_ads_instance ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'spw-includes/admin.php' );
		
			$spw_admin_instance = new simple_pw_admin();
		}
}


/** 
* Class for widget.
* 
* @since 1.0.0
* @package simple_pw_ads
*/
class spw_widget extends WP_Widget {
	
	/* constructor */
	function spw_widget() {
		$widget_ops = array( 'classname'=>'spw_widget', 'description'=>'Display a Project Wonderful ad' );
		
		$this->WP_Widget( 'spw_widget', 'Simple PW Ad', $widget_ops );
	}
	
	/* widget output */
	function widget( $args, $instance ) {
		global $spw_ads_instance;
		extract( $args );
			
		$title = apply_filters( 'widget_title', $instance['title'] );
		$ad = $instance['managed_ad'];
			
		echo $before_widget;
			
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		if ( empty( $ad ) ) {
			echo '<!--' . esc_html__( 'Simple PW widget set to display nothing', 'spw_ads' ) . '-->';
		} else {
			echo $spw_ads_instance->spw_ad_func( $ad );
		}
			
		echo $after_widget;
	}
		
	/* widget options update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
			
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['managed_ad'] = ( isset( $new_instance['managed_ad'] ) && strlen( $new_instance['managed_ad'] ) == 1 && is_numeric( $new_instance['managed_ad'] ) ) ? $new_instance['managed_ad'] : '';
		
		return $instance;
	}
		
	/* widget control form */
	function form( $instance ) {
		$defaults = array( 'title' => '', 'managed_ad' => '');
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$managed_ad = $instance['managed_ad'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'spw_ads' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" type="text" style="width:100%;" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'managed_ad' ); ?>"><?php esc_html_e( 'Your ad:', 'spw_ads' ); ?>:</label><br />
		<?php 
		$options = get_option( 'saved_spw_ads' );
		if ( !empty( $options['ads'] ) && count( $options['ads'] ) > 0 ) { 
		?>
			<select name="<?php echo $this->get_field_name( 'managed_ad' ); ?>">
				<option value="" <?php selected( $managed_ad, '' ); ?>>Show nothing.</option>
				<?php foreach( $options['ads'] as $count => $option ) { ?>
				<?php $ad_number = ( isset( $option['ad_number'] ) ) ? $option['ad_number'] : $count; ?>
				<option value="<?php echo $ad_number; ?>" <?php selected( $managed_ad, $ad_number ); ?>"><?php echo 'Ad ' . $ad_number . ' - ' . $option['ad'] . ' - ' . $option['type']; ?></option>
				<?php } ?>
			</select>
		<?php } else { ?>
			<span><a href="<?php echo admin_url( 'options-general.php?page=simple_pw_ads' ); ?>"><?php esc_html_e( 'No ads. Please see the management page.', 'spw_ads' ); ?></a></span>
		<?php } ?>
		</p>
		<?php
	}
}


/** 
* Template tag to show a project wonderful ad.
*
* Echos the result of spw_ad_func().
*
* @since 1.0.0
* @package simple_pw_ads
*
* @param	str	$ad	The Project Wonderful ad number with underscores.
*/
if ( ! function_exists( 'spw_insert_ad' ) ) {
	function spw_insert_ad( $ad ) {
		echo $spw_ads_instance->spw_ad_func( $ad );
	}
}
?>