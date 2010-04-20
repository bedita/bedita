<?php
/*
* Smarty plugin
*
-------------------------------------------------------------
* File: modifier.html_substr.php
* Type: modifier
* Name: html_substr
* Version: 1.0
* Date: June 19th, 2003
* Purpose: Cut a string preserving any tag nesting and matching.
* Install: Drop into the plugin directory.
* Author: Original Javascript Code: Benjamin Lupu <lupufr@aol.com>
* Translation to PHP & Smarty: Edward Dale <scompt@scompt.com>
* Modification to add a string: Sebastian Kuhlmann <sebastiankuhlmann@web.de>
* 
* bato edit: insert $addstring into last closed tag
*
-------------------------------------------------------------
*/
function smarty_modifier_html_substr($string, $length, $addstring="")
{
	$addstring = " " . $addstring;
	
	if (strlen($string) > $length) {
		if( !empty( $string ) && $length>0 ) {
			$isText = true;
			$ret = "";
			$i = 0;
			
			$currentChar = "";
			$lastSpacePosition = -1;
			$lastChar = "";
			
			$tagsArray = array();
			$currentTag = "";
			$tagLevel = 0;
			
			$noTagLength = strlen( strip_tags( $string ) );
		
			// Parser loop
			for( $j=0; $j<strlen( $string ); $j++ ) {
			
				$currentChar = substr( $string, $j, 1 );
				$ret .= $currentChar;
		
				// Lesser than event
				if( $currentChar == "<") $isText = false;
		
				// Character handler
				if( $isText ) {
		
					// Memorize last space position
					if( $currentChar == " " ) { $lastSpacePosition = $j; }
					else { $lastChar = $currentChar; }
		
					$i++;
				} else {
					$currentTag .= $currentChar;
				}
		
				// Greater than event
				if( $currentChar == ">" ) {
					$isText = true;
		
					// Opening tag handler
					if( ( strpos( $currentTag, "<" ) !== FALSE ) &&
					( strpos( $currentTag, "/>" ) === FALSE ) &&
					( strpos( $currentTag, "</") === FALSE ) ) {
		
						// Tag has attribute(s)
						if( strpos( $currentTag, " " ) !== FALSE ) {
							$currentTag = substr( $currentTag, 1, strpos( $currentTag, " " ) - 1 );
						} else {
							// Tag doesn't have attribute(s)
							$currentTag = substr( $currentTag, 1, -1 );
						}
		
						array_push( $tagsArray, $currentTag );
		
					} else if( strpos( $currentTag, "</" ) !== FALSE ) {
						array_pop( $tagsArray );
					}
		
					$currentTag = "";
				}
		
				if( $i >= $length) {
					break;
				}
			}
		
			// Cut HTML string at last space position
			if( $length < $noTagLength ) {
				if( $lastSpacePosition != -1 ) {
					$ret = substr( $string, 0, $lastSpacePosition );
				} else {
					$ret = substr( $string, $j );
				}
			}
		
			if (!empty($tagsArray)) {
				// Close broken XHTML elements
				while( sizeof( $tagsArray ) != 0 ) {
					$aTag = array_pop( $tagsArray );
					$ret .= (count($tagsArray) == 0)? $addstring . "</" . $aTag . ">" : "</" . $aTag . ">";
				}
			} else {
				$ret .= $addstring;
			}
	
		} else {
			$ret = "";
		}
	
		return $ret;
		// only add string if text was cut
//		if ( strlen($string) > $length ) {
//			return( $ret.$addstring );
//		}
//		else {
//			return ( $res );
//		}
	}
	else {
		return ( $string );
	}
}

/* vim: set expandtab: */

?>