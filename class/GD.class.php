<?php
class GD{
  /**
   * 图片压缩
   * $uploadfile 要上传的文件资源，需要用imagecreatefrom jpg|png ）创建
   * $maxwidth 最大宽度px
   * $maxheight 最大高度px
   * $filename 最后输出位置，带名称的地址
   * 
   * @return bool|string
   */
	public function resizeImage( $uploadfile, $maxwidth, $maxheight, $filename ) {
		//取得当前图片大小
		$width = imagesx( $uploadfile );
		$height = imagesy( $uploadfile );

		//压缩比值

		$i = 0.5;
		//生成缩略图的大小
		if ( ( $width > $maxwidth ) || ( $height > $maxheight ) ) {

			$widthratio = $maxwidth / $width;
			$heightratio = $maxheight / $height;

			if ( $widthratio < $heightratio ) {
				$ratio = $widthratio;
			} else {
				$ratio = $heightratio;
			}

			$newwidth = $width * $ratio;
			$newheight = $height * $ratio;

			if ( function_exists( "imagecopyresampled" ) ) {
				$uploaddir_resize = imagecreatetruecolor( $newwidth, $newheight );
				imagecopyresampled( $uploaddir_resize, $uploadfile, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			} else {
				$uploaddir_resize = imagecreate( $newwidth, $newheight );
				imagecopyresized( $uploaddir_resize, $uploadfile, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			}

			ImageJpeg( $uploaddir_resize, $filename );
			ImageDestroy( $uploaddir_resize );
			return true;
		} else {
			ImageJpeg( $uploadfile, $filename );
      return true;
		}
	}
}



?>