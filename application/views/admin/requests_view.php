<table align="center" width="100%">
    <tr>
        <td align="right"><b>Тема</b></td> 
        <td align='left'><?php echo $result->subject;?></td>
    </tr>
    <tr>
        <td align="right"><b>Автор</b></td> 
        <td align='left'><?php echo $result->name;?></td>
    </tr>
    <tr>
        <td align="right"><b>Номер мобильного / E-Mail</b></td> 
        <td align='left'><?php echo ($result->phone ? $result->phone : 'Отсутствует') . ' / ' . ($result->email ? $result->email : 'Отсутствует');?></td>
    </tr>
    <tr>
        <td align="right"><b>Сообщение</b></td> 
        <td align='left'><?php echo $result->descr;?></td>
    </tr>
</table>