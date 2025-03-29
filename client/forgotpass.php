<?php
	define('REQUIRE_TKGLOBAL_DB',    TRUE);
	define('NO_PERMISSION_REQUIRED', TRUE);
	define('NO_USER_REQUIRED',       TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	$username = (strstr($_REQUEST["frm_username"], "@") === false ? $_REQUEST["frm_username"] . DEFAULT_EMAIL_DOMAIN : $_REQUEST["frm_username"]);

	if ($username != "" && ($user = $db->get("user", "Status = 'Active' AND Username = '{$username}'"))) {

		$SQL = "SELECT ad.* \n"
			 . "  FROM site si \n"
			 . "       LEFT JOIN " . GLOBAL_DATABASE_NAME . ".activedirectory ad ON ad.SiteID = si.id \n"
			 . " WHERE si.id = " . SITE_ID . "\n"
			 . "       AND ad.IsEnabled = 1 \n";
		$ldap = $db->get($SQL);

		// Reject if user has an 8ID (Active Directory ID)
		if ($ldap && !empty($user->ActiveDirectoryID)) {
			viewRedirect("login.php?errorhead=" . urlencode(xlate("System error")) . "&errorbody=" . urlencode(xlate("If you forget your Active Directory password, please please contact your local IT helpdesk and FOS admin")));
		} else {
			if (mailPasswdConfirmUser($user)) {
				$GLOBALS['currentuser'] =& $user;
				logAction(EDIT_USERS, 'user', $user->id, "Password reset requested. IP: {$_SERVER['REMOTE_ADDR']}");
				unset($GLOBALS['currentuser']);
				viewRedirect("login.php?confirmhead=" . urlencode(xlate("A password confirmation email has been sent")));
			} else {
				viewRedirect("login.php?errorhead=" . urlencode(xlate("System error")) . "&errorbody=" . urlencode(xlate("An error has occured whilst trying to issue your new password. Contact the System Administrator if problems persist.")));
			}
		}
	} else {
		viewRedirect("login.php?errorhead=" . urlencode(xlate("Email address unknown")) . "&errorbody=" . urlencode(xlate("Please try again, checking that the spelling is correct. Contact the System Administrator if problems persist.")));
	}
	exit;

?>
