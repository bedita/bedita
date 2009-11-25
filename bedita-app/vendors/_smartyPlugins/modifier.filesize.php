<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     filesize
 * Purpose:  return size in Kilobyte, Megabyte, Gigabyte or Terabyte
 * -------------------------------------------------------------
 */

/////////////////////////////////////////////
function smarty_modifier_filesize($size) {
   /*
   // First check if the file exists.
 	//if(!is_file("./".$file)) exit("File does not exist!");
	if(!is_file("./".$file)) {
		 return $size."*fnp*";
	}
  */
   // Setup some common file size measurements.
	$kb = 1024;         // Kilobyte
	$mb = 1024 * $kb;   // Megabyte
	$gb = 1024 * $mb;   // Gigabyte
	$tb = 1024 * $gb;   // Terabyte
	//// Get the file size in bytes.
	//$size = filesize($file);
	//// If it's less than a kb we just return the size, otherwise we keep going until  the size is in the appropriate measurement range. */
  
   if($size < $kb) {
      //return $size."b";
	$myfilesize = round($size/$kb,2)."KB";
   }
   else if($size < $mb) {
     $myfilesize = round($size/$kb,0)."KB";
   }
   else if($size < $gb) {
       $myfilesize = round($size/$mb,1)."MB";
  }
   else if($size < $tb) {
      $myfilesize = round($size/$gb,2)."GB";
   }
   else {
      $myfilesize =  round($size/$tb,2)."TB";
   }
   
   return $myfilesize;
}
/////////////////////////////////////////////



?>
