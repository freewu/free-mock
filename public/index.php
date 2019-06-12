<?php
// mock地址
define("MOCK_PATH",__DIR__."/../mock/");

$uri = isset($_SERVER['PATH_INFO'])? $_SERVER['PATH_INFO'] : "";
if(empty($uri))
{
    // 有可能没有配置pathinfo的处理 使用REQUEST_URI
    $t = $_SERVER['REQUEST_URI'];
    $t = explode("?",$t);
    $path_info = explode("/", trim($t[0],"/"));
    //$path_info = array_slice($path_info,1);
} else {
    $path_info = explode('/', trim($_SERVER['PATH_INFO'],"/"));
}

$d = array_shift($path_info);
// 兼容一下 之前挖的坑
if($d == "mock")
{
    $d = array_shift($path_info);
}

$file = constant("MOCK_PATH")."/{$d}/".implode(".",$path_info).".php";
if(!is_file($file)) 
{
    // 判断是否只配置了.json文件
    $json_file = constant("MOCK_PATH")."/{$d}/".implode(".",$path_info).".json";
    if(is_file($json_file)) die(file_get_contents($json_file));
    error(404,"missing file: ".$file);
}

$config = include_once($file);

// todo 验证入参

// 头部输出
if(isset($config["header"]) && $config["header"])
{
    if(is_array($config["header"]))
    {
        foreach($config["header"] as $row)
        {
            header($row);
        }
    } else {
        header($config["header"]);
    }
} else {
    header("Content-type:application/json;charset=utf-8");
}

// 输出
// 优先输出文件内容
if (isset($config['output']['file']) && $config['output']['file']) 
{
    $output_file = constant("MOCK_PATH")."{$d}/". $config['output']['file'];
    
    // 处理 yf.v1.query-data 这种 bt 接口
    $req = $_REQUEST;
    // 通过body传入json
    $body =  isset($GLOBALS['rawContent']) ? $GLOBALS['rawContent'] : file_get_contents("php://input");
    if($body)
    {
        $body = @json_decode($body, true);
        if($body && is_array($body))
        {
            foreach($body as $key => $row) $req[$key] = $row;
        }
    }
    $output_file = parameter_path($output_file,$req);

    if (file_exists($output_file))
    {
        die(file_get_contents($output_file));
    }
} else if(isset($config['output']['content']) && $config['output']['content']) {
    die($config['output']['content']);
}
error(500,"missing content");


function error($code = 404,$msg = "")
{
    $params = [
        "code" => $code,
        "msg" => $msg
    ];
    die(json_encode($params));
}

function parameter_path($url,$map)
{
    if(empty($map)) return $url;
    $data = [];
    foreach($map as $key => $row)
    {
        $data["{".$key."}"] = is_string($row)? $row : json_encode($row);
    }
    return str_replace(array_keys($data),array_values($data),$url);
}