<?php 
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};
	
	define('NO_PERMISSION_REQUIRED', TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/bizlogic.lib");
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/callrate.lib");

	//#142250 bug on Mtc item complete report
	$currentpage = htmlentities_utf8(xlate("Maintenance Complete Report"));

	require_once(SYSTEM_DIR . "/includes/clientheader.php");


	$restrictions = new FormRestrictions();
	$restrictions->add("rptfilters[year]", "^.+$", "You must choose a year or date range");

	$restrictions->add("rptfilters[startmonth]", "^.*$", "You must choose a year or select date range");
	$restrictions->add("rptfilters[startyear]", "^.*$", "You must choose a year or select date range");
	$restrictions->add("rptfilters[endmonth]", "^.*$", "You must choose a year or select date range");
	$restrictions->add("rptfilters[endyear]", "^.*$", "You must choose a year or select date range");

	$restrictions->js($JS["onload"]);


	// Generate year from 2009 until two years ahead
	$year_options[''] = "YEAR";
	for ($year = 2007; $year < date('Y') + 3; $year++) {
		$year_options[$year] = $year;

		if ($year == date('Y')) {
			$selected_year = $year;
		}
	}

	$month_options = month_options(1, TRUE, "Month");


	$JS["exec"][] = "
		function changeDates(datebox) {
			var frm = datebox.form;
			frm.elements['rptfilters[month]'].selectedIndex = 0;
			frm.elements['rptfilters[year]'].selectedIndex = 0;

			restrictions.change('rptfilters[startmonth]', '^.+$');
			restrictions.change('rptfilters[startyear]', '^.+$');
			restrictions.change('rptfilters[endmonth]', '^.+$');
			restrictions.change('rptfilters[endyear]', '^.+$');

			restrictions.change('rptfilters[year]', '^.*$');

			restrictions.test();
		}

		function changeMonth(monthbox) {
			var frm = monthbox.form;
			frm.elements['rptfilters[startmonth]'].value = '';
			frm.elements['rptfilters[startyear]'].value = '';
			frm.elements['rptfilters[endmonth]'].value = '';
			frm.elements['rptfilters[endyear]'].value = '';

			restrictions.change('rptfilters[startmonth]', '^.*$');
			restrictions.change('rptfilters[startyear]', '^.*$');
			restrictions.change('rptfilters[endmonth]', '^.*$');
			restrictions.change('rptfilters[endyear]', '^.*$');

			restrictions.change('rptfilters[year]', '^.+$');

			restrictions.test();
		}
	";

?>

<!-- begin form -->
<div class="formcell">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="40%" valign="top">
			<form action="/common/report.php" method="get" onsubmit="if (emptyElement(this['rptfilters[building.id]'], <?php print stringtojs(xlate("building")); ?>)) return false; frmpopup(this,800,600);"><input type="hidden" name="rpt" value="MaintenanceItemCompletedEvolution" />
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
				<tr>
					<th colspan="2"><?php print html_xlate("Reports"); ?></th>
				</tr>
				<tr>
					<td width="35%"><strong><?php print html_xlate("Choose a Building"); ?></strong></td>
					<?php //#142250 bug on Mtc item complete report ?>
					<td><?php print hidden("rptfilters[branch.id]", "value:={$GLOBALS['currentbranch']->id}") . textbox("rptfilters[building.id]_label", "size:=35, readonly:=1") . hidden("rptfilters[building.id]") . button("op", "onclick:=popup('/client/building_list.php?openerFormElementName=rptfilters[building.id]',900,600); return false;", xlate("Choose")); ?></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td><strong><?php print html_xlate("Choose Month / Year"); ?></strong></td>
					<td><?php print dropbox('rptfilters[month]', $month_options, 'onchange:=changeMonth(this);') . dropbox("rptfilters[year]", $year_options, "onchange:=changeMonth(this);", $selected_year); ?></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td><strong><?php print html_xlate("Or Between"); ?></strong></td>
					<td><?php print dropbox('rptfilters[startmonth]', $month_options, 'onchange:=changeDates(this);') . dropbox("rptfilters[startyear]", $year_options, "onchange:=changeDates(this);", ''); ?></td>
				</tr>
					
				<tr>
					<td><strong><?php print html_xlate("And"); ?></strong></td>
					<td><?php print dropbox('rptfilters[endmonth]', $month_options, 'onchange:=changeDates(this);') . dropbox("rptfilters[endyear]", $year_options, "onchange:=changeDates(this);", ''); ?></td>
				</tr>
					
				<tr>
					<td colspan="2" style="text-align:right"><input type="submit" value=" <?php print html_xlate("CREATE"); ?> " /></td>
				</tr>
				<tr>
					<td colspan="2"><hr /></td>
				</tr>
				</table>
			</form>
		</td>
		<td width="5%">&nbsp;</td>
		<td width="55%" valign="top">
			<iframe name="work" width="100%" height="300" frameborder="0" scrolling="no" src="/common/blank.php"></iframe>
		</td>
	</tr>
	</table>
</div>
<!-- end form -->

<?php require_once(SYSTEM_DIR . "/includes/footer.php"); ?>
