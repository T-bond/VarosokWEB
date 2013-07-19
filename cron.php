<?php //Cron-job: 1 0 * * *
require_once("config.php");
include("modules/users.php");
$user=CUsers::getInstance();
$users=$user->get("`status` = '0'");
foreach($users as $profile)
	if(isset($profile["data"]["bant"]) and strtotime($profile["data"]["bant"])<=strtotime(date("Y-m-d h:i:s"))) {
		unset($profile["data"]["ban"]);
		unset($profile["data"]["bant"]);
		$profile["status"] = 2;
		$user->update($profile);
	}
$users=$user->get("`status` = '1'");
foreach($users as $profile) {
	if(!isset($profile["data"]["old_mail"]) and isset($profile["data"]["acst"]) and strtotime($profile["data"]["acst"].' + '.$config["activation/delete"].' day')<=strtotime(date("Y-m-d h:i:s")))
		$user->delete($profile);
	if(isset($profile["data"]["old_mail"]) and isset($profile["data"]["acst"]) and strtotime($profile["data"]["acst"].' + '.$config["newmail/backup"].' day')<=strtotime(date("Y-m-d h:i:s"))) {
		unset($profile["data"]["acst"]);
		unset($profile["data"]["activation_code"]);
		$profile["mail"] = $profile["data"]["old_mail"];
		unset($profile["data"]["old_mail"]);
		$profile["status"] = 2;
		$user->update($profile);
	}
}
?>