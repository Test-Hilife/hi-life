<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <!--meta tags-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
    <meta name="description" Content="<?php echo $this->siteModel->pageInfo['descr']; ?>">
    <meta name="keywords" Content="<?php echo $this->siteModel->pageInfo['keywords']; ?>">
    <!--css styles-->
    <link rel="stylesheet" type="text/css" href="<?=$this->config->item("site_url");?>theme/<?=$this->config->item("default_theme");?>/style.css">
    
    <title><?php echo $this->siteModel->pageInfo['title']; ?></title>
</head>
<body>
    <div class="body">
        
        <div class="head">
            <div class="logo">
                <a href="">
                    <img src="theme/<?=$this->config->item("default_theme");?>/images/plus.gif" border="0">
                </a>
            </div>
            <div class="user_bar">
                <?php if($this->siteModel->login == FALSE): ?>
                    <form name="auth" method="post" action="user/auth/false">
                        <input pattern="2-[0-9]{3}-[0-9]{3}" type="text" size="30" name="email" required placeholder="Email" /><br />
                        <input type="text" size="30" name="password" required placeholder="Password" /><br />
                        <center>
                            <input type="submit" value="<?=$this->lang->line("auth_button");?>" />
                            <a class="button" href="user/signup"><?=$this->lang->line("signup_button");?></a>
                        </center>
                    </form>
                <?php else: ?>
                    <b><?=$this->lang->line("hello_user");?>, </b><?=$this->siteModel->user->username;?><br /><br/>
                    <a class="button" href="user/logout"><?=$this->lang->line("logout_button");?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="search">
            <form name="search" method="post" action="product/search">
                <input type="text" size="50" name="search" />
                <input type="submit" value="<?=$this->lang->line("search_button");?>" />
            </form>
        </div>
        