<?PHP

class template {

function fill_area($data){

$content = '<div class="category_area_contact">' . LB . 
			$data . '
			</div>' . LB;
	  
return $content;
}

function two_column($left_column,$right_column,$position = '', $borderleft = '', $borderright = '') {

$content = '<div class="category_area">' . LB . 
          '<table width="100%">' . LB . 
		  '<tr>' . LB . 
		  '<td class="'.($position == 'right' ? 'category_content' : 'nav_left').'" '.(!empty($borderleft) ? 'style="'.$borderleft.'"' : '').' valign="top">' . LB . 
		  $left_column .
		  '</td>' . LB . 
		  '<td valign="top" '.(!empty($borderright) ? 'style="'.$borderright.'"' : '').' class="'.($position == 'right' ? 'nav_left' : 'category_content').'">' . LB . 
			$right_column . '
    </td>' . LB . 
	'</tr>' . LB . 
	'</table>' . LB . 
	'</div>' . LB;
	
return $content;
}

function front_page($left,$image,$center,$right) {
$content = '<table width="100%" border="0" cellspacing="0" cellpadding="8">' . LB . 
        '<tr>' . LB . 
          '<td width="'. LEFT_HOMEPAGE_COLUMN_WIDTH . '" valign="top">' . LB . 
		   $left . 
          '</td>' . LB . 
          '<td width="' . CENTER_HOMEPAGE_COLUMN_WIDTH . '" valign="top">' . LB . 
		   $image . '<br>' . 
           $center . 
		  '</td>' . LB . 
          '<td width="' . RIGHT_HOMEPAGE_COLUMN_WIDTH . '" valign="top">' . LB . 
		   $right . 
          '</td>' . LB . 
        '</tr>' . LB . 
      '</table>' . LB;
	  
return $content;	  
}

}

?>