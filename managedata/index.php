<!DOCTYPE html>
<?php
include_once("../func/sql.php");
include_once("../func/url.php");
include_once("../func/log.php");
include_once("../func/checklogin.php");
include_once("../func/consolelog.php");
$error="";
$message="";
$data=checklogin();
$nousename=array("account","session","log","tablelist");
if($data==false)header("Location: ../login/?from=managedata");
else if($data["power"]<=1){
	$error="你沒有權限";
	insertlog($data["id"],0,"managebook",false,"no power");
	?><script>setTimeout(function(){history.back();},1000);</script><?php
}
else if(isset($_POST["muzzle"])){
	INSERT($_GET["table"],array(array("muzzle",$_POST["muzzle"]),array("terminal",$_POST["terminal"]),array("token",md5(uniqid(rand(),true)))));
	insertlog($data["id"],0,"adddata",true,$_GET["table"]." (".$_POST["muzzle"].",".$_POST["terminal"].")");
		$message="已增加 ".$_GET["table"]." (".$_POST["muzzle"].",".$_POST["terminal"].")";
}
else if(isset($_POST["deltoken"])){
	UPDATE($_GET["table"],array(array("aval",$_POST["aval"])),array(array("token",$_POST["deltoken"])));
	$message="已".($_POST["aval"]==1?"復原":"刪除");
}
?>
<head>
<meta charset="UTF-8">
<title>ManageData-EDAMS</title>
<?php
include_once("../res/meta.php");
meta();
?>
</head>
<body Marginwidth="-1" Marginheight="-1" Topmargin="0" Leftmargin="0">
<?php
include_once("../res/header.php");
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
		<div style="display:none">
			<form method="post" id="delform">
				<input name="deltoken" type="hidden" id="deltoken">
				<input name="aval" type="hidden" id="aval">
			</form>
		</div>
		<tr>
			<td class="datatd">time</td>
			<td class="datatd">muzzle</td>
			<td class="datatd">terminal</td>
			<td class="datatd">aval</td>
			<td class="datatd">del</td>
		</tr>
		<?php
		$row=SELECT( "*",$_GET["table"],null,array(array("time","DESC")) ,"all" );
		while($temp = mfa($row) ){
		?>
			<tr>
				<td align="right" class="datatd"><?php echo $temp["time"]; ?></td>
				<td align="right" class="datatd"><?php echo $temp["muzzle"]; ?></td>
				<td align="right" class="datatd"><?php echo $temp["terminal"]; ?></td>
				<td align="right" class="datatd"><?php echo ($temp["aval"]==1?"":"del"); ?></td>
				<td><input type="button" value="<?php echo ($temp["aval"]==1?"刪除":"復原"); ?>" onClick="if(!confirm('確認?'))return false;deltoken.value='<?php echo $temp["token"]; ?>';aval.value='<?php echo ($temp["aval"]==1?"0":"1"); ?>';delform.submit();" ></td>
			</tr>
		<?php
		}
		?>
		</table>
	</td>
	<td width="20px"></td>
	<td align="center" valign="top">
	<h2>Add</h2>
	<form action="" method="post">
		<table border="0" cellpadding="2" cellspacing="0">
		<tr>
			<script>
				window.onload = function(){
					muzzle.focus();
					muzzle.select();
				}
			</script>
			<td>muzzle</td><td><input id="muzzle" name="muzzle" type="text" onload="this.focus();this.select();"></td>
		</tr>
		<tr>
			<td>terminal</td><td><input name="terminal" type="text"></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input name="" type="submit" value="送出"></td>
		</tr>
		</table>
	</form>
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