<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define('NO_USER_REQUIRED', true);
	define('NO_CLOSE_SESSION', TRUE);
	
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	session_destroy();
	//setcookie(session_id(), '', time()-3600);
	
	require_once(SYSTEM_DIR . "/includes/loginheader.php");

?>
<!-- begin form -->
<div class="formcell"> 
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="formbody">
    <tr> 
      <th><?php print htmlentities_utf8(xlate("Thank You")); ?></th>
    </tr>
    <tr> 
        <!-- ticket fix for 24174 -->
      <td><strong><?php print htmlentities_utf8(xlate("You have logged out.")); ?> <a href="login.php"><?php print htmlentities_utf8(xlate("Click to log in again")); ?></a>.</strong></td>
    </tr>
    <tr> 
      <td><hr /></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
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
          <!-- ticket fix for 24174 -->
          <?php
              $SQL = "SELECT IFNULL(b.Phone1,b.Phone2) as Phone,b.Fax,b.URL,b.AfterHoursPhone,
                        b.Address_val as Address,
                        " .sql_local_en('b.Code'). " as Code
                        FROM `branch` b ;"; 
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
          <!-- ST-Long end-->
        </table></td>
      <td width="5%">&nbsp;</td>
      <td width="25%" valign="top">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbody">
          <tr>
            <th>Coming Soon</th>
          </tr>
          <tr> 
            <td>More coming to TK Elevator Online.</td>
          </tr>
        </table></td>
    </tr>
  </table>
</div>
<!-- end form -->
	
<?php require_once(SYSTEM_DIR . "/includes/loginfooter.php"); ?>
