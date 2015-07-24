<?php
namespace Stocks\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ScripTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getScrip($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getScripBySymbol($symbol, $series)
    {

        $resultSet = $this->tableGateway->select(function (Select $select) use ($symbol, $series) {
            $select->where->like('symbol', $symbol);
            $select->where->like('series', $series);
        });
        return $resultSet;
        
    }
    
    
    public function saveScrip(Scrip $scrip)
    {
        $data = array(
            'symbol' => $scrip->symbol,
            'series' => $scrip->series,
//             'timestamp'  => $scrip->timestamp,
            'created_time'  => date('Y-m-d H:i:s'),
        );

        $id = (int) $scrip->id;
        if ($id == 0) {
            $result = $this->tableGateway->insert($data);
            return $result;
        } else {
            if ($this->getScrip($id)) {
                $result = $this->tableGateway->update($data, array('id' => $id));
                return $result;
            } else {
                throw new \Exception('Scrip id does not exist');
            }
        }
    }

    public function deleteScrip($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}