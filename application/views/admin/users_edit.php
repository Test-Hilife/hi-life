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
    <option value="0" <?=($result->type == 0 ? 'selected':''); ?> >Клиент</option>
    <option value="1" <?=($result->type == 1 ? 'selected':''); ?> >Поставщик</option>
    <option value="2" <?=($result->type == 2 ? 'selected':''); ?> >Модератор</option>
    <option value="3" <?=($result->type == 3 ? 'selected':''); ?> >Администратор</option>
</select>

<?php echo form_error('date_unban'); ?>
<h5>Забанить пользователя на</h5>
<select name="date_unban">
    <option value="0">Выберите</option>
    <option value="1">1 день</option>
    <option value="3">3 дня</option>
    <option value="7">7 дней</option>
    <option value="14">14 дней</option>
    <option value="30">30 дней</option>
</select>

<?php echo form_error('status'); ?>
<h5>Статус пользователя</h5>
<select name="status">
    <option value="active" <?=($result->status == 'active' ? 'selected':''); ?> >Активен</option>
    <option value="banned" <?=($result->status == 'banned' ? 'selected':''); ?> >Забанить</option>
    <option value="not_active" <?=($result->status == 'not_active' ? 'selected':''); ?> >Выключить</option>
</select>

<div><input type="submit" value="Редактировать" /></div>

</form>
