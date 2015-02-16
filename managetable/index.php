<html>
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
if($data==false)header("Location: ../login/?from=managebook");
else if($data["power"]<=1){
	$error="你沒有權限";
	insertlog($data["id"],0,"managebook",false,"no power");
	?><script>setTimeout(function(){history.back();},1000);</script><?php
}
else if(isset($_POST["addname"])){
	$row=mfa(SELECT("*","tablelist",array(array("name",$_POST["addname"]))));
	if(in_array($_POST["addname"],$nousename)||substr($_POST["addname"],0,4)=="del_"){
		$error="這是保留字";
	}else if($row){
		$error="已有此名稱";
	}else {
		INSERT( "tablelist",[ [ "name",$_POST["addname"] ] ]);
		SQL("CREATE TABLE IF NOT EXISTS `".$_POST["addname"]."` (  `muzzle` double DEFAULT NULL,  `terminal` double NOT NULL,  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  `aval` int(11) NOT NULL DEFAULT '1') ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		insertlog($data["id"],0,"addtable",true,$_POST["addname"]);
		$message="已增加 ".$_POST["addname"];
	}
}
else if(isset($_POST["editname"])){
	$row=mfa(SELECT("*","tablelist",[ [ "name",$_POST["newname"] ] ]));
	if(in_array($_POST["newname"],$nousename)||substr($_POST["addname"],0,4)=="del_"){
		$error="這是保留字";
	}else if($row){
		$error="已有此名稱";
	}else {
		UPDATE( "tablelist",[ ["name",$_POST["newname"]] ],[ ["name",$_POST["editname"]] ] );
		SQL("RENAME TABLE  `".$_POST["editname"]."` TO  `".$_POST["newname"]."` ;");
		insertlog($data["id"],0,"edittable",true,$_POST["editname"]." to ".$_POST["newname"]);
		$message="已更名 ".$_POST["newname"];
	}
}
else if(isset($_POST["delname"])){
	UPDATE( "tablelist",[ ["aval",0] ],[ ["name",$_POST["delname"]] ] );
	SQL("RENAME TABLE  `".$_POST["delname"]."` TO  `del_".$_POST["delname"]."` ;");
	insertlog($data["id"],0,"deltable",true,$_POST["delname"]);
	unlink("../data/".$_POST["delname"].".png");
	$message="已刪除 ".$_POST["delname"];
}
$row=SELECT("*","tablelist",array(array("aval",1)),array(array("time"),array("name")),"all");
while($temp=mfa($row)){
	$list[]=$temp;
}
?>
<head>
<meta charset="UTF-8">
<title>Managetable-EDAMS</title>
<?php
include_once("../res/meta.php");
meta();
?>
</head>
<body Marginwidth="-1" Marginheight="-1" Topmargin="0" Leftmargin="0">
<?php
	include_once("../res/header.php");
	if($error!=""){
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" valign="middle" bgcolor="#F00" class="message"><?php echo $error;?></td>
	</tr>
</table>
<?php
	}
	if($message!=""){
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" valign="middle" bgcolor="#0A0" class="message"><?php echo $message;?></td>
	</tr>
</table>
<?php
	}
	if($data["power"]>=2){
?>
<center>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class="dfromh" colspan="3">&nbsp;</td>
</tr>
<tr>
<td valign="top">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" align="center"><h2>Manage Table</h2></td>
	</tr>
	<tr>
		<td align="center" valign="top">
		<form method="post">
			<table border="0" cellspacing="3" cellpadding="0">
			<tr>
				<td colspan="2" align="center"><h2>Add</h2></td>
			</tr>
			<tr>
				<td>Name</td>
				<td><input name="addname" type="text" id="addname"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" value="新增"></td>
			</tr>
			</table>
		</form>
		</td>
		<td valign="top">
		<form method="post">
			<table border="0" cellspacing="3" cellpadding="0">
			<tr>
				<td colspan="2" align="center"><h2>Edit</h2></td>
			</tr>
			<tr>
				<td>Table</td>
				<td>
				<select name="editname">
				<?php
					foreach($list as $temp){
				?>
					<option value="<?php echo $temp["name"]; ?>"><?php echo $temp["name"]; ?></option>
				<?php
					}
				?>
				</select>
				</td>
			</tr>
			<tr>
				<td>Name</td>
				<td><input name="newname" type="text" id="newname"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" value="修改"></td>
			</tr>
			</table>
		</form>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<table border="1" cellspacing="0" cellpadding="2">
			<div style="display:none">
				<form method="post" id="delform">
					<input name="delname" type="hidden" id="delname">
				</form>
			</div>
			<tr>
				<td>Time</td>
				<td>Name</td>
				<td>Del</td>
			</tr>
			<?php
				foreach($list as $temp){
			?>
				<tr>
				<td><?php echo $temp["time"]; ?></td>
				<td><?php echo $temp["name"]; ?></td>
				<td><input type="button" value="刪除" onClick="if(!confirm('確認刪除?'))return false;delname.value='<?php echo $temp["name"]; ?>';delform.submit();" ></td>
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
<?php
	}
?>
</body>
</html>