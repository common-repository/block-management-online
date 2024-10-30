<?php 
	echo $usermessage;
?>
<form id="login" action="?action=login&key=<?php echo time(); ?>" method="post" name="login">

	<label for="accountid">Login</label>
	<p><input class="auth_email"  type="text" name="auth_email" value=""></p>
	
	<label for="password">Password</label>
	<p><input class="auth_password"  type="password" name="auth_password" value=""></p>
	
	<p><input type="submit" name="login" value="Sign in"></p>
	
	<input type="hidden" name="time" id="time" value="<?php echo time(); ?>">
	
</form>
