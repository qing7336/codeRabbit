<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};


	define('NO_PERMISSION_REQUIRED', TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
        require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/bizlogic.lib");
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/callrate.lib");

	if ($currentbranch === null) viewRedirect("entry.php");
	if ($currentclient === null) viewRedirect(CLIENT_CHOOSE);
	$currentpage = "{$currentbranch->Code} ".html_xlate("Summary");
	require_once(SYSTEM_DIR . "/includes/clientheader.php");

	$_Y = strftime("%Y");
	$_m = strftime("%m");
?>

<!-- begin form -->
<div class="formcell">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbodyline">
    <tr>
      <th colspan="40"><?php print htmlentities_utf8($currentbranch->Code); ?> <?php print html_xlate("Summary"); ?></th>
    </tr>
    <tr>
      <td><strong><?php print html_xlate("PREM."); ?></strong></td>
      <td><strong><?php print html_xlate("Units"); ?></strong></td>
      <td width="2%">&nbsp;</td>
      <td colspan="3" nowrap="nowrap"><strong><?php print html_xlate("CALLS"); ?></strong></td>
      <td width="2%">&nbsp;</td>
      <td colspan="6"><strong><?php print html_xlate("Call Rate"); ?></strong></td>
      <td width="2%">&nbsp;</td>
      <td colspan="11"><strong><?php print html_xlate("SWP (Num | Avg)"); ?></strong></td>
      <td width="2%">&nbsp;</td>
      <td colspan="11"><strong><?php print html_xlate("SWP A/H (Num | Avg)"); ?></strong></td>
      <td width="2%">&nbsp;</td>
      <td colspan="2"><strong><?php print html_xlate("Shutdowns"); ?></strong></td>
      <!--
      <td colspan="4"><strong>Shutdowns</strong></td>
	  <td width="2%">&nbsp;</td>
      <td><strong>Maintenance</strong></td>
      <td width="1%">&nbsp;</td>
      <td><strong>IMPROVE.</strong></td>
	  -->
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><strong><?php print html_xlate("OPEN"); ?></strong></td>
      <td>2/7</td>
      <td>3/30</td>
      <td>&nbsp;</td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m"),1,date("Y")))); ?></td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m")-1,1,date("Y")))); ?></td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><strong><?php print html_xlate("YTD"); ?></strong></td>
      <td>&nbsp;</td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m"),1,date("Y")))); ?></td>
      <td width="1%">&nbsp;</td>
      <td width="1%">&nbsp;</td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m")-1,1,date("Y")))); ?></td>
      <td width="1%">&nbsp;</td>
      <td width="1%">&nbsp;</td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><strong><?php print html_xlate("YTD"); ?></strong></td>
      <td width="1%">&nbsp;</td>
      <td>&nbsp;</td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m"),1,date("Y")))); ?></td>
      <td width="1%">&nbsp;</td>
      <td width="1%">&nbsp;</td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m")-1,1,date("Y")))); ?></td>
      <td width="1%">&nbsp;</td>
      <td width="1%">&nbsp;</td>
      <td width="1%"><img src="/img/form_target_head_small.gif" width="17" height="17" /></td>
      <td class="nowrap"><strong><?php print html_xlate("YTD"); ?></strong></td>
      <td width="1%">&nbsp;</td>
      <td>&nbsp;</td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m"),1,date("Y")))); ?></td>
      <td class="nowrap"><?php print strtoupper(strftime("%b", mktime(0,0,0,date("m")-1,1,date("Y")))); ?></td>
      <!--
      <td>YTD</td>
      <td>AV.HR</td>
	  <td>&nbsp;</td>
      <td>YTD (HOURS)</td>
      <td>&nbsp;</td>
      <td>YTD ($)</td>
	  -->
    </tr>
	<?php

		$curdate = "'" . strftime("%Y-%m-%d") . "'";

		$summary = array("units" => 0, "open" => 0, "2in7" => 0, "3in30" => 0, "targetcallrate" => 0, "targetswpresponse" => 0, "targetswpahresponse" => 0, "callratethismonth" => "0.0", "callratelastmonth" => "0.0", "callratethisyear" => "0.0", "swpthismonth" => "0", "swplastmonth" => "0", "swpthisyear" => "0", "swpahthismonth" => "0", "swpahlastmonth" => "0", "swpahthisyear" => "0", "swpthismonthavg" => "0", "swplastmonthavg" => "0", "swpthisyearavg" => "0", "swpahthismonthavg" => "0", "swpahlastmonthavg" => "0", "swpahthisyearavg" => "0", "shutdownsthismonth" => 0, "shutdownslastmonth" => 0, "shutdownsthisyear" => 0, "shutdownsavg" => "0.0");
		$data = array();

		$SQL = "SELECT b.id, " . sql_local_en('b.Premises') . " AS Premises, COALESCE(c.TargetCallRate, r.Target, br.TargetCallRate) AS TargetCallRate,\n"
			. "       IFNULL(c.SWPResponse, br.SWPResponse) AS SWPResponse,\n"
			. "       IFNULL(c.AfterHoursSWPResponse, br.AfterHoursSWPResponse) AS AfterHoursSWPResponse\n"
			. "  FROM building b JOIN bank ba JOIN unit u JOIN unit_run ur JOIN run r JOIN user_building ub JOIN branch br\n"
			. "       LEFT JOIN contract c ON c.id = " . sql_currentcontract() . "\n"
             //tank delete the unuseless filter for #217733 2015/03/23  （AND ur.UnitID = u.id ）
			 . " WHERE u.BankID = ba.id AND ba.BuildingID = b.id AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id}\n"
			 . "       AND ur.RunID = r.id AND {$BRANCHNOW} BETWEEN ur.FromDate AND ur.Cached_ToDate\n"
			 . "       AND br.id = u.BranchID AND br.id = {$currentbranch->id}\n"
			 . " GROUP BY b.id\n"
			 . " ORDER BY " . sql_local_en('b.Premises');
		$tmp_sumtargetcallrate = 0;
		$tmp_sumtargetswpresponse = 0;
		$tmp_sumtargetswpahresponse = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->id] = array("name" => $row->Premises, "units" => 0, "open" => 0, "2in7" => 0, "3in30" => 0, "targetcallrate" => $row->TargetCallRate, "targetswpresponse" => $row->SWPResponse, "targetswpahresponse" => $row->AfterHoursSWPResponse, "callratethismonth" => "0.0", "callratelastmonth" => "0.0", "callratethisyear" => "0.0", "swpthismonth" => "0", "swplastmonth" => "0", "swpthisyear" => "0", "swpahthismonth" => "0", "swpahlastmonth" => "0", "swpahthisyear" => "0", "swpthismonthavg" => "0", "swplastmonthavg" => "0", "swpthisyearavg" => "0", "swpahthismonthavg" => "0", "swpahlastmonthavg" => "0", "swpahthisyearavg" => "0", "shutdownsthismonth" => 0, "shutdownslastmonth" => 0, "shutdownsthisyear" => 0, "shutdownsavg" => "0.0");
			$tmp_sumtargetcallrate += $row->TargetCallRate;
			$tmp_sumtargetswpresponse += $row->SWPResponse;
			$tmp_sumtargetswpahresponse += $row->AfterHoursSWPResponse;
		}
		$summary["targetcallrate"] = sprintf("%0.1f", round(count($data) ? $tmp_sumtargetcallrate/count($data) : 0, 1));
		$summary["targetswpresponse"] = sprintf("%0.1f", round(count($data) ? $tmp_sumtargetswpresponse/count($data) : 0, 1));
		$summary["targetswpahresponse"] = sprintf("%0.1f", round(count($data) ? $tmp_sumtargetswpahresponse/count($data) : 0, 1));

		// Open calls column
		$SQL = "SELECT b.id AS BuildingID, COUNT(c.id) AS NumCalls "
			 . "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . " WHERE u.BankID = ba.id AND ba.BuildingID = b.id AND c.UnitID = u.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND (c.Cached_ArriveDate IS NULL OR c.Cached_OutDate IS NULL) "
			 . "       AND u.BranchID = {$currentbranch->id} "
             //tank add the filter for #217733
             . "       AND c.Cancelled = 0 "
			 . " GROUP BY b.id";
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["open"] = $row->NumCalls;
			$summary["open"] += $row->NumCalls;
		}

		// 2 in 7 column
		$SQL = "SELECT b.id AS BuildingID, COUNT(DISTINCT u.id) AS Calls2in7 "
			 . "  FROM `call` c1 JOIN `call` c2 JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . "       LEFT JOIN faultcause fc1 ON fc1.id = c1.FaultCauseID "
			 . "       LEFT JOIN faultcause fc2 ON fc2.id = c2.FaultCauseID "
			 . " WHERE c1.UnitID = u.id AND c2.UnitID = u.id AND u.InService = 1 AND u.BankID = ba.id AND ba.BuildingID = b.id AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND c1.LoggedDate >= {$curdate}-INTERVAL 7 DAY AND c2.LoggedDate >= {$curdate}-INTERVAL 7 DAY "
			 . "       AND c1.id < c2.id "
			 . "       AND IFNULL(fc1.NoFault, 0) = 0 AND c1.Cancelled = 0 AND c1.Interferance = 0 "
			 . "       AND IFNULL(fc2.NoFault, 0) = 0 AND c2.Cancelled = 0 AND c2.Interferance = 0 "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["2in7"] = $row->Calls2in7;
			$summary["2in7"] += $row->Calls2in7;
		}

		// 3 in 30 column
		$SQL = "SELECT b.id AS BuildingID, COUNT(DISTINCT u.id) AS Calls3in30 "
			 . "  FROM `call` c1 JOIN `call` c2 JOIN `call` c3 JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . "       LEFT JOIN faultcause fc1 ON fc1.id = c1.FaultCauseID "
			 . "       LEFT JOIN faultcause fc2 ON fc2.id = c2.FaultCauseID "
			 . "       LEFT JOIN faultcause fc3 ON fc3.id = c3.FaultCauseID "
			 . " WHERE c1.UnitID = u.id AND c2.UnitID = u.id AND c3.UnitID = u.id AND u.InService = 1 AND u.BankID = ba.id AND ba.BuildingID = b.id AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND c1.LoggedDate >= {$curdate}-INTERVAL 30 DAY AND c2.LoggedDate >= {$curdate}-INTERVAL 30 DAY AND c3.LoggedDate >= {$curdate}-INTERVAL 30 DAY "
			 . "       AND c1.id < c2.id AND c2.id < c3.id "
			 . "       AND IFNULL(fc1.NoFault, 0) = 0 AND c1.Cancelled = 0 AND c1.Interferance = 0 "
			 . "       AND IFNULL(fc2.NoFault, 0) = 0 AND c2.Cancelled = 0 AND c2.Interferance = 0 "
			 . "       AND IFNULL(fc3.NoFault, 0) = 0 AND c3.Cancelled = 0 AND c3.Interferance = 0 "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["3in30"] = $row->Calls3in30;
			$summary["3in30"] += $row->Calls3in30;
		}

		// Number of units column
		$SQL = "SELECT b.id AS BuildingID, COUNT(u.id) AS NumUnits "
			 . "  FROM unit u, bank ba, building b, user_building ub "
			 . " WHERE u.BankID = ba.id AND ba.BuildingID = b.id AND ub.BuildingID = b.id AND u.InService = 1 AND ub.UserID = {$currentclient->id} AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["units"] = $row->NumUnits;
			$summary["units"] += $row->NumUnits;
		}

		// Issue #14849 - new calculation of call rate, total calls
		$call_rate_dates = call_rate_client_prepare_dates($curdate);
		$call_rate_group_by = 'BuildingID';
		call_rate_client_prepare_summary_data($db, $currentclient, $call_rate_dates, $currentbranch->id, 'ba', 'BuildingID', $call_rate_group_by);

		// Call rate this month
		$CALL_RATE_SQL = call_rate_client_call_rate_sql($call_rate_dates['thismth_startdate'], $call_rate_dates['thismth_enddate'], 'NumCallsThisMonth', 'ContractDurationThisMonth', $call_rate_group_by);
		$tmp_num_branches = 0;
		$tmp_callrate = 0;
		while ($row = $db->getnext($CALL_RATE_SQL)) {
			$data[$row->$call_rate_group_by]["callratethismonth"] = sprintf("%0.1f", $row->CallRate);
			$tmp_callrate += $row->CallRate;
			$tmp_num_branches++;
		}
		$summary["callratethismonth"] = sprintf("%0.1f", $tmp_num_branches ? $tmp_callrate/$tmp_num_branches : 0);

		// Call rate previous month
		$CALL_RATE_SQL = call_rate_client_call_rate_sql($call_rate_dates['prevmth_startdate'], $call_rate_dates['prevmth_enddate'], 'NumCallsPrevMonth', 'ContractDurationPrevMonth', $call_rate_group_by);
		$tmp_num_branches = 0;
		$tmp_callrate = 0;
		while ($row = $db->getnext($CALL_RATE_SQL)) {
			$data[$row->$call_rate_group_by]["callratelastmonth"] = sprintf("%0.1f", $row->CallRate);
			$tmp_callrate += $row->CallRate;
			$tmp_num_branches++;
		}
		$summary["callratelastmonth"] = sprintf("%0.1f", $tmp_num_branches ? $tmp_callrate/$tmp_num_branches : 0);

		// Call rate YTD
		$CALL_RATE_SQL = call_rate_client_call_rate_sql($call_rate_dates['ytd_startdate'], $call_rate_dates['ytd_enddate'], 'NumCallsYTD', 'ContractDurationYTD', $call_rate_group_by);
		$tmp_num_branches = 0;
		$tmp_callrate = 0;
		while ($row = $db->getnext($CALL_RATE_SQL)) {
			$data[$row->$call_rate_group_by]["callratethisyear"] = sprintf("%0.1f", $row->CallRate);
			$tmp_callrate += $row->CallRate;
			$tmp_num_branches++;
		}
		$summary["callratethisyear"] = sprintf("%0.1f", $tmp_num_branches ? $tmp_callrate/$tmp_num_branches : 0);

		$SQL = "SELECT b.id AS BuildingID, COUNT(c.id) AS NumSWP, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate, (UNIX_TIMESTAMP(c.Cached_ArriveDate)-UNIX_TIMESTAMP(c.LoggedDate))/60, 0)) AS SWPResponse, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate,1,0)) AS NumReleaseDate "
			 . "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . "       LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
			 . " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 AND c.AfterHours = 0 "
			 . "       AND (c.SWP = 1 OR c.SWPOnArrival = 1) "
			 . "       AND MONTH(c.LoggedDate) = MONTH({$curdate}) AND YEAR(c.LoggedDate) = YEAR({$curdate}) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		$tmp_sumswp = 0;
		$tmp_numswprel = 0;
		$tmp_numswp = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["swpthismonth"] = $row->NumSWP; //. "/" . round($row->MaxSWPResponse);
			$data[$row->BuildingID]["swpthismonthavg"] = $row->NumReleaseDate ? round($row->SWPResponse/$row->NumReleaseDate) : 0;
			$tmp_sumswp += $row->SWPResponse;
			$tmp_numswp += $row->NumSWP;
			$tmp_numswprel += $row->NumReleaseDate;
		}
		if ($tmp_numswp > 0) {
			$summary["swpthismonth"] = $tmp_numswp; //. "/" . round($tmp_maxswp);
			$summary["swpthismonthavg"] = $tmp_numswprel ? round($tmp_sumswp/$tmp_numswprel) : 0;
		}

		$SQL = "SELECT b.id AS BuildingID, COUNT(c.id) AS NumSWP, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate, (UNIX_TIMESTAMP(c.Cached_ArriveDate)-UNIX_TIMESTAMP(c.LoggedDate))/60, 0)) AS SWPResponse, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate,1,0)) AS NumReleaseDate "
			 . "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . "       LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
			 . " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 AND c.AfterHours = 0 "
			 . "       AND (c.SWP = 1 OR c.SWPOnArrival = 1) "
			 . "       AND MONTH(c.LoggedDate) = MONTH({$curdate}-INTERVAL 1 MONTH) AND YEAR(c.LoggedDate) = YEAR({$curdate}-INTERVAL 1 MONTH) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		$tmp_sumswp = 0;
		$tmp_numswprel = 0;
		$tmp_numswp = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["swplastmonth"] = $row->NumSWP; //. "/" . round($row->MaxSWPResponse);
			$data[$row->BuildingID]["swplastmonthavg"] = $row->NumReleaseDate ? round($row->SWPResponse/$row->NumReleaseDate) : 0;
			$tmp_sumswp += $row->SWPResponse;
			$tmp_numswp += $row->NumSWP;
			$tmp_numswprel += $row->NumReleaseDate;
		}
		if ($tmp_numswp > 0) {
			$summary["swplastmonth"] = $tmp_numswp; //. "/" . round($tmp_maxswp);
			$summary["swplastmonthavg"] = $tmp_numswprel ? round($tmp_sumswp/$tmp_numswprel) : 0;
		}

		$SQL = "SELECT b.id AS BuildingID, COUNT(c.id) AS NumSWP, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate, (UNIX_TIMESTAMP(c.Cached_ArriveDate)-UNIX_TIMESTAMP(c.LoggedDate))/60, 0)) AS SWPResponse, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate,1,0)) AS NumReleaseDate "
			 . "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . "       LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
			 . " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 AND c.AfterHours = 0 "
			 . "       AND (c.SWP = 1 OR c.SWPOnArrival = 1) "
			 . "       AND YEAR(c.LoggedDate) = YEAR({$curdate}) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		$tmp_sumswp = 0;
		$tmp_numswprel = 0;
		$tmp_numswp = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["swpthisyear"] = $row->NumSWP; //. "/" . round($row->MaxSWPResponse);
			$data[$row->BuildingID]["swpthisyearavg"] = $row->NumReleaseDate ? round($row->SWPResponse/$row->NumReleaseDate) : 0;
			$tmp_sumswp += $row->SWPResponse;
			$tmp_numswp += $row->NumSWP;
			$tmp_numswprel += $row->NumReleaseDate;
		}
		if ($tmp_numswp > 0) {
			$summary["swpthisyear"] = $tmp_numswp; //. "/" . round($tmp_maxswp);
			$summary["swpthisyearavg"] = $tmp_numswprel ? round($tmp_sumswp/$tmp_numswprel) : 0;
		}

		$SQL = "SELECT b.id AS BuildingID, COUNT(c.id) AS NumSWP, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate, (UNIX_TIMESTAMP(c.Cached_ArriveDate)-UNIX_TIMESTAMP(c.LoggedDate))/60, 0)) AS SWPResponse, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate,1,0)) AS NumReleaseDate "
			 . "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . "       LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
			 . " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 AND c.AfterHours = 1 "
			 . "       AND (c.SWP = 1 OR c.SWPOnArrival = 1) "
			 . "       AND MONTH(c.LoggedDate) = MONTH({$curdate}) AND YEAR(c.LoggedDate) = YEAR({$curdate}) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		$tmp_sumswp = 0;
		$tmp_numswprel = 0;
		$tmp_numswp = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["swpahthismonth"] = $row->NumSWP; //. "/" . round($row->MaxSWPResponse);
			$data[$row->BuildingID]["swpahthismonthavg"] = $row->NumReleaseDate ? round($row->SWPResponse/$row->NumReleaseDate) : 0;
			$tmp_sumswp += $row->SWPResponse;
			$tmp_numswp += $row->NumSWP;
			$tmp_numswprel += $row->NumReleaseDate;
		}
		if ($tmp_numswp > 0) {
			$summary["swpahthismonth"] = $tmp_numswp; //. "/" . round($tmp_maxswp);
			$summary["swpahthismonthavg"] = $tmp_numswprel ? round($tmp_sumswp/$tmp_numswprel) : 0;
		}

		$SQL = "SELECT b.id AS BuildingID, COUNT(c.id) AS NumSWP, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate, (UNIX_TIMESTAMP(c.Cached_ArriveDate)-UNIX_TIMESTAMP(c.LoggedDate))/60, 0)) AS SWPResponse, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate,1,0)) AS NumReleaseDate "
			 . "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
			 . "       LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
			 . " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 AND c.AfterHours = 1 "
			 . "       AND (c.SWP = 1 OR c.SWPOnArrival = 1) "
			 . "       AND MONTH(c.LoggedDate) = MONTH({$curdate}-INTERVAL 1 MONTH) AND YEAR(c.LoggedDate) = YEAR({$curdate}-INTERVAL 1 MONTH) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		$tmp_sumswp = 0;
		$tmp_numswprel = 0;
		$tmp_numswp = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["swpahlastmonth"] = $row->NumSWP ."/" . round($row->NumReleaseDate ? $row->SWPResponse/$row->NumReleaseDate : 0) ; //. "/" . round($row->MaxSWPResponse);
			$data[$row->BuildingID]["swpahlastmonthavg"] = $row->NumReleaseDate ? round($row->SWPResponse/$row->NumReleaseDate) : 0;
			$tmp_sumswp += $row->SWPResponse;
			$tmp_numswp += $row->NumSWP;
			$tmp_numswprel += $row->NumReleaseDate;
		}
		if ($tmp_numswp > 0) {
			$summary["swpahlastmonth"] = $tmp_numswp; //. "/" . round($tmp_maxswp);
			$summary["swpahlastmonthavg"] = $tmp_numswprel ? round($tmp_sumswp/$tmp_numswprel) : 0;
		}

		$SQL = "SELECT b.id AS BuildingID, COUNT(c.id) AS NumSWP, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate, (UNIX_TIMESTAMP(c.Cached_ArriveDate)-UNIX_TIMESTAMP(c.LoggedDate))/60, 0)) AS SWPResponse, "
			 . "       SUM(IF(c.Cached_ArriveDate > c.LoggedDate,1,0)) AS NumReleaseDate "
			 . "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub JOIN run r "
			 . "       LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
			 . " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 AND c.AfterHours = 1 "
			 . "       AND (c.SWP = 1 OR c.SWPOnArrival = 1) "
			 . "       AND YEAR(c.LoggedDate) = YEAR({$curdate}) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		$tmp_sumswp = 0;
		$tmp_numswprel = 0;
		$tmp_numswp = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["swpahthisyear"] = $row->NumSWP; //. "/" . round($row->MaxSWPResponse);
			$data[$row->BuildingID]["swpahthisyearavg"] = $row->NumReleaseDate ? round($row->SWPResponse/$row->NumReleaseDate) : 0;
			$tmp_sumswp += $row->SWPResponse;
			$tmp_numswp += $row->NumSWP;
			$tmp_numswprel += $row->NumReleaseDate;
		}
		if ($tmp_numswp > 0) {
			$summary["swpahthisyear"] = $tmp_numswp; //. "/" . round($tmp_maxswp);
			$summary["swpahthisyearavg"] = $tmp_numswprel ? round($tmp_sumswp/$tmp_numswprel) : 0;
		}

		$summary['shutdownsthismonth'] = 0;
		$SQL = "SELECT b.id AS BuildingID, COUNT(s.id) AS NumShutdowns "
			 . "  FROM shutdown s, shutdown_unit su, unit u, bank ba, building b, user_building ub "
			 . " WHERE s.id = su.ShutdownID AND su.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND MONTH(s.ShutdownDate) = MONTH({$curdate}) AND YEAR(s.ShutdownDate) = YEAR({$curdate}) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["shutdownsthismonth"] = $row->NumShutdowns;
			$summary["shutdownsthismonth"] += $row->NumShutdowns;
		}

		$summary["shutdownslastmonth"] = 0;
		$SQL = "SELECT b.id AS BuildingID, COUNT(s.id) AS NumShutdowns "
			 . "  FROM shutdown s, shutdown_unit su, unit u, bank ba, building b, user_building ub "
			 . " WHERE s.id = su.ShutdownID AND su.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND MONTH(s.ShutdownDate) = MONTH({$curdate}-INTERVAL 1 MONTH) AND YEAR(s.ShutdownDate) = YEAR({$curdate}-INTERVAL 1 MONTH) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["shutdownslastmonth"] = $row->NumShutdowns;
			$summary["shutdownslastmonth"] += $row->NumShutdowns;
		}

		$SQL = "SELECT b.id AS BuildingID, COUNT(s.id) AS NumShutdowns, SUM(UNIX_TIMESTAMP(IFNULL(s.InserviceDate, {$BRANCHNOW}))-UNIX_TIMESTAMP(s.ShutdownDate))/60/60 AS SumShutdowns "
			 . "  FROM shutdown s, shutdown_unit su, unit u, bank ba, building b, user_building ub "
			 . " WHERE s.id = su.ShutdownID AND su.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
			 . "       AND YEAR(s.ShutdownDate) = YEAR({$curdate}) "
			 . "       AND u.BranchID = {$currentbranch->id} "
			 . " GROUP BY b.id";
		$tmp_sumshutdowns = 0;
		while ($row = $db->getnext($SQL)) {
			$data[$row->BuildingID]["shutdownsthisyear"] = $row->NumShutdowns;
			$data[$row->BuildingID]["shutdownsavg"] = sprintf("%0.1f", round($row->NumShutdowns ? $row->SumShutdowns/$row->NumShutdowns : 0,1));
			$summary["shutdownsthisyear"] += $row->NumShutdowns;
			$tmp_sumshutdowns += $row->SumShutdowns;
		}
		if ($summary["shutdownsthisyear"] > 0) $summary["shutdownsavg"] = sprintf("%0.1f", round($tmp_sumshutdowns/$summary["shutdownsthisyear"],1));

		$prev_month = strftime("%m", mktime(0,0,0,$_m-1,1,$_Y));
		$prev_year = strftime("%Y", mktime(0,0,0,$_m-1,1,$_Y));

		$i = 0;
		foreach ($data as $key => $row) {
            //tank modify the repfilters #217733 rptfilters[nummonths]=1 shuld remove
			print "<tr" . (($i++)%2 == 0 ? ' class="extratwo"' : '') . ">\n";
			print "\t<td class=\"nowrap\"><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReportBuilding&rptfilters[clientonly]=1&rptfilters[showgraphs]=1&rptfilters[building.id]={$key}&rptfilters[month]=" . $_m . "&rptfilters[year]=" . $_Y . "', 800, 600);\">" . htmlentities_utf8($row["name"]) . "</a></td>\n";
			print "\t<td>{$row["units"]}</td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=OpenCalls&rptfilters[clientonly]=1&rptfilters[building.id]={$key}&rptfilters[summary]=1', 800, 600);\">{$row["open"]}</a></strong></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallsInDays&rptfilters[clientonly]=1&rptfilters[building.id]={$key}&rptfilters[preset]=2in7', 800, 600);\">{$row["2in7"]}</a></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallsInDays&rptfilters[clientonly]=1&rptfilters[building.id]={$key}&rptfilters[preset]=3in30', 800, 600);\">{$row["3in30"]}</a></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["callratethismonth"] <= $row["targetcallrate"] ? "" : "no_") . "small.gif\" width=\"17\" height=\"17\" alt=\"Target {$row["targetcallrate"]}\" title=\"Target {$row["targetcallrate"]}\" /></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReportBuilding&rptfilters[clientonly]=1&rptfilters[showgraphs]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[month]=" . $_m . "&rptfilters[year]=" . $_Y . "', 800, 600);\">{$row["callratethismonth"]}</a></td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["callratelastmonth"] <= $row["targetcallrate"] ? "" : "no_") . "small.gif\" width=\"17\" height=\"17\" alt=\"Target {$row["targetcallrate"]}\" title=\"Target {$row["targetcallrate"]}\" /></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReportBuilding&rptfilters[clientonly]=1&rptfilters[showgraphs]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[month]={$prev_month}&rptfilters[year]={$prev_year}&rptfilters[nummonths]=1', 800, 600);\">{$row["callratelastmonth"]}</a></td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["callratethisyear"] <= $row["targetcallrate"] ? "" : "no_") . "small.gif\" width=\"17\" height=\"17\" alt=\"Target {$row["targetcallrate"]}\" title=\"Target {$row["targetcallrate"]}\" /></td>\n";
			print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReportBuilding&rptfilters[clientonly]=1&rptfilters[showgraphs]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[month]=1&rptfilters[year]=" . $_Y . "&rptfilters[nummonths]=" . $_m . "', 800, 600);\">{$row["callratethisyear"]}</a></strong></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["swpthismonthavg"] <= $row["targetswpresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$row["targetswpresponse"]}\" title=\"Target {$row["targetswpresponse"]}\" /></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpthismonth"]}</a></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpthismonthavg"]}</a></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["swplastmonthavg"] <= $row["targetswpresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$row["targetswpresponse"]}\" title=\"Target {$row["targetswpresponse"]}\" /></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$row["swplastmonth"]}</a></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$row["swplastmonthavg"]}</a></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["swpthisyearavg"] <= $row["targetswpresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$row["targetswpresponse"]}\" title=\"Target {$row["targetswpresponse"]}\" /></td>\n";
			print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpthisyear"]}</a></strong></td>\n";
			print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpthisyearavg"]}</a></strong></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["swpahthismonthavg"] <= $row["targetswpahresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$row["targetswpahresponse"]}\" title=\"Target {$row["targetswpahresponse"]}\" /></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpahthismonth"]}</a></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpahthismonthavg"]}</a></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["swpahlastmonthavg"] <= $row["targetswpahresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$row["targetswpahresponse"]}\" title=\"Target {$row["targetswpahresponse"]}\" /></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$row["swpahlastmonth"]}</a></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$row["swpahlastmonthavg"]}</a></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><img src=\"/img/form_target_" . ($row["swpahthisyearavg"] <= $row["targetswpahresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$row["targetswpahresponse"]}\" title=\"Target {$row["targetswpahresponse"]}\" /></td>\n";
			print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpahthisyear"]}</a></strong></td>\n";
			print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[building.id]=*&rptfilters[building.id]={$key}&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["swpahthisyearavg"]}</a></strong></td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=Shutdown&rptfilters[clientonly]=1&rptfilters[building.id]={$key}&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["shutdownsthismonth"]}</a></td>\n";
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=Shutdown&rptfilters[clientonly]=1&rptfilters[building.id]={$key}&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$row["shutdownslastmonth"]}</a></td>\n";
			/*
			print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=Shutdown&rptfilters[clientonly]=1&rptfilters[building.id]={$key}&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$row["shutdownsthisyear"]}</a></td>\n";
			print "\t<td>{$row["shutdownsavg"]}</td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td>-</td>\n";
			print "\t<td>&nbsp;</td>\n";
			print "\t<td>-</td>\n";
			*/
			print "</tr>\n";
		}

		print "<tr><td colspan=\"41\">&nbsp;</td></tr>\n";
		print "<tr>\n";
		print "\t<td><strong>" . htmlentities_utf8($currentbranch->Code) . "</strong></td>\n";
		print "\t<td>{$summary["units"]}</td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=OpenCalls&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[summary]=1', 800, 600);\">{$summary["open"]}</a></strong></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallsInDays&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[preset]=2in7', 800, 600);\">{$summary["2in7"]}</a></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallsInDays&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[preset]=3in30', 800, 600);\">{$summary["3in30"]}</a></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["callratethismonth"] <= $summary["targetcallrate"] ? "" : "no_") . "small.gif\" width=\"17\" height=\"17\" alt=\"Target {$summary["targetcallrate"]}\" title=\"Target {$summary["targetcallrate"]}\" /></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReportBuilding&rptfilters[clientonly]=1&rptfilters[showgraphs]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[month]=" . $_m . "&rptfilters[year]=" . $_Y . "&rptfilters[nummonths]=1', 800, 600);\">{$summary["callratethismonth"]}</a></td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["callratelastmonth"] <= $summary["targetcallrate"] ? "" : "no_") . "small.gif\" width=\"17\" height=\"17\" alt=\"Target {$summary["targetcallrate"]}\" title=\"Target {$summary["targetcallrate"]}\" /></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReportBuilding&rptfilters[clientonly]=1&rptfilters[showgraphs]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[month]={$prev_month}&rptfilters[year]={$prev_year}&rptfilters[nummonths]=1', 800, 600);\">{$summary["callratelastmonth"]}</a></td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["callratethisyear"] <= $summary["targetcallrate"] ? "" : "no_") . "small.gif\" width=\"17\" height=\"17\" alt=\"Target {$summary["targetcallrate"]}\" title=\"Target {$summary["targetcallrate"]}\" /></td>\n";
		print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReportBuilding&rptfilters[clientonly]=1&rptfilters[showgraphs]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[month]=1&rptfilters[year]=" . $_Y . "&rptfilters[nummonths]=" . $_m . "', 800, 600);\">{$summary["callratethisyear"]}</a></strong></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["swpthismonthavg"] <= $summary["targetswpresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$summary["targetswpresponse"]}\" title=\"Target {$summary["targetswpresponse"]}\" /></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpthismonth"]}</a></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpthismonthavg"]}</a></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["swplastmonthavg"] <= $summary["targetswpresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$summary["targetswpresponse"]}\" title=\"Target {$summary["targetswpresponse"]}\" /></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$summary["swplastmonth"]}</a></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$summary["swplastmonthavg"]}</a></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["swpthisyearavg"] <= $summary["targetswpresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$summary["targetswpresponse"]}\" title=\"Target {$summary["targetswpresponse"]}\" /></td>\n";
		print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpthisyear"]}</a></strong></td>\n";
		print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpthisyearavg"]}</a></strong></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["swpahthismonthavg"] <= $summary["targetswpahresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$summary["targetswpahresponse"]}\" title=\"Target {$summary["targetswpahresponse"]}\" /></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpahthismonth"]}</a></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*rptfilters[call.swp]=1&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpahthismonthavg"]}</a></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["swpahlastmonthavg"] <= $summary["targetswpahresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$summary["targetswpahresponse"]}\" title=\"Target {$summary["targetswpahresponse"]}\" /></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$summary["swpahlastmonth"]}</a></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$summary["swpahlastmonthavg"]}</a></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><img src=\"/img/form_target_" . ($summary["swpahthisyearavg"] <= $summary["targetswpahresponse"] ? "" : "no_") . "small.gif\" width=\"18\" height=\"18\" alt=\"Target {$summary["targetswpahresponse"]}\" title=\"Target {$summary["targetswpahresponse"]}\" /></td>\n";
		print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpahthisyear"]}</a></strong></td>\n";
		print "\t<td><strong><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=SWP&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[building.id]=*&rptfilters[call.swp]=1&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["swpahthisyearavg"]}</a></strong></td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=Shutdown&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[start_month]=" . $_m . "&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["shutdownsthismonth"]}</a></td>\n";
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=Shutdown&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[start_month]={$prev_month}&rptfilters[start_year]={$prev_year}&rptfilters[end_month]={$prev_month}&rptfilters[end_year]={$prev_year}', 800, 600);\">{$summary["shutdownslastmonth"]}</a></td>\n";
		/*
		print "\t<td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=Shutdown&rptfilters[clientonly]=1&rptfilters[branch.id]={$currentbranch->id}&rptfilters[start_month]=1&rptfilters[start_year]=" . $_Y . "&rptfilters[end_month]=" . $_m . "&rptfilters[end_year]=" . $_Y . "', 800, 600);\">{$summary["shutdownsthisyear"]}</a></td>\n";
		print "\t<td>{$summary["shutdownsavg"]}</td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td>Coming soon</td>\n";
		print "\t<td>&nbsp;</td>\n";
		print "\t<td>Coming soon</td>\n";
		*/
		print "</tr>\n";

?>
	</table>
	<br />
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="32%" valign="top">
		<!-- latest from all states all buildings -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbodyline">
			<tr>
				<th colspan="3"><?php print html_xlate("Latest Calls"); ?></th>
			</tr>
			<tr>
				<td width="20%"><strong><?php print html_xlate("Date"); ?></strong></td>
				<td><strong><?php print html_xlate("Premises"); ?></strong></td>
				<td width="5%"><strong><?php print html_xlate("Unit"); ?></strong></td>
			</tr>
			<?php
				$SQL = "SELECT c.id, DATE_FORMAT(c.LoggedDate, '%d %b %y') AS Date, " . sql_local_en('b.Premises') . " AS Building, u.LiftNumber "
						. "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
						. "       LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
						. " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 "
						. "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 "
						. "       AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} AND u.BranchID = {$currentbranch->id} "
						. "       AND c.SWP = 0 AND c.SWPOnArrival != 1 "
						. " ORDER BY c.LoggedDate DESC LIMIT 5";
				while ($row = $db->getnext($SQL)) {
					print "<tr><td nowrap>" . htmlentities_utf8($row->Date) . "</td><td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReport&rptfilters[clientonly]=1&rptfilters[call.id]={$row->id}', 800, 600);\">" . htmlentities_utf8($row->Building) . "</a></td><td class=\"nowrap\">" . htmlentities_utf8($row->LiftNumber) . "</td></tr>\n";
				}

			?>
			</table>
		</td>
		<td width="2%">&nbsp;</td>
		<td width="32%" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbodyline">
			<tr>
				<th colspan="3"><?php print html_xlate("Latest SWP"); ?></th>
			</tr>
			<tr>
				<td width="20%"><strong><?php print html_xlate("Date"); ?></strong></td>
				<td><strong><?php print html_xlate("Premises"); ?></strong></td>
				<td width="5%"><strong><?php print html_xlate("Unit"); ?></strong></td>
			</tr>
			<?php

				$SQL = "SELECT c.id, DATE_FORMAT(c.LoggedDate, '%d %b %y') AS Date, " .sql_local_en('b.Premises') . " AS Building, u.LiftNumber "
					. "  FROM `call` c JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
					. "	   LEFT JOIN faultcause fc ON fc.id = c.FaultCauseID "
					. " WHERE c.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 "
					. "       AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} "
					. "       AND IFNULL(fc.NoFault, 0) = 0 AND c.Cancelled = 0 AND c.Interferance = 0 "
					. "       AND (c.SWP = 1 OR c.SWPOnArrival = 1) AND u.BranchID = {$currentbranch->id} "
					. " ORDER BY c.LoggedDate DESC LIMIT 5";

				while ($row = $db->getnext($SQL)) {
					print "<tr><td nowrap>" . htmlentities_utf8($row->Date) . "</td><td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=CallReport&rptfilters[clientonly]=1&rptfilters[call.id]={$row->id}', 800, 600);\">" . htmlentities_utf8($row->Building) . "</a></td><td class=\"nowrap\">" . htmlentities_utf8($row->LiftNumber) . "</td></tr>\n";
				}

			?>
			</table>
		</td>
		<td width="2%">&nbsp;</td>
		<td width="32%" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formbodyline">
			<tr>
				<th colspan="3"><?php print html_xlate("Latest Shutdowns"); ?></th>
			</tr>
			<tr>
				<td width="20%"><strong><?php print html_xlate("Date"); ?></strong></td>
				<td><strong><?php print html_xlate("Premises"); ?></strong></td>
				<td width="5%"><strong><?php print html_xlate("Unit"); ?></strong></td>
			</tr>
			<?php
			$SQL = "SELECT s.id, DATE_FORMAT(s.ShutdownDate, '%d %b %y') AS Date, " . sql_local_en('b.Premises') . " AS Building, u.LiftNumber "
						. "  FROM shutdown s JOIN shutdown_unit su JOIN unit u JOIN bank ba JOIN building b JOIN user_building ub "
						. " WHERE s.id = su.ShutdownID AND su.UnitID = u.id AND u.BankID = ba.id AND ba.BuildingID = b.id AND u.InService = 1 "
						. "       AND ub.BuildingID = b.id AND ub.UserID = {$currentclient->id} AND u.BranchID = {$currentbranch->id} "
						. " ORDER BY s.ShutdownDate DESC LIMIT 5";
				while ($row = $db->getnext($SQL)) {
					print "<tr><td nowrap>" . htmlentities_utf8($row->Date) . "</td><td><a href=\"#\" onclick=\"return popup('/common/report.php?rpt=Shutdown&rptfilters[clientonly]=1&rptfilters[shutdown.id]={$row->id}', 800, 600);\">" . htmlentities_utf8($row->Building) . "</a></td><td class=\"nowrap\">" . htmlentities_utf8($row->LiftNumber) . "</td></tr>\n";
				}

			?>
			</table>
		</td>
	</tr>
	</table>
	<br />
	<?php
		if ($links = $db->get("clientaccesslinks", null, "*, FileTitle_val as FileTitle, FileDescription_val as FileDescription, WebDescription_val as WebDescription")) {
			if ($links->WebAddress != "" && $links->WebDescription != "") {
				if ($links->UploadedFile != "" && is_readable(FILE_DIR_CLIENTACCESSLINKS . "document.pdf") && $links->FileDescription != "") {
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
	<tr valign="top">
		<td width="66%">
			<table class="formbody" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
			<tr>
				<th colspan="2"><?php print html_xlate("TK Elevator"); ?></th>
			</tr>
			<tr valign="top">
				<td width="12%"><a href="/common/clientdocument_download.php?filename=document.pdf"><img src="/img/icon_tkin_big.gif" width="94" height="98" border="0" /></a></td>
				<td><a href="/common/clientdocument_download.php?filename=document.pdf"><strong><?php print $links->FileTitle; ?></strong></a> (<?php print html_xlate("PDF"); ?>)<br />
					<?php print $links->FileDescription; ?>
				</td>
			</tr>
			</tbody>
			</table>
		</td>
		<td width="2%">&nbsp;</td>
		<td width="32%">
			<table class="formbody" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
			<tr>
				<th><?php print html_xlate("Website"); ?></th>
			</tr>
			<tr>
				<td valign="top" width="5%"><a href="<?php print $links->WebAddress; ?>"><?php print substr($links->WebAddress, 7); ?></a><br /><?php print $links->WebDescription; ?></td>
			</tr>
			</tbody>
			</table>
		</td>
	</tr>
	</tbody>
	</table>
	<?php
				} else {
	?>
	<table class="formbody" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
	<tr>
		<th><?php print html_xlate("TK Elevator Website"); ?></th>
	</tr>
	<tr>
		<td valign="top" width="5%"><a href="<?php print $links->WebAddress; ?>"><?php print substr($links->WebAddress, 7); ?></a><br /><?php print $links->WebDescription; ?></td>
	</tr>
	</tbody>
	</table>
	<?php
				}
			} else if ($links->UploadedFile != "" && is_readable(FILE_DIR_CLIENTACCESSLINKS . "document.pdf") && $links->FileDescription != "") {
	?>
	<table class="formbody" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
	<tr>
		<th colspan="2"><?php print html_xlate("TK Elevator"); ?></th>
	</tr>
	<tr>
		<td rowspan="2" valign="top" width="12%"><a href="/common/clientdocument_download.php?filename=document.pdf"><img src="/img/icon_tkin_big.gif" width="94" height="98" border="0" /></a></td>
		<td><a href="/common/clientdocument_download.php?filename=document.pdf"><strong><?php print $links->FileTitle; ?></strong></a> (PDF)</td>
	</tr>
	<tr>
		<td valign="top"><?php print $links->FileDescription; ?></td>
	</tr>
	</tbody>
	</table>
	<?php
			}
		}
	?>
</div>
<!-- end form -->

<?php require_once(SYSTEM_DIR . "/includes/clientfooter.php"); ?>
