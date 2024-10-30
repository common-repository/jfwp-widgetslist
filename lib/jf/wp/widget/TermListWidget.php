<?php

namespace jf\wp\widget;

/**
 * This file is part of the Wordpress plugin jfWP.WidgetsList
 *
 * Copyright (c) 2010-2011, Jérôme Fath <http://www.jeromefath.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.txt.
 */

use jf\core\object\HierachicalStdObject;
use jf\core\object\HierachicalInterface;

use jf\wp\util\ElementUtil;
use jf\wp\widget\field\TextField;

/**
 * Widget manages lists of terms
 *
 * @category   jf
 * @package    wp
 * @subpackage widget
 * @author     Jérôme Fath <projects@jeromefath.com>
 * @copyright  Copyright (c) 2010-2011, Jérôme Fath <http://www.jeromefath.com>
 */
class TermListWidget extends HierachicalListWidget
{
    /**
     * @var array Default widget's instance settings
     */
    private $_defaults = array('title' => '',
                               'list' => 2,
                               'before_list' => '<ul><li>',
							   'separator_list' => '</li><li>',
                               'after_list' => '</li></ul>',
                               'hierarchy' => true,
    						   'taxonomy' => 'category',
		                       'arguments' => 'order=ASC&orderby=name');
    
    /**
     * Object method to call the function the_widget()
     * 
     * <code>
     * $instance['title'] //The title's widget 
     * $instance['list'] //Total number of lists 
     * $instance['before_list'] //Content before the start of list
     * $instance['separator_list'] ///Content before the start of list
     * $instance['after_list'] //Content after the end of list
     * $instance['hierarchy'] //Displays the hierarchy
     * $instance['taxonomy'] //The taxonomies to retrieve terms from
     * $instance['arguments'] //Arguments passed to the function get_terms(). Parameters 'fields' is not supported
     * </code>
     * 
     * $args see the wordpress documentation the_widget()
     * 
     * @param array $instance The widget's instance settings
     * @param array $args The widget's sidebar args 
     * @return void
     */
    public static function display($instance = null, $args = null)
    {
        the_widget(__CLASS__, $instance, $args);
    }
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $name = __('Terms - Lists', 'jfwp-widgetslist');
        $widget_ops = array('description' => __('Displays terms of a taxonomy in lists', 'jfwp-widgetslist') );
       
        parent::__construct(__CLASS__, $name, $widget_ops);
    }
    
    /**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
     * @return void
     */
	public function widget($args, $instance)
	{
		$instance = array_merge( $this->_defaults, (array) $instance );
        
		$taxonomy = $instance['taxonomy'];
		$arguments = $instance['arguments'].'&fields=all';
	    
        $terms = get_terms($taxonomy, $arguments);

        if (!($terms instanceof \WP_Error) && sizeof($terms) > 0) 
        {
            if($instance['hierarchy'] === true)
            {
                $terms = ElementUtil::hierarchy($terms, 'term_id', 'parent');
            }
            
            $hierachicalObject = new HierachicalStdObject('root', $terms);
            
            $linkFunction = function (HierachicalInterface $hierachicalObject) use ($taxonomy)
        	{
        	    $term = $hierachicalObject->getStdClass();

        	    return sprintf('<a href="%s" title="%s">%s</a>', get_term_link( $term , $taxonomy), $term->name, $term->name);
        	};
            
            $this->displayList($args, $instance, $hierachicalObject, $linkFunction);
        }
	}
	
	/** 
	 * Update a particular instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
    public function update($new_instance, $old_instance)
	{
		$instance = parent::update($new_instance, $old_instance);

		$instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
		$instance['arguments'] = strip_tags($new_instance['arguments']);
		
		return $instance;
	}

	/** 
	 * Echo the settings update form
	 *
	 * @param array $instance Current settings
	 */
    public function form($instance) 
    {
        $this->createFields();
        
        $this->setFields(array('taxonomy' =>  new TextField( array('label' => __('Taxonomy : ', 'jfwp-widgetslist')) ),
        
                         	   'arguments' => new TextField( array('label' =>  __('get_terms parameters : ', 'jfwp-widgetslist')),
                                                             array('class' => 'widefat')),
                         ));
        
        $this->displayFields( (array) $instance, $this->_defaults);      
	}
}