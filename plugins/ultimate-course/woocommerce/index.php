<?php
require ( '../../../../wp-load.php');
if(isset($_REQUEST["Response"]) && $_REQUEST["Response"]=="000"){
		//print_r($_GET);
		
		global $woocommerce;
		
    	$order = new WC_Order( $_REQUEST["orderid"] );
		$order->payment_complete();
		
		$url = add_query_arg(
		array(
			'key' => $order->order_key,
			'order' => $_REQUEST["orderid"],
		)
		, get_permalink(886));
		update_post_meta($_REQUEST["orderid"],"response",$_REQUEST["Response"]);
		?>
		<script>
		window.top.location.href = "<?=$url?>"; 
		</script>
		<?php

	}
	else if(isset($_REQUEST["Response"]) && $_REQUEST["Response"]!=000){
		$order = new WC_Order( $_REQUEST["orderid"] );
		$order->update_status('failed');
		$url = add_query_arg(
		array(
			'key' => $order->order_key,
			'order' => $_REQUEST["orderid"],
		)
		, get_permalink(886));
		
		update_post_meta($_REQUEST["orderid"],"response",$_REQUEST["Response"]);
		?>
		<script>
		window.top.location.href = "<?=$url?>"; 
		</script>
		<?php
	}
?>