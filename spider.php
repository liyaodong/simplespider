<?php
header("Content-type: text/html; charset=utf8");

if( ! defined('BASEPATH') ) {
    define ('BASEPATH', $_SERVER['DOCUMENT_ROOT'].'/spider/');  // 此处需要设置你的目录
}

// 首先导入curl类和simple_html_dom类

require_once('curl_class.php');
require_once('simple_html_dom.php');

// 数据库配置信息
$db_host = '';
//数据库用户名
$db_user = '';
//数据库密码
$db_pwd = '';
//数据库名
$db_name = '';
//数据表编码
$db_charset = 'utf8';


$coon = mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name, $coon);
mysql_set_charset('utf8', $coon);
// 加密访问
$key = 'yourtoken';
if(isset($_GET['key'])){
  if($_GET['key'] == $key) {
  	//API：列表页面
		$curlpage = '1';   // 第几页列表
		$list_url = '列表页面';
		$list_array = getList($list_url,$curlpage);
		$post_array = getPostFromListArr($list_array);
		dealPost($post_array);
  }
}



function dealPost($post_array){
	//传入一个包含标题、时间、内容的文章输入，对其进行处理
	$imported = 0;
	$sql_list = '';
	$sql = "SELECT `orderid` FROM `pmw_infolist` ORDER BY `orderid` desc LIMIT 1";  // 获取到已有排序的最高值
	$result = mysql_query($sql);
	$order = mysql_fetch_array($result);
	$order = $order[0];
	$fp = fopen("log.txt","a");
	foreach ($post_array as $post) {
		// 对时间进行修正
		$post_time = strtotime($post['time']) + rand(7200,30000);
		// 格式化内容
		$post_title = trim($post['title']);
		$post_content = trim($post['content']);
		$post_content = str_replace("'Times New Roman'",' ',$post_content);
		// 随机生成点击数
		$post_hits = rand(80,200);

		$orderid = $order + 1;
		// 构造SQL
		$ifExistSQL = "SELECT * FROM `pmw_infolist` WHERE `title`= '$post_title'";
		$result = mysql_query($ifExistSQL);
		if(mysql_numrows($result) === 0){
			$sql = "INSERT INTO `pmw_infolist` (`id`, `siteid`, `classid`, `parentid`, `parentstr`, `mainid`, `mainpid`, `mainpstr`, `title`, `colorval`, `boldval`, `flag`, `source`, `author`, `linkurl`, `keywords`, `description`, `content`, `picurl`, `picarr`, `hits`, `orderid`, `posttime`, `checkinfo`, `delstate`, `deltime`) VALUES (NULL, '1', '26', '0', '0,', '-1', '-1', '', '".$post['title']."', '', '', '', '', 'admin', '', '', '', '".$post_content."', '', '', '".$post_hits."', '".$orderid."', '".$post_time."', 'true', '', '0');";
			mysql_query($sql);
			fwrite($fp,date('Y-m-d H:i:s',time())."已导入 ".$post_title. PHP_EOL);
			$order ++;
			$imported ++;
		}
	}
	fwrite($fp,date('Y-m-d',time())." 导入完毕" . PHP_EOL);
	fclose($fp);
	echo $imported;
}


function getPostFromListArr($list_array){
	// 传入一个包含文章标题、时间和链接地址的数组
	// 返回包含文章标题、时间、和内容的数组
	$post_array = array();
	foreach ($list_array as $post_item) {
		$post = array(
			'title' => $post_item['title'],
			'time' => $post_item['time'],
			'content' => getPost($post_item['href'])
		);
		array_push($post_array, $post);
	}
	return $post_array;
}


function getPost($url){
	// 传入某篇文章的URL
	// 返回文章正文内容

	$post_dom = curlGetHTML($url);
	$post_article = explode('<tr><td></td></tr>', $post_dom);
	$post_article = explode('</td></tr>',$post_article[1]);
	return iconv("GBK","UTF-8",$post_article[0]);
}
function getList($list_url,$page = '1'){
	// 传入一个列表页面
	// 返回所有文章的标题、时间和链接地址

	$spider_domain = '你要抓取的网址前缀（方便后边拼出相对链接地址）';
	//此函数用于解析url中的列表
	// CountNo和PAGE用于调整每页显示文章篇数，建议20
  // 根据自身情况调整post参数等
	$post_data = array(
		'a2289CURURI' => '/class/xb_dongtai.jsp',
		'a2289KEYTYPES' => '4,12,12,93,4,12,4,4,12,4,4,12,12',
		'a2289actiontype' => '',
		'a2289ORDER' => 'DESC',
		'a2289ORDERKEY' => 'wbdate',
		'a2289NOWPAGE' => $page,
		'a2289Find.x' => '22',
		'a2289Find.y' => '8',
		'a2289CountNo' => '20',
		'a2289PAGE' => '20',
		'a2289rowCount' => '236',
		'a2289FIRSTID' => '-1',
		'a2289LASTID' => '-1'
	);
	$list_dom = str_get_html(curlPostData($list_url,json_encode($post_data)));
	$all_post = $list_dom->find('table.winstyle456845412_2289');
	$list_array = array();
	for ($i = 1; $i < count($all_post); $i++) {
		$post_title = $all_post[$i]->find('a', 0)->plaintext;
		$post_href = $all_post[$i]->find('a', 0)->href;
		$post_href = $spider_domain.substr($post_href, 2);
		$post_time = $all_post[$i]->find('span.timestyle456845412_2289', 0)->plaintext;
		$array = array(
			'title' => $post_title,
			'href' => $post_href,
			'time' => $post_time
		);
		array_push($list_array, $array);
	}
	// 输出时倒序，保证最后边的order最高
	return array_reverse($list_array);
}
?>
