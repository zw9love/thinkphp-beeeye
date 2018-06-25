
<?php
/**
 * Created by PhpStorm.
 * User: zengwei
 * Date: 2018/6/9
 * Time: 上午8:03
 */
//    echo date('h:i:s') . "<br>";
//
//    //sleep for 5 seconds
//    sleep(5);
//
//    //start again
//    echo date('h:i:s');
    $data = null;
    $msg = "成功";
    $status = 200;
    $postData = array(
        "data" => $data,
        "msg" => $msg,
        "status" => $status
    );
//    sleep(5);
    echo json_encode($postData);


