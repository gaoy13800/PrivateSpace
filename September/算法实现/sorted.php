<?php

$arr=array(1,43,54,62,21,66,32,78,36,76,39); 

//冒泡排序

function sorted($arr){

	$count = count($arr);


	//首先呢，先把数组遍历一遍
	for ($i=0; $i < $count; $i++) { 
		
		//其次，开始冒泡

		for ($j=0; $j < $count - 1; $j++) { 
			//大小比较

			//若是正序排序， 若当前值比后一个值大替换。
			//倒序排序 若当前值比后一个值小，则替换


			if ($arr[$j] > $arr[$j + 1]) {
				$tmp = $arr[$j + 1];
				$arr[$j + 1] = $arr[$j];
				$arr[$j] = $tmp;
			}


		}

	}

	return $arr;
}

var_dump(sorted($arr));


//快速排序

function quickSort($arr){

	$count = count($arr);

	if ($count <= 1) {
		return $arr;
	}


	//开始选择标尺

	//那我们就选择第一个元素

	$baseNum = $arr[0];


    $left_array = array();
    
    $right_array = array();
	//开始遍历啦，遍历除了标尺外的所有元素，按照大小关系放到两个数组

	for ($i=1; $i < $count; $i++) { 
		
		if ($baseNum > $arr[$i]) {
			// 放入左边数组

			$left_array[] = $arr[$i];
		} else{
			//放入右边数组
			$right_array[] = $arr[$i];
		}

	}

	//再分别对左边和右边的数组进行相同的排序处理方式 ----也就是递归

	$left_array = quickSort($left_array);
	$right_array = quickSort($right_array);

	return array_merge($left_array, array($baseNum), $right_array);
}

var_dump(quickSort($arr));


//斐波那契数列

function FIbonaq($num){

	if ($num == 1) {
		return 0;
	}elseif ($num == 2) {
		return 1;
	}else{
		return FIbonaq(n - 1) + FIbonaq(n -2);
	}
}