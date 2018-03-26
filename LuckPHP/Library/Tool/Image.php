<?php
/**
 * 图像处理类
 * @author LuckPHP
 *
 */
namespace Luck\Tool;
class Image {
	private $file;			//图片物理地址
	private $file_return;	//需返回图像地址
	private $width;			//图片长度
	private $height;		//图片长度
	private $type;			//图片类型
	private $img;			//原图的资源句柄
	private $new;			//新图的资源句柄
	private $error;			//返回错误信息
	private $copy;			//是否保留旧图像
	
	//构造方法，初始化
	public function __construct($_file, $_copy = '') {
		$this->file_return = $_file;
		$this->file = $_SERVER["DOCUMENT_ROOT"].$_file;
		if(!file_exists($this->file)) {
			$this->error = array("status"=>1.1, "info"=>$this->file." 文件不存在！");
			return false;
		}
		$this->copy = $_copy;
		if(!empty($this->copy)) {
			$fileArr = pathinfo($_file);
			$new_img = '';
			$new_img .= $_SERVER["DOCUMENT_ROOT"].$fileArr['dirname'];
			$basenameArr = explode('.', $fileArr['basename']);
			$new_img .= '/'.$basenameArr[0].$this->copy.'.'.$basenameArr[1];
			copy($this->file, $new_img);
			$this->file = $new_img;
			$this->file_return = $fileArr['dirname'].'/'.$basenameArr[0].$this->copy.'.'.$basenameArr[1];
		}

		list($this->width, $this->height, $this->type) = getimagesize($this->file);
		$this->img = $this->getFromImg($this->file, $this->type);
	}

	/**
     * 缩略图 (缩放图片大小)  
     * @param $new_width  新图片宽度 默认100px
     * @param $new_height 新图片高度 默认100px
     * @return success return array("status"=>1, "info"=>"success", "path"=>newImgPath);
     */
	public function thumb($new_width = 100, $new_height = 100) {
		if(!file_exists($this->file)) {
			$this->error = array("status"=>1.1, "info"=>$this->file." 文件不存在！");
			return false;
		}
		$this->new = imagecreatetruecolor($new_width, $new_height);
		//如果载入图片太小，切换为白色背景
		$color = imagecolorallocate($this->new, 255, 255, 255);
		imagefill($this->new, 0, 0, $color) ; 
		imagecopyresampled($this->new, $this->img, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
		$this->out();
		$this->file = str_replace($_SERVER["DOCUMENT_ROOT"], '', $this->file);
		return array("status"=>1, "info"=>"success", "path"=>$this->file_return);
	}

	/**
     * 缩略图 (比例缩放图片大小)[固定了大小，不失真，不变形]
     * @param $new_width  新图片宽度 如为空 则为原图像宽度  
     * @param $new_height 新图片高度 如为空 则为原图像高度  
     * @return success return array("status"=>1, "info"=>"success", "path"=>newImgPath);
     */
	public function thumb_1($new_width = 0, $new_height = 0) {
		if(!file_exists($this->file)) {
			$this->error = array("status"=>1.1, "info"=>$this->file." 文件不存在！");
			return false;
		}
		if (empty($new_width) && empty($new_height)) {
			$new_width = $this->width;
			$new_height = $this->height;
		}
		if (!is_numeric($new_width) || !is_numeric($new_height)) {
			$new_width = $this->width;
			$new_height = $this->height;
		}
		//创建容器
		$_n_w = $new_width;
		$_n_h = $new_height;
		
		//创建裁剪点
		$_cut_width = 0;
		$_cut_height = 0;
		
		if ($this->width < $this->height) {
			$new_width = ($new_height / $this->height) * $this->width;
		} else {
			$new_height = ($new_width / $this->width) * $this->height;
		}
		
		if ($new_width < $_n_w) { //如果新宽度小于新容器宽度
			$r = $_n_w / $new_width; //按长度求出等比例因子
			$new_width *= $r; //扩展填充后的长度
			$new_height *= $r; //扩展填充后的高度
			$_cut_height = ($new_height - $_n_h) / 2; //求出裁剪点的高度
		}
		
		if ($new_height < $_n_h) { //如果新高度小于容器高度
			$r = $_n_h / $new_height; //按高度求出等比例因子
			$new_width *= $r; //扩展填充后的长度
			$new_height *= $r; //扩展填充后的高度
			$_cut_width = ($new_width - $_n_w) / 2; //求出裁剪点的长度
		}
		$this->new = imagecreatetruecolor($_n_w, $_n_h);
		//如果载入图片太小，切换为白色背景
		$color = imagecolorallocate($this->new, 255, 255, 255);
		imagefill($this->new, 0, 0, $color) ; 
		imagecopyresampled($this->new, $this->img, 0, 0, $_cut_width, $_cut_height, $new_width, $new_height, $this->width, $this->height);
		$this->out();
		$this->file = str_replace($_SERVER["DOCUMENT_ROOT"], '', $this->file);
		return array("status"=>1, "info"=>"success", "path"=>$this->file_return);
	}

	/**
     * 缩略图 (物理裁剪)
     * @param $x 客户端选择区域左上角x轴坐标  
     * @param $y 客户端选择区域左上角y轴坐标  
     * @param $w 客户端选择区的宽   
     * @param $h 客户端选择区的高     
     * @param $new_width  新图片宽度 默认为空
     * @param $new_height 新图片高度 默认为空
     * @return success return array("status"=>1, "info"=>"success", "path"=>newImgPath);
     */
	public function thumb_2($x = 0, $y = 0, $w = 100, $h = 100, $new_width = 0, $new_height = 0) {
		if(!file_exists($this->file)) {
			$this->error = array("status"=>1.1, "info"=>$this->file." 文件不存在！");
			return false;
		}
		if(empty($new_width) && empty($new_height)) {
			$new_width = $w - $x;  //The new image is width
			$new_height = $h - $y; //The new image is height
		}
		$this->new = imagecreatetruecolor($new_width, $new_height);
		//如果载入图片太小，切换为白色背景
		$color = imagecolorallocate($this->new, 255, 255, 255);
		imagefill($this->new, 0, 0, $color) ; 
		imagecopyresampled($this->new, $this->img, 0, 0, $x, $y, $new_width, $new_height, $w, $h);
		$this->out();
		$this->file = str_replace($_SERVER["DOCUMENT_ROOT"], '', $this->file);
		return array("status"=>1, "info"=>"success", "path"=>$this->file_return);
	}

	/**
     * 图像设置文字水印
     * @param $txt 文字水印 默认为LuckPHP 
     * @param $font_position 文字水印位置 默认为4 1=左上方 2=右上方 3=左下方 4=右下方 5=中间位置 
     * @param $font_color 文字水印颜色 默认1 1=black 2=white 3=red 4=yellow 5=blue 
     * @param $fontsize 文字水印大小 默认为12  
     * @return success return array("status"=>1, "info"=>"success", "path"=>newImgPath);
     */
    public function watermark_font($txt = '', $font_position = '', $font_color = '', $font_size = '') {
    	if(!file_exists($this->file)) {
			$this->error = array("status"=>1.1, "info"=>$this->file." 文件不存在！");
			return false;
		}

		//=== 初始化水印文字 ===
		$txt = $txt;
		empty($txt) ? $txt = 'LuckPHP' : $txt = $txt;

		//=== 初始化水印文字大小 ===
		$font_size = $font_size;
		empty($font_size) ? $font_size = 12 : $font_size = $font_size;

		$this->new = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($this->new, $this->img, 0, 0, 0, 0,$this->width, $this->height, $this->width, $this->height);
		$font = 'simkai.ttf';

		//=== 初始化水印文字颜色 ===
		$font_color = $font_color;
		if(!empty($font_color)) {
			switch ($font_color) {
				case '1': //black
					$color = imagecolorallocate($this->new, 0, 0, 0);
					break;
				case '2': //white
					$color = imagecolorallocate($this->new, 255, 255, 255);
					break;
				case '3': //red
					$color = imagecolorallocate($this->new, 255, 0, 0);
					break;
				case '4': //yellow
					$color = imagecolorallocate($this->new, 255, 255, 0);
					break;
				case '5': //blue
					$color = imagecolorallocate($this->new, 0, 0, 255);
					break;			
			}
		} else {
			$color = imagecolorallocate($this->new, 0, 0, 0); //block
		}
		
		//=== 初始化水印文字位置 ===
		$font_box = imagettfbbox($font_size, 0, $font, $txt);
		$font_width = abs($font_box[2] - $font_box[0]);
		$font_height = abs($font_box[5] - $font_box[3]);
		$font_position = $font_position;
		if(!empty($font_position)) {
			switch ($font_position) {
				case '1': //left top
					$position_x = 0;
					$position_y = $font_height;
					break;
				case '2': //right top
					$position_x = $this->width - $font_width;
					$position_y = $font_height;
					break;
				case '3': //left bottom
					$position_x = 0;
					$position_y = $this->height;
					break;
				case '4': //right bottom
					$position_x = $this->width - $font_width;
					$position_y = $this->height;
					break;
				case '5': //center
					$position_x = $this->width / 2 - $font_width / 2;
					$position_y = $this->height / 2;
					break;			
			}
		} else { //right bottom
			$position_x = $this->width - $font_width;
			$position_y = $this->height;
		}

		imagettftext($this->new, $font_size, 0, $position_x, $position_y, $color, $font, $txt);
		$this->out();
		return array("status"=>1, "info"=>"success", "path"=>$this->file_return);
    }	
	
	/**
     * 图像设置图片水印
     * @param $watermark_img 图片水印URL 为空为系统图片 
     * @param $img_position 图片水印位置 默认为4 1=左上方 2=右上方 3=左下方 4=右下方 5=中间位置 
     * @param $parency 图片水印透明度 默认为50 1至100  
     * @return success return array("status"=>1, "info"=>"success", "path"=>newImgPath);
     */
    public function watermark_img($watermark_img = '', $img_position = '', $parency = '') {
    	if(!file_exists($this->file)) {
			$this->error = array("status"=>1.1, "info"=>$this->file." 文件不存在！");
			return false;
		}

		$this->new = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($this->new, $this->img, 0, 0, 0, 0,$this->width, $this->height, $this->width, $this->height);

		//=== 初始化水印图片 ===
		!empty($watermark_img) ? $watermark_img = $watermark_img : $watermark_img = 'http://wenda.luckphp.lh1010.com/uploads/avatar/000/00/00/01_avatar_mid.jpg';

		!empty($watermark_img) ? $watermark_img = $watermark_img : $watermark_img = $i;

		$src = imagecreatefromstring(file_get_contents($watermark_img));
		list($src_w, $src_h) = getimagesize($watermark_img);
		
		//=== 初始化水印图片位置 ===
		$img_position = $img_position;
		if(!empty($img_position)) {
			switch ($img_position) {
				case '1': //left top
					$position_x = 0;
					$position_y = 0;
					break;
				case '2': //right top
					$position_x = $this->width - $src_w;
					$position_y = 0;
					break;
				case '3': //left bottom
					$position_x = 0;
					$position_y = $this->height - $src_h;
					break;
				case '4': //right bottom
					$position_x = $this->width - $src_w;
					$position_y = $this->height - $src_h;
					break;
				case '5': //center
					$position_x = $this->width / 2 - $src_w / 2;
					$position_y = $this->height / 2 - $src_h / 2;
					break;			
			}
		} else { //right bottom
			$position_x = $this->width - $src_w;
			$position_y = $this->height - $src_h;
		}

		//=== 初始化水印图片透明度 ===
		!empty($parency) ? $parency = $parency : $parency = 50;

		imagecopymerge($this->new, $src, $position_x, $position_y, 0, 0, $src_w, $src_h, $parency);

		$this->out();
		return array("status"=>1, "info"=>"success", "path"=>$this->file_return);
	}

	//加载图片，各种类型，返回图片的资源句柄
	private function getFromImg($_file, $_type) {
		switch ($_type) {
			case 1 : //gif
				$img = imagecreatefromgif($_file);
				break;
			case 2 : //jpg
				$img = imagecreatefromjpeg($_file);
				break;
			case 3 : //png
				$img = imagecreatefrompng($_file);
				break;
		}
		return $img;
	}
	
	//图像输出
	private function out() {
		imagepng($this->new,$this->file);
		imagedestroy($this->img);
		imagedestroy($this->new);
	}
	
	//返回最后一次上传错误信息
    public function getError(){
        return $this->error;
    }	

	
	
}
?>