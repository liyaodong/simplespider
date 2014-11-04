<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Author: Dongdong
 * Mail: mail@liyaodong.com
 * Date: 2014年11月4日
*/


function curlGetHTML($url){
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 执行并输出到$data
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
function curlGetCookiesDisplay($url){
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    // 执行并输出到$data
    $data = curl_exec($curl);
    curl_close($curl);
    // 开始解析Cookies
    list($header, $body) = explode("\r\n\r\n", $data);
    preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
    $cookie = $matches[1];
    $data_array = array('cookies_string' => $cookie,'body' => $body );
    return $data_array;

}
function curlPostChangeCookies($url,$post_data,$cookie_string){
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置header
    curl_setopt($curl, CURLOPT_COOKIE, $cookie_string);
    curl_setopt($curl, CURLOPT_REFERER,'http://xxxxxx.com');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    // 执行并输出到$data
    $data = curl_exec($curl);
    // print_r($data);
    curl_close($curl);
    return $cookie_string;
}
function curlPostSaveCookies($url,$post_data,$referer = '0'){
    $post_data = json_decode($post_data);
    $post_string = '';
    foreach ($post_data as $key => $value) {
        $post_string = $post_string.'&'.$key."=".$value;
    }
    $post_string = $post_string.'&SUBMIT=Send';
    $post_string = substr($post_string, 1);
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置header
    if($referer !== '0') {
        curl_setopt($curl, CURLOPT_REFERER, $referer);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string);
    // 执行并输出到$data
    $data = curl_exec($curl);
    curl_close($curl);
    // 开始解析Cookies
    list($header, $body) = explode("\r\n\r\n", $data);
    preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
    $cookie = $matches[1];
    print_r($data);
    return $cookie;

}
function curlPostData($url,$post_data){
    $post_data = json_decode($post_data);
    $post_string = '';
    foreach ($post_data as $key => $value) {
        $post_string = $post_string.'&'.$key."=".$value;
    }
    $post_string = $post_string.'&SUBMIT=Send';
    $post_string = substr($post_string, 1); ;
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 设置方式为POST
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string);
    // 执行并输出到$data
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
function curlPostWithCookies($url,$post_data,$cookie_string){
    $post_data = json_decode($post_data);
    $post_string = '';
    foreach ($post_data as $key => $value) {
        $post_string = $post_string.'&'.$key."=".$value;
    }
    $post_string = $post_string.'&SUBMIT=Send';
    $post_string = substr($post_string, 1); ;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_COOKIE, $cookie_string);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_REFERER,'http://xxxxxx.com');//HTTP referer
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string); //使用上面获取的cookies
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
function curlWithCookies($url,$cookie_string){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //如果加上本句则只发送请求，不输出任何内容
    curl_setopt($curl, CURLOPT_COOKIE, $cookie_string);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
function cookies_can_login($cookie_string){
	$person_info = 'http://xxxxxx.com';
    $curl = curl_init($person_info);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //如果加上本句则只发送请求，不输出任何内容
    curl_setopt($curl, CURLOPT_COOKIE, $cookie_string);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $data = curl_exec($curl);
    curl_close($curl);
    $login_decide = substr($data, 0, 5);
    if($login_decide == '<span'){
    	return 0;
    } else {
    	return 1;
    }
}
function getHttpCode($url){
    $code='';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); //设置URL
    curl_setopt($curl, CURLOPT_HEADER, 1); //获取Header
    curl_setopt($curl,CURLOPT_NOBODY,true); //Body就不要了吧，我们只是需要Head
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //数据存到成字符串吧，别给我直接输出到屏幕了
    $data = curl_exec($curl); //开始执行啦～
    $code = curl_getinfo($curl,CURLINFO_HTTP_CODE); //我知道HTTPSTAT码哦～  
    return $code;
    curl_close($curl);
}
function curlGetWithReferer($url,$referer){
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_REFERER,$referer);
    //设置代理服务器
    // curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
    // curl_setopt($curl, CURLOPT_PROXY, "218.247.161.37"); //代理服务器地址
    // curl_setopt($curl, CURLOPT_PROXYPORT, 80); //代理服务器端口
    // curl_setopt($curl, CURLOPT_PROXYUSERPWD, "309:309"); //http代理认证帐号，username:password的格式
    // curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
    // 执行并输出到$data
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
function curlWithCookiesReferer($url,$cookie_string,$referer){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //如果加上本句则只发送请求，不输出任何内容
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_COOKIE, $cookie_string);
    curl_setopt($curl, CURLOPT_REFERER,$referer);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
function getRealName($person_info,$cookie_string){
    $curl_result = curlWithCookies($person_info,$cookie_string);
    $html = str_get_html($curl_result);
    $realname = $html->find('tr', 1)->children(3)->plaintext;
    return $realname;
}
function getSex($person_info,$cookie_string){
    $curl_result = curlWithCookies($person_info,$cookie_string);
    $html = str_get_html($curl_result);
    $sex = $html->find('tr', 3)->children(1)->plaintext;
    return $sex;
}
function getPerInfo($info_url,$cookie_string){
    $curl_result = curlWithCookies($info_url,$cookie_string);
    $html = str_get_html($curl_result);
    $person = array();
    $person["name"] = $html->find('tr tr', 1)->children(3)->plaintext;
    $person["gender"] = $html->find('tr tr', 2)->children(1)->plaintext;
    $person["department"] = $html->find('tr tr', 2)->children(3)->plaintext;
    $person["spcialty"] = $html->find('tr tr', 3)->children(3)->plaintext;
    $person["birthday"] = $html->find('tr tr', 4)->children(1)->plaintext;
    $person["class"] = $html->find('tr tr', 4)->children(3)->plaintext;
    if(trim($person['gender']) == '男') $person['gender'] = '0';
    else $person['gender'] = '1';
    $birthday = explode('-', $person['birthday']);
    $person['birth_year'] = $birthday[0];
    $person['birth_mon'] = $birthday[1];
    $person['birth_day'] = $birthday[2];
    return $person;
}
