<?php
if($_POST)
{
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
       
        $output = json_encode(array( //create JSON data
            'type'=>'error',
            'text' => 'Sorry Request must be Ajax POST'
        ));
        die($output); //exit script outputting json data
    }
	 include('database.php');
	$info="CREATE TABLE job_observation (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,";

$s="firstname VARCHAR(30) NOT NULL,
lastname VARCHAR(30) NOT NULL,
email VARCHAR(50),
reg_date TIMESTAMP
) ";
	foreach($_POST as $key=>$val){
		
		 if(is_array($val)){
			 $val2=$val;
			 $val="";
			for($i=0;$i<count($val2);$i++){
			$val.=($val2[$i]!=""?1:0);
				if($i<count($val2)-1)$val.=",";
			}
			}
			$key=str_replace('?',"",$key);
			$info.=$key." VARCHAR(200),\r\n";
		 }
		 $info.=")";
		
	
	file_put_contents('obslog.txt',$info);
	$output = json_encode(array('type'=>'message', 'text' =>'Thanks for submitting form' ));
        die($output);
}
?>