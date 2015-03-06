<!DOCTYPE html>
<?php
include_once("../func/sql.php");
include_once("../func/url.php");
include_once("../func/checklogin.php");
include_once("../func/consolelog.php");
?>
<head>
<meta charset="UTF-8">
<title>Total-EDAMS</title>
<?php
include_once("../res/meta.php");
meta();
?>
</head>
<body Marginwidth="-1" Marginheight="-1" Topmargin="0" Leftmargin="0">
<?php
include_once("../res/header.php");
$row=SELECT("*","tablelist",array(array("aval","1")),array(array("time")),"all");
$muzzle_min=100;
$muzzle_max=0;
$table=array();
$urltemp=explode("\r\n",$_GET["table"]);
foreach($urltemp as $temp){
	if($temp!=""){
		$table[]=$temp;
	}
}
while($list=mfa($row)){
	if(count($table)!=0&&!in_array($list["name"],$table))continue;
	$tablelist[]=$list["name"];
	$row2=SELECT(array("*","COUNT(*) AS `COUNT`"),$list["name"],array(array("aval","1")),null,"all",array("muzzle","terminal"));
	while($temp=mfa($row2)){
		if($temp["muzzle"]<$muzzle_min)$muzzle_min=$temp["muzzle"];
		else if($temp["muzzle"]>$muzzle_max)$muzzle_max=$temp["muzzle"];
		if($temp["COUNT"]>$data[(string)$temp["muzzle"]][$list["name"]]["count"]){
			$data[(string)$temp["muzzle"]][$list["name"]]["count"]=$temp["COUNT"];
			$data[(string)$temp["muzzle"]][$list["name"]]["same"]=1;
			$data[(string)$temp["muzzle"]][$list["name"]]["terminal"]=$temp["terminal"];
			consolelog("--action: replace");
		}
		else if($temp["COUNT"]==$data[(string)$temp["muzzle"]][$list["name"]]["count"]){
			$data[(string)$temp["muzzle"]][$list["name"]]["terminal"]*=$data[(string)$temp["muzzle"]][$list["name"]]["same"];
			$data[(string)$temp["muzzle"]][$list["name"]]["same"]++;
			$data[(string)$temp["muzzle"]][$list["name"]]["terminal"]+=$temp["terminal"];
			$data[(string)$temp["muzzle"]][$list["name"]]["terminal"]/=$data[(string)$temp["muzzle"]][$list["name"]]["same"];
		}
	}
}
//consolelog($data);
?>
<center>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class="dfromh">&nbsp;</td>
</tr>
<tr>
	<td align="center" valign="top">
		<h2>Total</h2>
		<form action="" method="get">
			<textarea name="table" cols="20" rows="5"><?php echo $_GET["table"]; ?></textarea><br>
			<input name="" type="submit" value="送出">
		</form>
		<table border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td class="datatd">muzzle</td>
			<?php
			foreach($tablelist as $temp){
			?>
			<td class="datatd"><?php echo $temp; ?></td>
			<?php
			}
			?>
		</tr>
		<?php
		for($i=$muzzle_min;$i<=$muzzle_max+0.005;$i+=0.01){
		?>
			<tr>
				<td class="datatd"><?php echo sprintf("%.2f",round($i,2)); ?></td>
			<?php
			foreach($tablelist as $table){
			?>
				<td bgcolor="<?php echo ($data[(string)$i][$table]["same"]>1?"#FFFF00":""); ?>" class="datatd"><?php echo (isset($data[(string)$i][$table]["terminal"])?sprintf("%.2f",round($data[(string)$i][$table]["terminal"],2)):"").($data[(string)$i][$table]["same"]>1?" (".$data[(string)$i][$table]["same"].")":""); ?></td>
			<?php
			}
			?>
			</tr>
		<?php
		}
		?>
		</table>
	</td>
</tr>
</table>
</td>
</tr>
</table>
</center>
</body>
</html>