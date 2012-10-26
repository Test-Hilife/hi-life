<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <title><?php
            echo $this->config->item('site_name') . 
            (@$title != '' ? $this->config->item('site_defis') . $title : ''); 
            ?>
    </title>
</head>
<body>
    <div>Всякое меню</div>