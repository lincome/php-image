<?php
/**
 * 拼接多幅图片成为一张图片
 *
 * 参数说明：原图片为文件路径数组，目的图片如果留空，则不保存结果
 *
 * 例子：
 * <code>
 * $ci = new CombineImage(array("D:/Downloads/1.jpg", "D:/Downloads/2.png"), "D:/Downloads/3.png");
 * $ci->combine();
 * $ci->show();
 * </code>
 *
 * @author 张荣杰
 * @version 2012.8.9
 */
class CombineImage {
	/**
	 * 原图地址数组
	 */
	private $srcImages;
	/**
	 * 每张图片缩放到这个宽度
	 */
	private $width;
	/**
	 * 每张图片缩放到这个高度
	 */
	private $height;
	/**
	 * 拼接模式，可以选择水平或垂直
	 */
	private $mode;
	/**
	 * 水平拼接模式常量
	 */
	const COMBINE_MODE_HORIZONTAL = "horizontal";
	/**
	 * 垂直拼接模式常量
	 */
	const COMBINE_MODE_VERTICAL = "vertical";
	/**
	 * 目标图片地址
	 */
	private $destImage;

	/**
	 * 临时画布
	 */
	private $canvas;

	/**
	 * 构造函数，传入原图地址数组和目标图片地址
	 */
	public function __construct($srcImages = '', $desImage = '',
				$width = 534, $height = 318,
				$mode = self::COMBINE_MODE_VERTICAL) {
		$this->srcImages = $srcImages;
		$this->destImage = $desImage;
		$this->width = $width;
		$this->height = $height;
		$this->mode = $mode;
		$this->canvas = NULL;
	}

	public function __destruct() {
		if ($this->canvas != NULL) {
			imagedestroy($this->canvas);
		}
	}

	/**
	 * 合并图片
	 */
	public function combine() {
		if (empty($this->srcImages)	|| $this->width==0 || $this->height==0) {
			return;
		}
		$this->createCanvas();
		for($i=0; $i<count($this->srcImages); $i++) {
			$srcImage = $this->srcImages[$i];
			$srcImageInfo = getimagesize($srcImage);
			// 如果能够正确的获取原图的基本信息
			if ($srcImageInfo) {
				$srcWidth = $srcImageInfo[0];
				$srcHeight = $srcImageInfo[1];
				$fileType = $srcImageInfo[2];
				if ($fileType == 2) {
					// 原图是 jpg 类型
					$srcImage = imagecreatefromjpeg($srcImage);
				} else if ($fileType == 3) {
					// 原图是 png 类型
					$srcImage = imagecreatefrompng($srcImage);
				} else {
					// 无法识别的类型
					continue;
				}

				// 计算当前原图片应该位于画布的哪个位置
				if ($this->mode == self::COMBINE_MODE_HORIZONTAL) {
					$destX = $i * $this->width;
					$desyY = 0;
				} elseif ($this->mode == self::COMBINE_MODE_VERTICAL) {
					$destX = 0;
					$desyY = $i * $this->height;
				}

				imagecopyresampled($this->canvas, $srcImage, $destX, $desyY,
							0, 0, $this->width, $this->height, $srcWidth, $srcHeight);
			}
		}

		// 如果有指定目标地址，则输出到文件
		if ( ! empty($this->destImage)) {
			$this->output();
		}
	}

	/**
	 * 输出结果到浏览器
	 */
	public function show() {
		if ($this->canvas == NULL) {
			return;
		}
		header("Content-type: image/jpeg");
		imagejpeg($this->canvas);
	}

	/**
	 * 私有函数，创建画布
	 */
	private function createCanvas() {
		$totalImage = count($this->srcImages);
		if ($this->mode == self::COMBINE_MODE_HORIZONTAL) {
			$width = $totalImage * $this->width;
			$height = $this->height;
		} else if ($this->mode == self::COMBINE_MODE_VERTICAL) {
			$width = $this->width;
			$height = $totalImage * $this->height;
		}
		$this->canvas = imagecreatetruecolor($width, $height);

		// 使画布透明
		$white = imagecolorallocate($this->canvas, 255, 255, 255);
		imagefill($this->canvas, 0, 0, $white);
		imagecolortransparent($this->canvas, $white);
	}

	/**
	 * 私有函数，保存结果到文件
	 */
	private function output() {
		// 获取目标文件的后缀
		$fileType = substr(strrchr($this->destImage, '.'), 1);
		if ($fileType=='jpg' || $fileType=='jpeg') {
			imagejpeg($this->canvas, $this->destImage);
		} else {
			// 默认输出 png 图片
			imagepng($this->canvas, $this->destImage);
		}

	}

	/**
	 * @return the $srcImages
	 */
	public function getSrcImages() {
		return $this->srcImages;
	}

	/**
	 * @param Array $srcImages
	 */
	public function setSrcImages($srcImages) {
		$this->srcImages = $srcImages;
	}

	/**
	 * @return the $width
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * @param int $width
	 */
	public function setWidth($width) {
		$this->width = $width;
	}

	/**
	 * @return the $height
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * @param int $height
	 */
	public function setHeight($height) {
		$this->height = $height;
	}

	/**
	 * @return the $mode
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * @param const $mode
	 */
	public function setMode($mode) {
		$this->mode = $mode;
	}

	/**
	 * @return the $destImage
	 */
	public function getDestImage() {
		return $this->destImage;
	}

	/**
	 * @param String $destImage
	 */
	public function setDestImage($destImage) {
		$this->destImage = $destImage;
	}
}
?>
