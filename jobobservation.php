<?php

if($_POST)

{

   /* if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

       

        $output = json_encode(array( //create JSON data

            'type'=>'error',

            'text' => 'Sorry Request must be Ajax POST'

        ));

        die($output); //exit script outputting json data

    }*/

	 include('database.php');

	 $query="INSERT INTO job_observation";

	 $col="(";

	 $col_value="VALUES (";

	$info="CREATE TABLE job_observation (

id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,";



$s="firstname VARCHAR(30) NOT NULL,

lastname VARCHAR(30) NOT NULL,

email VARCHAR(50),

reg_date TIMESTAMP

) ";

$target_dir = "uploads/";

$target_file = $target_dir . basename($_FILES["Upload_Photo"]["name"]);

$uploadOk = 1;

$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

move_uploaded_file($_FILES["Upload_Photo"]["tmp_name"], $target_file);

$message="";

	foreach($_POST as $key=>$val){

		if($key=='S_Issues') continue;

		 if(is_array($val)){

			 $val2=$val;
 
			 $val="";

			for($i=0;$i<count($val2);$i++){

			$val.=$val2[$i];

				if($i<count($val2)-1)$val.=",";

			}

			}
 
			$key=str_replace('?',"",$key);

			$info.=$key." VARCHAR(200),\r\n";

			$col.=$key.",";

			$col_value.="'".$val."',";

			$message.=$key.":--".$val."\r\n";

		 }

		 $col.=")";

		 $col=str_replace(',)',")",$col);

		 $col_value.=")";

		 $col_value=str_replace(',)',")",$col_value);

		 $info.=")";

		$query.=$col.$col_value;

	

	//file_put_contents('obslog.txt',$query);

	mysql_query($query) or die(json_encode(array('type'=>'message', 'text' =>'Error saving data'.mysql_error() )));

	if(!@mail($email,"Job Observation",$message)) die(json_encode(array('type'=>'message', 'text' =>'Error sending email' )));

	$output = json_encode(array('type'=>'message', 'text' =>'Thanks for submitting form' ));

        die($output);

}

?>