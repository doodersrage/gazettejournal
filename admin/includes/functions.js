var ccmode

function new_centered_popup(popW,popH,document_link) {

var left = Math.floor((screen.availWidth - popW) / 2);
var top = Math.floor((screen.availHeight - popH) / 2);
return window.open(document_link,"","height="+popH+",width="+popW+",left="+left+",top="+top+",scrollbars=1");
}

function new_centered_absolute(popW,popH,content) {

var left = Math.floor((screen.availWidth - popW) / 2);
var top = Math.floor((screen.availHeight - popH) / 2);

document.getElementById("center_form").style.top = top+'px';
document.getElementById("center_form").style.left = left+'px';
document.getElementById("center_form").style.width = popW+'px';
document.getElementById("center_form").style.height = popH+'px';
document.getElementById("center_form").style.background = '#fff';
document.getElementById("center_form").style.border = '1px solid #000';
document.getElementById("center_form").style.padding = '5px';

document.getElementById("center_form").style.visibility = 'visible';

document.getElementById("center_form").innerHTML = content + '<p align="right"><a href="javascript: void(0)" onMouseDown="javascript: hide_abs_box(\'center_form\')">Close Window</a></p>';
}

function hide_abs_box(box_name) {
document.getElementById(box_name).style.visibility = 'hidden';
}
	


function check_credit() {

if (ccmode != true) {
	ccmode = false;
}

var payment_type = document.edit_order.payment_method.selectedIndex;	
if (payment_type == 0 || payment_type == 1 || payment_type === 2) {
	if (ccmode == false) {
document.getElementById("extra_fields").innerHTML = '<table width="200" border="0" cellpadding="0" cellspacing="0">'+
  '<tr>'+
    '<td>CC#:</td>'+
    '<td><input type="text" name="ccnum" id="ccnum"></td>'+
  '</tr>'+
  '<tr>'+
    '<td>EXP:</td>'+
    '<td><select name="month" id="month">'+
      '<option>01</option>'+
      '<option>02</option>'+
      '<option>03</option>'+
      '<option>04</option>'+
      '<option>05</option>'+
      '<option>06</option>'+
      '<option>07</option>'+
      '<option>08</option>'+
      '<option>09</option>'+
      '<option>10</option>'+
      '<option>11</option>'+
      '<option>12</option>'+
    '</select>'+
     ' / '+
    '<select name="year" id="year">'+
      '<option>2008</option>'+
      '<option>2009</option>'+
      '<option>2010</option>'+
      '<option>2011</option>'+
      '<option>2012</option>'+
    '</select>    </td>'+
  '</tr>'+
  '<tr>'+
    '<td>CVV:</td>'+
    '<td><input name="cvv" type="text" id="cvv" size="4"></td>'+
  '</tr>'+
'</table>';
ccmode = true;
	}
} else {
document.getElementById("extra_fields").innerHTML = '';
ccmode = false;
}
}

function chngtableheader(id) {
	document.getElementById(id).style.background = '#FFF';
}

function chngtableheaderbck(id) {
	document.getElementById(id).style.background = '#CCCCCC';
}

function display_password_fld() {
document.getElementById('password_area').style.display = 'block';
document.getElementById('password_link').style.display = 'none';
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function changesection(id) {
switch (id) {
case 1:
document.getElementById('content_left').style.backgroundColor = '#FFFF66';
document.getElementById('content_center').style.backgroundColor = '#FFF';
document.getElementById('content_right').style.backgroundColor = '#CCCCCC';
document.getElementById('front_section').value = 1;
break;
case 2:
document.getElementById('content_left').style.backgroundColor = '#000';
document.getElementById('content_center').style.backgroundColor = '#FFFF66';
document.getElementById('content_right').style.backgroundColor = '#CCCCCC';
document.getElementById('front_section').value = 2;
break;
case 3:
document.getElementById('content_left').style.backgroundColor = '#000';
document.getElementById('content_center').style.backgroundColor = '#FFF';
document.getElementById('content_right').style.backgroundColor = '#FFFF66';
document.getElementById('front_section').value = 3;
break;
}

}

function remove_image(image_id) {
document.getElementById('image'+image_id).style.display = 'none';
if (document.getElementById('deleted_images').value != '') {
	document.getElementById('deleted_images').value += ','+image_id;
} else {
	document.getElementById('deleted_images').value += image_id;
}
}

function remove_file(setvalue) {
document.getElementById('file_info').style.display = 'none';
document.getElementById('delete_file').value += setvalue;
}


var cur_image_num = 1;

function new_image_fld() {
	
cur_image_num++;
	
image_field_string = '<tr id="new_image_box_'+cur_image_num+'"><td><table><tr>'+
            '  <td>Main Image:'+
            '     <input name="homepage_image" value="new_image_'+cur_image_num+'" type="radio"></td>'+
            '   <td>Sort Order:'+
            '     <input name="sort_order['+cur_image_num+']" type="text" size="4"></td>'+
            '  </tr>'+
            '  <tr>'+
            '   <td colspan="2"><input type="file" name="image['+cur_image_num+']"></td>'+
            '  </tr>'+
			'	<tr>'+
            '    <td colspan="2">Caption:<br>'+
            '      <textarea name="image_caption['+cur_image_num+']" cols="30" rows="3" id="image_caption['+cur_image_num+']"></textarea></td>'+
             ' </tr>'+
			 '  <tr>'+
            '    <td colspan="2"><a href="javascript: new_image_fld();">Add Image</a> <a href="javascript: delete_new_image_fld('+cur_image_num+');">Remove Image</a></td>'+
            '  </tr>'+
            '  <tr>'+
            '    <td colspan="2" align="center">'+
            '      </td>'+
            '</tr></table><td></tr>';

$("#images_table").append(image_field_string);

//document.getElementById("extra_images").innerHTML += image_field_string;

tinyMCE.execCommand('mceAddControl',true,'image_caption['+cur_image_num+']');
//start_tinyMCE();
}

function delete_new_image_fld(field_num) {
document.getElementById('new_image_box_'+field_num).innerHTML = '';
}