<?php
namespace Stocks\Model;

class Scrip
{
    public $id;
    public $timestamp;
    public $created_time;
    public $symbol;
    public $series;
    
    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->timestamp = (!empty($data['timestamp'])) ? $data['timestamp'] : null;
        $this->created_time = (!empty($data['created_time'])) ? $data['created_time'] : null;
        $this->symbol = (!empty($data['symbol'])) ? $data['symbol'] : null;
        $this->series = (!empty($data['symbol'])) ? $data['series'] : null;
    }
}