<?php
namespace Stocks\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class PullData implements InputFilterAwareInterface
{
    public $date;
    protected $inputFilter;                       // <-- Add this variable
    
    public function exchangeArray($data)
    {
        $this->date     = (isset($data['date']))     ? $data['date']     : null;
    }

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
    
            $inputFilter->add(array(
                'name'     => 'date',
                'required' => true,
//                 'filters'  => array(
//                     array('name' => 'Date'),
//                 ),
            ));
    

    
            $this->inputFilter = $inputFilter;
        }
    
        return $this->inputFilter;
    }
    
}

?>