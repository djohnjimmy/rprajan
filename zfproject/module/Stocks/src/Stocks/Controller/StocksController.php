<?php
namespace Stocks\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Stocks\Model\PullSymbolDetailsFromYahoo;

class StocksController extends AbstractActionController
{
    protected $stocksTable;
    protected $symbol;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'stocks' => $this->getStocksTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
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
//         var_dump($stocks);
        
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
//         try {
//             $album = $this->getAlbumTable()->getAlbum($symbol);
//         }
//         catch (\Exception $ex) {
//             return $this->redirect()->toRoute('album', array(
//                 'action' => 'index'
//             ));
//         }
                
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
}
