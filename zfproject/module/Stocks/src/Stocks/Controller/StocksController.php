<?php
namespace Stocks\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Stocks\Model\PullSymbolDetailsFromYahoo;
use Stocks\Form\StocksForm;
use Stocks\Model\PullData;
use Stocks\Model\PullFromNSE;
use ZfcDatagrid\Column;

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
        $col->setLabel('Name');
        $grid->addColumn($col);
    
        $grid->render();
    
        return $grid->getResponse();
    }
    
    public function indexAction()
    {
        return new ViewModel(array(
            'stocks' => $this->getStocksTable()->fetchAll(),
        ));
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

                
        return new ViewModel(array(
            'symbol' => $symbol,
            'stocks' => $stocks,
        ));
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
