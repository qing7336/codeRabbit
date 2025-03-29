<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};

	define('NO_PERMISSION_REQUIRED', TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	$currentpage = htmlentities_utf8(xlate("Recommended Improvements"));
	$rptfilters["improvement.status"] = '*';
	require_once(SYSTEM_DIR . "/includes/clientheader.php");
?>

<!-- begin form -->
<div class="formcell">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="40%" valign="top">
			<form action="/common/report.php" method="get" onsubmit="if (emptyElement(this['rptfilters[building.id]'], <?php print stringtojs(xlate("building")); ?>)) return false; frmpopup(this,800,600);"><input type="hidden" name="rpt" value="RecommendedImprovements" /><input type="hidden" name="rptfilters[clientonly]" value="1" /><input type="hidden" name="rptfilters[start_month]" value="1" /><input type="hidden" name="rptfilters[start_day]" value="1" /><input type="hidden" name="rptfilters[period]" value="+1y" />
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
			<tr>
				<th>Reports</th>
			</tr>
			<?php

					$building_options = array("" => xlate("Choose"));
					$lastbuilding = null;
					$sql = "SELECT b.id, " . sql_local_en('b.Premises') . " AS Premises, " . sql_local_en('br.Code') . " AS Code FROM unit u JOIN bank ba JOIN building b JOIN user_building ub JOIN branch br WHERE ub.UserID = {$db->quote($currentclient->id)} AND ub.BuildingID = b.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.BranchID = br.id AND u.{$branchcriteria} ORDER BY ".sql_local_en('b.Premises');

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
				<td><strong><?php print htmlentities_utf8(xlate("Include")); ?></strong></td>
			</tr>
			<tr>
				<td>
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=*'); ?> <?php print htmlentities_utf8(xlate("All")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=wip'); ?> <?php print htmlentities_utf8(xlate("WIP")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=open'); ?> <?php print htmlentities_utf8(xlate("Open")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=completed'); ?> <?php print htmlentities_utf8(xlate("Completed")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=declined'); ?> <?php print htmlentities_utf8(xlate("Declined")); ?></label>
				</td>
			</tr>
			<tr>
				<td><strong><?php print htmlentities_utf8(xlate("Choose a Year (Completed/Declined)")); ?></strong></td>
			</tr>
			<tr>
				<td><?php

					$year_options = array('' => htmlentities_utf8(xlate("Show All")));
					$tmp = $db->get('SELECT YEAR(IFNULL(LEAST(MIN(ProjectCompletedDate),MIN(DeclinedDate)),CURDATE())) AS MinYear, YEAR(IFNULL(GREATEST(MAX(ProjectCompletedDate),MAX(DeclinedDate)),CURDATE())) AS MaxYear FROM improvement');
					for ($i = $tmp->MinYear; $i <= $tmp->MaxYear; $i++) $year_options[$i] = $i;
					print dropbox('rptfilters[start_year]', $year_options);

				?></td>
			</tr>
			<tr>
				<td style="text-align: right;"><input type="submit" value=" <?php print htmlentities_utf8(xlate("CREATE")); ?> "></td>
			</tr>
			<tr>
				<td><hr /></td>
			</tr>
			</table>
			</form>
			<form action="/common/report.php" method="get" onsubmit="frmpopup(this,800,600);"><input type="hidden" name="rpt" value="RecommendedImprovements" /><input type="hidden" name="rptfilters[clientonly]" value="1" /><input type="hidden" name="rptfilters[start_month]" value="1" /><input type="hidden" name="rptfilters[start_day]" value="1" /><input type="hidden" name="rptfilters[period]" value="+1y" />
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
			<tr>
				<td><strong><?php print htmlentities_utf8(xlate("All Buildings")); ?></strong></td>
			</tr>
			<tr>
				<td>
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=*'); ?> <?php print htmlentities_utf8(xlate("All")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=wip'); ?> <?php print htmlentities_utf8(xlate("WIP")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=open'); ?> <?php print htmlentities_utf8(xlate("Open")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=completed'); ?> <?php print htmlentities_utf8(xlate("Completed")); ?></label> &nbsp;
					<label><?php print radiobutton('rptfilters[improvement.status]', 'value:=declined'); ?> <?php print htmlentities_utf8(xlate("Declined")); ?></label>
				</td>
			</tr>
			<tr>
				<td><strong><?php print htmlentities_utf8(xlate("Choose a Year (Completed/Declined)")); ?></strong></td>
			</tr>
			<tr>
				<td><?php

					print dropbox('rptfilters[start_year]', $year_options);

				?></td>
			</tr>
			<tr>
				<td style="text-align:right"><input type="submit" value=" <?php print htmlentities_utf8(xlate("CREATE")); ?> " /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
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
