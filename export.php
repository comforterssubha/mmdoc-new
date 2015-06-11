<?php 
require_once("/var/www/modoc/includes/configuration.php");
ini_set('display_errors', 1);
error_reporting(E_ALL);
//	$conn = mysql_connect($dbhost, $dbuname, $dbpass) OR DIE ("Not able to connect");
//	mysql_select_db($dbname);
	echo "Export Sync Start @".date("d.m.Y H:i:s")."\n";
	
	function mksync($table = "res_*", $table_from = "modoc_*", $hidefields = array(), $sayfields = false, $prefix = ""){
		$tquery = mysql_query("TRUNCATE ".$table."");
		if($table_from == 'modoc_samples')
		$fquery = mysql_query("SELECT * FROM ". $table_from . " WHERE sample_blood_testing_impossible !=1 and sample_code !='' and sample_test_id!=''");
		else
		$fquery = mysql_query("SELECT * FROM ". $table_from);
		echo $table_from.mysql_num_rows($fquery)."\n";
		$c = 0;
		echo $table_from." before while".date("d.m.Y H:i:s")."\n";
		while($row = mysql_fetch_array($fquery)){
			if($sayfields === true){
				$fields = "";
				for($i = 0; $i < count($hidefields); $i++){
					$fields .= "`".$prefix.$hidefields[$i]."` = '".mysql_real_escape_string($row[$hidefields[$i]])."', ";
				}
			}
			else{
				$fields = "";
				for($i = 0; $i < mysql_num_fields($fquery); $i++){
					$fieldname = mysql_field_name($fquery, $i);
					if(!in_array($fieldname, $hidefields)) $fields .= "`".$prefix.$fieldname."` = '".mysql_real_escape_string($row[$fieldname])."', ";
				}
			}
			if($table_from == "modoc_coc"){
				$squery = mysql_query("UPDATE `modoc_samples` SET `sample_coc_id` = '".$row['res_coc_id']."' WHERE `dynamic_coc_id` = '".$row['dynamic_coc_id']."'");	
			}
			$fields = substr($fields, 0 ,-2);
			$pquery = mysql_query("INSERT INTO `".$table."` SET ".$fields);
			
			$err = mysql_error();
			if($err != "") echo $err."\n";
			else $c++;
		}
		echo $table_from." after while".date("d.m.Y H:i:s")."\n";
		announe($table, $c);	
	}
	
	function announe($table, $c) {
		echo $table." was filled @".date("d.m.Y H:i:s")." (".$c." Rows)\n";
		$gkquery = mysql_query("UPDATE `modoc_res_gatekeeper` SET `res_given` = NOW() WHERE `res_type` = '".str_replace("modoc_","",$table)."'");
	}

	// Tests
	$tquery = mysql_query("TRUNCATE `modoc_res_tests`");
	$fquery = mysql_query("SELECT * FROM `modoc_tests`");
	echo "Total tests:".mysql_num_rows($fquery);
	$c = 0;
	while($row = mysql_fetch_array($fquery)){
		$sql = "INSERT INTO `modoc_res_tests` SET
			`res_test_id` = '".mysql_real_escape_string($row['test_id'])."',
			`res_test_version` = '".mysql_real_escape_string($row['test_version'])."',
			`res_test_status` = '".mysql_real_escape_string($row['test_status'])."',
			`res_test_dco_not` = '".mysql_real_escape_string($row['test_dco_not'])."',
			`res_test_dco_test` = '".mysql_real_escape_string($row['test_dco_test'])."',
			`res_test_bco` = '".mysql_real_escape_string($row['test_bco'])."',
			`res_test_created` = '".mysql_real_escape_string($row['test_created'])."',
			`res_test_updated` = '".mysql_real_escape_string($row['test_updated'])."',
			`res_test_not_stamp` = '".mysql_real_escape_string($row['test_not_stamp'])."',
			`res_test_arr_stamp` = '".mysql_real_escape_string($row['test_arr_stamp'])."',
			`res_test_final_stamp`= '".mysql_real_escape_string($row['test_final_stamp'])."',
			`res_test_1h_slot`= '".mysql_real_escape_string($row['test_1h_slot'])."',
			`res_test_ath_first`= '".mysql_real_escape_string($row['test_ath_first'])."',
			`res_test_ath_last`= '".mysql_real_escape_string($row['test_ath_last'])."',
			`res_test_ath_gender`= '".mysql_real_escape_string($row['test_ath_gender'])."',
			`res_test_dob`= '".mysql_real_escape_string($row['test_dob'])."',
			`res_test_ath_adr`= '".mysql_real_escape_string($row['test_ath_adr'])."',
			`res_test_ath_str`= '".mysql_real_escape_string($row['test_ath_str'])."',
			`res_test_ath_zip`= '".mysql_real_escape_string($row['test_ath_zip'])."',
			`res_test_ath_city`= '".mysql_real_escape_string($row['test_ath_city'])."',
			`res_test_ath_ctry`= '".mysql_real_escape_string($row['test_ath_ctry'])."',
			`res_test_event`= '".mysql_real_escape_string($row['test_event'])."',
			`res_test_ath_email`= '".mysql_real_escape_string($row['test_ath_email'])."',";
	
	// added reg Mantis tickets 1003		
		
		$sql .="`res_test_dateoftest` = '".mysql_real_escape_string($row['test_dateoftest'])."',
			`res_test_ath_nationality` = '".mysql_real_escape_string($row['test_ath_nationality'])."',
			`res_test_ath_dob` = '".mysql_real_escape_string($row['test_ath_dob'])."',
			`res_test_ath_tel` = '".mysql_real_escape_string($row['test_ath_tel'])."',
			`res_test_ath_id_prov` = '".mysql_real_escape_string($row['test_ath_id_prov'])."',
			`res_test_ath_id_type` = '".mysql_real_escape_string($row['test_ath_id_type'])."',
			`res_test_ath_id_number` = '".mysql_real_escape_string($row['test_ath_id_number'])."',
			`res_test_ath_pers_known` = '".mysql_real_escape_string($row['test_ath_pers_known'])."',
			`res_test_ath_third_party_id` = '".mysql_real_escape_string($row['test_ath_third_party_id'])."',
			`res_test_ath_third_party_name` = '".mysql_real_escape_string($row['test_ath_third_party_name'])."',
			`res_test_ath_third_party_doc_type`= '".mysql_real_escape_string($row['test_ath_third_party_doc_type'])."',
			`res_test_city`= '".mysql_real_escape_string($row['test_city'])."',
			`res_test_client_long`= '".mysql_real_escape_string($row['test_client_long'])."',
			`res_test_ctry`= '".mysql_real_escape_string($row['test_ctry'])."',
			`res_test_dco_not_name`= '".mysql_real_escape_string($row['test_dco_not_name'])."',
			`res_test_discipline`= '".mysql_real_escape_string($row['test_discipline'])."',
			`res_test_fed`= '".mysql_real_escape_string($row['test_fed'])."',
			`res_test_pool`= '".mysql_real_escape_string($row['test_pool'])."',
			`res_test_sport`= '".mysql_real_escape_string($row['test_sport'])."',
			`res_test_str`= '".mysql_real_escape_string($row['test_str'])."',
			`res_test_team`= '".mysql_real_escape_string($row['test_team'])."',
			`res_test_tmc`= '".mysql_real_escape_string($row['test_tmc'])."',
			`res_test_zip`= '".mysql_real_escape_string($row['test_zip'])."',
			`res_test_ath_rep_name`= '".mysql_real_escape_string($row['test_ath_rep_name'])."',
			`res_test_not_ath_first`= '".mysql_real_escape_string($row['test_not_ath_first'])."',
			`res_test_not_ath_last`= '".mysql_real_escape_string($row['test_not_ath_last'])."',
			`res_test_dco_name`= '".mysql_real_escape_string($row['test_dco_name'])."',
			`res_test_rep_underage_name`= '".mysql_real_escape_string($row['test_rep_underage_name'])."',
			`res_test_rep_underage_role`= '".mysql_real_escape_string($row['test_rep_underage_role'])."',
			`res_test_not_filename`= '".mysql_real_escape_string($row['test_not_filename'])."',
			`res_test_ath_other_id_type`= '".mysql_real_escape_string($row['test_ath_other_id_type'])."',
			`res_test_language`= '".mysql_real_escape_string($row['test_language'])."',
			`res_test_auth_agency`= '".mysql_real_escape_string($row['test_auth_agency'])."',
			`res_test_app_version`= '".mysql_real_escape_string($row['test_app_version'])."',
			`res_test_dco_test_selected`= '".mysql_real_escape_string($row['test_dco_test_selected'])."',";
			
		$curine = mysql_query("SELECT `sample_code` FROM `modoc_samples` WHERE `sample_test_id` = '".$row['test_id']."' AND `sample_type` = 1");
		if(mysql_num_rows($curine) > 0) $sql .= "`res_test_urine` = 1, ";
		else $sql .= "`res_test_urine` = 0,";
		
		$cblood = mysql_query("SELECT `sample_code` FROM `modoc_samples` WHERE `sample_test_id` = '".$row['test_id']."' AND `sample_type` = 2");
		if(mysql_num_rows($cblood) > 0) $sql .= "`res_test_blood` = 1, ";
		else $sql .= "`res_test_blood` = 0, ";
			// sample_is_partial
		
		/*$cpartial = mysql_query("SELECT `sample_code` FROM `modoc_samples` WHERE `sample_test_id` = '".$row['test_id']."' AND `sample_is_partial` IS NOT NULL");
		if(mysql_num_rows($cpartial) > 0) $sql .= "`res_test_partial` = 1, ";
		else $sql .= "`res_test_partial` = 0, ";*/
		
		$sql .= "`res_test_partial` = ".mysql_real_escape_string($row['test_partial']).",";
		
		$sql .= "`res_test_loc_city`= '".mysql_real_escape_string($row['test_loc_city'])."',
			`res_test_loc_ctry`= '".mysql_real_escape_string($row['test_loc_ctry'])."',
			`res_test_coach_first`= '".mysql_real_escape_string($row['test_coach_first'])."',
			`res_test_coach_last`= '".mysql_real_escape_string($row['test_coach_last'])."',
			`res_test_phys_first`= '".mysql_real_escape_string($row['test_phys_first'])."',
			`res_test_phys_last`= '".mysql_real_escape_string($row['test_phys_last'])."',
			`res_test_medication`= '".mysql_real_escape_string($row['test_medication'])."',
			`res_test_not_comments`= '".mysql_real_escape_string($row['test_not_comments'])."',
			`res_test_dco_comments`= '".mysql_real_escape_string($row['test_dco_comments'])."',
			`res_test_ath_comments`= '".mysql_real_escape_string($row['test_ath_comments'])."',
			`res_test_deleted`= ".mysql_real_escape_string($row['test_deleted']).",
			`res_test_client`= ".mysql_real_escape_string($row['test_client']).",
			`res_test_cancel_reason`= ".mysql_real_escape_string($row['test_cancel_reason']).",
			`res_test_cancel_rem`= '".mysql_real_escape_string($row['test_cancel_comment'])."',
			`res_test_dcf_filename`= '".mysql_real_escape_string($row['test_dcf_filename'])."',
			`res_test_research`= ".(int)$row['test_research']."";

		$pquery = mysql_query($sql);
		$err = mysql_error();
		if($err != "") echo $err."\n";
		else $c++;
	}
	
	announe("modoc_res_tests", $c);
	
	mksync('modoc_res_coc', 'modoc_coc', array('dynamic_coc_id', 'last_sync_date', 'id'), false, "");		// CoC
	mksync('modoc_res_samples', 'modoc_samples', array(
		'sample_test_id',
		'sample_code',
		'sample_coc_id',
		'sample_dco',
		'sample_type',
		'sample_blood_type',
		'sample_is_partial',
		'sample_stamp'), true, "res_test_");
		
	
	echo "Export Sync End @".date("d.m.Y H:i:s")."\n";
//	mysql_close($conn);
?>