<form name="request_admin" method='post'>
    
    <?php echo form_error('subject');?>
    <h5>Тема<font color="red">*</font></h5>
    <input type="text" name="subject" size="40">
    
    <?php echo form_error('name');?>
    <h5>Имя фамилия</h5>
    <input type="text" name="name" size="40">    

    <?php echo form_error('phone');?>
    <h5>Номер телефона</h5>
    <input type="text" name="phone" size="40">
    
    <?php echo form_error('email');?>
    <h5>Email</h5>
    <input type="text" name="email" size="40">
    
    <?php echo form_error('descr');?>
    <h5>Сообщение<font color="red">*</font></h5>
    <textarea name="descr" cols="32" rows="7"></textarea>
    
    <div><input type="submit" value="Отправить"></div>
</form>