<?php

$coupe = coupe();

?>

<h2>Home</h2>

<p>Login</p>

<div id="div_login">

<form action="<?php echo $coupe->url('phpcoupe/auth/login'); ?>" method="POST">

<label>Login:<br /><input name="login" type="text" /></label><br />

<label>Senha:<br /><input name="pass" type="password" /></label><br />

<label>Grupo:<br /><input name="group" type="text" /></label><br />

<div class="button">

<input type="submit" value="Acessar" name="auth_submit" />

</div>

</form>

</div>

<div>Ainda não possui uma página? <a href="<?php echo $coupe->url('/user/register'); ?>">Cadastre-se aqui.</a></div>