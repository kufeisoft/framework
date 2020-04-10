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
// | 验证器助手
// +----------------------------------------------------------------------


namespace kufei\helper;

use kufei\Helper;
use think\Validate;

class ValidateHelper extends Helper{
  /**
   * 快捷输入并验证（ 支持 规则 # 别名 ）
   * @param array $rules 验证规则（ 验证信息数组 ）
   * @param string $postdata POST提交来的数据
   * @return array
   */
  public function init(array $rules, $type = ''){
    list($data, $rule, $info) = [[], [], []];
    foreach ($rules as $name => $message) {
      if (stripos($name, '#') !== false) {
        list($name, $alias) = explode('#', $name);
      }
      if (stripos($name, '.') === false) {
        if (is_numeric($name)) {
          $keys = $message;
          if (is_string($message) && stripos($message, '#') !== false) {
            list($name, $alias) = explode('#', $message);
            $keys = empty($alias) ? $name : $alias;
          }
          $data[$name] = input("{$type}{$keys}");
        } else {
          $data[$name] = $message;
        }
      } else {
        list($_rgx) = explode(':', $name);
        list($_key, $_rule) = explode('.', $name);
        $keys = empty($alias) ? $_key : $alias;
        $info[$_rgx] = $message;
        $data[$_key] = input("{$type}{$keys}");
        $rule[$_key] = empty($rule[$_key]) ? $_rule : "{$rule[$_key]}|{$_rule}";
      }
    }
    $validate = new Validate();
    if ($validate->rule($rule)->message($info)->check($data)) {
      return $data;
    } else {
      $this->controller->error($validate->getError());
    }
  }
}