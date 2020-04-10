<?php
// +----------------------------------------------------------------------
// | Kufeisoft framework
// +----------------------------------------------------------------------
// | 版权所有 2020 西安酷飞软件有限公司
// +----------------------------------------------------------------------
// | 官方网站: https://framework.kufeisoft.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | 公共方法
// +----------------------------------------------------------------------

declare(strict_types=1);

if (!function_exists('safe_replace')) {
  /**
   * 危险字符过滤
   * 
   * @param string $con要过滤的内容
   * @return mixed
   */
  function safe_replace(string $content):string {
    return str_replace(
      ['\\', ';', '\'', '%2527', '%27', '%20', '&', '"', '<', '>'], 
      ['', '', '', '', '', '', '&amp;', '&quot;', '&lt;', '&gt;'], 
      $content
    );
  }	
}
if (!function_exists('safe_replace_array')) {
  /**
   * 数组的危险字符过滤
   * 
   * @param array $content 要过滤的内容
   * @param array $filter 要排除的字段
   * @return array
   */
  function safe_replace_array(array $array, array $filter=[]):array {
    foreach($array as $key => $value){
      if(in_array($key, $filter)) continue;
      $array[$key] = safe_replace($value);
    }
    return $array;
  }	
}

if (!function_exists('authcode')) {
  /**
   * @param string $string 原文或者密文
   * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
   * @param string $key 密钥
   * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
   * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
   */
  function authcode(string $string, string $operation = 'DECODE', string $key = '', Int $expiry = 0):string {
    $ckey_length = 4;
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = [];
    for ($i = 0; $i <= 255; $i++)  $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    for ($j = $i = 0; $i < 256; $i++) {
      $j = ($j + $box[$i] + $rndkey[$i]) % 256;
      $tmp = $box[$i];
      $box[$i] = $box[$j];
      $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
      $a = ($a + 1) % 256;
      $j = ($j + $box[$a]) % 256;
      $tmp = $box[$a];
      $box[$a] = $box[$j];
      $box[$j] = $tmp;
      $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE'){
      if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
        return substr($result, 26);
      }
      return '';
    }
    return $keyc . str_replace('=', '', base64_encode($result));
  }
}
if (!function_exists('enbase64url')) {
  /**
   * Base64安全URL编码
   * @param string $string
   * @return string
   */
  function enbase64url(string $string):string {
    return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
  }
}
if (!function_exists('debase64url')) {
  /**
   * Base64安全URL解码
   * @param string $string
   * @return string
   */
  function debase64url(string $string):string {
    return base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
  }
}
if (!function_exists('down_file')) {
  /**
   * 下载远程文件到本地
   * @param string $source 远程文件地址
   * @param boolean $force 是否强制重新下载
   * @param integer $expire 强制本地存储时间
   * @return string
   */
  function down_file($source, $force = false, $expire = 0):string {
    // $result = Storage::down($source, $force, $expire);
    // return isset($result['url']) ? $result['url'] : $source;
    return '';
  }
}
if (!function_exists('format_bytes')) {
  /**
   * 文件字节单位转换
   * @param integer $size
   * @return string
   */
  function format_bytes(int $size, int $dec=2, string $symbol=' '): string{
    $units = ['B', 'K', 'M', 'G', 'T'];
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, $dec) . $symbol . $units[$i];
  }
}
