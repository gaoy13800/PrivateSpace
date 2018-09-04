<?php


//单例模式

class SingleCase{

	public $hash;
	static protected $_instance = null;

	final protected function __construct(){
		$this->hash = rand(1, 9999);
	}

	static public function getInstance(){

		if (self::$_instance instanceof self) {
			return self::$_instance;
		}

		self::$_instance = new self();

		return self::$_instance;
	}


}