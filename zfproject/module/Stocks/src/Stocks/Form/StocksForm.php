<?php 
namespace Stocks\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class StocksForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('name');

//          $this->add(array(
//              'name' => 'date',
//              'type' => 'Text',
//              'options' => array(
//                  'label' => 'Date to Pull: ',
//              ),
//          ));
        
        $date = new Element\Date('date');
        $date
            ->setLabel('Date to Pull')
            ->setAttributes(array(
                'min'  => '2015-07-01',
                'max'  => '2020-01-01',
                'step' => '1', // days; default step interval is 1 day
            ))
            ->setOptions(array(
                'format' => 'Y-m-d'
            ));
         $this->add($date);
         
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Pull',
                 'id' => 'submitbutton',
             ),
         ));
          
     }
 }
 ?>