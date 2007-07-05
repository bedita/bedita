<?
/**
 * @author giangi@qwerg.com
 * 
 * Componente per la gestione dei permessi sugl' oggetti
 * 
 * I permessi sono espressi in un integer che raprresenta una combinazione 
 * di bit definiti nel file di configurazione (bedita.ini.php):
 * BEDITA_PERMS_READ	0x1
 * BEDITA_PERMS_MODIFY	0x2
 * BEDITA_PERMS_DELETE	0x4
 * BEDITA_PERMS_CREATE	0x8
 * 
 */
class PermissionComponent extends Object {
	static $SWITCH_USER		= 'user' ;
	static $SWITCH_GROUP	= 'group' ;
	
	var $Permission 		= null ;
	
	var $uses = array('Permission') ;
	
	function __construct() {
		if(!class_exists('Permission')) {
			loadModel('Permission') ;
		}
		$this->Permission = new Permission() ;
	} 
	
	/**
	 * Aggiunge 1 o + permessi a 1 o + oggetti.
	 * 
	 *
	 * @param mixed $IDs	Se un intero o stringa, e' l'ID di un oggetto solo
	 * 						se un array, {0..N} ID di oggetti
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid o nome gruppo
	 * 							switch  PermissionComponent::SWITCH_USER o PermissionComponent::SWITCH_GROUP
	 * 							flag	insieme di bit con le operazioni sopra definite
	 * @return boolean
	 */
	function add($IDs, &$perms) {
		$this->array2perms($perms, $formatedPerms) ;
		
		if(!is_array($IDs)) $IDs = array($IDs); 
		foreach ($IDs as $ID) {
			
			for($i=0; $i < count($formatedPerms) ; $i++) {
				$item = &$formatedPerms[$i] ;
				
				if($this->Permission->replace($ID, $item['name'], $item['switch'], $item['flag']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Rimuove 1 o + permessi a 1 o + oggetti.
	 * 
	 *
	 * @param mixed $IDs	Se un intero o stringa, e' l'ID di un oggetto solo
	 * 						se un array, {0..N} ID di oggetti
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid o nome gruppo
	 * 							switch  PermissionComponent::SWITCH_USER o PermissionComponent::SWITCH_GROUP
	 * @return boolean
	 */
	function remove($IDs, $perms) {
		$this->array2perms($perms, $arr) ;
		
		if(!is_array($IDs)) $IDs = array($IDs); 
		foreach ($IDs as $ID) {
			
			for($i=0; $i < count($arr) ; $i++) {
				$item = &$arr[$i] ;
				
				if($this->Permission->remove($ID, $item['name'], $item['switch']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Come add ma per tutti gli oggetti all'interno dell'abero dei contenuti
	 * dove ID  la radice. Ad esempio, un''area o una sezione intera. 
	 *
	 * @param integer $ID		ID oggetto radice albero dei contenuti
	 * @param array				{1..N} Permessi
	 * @return boolean
	 */
	function addTree($ID, &$perms) {
		$this->array2perms($perms, $formatedPerms) ;
		
		for($i=0; $i < count($formatedPerms) ; $i++) {
			$item = &$formatedPerms[$i] ;
				
			if($this->Permission->replaceTree($ID, $item['name'], $item['switch'], $item['flag']) === false) {
				return false ;
			}			
		}
		
		return true ;
	}
	
	/**
	 * Come remove ma per tutti gli oggetti all'interno dell'abero dei contenuti
	 * dove ID  la radice.
	 *
	 * @param integer $ID		ID oggetto radice albero dei contenuti
	 * @param array				{1..N} Permessi
	 * @return boolean
	 */
	function removeTree($ID, $perms) {
		$this->array2perms($perms, $arr) ;
		
		for($i=0; $i < count($arr) ; $i++) {
			$item = &$arr[$i] ;
				
			if($this->Permission->removeTree($ID, $item['name'], $item['switch']) === false) {
				return false ;
			}				
		}
		
		return true ;
	}
	
	/**
	 * Come remove ma rimuove tutti i permessi per l'oggetto/i
	 *
	 * @param integer $ID		ID oggetto radice albero dei contenuti
	 * @param array				{1..N} Permessi
	 * @return boolean
	 */
	function removeAll($IDs) {
		if(!is_array($IDs)) $IDs = array($IDs); 
		foreach ($IDs as $ID) {
			if($this->Permission->removeAll($ID) === false) {
				return false ;
			}				
		}
		
		return true ;
	}
	
	/**
	 * Come removeAll ma per tutti gli oggetti all'interno dell'abero dei contenuti
	 * dove ID  la radice.
	 *
	 * @param integer $ID		ID oggetto radice albero dei contenuti
	 * @return boolean
	 */
	function removeAllTree($ID) {
		if($this->Permission->removeAllTree($ID) === false) {
			return false ;
		}				
		
		return true ;
	}
	
	/**
	 * Carica i permessi per un dato oggetto
	 *
	 * @param integer $ID	Oggetto
	 * @return array $perms	Permessi individuati o FALSE
	 */
	function load($ID) {
		if(($perms = $this->Permission->findAllByObjectId($ID)) === false) return false ;
		
		$this->perms2arr($perms, $arr) ;
		
		return $arr ;
	}
	
	/**
	 * Torna true se l'operazione richiesta  permesse su un dato
	 * oggetto per un dato utente interno al sistema.
	 *
	 * @param integer $ID		Oggetto da verificare.
	 * @param string $userid	Userid utente da verificare
	 * @param integer $op		Operazione richiesta
	 * @return boolean
	 */
	function verify($ID, $userid, $op) {
		 return $this->Permission->permsByUserid($userid, $ID, $op) ;
	}
	
	/**
	 * Torna true se l'operazione richiesta  permesse su un dato
	 * oggetto per un dato gruppo di utenti.
	 *
	 * @param integer $ID		Oggetto da verificare.
	 * @param string $groupid	NOme gruppo da verificare
	 * @param integer $op		Operazione richiesta
	 * @return boolean
	 */
	function verifyGroup($ID, $groupid, $op) {
		 return $this->Permission->permsByGroup($groupid, $ID, $op) ;
	}
	
	/**
	 * Torna i permessi di default (se ci sono) per un dato tipo di 
	 * oggetto.
	 *
	 * @param integer $objectType	Tipo di oggetto in $conf->objectTypes
	 * @param array $perms			{0..N} dove torna il risultato
	 * @return array $perms			{0..N} dove torna il risultato 
	 */
	function getDefaultByType($objectType) {
		$conf  		= Configure::getInstance() ;
		
		if(isset($conf->permissions[$objectType])) 	return $conf->permissions[$objectType] ;
		else if(isset($conf->permissions['all'])) 	return $conf->permissions['all'] ;
		
		return array() ;
	}
	
	/**
	 * Trasforma un array in un array associativo x Cake
	 *
	 * @param array $arr	{0..N} item:
	 * 						0:ugid, 1:switch, 2:flag 
	 * @param array $perms	dove torna l'array associativo:
	 * 						ugid => ; switch => ; flag => 
	 */
	private function array2perms(&$arr, &$perms) {
		$perms = array() ;
		if(!count($arr))  return ;

		foreach ($arr as $item) {
			$perms[] = array(
					'name'		=> $item[0],
					'switch'	=> $item[1],
					'flag'		=> (isset($item[2]))?$item[2]:null,
			) ;
		}
	}
	
	private function perms2arr(&$perms, &$arr) {
		$arr = array() ;
		foreach ($perms as $item) {
			$arr[] = array(
				(($item['Permission']['switch'] == PermissionComponent::$SWITCH_USER)? $item['User']['userid'] : $item['Group']['name']),
				$item['Permission']['switch'],
				$item['Permission']['flag']
			) ;
		}
	}

}

?>