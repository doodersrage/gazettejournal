<?PHP
class embed {

function determine_media_type($media_name,$width = '',$height = '',$align = '', $class = '',$link = '',$target = '',$dir = '') {

!empty($dir) ? $dir = $dir : $dir = 'images/';

if (file_exists(IMAGES_DIRECTORY.$media_name)) {
if (strpos($media_name,'.mov') || strpos($media_name,'.3g2')) {
$media_string = $this->load_quicktime($dir.$media_name,$width,$height,$align);
} elseif (strpos($media_name,'.swf')) {
$media_string = $this->load_flash($dir.$media_name,$width,$height,$align);
} elseif (strpos($media_name,'.flv')) {
$media_string = $this->load_flash_video($dir.$media_name,$width,$height,$align);
} elseif (strpos($media_name,'.mpg') || strpos($media_name,'.wmv') || strpos($media_name,'.wma')) {
$media_string = $this->load_other_video($dir.$media_name,$width,$height,$align);
} elseif (strpos($media_name,'.ra') || strpos($media_name,'.rm')) {
$media_string = $this->load_realmedia($dir.$media_name,$width,$height,$align);
} else {
$media_string = $this->load_image($dir.$media_name,$width,$height,$align,$class,$link,$target);
}
} else {
$media_string = $this->load_image($dir."imagenotfound.jpg",$width,$height,$align,$class,$link,$target);
}

return $media_string;
}

function load_flash($media_name,$width = '',$height = '',$align = '') {
$flash_embed = "<!-- begin embedded Flash file... -->" . LB . 
      "<table border='0' cellpadding='0' ".(!empty($align) ? "align=\"".$align."\" " : "" ).">" . LB . 
        "<tr><td>" . LB . 
        "<OBJECT classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'" . LB . 
        "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0'" . LB . 
        "width=\"".$width."\" height=\"".$height."\">" . LB . 
        "<param name='movie' value=\"".$media_name."\">" . LB . 
        "<param name='quality' value=\"high\">" . LB . 
        "<param name='bgcolor' value='#FFFFFF'>" . LB . 
        "<param name='loop' value=\"false\">" . LB . 
        "<EMBED src=\"".$media_name."\" quality='high' bgcolor='#FFFFFF' width=\"".$width."\"" . LB . 
        "height=\"".$height."\" loop=\"false\" type='application/x-shockwave-flash'" . LB . 
        "pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'>" . LB . 
        "</EMBED>" . LB . 
        "</OBJECT>" . LB . 
        "</td></tr>" . LB . 
        "<!-- ...end embedded Flash file -->" . LB . 
       "</table>" . LB;

return $flash_embed;
}

function load_flash_video($media_name,$width = '',$height = '',$align = '') {
$flash_video_embed = "<!-- begin embedded Flash file... -->" . LB . 
      "<table border='0' cellpadding='0' ".(!empty($align) ? "align=\"".$align."\" " : "" ).">" . LB . 
        "<tr><td>" . LB . 
        "<OBJECT classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'" . LB . 
        "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0'" . LB . 
        "width=\"".$width."\" height=\"".$height."\">" . LB . 
        "<param name='movie' value=\"".SITE_ADDRESS."flvplayer.swf?file=".$media_name."&autoStart=false\">" . LB . 
        "<param name='quality' value=\"high\">" . LB . 
        "<param name='bgcolor' value='#FFFFFF'>" . LB . 
        "<param name='loop' value=\"false\">" . LB . 
        "<EMBED src=\"".SITE_ADDRESS."flvplayer.swf?file=".$media_name."\" quality='high' width=\"".$width."\"" . LB . 
        "height=\"".$height."\" loop=\"false\" type='application/x-shockwave-flash'" . LB . 
        "pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'>" . LB . 
        "</EMBED>" . LB . 
        "</OBJECT>" . LB . 
        "</td></tr>" . LB . 
        "<!-- ...end embedded Flash file -->" . LB . 
       "</table>" . LB;

return $flash_video_embed;
}

function load_quicktime($media_name,$width = '',$height = '',$align = '') {
$quicktime_embed = "<!-- begin embedded QuickTime file... -->" . LB . 
      "<table border='0' cellpadding='0' ".(!empty($align) ? "align=\"".$align."\" " : "" ).">" . LB . 
        "<!-- begin video window... -->" . LB . 
        "<tr><td>" . LB . 
        "<OBJECT classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width=\"".$width."\"" . LB . 
        "height=\"".$height."\" codebase='http://www.apple.com/qtactivex/qtplugin.cab'>" . LB . 
        "<param name='src' value=\"".$media_name."\">" . LB . 
        "<param name='autoplay' value=\"true\">" . LB . 
        "<param name='controller' value=\"true\">" . LB . 
        "<param name='loop' value=\"false\">" . LB . 
        "<EMBED src=\"".$media_name."\" width=\"".$width."\" height=\"".$height."\" autoplay=\"false\" " . LB . 
        "controller=\"true\" loop=\"false\" pluginspage='http://www.apple.com/quicktime/download/'>" . LB . 
        "</EMBED>" . LB . 
        "</OBJECT>" . LB . 
        "</td></tr>" . LB . 
        "<!-- ...end embedded QuickTime file -->" . LB . 
        "</table>" . LB;

return $quicktime_embed;
}

function load_realmedia($media_name,$width = '',$height = '',$align = '') {
$real_media_embed = "<!-- begin embedded RealMedia file... -->" . LB . 
      "<table border='0' cellpadding='0' ".(!empty($align) ? "align=\"".$align."\" " : "" ).">" . LB . 
        "<!-- begin video window... -->" . LB . 
        "<tr><td>" . LB . 
        "<OBJECT id='rvocx' classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA'" . LB . 
        "width=\"".$width."\" height=\"".$height."\">" . LB . 
        "<param name='src' value=\"".$media_name."\">" . LB . 
        "<param name='autostart' value=\"false\">" . LB . 
        "<param name='controls' value='imagewindow'>" . LB . 
        "<param name='console' value='video'>" . LB . 
        "<param name='loop' value=\"false\">" . LB . 
        "<EMBED src=\"".$media_name."\" width=\"".$width."\" height=\"".$height."\" " . LB . 
        "loop=\"false\" type='audio/x-pn-realaudio-plugin' controls='imagewindow' console='video' autostart=\"false\">" . LB . 
        "</EMBED>" . LB . 
        "</OBJECT>" . LB . 
        "</td></tr>" . LB . 
        "<!-- ...end video window -->" . LB . 
          "<!-- begin control panel... -->" . LB . 
          "<tr><td>" . LB . 
          "<OBJECT id='rvocx' classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA'" . LB . 
          "width=\"".$width."\" height='".$height."'>" . LB . 
          "<param name='src' value=\"".$media_name."\">" . LB . 
          "<param name='autostart' value=\"false\">" . LB . 
          "<param name='controls' value='ControlPanel'>" . LB . 
          "<param name='console' value='video'>" . LB . 
          "<EMBED src=\"".$media_name."\" width=\"".$width."\" height='".$height."' " . LB . 
          "controls='ControlPanel' type='audio/x-pn-realaudio-plugin' console='video' autostart=\"false\">" . LB . 
          "</EMBED>" . LB . 
          "</OBJECT>" . LB . 
          "</td></tr>" . LB . 
          "<!-- ...end control panel -->" . LB . 
          "<!-- ...end embedded RealMedia file -->" . LB . 
        "<!-- begin link to launch external media player... -->" . LB . 
        "<tr><td align='center'>" . LB . 
        "<a href=\"".$media_name."\" style='font-size: 85%;' target='_blank'>Launch in external player</a>" . LB . 
        "<!-- ...end link to launch external media player... -->" . LB . 
        "</td></tr>" . LB . 
      "</table>" . LB;

return $real_media_embed;
}

function load_other_video($media_name,$width = '',$height = '',$align = '') {
$other_embed = "<!-- begin embedded WindowsMedia file... -->" . LB . 
      "<table border='0' cellpadding='0' ".(!empty($align) ? "align=\"".$align."\" " : "" ).">" . LB . 
      "<tr><td>" . LB . 
      "<OBJECT id='mediaPlayer' width=\"".$width."\" height=\"".$height."\" " . LB . 
      "classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95' " . LB . 
      "codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701'" . LB . 
      "standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'>" . LB . 
      "<param name='fileName' value=\"".$media_name."\">" . LB . 
      "<param name='animationatStart' value='true'>" . LB . 
      "<param name='transparentatStart' value='true'>" . LB . 
      "<param name='autoStart' value=\"false\">" . LB . 
      "<param name='showControls' value=\"true\">" . LB . 
      "<param name='loop' value=\"false\">" . LB . 
      "<EMBED type='application/x-mplayer2'" . LB . 
        "pluginspage='http://microsoft.com/windows/mediaplayer/en/download/'" . LB . 
        "id='mediaPlayer' name='mediaPlayer' displaysize='4' autosize='-1' " . LB . 
        "bgcolor='darkblue' showcontrols=\"true\" showtracker='-1' " . LB . 
        "showdisplay='0' showstatusbar='-1' videoborder3d='-1' width=\"".$width."\" height=\"".$height."\"" . LB . 
        "src=\"".$media_name."\" autostart=\"false\" designtimesp='5311' loop=\"false\">" . LB . 
      "</EMBED>" . LB . 
      "</OBJECT>" . LB . 
      "</td></tr>" . LB . 
      "<!-- ...end embedded WindowsMedia file -->" . LB . 
    "<!-- begin link to launch external media player... -->" . LB . 
        "<tr><td align='center'>" . LB . 
        "<a href=\"".$media_name."\" style='font-size: 85%;' target='_blank'>Launch in external player</a>" . LB . 
        "<!-- ...end link to launch external media player... -->" . LB . 
        "</td></tr>" . LB . 
      "</table>" . LB;
	  
return $other_embed;
}

function load_image($media_name,$max_width = '',$max_height = '',$align = '',$class = '',$link = '',$target = '') {

list($width,$height)=getimagesize($media_name);

$x_ratio = $max_width / $width;
$y_ratio = $max_height / $height;

if( ($width <= $max_width) && ($height <= $max_height) ){
    $tn_width = $width;
    $tn_height = $height;
    }elseif (($x_ratio * $height) < $max_height){
        $tn_height = ceil($x_ratio * $height);
        $tn_width = $max_width;
    }else{
        $tn_width = ceil($y_ratio * $width);
        $tn_height = $max_height;
}

$image_string = (!empty($link) ? "<a href=\"".$link."\" ".(!empty($target) ? "target=\"".$target."\"" : "").">" : "" ) . "<img src=\"".$media_name."\" alt=\"".$media_name."\" border=\"0\" height=\"".$tn_height."\" width=\"".$tn_width."\" ".(!empty($align) ? "align=\"".$align."\" " : "" ).(!empty($class) ? "class=\"".$class."\" " : "" ).">".(!empty($link) ? "</a>" : "");

return $image_string;
}

}
?>