<?php
define('NO_USER_REQUIRED', true);
require_once("{$_SERVER["DOCUMENT_ROOT"]}/../sys/libs/init.lib");
require_once(SYSTEM_DIR . "/libs/jsonwrapper.php");

$aCountryArr = array();//all country data on APAC/ME/EA
$aSiteArr = array();//all site data on APAC/ME/EA
$aCountry = array();
$aSite = array();

$aEvnArr = array("DEV" => "dev", "DEV2" => "dev2", "RC" => "rc", "LIVE" => "");

$sql = "SELECT s.`id`,s.`ShortName`,s.`RCUrl`," . sql_local_en('st.Name') . " AS CountryName, s.DatabaseName
        FROM site s
        LEFT JOIN structure st ON st.`id` = s.`StructureID`
        WHERE s.IsActive = 1 AND s.`SubscribedToGlobal`=1";

while ($row = $db_read_service->getnext($sql))
{
    preg_match("/^https:\/\/(.+)?rc.fos.tkeasia.com/i", $row->RCUrl, $match);
    preg_match("/^https:\/\/(.+)?rc.tkelevator.com.cn/i", $row->RCUrl, $match1);//china new domain name
    preg_match("/^https:\/\/(.+)?rc.tkeview.com/i", $row->RCUrl, $match2);//frglobal and ams all country domain name
    $aCountry['Country'] = $row->ShortName;
    $aCountry['CountryName'] = $row->CountryName;
    $aCountry['CountryId'] = $row->id;
    array_push($aCountryArr, $aCountry);

    foreach($aEvnArr as $key=>$value)
    {
        $aSite['Country'] = $row->ShortName;
        $aSite['CountryId'] = $row->id;
        if (!empty($match))
        {
            $aSite['Site'] = "https://" . $match[1] . $value . ".fos.tkeasia.com/sharp/vsm";
        }elseif (!empty($match1))
        {
            $aSite['Site'] = "https://view" . ($value != '' ? "-" . $value : "") . ".tkelevator.com.cn/sharp/vsm";
        }elseif (!empty($match2))
        {
            $aSite['Site'] = "https://" . $match2[1] . $value . ".tkeview.com/sharp/vsm";
        }

        if ($row->DatabaseName == 'tkchina')
        {
            $aSite['Site'] = "https://view" . ($value != '' ? "-" . $value : "") . ".tkelevator.com.cn/sharp/vsm";
        }
        $aSite['Environment'] = $key;
        array_push($aSiteArr, $aSite);
    }
}

$aSiteList['Country'] = $aCountryArr;
$aSiteList['SiteList'] = $aSiteArr;
echo json_z_encode($aSiteList);
