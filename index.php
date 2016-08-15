<?PHP

require 'includes/application_top.php';

require INCLUDES_DIRECTORY.'/classes/home_page.php';

$home_page = new home_page();

// load template 1
if (HOMEPAGE_TEMPLATE_VAL == 1) {
// write content to template class
$content = $template->front_page(encapsulate($home_page->left_content()),encapsulate($home_page->center_image()),encapsulate($home_page->center_content()),encapsulate($home_page->right_content()));
// load template 2
} elseif (HOMEPAGE_TEMPLATE_VAL == 2) {
$left_column = encapsulate($home_page->left_content());
$right_column = encapsulate($home_page->center_image(),'border-bottom:1px solid #000') . encapsulate($home_page->center_content(),'border-bottom:1px solid #000') . encapsulate($home_page->right_content());

// write content to template class
$content = $template->two_column($left_column,$right_column,'','border-right:1px solid #000');
// load template 3
}elseif (HOMEPAGE_TEMPLATE_VAL == 3) {
$left_column = encapsulate($home_page->left_content(),'border-bottom:1px solid #000') . encapsulate($home_page->center_image(),'border-bottom:1px solid #000') . encapsulate($home_page->center_content());
$right_column = encapsulate($home_page->right_content());

// write content to template class
$content = $template->two_column($left_column,$right_column,'right','','border-left:1px solid #000');
}

// load template file
require INCLUDES_DIRECTORY.'/templates/index.php';
?>
