<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Gloucester-Mathews Gazette-Journal<?PHP echo (!empty($title_tag) ? ': ' . $title_tag: '' ); ?></title>
<link type="text/css" href="/includes/styles/categories.css" rel="stylesheet">
<base href="<?PHP echo SECURE_SITE_ADDRESS; ?>">
<link rel="stylesheet" type="text/css" href="/admin/includes/epoch/epoch_styles.css" /> <!--Epoch's styles-->
<script type="text/javascript" src="/admin/includes/epoch/epoch_classes.js"></script> <!--Epoch's Code-->
<script type="text/javascript">
/*<![CDATA[*/
/*You can also place this code in a separate file and link to it like epoch_classes.js*/
	var bas_cal,dp_cal,ms_cal;      
window.onload = function () {
	dp_cal  = new Epoch('epoch_popup','popup',document.getElementById('date_start'));
	dp_cal1  = new Epoch('epoch_popup1','popup',document.getElementById('date_end'));
};
/*]]>*/
</script>
<script language="JavaScript">

function calc_total_price() {
var words_cost,weeks_cost,total_cost;

var baseprice = <?PHP echo $item_price; ?>;

/*get number of words val*/
var words = document.form1.tot_word_count.value;
/*get weeks val*/
var weeks = document.form1.weeks.value;

/*process total cost for words over 25*/
if (words > 25) {
words_cost = words * .40;
if (weeks > 1) {
weeks_cost = words_cost * weeks;
} else {
weeks_cost = words_cost;
}
} else {
if (weeks > 1) {
weeks_cost = baseprice + (6 * (weeks - 1));
} else {
weeks_cost = baseprice;
}
}

total_cost = weeks_cost.toFixed(2);

/* assign cost values */
document.getElementById("total_cost").innerHTML = total_cost;
document.form1.total_cost_val.value = total_cost;
}

function countit(){
var formcontent=document.form1.new_ad.value;
formcontent=formcontent.split(" ");
var total_word_count = formcontent.length;
document.getElementById("word_count").innerHTML = total_word_count;
document.form1.tot_word_count.value = total_word_count;
calc_total_price();
}
</script>
</head>

<body>
<div class="container">
<div class="content">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center" valign="top" class="template_blue"><table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td align="center"><?PHP require INCLUDES_DIRECTORY.'/top_nav.php'; ?></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td bgcolor="#F2F1D1"><img src="images/banner.jpg" alt="Gloucester-Mathews Gazette-Journal" width="771" height="114"></td>
      </tr>
      <tr>
        <td class="template_line1"><table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td><div class="header_left"><?PHP echo DATE_VOLUME_STRING; ?></div> <div class="last_updated">Last Updated: <?PHP echo date("F j, Y, g:i a",strtotime(SITE_LAST_UPDATED)); ?></div></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td><img src="images/line2.jpg" alt="Holding The line" width="858" height="6"></td>
      </tr>
    </table>
<?PHP echo $content; ?>
<div align="center"><P class="copywrite">Copyright &copy; <?PHP echo date("Y"); ?>, Tidewater Newspapers, Inc.</P></div>
<?PHP echo $search_box; ?>
</div>
<div class="notepad">
<?PHP echo NOTEPAD_TEXT; ?>
</div>
</div>
</body>
</html>