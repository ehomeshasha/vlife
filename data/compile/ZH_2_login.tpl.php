<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('header', '0', ''); ?><style type="text/css">
#username, #password {float: left; margin-right: 10px;}
</style>
<section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<form action="index.php?home=login&amp;act=login" method="post">
<input type="text" name="username" id="username" value="" placeholder="用户名" />
<input type="password" name="password" id="password" value="" placeholder="密码" />
<button type="submit" class="btn btn-primary" id="login_btn">登录</button>
</form>
</div>
</div>
</section><? include template('footer', '0', ''); ?>