<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2013 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/
if(class_exists('Facebook') != true) {
	require(BEDITA_CORE_PATH . DS . "vendors" . DS . 'social' . DS . 'facebook' . DS . 'facebook.php');
}

/**
 * Facebook User auth component
*/
class BeAuthFacebookComponent extends BeAuthComponent {

	public $components = array("BeAuth");
	public $userid = null;
	public $userAuth = 'bedita';

	protected $params = null;
	protected $extController = null;

	/**
	 * startup component
	 * @param Controller $controller
	 */
	public function startup(&$controller=null) {
		$this->Controller = &$controller;
		$this->Session = &$controller->Session;

		foreach ($this->components as $comp) {
			if (!isset($this->$comp)) {
				App::import('Component', $comp);
				$componentName = $comp . "Component";
				$this->{$comp} = new $componentName($this->Controller) ;
				$this->{$comp}->initialize($this->Controller);
			}
		}

		$this->params = Configure::read("ext_auth_params");

		if (isset( $this->params['facebook'] ) && isset( $this->params['facebook']['kies'] )) {
			$this->extController = new Facebook(array(
				'appId'  => $this->params['facebook']['kies']['appId'],
				'secret' => $this->params['facebook']['kies']['secret'],
				'cookie' => true
			));
		}

		if($this->checkSessionKey()) {
			$this->user = $this->Session->read($this->sessionKey);
		}
		
		$this->controller->set($this->sessionKey, $this->user);
	}

	protected function checkSessionKey() {
		if (!parent::checkSessionKey()) {
			if (isset( $this->extController )) {
				$this->userid = $this->extController->getUser();
				if ($this->userid) {
					$this->userAuth = 'facebook';
					try {
						$profile = $this->extController->api('/me');
						if (isset($profile['email'])) {
							$be_user_object = $this->toBeUser($profile, 'facebook');
							return $this->login($be_user_object['email'], 'facebook', null, $be_user_object['groups']);
						}
					} catch (FacebookApiException $e) {
						$this->log("Facebook login failed, error: " . $e, 'info');
						return false;
					}
				}
				return false;
			} else {
				return false;
			}
		} else {
			return true;
		}			
	}

	/**
	 * User authentication
	 *
	 * @param string $userid
	 * @param string $authType
	 * @param array $policy (could contain parameters like maxLoginAttempts,maxNumDaysInactivity,maxNumDaysValidity)
	 * @param array $auth_group_name
	 * @return boolean 
	 */
	public function login($userid, $authType, $policy = null, $auth_group_name=array()) {
		$userModel = ClassRegistry::init('User');
		$conditions = array(
			"User.userid" => $userid,
			"User.auth_type" => $authType,
		);
		
		$userModel->containLevel("default");
		$u = $userModel->find($conditions);
		if(!$this->loginPolicy($userid, $u, $policy, $auth_group_name)) {
			return false ;
		}
		$userModel->compact($u) ;
		$this->user = $u;
		$this->setSessionVars();
		
		return true ;
	}

	protected function facebookLogin() {
		if (!isset( $this->extController )) {
			return;
		}

		//get the user
		$this->userid = $this->extController->getUser();
		if ($this->user) {
			return true;
		} else {
			$params = array(
				'scope' => $this->params['facebook']['permissions']
			);
			$url = $this->extController->getLoginUrl($params);
			header('Location: ' . $url);
		}
	}

	public function createUser($userData, $groups=NULL, $notify=true) {
		$user = ClassRegistry::init('User');
		$user->containLevel("minimum");
		$u = $user->findByUserid($userData['User']['userid']);
		if(!empty($u["User"])) {
			return $u["User"]['id'];
		}

		$this->userGroupModel($userData, $groups);
		if ($notify) {
			$user->Behaviors->attach('Notify');
		}
		
		$user->create();
		if(!$user->save($userData)) {
			throw new BeditaException(__("Error saving user", true), $user->validationErrors);
		}
		if ($notify) {
			$user->Behaviors->detach('Notify');
		}
		return $user->id;
	}

	protected function toBeUser($profile, $authType) {
		//create the data array
		$res = array();
		$res['User'] = array(
			'userid' => $profile['email'],
			'email' => $profile['email'],
			'realname' => $profile['name'],
			'auth_type' => $authType,
			'auth_params' => array(
				'userid' => $profile['id']
			)
		);

		$groups = array();
		if (!empty($this->params['facebook']['groups'])) {
			foreach ($this->params['facebook']['groups'] as $key => $value) {
				array_push($groups, $value);
			}
		}

		//create the BE user
		$user = ClassRegistry::init('User');
		$user->containLevel("minimum");
		$u = $user->findByUserid($res['User']['userid']);
		if(!empty($u["User"])) {
			return $u["User"]['id'];
		}

		$this->userGroupModel($res, $groups);
		if ($notify) {
			$user->Behaviors->attach('Notify');
		}
		
		$user->create();
		if(!$user->save($userData)) {
			throw new BeditaException(__("Error saving user", true), $user->validationErrors);
		}
		if ($notify) {
			$user->Behaviors->detach('Notify');
		}
 
		return $user;
	}

	public function toBeCard() {
		$res = array();
		$profile = array();
		$photo = null;
		if (isset( $this->extController )) {
			$this->userid = $this->extController->getUser();
			if ($this->userid) {
				$this->userAuth = 'facebook';
				try {
					$profile = $this->extController->api('/me');
					$photo = $this->extController->api(
						'/me/picture',
						"GET",
					    array(
					        'redirect' => false,
					        'height' => '200',
					        'type' => 'normal',
					        'width' => '200',
					    )
					);
				} catch (FacebookApiException $e) {
					$this->log("Facebook login failed, error: " . $e, 'info');
					return false;
				}
			}
		}

		$res = array(
			'title' => $profile['name'],
			'email' => $profile['email'],
			'name' => $profile['first_name'],
			'surname' => $profile['last_name'],
			'birthdate' => $profile['birthday'],
			'gender' => $profile['gender']
		);

		if ($photo) {
			$res['avatar'] = $photo['data']['url'];
		}
 
		return $res;
	}
}
?>