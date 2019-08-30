<div class="heading-tab">
    <ul class="main-menu">
        <?php
        $main_page 		= admin_url('admin.php?page='.BOX_SETTING_SLUG);
        $escrow_link 	= add_query_arg('section','escrow', $main_page);
        $general_link 	= add_query_arg('section','general', $main_page);
        $install_link 	= add_query_arg('section','install', $main_page);
        $email_link 	= add_query_arg('section','email', $main_page);
        $payment_link 	= add_query_arg('section','currency_package', $main_page);
        $gateway_link 	= add_query_arg('section','payment_gateways', $main_page);
        $plugin_link   = add_query_arg('section','plugins', $main_page);
        $account   = add_query_arg('section','account', $main_page);
        ?>
        <li><a href="<?php echo $general_link;?>"><?php _e('General','boxtheme');?></a></li>
        <li><a href="<?php echo $account;?>"><?php _e('Account','boxtheme');?></a></li>
        <li><a href="<?php echo $payment_link;?>"><?php _e('Currency and Packages','boxtheme');?></a></li>
        <li><a href="<?php echo $gateway_link;?>"><?php _e('Payment Gateways','boxtheme');?></a></li>
        <li><a href="<?php echo $escrow_link;?>"><?php _e('Escrow','boxtheme');?></a></li>
        <li><a href="<?php echo $email_link;?>"><?php _e('Email','boxtheme');?></a></li>
        <li><a href="<?php echo $install_link;?>"><?php _e('Install','boxtheme');?></a></li>
        <li><a href="<?php echo $plugin_link;?>"><?php _e('Plugins','boxtheme');?></a></li>
    </ul>
</div>
