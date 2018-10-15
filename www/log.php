<?php
$file = $_GET['date'];
$logPath = './log/log-'.$file.'.php';
if (file_exists($logPath)) {//如果存在则删除更新
    if(@unlink($logPath)){
        echo "yes";
    }else{
        echo "no";
    }
}else{
    echo "no file";
    echo 'log file:'.$logPath.'not exist!';
}