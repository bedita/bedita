<?php
/**
 *
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
*/
class Module extends BEAppModel
{
	var $components = array('BeAuth');
	
	/**
	 * Torna la lista dei moduli con un flag che indica l'accessibilitˆ da parte dell'utente
	 *
	 * @param integer $userID	utente da verificare
	 */
	function generateListEnambledModules($userID = 0) {
		$recordset = $this->execTemplate("listModulesOfUser.sql", $dati = array("userID" => $userID)) ;

		// Formatta il record set da tornare
		for ($i =0; $i < count($recordset); $i++) {
			$recordset[$i] = $this->am($recordset[$i]);
		}
	
		return $recordset ;
	}
}
?>
