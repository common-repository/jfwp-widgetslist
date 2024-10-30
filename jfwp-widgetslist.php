<?php

/*
Plugin Name: jfWP.WidgetsList
Plugin URI: http://projects.jeromefath.com
Description: Widgets for displaying elements in lists. Requires the latest version of the jfWP.Core to be installed.
Version: 0.1.3
Author: Jérôme Fath
Author URI: http://www.jeromefath.com
*/

function is_active_jfwpcore()
{
    $plugin = 'jfwp-core/jfwp-core.php';
    $plugins = get_option('active_plugins');
    
    return in_array($plugin, $plugins);
}

add_action('init', 'jfwp_widgetslist_textdomain');
function jfwp_widgetslist_textdomain() {
	load_plugin_textdomain('jfwp-widgetslist', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}

add_action('widgets_init', 'jfwp_widgetslist_init');
function jfwp_widgetslist_init()
{
    if(is_active_jfwpcore())
    {
        $dir = dirname(__FILE__);
    
        $class = array(array('class'=>'ListWidget', 'register' => false),
                       array('class'=>'ArchiveListWidget', 'register' => true),
                       array('class'=>'HierachicalListWidget', 'register' => false),
                       array('class'=>'TermListWidget', 'register' => true), );
        
        foreach ($class as $value)
        {
            include $dir.'/lib/jf/wp/widget/'.$value['class'].'.php';
            
            if($value['register'] == true)
            {
               register_widget('jf\wp\widget\\'.$value['class']);
            }
        }
    }
}

add_action('admin_notices', 'jfwp_widgetslist_admin_notices');
function jfwp_widgetslist_admin_notices()
{
    if(!is_active_jfwpcore())
    {
        echo '<div class="error" style="text-align: center;">
                 '.__('jfWP.Core plugin must be activated to run jfWP.WidgetsList', 'jfwp-widgetslist').'
              </div>';
    }
}