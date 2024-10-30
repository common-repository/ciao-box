<?php
/*
Plugin Name: Ciao Box
Plugin URI: http://pab-media.de/ciao-box
Description: Displays a Ciao Box on the sidebar.
Author: Patrick Brückner
Version: 1.31
Author URI: http://pab-media.de/
License: GPL 2.0, @see http://www.gnu.org/licenses/gpl-2.0.html
*/

class ciao_box {

    function init() {
    	// check for the required WP functions, die silently for pre-2.2 WP.
    	if (!function_exists('wp_register_sidebar_widget'))
    		return;
    		
    	// load all l10n string upon entry
        load_plugin_textdomain('ciao_box');
        
        // let WP know of this plugin's widget view entry
    	wp_register_sidebar_widget('ciao_box', __('Ciao Box', 'ciao_box'), array('ciao_box', 'widget'),
            array(
            	'classname' => 'ciao_box',
            	'description' => __('Displays a Ciao Box on the sidebar.', 'ciao_box')
            )
        );
    
        // let WP know of this widget's controller entry
    	wp_register_widget_control('ciao_box', __('Ciao Box', 'ciao_box'), array('ciao_box', 'control'),
    	    array('width' => 300)
        );

        // short code allows insertion of ciao_box into regular posts as a [ciao_box] tag. 
        // From PHP in themes, call do_shortcode('ciao_box');
        add_shortcode('ciao_box', array('ciao_box', 'shortcode'));
    }
    		
	// back end options dialogue
	function control() {
	    $options = get_option('ciao_box');
		if (!is_array($options))
			$options = array('title'=>__('Ciao Box', 'ciao_box'), 'code'=>__(''));
		if ($_POST['ciao_box-submit']) {
			$options['title'] = strip_tags(stripslashes($_POST['ciao_box-title']));
			//$options['code'] = strip_tags(stripslashes($_POST['ciao_box-code']));
			$options['code'] = stripslashes($_POST['ciao_box-code']);
			update_option('ciao_box', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		//$code = htmlspecialchars($options['code'], ENT_QUOTES);
		$code = $options['code'];

		echo '<p><label for="ciao_box-title">' . __('Title:') .
		' <br><input style="width: 200px;" id="ciao_box-title" name="ciao_box-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p><label for="ciao_box-code">' .  __('Ciao Box Code:', 'widgets') .
		' <br><textarea cols="35" rows="6" id="ciao_box-code" name="ciao_box-code">'.$code.'</textarea></label></p>';
		echo '<input type="hidden" id="ciao_box-submit" name="ciao_box-submit" value="1" />';
	}

    function view($is_widget, $args=array()) {
    	if($is_widget) extract($args);
    
    	// get widget options
    	$options = get_option('ciao_box');
    	$title = $options['title'];
    	$code = $options['code'];
        
    	// all calculation is done by the client, trying to compensate for common errors like mixing meters with centimeters.
    	$point = __('.', 'ciao_box'); // decimal point
    	$bs = '\\';

    	// the widget's form
		$out[] = $before_widget . $before_title . $title . $after_title;
		$out[] = $code;
    	$out[] = $after_widget;
    	return join($out, "\n");
    }

    function shortcode($atts, $content=null) {
        return ciao_box::view(false);
    }

    function widget($atts) {
        echo ciao_box::view(true, $atts);
    }
}

add_action('widgets_init', array('ciao_box', 'init'));

?>
