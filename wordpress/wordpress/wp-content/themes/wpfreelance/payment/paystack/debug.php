<?php
function check_mobipay_debug(){
	$t = box_get_paystack('paystack');
	echo '<pre>';
	var_dump($t);
	$order_id = '999';

	$path1 = MOBILPAY_URL.'/bplog.txt'	;
	$path2 = plugin_dir_url('sn-wc-mobilpay_').'/sn-wc-mobilpay_/bplog.txt';

	echo '</pre>';
	echo '<a href="'.$path1.'" target="_blank">Fre Log </a> &nbsp; ';
	echo '<a href="'.$path2.'" target="_blank"> Woo log</a>';
	$member_log_file = MOBILPAY_URL.'/track.txt';
	echo '<a href="'.$member_log_file.'" target="_blank"> Track</a>';


	?>
	Test card: <strong> 99110059532258</strong><br />
	CVC: 111
	<a href="https://suport.mobilpay.ro/index.php?/Knowledgebase/Article/View/57/12/carduri-de-test">Test card </a>
	<?php
}
// add_action('wp_footer','check_mobipay_debug',9999);