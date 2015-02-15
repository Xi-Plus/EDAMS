<!DOCTYPE html>
<?php
include_once("../func/sql.php");
include_once("../func/url.php");
include_once("../func/checklogin.php");
include_once("../func/consolelog.php");
?>
<head>
<meta charset="UTF-8">
<title>Data-EDAMS</title>
<link href="../res/css.css" rel="stylesheet" type="text/css">
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
				$row=SELECT("*","tablelist",null,null,"all");
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
			<td colspan="3" align="center" class="datatd">時間</td>
			<td colspan="2" align="center" class="datatd">速率</td>
			<td colspan="2" align="center" class="datatd">速率平方</td>
			<td align="center" class="datatd">速率差</td>
		</tr>
		<tr>
			<td class="datatd">before</td>
			<td class="datatd">after</td>
			<td class="datatd">count</td>
			<td class="datatd">before</td>
			<td class="datatd">after</td>
			<td class="datatd">before</td>
			<td class="datatd">after</td>
			<td class="datatd"></td>
		</tr>
		<?php
		$row=SELECT( "*",$_GET["table"],null,array(array("before","ASC"),array("after","ASC")) ,"all" );
		$vmax=0;
		$vmin=1000;
		$dmax=0;
		$dmin=1000000;
		$vsum=0;
		$dsum=0;
		$count=0;
		while($temp = mfa($row) ){
			$count++;
			$tb=$temp["before"];
			$ta=$temp["after"];
			$vb=10/$tb;
			$vsum+=$vb;
			$va=10/$ta;
			$vpb=pow($vb,2);
			$vpa=pow($va,2);
			$vpd=$vpb-$vpa;
			$dsum+=$vpd;
			if($vb>$vmax)$vmax=$vb;
			else if($vb<$vmin)$vmin=$vb;
			if($vpd>$dmax)$dmax=$vpd;
			else if($vpd<$dmin)$dmin=$vpd;
			if($data[$temp["before"]][$temp["after"]]==0)$beforecount[$temp["before"]]++;
			$data[$temp["before"]][$temp["after"]]++;
		}
		$vavg=$vsum/$count;
		$davg=$dsum/$count;
		
		$border=50;
		$imgw=600;
		$imgh=400;
		$truew=$imgw+$border*2;
		$trueh=$imgh+$border*2;
		$img = ImageCreateTrueColor($truew,$trueh);
		imagefilledrectangle($img,0,0,$truew,$trueh,imagecolorallocate($img,255,255,255));
		imagettftext($img,20,0,100,35,imagecolorallocate($img,0,0,0),"arial.ttf",$_GET["table"]);
		
		/*$vmax+=5;
		$vmin-=5;
		$dmax+=100;
		$dmin-=100;*/
		
		$vd=$vmax-$vmin;
		$add=10;
		for($i=ceil($vmin/$add)*$add;$i<=floor($vmax/$add)*$add;$i+=10){
			imagettftext($img,10,0,translen($i,$vmin,$vd,$imgw,$border)-5,$trueh-$border+15,imagecolorallocate($img,0,0,0),"simhei.ttf",$i);
			imageline($img,translen($i,$vmin,$vd,$imgw,$border),$trueh-$border,translen($i,$vmin,$vd,$imgw,$border),$border,imagecolorallocate($img,0,0,0));
		}
		$dd=$dmax-$dmin;
		$add=200;
		if($dmax/200>20)$add=250;
		for($i=ceil($dmin/$add)*$add;$i<=floor($dmax/$add)*$add;$i+=$add){
			imagettftext($img,10,0,5,translen($i,$dmin,$dd,$imgh,$border,true)+5,imagecolorallocate($img,0,0,0),"simhei.ttf",str_pad(intval($i),5," ",STR_PAD_LEFT));
			imagettftext($img,10,0,$truew-$border+5,translen($i,$dmin,$dd,$imgh,$border,true)+5,imagecolorallocate($img,0,0,0),"simhei.ttf",str_pad(intval($i),5," ",STR_PAD_LEFT));
			imageline($img,$border,translen($i,$dmin,$dd,$imgh,$border,true),$truew-$border,translen($i,$dmin,$dd,$imgh,$border,true),imagecolorallocate($img,0,0,0));
		}
		
		$a=0;
		$a2=0;
		$r11=0;
		$r21=0;
		$r22=0;
		$index=0;
		foreach($data as $bindex => $before){
		?>
		<tr>
			<td rowspan="<?php echo $beforecount[$bindex]; ?>" align="right" class="datatd"><?php echo sprintf("%.2f",round($bindex,2)); ?></td>
		<?php
			foreach($before as $aindex => $after){
				$total+=$after;
				$tb=$bindex;
				$ta=$aindex;
				$vb=10/$tb;
				$va=10/$ta;
				$vpb=pow($vb,2);
				$vpa=pow($va,2);
				$vpd=$vpb-$vpa;
				$a+=($vb-$vavg)*($vpd-$davg)*$after;
				$a2+=pow($vb-$vavg,2)*$after;
				$r11+=$vb*$vpd*$after;
				$r21+=pow($vb,2)*$after;
				$r22+=pow($vpd,2)*$after;
				imagefilledellipse($img,translen($vb,$vmin,$vd,$imgw,$border),translen($vpd,$dmin,$dd,$imgh,$border,true),5,5,imagecolorallocate($img,0,0,0));
		?>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($ta,2)); ?></td>
				<td align="right" class="datatd"><?php echo $after; ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vb,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($va,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vpb,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vpa,2)); ?></td>
				<td align="right" class="datatd"><?php echo sprintf("%.2f",round($vpd,2)); ?></td>
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
		imagettftext($img,16,0,250,35,imagecolorallocate($img,0,0,0),"simhei.ttf","迴歸線: y=".round($a,2)."x".($b>=0?"+":"").round($b,2)."   R^2=".round(pow($r,2),4));
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