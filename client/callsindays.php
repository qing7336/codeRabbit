<?php

	define('NO_PERMISSION_REQUIRED', TRUE);
	require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
	$currentpage = htmlentities_utf8(xlate("? Calls in ?? Days"));
	require_once(SYSTEM_DIR . "/includes/clientheader.php");
	if (!$_REQUEST["rptfilters"]["preset"]) $rptfilters["preset"] = "2in7";
	$rptfilters["summary"] = 0;

?>

<!-- begin form -->
<div class="formcell">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="40%" valign="top">
		<form action="/common/report.php" method="get" onsubmit="frmpopup(this,800,600);"><input type="hidden" name="rpt" value="CallsInDays" /><input type="hidden" name="rptfilters[clientonly]" value="1" />
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="formbody">
          <tr>
            <th width="40%"><?php print htmlentities_utf8(xlate("Reports")); ?></th>
          </tr>
          <tr>
            <td><strong><?php print htmlentities_utf8(xlate("Choose a Frequency")); ?></strong></td>
          </tr>
          <tr>
            <td>
				<label><?php print radiobutton("rptfilters[preset]", "VALUE:=2in7"); ?> <?php print htmlentities_utf8(xlate("2 IN 7")); ?></label> &nbsp;
				<label><?php print radiobutton("rptfilters[preset]", "VALUE:=3in30"); ?> <?php print htmlentities_utf8(xlate("3 IN 30")); ?></label> &nbsp;
				<?php print radiobutton("rptfilters[preset]", "VALUE:=other") . " " . textbox("rptfilters[calls]", "size:=2, onfocus:=this.form.elements['rptfilters[preset]'][2].checked=true;") . " IN " . textbox("rptfilters[days]", "size:=2, onfocus:=this.form.elements['rptfilters[preset]'][2].checked=true;"); ?>
			</td>
          </tr>
          <tr>
            <td><strong><?php print htmlentities_utf8(xlate("Include")); ?></strong></td>
          </tr>
          <tr>
            <td>
				<label><?php print radiobutton("rptfilters[summary]", "VALUE:=0"); ?> <?php print htmlentities_utf8(xlate("Full Details")); ?></label> &nbsp;
				<label><?php print radiobutton("rptfilters[summary]", "VALUE:=1"); ?> <?php print htmlentities_utf8(xlate("Summary Only")); ?></label>
			</td>
          </tr>
          <tr>
            <td style="text-align:right"><input type="submit" value=" <?php print htmlentities_utf8(xlate("CREATE")); ?> " />
            </td>
          </tr>
          <tr>
            <td><hr /></td>
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
