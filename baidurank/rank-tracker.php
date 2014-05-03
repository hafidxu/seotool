<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>【查询结果】关键词排名查询工具 By: Hafidxu.com</title>
<style>
table{
border-spacing:0;
border-top:1px solid #ccc;
border-left:1px solid #ccc;
}
td,th{
border-bottom:1px solid #ccc;
border-right:1px solid #ccc;
padding:5px;
}
th{
background:#eee;
}
tr.table_header{
background:#eee;
font-weight:bold;
}
</style>
</head>
<body style="padding:50px 50px;">
<h3>查询结果</h3>
<table><tr class="table_header"><td>关键词</td><td>域名</td><td>排名</td><td>日期</td><td>网址</td></tr>
<?php 
ini_set('max_execution_time', '0');
echo str_repeat(' ',1024);
if($_SERVER['REQUEST_METHOD']=='POST'){
	$keywordslist=$_POST['keywordslist'];
	$keywordslist = str_replace("\r\n", ',', $keywordslist); //清除换行符
	$keywords=explode(",",$keywordslist);
	$domain=$_POST['domains'];
	foreach($keywords as $keyword){//关键词遍历
			$isranked=0;
			$baiduSerpUrl='http://www.baidu.com/s?wd='.urlencode($keyword).'&tn=98067068_ie_dg&rn=100';
			$html=file_get_contents($baiduSerpUrl);
			$html = str_replace("\r\n", '', $html); //清除换行符
			$html = str_replace("\n", '', $html); //清除换行符
			$html = str_replace("\t", '', $html); //清除制表符
			$isranked=0;
			if(preg_match_all('/<div class=\"result(?:|\-op) c-container\"(?:.*) id="([0-9]*)"(?:.*)\<h3 class=\"t\">(?:.*)href(?:=| = )"(.*)"(?:.*)class=\"(?:g|c-showurl)\">(.*)<\/span>(?:.*)(?:c\-tools|weibo_source|op_sp_realtime_preBox)/U',$html,$matches,PREG_SET_ORDER) ){}
			else{
			preg_match_all('/<div class=\"result(?:|\-op) c-container\"(?:.*) id="([0-9]*)"(?:.*)><h3 class=\"t\">(?:.*)href(?:=| = )"(.*)"(?:.*)class=\"g\">(.*)<\/span>(.*)<\/span> (?:.*)<div class=\"c-tools\"/U',$html,$matches,PREG_SET_ORDER);
			}
			if($matches){
				$total=count($matches);
				//var_dump($matches);
				print str_repeat(" ", 4096);
				ob_flush();
				flush();
				for($i=0;$i<$total;$i++){ //遍历网页搜索结果
						
						$partern="/".$domain."/";
						if(preg_match($partern,$matches[$i][3])){
							$rank=$matches[$i][1];
							$date=date('Y-m-d');
							$url=get_redirect_url($matches[$i][2]);				
							echo "<tr><td>$keyword</td><td>$domain</td><td>$rank</td><td>$date</td><td>$url</td></tr>";
							$isranked=1;
							break 1;
						}else{
							$isranked=0;
						}
		
				}
				if($isranked=0){
					echo "<tr><td>$keyword</td><td>$domain</td><td>1000</td><td>$date</td><td>N/A</td></tr>";
				}
			
			};				

			sleep(2);
		}
		echo "</table><br/><br/><br/>查询完毕！";
}
	function gb2utf($string){
		return iconv("gb2312","utf-8",$string);
	}
	function utf2gb($string){
		return iconv("utf-8","gb2312",$string);
	}
	


	function get_redirect_url($url){
    $redirect_url = null; 
    $url_parts = @parse_url($url);
    if (!$url_parts) return false;
    if (!isset($url_parts['host'])) return false; //can't process relative URLs
    if (!isset($url_parts['path'])) $url_parts['path'] = '/';

    $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
    if (!$sock) return false;

    $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n"; 
    $request .= 'Host: ' . $url_parts['host'] . "\r\n"; 
    $request .= "Connection: Close\r\n\r\n"; 
    fwrite($sock, $request);
    $response = '';
    while(!feof($sock)) $response .= fread($sock, 8192);
    fclose($sock);
	
    if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
        if ( substr($matches[1], 0, 1) == "/" )
            return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
        else
            return trim($matches[1]);

    } else {
        return $url;;
    }

}
function get_domain($url){
			$urlpra=parse_url($url);
			$domain=$urlpra['host'];
			return $domain;
}

  function parseHost($httpurl)  
  {  
      
    $httpurl = strtolower( trim($httpurl) );  
    if(empty($httpurl)) return ;  
    $regx1 = '/https?:\/\/(([^\/\?#&]+\.)?([^\/\?#&\.]+\.)(com\.cn|org\.cn|net\.cn|com\.jp|co\.jp|com\.kr|com\.tw)(\:[0-9]+)?)\/?/i';  
    $regx2 = '/https?:\/\/(([^\/\?#&]+\.)?([^\/\?#&\.]+\.)(cn|com|org|info|us|fr|de|tv|net|cc|biz|hk|jp|kr|name|me|tw|la|co)(\:[0-9]+)?)\/?/i';  
    $host = $tophost = '';  
    if(preg_match($regx1,$httpurl,$matches))  
    {  
      $host = $matches[1];  
    } elseif(preg_match($regx2, $httpurl, $matches)) {  
      $host = $matches[1];  
    }     
    if($matches)   
    {  
        $tophost = $matches[3].$matches[4];  
        $domainLevel = $matches[2] == 'www.' ? 1:(substr_count($matches[2],'.')+1);   
    } else {  
        $tophost = '';  
        $domainLevel = 0;  
    }  
    //print_r($matches);  
    return $tophost;  
  }  
?>


<div><a href="javascript:history.go(-1);" >返回</div>
</body>
</html>