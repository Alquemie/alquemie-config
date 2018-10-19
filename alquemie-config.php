<?php
/*
Plugin Name: Alquemie Config
Description: Wordpress Configuration Best Practices as defined by Chris Carrel
Version: 0.1.1
Author: Chris Carrel
Author URI: https://www.linkedin.com/in/chriscarrel
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

------------------------------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

if ( !defined('ABSPATH') ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

if ( ! class_exists( 'Alquemie_Config' ) ) :
	/**
	 *
	 */
	class Alquemie_Config {
        private $_settings = array();
        
		public function __construct() {
			self::includes();
			self::hooks();
		}

		private static function includes() {
            require_once dirname( __FILE__ ) . '/admin/alquemie-config-options.php';
		}

		private static function hooks() {
            if (!checked(1, get_option('alquemie-config-author-url'), false)) {
                add_action( 'template_redirect', array( __CLASS__, 'remove_author_pages_page') );
                add_filter( 'author_link', array( __CLASS__, 'remove_author_pages_link') );
            }

            if (!checked(1, get_option('alquemie-config-delay-rss'), false)) {
                add_filter('posts_where', array(__CLASS__, 'delay_rss_feed' ) );
            }
         
            if (!checked(1, get_option('alquemie-config-remove-generator'), false)) {
                // remove version from head
                remove_action('wp_head', 'wp_generator');
                // remove version from rss
                add_filter('the_generator', '__return_empty_string');
            }

            
            add_filter('admin_footer_text', array( __CLASS__, 'remove_footer_admin' )) ;
            add_filter('gettext', array(__CLASS__, 'howdy_message'), 10, 3);

		}

        public function remove_author_pages_page() {
            if ( is_author() ) {
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
            }
        }

        public function remove_author_pages_link( $content ) {
            return get_option( 'home' );
        }
    
        public function remove_footer_admin () {
            echo get_option('alquemie-config-footer-msg', 'Authorized Users Only!');
        } 
          
        private function custom_message() {
            $date = date('d-m');
            switch($date) {
                case '03-14':
                    $message = 'Happy St Patrick\'s Day';
                    break;

                case '05-05':
                    $message = 'Happy Cinco de Mayo';
                    break;
                
                default:
                    $message = get_option('alquemie-config-welcome-msg', 'Logged in as');
            }
            
            return $message;
        }
            
        public static function howdy_message($translated_text, $text, $domain) {
            $message = self::custom_message();
            $new_message = str_replace('Howdy', $message, $text);
            return $new_message;
        }
            
        //* Delay posts from appearing immediately in WordPress RSS feed
        public function delay_rss_feed($where) {
            global $wpdb;
            
            if ( is_feed() ) {
                $now = gmdate('Y-m-d H:i:s'); // Timestamp in WordPress format
                $wait = '12'; // Integer
                $device = 'HOUR'; // MINUTE, HOUR, DAY, WEEK, MONTH, YEAR
                $where.= " AND TIMESTAMPDIFF($device, $wpdb->posts.post_date_gmt, '$now') > $wait "; // Add SQL syntax to default $where
            }
            return $where;
        }

  
        
	}

   new Alquemie_Config();

endif;

