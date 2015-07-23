<?php
namespace Stocks\Model;

class Stocks
{
    public $id;
    public $last;
    public $prevclose;
    public $tottrdqty;
    public $gain;
    public $timestamp;
    public $scrip_id;
    public $created_time;
    public $symbol;
    public $series;
    
    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->last = (!empty($data['last'])) ? $data['last'] : null;
        $this->prevclose  = (!empty($data['prevclose'])) ? $data['prevclose'] : null;
        $this->tottrdqty = (!empty($data['tottrdqty'])) ? $data['tottrdqty'] : null;
        $this->gain = (!empty($data['gain'])) ? $data['gain'] : null;
        $this->timestamp = (!empty($data['timestamp'])) ? $data['timestamp'] : null;
        $this->scrip_id = (!empty($data['scrip_id'])) ? $data['scrip_id'] : null;
        $this->created_time = (!empty($data['created_time'])) ? $data['created_time'] : null;
        $this->symbol = (!empty($data['symbol'])) ? $data['symbol'] : null;
        $this->series = (!empty($data['symbol'])) ? $data['series'] : null;
    }
}