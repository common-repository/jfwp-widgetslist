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

use jf\core\object\HierachicalInterface;
use jf\core\iterator\HierachicalIterator;

use jf\wp\widget\field\CheckboxField;

/**
 * Abstract class widget manages hierachical lists 
 *
 * @category   jf
 * @package    wp
 * @subpackage widget
 * @author     Jérôme Fath <projects@jeromefath.com>
 * @copyright  Copyright (c) 2010-2011, Jérôme Fath <http://www.jeromefath.com>
 */
abstract class HierachicalListWidget extends ListWidget
{
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
		$instance = parent::update($new_instance, $old_instance);

		$instance['hierarchy'] = !empty($new_instance['hierarchy']) ? true : false;
		
		return $instance;
	}
	
	/** 
	 * Creating fields inheriting from the class HierachicalListWidget
	 *
	 * @return void
	 */
    protected function createFields() 
    {
        parent::createFields();
        
        $this->setField('hierarchy', new CheckboxField( array('label' => __('Display hierarchy : ', 'jfwp-widgetslist')) ));   
	}
	
    /**
     * Displays the list of hierachical links
     * 
     * @param array $args
     * @param array $instance
     * @param HierachicalInterface $hierachicalObject
     * @param Closure $linkFunction
     * 
     * @return void
     */
    protected function displayList($args, $instance, HierachicalInterface $hierachicalObject, $linkFunction)
    {
        $this->configure($args, $instance, $hierachicalObject->countChildren());
        
        $this->displayStartWidget();
        
        $iterator = new HierachicalIterator($hierachicalObject);   
                         
        foreach ($iterator as $hierachicalObject)
        {
            $this->displayStartList();
            
            echo $linkFunction($hierachicalObject);
            
            if($hierachicalObject->hasChildren())
            {
                $this->displaySubList($hierachicalObject, $linkFunction);
            }
            
            $this->displayEndList();
        }
       
        $this->displayEndWidget();
    }
    
   /**
     * Displays the sublist of hierachical links
     * 
     * @param HierachicalInterface $hierachicalObject
     * @param Closure $linkFunction
     * 
     * @return void
     */
    protected function displaySubList(HierachicalInterface $hierachicalObject, $linkFunction)
	{
	    $count = 0;
        $countChildren = $hierachicalObject->countChildren();
                    
        $iterator = new HierachicalIterator($hierachicalObject);
                    
        foreach ($iterator as $hierachicalObject)
        {
            $count ++;
            
            if($count == 1)
            {
                echo $this->beforeList;
            }
            else
            {
                echo $this->separatorList;
            }
            
            echo $linkFunction($hierachicalObject);
            
            if($hierachicalObject->hasChildren())
            {
                $this->displaySubList($hierachicalObject, $linkFunction);
            }
            
            if($count == $countChildren)
            {
                echo $this->afterList;
            }
        }
	}
}