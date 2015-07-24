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

            $yesterday = date ( 'Y-m-d ', mktime ( 0, 0, 0, date ( "m" ), date ( "d" )-2, date ( "Y" ) ) );
            $today = date ( 'Y-m-d ', mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ));
            $select->join(array('p' => 'scrip'), 'p.id = stocks.scrip_id');
            $select->where->greaterThan('gain', 6);
            $select->where->between('timestamp', $yesterday, $today);
//             $select->order('gain DSC')->limit(2);
            $select->order('timestamp DESC');
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
    
    public function getStocksByScripAndTime($scrip_id, $timestamp)
    {
        $date = date ( 'Y-m-d ', $timestamp);
        $resultSet = $this->tableGateway->select(function (Select $select) use ($scrip_id, $date) {
            $select->where->equalTo('scrip_id', $scrip_id);
            $select->where->equalTo('timestamp', $date);
//             echo "$select->getSqlString($this->tableGateway->getAdapter())";
//             echo "$this->tableGateway->getSql())";
            
            });
        

        return $resultSet;
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
            'created_time'  => date('Y-m-d H:i:s'),
        );

        $id = (int) $stocks->id;
        if ($id == 0) {
            $result = $this->tableGateway->insert($data);
            return $result;
        } else {
            if ($this->getStocks($id)) {
                $result = $this->tableGateway->update($data, array('id' => $id));
                return $result;
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
