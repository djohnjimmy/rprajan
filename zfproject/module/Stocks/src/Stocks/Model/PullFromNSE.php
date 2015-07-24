<?php
namespace Stocks\Model;

use Stocks\Model\CsvImporter;
use Zend\Filter\Decompress;

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(- 1);
date_default_timezone_set('Asia/Kolkata');

class PullFromNSE
{

    function __construct()
    {
    }
    

    public function pull($day, $month, $year)
    {
//         echo "<br /> year = $year";
//         echo "<br /> month = $month";
//         echo "<br /> day = $day";
        
        $url = "http://www.nse-india.com/content/historical/EQUITIES/$year/$month/cm$day$month${year}bhav.csv.zip";
        
//         echo "<br /> URL = $url";
        // Get cURL resource
        
        if (! $this->url_exists($url)) {
            echo "<br /> The file cm$day$month${year}bhav.csv.zip does not exist on the server yet!  ";
            exit();
        }
        
        $curl = curl_init();
        $tempFile = "/tmp/cm$day$month${year}bhav.csv.zip";
        $fp = fopen($tempFile, "w");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        
        curl_exec($curl);
        curl_close($curl);
        
        
        $filter     = new Decompress(array(
            'adapter' => 'Zip',
            'options' => array(
                'target' => '/tmp/',
            )
        ));
        $decompressed = $filter->filter($tempFile);
        // Returns true on success and decompresses the archive file
        // into the given target directory
        if ($decompressed) {
//             echo '<br /> Extraction Completed!';
        } else {
            echo '<br /> EXTRACTION FAILED';
        }
    }

    public function url_exists($url)
    {
        if (! $fp = curl_init($url))
            return false;
        else
            return true;
    }

    public function load($file, $scripTable, $stockTable)
    {
        if (! file_exists($file)) {
            echo "<br /> The file $file does not exist";
            exit();
        }
        
        $importer = new CsvImporter($file, true);
        
        $data = $importer->get(2000);
        $newScripCount = 0;
        $newStockCount = 0;
        
        foreach ($data as $line) {
            foreach ($line as $row) {
                $record = explode(",", $row);
                $symbol = $record[0];
                $series = $record[1];
                $last = $record[6];
                $prevclose = $record[7];
                $TOTTRDQTY = $record[8];
                $time = $record[10];
                $timestamp = strtotime($time);
                $gain = round(floatval($last) - floatval($prevclose), 2);
                
//                 echo "<br/>";
//                 echo " symbol = $symbol";
//                 echo " last = $last";
//                 echo " prevclose = $prevclose";
//                 echo " TOTTRDQTY = $TOTTRDQTY";
//                 echo " time = $time";
//                 echo " timestamp = $timestamp";
//                 echo " gain = $gain";
//                 echo "<br/>";
                
//                 $selectSql = "SELECT id, SYMBOL FROM stocks.SCRIP WHERE SYMBOL like '$symbol' AND SERIES like '$series'";
                $selectResult = $scripTable->getScripBySymbol($symbol, $series);
//                 var_dump($selectResult);
                
                if ($selectResult->count() == 0) {
                    // echo " New SCRIP Entry found. Inserting into the SCRIP TABLE.";
                   $new_scrip = new Scrip();
                   $new_scrip->symbol = $symbol;
                   $new_scrip->series = $series;
//                    echo "Creating new_scrip";
//                    var_dump($new_scrip);
                   
                   $insertResult = $scripTable->saveScrip( $new_scrip);
                    
                    // echo "<br /> insert result = $insertResult";
                    if ($insertResult == 1) {
//                         echo "New scrip created successfully";
                        $newScripCount ++;
                    } else {
                        echo "Error: inserting new SCRIP for $symbol . $series";
                    }
                } else {
                    // echo "<br/> Existing SCRIP record found";
                }
                
                // Rerun the SQL to check if the insert has made it.
                $selectResult = $scripTable->getScripBySymbol($symbol, $series);
                if ($selectResult->count() > 0) {
                    
                    // output data of each row
                    foreach ($selectResult as $scrip ) {
//                         var_dump($scrip);
                        
                        $stockResult = $stockTable->getStocksByScripAndTime($scrip->id, $timestamp);
//                         var_dump($stockResult);

                        if ($stockResult->count() == 0) {
                                                        
                            $new_stock = new Stocks();
                            $new_stock->last = $last;
                            $new_stock->prevclose = $prevclose;
                            $new_stock->tottrdqty = $TOTTRDQTY;
                            $new_stock->timestamp = date ( 'Y-m-d ', $timestamp);
                            $new_stock->scrip_id = $scrip->id;
                            
                            $insertResult = $stockTable->saveStocks($new_stock);

                            // echo "<br /> insert result = $insertResult";
                            if ($insertResult == 1) {
//                                 echo "New stock created successfully";
                                $newScripCount ++;
                            } else {
                                echo "Error: inserting new stock for ";
                            }
                            $newStockCount ++;
                        } else {
                            // echo "record already exists";
                        }
                    }
                } else {
                    echo "<br />COULD NOT find the SCRIP in database with $symbol and $series";
                }
            }
        }
        return array("newScripCount" => $newScripCount,
            "newStockCount" => $newStockCount
        );
        
//         if ($newScripCount > 0) {
//             echo "<br/> ";
//             echo " Found $newScripCount SCRIPs. Inserted into the database";
//         } else {
//             echo "<br/> ";
//             echo " No new SCRIPs found ";
//         }
//         if ($newStockCount > 0) {
//             echo "<br/> ";
//             echo " Found $newStockCount Stocks. Inserted into the database";
//         } else {
//             echo "<br/> ";
//             echo " No new Stocks found ";
//         }
    }
}

?>