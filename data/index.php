<!DOCTYPE html>
<?php
include_once("../func/sql.php");
include_once("../func/url.php");
include_once("../func/checklogin.php");
include_once("../func/consolelog.php");
include_once("../func/math.php");
?>
<head>
<meta charset="UTF-8">
<title>Data-EDAMS</title>
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
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class="dfromh" colspan="3">&nbsp;</td>
</tr>
<tr>
	<td align="center" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="2" align="center"><h2>選單</h2></td>
		</tr>
		<tr>
			<td valign="top">
				<?php
				$row=SELECT("*","tablelist",array(array("aval",1)),array(array("time"),array("name")),"all");
				while($temp = mfa($row) ){
					$tablelist[]=$temp["name"];
				?>
					<a href="./?table=<?php echo $temp["name"]; ?>"><?php echo $temp["name"]; ?></a><br>
				<?php
				}
				?>
			</td>
		</tr>
		</table>
	</td>
	<?php
	if(in_array($_GET["table"],$tablelist)){
	?>
	<td width="20px"></td>
	<td align="center" valign="top">
		<h2><?php echo $_GET["table"]; ?></h2>
		<table border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td colspan="3" align="center" class="datatd">時間<br>(s)</td>
			<td colspan="2" align="center" class="datatd">速率<br>(cm/s)</td>
			<td colspan="2" align="center" class="datatd">速率<sup>2</sup><br>(cm<sup>2</sup>/s<sup>2</sup>)</td>
			<td align="center" class="datatd">速率<sup>2</sup>差<br>(cm<sup>2</sup>/s<sup>2</sup>)</td>
			<td colspan="2" align="center" class="datatd">log</td>
		</tr>
		<tr>
			<td class="datatd" align="center">初</td>
			<td class="datatd" align="center">末</td>
			<td class="datatd" align="center">count</td>
			<td class="datatd" align="center">初</td>
			<td class="datatd" align="center">末</td>
			<td class="datatd" align="center">初</td>
			<td class="datatd" align="center">末</td>
			<td class="datatd" align="center"></td>
			<td class="datatd" align="center">初速</td>
			<td class="datatd" align="center">動能差</td>
		</tr>
		<?php
		$row=SELECT( "*",$_GET["table"],array(array("aval",1)),array(array("muzzle"),array("terminal")) ,"all" );
		$vmax=0;
		$vmin=1000;
		$dmax=0;
		$dmin=1000000;
		$vsum=0;
		$dsum=0;
		$count=0;
		
		$vsum_log=0;
		$dsum_log=0;
		$A=array();
		$B=array();
		while($temp = mfa($row) ){
			$count++;
			$tb=$temp["muzzle"];
			$ta=$temp["terminal"];
			$vb=10/$tb;
			$vsum+=$vb;
			$vsum_log+=log10($vb);
			$va=10/$ta;
			$vpb=pow($vb,2);
			$vpa=pow($va,2);
			$vpd=$vpb-$vpa;
			$A[]=$vb;
			$B[]=$vpd;
			$dsum+=$vpd;
			$dsum_log+=log10($vpd);
			if($vb>$vmax)$vmax=$vb;
			else if($vb<$vmin)$vmin=$vb;
			if($vpd>$dmax)$dmax=$vpd;
			else if($vpd<$dmin)$dmin=$vpd;
			if($data[$temp["muzzle"]][$temp["terminal"]]==0)$muzzlecount[$temp["muzzle"]]++;
			$data[$temp["muzzle"]][$temp["terminal"]]++;
		}
		
		
		echo "新式算法<br>";
		
		$D=LR($A,$B,1);
		echo "一次迴歸線: y=".round($D[1],2)."x".($D[0]>=0?"+":"").round($D[0],2)."<br>R<sup>2</sup>=".round(R2($A,$B,$D),4)."<br>";
		
		$D=LR($A,$B,2);
		echo "<font color='#FF0000'>二次迴歸線: y=".round($D[2],2)."x<sup>2</sup>".($D[1]>=0?"+":"").round($D[1],2)."x".($D[0]>=0?"+":"").round($D[0],2)."<br>R<sup>2</sup>=".round(R2($A,$B,$D),4)."</font><br>";
		
		
		$D=LR($A,$B,2,array(0,0));
		echo "<font color='#0000FF'>僅二次迴歸線: y=".round($D[2],2)."x<sup>2</sup>".($D[1]>=0?"+":"").round($D[1],2)."x".($D[0]>=0?"+":"").round($D[0],2)."<br>R<sup>2</sup>=".round(R2($A,$B,$D),4)."</font><br>";
		
		echo "<hr>";
		
		$D=LR1($A,$B);
		echo "一次迴歸線: y=".round($D["a"],2)."x".($D["b"]>=0?"+":"").round($D["b"],2)."<br>R<sup>2</sup>=".round($D["R2"],4)."<br>";
		$D=LR2($A,$B);
		echo "二次迴歸線: y=".round($D["a"],2)."x<sup>2</sup>".($D["b"]>=0?"+":"").round($D["b"],2)."x".($D["c"]>=0?"+":"").round($D["c"],2)."<br>R<sup>2</sup>=".round($D["R2"],4)."<br>";
		
		$vavg=$vsum/$count;
		$davg=$dsum/$count;
		
		$border=50;
		$imgw=500;
		$imgh=400;
		$truew=$imgw+$border*2;
		$trueh=$imgh+$border*2;
		$img = ImageCreateTrueColor($truew,$trueh);
		imagefilledrectangle($img,0,0,$truew,$trueh,imagecolorallocate($img,255,255,255));
		imagettftext($img,20,0,10,35,imagecolorallocate($img,0,0,0),"arial.ttf",$_GET["table"]);
		
		/*$vmax+=5;
		$vmin-=5;
		$dmax+=100;
		$dmin-=100;*/
		
		$vd=$vmax-$vmin;
		$add=10;
		for($i=ceil($vmin/$add)*$add;$i<=floor($vmax/$add)*$add;$i+=$add){
			imagettftext($img,10,0,translen($i,$vmin,$vd,$imgw,$border)-5,$trueh-$border+15,imagecolorallocate($img,0,0,0),"simhei.ttf",$i);
			imageline($img,translen($i,$vmin,$vd,$imgw,$border),$trueh-$border,translen($i,$vmin,$vd,$imgw,$border),$border,imagecolorallocate($img,0,0,0));
		}
		$dd=$dmax-$dmin;
		$add=200;
		if($dmax/200>30)$add=250;
		if($dmax/250>30)$add=500;
		for($i=ceil($dmin/$add)*$add;$i<=floor($dmax/$add)*$add;$i+=$add){
			imagettftext($img,10,0,5,translen($i,$dmin,$dd,$imgh,$border,true)+5,imagecolorallocate($img,0,0,0),"simhei.ttf",str_pad(intval($i),5," ",STR_PAD_LEFT));
			imagettftext($img,10,0,$truew-$border+5,translen($i,$dmin,$dd,$imgh,$border,true)+5,imagecolorallocate($img,0,0,0),"simhei.ttf",str_pad(intval($i),5," ",STR_PAD_LEFT));
			imageline($img,$border,translen($i,$dmin,$dd,$imgh,$border,true),$truew-$border,translen($i,$dmin,$dd,$imgh,$border,true),imagecolorallocate($img,0,0,0));
		}
		
		$D=LR2($A,$B);
		for($i=ceil($vmin*10)/10;$i<=floor($vmax*10)/10;$i+=0.1){
			imagefilledellipse($img,translen($i,$vmin,$vd,$imgw,$border),translen(pow($i,2)*$D["a"]+$i*$D["b"]+$D["c"],$dmin,$dd,$imgh,$border,true),1,1,imagecolorallocate($img,255,0,0));
		}
		$D=LR($A,$B,2,array(0,0));
		for($i=ceil($vmin*10)/10;$i<=floor($vmax*10)/10;$i+=0.1){
			imagefilledellipse($img,translen($i,$vmin,$vd,$imgw,$border),translen(pow($i,2)*$D[2]+$i*$D[0]+$D[1],$dmin,$dd,$imgh,$border,true),1,1,imagecolorallocate($img,0,0,255));
		}
		/********************************************************/
		$vmax_log=log10($vmax);
		$vmin_log=log10($vmin);
		$dmax_log=log10($dmax);
		$dmin_log=log10($dmin);
		$vavg_log=$vsum_log/$count;
		$davg_log=$dsum_log/$count;
		
		$img_log = ImageCreateTrueColor($truew,$trueh);
		imagefilledrectangle($img_log,0,0,$truew,$trueh,imagecolorallocate($img_log,255,255,255));
		imagettftext($img_log,20,0,10,35,imagecolorallocate($img_log,0,0,0),"arial.ttf",$_GET["table"]."_log");
		
		$vd_log=$vmax_log-$vmin_log;
		$add=0.1;
		for($i=ceil($vmin_log/$add)*$add;$i<=floor($vmax_log/$add)*$add;$i+=$add){
			imagettftext($img_log,10,0,translen($i,$vmin_log,$vd_log,$imgw,$border)-5,$trueh-$border+15,imagecolorallocate($img_log,0,0,0),"simhei.ttf",$i);
			imageline($img_log,translen($i,$vmin_log,$vd_log,$imgw,$border),$trueh-$border,translen($i,$vmin_log,$vd_log,$imgw,$border),$border,imagecolorallocate($img_log,0,0,0));
		}
		$dd_log=$dmax_log-$dmin_log;
		$add=0.1;
		for($i=ceil($dmin_log/$add)*$add;$i<=floor($dmax_log/$add)*$add;$i+=$add){
			imagettftext($img_log,10,0,5,translen($i,$dmin_log,$dd_log,$imgh,$border,true)+5,imagecolorallocate($img_log,0,0,0),"simhei.ttf",str_pad(($i),5," ",STR_PAD_LEFT));
			imagettftext($img_log,10,0,$truew-$border+5,translen($i,$dmin_log,$dd_log,$imgh,$border,true)+5,imagecolorallocate($img_log,0,0,0),"simhei.ttf",str_pad(($i),5," ",STR_PAD_LEFT));
			imageline($img_log,$border,translen($i,$dmin_log,$dd_log,$imgh,$border,true),$truew-$border,translen($i,$dmin_log,$dd_log,$imgh,$border,true),imagecolorallocate($img_log,0,0,0));
		}
		
		/********************************************************/
		$a=0;
		$a2=0;
		$r11=0;
		$r21=0;
		$r22=0;
		
		$a_log=0;
		$a2_log=0;
		$r11_log=0;
		$r21_log=0;
		$r22_log=0;
		
		$index=0;
		foreach($data as $bindex => $muzzle){
		?>
		<tr>
			<td rowspan="<?php echo $muzzlecount[$bindex]; ?>" align="right" class="datatd"><?php echo sprintf("%.2f",round($bindex,2)); ?></td>
		<?php
			foreach($muzzle as $aindex => $terminal){
				$total+=$terminal;
				$tb=$bindex;
				$ta=$aindex;
				$vb=10/$tb;
				$va=10/$ta;
				$vpb=pow($vb,2);
				$vpa=pow($va,2);
				$vpd=$vpb-$vpa;
				
				$a+=($vb-$vavg)*($vpd-$davg)*$terminal;
				$a2+=pow($vb-$vavg,2)*$terminal;
				$r11+=$vb*$vpd*$terminal;
				$r21+=pow($vb,2)*$terminal;
				$r22+=pow($vpd,2)*$terminal;
				
				$vb_log=log10($vb);
				$vpd_log=log10($vpd);
				$a_log+=($vb_log-$vavg_log)*($vpd_log-$davg_log)*$terminal;
				$a2_log+=pow($vb_log-$vavg_log,2)*$terminal;
				$r11_log+=$vb_log*$vpd_log*$terminal;
				$r21_log+=pow($vb_log,2)*$terminal;
				$r22_log+=pow($vpd_log,2)*$terminal;
				
				imagefilledellipse($img,translen($vb,$vmin,$vd,$imgw,$border),translen($vpd,$dmin,$dd,$imgh,$border,true),5,5,imagecolorallocate($img,0,0,0));
				
				imagefilledellipse($img_log,translen(log10($vb),$vmin_log,$vd_log,$imgw,$border),translen(log10($vpd),$dmin_log,$dd_log,$imgh,$border,true),5,5,imagecolorallocate($img_log,0,0,0));
		?>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($ta,2)); ?></td>
				<td align="right" class="datatd"><?php echo $terminal; ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vb,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($va,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vpb,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vpa,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vpd,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round(log10($vb),2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round(log10($vpd),2)); ?></td>
			</tr>
		<?php
			}
		?>
		<?php
		}
		$a/=$a2;
		$b=$davg-$a*$vavg;
		$r=( $count*$r11-$vsum*$dsum )/sqrt(( $count*$r21-pow($vsum,2) )*( $count*$r22-pow($dsum,2) ));
		imageline($img,translen($vmin,$vmin,$vd,$imgw,$border),translen($vmin*$a+$b,$dmin,$dd,$imgh,$border,true),translen($vmax,$vmin,$vd,$imgw,$border),translen($vmax*$a+$b,$dmin,$dd,$imgh,$border,true),imagecolorallocate($img,0,0,0));
		//imagettftext($img,16,0,150,35,imagecolorallocate($img,0,0,0),"simhei.ttf","迴歸線: y=".round($a,2)."x".($b>=0?"+":"").round($b,2)."   R^2=".round(pow($r,2),4));
		//echo "一次迴歸線: y=".round($a,2)."x".($b>=0?"+":"").round($b,2)."<br>R<sup>2</sup>=".round(pow($r,2),4)."<br>";
		
		$a_log/=$a2_log;
		$b_log=$davg_log-$a_log*$vavg_log;
		$r_log=( $count*$r11_log-$vsum_log*$dsum_log )/sqrt(( $count*$r21_log-pow($vsum_log,2) )*( $count*$r22_log-pow($dsum_log,2) ));
		imageline($img_log,translen($vmin_log,$vmin_log,$vd_log,$imgw,$border),translen($vmin_log*$a_log+$b_log,$dmin_log,$dd_log,$imgh,$border,true),translen($vmax_log,$vmin_log,$vd_log,$imgw,$border),translen($vmax_log*$a_log+$b_log,$dmin_log,$dd_log,$imgh,$border,true),imagecolorallocate($img_log,0,0,0));
		//imagettftext($img_log,16,0,200,35,imagecolorallocate($img_log,0,0,0),"simhei.ttf","迴歸線: y=".round($a_log,2)."x".($b_log>=0?"+":"").round($b_log,2)."   R^2=".round(pow($r_log,2),4));
		echo "log迴歸線: y=".round($a_log,2)."x".($b_log>=0?"+":"").round($b_log,2)."<br>R<sup>2</sup>=".round(pow($r_log,2),4)."<br>";
		?>
		<tr>
			<td class="datatd"></td>
			<td class="datatd"></td>
			<td align="right" class="datatd"><?php echo $total; ?></td>
			<td class="datatd"></td>
			<td class="datatd"></td>
			<td class="datatd"></td>
			<td class="datatd"></td>
			<td class="datatd"></td>
			<td class="datatd"></td>
			<td class="datatd"></td>
		</tr>
		</table>
	</td>
	<td width="20px"></td>
	<td align="center" valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" valign="top" style="border-style: solid; border-width: 1px;">
			<?php
			imagepng($img,"./".$_GET["table"].".png");
			imagedestroy($img);
			?>
			<img src="<?php echo "./".$_GET["table"].".png"; ?>">
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="border-style: solid; border-width: 1px;">
			<?php
			imagepng($img_log,"./".$_GET["table"]."_log.png");
			imagedestroy($img_log);
			?>
			<img src="<?php echo "./".$_GET["table"]."_log.png"; ?>">
			</td>
		</tr>
		</table>
	</td>
	<?php
	}
	?>
</tr>
</table>
</form>
</td>
</tr>
</table>
</center>
</body>
</html>