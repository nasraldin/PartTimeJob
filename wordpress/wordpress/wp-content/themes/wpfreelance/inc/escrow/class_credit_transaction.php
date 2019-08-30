<?php
class Box_Transaction{
	public $id;
	public $project_id;
	public $payer_id; // payer for this transaction.
	public $receiver_id; // receiver for this transaction.
	public $emp_pay;
	public $fre_receive;
	public $status;
	public $date_create;
	public $checkout_mode;
	public $is_realmode;
	static $instance;

	function __construct ($trans_id){

		$this->id = $trans_id;
		if( $this->id ) {
			$trans = get_post($trans_id);
			$this->status = $trans->post_status;
			$this->status = $trans->post_status;
			$this->total = get_post_meta($trans_id, 'total', true);
			$this->payer_id = get_post_meta($trans_id, 'payer_id', true);
			$this->receiver_id = get_post_meta($trans_id, 'receiver_id', true);
			$this->emp_pay = get_post_meta($trans_id, 'emp_pay', true);
			$this->fre_receive = get_post_meta($trans_id, 'fre_receive', true);
			$this->commision_fee = get_post_meta($trans_id, 'commision_fee', true);
			$this->project_id = $trans->post_parent;
			$this->date_create = $trans->post_date;
		}
		global $checkout_mode;
		$this->is_realmode = (int) $checkout_mode;
	}
	static function get_instance( $trans_id = 0){

		if( self::$instance == null){
			self::$instance =  new static($trans_id);
		}
		return self::$instance;
	}
	function create($args, $project){
		$default = array(
			'post_title' => 'Transaction of project '.$project->post_title,
			'post_type' => 'transaction',
			'post_status' => 'pending',
			'post_parent' => $project->ID,
			'meta_input' =>
				array(
					'total' => $args['total'],
					'emp_pay' => $args['emp_pay'],
					'payer_id' => $args['payer_id'],
					'receiver_id' => $args['receiver_id'],
					'fre_receive' => $args['fre_receive'], // number amount will be tranfer to ballance.
					'commision_fee' => $args['commision_fee'],
					'user_pay' => $args['user_pay'], // fre - emp or 50/50,
					'is_realmode' => $this->is_realmode,
				),
		);

		$id = wp_insert_post( $default );

		if( ! is_wp_error( $id ) )
			$this->id = $id;
		return $this;
	}
	function update_status($trans_id, $status ){
		return wp_update_post(
			array(
				'ID' => $trans_id,
				'post_status' => $status,
			)
		);
	}
	function delete(){
		wp_delete_post( $this->id, true);
	}
	function release($id){
		$this->update_status('publish');
		// update total_spent, erned here.
	}

	function refund($trans_id){
		$this->update_status('');
	}
	function get_transaction($trans_id){
		$this->id = $trans_id;

		$transaction = get_post($trans_id);
		$transaction->project_id = $transaction->post_parent;
		$transaction->date_create = $transaction->post_date;
		$transaction->id = $trans_id;

		$transaction->total = (float) get_post_meta($trans_id, 'total', true);
		$transaction->emp_pay = (float) get_post_meta($trans_id, 'emp_pay', true);
		$transaction->payer_id = get_post_meta($trans_id, 'payer_id', true);

		$transaction->receiver_id = get_post_meta($trans_id, 'receiver_id', true);

		$transaction->fre_receive = (float) get_post_meta($trans_id, 'fre_receive', true);
		$transaction->commision_fee = (float) get_post_meta($trans_id, 'commision_fee', true);
		$transaction->is_realmode = get_post_meta($trans_id, 'is_realmode', true);

		return $transaction;
	}

}
Class Box_Transaction_Backend {
	protected $trans;
	function __construct(){
		$this->trans = 0;
		add_action('edit_form_after_editor', array($this,'show_detail_transaction') );
		add_action('pre_post_update', array( $this, 'disable_publish_in_admin'), 10 ,2  );
	}
	function disable_publish_in_admin($trans_id, $post_data){
		if( isset( $post_data['post_type'] ) && $post_data['post_type'] == 'transaction'){
			wp_die('This action is disabled.');
		}
	}
	function show_detail_transaction($post){
		if($post->post_type == 'transaction' ){
			$this->trans = Box_Transaction::get_instance( $post->ID );
			echo '<div class="trans-detail">';
			?>
			<h1> Detail of transaction </h1>
			<div class="row">Transaction ID :<?php the_ID();?></div>
			<?php
			$project = get_post($this->trans->project_id);
			echo '<div class="row"> Deated Create : '.date('M d, Y', strtotime( $this->trans->date_create ) ) .'</div>';
			echo '<div class="row"> Payer ID(Employer ID): '.$this->trans->payer_id .'</div>';
			echo '<div class="row"> Freelancer ID: '.$this->trans->receiver_id.'</div>';
			echo '<div class="row"> Employer Pay: '.$this->trans->emp_pay.'</div>';
			echo '<div class="row"> Freelancer Receive : '.$this->trans->fre_receive.'</div>';
			if( $project ){
				echo '<div class="row"> Project : <a target="_blank" href="'.get_permalink($project->ID).'">'.$project->post_title.'</a></div>';
			}

			echo '</div>';
			?>
			<style type="text/css">
				.trans-detail{
					background-color: #fff;
					padding: 30px;
				}
				.trans-detail .row{
					display: block;
					padding-bottom: 15px;
				}
				#side-sortables { display: none; }
				.wrap .trans-detail h1{
					font-size: 23px;
					padding-bottom: 30px;
				}
			</style>
			<?php
		}
	}
}
if( is_admin() && ! wp_doing_ajax() ) {
	new Box_Transaction_Backend();
}
?>