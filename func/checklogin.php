<?php
include_once("sql.php");
function checklogin(){
	if($_COOKIE["EDAMScookie"]=="")return false;
	$row = mfa(SELECT(array("id"),"session",array(array("cookie",$_COOKIE["EDAMScookie"])),null,array(0,1)));
	if($row=="")return false;
	return mfa(SELECT(array("id","user","name","power"),"account",array(array("id",$row["id"])),null,array(0,1)));
}
?>