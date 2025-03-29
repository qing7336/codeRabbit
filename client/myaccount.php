<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define("NO_PERMISSION_REQUIRED", TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	define("PAGE_TITLE", html_xlate("My Account"));

 	/* if (file_exists(BASE_DIR . "/../local/is-dev-server")) {
 		$JS["onload"][] = "window.top.showError(".stringtojs(xlate("Permission Denied")).", " . stringtojs(xlate("Changing your password is not permitted on the development server")) . ");";
 	} else { */
 		$restrictions = new FormRestrictions();
 		$restrictions->js($JS["onload"]);
 
 		if (!empty($errors)) {
 			$JS["onload"][] = "window.top.showError(".stringtojs(xlate("The following errors occured")).", '" . implode("', '", $errors) . "');";
 		}
 	//}

	require_once(SYSTEM_DIR . "/includes/clientheader.php");

	print form("action:=myaccount_changepass.php, target:=work");
?>

<!-- begin form -->
<div class="formcell"> 
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr> 
		<td width="45%"><?php

			//if (!file_exists(BASE_DIR . "/../local/is-dev-server")) {
				print '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbody">';
				print '<tr>';
				print '<th>' . html_xlate("Password") . '</th>';
				print '</tr>';
				print '<tr>';
				print '<td><strong>' . html_xlate("Existing Password") . '</strong></td>';
				print '</tr>';
				print '<tr>';
				print '<td>' . password("frm_currentpassword", "size:=8") . '</td>';
				print '</tr>';
				print '<tr>';
				print '<td><strong>' . html_xlate("New Password") . '</strong></td>';
				print '</tr>';
				print '<tr>';
				print '<td>' . password("frm_newpassword", "size:=8") . '</td>';
				print '</tr>';
				print '<tr>';
				print '<td><strong>' . html_xlate("Confirm New Password") . '</strong></td>';
				print '</tr>';
				print '<tr>';
				print '<td>' . password("frm_confirmpassword", "size:=8") . '</td>';
				print '</tr>';
				print '<tr>';
				print '<td style="text-align:right">' . submitbutton("op", NULL, "UPDATE") . '</td>';
				print '</tr>';
				print '<tr>';
				print '<td><hr /></td>';
				print '</tr>';
				print '</table>';
			//}

		?></td>
		<td width="5%">&nbsp;</td>
		<td width="50%"><iframe name="work" width="100%" height="300" frameborder="0" scrolling="no" src="/common/blank.php"></iframe></td>
	</tr>
	</table>
</div>
<!-- end form -->

<?php require_once(SYSTEM_DIR . "/includes/clientfooter.php"); ?>
