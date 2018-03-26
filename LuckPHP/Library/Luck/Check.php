<?php
/**
 * 验证类基类
 */
namespace Luck\Luck;
use Luck\Luck\Tpl;
class Check
{
	protected $tpl = null;

	public function __construct()
	{
		$this->tpl = Tpl::getInstance();
	}

	/**
	 * 重写success
	 */
	protected function success($info = '' , $url = '')
	{
		$this->tpl->success($info,$url);
	}

	/**
	 * 重写error
	 */
	protected function error($_info = '' , $_url = '')
	{
		$this->tpl->error($_info,$_url);
	}

	/**
	 * 重写ajaxReturn
     */
    protected function ajaxReturn($status, $data, $type = '')
    {
        $this->tpl->ajaxReturn($status, $data, $type);
    }

}
