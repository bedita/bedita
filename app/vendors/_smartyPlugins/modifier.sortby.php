<?php

#
# sorts an array of named arrays by the supplied fields
#   code by dholmes at jccc d0t net
#   taken from http://au.php.net/function.uasort
#
function array_sort_by_fields(&$data, $sortby){
    if(is_array($sortby)) {
        $sortby = join(',', $sortby);
    }
    uasort( $data,
         create_function( '$a, $b', '
            $skeys = split(\',\',\''.$sortby.'\');
            foreach($skeys as $key){
               if( ($c = strcasecmp($a[$key],$b[$key])) != 0 ){
                            return($c);
               }
            }
           return($c);  '));
}

#
# Modifier: sortby - allows arrays of named arrays to be sorted by a given field
#
function smarty_modifier_sortby($arrData, $sortfields) {
   array_sort_by_fields($arrData, $sortfields);
   return $arrData;
} 



?>