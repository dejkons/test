<?php
/* 
 * Author: Dejan Adamovic
 * Date: 10.05.2017.
 * Description: function for parsing given xml
 * Comment: Function is using stream based parser "XMLReader" library because it is not mentioned
 * in assignement size of potential XML file. Also, it is not mentioned
 * available memory
 */

function xmlToCSV($text) {
    
    // prepare csv file
    
    // create xml reader and loop through xml nodes
    $reader = new XMLReader();
    $reader->xml($text);
    
    while ($reader->read()) {
        
        if ($reader->nodeType == XMLREADER::ELEMENT) {
                if ($reader->localName == "TOUR") {
                    $xmlNodeArray = array();
                    $nodeLowestPrice = 0;
                    // TOUR node found, read internal TOUR elements
                    while ($reader->read()) { 
                        if ($reader->nodeType == XMLREADER::ELEMENT) {
                            
                            if ($reader->localName == "Title") {
                                $reader->read();
                                if (!empty(trim($reader->value))) {
                                    $xmlNodeArray[] = htmlspecialchars_decode($reader->value);
                                } else {
                                    $xmlNodeArray[] = "";
                                }
                            } else if ($reader->localName == "Code") {
                                $reader->read();
                                if (!empty(trim($reader->value))) {
                                    $xmlNodeArray[] = $reader->value;
                                } else {
                                    $xmlNodeArray[] = "";
                                }
                            } else if ($reader->localName == "Duration") {
                                $reader->read();
                                if (!empty(trim($reader->value))) {
                                    $xmlNodeArray[] = $reader->value;
                                } else {
                                    $xmlNodeArray[] = "";
                                }
                            } else if ($reader->localName == "Inclusions") {
                                $value = $reader->readString();
                                if (!empty(trim($value))) {
                                    $tempInclusions = trim(htmlspecialchars_decode(strip_tags($value)));
                                    $tempInclusions = str_replace("&nbsp;", ' ', $tempInclusions);
                                    $xmlNodeArray[] = preg_replace("/\s+/", " ", $tempInclusions);
                                } else {
                                    $xmlNodeArray[] = "";
                                } 
                            } else if ($reader->localName == "DEP") {
         
                                if($reader->hasAttributes) {
                                    
                                    $eurPrice = 0;
                                    $discount = 0;
                                    
                                    while($reader->moveToNextAttribute())
                                    {
                                        if ($reader->name == "EUR") {
                                            $eurPrice = round($reader->value,2);
                                        }
                                        
                                        if ($reader->name == "DISCOUNT") {
                                            $discount = intval(str_replace("%","", $reader->value));
                                        }
                                    }
                                    
                                    if ($discount > 0) {
                                        $eurPrice = $eurPrice - ($eurPrice / 100 * $discount);
                                    }
                                    
                                    if (($nodeLowestPrice == 0) || ($eurPrice < $nodeLowestPrice)) {
                                        $nodeLowestPrice = $eurPrice;
                                    }
                                }
                            }
                        }
                        
                        if ($reader->nodeType == XMLREADER::END_ELEMENT) {
                            if ($reader->localName == "TOUR") {
                                if ($nodeLowestPrice > 0) {
                                    $xmlNodeArray[] = number_format($nodeLowestPrice, 2,'.','');
                                } else {
                                    $xmlNodeArray[] = "";
                                }
                                break;
                            }
                        }
                    }
                    
                    print_r($xmlNodeArray);
                }
        }
    }
}

$text = file_get_contents("doc.xml");
xmlToCSV($text);

?>

