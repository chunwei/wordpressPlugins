<?php
/* 
Plugin Name: AutoSave_Image
Version: 0.0.1
Plugin URI: http://www.imdevice.com
Description: 自动保存远程图片
Author: Lu Chunwei
Author URI: http://www.imdevice.com
*/

add_action('xmlrpc_publish_post', 'Auto_Save_Image');

//保存或修改文章时自动保存远程图片
function Auto_Save_Image($post_id){
	//$Auto_Save_Image = get_option("Auto_Save_Image");
	//$Auto_Save_Image = split("@@@",$Auto_Save_Image);

	$photo_savepath = "auto_save_image/";//$Auto_Save_Image[0];
	
	$post=get_post($post_id);
	$content=$post->post_content;
	$post_title =$post->post_title;

	//保存图片
		require_once("../wp-includes/class-snoopy.php");
		$snoopy_Auto_Save_Image = new Snoopy;
		// begin to save pic;
		$img_array = array();
		$content1 = stripslashes($content);
		if (get_magic_quotes_gpc()) $content1 = stripslashes($content1);
		preg_match_all("/ src=(\"|\'){0,}(http:\/\/(.+?))(\"|\'|\s)/is",$content1,$img_array);
		$img_array = array_unique(dhtmlspecialchars($img_array[2]));
		foreach ($img_array as $key => $value){
			set_time_limit(180); //每个图片最长允许下载时间,秒
			if(str_replace(get_bloginfo('url'),"",$value)==$value&&str_replace(get_bloginfo('home'),"",$value)==$value){
				$fileext = substr(strrchr($value,'.'),1);
				$fileext = strtolower($fileext);
				if($fileext==""||strlen($fileext)>4)$fileext = "jpg";
				$savefiletype = array('jpg','gif','png','bmp');
				if (in_array($fileext, $savefiletype)){ 
					if($snoopy_Auto_Save_Image->fetch($value)){
						$get_file = $snoopy_Auto_Save_Image->results;
					}else{
						echo "error fetching file: ".$snoopy_Auto_Save_Image->error."<br>";
						echo "error url: ".$value;
						die();
					}
					$filetime = time();
					$filepath = "/wp-content/uploads/".$photo_savepath.date("Y",$filetime)."/".date("m",$filetime)."/";//图片保存的路径目录
					!is_dir("..".$filepath) ? mkdirs("..".$filepath) : null; 
					$filename = date("His",$filetime).random(3);

					$fp = @fopen("..".$filepath.$filename.".".$fileext,"w");
					@fwrite($fp,$get_file);
					fclose($fp);
			
					$wp_filetype = wp_check_filetype( $filename.".".$fileext, false );
					$type = $wp_filetype['type'];
					$title = $post_title;
					$url = get_bloginfo('url').$filepath.$filename.".".$fileext;
					$file = $_SERVER['DOCUMENT_ROOT'].$filepath.$filename.".".$fileext;
					
					//添加数据库记录
					$attachment = array(
						'post_type' => 'attachment',
						'post_mime_type' => $type,
						'guid' => $url,
						'post_parent' => $post_id,
						'post_title' => $title,
						'post_content' => '',
					);
					$id = wp_insert_attachment($attachment, $file, $post_parent);
					//if ( !is_wp_error($id) ) {
						//这里会生成缩略图，不要了
						//wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
					//}
					
					$content1 = str_replace($value,get_bloginfo('url').$filepath.$filename.".".$fileext,$content1); //替换文章里面的图片地址
				}
			}
		}
		$content = AddSlashes($content1);
		// end save pic;
		
		$post->post_content=$content ;
		remove_action('xmlrpc_publish_post', 'Auto_Save_Image');
		wp_update_post($post);
		add_action('xmlrpc_publish_post', 'Auto_Save_Image');

}

//用到的函数
function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	}else{
		$string = str_replace('&', '&', $string);
		$string = str_replace('"', '"', $string);
		$string = str_replace('<', '<', $string);
		$string = str_replace('>', '>', $string);
		$string = preg_replace('/&(#\d;)/', '&\1', $string);
	}
	return $string;
}

function random($length) {
	$hash = '';
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	mt_srand((double)microtime() * 1000000);
	for($i = 0; $i < $length; $i++) {
	  $hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
 
function mkdirs($dir)
{
	if(!is_dir($dir))
	{
		mkdirs(dirname($dir));
		mkdir($dir);
	}
	return ;
}  
?>
