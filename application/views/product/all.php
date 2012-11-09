<?php foreach($row AS $product): ?>
    <div class="product_view_small">
        <div class="product_head_info">
            <div class="left"></div>
            <div class="right">
                <?=$product->name;?>
            </div>
        </div>
        <div class="product_info">
            <div class="poster">
                <img src="/uploads/images/product/<?=$product->image1;?>" width="200px" border="0">
            </div>
            <div class="info">
                <table align="right" width="800px" border="0">
                    <tr>
                        <td align="right">
                            <?php echo $product->small_text;?>
                        </td>
                        <td align="right" width="250px">
                            <div style="margin-right:10px;">
                                <img src="/theme/<?=$this->config->item('default_theme');?>/images/shekel.jpg" border="0">
                                <span class="price"><?=$product->price;?></span>
                            </div>
                            <div class="arrow_left">
                                <span style="margin-right:20px">
                                    <?=$this->lang->line('buy');?>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">
                            <table align="left">
                                <tr>
                                    <td align="center">
                                        <?=$this->lang->line('comments');?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <font style="font-size:20px;"><?=$this->lang->line('view');?></font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="center">
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <?=$this->lang->line('buyed');?>
                                    </td>
                                    <td align="center">
                                        <?=$this->lang->line('review_money');?>
                                    </td>
                                    <td align="center">
                                        <?=$this->lang->line('discount');?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <span class="red_text"><?=$product->buyed;?></span>
                                    </td>
                                    <td align="center">
                                        <img src="/theme/<?=$this->config->item('default_theme');?>/images/shekel.jpg" width="15px" border="0">
                                        <span class="red_text"><?=$product->price_review;?></span>
                                    </td>
                                    <td align="center">
                                        <span class="red_text"><?=$product->discount;?></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>
