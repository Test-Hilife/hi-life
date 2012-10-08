<table border="0" width="100%">
    <?php if(count($result) > 0): ?>
        <tr>
            <td><b>ID</b></td>
            <td><b>Имя Фамилия</b></td>
            <td><b>Дата регистрации</b></td>
            <td><b>Емайл</b></td>
            <td><b>Телефон</b></td>
            <td><b>Класс</b></td>
            <td><b>Статус</b></td>
        </tr>
        <?php foreach($result AS $row): ?>
            <?php
                switch ($row->type){
                    case 'client':
                        $class = $this->lang->line('client');
                    break;
                    case 'postav':
                        $class = $this->lang->line('postav');
                    break;
                    case 'moderator':
                        $class = $this->lang->line('moderator');
                    break;
                    default:
                        $class = $this->lang->line('admin');
                    break;
                }
                switch ($row->status){
                    case 'active':
                        $status = $this->lang->line('active');
                    break;
                    case 'banned':
                        $status = $this->lang->line('banned');
                    break;
                    default:
                        $status = $this->lang->line('not_active');
                    break;
                }
            ?>
            <tr>
                <td><?php echo $row->id;?></td>
                <td><?php echo $row->username;?></td>
                <td><?php echo $row->added;?></td>
                <td><?php echo $row->email;?></td>
                <td><?php echo $row->phone;?></td>
                <td><?php echo $class;?></td>
                <td><?php echo $status;?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?php $this->load->view( $this->config->item('template_dir') . 'div_error', array('text' => $this->lang->line('not_requests'))) ?>
    <?php endif; ?>
</table>
