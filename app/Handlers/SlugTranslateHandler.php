<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    public function translate($text)
    {
        // Instantiate HTTP client
        $http = new Client;

        // Initialize config data
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // If Baidu translate not configured, automatically use compatible pinyin scheme
        if(empty($appid) || empty($key)){
            return $this->pinyin($text);
        }

        // Based on API docs, generate sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+key MD5 value
        $sign = md5($appid . $text . $salt . $key);

        // create request query
        $query = http_build_query([
            'q' => $text,
            'from' => 'zh',
            'to' => 'en',
            'appid' => $appid,
            'salt' => $salt,
            'sign' => $sign,
        ]);

        // send HTTP GET request
        $response = $http->get($api.$query);

        $result = json_decode($response->getBody(), true);

        /**
        获取结果，如果请求成功，dd($result) 结果如下：

        array:3 [▼
            "from" => "zh"
            "to" => "en"
            "trans_result" => array:1 [▼
                0 => array:2 [▼
                    "src" => "XSS 安全漏洞"
                    "dst" => "XSS security vulnerability"
                ]
            ]
        ]

        **/

        // get result from translation
        if(isset($result['trans_result'][0]['dst'])){
            return str_slug($result['trans_result'][0]['dst']);
        }else{
            // If Baidu translate wasn't able to product result, use pinyin as backup
            return $this->pinyin($text);
        }
    }

    public function pinyin($text)
    {
        return str_slug(app(Pinyin::class)->permalink($text));
    }
}
