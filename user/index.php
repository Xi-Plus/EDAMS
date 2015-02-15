<html>
<?php
include_once("../func/checklogin.php");
include_once("../func/sql.php");
include_once("../func/url.php");
include_once("../func/log.php");
$login=checklogin();
if($login==false)header("Location: ../login/?from=user");
$editid=$login["id"];
if(is_numeric($_GET["id"]))$editid=$_GET["id"];
$error="";
$message="";
$message2="";
$showdata=true;
$editdata=mfa(SELECT(["name","power"],"account",[["id",$editid]]));
if(isset($_POST["sid"])&&$editid!=$_POST["sid"]){
	$error="有預設資料遭到修改，沒有任何修改動作被執行";
	insertlog($login["id"],$editid,"useredit",false,"attack");
	$showdata=false;
}
else if($editdata==""){
	$error="無此ID";
	insertlog($login["id"],$editid,"useredit",false,"no id");
	$showdata=false;
}
else{
	if($editid!=$login["id"]&&$login["power"]<=1){
		$error="你沒有權限更改別人的資料";
		insertlog($login["id"],$editid,"useredit",false,"no power");
		$showdata=false;
	}
	else if($login["power"]<$editdata["power"]){
		$error="無法更改較高權限的帳戶";
		insertlog($login["id"],$editid,"useredit",false,"higher power");
		$showdata=false;
	}
	else{
		if($editid!=$login["id"]){
			$message="注意!你正在更改其他人的資料";
		}
		$oldpwd=mfa(SELECT(["pwd"],"account",[["id",$login["id"]]]))["pwd"];
		if($_POST['spwd1']!=""){
			if(crypt($_POST['spwd0'],$oldpwd)!=$oldpwd){
				$error="舊密碼錯誤";
				insertlog($login["id"],$editid,"useredit",false,"wrong old password");
			}else if($_POST["spwd1"]!=$_POST["spwd2"]){
				$error="密碼不符";
				insertlog($login["id"],$editid,"useredit",false,"password not match");
			}else if(preg_match("/\s/", $_POST["spwd1"])){
				$error="密碼不可有空白";
				insertlog($login["id"],$editid,"useredit",false,"password has space");
			}else if(!preg_match("/^.{4,}$/", $_POST["spwd1"])){
				$error="密碼至少4個字";
				insertlog($login["id"],$editid,"useredit",false,"password length");
			}else{
				UPDATE("account",[ ["pwd",crypt($_POST['spwd1'])] ],[ ["id",$editid] ]);
				insertlog($login["id"],$editid,"useredit",true,"edit password");
				if($message2=="")$message2.="已更新以下資料";
				$message2.=" 密碼";
			}
		}
		if($_POST['sname']!=""&&$_POST['sname']!=$editdata["name"]){
			UPDATE("account",[ ["name",$_POST['sname']] ],[ ["id",$editid] ]);
			insertlog($login["id"],$editid,"useredit",true,"edit name");
			if($message2=="")$message2.="已更新以下資料";
			$message2.=" 姓名";
		}
	}
}
if($message!=""&&$message2!="")$message.="<br>";
$message.=$message2;
$editdata=mfa(SELECT(["name"],"account",[["id",$editid]]));
?>
<head>
<meta charset="UTF-8">
<title>User-EDAMS</title>
<link href="user.css" rel="stylesheet" type="text/css">
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
	if($showdata){
?>
<center>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class="dfromh">&nbsp;</td>
</tr>
<tr>
	<td valign="top">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center"><h1>更新資料</h1></td>
		</tr>
		<tr>
			<td>
				<form method="post">
					<input name="sid" type="hidden" id="sid" value="<?php echo $editid;?>">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" class="inputleft">舊密碼：</td>
							<td valign="top" class="inputright"><input name="spwd0" type="password" id="spwd0"></td>
						</tr>
						<tr>
							<td valign="top" class="inputleft">新密碼：</td>
							<td valign="top" class="inputright"><input name="spwd1" type="password" id="spwd1"></td>
						</tr>
						<tr>
							<td valign="top" class="inputleft">再確認：</td>
							<td valign="top" class="inputright"><input name="spwd2" type="password" id="spwd2"></td>
						</tr>
						<tr>
							<td valign="top" class="inputleft">姓名：</td>
							<td valign="top" class="inputright"><input name="sname" type="text" id="sname" value="<?php echo het($editdata["name"]);?>" maxlength="32"></td>
						</tr>
						<tr>
							<td align="center" colspan="2"><input type="submit" value="更新資料"></td>
						</tr>
					</table>
				</form>
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