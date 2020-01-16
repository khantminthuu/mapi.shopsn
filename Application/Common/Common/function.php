<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王波 <opjklu@126.com>
// +----------------------------------------------------------------------
/**
 * 根据经纬度获取用户详细地址
 */
	function use_LL_getAddress($lat,$lng){
		$AK = C("Ak");
		$url = "http://api.map.baidu.com/geocoder/v2/?location=$lat,$lng&output=json&pois=1&latest_admin=1&ak=$AK";
		$content = get_remote_file($url);
		$json = json_decode($content);
		if(count($json->{'result'}) == 0){
			return  '';
		}
		$addressData['province'] = $json->{'result'}->{'addressComponent'}->{'province'};
		$addressData['city'] = $json->{'result'}->{'addressComponent'}->{'city'};
		$addressData['district'] = $json->{'result'}->{'addressComponent'}->{'district'};
		$addressData['street'] = $json->{'result'}->{'addressComponent'}->{'street'}; //路
		$addressData['street_number'] = $json->{'result'}->{'addressComponent'}->{'street_number'};//号
		return  $addressData;
	}
//根据地址获取经纬度
    function getLatByAddress($address){
        $AK = C("Ak");
        $url='http://api.map.baidu.com/geocoder/v2/?address='.$address.'&output=json&ak='.$AK;
        $rs = file_get_contents($url);
        $json_data = json_decode($rs);
        $lng = $json_data->result->location->lng;
        $lat = $json_data->result->location->lat;
        $date['lat'] =$lat;
        $date['log'] =$lng;
		return  $date;
    }
    function getdistance($lng1, $lat1, $lng2, $lat2) {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }
/**
 * 封装file_get_contents()，设置超时时间
 *
 * @param string $url
 * @param int $timeout
 * @return string|void
 */
function get_remote_file($url = '', $timeout = 5)
{
    if (empty($url))
        return;

    // 解析协议
    $protocol = parse_url($url)['scheme'];
    $options = [
        'http' => [
            'method'  => 'GET',
            'timeout' => $timeout,
        ],
        'https' => [
            'method'  => 'POST',
            'timeout' => $timeout,
        ]
    ];
    // 必须是二维数组
    $option[$protocol] = $options[$protocol];
    $result  = file_get_contents($url, false, stream_context_create($option));
    return $result;
}

?>
