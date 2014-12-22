<?php
/*
Plugin Name: Flash2HTML5Video_Youku
Plugin URI: http://imdevice.com
Description: Switch flash videos from Youku to HTML5 video when visitors come from IOS device
Version: 1.0
Author: Lu Chunwei
Author URI: http://imdevice.com
*/


add_action('init','add_js');
add_action('wp_footer', 'exe_js');

function add_js(){
//wp_enqueue_script('player5script',plugins_url('/player5script.js', __FILE__)  // where the this file is in /someplugin/
//);
?>
<script type="text/javascript"><!--
if (!window.Player5) {
            window.Player5 = {}
        }
Player5.getM3U8 = function(videoid, type) {
var d="http://youku.com";
    var r =d+ "/player/getM3U8/vid/" + videoid + "/";
    if (type !== undefined && (type == "flv" || type == "mp4")) {
        r += "type/" + type + "/"
    }
    r += "v.m3u8";
    return r
};
Player5.getVideoId=function(flvSrc){//pass the embed flash video 'src' as parameter
	var s=flvSrc.indexOf("sid")+4;
	var temp=flvSrc.substring(s);
	return temp.substr(0,temp.indexOf('/'));
};
Player5.isIphone = function() {
    if (navigator.userAgent.indexOf('iPhone') != -1) {
        return true
    }
    return false
};
Player5.isIpad = function() {
    if ((navigator.userAgent.indexOf('iPod') != -1) || (navigator.userAgent.indexOf('iPhone') != -1) || (navigator.userAgent.indexOf('iPad') != -1)) {
        return true
    }
    return false
};
Player5.newVideoNode=function(videoid) {
	var v=document.createElement("video");
	v.src=this.getM3U8(videoid);
	v.width=480;
	v.heigth=400;
	v.controls=true;
	v.preload=true;
	return v;
}

-->
</script>
<?php
}

function exe_js() {
    //echo '<script type="text/javascript">Player5.switch();</script>';
?>
<script type="text/javascript"><!--

if(Player5.isIpad()||true){
var pc=document.getElementsByTagName('article')[0];
var flashvideos=pc.getElementsByTagName('embed');
var flvs=[];
for(var i=0;i<flashvideos.length;i++){
if(flashvideos[i].src.indexOf("youku.com")>-1)
flvs[i]=flashvideos[i];
}
for(var i=0;i<flvs.length;i++){
var flv=flvs[i];
var videoid=Player5.getVideoId(flv.src);
var obj=flv.parentNode;
obj.parentNode.replaceChild(Player5.newVideoNode(videoid),obj);
}
flvs=[];
}
-->
</script>

<?php
}
?>