<?php
/* 
 * Author: Dejan Adamovic
 * Date: 10.05.2017.
 * Description: function for parsing given xml
 * Comment: Function is using stream based parser "XMLReader" library because it is not mentioned
 * size of potential XML file in assignement and available memory
 */

function xmlToCSV($text) {
    
    // create xml reader and loop through xml nodes
    $reader = new XMLReader();
    $reader->xml($text);
	
	// initialize output
	$csv = "Title|Code|Duration|Inclusions|MinPrice\n\r";
    
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
								$value = trim($reader->value);
                                if (!empty($value) and ($value != '')) {
                                    $xmlNodeArray[] = htmlspecialchars_decode($reader->value);
                                } else {
                                    $xmlNodeArray[] = "";
                                }
                            } else if (($reader->localName == "Code") or ($reader->localName == "Duration")) {
                                $reader->read();
								$value = trim($reader->value);
                                if (!empty($value) and ($value != '')) {
                                    $xmlNodeArray[] = $value;
                                } else {
                                    $xmlNodeArray[] = "";
                                }
                            } else if ($reader->localName == "Inclusions") {
                                $value = trim($reader->readString());
                                if (!empty($value) and ($value != '')) {
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
                    
                    if (sizeof($xmlNodeArray) == 5) {
						$csv .= implode("|", $xmlNodeArray)."\n\r";
					}
                }
        }
    }
	
	return $csv;
}

// test
$text = file_get_contents("doc.xml");
$csv = xmlToCSV($text);
echo $csv;


?>

