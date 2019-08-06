<?php

/**
 * isosec-logg
 *
 * @package           isosec
 * @author            Pål Bergquist
 * @copyright         2019 Pål Bergquist
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       isosec_logg
 * Plugin URI:        https://isosec.no/isosec-logg
 * Description:       This will logg all incoming request to your wordpress site
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Pål Bergquist
 * Author URI:        https://isosec.no
 * Text Domain:       isosec
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

class isosec_logg
{

    public static function register_activations()
    {
        register_activation_hook(__FILE__, 'isosec_logg::isosec_activation');
        register_deactivation_hook(__FILE__, 'isosec_logg::isosec_deactivation');
    }

    public static function isosec_add_action()
    {
        //add_action('init', 'isosec_logg::isosec_request');
        add_shortcode('isosec', 'isosec_logg::isosec_shortcode');
    }

    public static function isosec_activation()
    {
        error_log("Jeg har blitt aktivisert, file=" . __FILE__);
    }
    public static function isosec_deactivation()
    {
        error_log("Jeg har blitt deaktivisert");
    }

    public static function isosec_request()
    {
        // add_shortcode('isosec', 'isosec_logg::isosec_shortcode');
        if (count($_GET, COUNT_RECURSIVE) > 0) {
            error_log("Dette var en get");
            foreach ($_GET as $key => $value) {
                error_log($key . "=" . $value);
            }
            return;
        }
        if (count($_POST, COUNT_RECURSIVE) > 0) {
            error_log("Dette var en post");
            foreach ($_POST as $key => $value) {
                error_log($key . "=" . $value);
            }
            return;
        }
        error_log("Dette var en " . $_SERVER['REQUEST_METHOD']);
    }

    public static function isosec_shortcode($attr)
    {
        $obj = new isosec_logg();
        return $obj->isosec_action($attr);
    }
    public function isosec_action($attr)
    {
        $page = "";
        $html = 0;
        $mydir = dirname(__FILE__);
        require_once($mydir . '/classes/itsw-html.php');
        $atts = array_change_key_case((array) $attr, CASE_LOWER);
        try {
            $html = new ITSW_html();
            $page = $html->getTemplate($mydir . "/html/isosec-logg.html", "main");
        } catch (Exception $e) {
            $page .= "Error " . $e->getMessage() . "<br>";
        }

        date_default_timezone_set('Europe/Oslo');
        $date = date('Y-m-d H:i:s');

        $page .= "<p>" . $date . "</p>";

        $tz = new DateTimeZone(get_option('timezone_string'));
        $dt = new DateTime("now", $tz);

        $page .= "<p> DateTime " . $dt->format("Y-m-d H:i:s") . "</p>";

        $page .= "<p> timezone " . get_option('timezone_string') . "</p>";

        return $page;
    }
}

isosec_logg::register_activations();
isosec_logg::isosec_add_action();
