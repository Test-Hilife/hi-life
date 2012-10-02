
<form name="signup" method="post">

<?php echo form_error('email'); ?>
<h5>Email</h5>
<input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" />    

<?php echo form_error('name'); ?>
<h5>Имя Фамилия</h5>
<input type="text" name="name" value="<?php echo set_value('name'); ?>" size="50" />  

<?php echo form_error('password'); ?>
<h5>Пароль</h5>
<input type="password" name="password" size="50" />

<?php echo form_error('passconf'); ?>
<h5>Подтверждение пароля</h5>
<input type="password" name="passconf" size="50" />

<?php echo form_error('phone'); ?>
<h5>Номер телефона</h5>
<input type="text" name="phone" value="<?php echo set_value('phone');?>" size="50" />

<div><input type="submit" value="Отправить" /></div>

</form>