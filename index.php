<?php
$echo_var = [
	'QUERY_STRING' => '查询字符串',
  	'REQUEST_METHOD' => '请求方法',
  	'CONTENT_TYPE' => '请求信息类型',
  	'CONTENT_LENGTH' => '请求信息长度',
  	'SERVER_PROTOCOL' => '协议名称/版本',
  	'HTTPS' => 'HTTPS访问',
  	'REMOTE_ADDR' => '用户的 IP 地址',
  	'REMOTE_PORT' => '用户端口号',
  	'REDIRECT_STATUS' => '请求状态码',
  	'HTTP_UPGRADE_INSECURE_REQUESTS' => '自动跳转https',
  	'HTTP_USER_AGENT' => '用户UA',
  	'HTTP_DNT' => '用户申请禁止追踪',
  	'HTTP_ACCEPT' => '请求头中 Accept',
  	'HTTP_ACCEPT_ENCODING' => '请求头中 Accept-Encoding',
  	'HTTP_ACCEPT_LANGUAGE' => '请求头中 Accept-Language',
  	'HTTP_ACCEPT_CHARSET' => '请求头中 Accept-Charset',
  	'HTTP_CONNECTION' => '请求头中 Connection',
  	'HTTP_IF_NONE_MATCH' => 'ETag 实体标签',
  	'HTTP_IF_MODIFIED_SINCE' => 'If-Modified-Since 标签',
  	'HTTP_COOKIE' => 'cookie',
  	'REQUEST_TIME_FLOAT' => '请求时间戳(浮点型)',
  	'REQUEST_TIME' => '请求时间戳',
  	'REQUEST_METHOD' => '请求方法',
];
if (!empty($_GET['suo']))
{
    $api = 'http://m.fh.ink/dwz.php?format=txt&longurl=https://pos.yun-ling.cn/';
    $result = file_get_contents($api);
//    if ($result['code'] == 1)
//    {
//        $ae_url = $result['ae_url'];
//    }
//    else
//    {
//        $ae_url = $result['msg'];
//    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <script type="text/javascript" src="https://apis.map.qq.com/tools/geolocation/min?key=CVIBZ-CG73X-5SO4D-7JA6M-XV757-5DFW3&referer=internal_comps"></script>
    <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=w77wG0QgFww02QSz21uByclrtCVbkDx3"></script>
    <script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.14&key=7e7bd13f31a12e078453b65517f4a7aa"></script> 
</head>
<body>
  <p>状态:<span id="status">waiting</span></p>
  <hr>
  <p id="qq">腾讯定位(GCJ-02)</p>
  <hr>
  <p id="baidu">百度定位</p>
  <hr>
  <p id="ip">ip定位</p>
  <hr>
  <p id="amap">高德定位(GCJ-02)</p>
  <?php
  if (!empty($_GET['suo']))
  echo '<hr>短网址：'.$result;
  ?>
  <hr>
  <?php
  foreach($_SERVER as $k => $v){
    if (array_key_exists($k,$echo_var))
  	echo '<span style="color:red">'.$echo_var[$k].'</span><span style="color:red">:</span> '.$v.'<br>';
  }
  //echo '<hr>';
  //var_dump($http_response_header);
  ?>
  <!--
  <iframe height="500px" weight="300px" src="https://map.qq.com/">
<p>Your browser does not support iframes.</p>
</iframe>
-->
<script type="text/JavaScript">
	var tencent=document.getElementById("qq");
 	var baidu=document.getElementById("baidu");
 	var ip=document.getElementById("ip");
	var statusText=document.getElementById("status");
	var amap=document.getElementById("amap");
  
	var QQgeolocation = new qq.maps.Geolocation();
  	var Bgeolocation = new BMap.Geolocation();
 
	var options = {failTipFlag:true};

	function getCurLocation() {
      	statusText.innerHTML = 'starting';
      
      	//腾讯定位
		QQgeolocation.getLocation(success, error, options);
      
      	//百度定位
      	// 开启SDK辅助定位
		Bgeolocation.enableSDKLocation();
      	Bgeolocation.getCurrentPosition(function(r){
        if(this.getStatus() == BMAP_STATUS_SUCCESS){
                baidu.innerHTML += '<br>纬度(百度坐标): ' + r.point.lat;
                baidu.innerHTML += '<br>经度(百度坐标): ' + r.point.lng;
          		//坐标转换
                  AMap.convertFrom([r.point.lng,r.point.lat], 'baidu', function (status, result) {
                  if (result.info === 'ok') {
                    var lnglats = result.locations[0];
                    baidu.innerHTML += '<br>纬度: ' + lnglats.getLat();
                	baidu.innerHTML += '<br>经度: ' + lnglats.getLng();
                  }
                  else
                  {
                  	baidu.innerHTML += '<br>纬度: 转换错误';
                	baidu.innerHTML += '<br>经度: 转换错误';
                  }
                });
                baidu.innerHTML += '<br>误差范围: ' + r.accuracy + '&nbsp;米';
            }
            else {
                baidu.innerHTML += 'failed'+this.getStatus();
            }        
        },null,{enableHighAccuracy:true});
      
      	//IP定位
        var myCity = new BMap.LocalCity();
      	myCity.get(IPgeolocation);
	}
	function success(position) {
		//console.log(JSON.stringify(position));
		//tencent.innerHTML += '<br>' + JSON.stringify(position);
		tencent.innerHTML += '<br>' + position.nation + 
		position.province + 
		position.city + 
		position.district + '&nbsp;' +
		position.addr;
		tencent.innerHTML += '<br>行政区ID: ' + position.adcode;
      	tencent.innerHTML += '<br>定位类别: ' + position.type;
		tencent.innerHTML += '<br>纬度(火星坐标-gcj02): ' + position.lat;
		tencent.innerHTML += '<br>经度(火星坐标-gcj02): ' + position.lng;
		tencent.innerHTML += '<br>误差范围: ' + position.accuracy + '&nbsp;米';
        statusText.innerHTML = 'complete';
	};

	function error() {
		tencent.innerHTML += '<br>获取精确定位信息失败<br>开始获取粗糙定位信息';
		QQgeolocation.getIpLocation(success,error2);
	};

	function error2() {
		tencent.innerHTML += '<br>获取粗糙定位信息失败';
	};
  
  	function IPgeolocation(r){
        ip.innerHTML += '<br>城市: ' + r.name;
        ip.innerHTML += '<br>纬度(百度坐标): ' + r.center.lat;
        ip.innerHTML += '<br>经度(百度坐标): ' + r.center.lng;
      	AMap.convertFrom([r.center.lng,r.center.lat], 'baidu', function (status, result) {
          if (result.info === 'ok') {
            var lnglats = result.locations[0];
            ip.innerHTML += '<br>纬度: ' + lnglats.getLat();
            ip.innerHTML += '<br>经度: ' + lnglats.getLng();
          }
          else
          {
            ip.innerHTML += '<br>纬度: 转换错误';
            ip.innerHTML += '<br>经度: 转换错误';
          }
        });
    }
  
  	//高德定位
  	AMap.plugin('AMap.Geolocation', function() {
      var geolocation = new AMap.Geolocation({enableHighAccuracy: true,noIpLocate:0,noGeoLocation:0,GeoLocationFirst:true,useNative:true,convert:true});
      geolocation.getCurrentPosition(function(status,result){
        if(status=='complete'){
          onComplete(result)
        }else{
          onError(result)
        }
      });
    });
    //解析定位结果
    function onComplete(data) {
        var str = [];
        str.push('<br>定位结果：' + data.position);
        str.push('定位类别：' + data.location_type);
        if(data.accuracy){
             str.push('精度：' + data.accuracy + ' 米');
        }//如为IP精确定位结果则没有精度信息
        str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));
        amap.innerHTML += str.join('<br>');
    }
    //解析定位错误信息
    function onError(data) {
        amap.innerHTML+='<br>定位失败'
        amap.innerHTML += '失败原因排查信息:'+data.message;
    }
  
  	//百度坐标转火星坐标，(经度，纬度)
  	function baiduPointToggPoint (lng,lat){
		var baidu = [116.3, 39.9];
        AMap.convertFrom(baidu, 'baidu', function (status, result) {
          if (result.info === 'ok') {
            var lnglats = result.locations; // Array.<LngLat>
            return lnglats;
          }
        });
    }
     
	
	window.onload = getCurLocation();
	statusText.innerHTML = 'loaded';
    
    
</script>
</body>
</html>
