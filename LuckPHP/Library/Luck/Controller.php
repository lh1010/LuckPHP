<?php
/**
 * @Author: LuckPHP
 * @Explain: Controller
 */
namespace Luck\Luck;
use Luck\Luck\Tpl;
abstract class Controller
{
	protected $tpl = null;

	public function __construct()
	{
		$this->tpl = Tpl::getInstance();
	}

	/**
	 * Rewrite display
	 */
	protected function display($tplFile = '')
	{
		empty($tplFile) ? $this->tpl->display(ucfirst(__A__).'/'.__M__.TPL_SUFFIX) : $this->tpl->display(__A__.'/'.$tplFile);
	}

	/**
	 * Rewrite assign
	 */
	protected function assign($key,$value)
	{
		$this->tpl->assign($key,$value);
	}

	/**
	 * Rewrite success
	 */
	protected function success($info = '' , $url = '')
	{
		$this->tpl->success($info,$url);
	}

	/**
	 * Rewrite error
	 */
	protected function error($_info = '' , $_url = '')
	{
		$this->tpl->error($_info,$_url);
	}

	/**
	 * Rewrite ajaxReturn
     */
    protected function ajaxReturn($status, $data, $type = '')
    {
        $this->tpl->ajaxReturn($status, $data, $type);
    }

}
