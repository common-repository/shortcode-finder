<?php
/**
 * Plugin Name: Shortcode Finder
 * Plugin URI: https://github.com/MediaSoil/Shortcode-Finder
 * Description: Displays a list of shortcodes available for use.
 * Version: 0.1
 * Author: Media Soil
 * Author URI: http://www.mediasoil.com
 * License: GPLv2
*/
 
class MS_shortcode
{

        /**
         * @name PLUGIN_SLUG
         * @var const
         */
        const PLUGIN_SLUG = 'shortcode-finder';
        /**
         * @access public
         */
        public $popupID = 'mssc-popup';
        public $shortcodes = array();
        
        /**
         * Constructor for MS_shortcode class.
         */
        public function __construct() {
                // Wordpress Hooks
                add_action('init', array($this, 'plugin_init'));
        }
        
        /**
         * Runs when plugin is initialized.
         */
        public function plugin_init() {
                add_action('media_buttons_context', array($this, 'post_add_shortcode_button'));
                add_action('admin_menu', array($this, 'menu_add'));
                add_action('admin_enqueue_scripts', array($this, 'load_admin_assets'));
                add_action('admin_footer-post.php', array($this, 'render_shortcode_popup'));
                add_action('admin_footer-post-new.php', array($this, 'render_shortcode_popup'));
                add_action('sidebar_admin_setup', array($this, 'widget_add_shortcode_button'));
                add_action('wp_ajax_get_shortcodes', array($this, 'get_shortcodes_ajax'));
        }

        /**
         * Enqueues the admin CSS file.
         * @param string $hook
         *   Passes the name of the page that the user is on.
         */
        public function load_admin_assets($hook) {

                wp_enqueue_script('mssc-js', plugins_url().'/'.self::PLUGIN_SLUG.'/js/shortcode.js', array('jquery'));
                wp_enqueue_style('mssc-tools-css', plugins_url().'/'.self::PLUGIN_SLUG.'/css/tools.css');
                
                if ($hook == 'post.php' || $hook == 'post-new.php') { 
                        wp_enqueue_style('mssc-post-css', plugins_url().'/'.self::PLUGIN_SLUG.'/css/post.css'); 
                        wp_enqueue_script('mssc-post-js', plugins_url().'/'.self::PLUGIN_SLUG.'/js/shortcode-popup.js', array('jquery', 'mssc-js'));
                }
        }
        
        /**
         * Adds the 'Shortcode' link to the Tools menu.
         */
        public function menu_add() {
                add_submenu_page('tools.php', 'Shortcodes', 'Shortcodes', 'manage_options', 'shortcode-finder', array($this, 'render_menu'));
        }
        
        /**
         * Retrieves all shortcodes and information about them.
         */
        public function get_shortcodes() {
                //Add any other shortcodes
                global $shortcode_tags;
                $codes = array();
                $DS = DIRECTORY_SEPARATOR;

                foreach($shortcode_tags as $codename => $func) {
                        $code = array();

                        if(is_string($func)) {
                                $reflection = new ReflectionFunction($func);
                        } else if (is_array($func)) {
                                $reflection = new ReflectionMethod($func[0], $func[1]);
                        }
                        
                        $code['name'] = $codename;
                        $funcName = $reflection->getName();
                                $code['function_name'] = $funcName;
                        $funcFileName = $reflection->getFileName();
                                $code['filename'] = $funcFileName;
                        if(stripos($funcFileName, 'wp-content'.$DS.'plugins') !== false) {
                                $code['type'] = 'plugin';
                                $code['details'] = $this->get_plugin_data($funcFileName);
                        } else if(stripos($funcFileName, 'wp-content'.$DS.'themes') !== false) {
                                $code['type'] = 'theme';
                                $code['details'] = array(
                                     'Name' => wp_get_theme()->get('Name'),
                                     'ThemeURI' => wp_get_theme()->get('ThemeURI'),
                                     'Description' => wp_get_theme()->get('Description'),
                                     'Author' => wp_get_theme()->get('Author'),
                                     'AuthorURI' => wp_get_theme()->get('AuthorURI'),
                                     'Version' => wp_get_theme()->get('Version'),
                                     'TextDomain' => wp_get_theme()->get('TextDomain'),
                                     'DomainPath' => wp_get_theme()->get('DomainPath')
                                 );
                        } else {
                                $code['type'] = 'native';
                                $code['details'] = array();
                        }

                        $funcDefinition = $this->function_to_string($func);
                        $funcParams = $reflection->getParameters();
                        $funcAttrParam = $funcParams[0]->name;
                        
                        //Literal match based on array name
                        $regex = '|'.$funcAttrParam.'\[[\'\"](.+?)[\'\"]\]|';
                        preg_match_all($regex, $funcDefinition, $lmatches, PREG_PATTERN_ORDER);
                        
                        //Array based match based on shortcode_atts func
                        $regex = '|shortcode_atts\s*\(\s*array\s*\(([\s\S]+?);|';
                        preg_match_all($regex, $funcDefinition, $smatches, PREG_PATTERN_ORDER);
                        foreach($smatches[1] as $sm) {
                                $regex = '|[\'\"](.+?)[\'\"]\s*=>|';
                                preg_match_all($regex, $sm, $smatches, PREG_PATTERN_ORDER);
                        }
                        
                        //Array based match based on shortcode_atts reference array
                        $regex = '|shortcode_atts\s*\(\s*\$(.+?),|';
                        preg_match_all($regex, $funcDefinition, $rmatches, PREG_PATTERN_ORDER);
                        foreach($rmatches[1] as $rm) {
                                $regex = '|\$'.$rm.'\s*=\s*array\(([\s\S]+?);|';
                                preg_match_all($regex, $funcDefinition, $rmatches, PREG_PATTERN_ORDER);
                        }
                        foreach($rmatches[1] as $rm) {
                                $regex = '|[\'\"](.+?)[\'\"]\s*=>|';
                                preg_match_all($regex, $rm, $rmatches, PREG_PATTERN_ORDER);
                        }

                        $code['params'] = array_unique(array_merge($lmatches[1], $smatches[1], $rmatches[1]));

                        $codes[] = $code;
                }
                
                return $codes;
        }

        /**
         * Get server side shortcodes via AJAX
         */
        public function get_shortcodes_ajax() {
                echo json_encode($this->get_shortcodes());
                die();
        }
        
        /**
         * Arrange all shortcodes by function.
         * @param array $shortcodes
         *   This contains all shortcodes to sort.  These are typically retrieved with get_shortcodes.
         * @return array $shortcode_array
         *   This contains all shortcodes, but now arranged according to function.
         */
        protected function sort_shortcodes_by_function($shortcodes) {
            foreach($shortcodes as $shortcode) {
                switch ($shortcode['type']) {
                    case 'native':
                        $shortcode_array['Native WordPress'][] = $shortcode;
                        break;
                    case 'plugin':
                    case 'theme':
                        $name = $shortcode['details']['Name'];
                        
                        //Sometimes details aren't provided
                        if (!$name) {
                            $name = $shortcode['name'];
                        }

                        $shortcode_array[$shortcode['details']['Name']][] = $shortcode;
                        break;
                    default:
                        $shortcode_array['Misc'][] = $shortcode;
                        break;
                }
            }
            return $shortcode_array;
        }
        /**
         * Converts plugin functions into strings (actual shortcode value).
         * @param mixed $fun
         *   This expects a shortcode from the global array of shortcodes from Wordpress.
         * @return string $def
         *   This returns the name of the function, or in our case, the name of the shortcode.
         */
        private function function_to_string($func) {
                if (is_array($func)) {
                        $rf = is_object($func[0]) ? new ReflectionObject($func[0]) : new ReflectionClass($func[0]);
                        $rf = $rf->getMethod($func[1]);
                } else {
                        $rf = new ReflectionFunction($func);
                }

                $c = file($rf->getFileName());
                $def = '';
                for($i = $rf->getStartLine(); $i <= $rf->getEndLine(); $i++) {
                        $def .= sprintf('%s', $c[$i-1]);
                }
                
                return $def;
        }
        
        /**
         * Adds the 'Shortcode' link to the Tools menu.
         */
        public function render_menu() { 
                require 'includes/shortcode-admin.php';
        }
        
        /**
         * Adds the 'Shortcode' button above the TinyMCE Editor.
         */
        public function post_add_shortcode_button() {
                
                // Popup Variablese
                $pTitle = 'Add Shortcode';

                $buttonMarkup = '
                        <a href="#TB_inline?width=400&inlineId='.$this->popupID.'&class=testest" id="mssc-button" class="button mssc-button thickbox" title="'.$pTitle.'">
                                <span class="mssc-button-icon"></span>
                                 Add Shortcode
                        </a>
                ';
                        
                return $buttonMarkup;
        }
        
        /**
         * Adds the 'Shortcode' button into each widget.
         */
        public function widget_add_shortcode_button() {
                
                global $wp_registered_widgets, $wp_registered_widget_controls;
                
                foreach ($wp_registered_widgets as $key => $w) {
                        if ($wp_registered_widget_controls[$key]['name'] == 'Text') { $wp_registered_widget_controls[$key]['callback'] = 'widget_render_shortcode'; }
                }
        }
        
                /**
                 * Adds the 'Shortcode' button into each widget.
                 */
                public function widget_render_shortcode() {
                        
                        $pTitle = 'Add Shortcode';
                        
                        $buttonMarkup = '
                        <a href="#TB_inline?width=400&inlineId='.$this->popupID.'&class=testest" id="mssc-button" class="thickbox" title="'.$pTitle.'">
                                 Add Shortcode
                        </a>';
                        
                        echo $buttonMarkup;
                }
        
        /**
         * Renders the popup for the 'Shortcodes' button.
         */
        public function render_shortcode_popup() {
                require 'includes/shortcode-popup.php';
        }

        //Copied from WP. This method is not allowed on the front end.
        private function get_plugin_data($plugin_file) {
        $default_headers = array(
                'Name' => 'Plugin Name',
                'PluginURI' => 'Plugin URI',
                'Version' => 'Version',
                'Description' => 'Description',
                'Author' => 'Author',
                'AuthorURI' => 'Author URI',
                'TextDomain' => 'Text Domain',
                'DomainPath' => 'Domain Path'
        );
        $plugin_data = get_file_data($plugin_file, $default_headers, 'plugin');

        $plugin_data['Title']      = $plugin_data['Name'];
        $plugin_data['AuthorName'] = $plugin_data['Author'];

        return $plugin_data;
        }
        
}

$ms_shortcode = new MS_shortcode();
