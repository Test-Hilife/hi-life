<table border="0" width="100%">
    <?php if(count($result) > 0): ?>
        <tr><td><b>Имя</b></td><td><b>Тема</b></td><td><b>Дата</b></td><td><b>Телефон</b></td></tr>
        <?php foreach($result AS $row): ?>
            <tr>
                <td><?php echo $row->name;?></td>
                <td><?php echo $row->subject;?></td>
                <td><?php echo $row->added;?></td>
                <td><?php echo $row->phone;?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?php $this->load->view( $this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_requests'))) ?>
    <?php endif; ?>
</table>

