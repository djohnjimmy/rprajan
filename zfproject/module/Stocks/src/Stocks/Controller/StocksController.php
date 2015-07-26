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
        
        //HIGHCHARTS
        
        $chart = new Highchart();
        
        $chart->chart->renderTo = "container";
        $chart->chart->type = "area";
        $chart->title->text = "US and USSR nuclear stockpiles";
        $chart->subtitle->text = "Source: <a href=\"http://thebulletin.metapress.com/content/c4120650912x74k7/fulltext.pdf\">thebulletin.metapress.com</a>";
        $chart->xAxis->labels->formatter = new HighchartJsExpr("function() { return this.value;}");
        $chart->yAxis->title->text = "Nuclear weapon states";
        $chart->yAxis->labels->formatter = new HighchartJsExpr("function() { return this.value / 1000 +'k';}");
        $chart->tooltip->formatter = new HighchartJsExpr(
            "function() {
                              return this.series.name +' produced <b>'+
                              Highcharts.numberFormat(this.y, 0) +'</b><br/>warheads in '+ this.x;}");
        $chart->plotOptions->area->pointStart = 1940;
        $chart->plotOptions->area->marker->enabled = false;
        $chart->plotOptions->area->marker->symbol = "circle";
        $chart->plotOptions->area->marker->radius = 2;
        $chart->plotOptions->area->marker->states->hover->enabled = true;
        
        $chart->series[] = array(
            'name' => 'USA',
            'data' => array(
                null,
                null,
                null,
                null,
                null,
                6,
                11,
                32,
                110,
                235,
                369,
                640,
                1005,
                1436,
                2063,
                3057,
                4618,
                6444,
                9822,
                15468,
                20434,
                24126,
                27387,
                29459,
                31056,
                31982,
                32040,
                31233,
                29224,
                27342,
                26662,
                26956,
                27912,
                28999,
                28965,
                27826,
                25579,
                25722,
                24826,
                24605,
                24304,
                23464,
                23708,
                24099,
                24357,
                24237,
                24401,
                24344,
                23586,
                22380,
                21004,
                17287,
                14747,
                13076,
                12555,
                12144,
                11009,
                10950,
                10871,
                10824,
                10577,
                10527,
                10475,
                10421,
                10358,
                10295,
                10104
            )
        );
        
        $chart->series[] = array(
            'name' => 'USSR/Russia',
            'data' => array(
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                5,
                25,
                50,
                120,
                150,
                200,
                426,
                660,
                869,
                1060,
                1605,
                2471,
                3322,
                4238,
                5221,
                6129,
                7089,
                8339,
                9399,
                10538,
                11643,
                13092,
                14478,
                15915,
                17385,
                19055,
                21205,
                23044,
                25393,
                27935,
                30062,
                32049,
                33952,
                35804,
                37431,
                39197,
                45000,
                43000,
                41000,
                39000,
                37000,
                35000,
                33000,
                31000,
                29000,
                27000,
                25000,
                24000,
                23000,
                22000,
                21000,
                20000,
                19000,
                18000,
                18000,
                17000,
                16000
            )
        );
        $view->setVariable("chart", $chart);
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
