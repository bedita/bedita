<?php
/**
 * block.bedev.php - dev block plugin
 *
 *
 * @version		$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
 
/**
 * Smarty block: shows or hides content using a global var for devel purposes.
 * Tipically shows or hides unimplemented features...
 * {bedev}.... {/bedev}
 */
function smarty_block_bedev($params, $text, &$smarty)
{
	if (empty($text)) {
        return;
    }
	
	if(!defined('BEDITA_DEV_SYSTEM') || BEDITA_DEV_SYSTEM === false)
		return;

	return "<div class='bedev'>".$text."</div>";
}

?>