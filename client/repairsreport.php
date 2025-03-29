<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define('NO_PERMISSION_REQUIRED', TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	$currentpage = htmlentities_utf8(xlate("Repairs Report"));

	$rptfilters["period"] = "+1m";

	$month_options = month_options(1, TRUE, "Month");
	$year_options = array("" => htmlentities_utf8(xlate("Year")));

	$SQL = "SELECT DISTINCT YEAR(t.StartDate) AS Year\n"
		 . "  FROM repair r JOIN repair_timesheet rt JOIN timesheet t\n"
		 . " WHERE t.id = rt.TimesheetID AND rt.RepairID = r.id\n"
		 . "       AND r.IsScheduled = 0\n"
		 . " ORDER BY Year";
	while ($tmp = $db->getnext($SQL)) {
		$year_options[$tmp->Year] = $tmp->Year;
	}

	$rptfilters['showdetails'] = 0;
	require_once(SYSTEM_DIR . "/includes/clientheader.php");
?>

<!-- begin form -->
<div class="formcell">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="40%" valign="top">
			<form action="/common/report.php" method="get" onsubmit="if (emptyElement(this['rptfilters[building.id]'], <?php print stringtojs(xlate("building")); ?>) || emptyElement(this['rptfilters[start_month]'], <?php print stringtojs(xlate("month")); ?>) || emptyElement(this['rptfilters[start_year]'], <?php print stringtojs(xlate("year")); ?>)) return false; frmpopup(this,800,600);"><input type="hidden" name="rpt" value="Repairs" /><input type="hidden" name="rptfilters[clientonly]" value="1" />
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
			<tr>
				<th colspan="2"><?php print htmlentities_utf8(xlate("Reports")); ?></th>
			</tr>
			<?php

					$building_options = array("" => xlate("Choose"));
					$lastbuilding = null;
					$sql = "SELECT b.id, " .sql_local_en('b.Premises'). " AS Premises, " . sql_local_en('br.Code') . " AS Code FROM unit u JOIN bank ba JOIN building b JOIN user_building ub JOIN branch br WHERE ub.UserID = {$currentclient->id} AND ub.BuildingID = b.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.BranchID = br.id AND u.{$branchcriteria} ORDER BY " .sql_local_en('b.Premises');
				
					while ($tmp = $db->getnext($sql)) {
						$building_options[$tmp->id] = $tmp->Premises;
						$lastbuilding = $tmp;
					}
					if (count($building_options) > 3) {
						print "<tr><td><strong>".htmlentities_utf8(xlate("Choose Building"))."</strong></td><td>&nbsp;</td></tr>\n";
						print "<tr><td colspan=\"2\">" . dropbox("rptfilters[building.id]", $building_options) . "</td></tr>\n";
					} else {
						print "<tr><td colspan=\"2\">" . htmlentities_utf8($lastbuilding->Premises) . hidden("rptfilters[building.id]", DEFARG, $lastbuilding->id) . "</td></tr>\n";
					}

			?>
			<tr>
				<td width="40%"><strong><?php print htmlentities_utf8(xlate("Month")); ?></strong></td>
				<td width="40%"><strong><?php print htmlentities_utf8(xlate("Year")); ?></strong></td>
			</tr>
			<tr>
				<td><?php print dropbox("rptfilters[start_month]", $month_options); ?></td>
				<td><?php print dropbox("rptfilters[start_year]", $year_options); ?></td>
			</tr>
			<tr>
				<td><strong><?php print htmlentities_utf8(xlate("Time Period")); ?></strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">
					<label><?php print radiobutton("rptfilters[period]", "VALUE:=+1m"); ?> <?php print htmlentities_utf8(xlate("1 Month")); ?></label> &nbsp;
					<label><?php print radiobutton("rptfilters[period]", "VALUE:=+3m"); ?> <?php print htmlentities_utf8(xlate("3 Months")); ?></label> &nbsp;
					<label><?php print radiobutton("rptfilters[period]", "VALUE:=+6m"); ?> <?php print htmlentities_utf8(xlate("6 Months")); ?></label> &nbsp;
					<label><?php print radiobutton("rptfilters[period]", "VALUE:=+12m"); ?> <?php print htmlentities_utf8(xlate("12 Months")); ?></label>
				</td>
			</tr>
			<tr>
				<td colspan="2"><strong><?php print htmlentities_utf8(xlate("Include")); ?></strong></td>
			</tr>
			<tr>
				<td colspan="2">
					<label><?php print radiobutton('rptfilters[showdetails]', 'value:=0'); ?> <?php print htmlentities_utf8(xlate("Summary Only")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[showdetails]', 'value:=1'); ?> <?php print htmlentities_utf8(xlate("Summary & Details")); ?></label>
				</td>
			</tr>
			<tr>
				<td style="text-align: right;" colspan="2"><input type="submit" value=" <?php print htmlentities_utf8(xlate("CREATE")); ?> "></td>
			</tr>
			<tr>
				<td colspan="2"><hr /></td>
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
