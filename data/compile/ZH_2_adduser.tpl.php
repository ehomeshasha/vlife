<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="#">OA管理</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?>">添加新用户</a></li>
</ul>
<form action="index.php?home=login&amp;act=register" method="post" id="form">
<p>用户名：</p>
<input type="text" name="username" maxlength="32" value="" />
<p>密码：</p>
<input type="password" name="password" maxlength="32" value="" />
<p>用户级别:</p>
<select name="userlevel">
<option value="1">网编</option>
<option value="0">技术</option>
<option value="2">总编</option>
<option value="9">管理员</option>
<option value="11">实习生</option>
</select>
<div>
<button class="btn btn-primary" type="submit">提交</button>
</div>
</form>
</div>
</div>
</div>
</section><? include template('footer', '0', ''); ?>