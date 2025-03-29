<?php
header('Content-type: application/json');
set_time_limit(0);
ini_set("memory_limit", "-1");
define('NO_SESSION_REQUIRED', TRUE);
define('NO_USER_REQUIRED', TRUE);
define('NO_PERMISSION_REQUIRED', TRUE);
include_once("{$_SERVER['DOCUMENT_ROOT']}/../sys/libs/init.lib");
global $db;
$header = apache_request_headers();
if(!isset($header['Authorization'])){
    $response = [
        'code'=>'500',
        'message'=>'Prohibit access'
    ];
    logger_info(json_encode($response),'','','run','run opt');
    exit;
}
$header = $header['Authorization'];
$config = parse_ini_file(BASE_DIR.'/.restfulapi.authentication.ini', true);
if (isset($config['RUN_OPTMIZATION_API'])) {
    $config = $config['RUN_OPTMIZATION_API'];
}else{
    $response = [
        'code'=>'400',
        'message'=>'not config pwd',
    ];
    logger_info(json_encode($response),'','','run','run opt');
    exit;
}

$username = $config['view-name'];
$password = $config['view-password'];
$credentials = base64_encode("$username:$password");
if($header == 'Basic '.base64_encode($username.':'.$password)){
    $data = file_get_contents("php://input");
    logger_info('info:'.$data,'','','run','run opt');
    $data = json_decode($data,true);
    $requestId = $data['service_run_opt_request']['Id'];
    $time = $db->quote(LocalToGmt());
    if($data['errorinfo']['FailingNode'] == 'none'){
        $db->update('service_run_opt_request', [
            'OptResult' => $db->quote($data['service_run_opt_request']['OptResult']),
            'DeviationActual' => $db->quote($data['service_run_opt_request']['DeviationActual']),
            "OptRunQty"=> $db->quote($data['service_run_opt_request']['OptRunQty']),
            "MaxRunHour"=> $db->quote($data['service_run_opt_request']['MaxTargetRunHours']),
            "MaxTargetRunHoursActual"=> $db->quote($data['service_run_opt_request']['MaxTargetRunHoursActual']),
            'LastModifiedDate'=> $time,
            'LastModifiedBy'=> 0,
        ], "Id = ".$db->quote($data['service_run_opt_request']['Id']));
    }else {
        $db->update('service_run_opt_request', [
            'OptResult' => 2,
            'LastModifiedDate' => $time,
            'LastModifiedBy' => 0,
        ], "Id = " . $db->quote($data['errorinfo']['requestId']));
        $response = [
            'code'=>'500',
            'message'=>'error in bi',
            'data' => $data
        ];
        logger_info(json_encode($response),'','','run','run opt');
        exit();
    }
    foreach ($data['service_run_opt_request_detail'] as $building){
        $db->update('service_run_opt_request_detail', [
            'OptRunSeq'=> $db->quote($building['OptRunSeq']),
            'OptResult'=> $db->quote($building['OptResult']),
            'LastModifiedDate'=> $time,
            'LastModifiedBy'=> 0,
        ], "RequestId = ".$db->quote($building['RequestId'])." AND BuildingId = ".$db->quote($building['BuildingId']));
    }
    $response = [
        'code'=>'200',
        'result'=>'Success!',
        'data' => $data
    ];
    logger_info(json_encode($response),'','','run','run opt');
    exit();
}else{
    $response = [
        'code'=>'500',
        'message'=>'wrong pwd',
    ];
    echo json_encode($response);exit;
}
