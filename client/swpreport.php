<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define('NO_PERMISSION_REQUIRED', TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	$currentpage = htmlentities_utf8(xlate("Stop With Passengers"));

	require_once(SYSTEM_DIR . "/includes/clientheader.php");

	$rptfilters["summary"] = 0;
	$month_options = month_options(1, TRUE, "Month");
	$maxYear=strftime("%Y");
	$tmp = $db->get("SELECT value as MinYear from key_value where `key`='call_min_year'");echo "<!--\n #Tmp# ".var_dump($tmp)."\n-->\n\n";
	$year_options = array("" => htmlentities_utf8(xlate("Year")));
	for ($i = $tmp->MinYear; $i <=$maxYear; $i++) $year_options[$i] = $i;

?>

<!-- begin form -->
<div class="formcell">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="40%" valign="top">
		<form action="/common/report.php" method="get" onsubmit="if (emptyElement(this['rptfilters[building.id]'], <?php print stringtojs(xlate("building")); ?>)) return false; if (emptyElement(this['rptfilters[start_month]'], <?php print stringtojs(xlate("start month")); ?>)) return false; if (emptyElement(this['rptfilters[start_year]'], <?php print stringtojs(xlate("start year")); ?>)) return false; if (emptyElement(this['rptfilters[end_month]'], <?php print stringtojs(xlate("end month")); ?>)) return false; if (emptyElement(this['rptfilters[end_year]'], <?php print stringtojs(xlate("end year")); ?>)) return false; frmpopup(this,800,600);"><input type="hidden" name="rpt" value="SWP" /><input type="hidden" name="rptfilters[call.swp]" value="1" /><input type="hidden" name="rptfilters[clientonly]" value="1" />
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbody">
          <tr>
            <th colspan="4"><?php print htmlentities_utf8(xlate("Reports")); ?></th>
          </tr>
		  <?php

				$building_options = array("" => xlate("Choose"));
				$lastbuilding = null;
				$sql = "SELECT b.id, " . sql_local_en('b.Premises') . " AS Premises, " . sql_local_en('br.Code') . " AS Code FROM unit u JOIN bank ba JOIN building b JOIN user_building ub JOIN branch br WHERE ub.UserID = {$currentclient->id} AND ub.BuildingID = b.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.BranchID = br.id AND u.{$branchcriteria} ORDER BY ".sql_local_en('b.Premises');
			
				while ($tmp = $db->getnext($sql)) {
					$building_options[$tmp->id] = $tmp->Premises;
					$lastbuilding = $tmp;
				}
				if (count($building_options) > 2) {
					print "<tr><td colspan=\"3\"><strong>".htmlentities_utf8(xlate("Choose Building"))."</strong></td><td>&nbsp;</td></tr>\n";
					print "<tr><td colspan=\"4\">" . dropbox("rptfilters[building.id]", $building_options) . "</td></tr>\n";
				} else {
					print "<tr><td colspan=\"4\">" . htmlentities_utf8($lastbuilding->Premises) . hidden("rptfilters[building.id]", DEFARG, $lastbuilding->id) . "</td></tr>\n";
				}

          ?>
          <tr>
            <td width="40%"><strong><?php print htmlentities_utf8(xlate("Start Date")); ?></strong></td>
            <td width="40%">&nbsp;</td>
            <td width="40%"><strong><?php print htmlentities_utf8(xlate("End Date")); ?></strong></td>
            <td width="40%">&nbsp;</td>
          </tr>
          <tr>
            <td><?php print dropbox("rptfilters[start_month]", $month_options); ?></td>
            <td><?php print dropbox("rptfilters[start_year]", $year_options); ?></td>
            <td><?php print dropbox("rptfilters[end_month]", $month_options); ?></td>
            <td><?php print dropbox("rptfilters[end_year]", $year_options); ?></td>
          </tr>
          <tr>
            <td colspan="4"><strong><?php print htmlentities_utf8(xlate("Include")); ?></strong></td>
          </tr>
          <tr>
            <td colspan="4">
				<label><?php print radiobutton("rptfilters[summary]", "VALUE:=0"); ?> <?php print htmlentities_utf8(xlate("Full Details")); ?></label> &nbsp;
				<label><?php print radiobutton("rptfilters[summary]", "VALUE:=1"); ?> <?php print htmlentities_utf8(xlate("Summary Only")); ?></label>
			</td>
          </tr>
          <tr>
            <td style="text-align:right" colspan="4"><input type="submit" value=" <?php print htmlentities_utf8(xlate("CREATE")); ?> " /></td>
          </tr>
        </table>
		</form>
	  </td>
      <td width="60%">&nbsp;</td>
    </tr>
  </table>
</div>
<!-- end form -->

<?php require_once(SYSTEM_DIR . "/includes/clientfooter.php"); ?>
