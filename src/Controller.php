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
// | 标准控制器基类
// +----------------------------------------------------------------------

namespace kufei;

use think\App;
use think\Request;
use think\Response;
use think\response\Redirect;
use think\exception\HttpResponseException;

abstract class Controller
{
  /** 当前应用 */
  public $app;
   
  /** 请求对象 */
  public $request;

  /**
   * 构造方法
   * @access public
   * @param  App  $app  应用对象
   */
  public function __construct(App $app){
    $this->app     = $app;
    $this->request = $this->app->request;

    // 控制器初始化
    $this->initialize();
  }

  /**
   * 控制器初始化
   */
  protected function initialize() {
    
  }

  /**
   * 返回失败的操作
   * @param mixed $info 消息内容
   * @param mixed $data 返回数据
   * @param integer $code 返回代码
   */
  protected function error($msg = '', $url = null, $data = '', $wait = 3, array $header = []){
    $result = ['code' => 0, 'msg'  => $msg, 'data' => $data, 'url'  => $url, 'wait' => $wait ];
    $type = $this->app->isAjax() ? 'json' : 'html';
    if($type == 'json') $msg = json_encode($result, JSON_UNESCAPED_UNICODE);
    $response = Response::create($result, $type)->header($header)->content($msg)->options(['jump_template' => config('app.exception_tmpl')])->code(500);
    throw new HttpResponseException($response);
  }

  /**
   * 操作成功跳转的快捷方法
   * @access protected
   * @param  mixed     $msg 提示信息
   * @param  string    $url 跳转的URL地址
   * @param  mixed     $data 返回的数据
   * @param  integer   $wait 跳转等待时间
   * @param  array     $header 发送的Header信息
   * @return void
  */
  protected function success($msg = '', $url = null, $data = '', $wait = 3, array $header = []){
    if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) $url = $_SERVER["HTTP_REFERER"];
    $result = ['code' => 1, 'msg'  => $msg, 'data' => $data, 'url'  => $url, 'wait' => $wait ];
    $type = $this->app->isAjax() ? 'json' : 'html';
    if($type == 'json') $msg = json_encode($result, JSON_UNESCAPED_UNICODE);
    $response = Response::create($result, $type)->header($header)->content($msg)->options(['jump_template' => config('app.exception_tmpl')])->code(200);
    throw new HttpResponseException($response);
  }

  /**
   * URL重定向
   * @access protected
   * @param  string         $url 跳转的URL表达式
   * @param  array|integer  $params 其它URL参数
   * @param  integer        $code http code
   * @param  array          $with 隐式传参
   * @return void
  */
  protected function redirect($url, $params = [], $code = 302, $with = []){
    $response = new Redirect($url);
    if (is_integer($params)) {
      $code   = $params;
      $params = [];
    }
    $response->code($code)->params($params)->with($with);
    throw new HttpResponseException($response);
  }

  /**
   * 加载模板输出
   * @access protected
   * @param  string $template 模板文件名
   * @param  array  $vars     模板输出变量
   * @param  array  $config   模板参数
   * @return mixed
  */
  protected function fetch(...$args){
    return \think\facade\View::fetch(...$args);
  }

  /**
   * 模板变量赋值
   * @access protected
   * @param  mixed $name  要显示的模板变量
   * @param  mixed $value 变量的值
   * @return $this
  */
  protected function assign(...$args){
    return \think\facade\View::assign(...$args);
  }

  /**
   * 快捷表单逻辑器
   * @param string|Query $dbQuery
   * @param string $template 模板名称
   * @param string $field 指定数据对象主键
   * @param array $where 额外更新条件
   * @param array $data 表单扩展数据
   * @return array|boolean
   * @throws DataNotFoundException
   * @throws DbException
   * @throws ModelNotFoundException
   */
  protected function _form($dbQuery, $template = '', $field = '', $where = [], $data = []){
    return FormHelper::instance()->init($dbQuery, $template, $field, $where, $data);
  }

  /**
   * 快捷输入并验证（ 支持 规则 # 别名 ）
   * @param array $rules 验证规则（ 验证信息数组 ）
   * @param string $type 输入方式 ( post. 或 get. )
   * @return array
   */
  protected function _vali(array $rules, $type = ''){
    return ValidateHelper::instance()->init($rules, $type);
  }

}