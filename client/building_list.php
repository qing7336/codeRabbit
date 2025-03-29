<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	/*****************************************************
	 * BUILDING POP-UP LIST (inner)
	 * Change Log:	03/07/2003	Created
	 *****************************************************/

	define("NO_PERMISSION_REQUIRED", true);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/bizlogic.lib");

	if (!array_key_exists('openerFormIdx', $_REQUEST)) $_REQUEST['openerFormIdx'] = 0;

	$JS["exec"][] = "openerFormElementName = '" . addslashes($_REQUEST["openerFormElementName"]) . "';";
	$JS["exec"][] = "openerFormIdx = " . (int)$_REQUEST["openerFormIdx"] . ";";
	$JS["onload"][] = "document.forms[0].elements.keyword.focus();";
	// Include header
	require_once(SYSTEM_DIR . "/includes/popupheader.php");

	print form() . hidden("openerFormElementName") . hidden("multiselect") . hidden("openerFormIdx") . hidden("accountonly") . hidden("allbranches");

	$allowedbranches = array();
	while ($tmp = $db_read_service->getnext("branch", sql_user_branch($db_read_service, $currentuser, $currentbranch, "id"))) {
		$allowedbranches[] = $tmp->id;
	}

	$selectedbranches = array();
	if (!$currentbranch) {
		if (isset($_REQUEST["branchid"]) && is_array($_REQUEST["branchid"]) && !empty($_REQUEST["branchid"])) {
			$selectedbranches = array_keys($_REQUEST["branchid"]);
		}
	} else if ($_REQUEST['allbranches']) {
		if (is_array($_REQUEST["branchid"]) && !empty($_REQUEST["branchid"])) {
			$selectedbranches = array_intersect(array_keys($_REQUEST["branchid"]), $allowedbranches);
		}
		if (empty($selectedbranches)) $selectedbranches = $allowedbranches;
	} else {
		$selectedbranches = array($currentbranch->id);
	}

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="formbody">
<tr>
	<th colspan="2"><?php print htmlentities_utf8(xlate("Building")); ?> <?php if ($_REQUEST["accountonly"]) print "Account "; print htmlentities_utf8(xlate("Selection")); ?></th>
</tr>
<tr>
	<td width="85%"><?php print textbox("keyword", "size:=40, style:=width:100%; "); ?></td>
	<td width="15%"><?php

		$searchtype_options = array(''=>xlate('Building'), 'c'=>xlate('Contract Number'), 'u'=>xlate('Unit Number'));
		print dropbox("searchtype", $searchtype_options);

	?></td>
</tr>
<tr>
	<td><?php print "<label>" . checkbox("inactive") . html_xlate("Include inactive buildings") . "</label>"; ?></td>
        <td><?php if($_REQUEST["all"] == 1){ print hidden("all"); } ?></td>
</tr>
<tr>
	<td id="runs_id"></td>
        <td></td>
</tr>
</table>
<!-- search multiply states option -->
<!-- user states selected by default -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="formbody">
<tr>
	<td>
		
	</td>
	<td style="text-align:right;"><?php print submitbutton("op", DEFARG, "SEARCH"); ?></td>
</tr>
</table>

<!-- search result - close window on selection - click -->
<br />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="formbodyline">
<?php

	if (array_key_exists('keyword', $_REQUEST) && !is_null($_REQUEST['keyword'])) {

		$extrajoin   = "";
		$extrawhere  = "";
		$extrahaving = "";

		if (!empty($selectedbranches)) {
			$extrawhere .= " AND u.BranchID IN (" . implode(", ", $selectedbranches) . ")";
		}
		//$extrawhere .= " AND " . sql_user_office($db_read_service, $currentuser, $currentoffice, "u.OfficeID");
		if ($_REQUEST["keyword"] != "") {
			$matchstr = $db->quote("%" . implode("%", preg_split("/ +/", $db->escapewildcard($_REQUEST["keyword"]))) . "%");

			switch ($_REQUEST["searchtype"]) {
				case "c": // contract number
					$extrajoin .= " JOIN " . sql_currentcontract(NULL, TRUE, "du", TRUE) . " JOIN contract c ";
					$extrawhere .= " AND du.id = u.id AND du.ContractID = c.id AND c.ContractNumber LIKE {$matchstr}";
					break;
				case "u": // unit number
					$extrawhere .= " AND u.UnitNumber LIKE {$matchstr}";
					break;
				default:
					$extrawhere .= " AND (" . sql_local_en('b.Premises') . " LIKE {$matchstr} OR b.Address_val LIKE {$matchstr} OR b.BuildingNumber LIKE {$matchstr})";
					break;
			}
		}
		if (!$_REQUEST["inactive"]) {
			$extrahaving .= " AND Active > 0";
		}


            $runid_str = '';
             
			$SQL = "SELECT b.id, BuildingNumber, IF(" . sql_local_en('b.Premises') . " IS NULL OR " . sql_local_en('b.Premises') . " = '' OR " . sql_local_en('b.Premises') . " = b.Address_val, b.Address_val, CONCAT(" . sql_local_en('b.Premises') . ", ' - ', b.Address_val)) AS Name,\n"
				 . "       SUM(u.Inservice) AS Active\n"
				 . "  FROM building b JOIN bank k \n"
				 . "       JOIN unit u {$extrajoin}\n"
				 . "       JOIN user_building ub\n"
				 . "       JOIN branch br \n"       				
				 . " WHERE ub.UserID = {$currentclient->id} AND ub.BuildingID = b.id AND \n"
				 . "       u.BankID = k.id AND k.BuildingID = b.id AND u.BranchID = br.id AND u.{$branchcriteria} {$extrawhere}\n"
				 . " GROUP BY b.id HAVING 1=1 {$extrahaving}\n"
				 . " ORDER BY Name\n";
/*
			echo $SQL;exit;

			$SQL = "SELECT b.id, " . sql_x_static('x1') . " AS Premises, " . sql_x_static('x2') . " AS Code "
				. " FROM unit u "
				. " JOIN bank ba "
				. " JOIN building b "
				. " JOIN user_building ub "
				. " JOIN branch br "
				. " LEFT JOIN dynamiccatalogue x1 ON b.Premises = x1.xid "
				. " LEFT JOIN dynamiccatalogue x2 ON br.Code = x2.xid "
				. " WHERE ub.UserID = {$currentclient->id} AND ub.BuildingID = b.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.BranchID = br.id AND u.{$branchcriteria} ORDER BY b.Premises";
                     */  

                
                $flag = TRUE;
		$db_read_service->do_xlate = FALSE;
		while ($building = $db_read_service->getnext($SQL)) {
			if (array_key_exists("multiselect", $_REQUEST) && $_REQUEST['multiselect']) {
				print "<tr><td>&nbsp;</td><td><a href=\"#\" onClick=\"return window.opener." . $_REQUEST['openerFormElementName'] . ".add({$building->id}, " . htmlentities_utf8(stringtojs("{$building->Name} ({$building->BuildingNumber})")) . ");\">" . htmlentities_utf8(str_replace("\r", "", str_replace("\n", " ", $building->Name))) . " (" . htmlentities_utf8($building->BuildingNumber) . ")</a></td><td>&nbsp;</td></tr>\n";//" . ($building->Active ? "" : " <span style=\"color:red;\">[" . xlate('inactive') . "]</span>") . "
			} else {
                            if($_REQUEST['all'] && $flag){
                                print "<tr><td>&nbsp;</td><td><a href=\"#\" onClick=\"return popuplistselect(0, " . htmlentities_utf8(stringtojs("ALL")) . ");\">" . htmlentities_utf8(str_replace("\r", "", str_replace("\n", " ", 'ALL'))) .  ($building->Active ? "" : " <span style=\"color:red;\">[" . xlate('inactive') . "]</span>") . "</a></td><td>&nbsp;</td></tr>\n";
                                $flag = FALSE;
                            }
				print "<tr><td>&nbsp;</td><td><a href=\"#\" onClick=\"return popuplistselect({$building->id}, " . htmlentities_utf8(stringtojs("{$building->Name} ({$building->BuildingNumber})")) . ");\">" . htmlentities_utf8(str_replace("\r", "", str_replace("\n", " ", $building->Name))) . " (" . htmlentities_utf8($building->BuildingNumber) . ")</a></td><td>&nbsp;</td></tr>\n";//" . ($building->Active ? "" : " <span style=\"color:red;\">[" . xlate('inactive') . "]</span>") . "
			}
			$db_read_service->do_xlate = FALSE;
		}
	}

?>
</table>
<script type="text/javascript">
var parentWin = window.opener ;
var str = parentWin.document.getElementsByName("mp[orunList][]");
for(var i=0;i<str.length;i++){
    document.getElementById('runs_id').innerHTML+="<input type='hidden' name='runsid[]' value='"+str[i].value+"'>";
}
var superStr = parentWin.document.getElementsByName("runid")[0];
var superVal = superStr.options[superStr.options.selectedIndex].value;
if(str.length == 0 && superVal != "all"){
    document.getElementById('runs_id').innerHTML+="<input type='hidden' name='superId' value='"+superVal+"'>";
}
</script>
<?php require_once(SYSTEM_DIR . "/includes/popupfooter.php"); ?>
