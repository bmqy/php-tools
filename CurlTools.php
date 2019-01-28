<?php

header('content-type:text/html;charset=utf-8');

/**
 * 跟curl相关的工具类
 * Class CurlsTools
 */

class CurlsTools{


    /**
     * @desc PHP get请求之发送数组
     * @param $url
     * @param array $param
     * @return mixed
     * @throws Exception
     */
    public function httpGet($url, $gzip = false, $ua='', $ck=''){

        $ch = curl_init($url);
        $uas = [
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11',
            'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Maxthon 2.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; TencentTraveler 4.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; The World)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SE 2.X MetaSr 1.0; SE 2.X MetaSr 1.0; .NET CLR 2.0.50727; SE 2.X MetaSr 1.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)'
        ];
        $c_ua = count($uas);
        if ($ua==='') {
            curl_setopt($ch,CURLOPT_USERAGENT,$uas[rand(0, $c_ua - 1)]);
        }else{
            curl_setopt($ch,CURLOPT_USERAGENT,$ua);
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($ck!==''){
            curl_setopt($ch, CURLOPT_COOKIE, $ck);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if($gzip) curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 关键在这里
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }


    /**
     * @desc PHP post请求之发送数组
     * @param $url
     * @param array $param
     * @return mixed
     * @throws Exception
     */
    public function httpsPost($url, $param=array(), $ua='', $ck='')
    {
        $ch = curl_init($url);
        if($ua===''){
            curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022)");
        }else{
            curl_setopt($ch,CURLOPT_USERAGENT,$ua);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($param));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($ck===''){
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . C('CURL_SESSION_ID'));
        }else{
            curl_setopt($ch, CURLOPT_COOKIE, $ck);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;

    }



    /**
     * @desc 使用curl获取远程数据
     * @param  string $url url连接路径
     * @return string      获取到的数据
     */
    function curlGetContents($url){

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
        curl_setopt($ch, CURLOPT_HEADER,1);               //是否显示头部信息
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);        //设置 referer
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);          //跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果

        //这个是重点，加上这个便可以支持http和https下载
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }


    /**
     * @desc 异步将远程链接上的内容(图片或内容)写到本地
     * @param $url    远程地址
     * @param $saveName    保存在服务器上的文件名
     * @param $path    保存路径
     * @return boolean
     */
    function putFileFromUrlContent($url, $saveName, $path) {

        // 设置运行时间为无限制
        set_time_limit ( 0 );
        $url = trim ( $url );
        $curl = curl_init ();
        // 设置你需要抓取的URL
        curl_setopt ( $curl, CURLOPT_URL, $url );
        // 设置header
        curl_setopt ( $curl, CURLOPT_HEADER, 0 );

        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );

        //这个是重点，加上这个便可以支持http和https下载
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // 运行cURL，请求网页
        $file = curl_exec ( $curl );

        // 关闭URL请求
        curl_close ( $curl );

        // 将文件写入获得的数据
        $filename = $path . $saveName;
        $write = @fopen ( $filename, "w" );
        if ($write == false) {
            return false;
        }
        if (fwrite ( $write, $file ) == false) {
            return false;
        }
        if (fclose ( $write ) == false) {
            return false;
        }
        return true;

    }
    //    $url = "https://www.52linmin.wang/upload/portal/20171104/cb1511a3b30ef088c358286110823309.jpg";
    //    $saveName = 'whm.jpg';
    //    $path = "./"; //保存在当前目录下
    //    $res = putFileFromUrlContent($url,$saveName,$path);
    //    var_dump($res);// 当返回为true时，代表成功，反之，为失败


    /**
     * @desc 使用代理抓取页面, 为什么要使用代理进行抓取呢？以google为例吧，如果去抓google的数据，短时间内抓的很频繁的话，你就抓取不到了
     * @desc google对你的ip地址做限制这个时候，你可以换代理重新抓。
     * @param $url
     * @return mixed
     */
    public function httpProxy($url){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //是否通过http代理来传输
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
        // curl_setopt($ch, CURLOPT_PROXY, 125.21.23.6:8080);
        curl_setopt($ch, CURLOPT_PROXY, 'ip:端口号');
        //url_setopt($ch, CURLOPT_PROXYUSERPWD, 'user:password');如果要密码的话，加上这个
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;

    }



    /**
     * @desc 继续保持本站session的调用, 在实现用户同步登录的情况下需要共享session,如果要继续保持本站的session,那么要把session_id放到http请求中
     * @param $url
     * @return mixed
     */
    public function httpSession($url){

        $session_str = session_name().'='.session_id().'; path=/; domain=.explame.com';
        session_write_close(); //将数据写入文件并且结束session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, $session_str);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;

    }


}