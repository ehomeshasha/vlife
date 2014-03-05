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
	$("#form").submit(function(){
		if(controller == 'businesslog' && (action == 'post' || action == 'edit')) {
			var judge = true;
			var textname;
			$(".big_textarea").each(function(){
				if($(this).val().length > parseInt($(this).attr("maxlen"))) {
					judge = false;
					textname = $(this).parent().parent().prev().find('a').html();
					$(this).tooltip({
						trigger:'manual',
						title:textname+'的字数超出限制',
						placement:'bottom',
					});
					$(this).tooltip('show');
					$(this).focus();
					return false;
				}
			}) ;
			if(judge == false) {
				setTimeout(function(){$('.big_textarea').tooltip('hide');},2000);
				return false;
			}
		}
		var data = $(this).serialize();
		var url = $(this).attr('action');
		$("#jumpurl").val("");
		var icon;
		$.ajax({
			url:url,
			type:'POST',
			contentType:'application/x-www-form-urlencoded; charset=utf-8',
			dataType:'json',
			data:data,
			error:function(){alert('操作失败，请重新尝试');return false;},
			success:function(data){
				if(data.code == '-1') {
					icon = "<i class='icon-remove-sign' style='margin-right:5px;'></i>";
				} else if(data.code == '0') {
					icon = "<i class='icon-info-sign' style='margin-right:5px;'></i>";
				} else if(data.code == '1') {
					icon = "<i class='icon-ok-sign' style='margin-right:5px;'></i>";
				}
				$("#jumpurl").val(data.url);
				$('#messagebox_body').html(icon + data.message);
				$('#messagebox').modal('show');
				return false;
			}
		});
		return false;
	});
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
	$(document).on("click", ".close_thread", function() {
		$(this).parent().remove();
	});
	$(".add_opt").click(function(){
		var html = $("#threadblock").html();
		$("#thread_body").append("<div class='well form-horizontal threadblock'><button class='close close_thread' type='button'>&times;</button>"+html+"</div>");
	});
	$(".search_btn").click(function(){
		$(".para_for_pagejump").val("");
	});
	$(".view_bulletin").click(function(){
		var obj = $(this).parent().next();
		if(obj.css("display") == "none") {
			obj.fadeIn();
		}
	});
	$(".hide_bulletin").click(function(){
		$(this).parent().fadeOut();
	});
	
	$('.username_popover').popover('show');
	$(document).on("keyup", ".input_check", function() {
		var len = $(this).val().length;
		var maxlen = $(this).attr("maxlen");
		var leftnum = maxlen - len;
		var box = $(this).parent().parent();
		box.find('.inputnum').html(len);
		box.find('.leftnum').html(leftnum);
		if(len > maxlen) {
			box.find('.wordview').html("超出");
		} else {
			box.find('.wordview').html("还可以输入");
		}
	});
	$(".view_iframe").click(function(){
		var id = $(this).attr("id").substr(12);
		addviews(id);
		$("#response_modal").addClass("iframe-modal");
	});
	$(".view_linkpage").click(function(){
		var id = $(this).attr("id").substr(14);
		addviews(id);
		location.href = $(this).attr("href-attr");
	})
	$(".change_threadtype").change(function(){
		var threadtype = $(this).val();
		var tid = $(this).parent().parent().find(".tid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=thread&act=change_threadtype",
			type:'POST',
			data:{tid:tid,threadtype:threadtype},
			dataType: 'json',
			error:function(){alert('操作失败，请重新尝试');return false;},
			success:function(data){
				obj.attr("data-title", data.msg);
				var score_obj = obj.parent().siblings().find('.score_thread');
				var html = "";
				if(data.isscore == 1) {
					score_obj.addClass('bg-green');
					html = data.html + "<option value='-1'>取消评分</option>";
				} else {
					html = "<option value=''>评分</option>" + data.html;
				}
				score_obj.html(html)
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".score_thread").change(function(){
		var score = $(this).val();
		var tid = $(this).parent().parent().find(".tid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=thread&act=score",
			type:'POST',
			data:{tid:tid,score:score},
			error:function(){alert('操作失败，请重新尝试');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".score_log").change(function(){
		var score = $(this).val();
		var uid = $(this).parent().parent().find(".uid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=memberlog&act=score_log",
			type:'POST',
			data:{uid:uid,score:score},
			error:function(){alert('操作失败，请重新尝试log');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".score_team").change(function(){
		var score = $(this).val();
		var uid = $(this).parent().parent().find(".uid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=memberlog&act=score_team",
			type:'POST',
			data:{uid:uid,score:score},
			error:function(){alert('操作失败，请重新尝试team');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".score_innovate").change(function(){
		var score = $(this).val();
		var uid = $(this).parent().parent().find(".uid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=memberlog&act=score_innovate",
			type:'POST',
			data:{uid:uid,score:score},
			error:function(){alert('操作失败，请重新尝试innovate');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".score_txt").change(function(){
		var score = $(this).val();
		var uid = $(this).parent().parent().find(".uid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=memberlog&act=score_txt",
			type:'POST',
			data:{uid:uid,score:score},
			error:function(){alert('操作失败，请重新尝试innovate');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".adm_ul").change(function(){
		var score = $(this).val();
		var uid = $(this).parent().parent().find(".uid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=admusers&act=adm_ul",
			type:'POST',
			data:{uid:uid,score:score},
			error:function(){alert('操作失败，请重新尝试innovate');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".adm_dt").change(function(){
		var score = $(this).val();
		var uid = $(this).parent().parent().find(".uid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=admusers&act=adm_dt",
			type:'POST',
			data:{uid:uid,score:score},
			error:function(){alert('操作失败，请重新尝试innovate');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".adm_pm").change(function(){
		var score = $(this).val();
		var uid = $(this).parent().parent().find(".uid").html();
		var obj = $(this);
		$.ajax({
			url: "index.php?home=admusers&act=adm_pm",
			type:'POST',
			data:{uid:uid,score:score},
			error:function(){alert('操作失败，请重新尝试innovate');return false;},
			success:function(data){
				obj.attr("data-title", data);
				obj.addClass('bg-green');
				obj.tooltip('show');
				setTimeout(function(){obj.tooltip('hide');},1000);
				return false;
			}
		});
	});
	$(".check_all").click(function(){
		var obj = $('.check_tid');
		if($(this).prop('checked')) {
			obj.prop('checked', true);
		} else {
			obj.prop('checked', false);
		}
	});
	$(".username").click(function(){
		var id = $(this).attr("id").substr(4);
		$(".author-input").val(id);
		$("#search_form").submit();
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
function setSendlistValue() {
	$(".threadblock").each(function(){
		var val = "";
		$(this).find('input[type=checkbox]:checked').each(function(){
			val += "," + $(this).val();
		});
		$(this).find('.sendlist_str').val(val.substring(1));
	});
}
function addviews(id) {
	$.post('index.php?home=misc&act=addviews', {id:id});
}