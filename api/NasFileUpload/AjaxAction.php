<?php
define('NO_PERMISSION_REQUIRED', TRUE);
require_once(dirname(__FILE__) . '/../../../sys/libs/init.lib');
$sAction = $_REQUEST['op'];
$aFileEentitykey = $_REQUEST['file_entitykey'];
if ($sAction == 'delfile') {
    if($aFileEentitykey['is_del'] == md5((string)false)){
        echo Json_encode(array('res' => 0, 'msg' => xlate('No permissions, please contact the administrator')));
    }else{
        deleteFile();
    }
} elseif ($sAction == 'downloadfile') {
    if($aFileEentitykey['is_down'] == md5((string)false)){
        echo "<script>alert('No permissions, please contact the administrator');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
    }else{
        downloadFile();
    }
} elseif ($sAction == 'uploadfile') {
    if($aFileEentitykey['is_up'] == md5((string)false)){
        echo Json_encode(array('res' => 0, 'msg' => xlate('No permissions, please contact the administrator')));
    }else{
        uploadFile();
    }
}
/**
 * For delete file
 * @return array
 * @version  2020-11-25 17:18:12 by Ciel Wang
 */
function deleteFile()
{
    $oUpload = new \VIEW\Util\FileStorage\ResponsiveFileUpload();
    try {
        $aFileEentitykey = $_REQUEST['file_entitykey'];
        $oUpload->setTable($aFileEentitykey['type'], $aFileEentitykey['id'], $aFileEentitykey['fileKey']);
        $iFileId = $_REQUEST['frm_version'];
        $sResultHtml = $oUpload->deleteFiles($iFileId, $aFileEentitykey);
        echo $sResultHtml;
        exit();
    } catch (Exception $e) {
        echo Json_encode(array('res' => 0, 'msg' => $e->getMessage()));
        exit();
    }
}

/**
 * For download file
 * @return array
 * @version  2020-11-25 17:18:12 by Ciel Wang
 */
function downloadFile()
{
    try {
        $oUpload = new \VIEW\Util\FileStorage\ResponsiveFileUpload();
        $aFileEentitykey = $_REQUEST['file_entitykey'];
        $oUpload->setTable($aFileEentitykey['type'], $aFileEentitykey['id'], $aFileEentitykey['fileKey']);
        $iFileId = $_REQUEST['fileid'];
        $oUpload->downloadFile($aFileEentitykey, $iFileId);
        exit;
    } catch (Exception $e) {
        echo Json_encode(array('res' => 0, 'msg' => $e->getMessage()));
        exit();
    }
}

/**
 * For upload file
 * @return array
 * @version  2020-11-25 17:18:12 by Ciel Wang
 */
function uploadFile()
{
    try {
        $oUpload = new \VIEW\Util\FileStorage\ResponsiveFileUpload();
        $aFileEentitykey = $_REQUEST['file_entitykey'];
        $oUpload->setTable($aFileEentitykey['type'], $aFileEentitykey['id'], $aFileEentitykey['fileKey']);
        $isAbleToDelete = md5((string)true) == $aFileEentitykey['is_del'] ? true : false;
        $isAbleToDownload = md5((string)true) == $aFileEentitykey['is_down'] ? true : false;
        $isAbleToUpload = md5((string)true) == $aFileEentitykey['is_up'] ? true : false;
        $isAbleToShowPhoto = md5((string)true) == $aFileEentitykey['is_showp'] ? true : false;
        $oUpload->setPermission($isAbleToDelete, $isAbleToDownload, $isAbleToUpload, $isAbleToShowPhoto);
        $aFile = $_FILES;
        $sChangelog = $_REQUEST['frm_changelog'];
        $sResult = $oUpload->uploadFiles($aFileEentitykey, $aFile, $sChangelog);
        echo Json_encode($sResult);
        exit();
    } catch (Exception $e) {
        echo Json_encode(array('res' => 0, 'msg' => $e->getMessage()));
        exit();
    }
}
