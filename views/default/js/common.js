var digit_regex = /^\d+$/
function is_empty(obj) {
	if(typeof(redirect_url) == "undefined" || redirect_url = null || redirect_url == "") {
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
	
	$(".deletelink").click(function(){
		var id = jQuery(this).attr("data-id");
		var type = jQuery(this).attr("data-type");
		var redirect_url = jQuery(this).attr("data-redirect");
		var url = jQuery(this).attr("data-href");
		
		
		var confirm_msg = 'Are you sure to delete this '+type+'?(cannot be undone)';
		
		if(confirm(confirm_msg)) {
			jQuery.ajax({
				url: url,
	            type:'POST',
	            data: {id:id, type:type},
	            complete :function(){},
	            error: function() { alert('Please try again');},
	            success: function() {
	            	if(is_empty) {
	            		location.href = location.href;
	            	} else {
	            		location.href = redirect_url;
	            	}
	            }
			});
		}
	});
	$('.breadcrumb .addicon').tooltip();
	
	$(".messagebox_button").click(function(){
		$('#messagebox').modal('hide');
	});
	$('#messagebox').on('hidden', function () {
		var jumpurl = $("#jumpurl").val();
		if(jumpurl == "") {
			var url = location.href;
			var reg = /#[^&]$/;
			url = url.replace(reg, "");
			location.href = url;
		} else {
			location.href = jumpurl;
		}
	});
	$('.datepicker_m').datepicker({'format':'yyyy-mm'});
	$('.datepicker').datepicker({'format':'yyyy-mm-dd'});
	var nowTemp = new Date();
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	 
	var checkin = $('#startdate').datepicker({
	  onRender: function(date) {
	    return date.valueOf() < now.valueOf() ? 'disabled' : '';
	  }
	}).on('changeDate', function(ev) {
	  if (ev.date.valueOf() > checkout.date.valueOf()) {
	    var newDate = new Date(ev.date)
	    newDate.setDate(newDate.getDate() + 1);
	    checkout.setValue(newDate);
	  }
	  checkin.hide();
	  $('#enddate')[0].focus();
	}).data('datepicker');
	var checkout = $('#enddate').datepicker({
	  onRender: function(date) {
	    return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
	  }
	}).on('changeDate', function(ev) {
	  checkout.hide();
	}).data('datepicker');
	$('[data-toggle="modal"]').bind('click',function(e) {
		$("#response_modal").removeClass("iframe-modal");
		e.preventDefault();
		var url = $(this).attr('data-href');
		if (url.indexOf('#') == 0) {
			$('#response_modal').modal('open');
		} else {
			$.get(url, function(data) {
	                        $('#response_modal').html(data);
	                        $('#response_modal').modal();
			}).success(function() {
				$('#response_modal input:text:visible:first').focus();
			});
		}
	});
	$(document).on("click", ".clear_session", function() {
		$.post(site_url+'index.php?home=misc&act=clear_session');
	});
	/*
	$(document).on("click", ".close_thread", function() {
		$(this).parent().remove();
	});
	$(".add_opt").click(function(){
		var html = $("#threadblock").html();
		$("#thread_body").append("<div class='well form-horizontal threadblock'><button class='close close_thread' type='button'>&times;</button>"+html+"</div>");
	});*/
	$(".search_btn").click(function(){
		$(".para_for_pagejump").val("");
	});
	
	$(".check_all").click(function(){
		var obj = $('.check_tid');
		if($(this).prop('checked')) {
			obj.prop('checked', true);
		} else {
			obj.prop('checked', false);
		}
	});
});
function jumpto(url) {
	var form = $("#search_form");
	if(form.length > 0) {
		form.attr("action", url);
		form.submit();
	} else {
		location.href = url;
	}
}