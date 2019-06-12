## mock服务
```

```

## 使用 php 内置 web 运行
```
cd public 
php -S 127.0.0.1:8001
```

## nginx配置
```
 server {

    listen 8001;
    server_name 127.0.0.1;

    set  $host_path "/data/web/mock";

    access_log  /data/logs/mock.access.log;
    error_log   /data/logs/mock.error.log;

    root   /data/web/data-mock/public;

    location / {
        index  index.html index.htm index.php;
        try_files $uri $uri/ /index.php$uri?$query_string;

    }
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   html;
    }

    include enable-php-pathinfo.conf;   
}
```
## 配置示列
### 正常配置(返回json)
```
test/show.1.php

return [
    'method' => 'GET',
    'input' => [
    ],
    'output' => [
        'file' => 'show.1.json'
    ]
];

test/show.1.json

{
    "info": "请求成功",
    "code": 1000,
    "data": {
        "ave_communication_cost": "88.144",
        "active_frequency": "0.12",
        "net_time": "2018年01月"
    }
}

curl http://127.0.0.1:8001/test/show/1
```

### 只需要配置json(默认返回 uft-8 json)
```
test/show.2.json

{
    "msg": "success",
    "code": 0,
    "data": {
        "score": "88.144"
    }
}

http://127.0.0.1:8001/test/show/2
```

### 输出 gbk编码的 json
```
test/gbk.php

return [
    'method' => 'GET',
    'input' => [
    ],
    'header' => [
        'Content-Type:application/json;charset=GBK'
    ],
    'output' => [
        'content_type' => 'json',
        'file' => 'gbk.json'
    ]
];

test/gbk.json

{
    "msg": "success",
    "code": 0,
    "data": {
        "score": "88"
    }
}

curl http://127.0.0.1:8001/test/gbk
```

### 输出 xml
```
test/xml.php

return [
    'method' => 'GET',
    'input' => [
    ],
    'header' => [
        'Content-Type:application/xml;charset=utf-8'
    ],
    'output' => [
        'file' => 'xml.xml'
    ]
];

test/xml.xml

<?xml version="1.0" encoding="UTF-8"?>
<note>
  <to>Tove</to>
  <from>Jani</from>
  <heading>中文</heading>
  <body>Don't forget me this weekend!</body>
</note>

curl http://127.0.0.1:8001/test/xml
```

### 输出 jpg(二进制)
```
test/jpg.php

return [
    'method' => 'GET',
    'input' => [
    ],
    'header' => [
        'Content-Type:image/jpg'
    ],
    'output' => [
        'file' => '1.jpg'
    ]
];


curl http://127.0.0.1:8001/test/jpg
```

### 通过参数输出不同的返回
```
test/param.php

return [
    'method' => 'GET',
    'input' => [
    ],
    'output' => [
        'file' => 'param.{val}.json'
    ]
];

test/param.1.json

{
    "msg": "success",
    "code": 0,
    "data": {
        "param" : 1
    }
}

test/param.2.json

{
    "msg": "success",
    "code": 0,
    "data": {
        "param" : 2
    }
}

curl http://127.0.0.1:8001/test/param?val=1
curl http://127.0.0.1:8001/test/param?val=2
```