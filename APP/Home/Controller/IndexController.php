<?php
/**
 * @Author: LuckPHP
 * @Explain: Explain
 */
use Luck\Luck\Controller;
class IndexController extends Controller
{
	public function index()
   {
		echo '<title>欢迎使用 LuckPHP</title><style>*{padding: 0; margin: 0; }body{ background: #fff; font-family: "微软雅黑"; color: #333; font-size: 16px; }.system-message{ padding: 24px 48px; }.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }.system-message .success{padding-top: 10px; font-size: 16px; color: green}.system-message .jump{padding-top: 10px;font-size: 12px;}.system-message .jump a{color: #333;}</style><div class="system-message"><h1>:)</h1><p class="success">欢迎使用 LuckPHP</p></div>';
	}

}

?>
