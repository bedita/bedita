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
 * 
 */
class BePermissionModuleComponent extends Object {
	const SWITCH_USER		= 'user' ;
	const SWITCH_GROUP	   = 'group' ;
	
	var $controller			= null ;
	var $Permission 		= null ;
	
	var $PermissionModule	= null ;
	private $groupModel	= null ;
	
	function __construct() {
		if(!class_exists('PermissionModule')) 	loadModel('PermissionModule') ;
		if(!class_exists('Group')) loadModel('Group') ;
		
		$this->PermissionModule = new PermissionModule() ;
		$this->groupModel = new Group() ;
		parent::__construct() ;
		
	} 
	
	
	/**
	 * Torna l'elenco dei moduli a cui l'utente puo' accedere
	 *
	 * @param string $userid	utente che vuole accedere
	 * @param boolean $all		se false solo  i moduli a cui ha accesso (BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY)
	 * 							e status = 'on'
	 * @return array|false
	 */
	function getListModules($userid, $all = false) {
		
		$condition 	=  "prmsModuleUserByID('{$userid}', Module.path, " . (BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) . ")" ;
//		$modules 	= $this->PermissionModule->Module->findAll($condition) ;
		
		$sql 		=  "SELECT *, {$condition} as flag FROM modules AS Module WHERE prmsModuleUserByID('{$userid}', Module.path, " . (BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) . ")" ;
		$modules 	= $this->PermissionModule->query($sql);
		
		for ($i=0; $i < count($modules) ; $i++) {
			$modules[$i]  = $this->PermissionModule->am($modules[$i]) ;
		}
//		$ret = $this->PermissionModule->execute("SELECT prmsModuleUserByID('{$userid}', 'areas', " . (BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) . ")");
/*
		// TEST 
		$modules = array(
			array(
				'id'		=> 1,
				'label'		=> 'admin',
				'path'		=> 'admin',
				'color'		=> '#000000',
				'status'	=> 'on',
				'flag'		=> 3,
			),

			array(
				'id'		=> 2,
				'label'		=> 'areas',
				'path'		=> 'areas',
				'color'		=> '#ff9933',
				'status'	=> 'on',
				'flag'		=> 3,
			),
		) ;
*/
		return $modules ;
	}
	
	public function getPermissionModulesForGroup($groupId) {
		$conditions=array("ugid"=>$groupId, "switch"=>self::SWITCH_GROUP);
		return $this->PermissionModule->findAll($conditions);
	}

/**
 * change module permissions for a group
 *
 * @param $groupId
 * @param $moduleFlags array ('module' => flag,....)
 */
	public function updateGroupPermission($groupId, $moduleFlags) {
		$conditions=array("ugid"=>$groupId, "switch"=>self::SWITCH_GROUP);
		$this->PermissionModule->deleteAll($conditions);
	  	
		$g = $this->groupModel->findById($groupId);
		$groupName = $g['Group']['name'];
		foreach ($moduleFlags as $mod=>$flag) {
			$perms =  array(array($groupName, self::SWITCH_GROUP, $flag));
			$this->add($mod, $perms);
		}
	}
	
	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
	}
	
	/**
	 * Aggiunge 1 o + permessi a 1 o + moduli.
	 * 
	 *
	 * @param mixed $names	Se una stringa, e' il nome di modulo solo
	 * 						se un array, {0..N} nomi di moduli
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid o nome gruppo
	 * 							switch  PermissionComponent::SWITCH_USER o PermissionComponent::SWITCH_GROUP
	 * 							flag	insieme di bit con le operazioni sopra definite
	 * @return boolean
	 */
	function add($names, &$perms) {
		$this->array2perms($perms, $formatedPerms) ;
		
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			
			for($i=0; $i < count($formatedPerms) ; $i++) {
				$item = &$formatedPerms[$i] ;
				
				if($this->PermissionModule->replace($name, $item['name'], $item['switch'], $item['flag']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Rimuove 1 o + permessi a 1 o + moduli.
	 * 
	 *
	 * @param mixed $names	Se una stringa, e' il nome di modulo solo
	 * 						se un array, {0..N} nomi di moduli
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid o nome gruppo
	 * 							switch  PermissionComponent::SWITCH_USER o PermissionComponent::SWITCH_GROUP
	 * @return boolean
	 */
	function remove($names, $perms) {
		$this->array2perms($perms, $arr) ;
		
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			
			for($i=0; $i < count($arr) ; $i++) {
				$item = &$arr[$i] ;
				
				if($this->PermissionModule->remove($name, $item['name'], $item['switch']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Come remove ma rimuove tutti i permessi per i moduli
	 *
	 * @param mixed $names	Se una stringa, e' il nome di modulo solo
	 * 						se un array, {0..N} nomi di moduli
	 * @param array				{1..N} Permessi
	 * @return boolean
	 */
	function removeAll($names) {
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			if($this->PermissionModule->removeAll($name) === false) {
				return false ;
			}				
		}
		
		return true ;
	}
	
	/**
	 * Carica i permessi per un dato modulo
	 *
	 * @param string $name	Modulo
	 * @return array $perms	Permessi individuati o FALSE
	 */
	function load($name) {
		$condition = "Module.label = '{$name}'" ;
		if(($perms = $this->PermissionModule->findAll($condition)) === false) return false ;
		
		$this->perms2arr($perms, $arr) ;
		
		return $arr ;
	}
	
	/**
	 * Torna true se l'operazione richiesta  permesse su un dato
	 * modulo per un dato utente interno al sistema.
	 *
	 * @param string $name		Modulo da verificare.
	 * @param string $userid	Userid utente da verificare
	 * @param integer $op		Operazione richiesta
	 * @return boolean
	 */
	function verify($name, $userid, $op) {
		 return $this->PermissionModule->permsByUserid($userid, $name, $op) ;
	}
	
	/**
	 * Torna true se l'operazione richiesta  permesse su un dato
	 * oggetto per un dato gruppo di utenti.
	 *
	 * @param string $name		Modulo da verificare.
	 * @param string $groupid	NOme gruppo da verificare
	 * @param integer $op		Operazione richiesta
	 * @return boolean
	 */
	function verifyGroup($name, $groupid, $op) {
		 return $this->PermissionModule->permsByGroup($groupid, $name, $op) ;
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
				(($item['PermissionModule']['switch'] == BePermissionModuleComponent::SWITCH_USER)? $item['User']['userid'] : $item['Group']['name']),
				$item['PermissionModule']['switch'],
				$item['PermissionModule']['flag']
			) ;
		}
	}

}

?>