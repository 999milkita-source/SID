<?php
function rate_limit($key, $limit = 5, $seconds = 300){
    if(!isset($_SESSION['rate'][$key])){
        $_SESSION['rate'][$key] = ['count'=>1,'time'=>time()];
        return true;
    }
    $data = $_SESSION['rate'][$key];
    if(time() - $data['time'] > $seconds){
        $_SESSION['rate'][$key] = ['count'=>1,'time'=>time()];	
        return true;
    }
    if($data['count'] >= $limit){
        return false;
    }
    $_SESSION['rate'][$key]['count']++;
    return true;
}
