<form name="edit_user" method="post">

<?php echo form_error('email'); ?>
<h5>Email</h5>
<input type="text" name="email" value="<?php echo $result->email; ?>" size="50" />    

<?php echo form_error('name'); ?>
<h5>Имя Фамилия</h5>
<input type="text" name="name" value="<?php echo $result->username; ?>" size="50" />  

<?php echo form_error('password'); ?>
<h5>Пароль</h5>
<input type="password" name="password" size="50" />

<?php echo form_error('phone'); ?>
<h5>Номер телефона</h5>
<input type="text" name="phone" value="<?php echo $result->phone;?>" size="50" />

<?php echo form_error('class'); ?>
<h5>Класс пользователя</h5>
<select name="class">
    <option value="client" <?=($result->type == 'client' ? 'selected':''); ?> >Клиент</option>
    <option value="postav" <?=($result->type == 'postav' ? 'selected':''); ?> >Поставщик</option>
    <option value="moderator" <?=($result->type == 'moderator' ? 'selected':''); ?> >Модератор</option>
</select>

<?php echo form_error('class'); ?>
<h5>Класс пользователя</h5>
<select name="status">
    <option value="active" <?=($result->status == 'active' ? 'selected':''); ?> >Активен</option>
    <option value="banned" <?=($result->status == 'banned' ? 'selected':''); ?> >Забанить</option>
    <option value="not_active" <?=($result->status == 'not_active' ? 'selected':''); ?> >Выключить</option>
</select>

<div><input type="submit" value="Редактировать" /></div>

</form>
