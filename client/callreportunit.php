<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define('NO_PERMISSION_REQUIRED', TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	$currentpage = htmlentities_utf8(xlate("Call Report - Unit"));

	$JS["exec"][] = "
		function decideOnDates(frm, type) {
			if (type == 'range') {
				frm['rptfilters[month]'].selectedIndex = 0;
				frm['rptfilters[year]'].selectedIndex = 0;
				frm['rptfilters[nummonths]'][0].checked = true;
				frm['rptfilters[period]'].disabled = false;
			} else {
				frm['rptfilters[startdate]'].value = '';
				frm['rptfilters[enddate]'].value = '';
				frm['rptfilters[period]'].disabled = true;
			}
		}
		function changeDates(datebox) {
			var frm = datebox.form;
			frm.elements['rptfilters[month]'].selectedIndex = 0;
			frm.elements['rptfilters[year]'].selectedIndex = 0;
		}
		
		
		";


	require_once(SYSTEM_DIR . "/includes/clientheader.php");

	$rptfilters["nummonths"] = "1";

	$month_options = month_options(1, TRUE, "Month");
	$maxYear=strftime("%Y");
	$tmp = $db->get("SELECT value as MinYear from key_value where `key`='call_min_year'");
	$year_options = array("" => htmlentities_utf8(xlate("Year")));
    for ($i = $tmp->MinYear; $i <= $maxYear; $i++) {
        $year_options[$i] = $i;
    }

?>

<!-- begin form -->
<div class="formcell">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="40%" valign="top">
		<form action="/common/report.php" method="get" onsubmit="if (emptyElement(this['rptfilters[unit.id]'], <?php print stringtojs(xlate("unit")); ?>)) return false; if (emptyElement(this['rptfilters[startdate]'], <?php print stringtojs(xlate("start date")); ?>, true) && emptyElement(this['rptfilters[month]'], <?php print stringtojs(xlate("month")); ?>, true) && emptyElement(this['rptfilters[year]'], <?php print stringtojs(xlate("year")); ?>, true)) { alert(<?php print stringtojs(xlate("You must fill in either the date range or the month and year")); ?>); return false; } if (emptyElement(this['rptfilters[startdate]'], <?php print stringtojs(xlate("start date")); ?>, true) && (emptyElement(this['rptfilters[month]'], <?php print stringtojs(xlate("month")); ?>) || emptyElement(this['rptfilters[year]'], <?php print stringtojs(xlate("year")); ?>))) return false; frmpopup(this,800,600);"><input type="hidden" name="rpt" value="CallReportUnit" /><input type="hidden" name="rptfilters[clientonly]" value="1" />
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
		<tr>
			<th colspan="2"><?php print htmlentities_utf8(xlate("Reports")); ?></th>
		</tr>
		<?php

				$unit_options = array("" => xlate("Choose"));
				$lastunit = null;
				$sql = "SELECT u.id, u.UnitTypeID, " . sql_local_en('b.Premises') . " AS Premises, " . sql_local_en('br.Code') . " AS Code, u.LiftNumber FROM building b JOIN bank ba JOIN unit u JOIN user_building ub JOIN branch br WHERE ub.UserID = {$currentclient->id} AND ub.BuildingID = b.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.BranchID = br.id AND u.{$branchcriteria} ORDER BY ".sql_local_en('b.Premises').", u.LiftNumber";
				while ($tmp = $db->getnext($sql)) {
					$unit_options[$tmp->id] = "{$tmp->Premises} - Unit {$tmp->LiftNumber}";
					$lastunit = $tmp;
				}
				if (count($unit_options) > 2) {
					print "<tr><td><strong>" . htmlentities_utf8(xlate('Choose Building & Unit')) . "</strong></td><td>&nbsp;</td></tr>\n";
					print "<tr><td colspan=\"2\">" . dropbox("rptfilters[unit.id]", $unit_options) . "</td></tr>\n";
				} else {
					print "<tr><td colspan=\"2\">" . htmlentities_utf8($lastunit->Premises) . " - Unit " . htmlentities_utf8($lastunit->LiftNumber) . hidden("rptfilters[unit.id]", DEFARG, $lastunit->id) . "</td></tr>\n";
				}

		?>
		<tr>
			<td colspan="2"><hr /></td>
		</tr>
		<tr>
			<td><strong><?php print htmlentities_utf8(xlate("Choose Date Range")); ?></strong></td>
			<td><?php print htmlentities_utf8(xlate("For a single day report enter start date only.")); ?></td>
		</tr>
		<tr>
			<td><strong><?php print htmlentities_utf8(xlate("Start Date")); ?></strong></td>
			<td><strong><?php print htmlentities_utf8(xlate("End Date")); ?></strong></td>
		</tr>
			<tr>
			<td><?php print dateChooser("rptfilters[startdate]","size:=8, onchange:=changeDates(this);"); ?> </td>
			<td><?php print dateChooser("rptfilters[enddate]","size:=8, onchange:=changeDates(this);"); ?></td>
		</tr>
		
		<tr>
			<td colspan="2"><hr /></td>
		</tr>
		<tr>
			<td><strong><?php print htmlentities_utf8(xlate("Or Choose Month(s)")); ?></strong></td>
			<td><?php print htmlentities_utf8(xlate("Not required if dates entered.")); ?></td>
		</tr>
		<tr>
			<td width="40%"><strong><?php print htmlentities_utf8(xlate("Month")); ?></strong></td>
			<td width="40%"><strong><?php print htmlentities_utf8(xlate("Year")); ?></strong></td>
		</tr>
		<tr>
			<td><?php print dropbox("rptfilters[month]", $month_options, "onchange:=decideOnDates(this.form,'month');"); ?></td>
			<td><?php print dropbox("rptfilters[year]", $year_options, "onchange:=decideOnDates(this.form,'month');"); ?></td>
		</tr>
		<tr>
			<td><strong><?php print htmlentities_utf8(xlate("Time Period")); ?></strong></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<label><?php print radiobutton("rptfilters[nummonths]", "value:=1, onchange:=decideOnDates(this.form,'month');"); ?> <?php print htmlentities_utf8(xlate("1 Month")); ?></label> &nbsp;
				<label><?php print radiobutton("rptfilters[nummonths]", "value:=3, onchange:=decideOnDates(this.form,'month');"); ?> <?php print htmlentities_utf8(xlate("3 Months")); ?></label> &nbsp;
				<label><?php print radiobutton("rptfilters[nummonths]", "value:=6, onchange:=decideOnDates(this.form,'month');"); ?> <?php print htmlentities_utf8(xlate("6 Months")); ?></label> &nbsp;
				<label><?php print radiobutton("rptfilters[nummonths]", "value:=12, onchange:=decideOnDates(this.form,'month');"); ?> <?php print htmlentities_utf8(xlate("12 Months")); ?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2"><strong><?php print htmlentities_utf8(xlate("Show Calls with This Fault Only")); ?></strong></td>
		</tr>
		<tr>
			<td colspan="2"><?php

			$faultarea_options = array("" => xlate("Choose"))+getFaults("area", $lastunit->UnitTypeID);
			print dropbox("rptfilters[faultarea.faultareaid]", $faultarea_options);

			?></td>
		</tr>
		<tr>
			<td colspan="2"><strong><?php print htmlentities_utf8(xlate("Group By")); ?></strong></td>
		</tr>
		<tr>
			<td colspan="2"><?php

				$groupby_options = array('' => xlate('Auto'), "building" => xlate("Building"), "unit" => xlate("Unit"), "months" => xlate("Months"));
				print dropbox("rptfilters[groupby]", $groupby_options);

			?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="text-align:right"><input type="submit" value=" <?php print htmlentities_utf8(xlate("CREATE")); ?> " /></td>
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
