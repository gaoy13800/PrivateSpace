<?php



function getKing($m, $n){

	$monkeys = range(1, $n);

	$i = 0;
	$k = $n;

	while (count($monkeys) > 1) {
		if (($i + 1) % $n ==0) {
			unset($monkeys[$i]);
		} else{
			array_push($monkeys, $monkeys[$i]);
			unset($monkeys[$i]);
		}

		$i++;
	}

	return current($monkeys); //返回当前指针指向的元素
	
}


//汉诺塔

function hanoi($n, $x, $y, $z){

	if ($n == 1) {
		echo 'move disk 1 from ' . $x . ' to ' . $z . "\n";
	}else{

		hanoi($n -1, $x, $z, $y);

		 echo 'move disk '.$n.' from '.$x.' to '.$z."\n";

		 hanoi($n-1, $y, $x, $z)

	}

}

//二分查找

function bin_sch($array, $low, $high, $k){

	if ($low <= $high) {
		$mid = intval(($low+$high)/2);

		if ($array[$mid] == $k) {
			return $mid;
		}elseif ($k < $array[$mid]) {
			return bin_sch($array, $low, $mid - 1, $k)
		}
	}{
		return bin_sch($array, $mid + 1, $high, $k)
	}


	return -1;
}