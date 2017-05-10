<?php
/* 
 * Author: Dejan Adamovic
 * Date: 10.05.2017.
 * Description: function which is comparing strings and checking are they anagrams
 * Comment: In this function it is presumed that strings are ASCII. In case of UTF-8 or other encoding strtolower() function needs to be replaced with mb_strtolower() etc...
 */

function isAnagram($string1, $string2) {
    
    $res = false;
    
    if (is_string($string1) && is_string($string2) && (trim($string1) != '') && (trim($string2) != '')) {
    
        $array1 = str_split(str_replace(' ','',strtolower($string1)));
        sort($array1);
        
        $array2 = str_split(str_replace(' ','',strtolower($string2)));
        sort($array2);
        
        if ($array1 === $array2) {
            $res = true;
        }
    }   
    
    return $res;
}

?>

