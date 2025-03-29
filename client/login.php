<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define('NO_USER_REQUIRED', true);
	define('NO_CLOSE_SESSION', TRUE);
	
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	$frm_username = NULL;

	define("PAGE_TITLE", html_xlate("Login"));
	if (isset($_SESSION["currentuserid"]) && $_SESSION["currentuserid"] > 0) viewRedirect(LOGOUT_PAGE);
	
	if (isset($_REQUEST["frm_username"]) && $_REQUEST["frm_username"] != "" && $_REQUEST["frm_password"] != "") {
		if ($user = $db->get("user", "Status = 'Active' AND LOWER(Username) = LOWER('{$_REQUEST["frm_username"]}') AND Type = 'Client'")) {
			if (chkPasswdNew($_REQUEST["frm_password"], $user->Password)) {
				$_SESSION["currentuserid"] = $user->id;
				$currentuser = &$user;
				define('HOME_PAGE', '/client/entry.php');
				setcookie("clientusername", $_REQUEST["frm_username"], time()+SAVE_USERNAME_TIME);
				logAction(DEFARG, DEFARG, DEFARG, "Logged in", DEFARG, DEFARG,DEFARG,DEFARG,DEFARG, "Logged in");
				if ($_REQUEST['url']) {
					viewRedirect($_REQUEST['url']);
				} else if ($_REQUEST['direct']) {
					viewRedirect(HOME_PAGE);
				} else {
					viewRedirect(LAUNCH_PAGE);
				}
			}else{
                //redirect to update password encrypt, add by Lacey. 2016-11-25 
                if(chkPasswd($_REQUEST["frm_password"], $user->Password))
                    viewRedirect("/passwordresetbynewencrypt.php?username=".$_REQUEST["frm_username"]); 
			}
            //ticket fix for 24174
			$JS["onload"][] = "window.top.showError(".stringtojs(xlate("Password Incorrect")).", ".stringtojs(xlate("Please try again, checking that the spelling is correct.\nContact the System Administrator if problems persist.")).");\n";
		} else {
                    //ticket fix for 24174
			$JS["onload"][] = "window.top.showError(".stringtojs(xlate("Username Unknown")).", ".stringtojs(xlate("Please try again, checking that the spelling is correct.\nContact the System Administrator if problems persist.")).");\n";
		}
	}
	
	if ($frm_username == "" && isset($_COOKIE["clientusername"]) && $_COOKIE["clientusername"] != "") {
		$frm_username = $_COOKIE["clientusername"];
	}
	
	// Include header
	require_once(SYSTEM_DIR . "/includes/header_v2.php");

?>
<!-- begin form -->
<div class="formcell">
    
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="45%" valign="top">
			<form action="login.php" method="post" onSubmit="return errorOnBlankTextbox(this.frm_username, <?php print stringtojs(xlate("You must enter your username")); ?>) && errorOnBlankTextbox(this.frm_password, <?php print stringtojs(xlate("You must enter your password")); ?>); ">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
			<tr> 
				<th colspan="2"><?php print htmlentities_utf8(xlate("Login")); ?></th>
			</tr>
			<tr> 
				<td><strong><?php print htmlentities_utf8(xlate("Username")); ?></strong></td>
				<td><strong><?php print htmlentities_utf8(xlate("Password")); ?></strong></td>
			</tr>
			<tr> 
				<td><?php print textbox("frm_username", "size:=40"); ?></td>
				<td><?php print password("frm_password", "size:=10, maxlen:=25", "") . hidden("direct") . hidden("url"); ?></td>
			</tr>
			<tr> 
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr> 
				<td style="text-align:right">&nbsp;</td>
				<td style="text-align:right"><input type="submit" value=" <?php print htmlentities_utf8(xlate("LOGIN")); ?> " /></td>
			</tr>
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
			<tr> 
				<td colspan="2">&nbsp;</td>
			</tr>
			</table>
			</form>
                        <!-- //ticket fix for 24174 -->
			<form action="forgotpass.php" method="get" onSubmit="return errorOnBlankTextbox(this.frm_username, <?php print stringtojs(xlate("You must enter your email address")); ?>); ">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
			<tr> 
				<th><?php print htmlentities_utf8(xlate("New Password")); ?></th>
			</tr>
			<tr> 
				<td><strong><?php print htmlentities_utf8(xlate("Email Address")); ?></strong></td>
			</tr>
			<tr> 
				<td><?php print textbox("frm_username", "size:=40"); ?></td>
			</tr>
			<tr> 
				<td><?php print htmlentities_utf8(xlate("If you have lost your password enter your email address and a new one will be sent to you.")); ?></td>
			</tr>
			<tr> 
				<td style="text-align:right"><input type="submit" value=" <?php print htmlentities_utf8(xlate("SEND")); ?> " /></td>
			</tr>
			<tr> 
				<td><hr /></td>
			</tr>
			<tr> 
				<td>&nbsp;</td>
			</tr>
			</table>
			</form>
		</td>
		<td width="5%">&nbsp;</td>
		<td width="50%" valign="top">&nbsp; </td>
	</tr>
	</table>
  <!-- complete contact details from office details in db -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="70%"> 
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="formbody">
          <tr> 
            <th colspan="3"><?php print htmlentities_utf8(xlate("CONTACT DETAILS")); ?></th>
          </tr>
          <!--//ticket fix for 24174 -->
          <?php
              $fromTable = '`branch`';
              if(isGlobalContext()) $fromTable = '`branch_cache`';
              $SQL = "SELECT IFNULL(b.Phone1,b.Phone2) as Phone,b.Fax,b.URL,b.AfterHoursPhone,
                        b.Address_val as Address,
                        " . sql_local_en('b.Code') . " as Code
                        FROM {$fromTable} b ;"; 
                while($ROW = $db->getnext($SQL))
                {
                    if(!empty($ROW->Address) || !empty($ROW->Phone) || !empty($ROW->Fax))
                    {
                        $officeArray['Address'] = $ROW->Address;
                        $officeArray['Name'] = $ROW->Code;
                        $officeArray['Phone'] = $ROW->Phone;
                        $officeArray['Fax'] = $ROW->Fax;
                        $officeArray['AfterHoursPhone'] = $ROW->AfterHoursPhone;
                        $oArray[] = $officeArray;
                        if(!empty($ROW->URL))
                        $urlArray[] = $ROW->URL;
                    }
                } 
                foreach($oArray as $key=>$val)
                {
                    if($key%2 ==0)
                    {
                        print "<tr> 
                                <td><strong>" . $val['Name'] . "</strong><br />
                                " . $val['Address'] . "<br />
                                Ph: " . $val['Phone'] . "<br />
                                Fax: " . $val['Fax'] . "<br />";
                        if(!empty($val['AfterHoursPhone']))
                        {
                            print   "24 hour breakdown: " . $val['AfterHoursPhone']. "<br />";
                        }
                        print   "</td><td width='5%'>&nbsp;</td>";
                        if(!empty($oArray[$key+1]['Name']))
                        {        
                            print "<td valign='top'><strong>" . $oArray[$key+1]['Name'] . "</strong><br />
                                    " . $oArray[$key+1]['Address'] . "<br />
                                    Ph: " . $oArray[$key+1]['Phone'] . "<br />
                                    Fax: " . $oArray[$key+1]['Fax'] . "<br />";
                            if(!empty($oArray[$key+1]['AfterHoursPhone']))
                            {
                                print   "24 hour breakdown: " . $oArray[$key+1]['AfterHoursPhone']. "<br />";
                            }
                            print   "</td>";
                        }
                    }
                }
                $urlArray = array_unique($urlArray);
                if(!empty($urlArray))
                {
                    print "<tr><td>&nbsp<br /></td><td width='5%'>&nbsp;<br /></td><td valign='top'><strong>WWW</strong><br />";
                    foreach($urlArray as $key => $val)
                    {
                        print "<a href='" .$val . "'> " . $val . "</a><br />"; 
                    }
                    print "</td></tr>";
                }
          ?>
          <!-- ST-Long end -->
        </table></td>
      <td width="5%">&nbsp;</td>
      <td width="25%" valign="top">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbody">
          <tr>
            <th>Coming Soon</th>
          </tr>
          <tr> 
            <td>More coming to TK Elevator Online. </td>
          </tr>
        </table></td>
    </tr>
  </table>
</div>
<!-- end form -->
	
<?php require_once(SYSTEM_DIR . "/includes/loginfooter.php"); ?>
