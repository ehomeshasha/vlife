var digit_regex = /^\d+$/
function is_empty(val) {
	if(typeof val === 'undefined' || val == null || val == "") {
		return true;
	}
	return false;
}
function chkNumber(n, v) {
	if(v == "" || isNaN(v)) {
		alert('Number only for '+n);
		return false;
	}
	return true;
}
function chkDigit(n, v, min, max) {
	if(chkLength(n, v, min, max) === false) {
		return false;
	}
	if(!digit_regex.test(v)) {
		alert('Digit only for '+n);
		return false;
	}
	return true;
}
function chkLength(n, v, min, max) {
	if(min == 0 && v == "") {
		alert(n+' can not be empty');
		return false;
	}
	if(v.length < min || v.length > max) {
		alert('The length of '+n+' is not valid(must '+min+' < '+max+')');
		return false;
	}
	return true;
}
function chkUploadExist(n,o) {
	if(o.length == 0 || o.val() == "") {
		alert("Please upload "+n);
		return false;
	}
	return true;
}
function checkCookie(cname) {
	var cookiename=getCookie(cname);
	if (cookiename!="") {
  		return true;
  	} else {
  		return false;
  	}
}	
function setCookie(cname,cvalue,exdays)
{
var d = new Date();
d.setTime(d.getTime()+(exdays*24*60*60*1000));
var expires = "expires="+d.toGMTString();
document.cookie = cname + "=" + cvalue + "; " + expires+ ";path=/";
}
function getCookie(cname)
{
var name = cname + "=";
var ca = document.cookie.split(';');
for(var i=0; i<ca.length; i++) 
  {
  var c = ca[i].trim();
  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
  }
return "";
}
$(function(){
	$(document).on("click", ".clear_session", function() {
		$.post(site_url+'index.php?home=misc&act=clear_session');
	});
	$(".collapse_btn").click(function(){
		var id = $(this).attr("data-id");
		var status = $(this).attr("data-status");
		if(status == 'collapse') {
			$(this).attr("data-status", "expand");
			$(this).html("expand");
			$(this).next().removeClass("hidden");
		} else if(status == 'expand') {
			$(this).attr("data-status", "collapse");
			$(this).html("collapse");
			$(this).next().addClass("hidden");
		}
	});
});