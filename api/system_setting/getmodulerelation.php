<?php
require_once('ViewArray.php');
use function VIEW\Util\ArrayUtil\ViewArray\{array_column, array_diff, array_filter, array_intersect, array_key_exists, array_keys, array_merge, array_search, array_unique, array_values, count, date, explode, implode, in_array, reset, round};
set_time_limit(0);
ini_set("memory_limit", "-1");
define('NO_SESSION_REQUIRED', TRUE);
define('NO_USER_REQUIRED', TRUE);
define('NO_PERMISSION_REQUIRED', TRUE);
define('SYSTEM_DIR', implode('/', array_slice(explode('/', $_SERVER["DOCUMENT_ROOT"]),0,-1)).'/sys');
include_once("{$_SERVER['DOCUMENT_ROOT']}/../sys/libs/init.lib");
require_once($_SERVER["DOCUMENT_ROOT"] . "/../sys/libs/jwt_wrapper/autoloader.php");
require_once(SYSTEM_DIR."/libs/logic/SystemAdmin/SystemSetting/service/GetSystemModuleService.php");
$header = apache_request_headers();
if(!isset($header['Authorization']) || isGlobalContext()){
    $response = [
        'code'=>'error',
        'message'=>'Prohibit access'
    ];

    echo json_encode($response);exit;
}
$header = $header['Authorization'];
$token = trim(substr($header,strrpos($header,' ')));

try{
    $data = Firebase\JWT\JWT::decode($token,'tkeseed',['HS256']);
}catch(Exception $e){
    $response = [
        'code'=>'error',
        'message'=>'access error'
    ];

    echo json_encode($response);exit;
}

$sysModule = new GetSystemModuleService();

$sysSettingModules = $sysModule->getSystemModuleInfo();

$response = [
    'code'=>'success',
    'result'=>$sysSettingModules
];

echo json_encode($response);




