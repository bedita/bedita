<?php
/**
 * Smarty plugin
 * @file function.html_dir_icons.php
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {html_dir_icons} plugin
 *
 * Type: function
 * Name: html_dir_icons
 * @short	Purpose: Scans and displays a directoy using userdefined icons for files
 *			The icons are displayed enclosed in a span, with the name of the file underneath,
 *			with an optional hyperlink to the real file. By default pictures (png, jpg, gif)
 *			not specified with default icons are displayed directly without any hyperlinks.
 *
 * Params:
 *			dir			Web path of the directory (eg. the /dir/to/scan/ of http://www.domain.com/dir/to/scan/)
 *
 * 			filetypes	Associative array of file extensions and icons to display
 *						eg ('xls' => '/path/to/xls/icon',
 *							'pdf' => '/path/to/pdf/image'
 *							 etc.....
 *
 *			format		Changes output format, valid types are box, ulist and olist
 *						Boxed is where each icon (and text) set is enclosed in a span (default)
 *						ulist displays icons and text in an unordered list
 *						olist displays icons and text in an ordered list
 *
 *			list_id		The id of the list (for css etc.) default empty
 *
 *			link		True to produce hyperlinks for the icons (default true)
 *
 *			thumb_pics	True to show any pictures (not defined by filetypes) as unlinked pictures. (default true)
 *
 * ChangeLog:
 *		- 1.2 added output formats - box, unordered and ordered lists
 *		- 1.1 Simplified API, so that only the web directoy is needed, removed inline styles.
 * 		- 1.0 initial release
 *
 * Modified: 2004/09/10
 *
 * @author: Sejul Shah (sage at ss dash diy dot com)
 * @version: 1.2
 * @param array
 * @param Smarty
 * @return string of valid XHTML
 */

function smarty_function_html_dir_icons($params, &$smarty)
{
	/**
	 * @var string
	 * Web path of the directory (eg. the /dir/to/scan/ of http://www.domain.com/dir/to/scan/)
	 */
	$dir	= '';

	/**
	 * @var array
	 * Associative array of file extensions and icons to display
	 * eg (	'xls' => '/path/to/xls/icon',
	 *		'pdf' => '/path/to/pdf/image'
	 *		 etc.....
	 */
	$filetypes	= array();
	
	/**
	 * @var string
	 * Changes output format, valid types are box, ulist and olist
	 * # Boxed is where each icon (and text) set is enclosed in a span (default)
	 * # ulist displays icons and text in an unordered list
	 * # olist displays icons and text in an ordered list
	 */
	$format		= 'box';
	
	/**
	 * @var string
	 * The id of the list (for css etc.)
	 */
	$list_id	= '';
	
	/**
	 * @var bool
	 * True to produce hyperlinks for the icons
	 */
	$link		= true;
	
	/**
	 * @var bool
	 * True to show any pictures (not defined by filetypes) as unlinked pictures
	 */
	$thumb_pics	= true;
	
	extract($params);

	//sort out defaults
	$list_id = ($list_id == '') ? '' : ' id="'.$list_id.'"';

	//check paths
	if($dir == '') return;
	
	$abs_dir = $_SERVER['DOCUMENT_ROOT'].$dir;
	if (!is_dir($abs_dir)) return;
	
	//Scan Directory and puts it in an array (will want to change to scandir with php 5, so mimic behaviour)
	$file_list = array();
	
	if ($handle = opendir($abs_dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			array_push($file_list,$file);
		}
		closedir($handle);
	}
	sort($file_list);

	$full_xhtml = '';

	if ($format == 'ulist') $full_xhtml .= "<ul$list_id>\n";
	if ($format == 'olist') $full_xhtml .= "<ol$list_id>\n";
	
	// goes through directoy and builds up xhtml
	for($i = 2; $i < count($file_list); $i++)
	{
		$item_xhtml = '';

		//match things like a.silly.file.match.xls
		if (preg_match("/\.([^\.]+)$/",$file_list[$i],$matches))
		{

			if ($format == 'box') $item_xhtml .= "<span>\n";
			else $item_xhtml .= "<li>";		
		
			$ext = strtolower($matches[1]);
			//check if its in the display array
			if ( isset($filetypes[ $ext ]) )
			{
				
				if($link)
				{
					$item_xhtml .= '<a href="'.$dir.$file_list[$i].'">';
				}
				$item_xhtml .= '<img src="'.$filetypes[ $ext ].'" alt="icon of '.$file_list[$i].'" />';
				if($format == 'box') $item_xhtml .= "<br/>\n";
				$item_xhtml .= $file_list[$i];
				if($link)
				{
					$item_xhtml .= "</a>";
				}
			}
			elseif($thumb_pics)
			{
				if( $ext == 'png' || $ext == 'jpg' || $ext == 'gif' )
				{
					
					$item_xhtml .= '<img src="'.$dir.$file_list[$i].'" alt="file '.$file_list[$i].'" />';
					if($format == 'box') $item_xhtml .= "<br/>\n";
					$item_xhtml .= $file_list[$i];
					
				}
			}
			
			if ($format == 'box') $item_xhtml .= "\n</span>\n";
			else $item_xhtml .= "</li>\n";
		}
		$full_xhtml .= $item_xhtml;
	}
	
	if ($format == 'ulist') $full_xhtml .= "</ul>\n";
	if ($format == 'olist') $full_xhtml .= "</ol>\n";
	
	
	return $full_xhtml;
}
?>