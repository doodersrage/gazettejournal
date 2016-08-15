<?PHP
class banners {

function load_front_bottom() {
global $embed;

$banners_query = mysql_query("SELECT banner_id, name, image, image_alt_text, link, new_window, weight FROM banners WHERE status > 0 ORDER BY RAND(), weight DESC LIMIT 0,1");
$banners_result = mysql_fetch_array($banners_query);

$banners_string = $embed->determine_media_type($banners_result['image'],169,144,'','',$banners_result['link'],(!empty($banners_result['new_window']) ? "_blank" : ""));

return $banners_string;
}

}
?>