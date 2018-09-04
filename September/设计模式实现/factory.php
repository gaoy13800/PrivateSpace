<?php


// 手敲工厂模式


class MyObject{



}

class MyFactory{

	public static function factory(){
		return new MyObject();
	}

}


$instance = MyFactory::factory();

// 一个稍微复杂的工厂模式

interface Transport{
	public function go();
}

class Bus implements Transport{
	public function go(){
		echo "bus 每一站都要停";
	}
}

class Car implements Transport{
	public function go(){
		echo "car 比较快";
	}
}

class Bike implements Transport{

	public function go(){
		echo "bike比较慢";
	}

}

class transFactory{

	public static function factory($transport){

		switch ($transport) {
			case 'bus':
				return new Bus();
				break;
			
			case 'car':
				return new Car();
			case 'bike':
				return new Bike();	
			default:
				break;
		}

	}


}


$transport = transFactory::factory('car');
$transport->go();