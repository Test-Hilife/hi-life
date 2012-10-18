<form name="request_in_postav" method="post">
    <h5>Название компании</h5>
    <input type="text" name="name" size="50">
    
    <h5>Фамилия Имя</h5>
    <?php echo $username;?>
    <h5>Email</h5>
    <?php echo $email;?>
    
    <h5>Описание компании</h5>
    <textarea cols="30" rows="5" name="descr"></textarea>
    <br />
    <input type="submit" value="Отправить">
</form>