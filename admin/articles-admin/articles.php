<?PHP
require '../includes/application_top.php';

$update_sel = $_GET['update'];
$_SESSION['article_list'] = 1;

switch ($update_sel) {
case 'article_updated':
$message = '<script language="javascript">alert(\'Article Updated!\');</script>';
break;
case 'article_added':
$message = '<script language="javascript">alert(\'New article added!\');</script>';
break;
}

if (isset($_POST['Delete'])) {
foreach($_POST['delete_article'] as $article_id) {
mysql_query('DELETE FROM articles WHERE articles_id = "' . $article_id . '";');
mysql_query('DELETE FROM articles_to_categories WHERE articles_id = "' . $article_id . '";');
mysql_query('DELETE FROM articles_images WHERE articles_id = "' . $article_id . '";');
}
}

// filter by status
if (isset($_GET['filterstatus'])) {
$_SESSION['filter_status'] = ($_GET['filterstatus'] == 0 ? '' : ' status = "'.(int)$_GET['filterstatus'].'" ');
$_SESSION['filter_id'] = (int)$_GET['filterstatus'];
}

// filter by category
if (isset($_GET['catfilterstatus'])) {
$_SESSION['category_join'] = ($_GET['catfilterstatus'] == 0 ? '' : ' INNER JOIN articles_to_categories atc ON a.articles_id = atc.articles_id');
$_SESSION['category_filter'] = ($_GET['catfilterstatus'] == 0 ? '' : ' atc.categories_id = "'.(int)$_GET['catfilterstatus'].'" ');
$_SESSION['categories_filter_id'] = $_GET['catfilterstatus'];
}

// set page filter
if (!isset($_GET['pageid'])) $_GET['pageid'] = 1;
$_SESSION['ppfilterid'] = (isset($_GET['filterpp']) ? $_GET['filterpp'] : (isset($_SESSION['ppfilterid']) ? $_SESSION['ppfilterid'] : 10));
$start_val = ((isset($_GET['pageid']) ? $_GET['pageid'] - 1 : 0 )) * $_SESSION['ppfilterid'];
$end_val = ($_GET['pageid'] * $_SESSION['ppfilterid']);
$_SESSION['ppfilter'] = ' LIMIT '.$_SESSION['ppfilterid'].' OFFSET ' . $start_val;

if (isset($_POST['search'])) {
$search_string = " title LIKE '%".$_POST['search']."%' or summary LIKE '%".$_POST['search']."%' or content LIKE '%".$_POST['search']."%' or author LIKE '%".$_POST['search']."%'";
}

// order by
	  switch ($_GET['orderby']) {
	  case 'sort_order':
	  $orderby_str = 'sort_order ASC ';
	  break;
	  case 'sort_orderdesc':
	  $orderby_str = 'sort_order DESC ';
	  break;
	  case 'id':
	  $orderby_str = 'articles_id ASC ';
	  break;
	  case 'iddesc':
	  $orderby_str = 'articles_id DESC ';
	  break;
	  case 'title':
	  $orderby_str = 'title ASC ';
	  break;
	  case 'titledesc':
	  $orderby_str = 'title DESC ';
	  break;
	  case 'status':
	  $orderby_str = 'status ASC ';
	  break;
	  case 'statusdesc':
	  $orderby_str = 'status DESC ';
	  break;
	  case 'added':
	  $orderby_str = 'created ASC ';
	  break;
	  case 'addeddesc':
	  $orderby_str = 'created DESC ';
	  break;
	  case 'modified':
	  $orderby_str = 'modified ASC ';
	  break;
	  case 'modifieddesc':
	  $orderby_str = 'modified DESC ';
	  break;
	  default:
	  $orderby_str = 'title ASC ';
	  }

$articles_query = mysql_query('SELECT a.sort_order, a.articles_id, a.title, a.status, a.modified, a.created FROM articles a '.$_SESSION['category_join'].(!empty($_SESSION['category_filter']) || !empty($_SESSION['filter_status']) || !empty($search_string) ? ' WHERE' : '' ).$_SESSION['category_filter'].(!empty($_SESSION['category_filter']) && (!empty($_SESSION['filter_status']) || !empty($search_string)) ? ' and' : '').$_SESSION['filter_status'].(!empty($_SESSION['filter_status']) && !empty($search_string) ? ' and' : '').$search_string.'ORDER BY '.$orderby_str.' '.$_SESSION['ppfilter'].';');

$articles_count_query = mysql_query('SELECT a.articles_id, a.title, a.status, a.modified, a.created FROM articles a '.$_SESSION['category_join'].(!empty($_SESSION['category_filter']) || !empty($_SESSION['filter_status']) || !empty($search_string) ? ' WHERE' : '' ).$_SESSION['category_filter'].(!empty($_SESSION['category_filter']) && (!empty($_SESSION['filter_status']) || !empty($search_string)) ? ' and' : '').$_SESSION['filter_status'].(!empty($_SESSION['filter_status']) && !empty($search_string) ? ' and' : '').$search_string.'ORDER BY '.$orderby_str.';');

$articles_count = mysql_num_rows($articles_count_query);
$articles_rows = "";

// write page links
$article_count_div = ceil($articles_count/$_SESSION['ppfilterid']);
$max_ceil_articles = $_SESSION['ppfilterid'] * $article_count_div;
$page_num = 0;
for ($num = $_SESSION['ppfilterid']; $num <= $max_ceil_articles; $num = $num + $_SESSION['ppfilterid']) {
$page_num++;
$pagelinks .= '<a href="?pageid='.$page_num.'">'.$page_num.'</a> ';
}

if ($articles_count > 0) {
$list = 0;
while ($articles_result = mysql_fetch_array($articles_query)) {
$list++;
$list == 2 ? $list = 0 : ''; 
$list == 1 ? $cssclass = "class=\"listing_even\" " : $cssclass = ""; 

$articles_rows .= "<tr ".$cssclass.">\r\n";
$articles_rows .= "<td class=\"section_list_left\">".$articles_result['title']."</td>\r\n";
$articles_rows .= "<td class=\"section_list\">";
// bof build assigned categories_list
$categories_query = mysql_query('SELECT c.name FROM categories c LEFT JOIN articles_to_categories atc ON  c.categories_id = atc.categories_id WHERE atc.articles_id = "'.$articles_result['articles_id'].'" ORDER BY name ASC;');
$categories_count = mysql_num_rows($categories_query);
$cur_article = 0;
while ($categories_result = mysql_fetch_array($categories_query)) {
$cur_article++;
$articles_rows .= $categories_result['name'] . ( $cur_article < $categories_count ? ', ' : ' ');
}
// eof build assigned categories_list
$articles_rows .= "</td>\r\n";
$articles_rows .= "<td class=\"section_list\">";
// bof get articles status
$articles_status_query = mysql_query('SELECT status FROM articles_status WHERE articles_status_id = "'.$articles_result['status'].'";');
$articles_status_result = mysql_fetch_array($articles_status_query);
$articles_rows .= $articles_status_result['status'];
// eof get articles status
$articles_rows .= "</td>\r\n";
$articles_rows .= "<td class=\"section_list\" align=\"center\">".$articles_result['sort_order']."</td>\r\n";
$articles_rows .= "<td class=\"section_list\">".date('m/d/Y',strtotime($articles_result['created']))."</td>\r\n";
$articles_rows .= "<td class=\"section_list\">".date('m/d/Y',strtotime($articles_result['modified']))."</td>\r\n";
$articles_rows .= "<td align=\"center\" class=\"section_list\"><NOBR><a href=\"articles-edit.php?mode=edit&artid=".$articles_result['articles_id']."\" class=\"button\">Edit</a> <input name=\"delete_article[]\" type=\"checkbox\" value=\"".$articles_result['articles_id']."\"></NOBR></td>\r\n";
$articles_rows .= "</tr>\r\n";
}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Articles: Articles</title>
<link type="text/css" href="../includes/global.css" rel="stylesheet">
<script language="JavaScript" src="../includes/functions.js" type="text/javascript"></script>
</head>
<body>
<div class="container">
  <div class="header_area"></div>
  <div class="content">
    <table width="800" border="0" align="center" cellpadding="0" cellspaing="0" class="main_table">
      <tr>
        <td width="127" valign="top" class="left_nav"><?PHP require('../includes/mainmenu.php'); ?></td>
        <td align="right" valign="top"><div class="bc_nav"><?PHP echo brc(array('Articles' => 'articles-admin/articles.php')); ?></div>
          <table border="0" align="center" cellpadding="0" cellspacing="0" class="item_list">
            <tr align="right">
              <td align="center" class="listing_search"># Per Page: </td>
              <td align="center" class="listing_search">Filter By Status: </td>
              <td align="center" class="listing_search">Filter By Category:</td>
              <td width="250" class="listing_search">&nbsp;</td>
            </tr>
            <tr align="right">
              <td align="center" valign="top" class="listing_search"><form name="articlelimitform1" method="post" action="">
  <select name="ppmenu1" onChange="MM_jumpMenu('parent',this,0)">
        <option value="?filterpp=10" <?PHP echo ($_SESSION['ppfilterid'] == 10 ? 'selected' : ''); ?>>10</option>
        <option value="?filterpp=25" <?PHP echo ($_SESSION['ppfilterid'] == 25 ? 'selected' : '');?>>25</option>
        <option value="?filterpp=50" <?PHP echo ($_SESSION['ppfilterid'] == 50 ? 'selected' : '');?>>50</option>
      </select>
                            </form></td>
              <td align="center" valign="top" class="listing_search"><form name="status_filter" method="post" action="">
  <select name="statusmenu1" onChange="MM_jumpMenu('parent',this,0)">
        <option value="articles.php?filterstatus=0"></option>
        <?PHP
// bof get articles status
$articles_status_query = mysql_query('SELECT articles_status_id, status FROM articles_status ORDER BY articles_status_id ASC;');
while ($articles_status_result = mysql_fetch_array($articles_status_query)) {
echo '<option value="?filterstatus='.$articles_status_result['articles_status_id'].'" '.($_SESSION['filter_id'] == $articles_status_result['articles_status_id'] ? 'selected' : '').'>'.$articles_status_result['status'].'</option>';
}
// eof get articles status
?>
      </select>
                            </form></td>
              <td align="center" valign="top" class="listing_search"><form name="category_filer" method="post" action="">
  <select name="categorymenu1" onChange="MM_jumpMenu('parent',this,0)">
        <option value="articles.php?catfilterstatus=0"></option>
        <?PHP
// bof filter articles
$categories_query = mysql_query('SELECT categories_id, name FROM categories ORDER BY categories_id ASC;');
while ($categories_result = mysql_fetch_array($categories_query)) {
echo '<option value="?catfilterstatus='.$categories_result['categories_id'].'" '.($_SESSION['categories_filter_id'] == $categories_result['categories_id'] ? 'selected' : '').'>'.$categories_result['name'].'</option>';
}
// eof filter articles
?>
      </select>
                            </form></td>
              <td width="250" class="listing_search"><form action="" method="post" name="search" id="search">
                <input name="search" type="text" size="20">
                <input type="submit" name="Submit" value="Search" >
              </form></td>
            </tr>
            <tr>
              <td colspan="4">
			  <form action="" method="post" name="article_manager">
			  <table width="100%" border="0" cellpadding="2" cellspacing="0">
                  <tr align="center">
                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'title' ?  '?orderby=titledesc' :  '?orderby=title'); ?>">Title</a></td>
                    <td class="table_header">Categories</td>
                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'status' ?  '?orderby=statusdesc' :  '?orderby=status'); ?>">Status</a></td>
                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'sort_order' ?  '?orderby=sort_orderdesc' :  '?orderby=sort_order'); ?>">Sort Order</a></td>
                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'added' ? '?orderby=addeddesc' : '?orderby=added' ); ?>"> Added </a></td>
                    <td class="table_header"><a href="<?PHP echo ($_GET['orderby'] == 'modified' ? '?orderby=modifieddesc' : '?orderby=modified' ); ?>">Modified</a></td>
                    <td width="90" class="table_header_right">Edit/Delete</td>
                  </tr>
                  <?PHP echo $articles_rows; ?>
              </table>
			  <div style="padding:5px;" align="right"><input name="Delete" type="submit" value="Delete Selected" onclick="return confirm('Are you sure you want to delete the selected items?')"></div>
			  </form>
			  </td>
            </tr>
            <tr align="right">
              <td align="left" class="table_footer"><a href="articles-edit.php?mode=new" class="button">Add New</a> </td>
              <td class="table_footer"><div align="center"><?PHP echo ' &nbsp;Showing ' . ($start_val+1) . ' - ' . ($articles_count < $end_val ? $articles_count : $end_val) . ' of ' . $articles_count; ?> Articles</div></td>
              <td align="right" class="table_footer" colspan="2"><div align="right">Pages <?PHP echo $pagelinks ?></div></td>
            </tr>
          </table></td>
      </tr>
    </table>
    <?PHP require '../includes/footer.php'; ?>
  </div>
</div>
<?PHP
echo $message;
?>
</body>
</html>
