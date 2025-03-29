<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define("NO_PERMISSION_REQUIRED", TRUE);
 	define('REQUIRE_TKGLOBAL_DB',    TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");

	define("PAGE_TITLE", html_xlate("My Account"));

	if (strtolower(trim($_REQUEST["op"])) == "update") {
		if (empty($_REQUEST["frm_currentpassword"])) {
			$errors[] = xlate("You must enter your current password");
		}
		if (empty($_REQUEST["frm_newpassword"])) {
			$errors[] = xlate("You must enter your new password");
		}
		if (empty($_REQUEST["frm_confirmpassword"])) {
			$errors[] = xlate("You must confirm your new password");
		}
			
		if (empty($errors) && $_REQUEST["frm_newpassword"] != $_REQUEST["frm_confirmpassword"]) {
			$errors[] = xlate("The confirmation password you entered is different from your new password");
		}
		if (empty($errors) && !isPasswdSecure($_REQUEST["frm_newpassword"])) {
			$errors[] = xlate("The new password you have entered is not secure enough. Please enter a more secure one.");
		}
		if (empty($errors) && !(chkPasswd($_REQUEST["frm_currentpassword"], $currentuser->Password) ||
            chkPasswdNew($_REQUEST["frm_currentpassword"], $currentuser->Password))) {
			$errors[] = xlate("You have entered your current password incorrectly. Please try again.");
		}

		
		// Save data
		if (empty($errors)) {
			changeClientPasswd($currentuser, $_REQUEST["frm_newpassword"]);
			logAction(EDIT_MYACCOUNT, 'user', $currentuser->id, "Password manually changed. IP: {$_SERVER['REMOTE_ADDR']}");
			viewRedirect("/common/blank.php?reloadparent=1&confirmhead=" . urlencode(xlate("Updated successfully."))."&confirmbody=".urlencode(xlate("Please Note that the password has changed, you may want to update the password of other environments.")));
		} else {
			$JS["exec"][] = "window.top.showError(".stringtojs(xlate("The following errors occured")).", '" . implode("', '", $errors) . "');";
			$JS["onunload"][] = "window.top.hideError();\n";
		}			
	}

?>

<?php require_once(SYSTEM_DIR . "/includes/clientfooter.php"); ?>