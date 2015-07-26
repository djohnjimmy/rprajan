<?php
namespace Stocks\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Stocks\Model\PullSymbolDetailsFromYahoo;
use Stocks\Form\StocksForm;
use Stocks\Model\PullData;
use Stocks\Model\PullFromNSE;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Formatter;
use HighRoller\LineChart;
use HighRoller\BarChart;
use HighRoller\SeriesData;
use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;

class StocksController extends AbstractActionController
{
    protected $stocksTable;
    protected $symbol;

    protected $scripTable;
    /**
     * Simple bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction ()
    {
        $data = array(
            array ('displayName' => 'Mohammad ZeinEddin'),
            array ('displayName' => 'John Wayne'),
            array ('displayName' => 'Oprah Winfrey')
        );
    
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Minimal grid');
        $grid->setDataSource($data);
    
        $col = new Column\Select('displayName');
        $col->setLabel('displayName');
        $grid->addColumn($col);

        $grid->render();
    
        return $grid->getResponse();
    }
    
    public function indexAction()
    {
//         return new ViewModel(array(
//             'stocks' => $this->getStocksTable()->fetchAll(),
//         ));
//         }
        
//         public function testAction()
//         {
        
        $resultSet = $this->getStocksTable()->fetchAll();
//         var_dump($resultSet);
        foreach ($resultSet as $result){
            $data[] = (array) $result;
        }
        if(empty($data)){
            return new ViewModel(array(
                'message' => "No data to fetch from yesterday",
                'stocks' => array(),
            ));        
        }
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Minimal grid');
        $grid->setDataSource($data);
    
        $col = new Column\Select('id');
        $col->setLabel('Id');
        $grid->addColumn($col);
    
        $col = new Column\Select('symbol');
//         $this->getRequest()->getBasePath();
        $col->setLabel('Symbol');
        $formatter= new Formatter\Link();
        $formatter->setLink('./stocks/now/'.$formatter->getColumnValuePlaceholder($col));
//         $formatter->setLink("/now/".$col->value);
        $col->setFormatter($formatter);
        $grid->addColumn($col);
        
    
        $col = new Column\Select('series');
        $col->setLabel('Series');
        $grid->addColumn($col);
    
        $col = new Column\Select('last');
        $col->setLabel('Last');
        $grid->addColumn($col);
    
        $col = new Column\Select('prevclose');
        $col->setLabel('Previous Close');
        $grid->addColumn($col);
    
        $col = new Column\Select('tottrdqty');
        $col->setLabel('Total Traded Quantity');
        $grid->addColumn($col);
    
        $col = new Column\Select('gain');
        $col->setLabel('Gain');
        $grid->addColumn($col);
    
        $col = new Column\Select('timestamp');
        $col->setLabel('Date');
        $grid->addColumn($col);
        
    
//         $col = new Column\Select('created_time');
//         $col->setLabel('Created Time');
//         $grid->addColumn($col);
    
        $grid->render();
    
        return $grid->getResponse();
    
        //         return new ViewModel(array(
        //             'stocks' => $this->getStocksTable()->fetchAll(),
        //         ));
    }
    
    public function addAction()
    {

        $form = new StocksForm();
        $form->get('submit')->setValue('Load');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $pullData = new PullData();
            $form->setInputFilter($pullData->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $pullData->exchangeArray($form->getData());
                
                //
//                 $this->getStocksTable()->saveAlbum($album);
                 $message = " Date :". $pullData->date;
                 

                 if ($pullData->date != null) {
                 
//                      echo "Date picked:" . $pullData->date;
                     $year = date ( "Y", strtotime ( $pullData->date ) );
                     $month = strtoupper ( date ( "M", strtotime ( $pullData->date ) ) );
                     $day = date ( "d", strtotime ( $pullData->date ) );
                 } else {
                     $year = date ( "Y" );
                     $month = strtoupper ( date ( "M" ) );
                     $day = date ( "d" );
                 }
                 
                 $pullParser = new PullFromNSE();
                 
                 
                 //Pull the file from NSE
                 $pullParser->pull($day, $month, $year);
                 
                 //Load the file;
                 $status = $pullParser->load("/tmp/cm$day$month${year}bhav.csv", $this->getScripTable(), $this->getStocksTable());
                  $message .= "<br /><br /> Found ". $status['newScripCount']. " new scrips <br /><br />";
                  $message .= " Found ". $status['newStockCount']. " new stocks <br /><br />";
                  
                 
                // Redirect to list of albums
                return array('form' => $form, 'message' => $message);
            }
        }
        return array('form' => $form);
        
    }

    public function editAction()
    {
    }

    public function doneAction()
    {
        $symbol = $this->params()->fromRoute('symbol', '');
        if ($symbol == '') {
        return new ViewModel(array(
            'symbol' => $symbol,
            'stocks' => $stocks,
        ));
        }
    }
    
    public function nowAction()
    {
        
        $symbol = $this->params()->fromRoute('symbol', '');
        if ($symbol == '') {
            return $this->redirect()->toRoute('stocks', array(
                'action' => 'index'
            ));
        }
        
        
        $pullParser = new PullSymbolDetailsFromYahoo();
        
        //Pull the file from Yahoo
        $stocks = $pullParser->pull($symbol);
        foreach ($stocks as $result){
            $chartData[$result->dateTime ]=$result->total;
        }
        
        krsort($stocks, SORT_NUMERIC);
        foreach ($stocks as $result){
            $data[] = (array) $result;
        }
        
        
        
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Trend grid');
        $grid->setDataSource($data);
        
        $col = new Column\Select('id');
        $col->setLabel('Id');
        $grid->addColumn($col);

        $col = new Column\Select('symbol');
        $col->setLabel('symbol');
        $grid->addColumn($col);
        
        $col = new Column\Select('dateTime');
        $col->setLabel('Time');
        $grid->addColumn($col);

        $col = new Column\Select('total');
        $col->setLabel('total');
        $grid->addColumn($col);
        
        $col = new Column\Select('volume');
        $col->setLabel('volume');
        $grid->addColumn($col);
        
        
        $col = new Column\Select('high');
        $col->setLabel('high');
        $grid->addColumn($col);
        
        $col = new Column\Select('close');
        $col->setLabel('close');
        $grid->addColumn($col);
        
        $col = new Column\Select('low');
        $col->setLabel('low');
        $grid->addColumn($col);
        
        $col = new Column\Select('open');
        $col->setLabel('open');
        $grid->addColumn($col);
        
        $col = new Column\Select('volume');
        $col->setLabel('volume');
        $grid->addColumn($col);

        $grid->render();
        
        $linechart = new BarChart();
        $linechart->title->text = "Total trend for $symbol";
        
        $series = new SeriesData();
        $series->name = 'trend analysis';
        
//         $chartData = array(5324, 7534, 6234, 7234, 8251, 10324);
        foreach ($chartData as $value)
            $series->addData($value);
        
        $linechart->addSeries($series);
        
        $view = $grid->getResponse();
        $view->setVariable('highroller', $linechart);
        return $view;
        
        
//         return new ViewModel(array(
//             'symbol' => $symbol,
//             'stocks' => $stocks,
//         ));
    }
    
    
    // module/Album/src/Album/Controller/AlbumController.php:
    public function getStocksTable()
    {
        if (!$this->stocksTable) {
            $sm = $this->getServiceLocator();
            $this->stocksTable = $sm->get('Stocks\Model\StocksTable');
        }
        return $this->stocksTable;
    }
    public function getScripTable()
    {
        if (!$this->scripTable) {
            $sm = $this->getServiceLocator();
            $this->scripTable = $sm->get('Stocks\Model\ScripTable');
        }
        return $this->scripTable;
    }
    
    
}
