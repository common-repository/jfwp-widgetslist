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

use jf\wp\widget\field\TextField;

/**
 * Abstract class widget manages lists
 *
 * @category   jf
 * @package    wp
 * @subpackage widget
 * @author     Jérôme Fath <projects@jeromefath.com>
 * @copyright  Copyright (c) 2010-2011, Jérôme Fath <http://www.jeromefath.com>
 */
abstract class ListWidget extends Widget
{
    /**
     * @var string Content before the start of the widget
     */
    protected $beforeWidget = null;
    
    /**
     * @var string Content after the end of the widget
     */
    protected $afterWidget = null;
    
    /**
     * @var string Content before the start of the title
     */
    protected $beforeTitle = null;
    
    /**
     * @var string Content after the end of the title
     */
    protected $afterTitle = null;
    
    /**
     * @var string The title
     */
    protected $title = null;
     
    /**
     * @var int Total number of lists 
     */ 
    protected $list = 0;
    
    /**
     * @var string Content before the start of list
     */
    protected $beforeList = null;
    
   	/**
   	 * @var string List separator
   	 */
    protected $separatorList = null;
    
    /**
     * @var string Content after the end of list
     */
    protected $afterList = null;

    /**
     * @var int Total number of elements 
     */
    private $_element = 0;
    
    /**
     * @var array Number of elements in a list
     */
    private $_elementList = array();
    
    /**
     * @var int Counter elements
     */
    private $_countElement = 0;
    
    /**
     * @var int Counter elements of the current list
     */
    private $_countElementList = 0;
    
    /**
     * @var int  Counter lists
     */
    private $_countList = 0;
	
	/** 
	 * Update a particular instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * 
	 * @return array Settings to save or bool false to cancel saving
	 */
    public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['list'] = $new_instance['list'] > 0 ? $new_instance['list'] : 1 ;
		$instance['before_list'] = $new_instance['before_list'];
		$instance['separator_list'] = $new_instance['separator_list'];
        $instance['after_list'] = $new_instance['after_list'];
		
		return $instance;
	}
    
	/** 
	 * Creating fields inheriting from the class ListWidget
	 *
	 * @return void
	 */
    protected function createFields() 
    {
        $this->setFields( array('title' => new TextField( array('label' => __('Title : ', 'jfwp-widgetslist')),
                                                          array('class' => 'widefat') ),   
                                                                         
                                'list' => new TextField( array('label' => __('Number list : ', 'jfwp-widgetslist')) ),
                                                        
                                'before_list' => new TextField( array('label' => __('Before the start of a list : ', 'jfwp-widgetslist')),
                                                                array('class' => 'widefat') ),
                                                         
                                'separator_list' => new TextField( array('label' => __('Separator : ', 'jfwp-widgetslist')),
                                                                   array('class' => 'widefat') ),

                                'after_list' => new TextField( array('label' => __('After the end of a list : ', 'jfwp-widgetslist')),
                                                               array('class' => 'widefat') ),
                             ));
	}
	
	/**
	 * Configure the parameters used to display the widget
	 *
	 * @param array $args 
	 * @param array $instance 
	 * @param int $count Number of elements to display
	 * 
	 * @return void
	 */
    protected function configure($args, $instance, $count)
	{
	    extract($args); //Extration des valeurs natives  'before_widget', 'after_widget', .. 
        
        $this->beforeWidget = $before_widget;
        $this->afterWidget = $after_widget;
        $this->beforeTitle = $before_title;
        $this->afterTitle = $after_title;

        $this->title = $instance['title'];
        $this->list = $instance['list'] > 0 ? $instance['list'] : 1;
		$this->beforeList = $instance['before_list'];
		$this->separatorList = $instance['separator_list'];
		$this->afterList = $instance['after_list'];
		
		$this->_element = $count;
		$this->_elementList = array();
		$this->_countElement = 0;
		$this->_countElementList = 0;
		$this->_countList = 0;
    
		$countElement = $this->_element;
		$countList = $this->list;
		
		$elementList = ceil($this->_element / $this->list);
        
		for($i=0; $i < $this->list; $i++)
		{
		    $this->_elementList[$i+1] = $elementList;
		    
		    $countElement = $countElement - $elementList;
		    $countList --;
		    
		    $elementList = $countList > 0 ? ceil($countElement / $countList) : $countElement;
		}
	}
	
 	/**
     * Displays the list of links
     * 
     * @param array $args
     * @param array $instance
     * @param array $links
     * @return void
     */
    protected function displayList($args, $instance, $links)
    {
        $this->configure($args, $instance, sizeof($links)) ;
           
        $this->displayStartWidget();

        foreach ($links as $link) 
        {
            $this->displayStartList();
          
            echo $link;
          
            $this->displayEndList();
         }
       
         $this->displayEndWidget();
    }
    
	/**
	 * Displays the start widget
	 *
	 * @return void
	 */
    protected function displayStartWidget()
    {
		echo $this->beforeWidget;

		if ( $this->title != null )
		{
			echo $this->beforeTitle . $this->title . $this->afterTitle;
		}
    }
    
    /**
     * Displays the end widget
     * 
     * @return void
     */
    protected function displayEndWidget()
    {
        echo $this->afterWidget;
    }
    
    /**
     * Displays the start list
     * 
     * @return void
     */
    protected function displayStartList()
    {
        $this->_countElement ++;
        $this->_countElementList ++;
              
        if($this->_countElementList == 1)
        {
            $this->_countList ++;
             
            echo $this->beforeList;
        }
    }
    
    /**
     * Displays the end list
     * 
     * @return void
     */
    protected function displayEndList()
    {
        if($this->_elementList[$this->_countList] > $this->_countElementList)
        {
            echo $this->separatorList;
        }
        
        if($this->_elementList[$this->_countList] == $this->_countElementList)
        {
            $this->_countElementList = 0;
          
            echo $this->afterList;
        } 
    }
}