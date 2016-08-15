<?PHP
require '../includes/application_top.php';

// vars
$update_sel = $_GET['update'];
$type = $_GET['type'];
$orderby = $_GET['orderby'];
$mode = $_GET['mode'];
$artid = $_GET['artid'];

switch ($update_sel) {
case 'article_updated':
$message = '<script language="javascript">alert(\'Article Updated!\');</script>';
break;
case 'article_added':
$message = '<script language="javascript">alert(\'New article added!\');</script>';
break;
}

if ($mode == 'delete') {
mysql_query("DELETE FROM listings WHERE customers_articles_id = '".$artid."';");
}

// set article type
switch ($type) {
case 'classifieds':
$section_id = 1;
break;
case 'births':
$section_id = 2;
$sub_id = 3;
break;
case 'weddings':
$section_id = 2;
break;
case 'obituaries':
$section_id = 3;
break;
}

// set page filter
if (!isset($_GET['pageid'])) $_GET['pageid'] = 1;
$_SESSION['ppfilterid'] = (isset($_GET['filterpp']) ? $_GET['filterpp'] : (isset($_SESSION['ppfilterid']) ? $_SESSION['ppfilterid'] : 10));
$start_val = ((isset($_GET['pageid']) ? $_GET['pageid'] - 1 : 0 )) * $_SESSION['ppfilterid'];
$end_val = ($_GET['pageid'] * $_SESSION['ppfilterid']);
$_SESSION['ppfilter'] = ' LIMIT '.$_SESSION['ppfilterid'].' OFFSET ' . $start_val;

if (!empty($search)) {
$search_string = ' title LIKE "%'.$search.'%" OR summary LIKE "%'.$search.'%" OR content LIKE "%'.$search.'%"';
}


// order by
	  switch ($orderby) {
	  case 'title':
	  $orderby_str = 'title ASC ';
	  break;
	  case 'titledesc':
	  $orderby_str = 'title DESC ';
	  break;
	  case 'added':
	  $orderby_str = 'added ASC ';
	  break;
	  case 'addeddesc':
	  $orderby_str = 'added DESC ';
	  break;
	  case 'modified':
	  $orderby_str = 'modified ASC ';
	  break;
	  case 'modifieddesc':
	  $orderby_str = 'modified DESC ';
	  break;
	  default:
	  $orderby_str = 'added DESC ';
	  }

// pull available categories from database
$articles_query = mysql_query('SELECT customers_articles_id, title, start_date, exp_date, modified, added FROM listings WHERE section_id = "'.$section_id.'"' . (!empty($sub_id) ? ' and sub_id="'.$sub_id.'"' : '') . (!empty($search_string) ? $search_string : '') . ' ORDER BY '.$orderby_str.' '.$_SESSION['ppfilter']);

$articles_count_query = mysql_query('SELECT customers_articles_id, title, start_date, exp_date, modified, added FROM listings WHERE section_id = "'.$section_id.'"' . (!empty($sub_id) ? ' and sub_id="'.$sub_id.'"' : '') . (!empty($search_string) ? $search_string : '') . ' ORDER BY '.$orderby_str.';');


$article_count = mysql_num_rows($articles_count_query);

	  // write page links
	  $article_count_div = ceil($article_count/$_SESSION['ppfilterid']);
	  $max_ceil_article = $_SESSION['ppfilterid'] * $article_count_div;
	  $page_num = 0;
	  for ($num = $_SESSION['ppfilterid']; $num <= $max_ceil_article; $num = $num + $_SESSION['ppfilterid']) {
	  $page_num++;
	  $pagelinks .= '<a href="?type='.$type.'&pageid='.$page_num.'">'.$page_num.'</a> ';
	  }


$article_rows = "";

$list = 0;
while ($articles_result = mysql_fetch_array($articles_query)) {
$list++;
$list == 2 ? $list = 0 : ''; 
$list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 

// get date and expiration info
$start_date = strtotime($articles_result['start_date']);
$exp_date = strtotime($articles_result['exp_date']);
$todays_date = strtotime(date("Y-m-d"));

if (empty($exp_date)) {
$article_status = "Does Not Expire";
} else {
if ($start_date > $todays_date) {
$article_status = "Not Yet Active";
} elseif ($exp_date >= $todays_date && $start_date <= $todays_date) {
$article_status = "Active";
}
if ($exp_date <= $todays_date) $article_status = "Expired";
}

$article_rows .= "<tr ".$cssclass.">\r\n";
$article_rows .= "<td class=\"section_list_left\">".$articles_result['title']."</td>\r\n";
$article_rows .= "<td class=\"section_list\">".$article_status."</td>\r\n";
$article_rows .= "<td class=\"section_list\">".date('m/d/Y',strtotime($articles_result['added']))."</td>\r\n";
$article_rows .= "<td class=\"section_list\">".date('m/d/Y',strtotime($articles_result['modified']))."</td>\r\n";
$article_rows .= "<td align=\"center\" class=\"section_list\"><a href=\"articles-edit.php?mode=edit&artid=".$articles_result['customers_articles_id']."\" class=\"button\">Edit</a> <a href=\"articles.php?type=".$type."&mode=delete&artid=".$articles_result['customers_articles_id']."\" onclick=\"return confirm('Are you sure you want to delete this item?')\" class=\"button\">Delete</a> </td>\r\n";
$article_rows .= "</tr>\r\n";

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Listings: Articles</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
</head>
<body>
<div class="container">
  <div class="header_area"></div>
  <div class="content">
    <table width="800" border="0" align="center" cellpadding="3" class="main_table">
      <tr>
        <td width="127" valign="top" class="left_nav"><?PHP require('../includes/mainmenu.php'); ?>
        </td>
        <td valign="top"><div class="bc_nav"><?PHP echo brc(array(ucfirst($type) => 'listings-admin/articles.php?type='.$type)); ?></div>
          <table border="0" align="center" cellpadding="0" cellspacing="0" class="item_list">
            <tr align="right">
              <td colspan="2" class="listing_search"><form name="articlelimitform1" method="post" action="">
  # Per Page:
      <select name="ppmenu1" onChange="MM_jumpMenu('parent',this,0)">
        <option value="?type=<?PHP echo $type; ?>&filterpp=10" <?PHP echo ($_SESSION['ppfilterid'] == 10 ? 'selected' : ''); ?>>10</option>
        <option value="?type=<?PHP echo $type; ?>&filterpp=25" <?PHP echo ($_SESSION['ppfilterid'] == 25 ? 'selected' : '');?>>25</option>
        <option value="?type=<?PHP echo $type; ?>&filterpp=50" <?PHP echo ($_SESSION['ppfilterid'] == 50 ? 'selected' : '');?>>50</option>
        <option value="?type=<?PHP echo $type; ?>&filterpp=100" <?PHP echo ($_SESSION['ppfilterid'] == 100 ? 'selected' : '');?>>100</option>
        <option value="?type=<?PHP echo $type; ?>&filterpp=200" <?PHP echo ($_SESSION['ppfilterid'] == 200 ? 'selected' : '');?>>200</option>
      </select>
              </form></td>
              <td class="listing_search"><form name="form1" method="post" action="">
                <input name="search" type="text" id="search">
                <input type="submit" name="Submit" value="Search">
              </form></td>
            </tr>
            <tr>
              <td colspan="3"><table width="100%" border="0" cellpadding="2" cellspacing="0">
                  <tr align="center">
                    <td class="table_header"><a href="<?PHP echo ($orderby == 'title' ?  '?orderby=titledesc' :  '?orderby=title'); ?>">Title</a></td>
                    <td class="table_header">Status</td>
                    <td class="table_header"><a href="<?PHP echo ($orderby == 'added' ? '?orderby=addeddesc' : '?orderby=added' ); ?>">Added</a> </td>
                    <td class="table_header"><a href="<?PHP echo ($orderby == 'modified' ? '?orderby=modifieddesc' : '?orderby=modified' ); ?>">Modified</a></td>
                    <td width="90" class="table_header_right">Options</td>
                  </tr>
                  <?PHP 
echo $article_rows;
?>
                </table></td>
            </tr>
            <tr align="right">
              <td align="left" class="table_footer"><a href="articles-edit.php?secid=<?PHP echo $section_id . (!empty($sub_id) ? '&sub_id='.$sub_id : ''); ?>" class="button">Add New</a> </td>
              <td align="left" class="table_footer"><?PHP echo ' &nbsp;Showing ' . ($start_val+1) . ' - ' . ($article_count < $end_val ? $article_count : $end_val) . ' of ' . $article_count; ?> Listings</td>
              <td class="table_footer"><div align="right">Pages <?PHP echo $pagelinks ?></div></td>
            </tr>
          </table></td>
      </tr>
    </table>
    <?PHP require '../includes/footer.php'; ?>
  </div>
</div>
</body>
</html>
