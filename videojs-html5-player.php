<?php
/*
  Plugin Name: Videojs HTML5 Player
  Version: 1.0.1
  Plugin URI: http://wphowto.net/videojs-html5-player-for-wordpress-757
  Author: naa986
  Author URI: http://wphowto.net/
  Description: Easily embed videos using videojs html5 player
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('VIDEOJS_HTML5_PLAYER')) {

    class VIDEOJS_HTML5_PLAYER {

        var $plugin_version = '1.0.1';

        function __construct() {
            define('VIDEOJS_HTML5_PLAYER_VERSION', $this->plugin_version);
            $this->plugin_includes();
        }

        function plugin_includes() {
            if (is_admin()) {
                add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
            }
            add_action('wp_enqueue_scripts', 'videojs_html5_player_enqueue_scripts');
            add_action('admin_menu', array($this, 'add_options_menu'));
            add_action('wp_head', 'videojs_html5_player_header');
            add_shortcode('videojs_video', 'videojs_html5_video_embed_handler');
            //allows shortcode execution in the widget, excerpt and content
            add_filter('widget_text', 'do_shortcode');
            add_filter('the_excerpt', 'do_shortcode', 11);
            add_filter('the_content', 'do_shortcode', 11);
        }

        function plugin_url() {
            if ($this->plugin_url)
                return $this->plugin_url;
            return $this->plugin_url = plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__));
        }

        function plugin_action_links($links, $file) {
            if ($file == plugin_basename(dirname(__FILE__) . '/videojs-html5-player.php')) {
                $links[] = '<a href="options-general.php?page=videojs-html5-player-settings">Settings</a>';
            }
            return $links;
        }

        function add_options_menu() {
            if (is_admin()) {
                add_options_page('Videojs Settings', 'Videojs HTML5 Player', 'manage_options', 'videojs-html5-player-settings', array($this, 'options_page'));
            }
        }

        function options_page() {
            ?>
            <div class="wrap">
                <div id="poststuff"><div id="post-body">

                        <h2>Videojs HTML5 Player - v<?php echo $this->plugin_version; ?></h2>
                        <div class="postbox">
                            <h3><label for="title">Setup Guide</label></h3>
                            <div class="inside">		
                                <p>For detailed documentation please visit the plugin homepage <a href="http://wphowto.net/videojs-html5-player-for-wordpress-757" target="_blank">here</a></p>
                            </div></div>

                    </div></div>
            </div>
            <?php
        }

    }

    $GLOBALS['easy_video_player'] = new VIDEOJS_HTML5_PLAYER();
}

function videojs_html5_player_enqueue_scripts() {
    if (!is_admin()) {
        $plugin_url = plugins_url('', __FILE__);
        wp_register_script('videojs', $plugin_url . '/videojs/video.js');
        wp_enqueue_script('videojs');
        wp_register_style('videojs', $plugin_url . '/videojs/video-js.css');
        wp_enqueue_style('videojs');
        wp_register_style('videojs-style', $plugin_url . '/videojs-html5-player.css');
        wp_enqueue_style('videojs-style');
    }
}

function videojs_html5_player_header() {
    if (!is_admin()) {
        $config = '<!-- This site is embedding videos using the Videojs HTML5 Player plugin v' . VIDEOJS_HTML5_PLAYER_VERSION . ' - http://wphowto.net/videojs-html5-player-for-wordpress-757 -->';
        echo $config;
    }
}

function videojs_html5_video_embed_handler($atts) {
    extract(shortcode_atts(array(
        'url' => '',
        'width' => '',
        'autoplay' => 'false',
        'poster' => '',
        'class' => '',
    ), $atts));
    if ($autoplay == "true") {
        $autoplay = " autoplay";
    } else {
        $autoplay = "";
    }
    if(!empty($poster)) {
        $poster = ' poster="'.$poster.'"';
    }
    $container = "videocontent" . uniqid();
    $player = "videojs" . uniqid();
    $output = <<<EOT
    <div id="$container">
    <video id="$player" class="video-js vjs-default-skin" controls preload="auto" width="auto" height="auto"{$poster}{$autoplay} data-setup='{}'>
        <source src="$url" type='video/mp4'>
    </video>
    </div>
    <style>
    #$container {
        width:100%; 
        max-width:{$width}px;
    }
    </style>
EOT;
    return $output;
}
