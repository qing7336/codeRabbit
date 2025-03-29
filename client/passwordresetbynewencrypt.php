<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

/*
 * $Id: passwordreset.php 2016-11-14 09:23:15Z rt.lacey $
 */
define('REQUIRE_TKGLOBAL_DB', true);
require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");

define("PAGE_TITLE", html_xlate("Login"));

//if ($_SERVER['SERVER_ADDR'] == RADIUS_ON_IP) viewRedirect("/auth.php");

$loginError = FALSE;
//start of apac customer link
$_SESSION['TK_IIDCUSTOMER'] = '';

$username = (isset($_REQUEST['username']))?$_REQUEST['username']:NULL; 
if(array_key_exists("op", $_REQUEST) && $_REQUEST["op"] == "resetpwd")
{   
    
    if(empty($_REQUEST['frm_username']))
        viewRedirect("/login.php");

    $user = $db->get("user", "Status = 'Active' AND LOWER(Username) = LOWER(" . $db->quote($_REQUEST['frm_username']) . ")");
    $userID = $user->id;      

    if(empty($_REQUEST['frm_newpassword']))
         $errors[] = xlate("Please input your new password.");
    else
        $newPassword = $_REQUEST['frm_newpassword'];
    
    if(empty($errors) && empty($_REQUEST['frm_confirmpassword']))
        $errors[] = xlate("Please confirm your new password.");
    
    if(empty($errors) && $_REQUEST['frm_newpassword'] != $_REQUEST['frm_confirmpassword'])
        $errors[] = xlate("Please confirm your new password.");       
    
    if(empty($errors) && chkPasswd($_REQUEST['frm_newpassword'], $user->Password))
        $errors[] = xlate("Please replace your old password.");	
	
    if ($user && empty($errors)) {
        //use new encrypt algorithm
        $savedata['Password'] = $db->quote(sha512Passwd($newPassword)); 
		// check duplicate entry condition
        $db->onCondition('23000', DB_THROW_EXCEPTION);
        try {
            $success = $db->update("user", $savedata, "id={$userID}");
        } catch (NGSQLStateException $e) {
            if (preg_match('/^(23000)$/', $e->sqlstate)) {
                $errors[] = xlate("That employee already has a user account.");
                $success = FALSE;
            } else {
                $success = FALSE;
                $db->rollback();
                $errors[] = xlate("Unknown error. Please contact your database administrator");
            }
        }

        $db->onCondition('23000', NULL);
        

        if ($success) {
            $_SESSION["currentuserid"] = $user->id;
            $sql = "SELECT l.Code as Code FROM " . GLOBAL_DATABASE_NAME . ".user_default_language udl 
                    JOIN `language` l ON udl.LanguageID = l.id
                    WHERE udl.UserID =".$db->quote($_SESSION["currentuserid"])." and udl.SiteID = ".$db->quote(SITE_ID);
            $lan = $db->get($sql);
            if($lan){
                $_SESSION["currentlanguage"] = $lan->Code;
                setLanguage($_SESSION["currentlanguage"]);
            }
            $currentuser = &$user;
            setcookie("saveusername", $_REQUEST["frm_username"], time()+SAVE_USERNAME_TIME);
            setcookie("cookietype", 'local', time()+SAVE_USERNAME_TIME);
            unset($_SESSION['TK_IIDCUSTOMER']);
            $_SESSION['TK_IIDCUSTOMER']='local'; 
            logAction(DEFARG, DEFARG, DEFARG, "Logged in", DEFARG, DEFARG,DEFARG,DEFARG,DEFARG, "Logged in"); 
            //mail to end user
            mailPasswdResetUser($user);
            
            viewRedirect(buildurl("/login.php", "confirmhead=" . urlencode(xlate("Updated successfully"))));
        }else{
            $errors[] = xlate("Reset failed.\nContact the System Administrator if problems persist.");
            $loginError = TRUE;
        }
    }
}

if (isset($frm_username) && $frm_username == "" && $_COOKIE["saveusername"] != "") $frm_username = $_COOKIE["saveusername"];

// Include header
require_once(SYSTEM_DIR . "/includes/header_v2.php");
if (!empty($errors)) {
    foreach ($errors as $key => $val) $errors[$key] = "'" . addslashes($val) . "'";
    $JS["onload"][] = "window.top.showErrorHtml(" . stringtojs(xlate("The following errors occured")) . ", " . implode(",", $errors) . ");";
    $JS["onunload"][] = "window.top.hideError();\n";
}
?>
<!-- begin form -->
<div class="formcell">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="45%" valign="top">
                <form action="passwordresetbynewencrypt.php" method="post" onSubmit="return errorOnBlankTextbox(this.frm_newpassword, <?php print stringtojs(xlate('You must enter your new password')); ?>) || errorOnBlankTextbox(this.frm_confirmpassword, <?php print stringtojs(xlate('You must enter your confirm password')); ?>);">
                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
                        <tr>
                            <th colspan="2"><?php print html_xlate('Reset Password'); ?></th>
                        </tr>
                        <tr>
                            <td style="width:35%;">&nbsp;<?php print hidden('frm_username', '', $username); ?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;<strong><?php print html_xlate('New Password'); ?></strong></td>
                            <td><?php print password("frm_newpassword", "size:=10, maxlen:=25", ""); ?></td>               
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;<strong><?php print html_xlate('Confirm Password'); ?></strong></td>
                            <td><?php print password("frm_confirmpassword", "size:=10, maxlen:=25", ""); ?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align:right">&nbsp;<?php print hidden('op', '', 'resetpwd')?></td>
                            <td style="text-align:right"><input type="submit" value=" <?php print html_xlate('RESET'); ?> " /></td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr /></td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                    </table>
                </form>
            </td>
            <td width="5%">&nbsp;</td>
            <td width="50%" valign="top">&nbsp; </td>
        </tr>
    </table>
</div>
<!-- end form -->
<?php require_once(SYSTEM_DIR . "/includes/loginfooter.php"); ?>