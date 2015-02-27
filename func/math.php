<?php
function LR1($x,$y){
	$nx=0;
	$sum["x"]=0;
	foreach($x as $temp){
		$nx++;
		$data[$nx]["x"]=$temp;
		$sum["x"]+=$data[$nx]["x"];
	}
	$avg["x"]=$sum["x"]/$nx;
	
	$ny=0;
	$sum["y"]=0;
	foreach($y as $temp){
		$ny++;
		$data[$ny]["y"]=$temp;
		$sum["y"]+=$data[$ny]["y"];
	}
	$avg["y"]=$sum["y"]/$ny;
	
	if($nx!=$ny){
		echo "ERR";
		return false;
	}
	$n=$nx;
	
	for($i=1;$i<=$n;$i++){
		$data[$i]["x*"]=$data[$i]["x"]-$avg["x"];
	}
	for($i=1;$i<=$n;$i++){
		$data[$i]["y*"]=$data[$i]["y"]-$avg["y"];
	}
	$sum["x*x*"]=0;
	$sum["y*y*"]=0;
	$sum["x*y*"]=0;
	$sum["xy"]=0;
	$sum["xx"]=0;
	$sum["yy"]=0;
	for($i=1;$i<=$n;$i++){
		$data[$i]["x*y*"]=$data[$i]["x*"]*$data[$i]["y*"];
		$data[$i]["x*x*"]=pow($data[$i]["x*"],2);
		$data[$i]["y*y*"]=pow($data[$i]["y*"],2);
		$sum["x*y*"]+=$data[$i]["x*y*"];
		$sum["x*x*"]+=$data[$i]["x*x*"];
		$sum["y*y*"]+=$data[$i]["y*y*"];
		$data[$i]["xy"]=$data[$i]["x"]*$data[$i]["y"];
		$data[$i]["xx"]=pow($data[$i]["x"],2);
		$data[$i]["yy"]=pow($data[$i]["y"],2);
		$sum["xy"]+=$data[$i]["xy"];
		$sum["xx"]+=$data[$i]["xx"];
		$sum["yy"]+=$data[$i]["yy"];
	}
	$avg["x*y*"]=$sum["x*y*"]/$n;
	$avg["x*x*"]=$sum["x*x*"]/$n;
	$avg["y*y*"]=$sum["y*y*"]/$n;
	$avg["xy"]=$sum["xy"]/$n;
	$avg["xx"]=$sum["xx"]/$n;
	$avg["yy"]=$sum["yy"]/$n;
	

	$return["a"]=($sum["x*y*"])/($sum["x*x*"]);
	$return["b"]=$avg["y"]-$return["a"]*$avg["x"];
	$return["r"]=($n*$sum["xy"]-$sum["x"]*$sum["y"])/sqrt(($n*$sum["xx"]-pow($sum["x"],2))*($n*$sum["yy"]-pow($sum["y"],2)));
	$Ra=0;
	$Rb=0;
	for($i=1;$i<=$n;$i++){
		$Ra+=pow($data[$i]["x"]*$return["a"]+$return["b"]-$data[$i]["y"],2);
		$Rb+=pow($data[$i]["y"]-$avg["y"],2);
	}
	$return["R2"]=1-$Ra/$Rb;
	return $return;
}
function LR2($x,$y){
	$nx=0;
	$sum["x"]=0;
	foreach($x as $temp){
		$nx++;
		$data[$nx]["x"]=$temp;
		$sum["x"]+=$data[$nx]["x"];
	}
	$avg["x"]=$sum["x"]/$nx;
	
	$ny=0;
	$sum["y"]=0;
	foreach($y as $temp){
		$ny++;
		$data[$ny]["y"]=$temp;
		$sum["y"]+=$data[$ny]["y"];
	}
	$avg["y"]=$sum["y"]/$ny;
	
	if($nx!=$ny){
		echo "ERR";
		return false;
	}
	$n=$nx;
	
	$sum["x2"]=0;
	$sum["xy"]=0;
	$sum["x3"]=0;
	$sum["x2y"]=0;
	$sum["x4"]=0;
	for($i=1;$i<=$n;$i++){
		$data[$i]["x2"]=pow($data[$i]["x"],2);
		$data[$i]["xy"]=$data[$i]["x"]*$data[$i]["y"];
		$data[$i]["x3"]=pow($data[$i]["x"],3);
		$data[$i]["x2y"]=pow($data[$i]["x"],2)*$data[$i]["y"];
		$data[$i]["x4"]=pow($data[$i]["x"],4);
		$sum["x2"]+=$data[$i]["x2"];
		$sum["xy"]+=$data[$i]["xy"];
		$sum["x3"]+=$data[$i]["x3"];
		$sum["x2y"]+=$data[$i]["x2y"];
		$sum["x4"]+=$data[$i]["x4"];
	}
	$avg["x2"]=$sum["x2"]/$n;
	$avg["xy"]=$sum["xy"]/$n;
	$avg["x3"]=$sum["x3"]/$n;
	$avg["x2y"]=$sum["x2y"]/$n;
	$avg["x4"]=$sum["x4"]/$n;
	
	$S["xx"]=$sum["x2"]-pow($sum["x"],2)/$n;
	$S["xy"]=$sum["xy"]-$sum["x"]*$sum["y"]/$n;
	$S["xx2"]=$sum["x3"]-$sum["x"]*$sum["x2"]/$n;
	$S["x2y"]=$sum["x2y"]-$sum["x2"]*$sum["y"]/$n;
	$S["x2x2"]=$sum["x4"]-pow($sum["x2"],2)/$n;
	
	$return["a"]=($S["x2y"]*$S["xx"]-$S["xy"]*$S["xx2"])/($S["xx"]*$S["x2x2"]-pow($S["xx2"],2));
	$return["b"]=($S["xy"]*$S["x2x2"]-$S["x2y"]*$S["xx2"])/($S["xx"]*$S["x2x2"]-pow($S["xx2"],2));
	$return["c"]=$sum["y"]/$n-$return["b"]*$sum["x"]/$n-$return["a"]*$sum["x2"]/$n;
	$Ra=0;
	$Rb=0;
	for($i=1;$i<=$n;$i++){
		$Ra+=pow(pow($data[$i]["x"],2)*$return["a"]+$data[$i]["x"]*$return["b"]+$return["c"]-$avg["y"],2);
		$Rb+=pow($data[$i]["y"]-$avg["y"],2);
	}
	$return["R2"]=$Ra/$Rb;
	
	return $return;
}
function LR($x,$y,$p=1,$dy=false){
	$nx=0;
	$sum["x"]=0;
	foreach($x as $temp){
		$nx++;
		$data[$nx]["x"]=$temp;
		$sum["x"]+=$data[$nx]["x"];
	}
	$avg["x"]=$sum["x"]/$nx;
	
	$ny=0;
	$sum["y"]=0;
	foreach($y as $temp){
		$ny++;
		$data[$ny]["y"]=$temp;
		$sum["y"]+=$data[$ny]["y"];
	}
	$avg["y"]=$sum["y"]/$ny;
	
	if($nx!=$ny){
		echo "ERR";
		return false;
	}
	$n=$nx;
	if($dy!==false){
		for($i=count($dy);$i<=$p;$i++)$dy[$i]=false;
	}
	
	for($i=1;$i<=$n;$i++){
		for($j=$p;$j>=0;$j--){
			for($k=$p;$k>=0;$k--){
				if($j>=$k){
					$t1[$j+1][$k+1]+=pow($data[$i]["x"],$j)*pow($data[$i]["x"],$k);
				}else {
					$t1[$k+1][$j+1]+=pow($data[$i]["x"],$j)*pow($data[$i]["x"],$k);
				}
			}
			$t1[$j+1][0]+=pow($data[$i]["x"],$j)*$data[$i]["y"]*-1;
		}
		for($j=$p;$j>=0;$j--){
			$t1[$j+1][0]+=$data[$i]["y"]*-1*pow($data[$i]["x"],$j);
		}
	}
	$t2x=0;
	for($i=$p+1;$i>=1;$i--){
		$t2x++;
		$t2y=0;
		for($j=$p+1;$j>$i;$j--){
			$t2y++;
			if($i==$j)$t2[$t2x][$t2y]=($t1[$j][$i]*2);
			else $t2[$t2x][$t2y]=$t1[$j][$i];
		}
		for($j=$i;$j>=1;$j--){
			$t2y++;
			if($i==$j)$t2[$t2x][$t2y]=($t1[$i][$j]*2);
			else $t2[$t2x][$t2y]=$t1[$i][$j];
		}
		$t2[$t2x][0]=($t1[$i][0]*-1);
	}
	if($dy!==false){
		for($i=0;$i<=$p;$i++){
			if($dy[$i]!==false){
				for($j=1;$j<=$t2y;$j++){
					$t2[$p-$i+1][$j]=0;
				}
				$t2[$p-$i+1][$p-$i+1]=1;
				$t2[$p-$i+1][0]=$dy[$i];
			}
		}
	}
	for($i=1;$i<$t2x;$i++){
		for($j=$i+1;$j<=$t2x;$j++){
			if($t2[$j][$i]==0)continue;
			for($k=$i+1;$k<=$t2y;$k++){
				$t2[$j][$k]-=$t2[$i][$k]*($t2[$j][$i]/$t2[$i][$i]);
			}
			$t2[$j][0]-=$t2[$i][0]*($t2[$j][$i]/$t2[$i][$i]);
			$t2[$j][$i]=0;
		}
	}
	for($i=$t2x;$i>1;$i--){
		for($j=$i-1;$j>=1;$j--){
			$t2[$j][0]-=$t2[$i][0]*($t2[$j][$i]/$t2[$i][$i]);
			$t2[$j][$i]=0;
		}
	}
	for($i=1;$i<=$t2x;$i++){
		$LR[$p-$i+1]=$t2[$i][0]/$t2[$i][$i];
	}
	$LR["power"]=$p;
	return $LR;
}

function R2($x,$y,$LR){
	$nx=0;
	$sum["x"]=0;
	foreach($x as $temp){
		$nx++;
		$data[$nx]["x"]=$temp;
		$sum["x"]+=$data[$nx]["x"];
	}
	$avg["x"]=$sum["x"]/$nx;
	
	$ny=0;
	$sum["y"]=0;
	foreach($y as $temp){
		$ny++;
		$data[$ny]["y"]=$temp;
		$sum["y"]+=$data[$ny]["y"];
	}
	$avg["y"]=$sum["y"]/$ny;
	
	if($nx!=$ny){
		echo "ERR";
		return false;
	}
	$n=$nx;
	
	$Ra=0;
	$Rb=0;
	for($i=1;$i<=$n;$i++){
		$tempy=0;
		for($j=0;$j<=$LR["power"];$j++){
			$tempy+=pow($data[$i]["x"],$j)*$LR[$j];
		}
		$Ra+=pow($data[$i]["y"]-$tempy,2);
		$Rb+=pow($data[$i]["y"]-$avg["y"],2);
	}
	return 1-$Ra/$Rb;
}
?>