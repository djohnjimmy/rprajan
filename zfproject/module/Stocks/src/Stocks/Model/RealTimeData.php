<?php
namespace Stocks\Model;

class RealTimeData
{
    public $id;
    public $high;
    public $close;
    public $low;
    public $open;
    public $volume;
    public $symbol;
    public $timestamp;
    public $gain;
    public $series;
    public $dateTime;
    
    
    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->high = (!empty($data['high'])) ? $data['high'] : null;
        $this->close  = (!empty($data['close'])) ? $data['close'] : null;
        $this->low = (!empty($data['low'])) ? $data['low'] : null;
        $this->open = (!empty($data['open'])) ? $data['open'] : null;
        $this->volume = (!empty($data['volume'])) ? $data['volume'] : null;
        $this->symbol = (!empty($data['symbol'])) ? $data['symbol'] : null;
        $this->timestamp = (!empty($data['timestamp'])) ? $data['timestamp'] : null;
        $this->gain = (!empty($data['gain'])) ? $data['gain'] : null;
        $this->series = (!empty($data['symbol'])) ? $data['series'] : null;
        $this->dateTime = (!empty($data['dateTime'])) ? $data['dateTime'] : null;
    }
}