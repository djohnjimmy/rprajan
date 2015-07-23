<?php
namespace Stocks\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class StocksTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        
//         $resultSet = $this->tableGateway->select();
//         $select = new Select();
        
        $resultSet = $this->tableGateway->select(function (Select $select) {

            $yesterday = date ( 'Y-m-d ', mktime ( 0, 0, 0, date ( "m" ), date ( "d" )-17, date ( "Y" ) ) );
            $today = date ( 'Y-m-d ', mktime ( 0, 0, 0, date ( "m" ), date ( "d" )-4, date ( "Y" ) ));
//             $select->where->like('name', 'Brit%');
            $select->where->greaterThan('gain', 6);
            $select->where->between('timestamp', $yesterday, $today);
//             $select->order('gain DSC')->limit(2);
            $select->order('timestamp DESC')->limit(10);
        });
        return $resultSet;
    }

    public function getStocks($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveStocks(Stocks $stocks)
    {
        $data = array(
            'last' => $stocks->last,
            'prevclose'  => $stocks->prevclose,
            'tottrdqty'  => $stocks->tottrdqty,
            'gain'  => $stocks->gain,
            'timestamp'  => $stocks->timestamp,
            'scrip_id'  => $stocks->scrip_id,
            'created_time'  => $stocks->created_time,
        );

        $id = (int) $stocks->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getStocks($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Stocks id does not exist');
            }
        }
    }

    public function deleteStocks($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}