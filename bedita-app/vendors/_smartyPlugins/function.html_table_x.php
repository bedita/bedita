<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

#### modified by xho to support TH (with attr and colspan)

/**
 * Smarty {html_table} function plugin
 *
 * Type:     function<br>
 * Name:     html_table<br>
 * Date:     Mar 17, 2004<br>
 * Purpose:  make an html table from an array of data<br>
 * Input:<br>
 *         - loop = array to loop through
 *         - cols = number of columns
 *         - rows = number of rows
 *         - table_attr = table attributes
 *         - tr_attr = table row attributes (arrays are cycled)
 *         - td_attr = table cell attributes (arrays are cycled)
 *         - trailpad = value to pad trailing cells with
 *         - vdir = vertical direction (default: "down", means top-to-bottom)
 *         - hdir = horizontal direction (default: "right", means left-to-right)
 *         - inner = inner loop (default "cols": print $loop line by line,
 *                   $loop will be printed column by column otherwise)
 *
 *
 * Examples:
 * <pre>
 * {table loop=$data}
 * {table loop=$data cols=4 tr_attr='"bgcolor=red"'}
 * {table loop=$data cols=4 tr_attr=$colors}
 * </pre>
 * @author   Monte Ohrt <monte@ispi.net>
 * @version  1.0
 * @link http://smarty.php.net/manual/en/language.function.html.table.php {html_table}
 *          (Smarty online manual)
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_html_table_x($params, &$smarty)
{
    $table_attr = 'border="1"';
    $tr_attr = '';
    $td_attr = '';
    $cols = 3;
    $rows = 3;
    $trailpad = '&nbsp;';
    $vdir = 'down';
    $hdir = 'right';
    $inner = 'cols';

	// xho
	$th = array();
	$th_attr = '';
	$th_span = array();
	$tr_spec = array();
	$tr_spec_attr = '';

    if (!isset($params['loop'])) {
        throw new SmartyException("html_table: missing 'loop' parameter");
    }

    foreach ($params as $_key=>$_value) {
        switch ($_key) {
            case 'loop':
                $$_key = (array)$_value;
                break;

            case 'cols':
            case 'rows':
                $$_key = (int)$_value;
                break;

            case 'table_attr':
            case 'trailpad':
            case 'hdir':
            case 'vdir':
                $$_key = (string)$_value;
                break;

            case 'tr_attr':
            case 'td_attr':
                $$_key = $_value;
                break;

			case 'th':
                $$_key = $_value;
			case 'th_span':
                $$_key = $_value;
			case 'th_attr':
                $$_key = $_value;

			case 'td_spec':
                $$_key = $_value;
			case 'td_spec_attr':
				$$_key = $_value;
        }
    }

	
    $loop_count = count($loop);
    if (empty($params['rows'])) {
        /* no rows specified */
        $rows = ceil($loop_count/$cols);
    } elseif (empty($params['cols'])) {
        if (!empty($params['rows'])) {
            /* no cols specified, but rows */
            $cols = ceil($loop_count/$rows);
        }
    }

    $output = "<table $table_attr>\n";

	// xho
	if (count($th)) {

		if (is_array($th)) $th = array_slice($th, 0, $cols);

		$output .= "<tr>";

		foreach ($th as $key => $colTitle) {

			$output .= "<th " . $th_attr;
			if (!empty($th_span[$key])) $output .= "colspan='" . $th_span[$key] . "'";
			$output .= ">" . $colTitle . "</th>";
			
			
		}

		$output .= "</tr>\n";
	}
	

    for ($r=0; $r<$rows; $r++) {
        $output .= "<tr" . smarty_function_html_table_cycle('tr', $tr_attr, $r) . ">\n";
        $rx =  ($vdir == 'down') ? $r*$cols : ($rows-1-$r)*$cols;

        for ($c=0; $c<$cols; $c++) {
            $x =  ($hdir == 'right') ? $rx+$c : $rx+$cols-1-$c;
            if ($inner!='cols') {
                /* shuffle x to loop over rows*/
                $x = floor($x/$cols) + ($x%$cols)*$rows;
            }

			// xho
			if (count($td_spec) && $td_spec[$x]) $td_adds = $td_spec_attr;
			else $td_adds = $td_attr;

            if ($x<$loop_count) {
                $output .= "<td" . smarty_function_html_table_cycle('td', $td_adds, $c) . ">" . $loop[$x] . "</td>\n";
            } else {
                $output .= "<td" . smarty_function_html_table_cycle('td', $td_adds, $c) . ">$trailpad</td>\n";
            }
        }
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
    
    print $output;
}

function smarty_function_html_table_cycle($name, $var, $no) {
    if(!is_array($var)) {
        $ret = $var;
    } else {
        $ret = $var[$no % count($var)];
    }
    
    return ($ret) ? ' '.$ret : '';
}


/* vim: set expandtab: */

?>