<!DOCTYPE html>
<?php
include_once("../func/sql.php");
include_once("../func/url.php");
include_once("../func/consolelog.php");
include_once("../func/math.php");
?>
<head>
<meta charset="UTF-8">
<title>Math-EDAMS</title>
<?php
include_once("../res/meta.php");
meta();
?>
</head>
<body Marginwidth="-1" Marginheight="-1" Topmargin="0" Leftmargin="0">
<?php
include_once("../res/header.php");
function translen($len,$start,$differ,$reallen,$border,$onem=false){
	if($onem)return intval((1-($len-$start)/$differ)*$reallen+$border);
	else return intval(($len-$start)/$differ*$reallen+$border);
}
?>
<center>
<form action="" method="post">
<table border="0" cellspacing="0" cellpadding="5">
<tr>
	<td height="20" colspan="3"></td>
</tr>
<tr>
	<td align="center">X</td>
	<td align="center">Y</td>
	<td align="center">選項</td>
	<td align="center">趨勢線公式</td>
	<td align="center">繪圖</td>
</tr>
<tr>
	<?php
		$x=array(1,2,3,4,5,6,7);
		$y=array();
		foreach($x as $t){
			$y[]=pow($t,6);
		}
		if($_POST["x"]!=""){
			$x=$_POST["x"];
			$x=explode("\r\n",$x);
			for($i=count($x)-1;$i>=0;$i--){
				if($x[$i]=="")unset($x[$i]);
				else break;
			}
		}
		if($_POST["y"]!=""){
			$y=$_POST["y"];
			$y=explode("\r\n",$y);
			for($i=count($y)-1;$i>=0;$i--){
				if($y[$i]=="")unset($y[$i]);
				else break;
			}
		}
		$countx=count($x);
		$county=count($y);
		if($countx<$county){
			for($i=$countx;$i<$county;$i++){
				unset($y[$i]);
			}
		}else {
			for($i=$county;$i<$countx;$i++)unset($x[$i]);
		}
	?>
	<td valign="top"><textarea name="x" cols="5" rows="30"><?php foreach($x as $i => $t){echo ($i!=0?"\n":"").$t;} ?></textarea></td>
	<td valign="top"><textarea name="y" cols="5" rows="30"><?php foreach($y as $i => $t){echo ($i!=0?"\n":"").$t;} ?></textarea></td>
	<td align="center" valign="top">
		次方 <input name="power" type="number" value="<?php echo $_POST["power"]; ?>" style="width:40px"><br>
		<input name="" type="submit" value="送出">
	</td>
	<td valign="top">
	<?php
		$x_min=2147483647;
		$x_max=-2147483648;
		foreach($x as $t){
			if($t<$x_min)$x_min=$t;
			else if($t>$x_max)$x_max=$t;
		}
		$x_d=$x_max-$x_min;
		$x_min-=$x_d/20;
		$x_max+=$x_d/20;
		$x_d=$x_max-$x_min;
		$y_min=2147483647;
		$y_max=-2147483648;
		foreach($y as $t){
			if($t<$y_min)$y_min=$t;
			else if($t>$y_max)$y_max=$t;
		}
		$y_d=$y_max-$y_min;
		$y_min-=$y_d/20;
		$y_max+=$y_d/20;
		$y_d=$y_max-$y_min;
		
		$border=50;
		$imgw=500;
		$imgh=400;
		$truew=$imgw+$border*2;
		$trueh=$imgh+$border*2;
		$img = ImageCreateTrueColor($truew,$trueh);
		imagefilledrectangle($img,0,0,$truew,$trueh,imagecolorallocate($img,255,255,255));
		?>
		<table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td>次方</td>
			<td>y=</td>
			<td>R<sup>2</sup></td>
		</tr>
		<?php
		for($i=1;$i<=10;$i++){
		?>
		<tr>
		<?php
			$D=LR($x,$y,$i);
			?><td><?php echo $i; ?></td>
			<td <?php echo ($_POST["power"]!=""&&$_POST["power"]==$i?"style='color: #0000FF'":""); ?>><?php
			for($j=$i;$j>=0;$j--){
				echo ($D[$j]>=0&&$j!=$i?"+":"").round($D[$j],2).($j>0?"x":"").($j>1?"<sup>".$j."</sup>":"");
			}
			?></td><td><?php echo R2($x,$y,$D); ?></td><?php
			if($_POST["power"]!=""&&$_POST["power"]==$i)continue;
			$D=LR($x,$y,$i);
			for($j=$x_min;$j<=$x_max;$j+=$x_d/5000){
				$tempy=0;
				for($k=0;$k<=$i;$k++){
					$tempy+=$D[$k]*pow($j,$k);
				}
				imagefilledellipse($img,translen($j,$x_min,$x_d,$imgw,$border),translen($tempy,$y_min,$y_d,$imgh,$border,true),1,1,imagecolorallocate($img,255,0,0));
			}
		?>
		</tr>
		<?php
		}
		if($_POST["power"]){
			$D=LR($x,$y,$_POST["power"]);
			for($j=$x_min;$j<=$x_max;$j+=$x_d/5000){
				$tempy=0;
				for($k=0;$k<=$_POST["power"];$k++){
					$tempy+=$D[$k]*pow($j,$k);
				}
				imagefilledellipse($img,translen($j,$x_min,$x_d,$imgw,$border),translen($tempy,$y_min,$y_d,$imgh,$border,true),2,2,imagecolorallocate($img,0,0,255));
			}
		}
		?>
		</table>
		<?php
		for($i=$x_min;$i<=$x_max;$i+=($x_max-$x_min)/9){
			imagettftext($img,10,0,translen($i,$x_min,$x_d,$imgw,$border)-5,$trueh-$border+15,imagecolorallocate($img,0,0,0),"simhei.ttf",round($i,1));
			imageline($img,translen($i,$x_min,$x_d,$imgw,$border),$trueh-$border,translen($i,$x_min,$x_d,$imgw,$border),$border,imagecolorallocate($img,0,0,0));
		}
		for($i=$y_min;$i<=$y_max;$i+=($y_max-$y_min)/9){
			imagettftext($img,10,0,5,translen($i,$y_min,$y_d,$imgh,$border,true)+5,imagecolorallocate($img,0,0,0),"simhei.ttf",str_pad(round($i),6," ",STR_PAD_LEFT));
			imagettftext($img,10,0,$truew-$border+5,translen($i,$y_min,$y_d,$imgh,$border,true)+5,imagecolorallocate($img,0,0,0),"simhei.ttf",str_pad(round($i),6," ",STR_PAD_LEFT));
			imageline($img,$border,translen($i,$y_min,$y_d,$imgh,$border,true),$truew-$border,translen($i,$y_min,$y_d,$imgh,$border,true),imagecolorallocate($img,0,0,0));
		}
		foreach($x as $i => $t){
			imagefilledellipse($img,translen($t,$x_min,$x_d,$imgw,$border),translen($y[$i],$y_min,$y_d,$imgh,$border,true),5,5,imagecolorallocate($img,0,0,0));
		}
		imagepng($img,"temp.png");
		imagedestroy($img);
	?>
	</td>
	<td valign="top"><img src="temp.png"></td>
</tr>
</table>
</form>
</center>
</body>
</html>