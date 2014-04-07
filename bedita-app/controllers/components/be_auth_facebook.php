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
class BeAuthFacebookComponent extends BeAuthComponent{

	public $vendorId = null;
	public $userAuth = 'facebook';

	protected $params = null;
	public $controller = null;
	protected $vendorController = null;

	function __construct(&$controller=null) {
		$this->controller = &$controller;
		$this->Session = &$controller->Session;

		$this->params = Configure::read("ext_auth_params");

		if (isset( $this->params['facebook'] ) && isset( $this->params['facebook']['kies'] )) {
			$this->vendorController = new Facebook(array(
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
		if (isset( $this->vendorController )) {
			$this->vendorId = $this->vendorController->getUser();
			if ($this->vendorId) {
				try {
					$profile = $this->vendorController->api('/me');
					if (isset($profile['email'])) {
						$be_user_object = $this->createUser($profile, 'facebook');
						return $this->login();
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
	}

	public function login($policy = null, $auth_group_name = array()) {
		if (!isset( $this->vendorController )) {
			return;
		}

		//get the user
		$this->vendorId = $this->vendorController->getUser();
		if ($this->vendorId) {
			$profile = $this->vendorController->api('/me');
			//BE user
			$user = ClassRegistry::init('User');
			$user->containLevel("minimum");
			$userid = $profile['email'];
			$u = $user->findByUserid($userid);
			if(!$this->loginPolicy($userid, $u, $policy, $auth_group_name)) {
				return false ;
			}
		} else {
			$params = array(
				'scope' => $this->params['facebook']['permissions']
			);
			$url = $this->vendorController->getLoginUrl($params);
			header('Location: ' . $url);
		}
	}

	public function createUser($profile, $authType, $notify=true) {
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

		$res['Groups'] = $groups;

		//create the BE user
		$user = ClassRegistry::init('User');
		$user->containLevel("minimum");
		$u = $user->findByUserid($res['User']['userid']);
		if(!empty($u["User"])) {
			return $u;
		}

		$this->userGroupModel($res, $groups);
		if ($notify) {
			$user->Behaviors->attach('Notify');
		}
		
		$user->create();
		if(!$user->save($res)) {
			throw new BeditaException(__("Error saving user", true), $user->validationErrors);
		}

		if ($notify) {
			$user->Behaviors->detach('Notify');
		}
 
		$u = $user->findByUserid($res['User']['userid']);
		if(!empty($u["User"])) {
			return $u;
		} else {
			return null;
		}
	}

	public function toBeCard() {
		$res = array();
		$profile = array();
		$photo = null;
		if (isset( $this->extController )) {
			$this->vendorId = $this->extController->getUser();
			if ($this->vendorId) {
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