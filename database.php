<?php 

$mysql_server_name="localhost";

$mysql_user="morphcb_slcktls";

$mysql_pass="u8y-XpU-qEk-AV2";

$mysql_database_name="morphcb_slicktools";
$email="Jobs.reclaimit@telus.net";
 




$con=mysql_connect($mysql_server_name,$mysql_user,$mysql_pass) or die( $output = json_encode(array( //create JSON data

            'type'=>'message',

            'text' => 'Error!Can not connect to server'

        )));

		$rs=mysql_select_db($mysql_database_name) or die( $output = json_encode(array( //create JSON data

            'type'=>'message',

            'text' => 'Error!Can not connect to database'

        )));;

?>