<?php

class Model {

    function __construct() {
        $this->log = new Logs();

    }


    function ParseRequest($xml_post,$level=false) {
        $standard_array = $this->stan->ParseXMLRequest($xml_post,$level);
        return $standard_array;
    }





    function PostTransaction($vndr, $data) {
        $now = date('Y-m-d H:i:s');
        //Compute Fees:

  $this->log->LogToFile($vndr, "Model::PostTransaction FC for Transaction ".var_export($data, true), 2, 3);
        $billing_rules = $this->BillingRules($vndr, $data['service_id'], $data['pay_serv_id']);
        //$trans_fees = $this->fees->ComputeTransactionFees($billing_rules, $data['transaction_amount']);
	        $trans_fees['pay_serv_fee'] = 0;
	        $trans_fees['aggreg_fee'] = 0;
        $postData = array();

            $postData['transaction_date'] = $now;
            $postData['service_id'] = $data['service_id'];
            $postData['pay_serv_id'] = $data['pay_serv_id'];
            $postData['pay_serv_account'] = $data['pay_serv_account'];
            $postData['pay_account_ref'] = $data['pay_account_ref'];
		     	if(isset($data['merchant_trans_ref'])&&!empty($data['merchant_trans_ref'])){
            $postData['merchant_trans_ref'] = $data['merchant_trans_ref'];
			     }
          if(isset($data['request'])){
            $postData['request_type'] = $data['request'];
		      	}
          if(isset($data['merchant_user_id'])){
            $postData['merchant_user_id'] = $data['merchant_user_id'];
		      	}
		     	if(isset($data['pay_serv_trans_ref'])&&!empty($data['pay_serv_trans_ref'])){
            $postData['pay_serv_trans_ref'] = $data['pay_serv_trans_ref'];
		     	}
		     	if(isset($data['pay_serv_pay_ref'])&&!empty($data['pay_serv_pay_ref'])){
            $postData['pay_serv_pay_ref'] = $data['pay_serv_pay_ref'];
		     	}
            $postData['transaction_amount'] = $data['transaction_amount'];
            $postData['pay_serv_fee'] = $trans_fees['pay_serv_fee'];
            $postData['aggreg_fee'] = $trans_fees['aggreg_fee'];
            $postData['pay_serv_resp_code'] = 1000;
            $postData['processing_response'] = 'PENDING';

    $this->log->LogToFile($vndr, "Model::PostTransaction FC for getting ready to post ".var_export($postData, true), 2, 3);
            //  print_r($postData);die();
        $trans_id = $this->db->InsertData("mvd_payment_transactions", $postData, 'transaction_id');
         //print_r($trans_id);die();
        return $trans_id;
    }


    function PaymentTransaction($id) {

        $res= $this->db->SelectData("SELECT * FROM mvd_payment_transactions t JOIN mvd_aggregated_services s ON
		t.service_id = s.service_id WHERE t.transaction_id='".$id."' ");

   return $res;
    }

    function VerifyPaymentReference($vndr, $id) {
        $res = $this->db->SelectData("SELECT * FROM mvd_payment_transactions WHERE pay_serv_trans_ref=:pf", array('pf' => $id));
        $this->log->LogToFile($vndr, "Verifying Payment Refeance for  " . $id . "Returning Result " . var_export($res, true), 2, 1);
        return $res;
    }


    function VerifyMerchantReference($vndr, $id) {
        $res = $this->db->SelectData("SELECT * FROM mvd_payment_transactions WHERE merchant_trans_ref=:mf", array('mf' => $id));
        $this->log->LogToFile($vndr, "Verifying Payment Refeance for  " . $id . "Returning Result " . var_export($res, true), 2, 1);
        return $res;
    }



   function ProcessingRules($vndr, $serv, $psid, $pt, $rt) {

        $result = $this->db->SelectData("SELECT * FROM mvd_aggreg_service_parameters WHERE service_id=:sv
                AND pay_serv_id=:psid AND parameter_type=:pt AND request_type=:rt" ,
                array('sv' => $serv, 'psid' => $psid, 'pt' => $pt, 'rt' => $rt));

        $this->log->LogToFile($vndr, "Model::ProcessingRules For Service: " . $serv . " On Platform " . $psid . " And Parameter Type " . $pt . " And Request Type " . $rt . "  Found As " . var_export($result, true), 2, 2);

        return $result;
    }

    function BillingRules($vndr, $sid, $psid) {
        $result = $this->db->SelectData("SELECT * FROM mvd_service_billing_rules WHERE serv_id=:sv
                AND pay_serv_id=:psid", array('sv' => $sid, 'psid' => $psid));
        $this->log->LogToFile($vndr, "Model::BillingRules For Service: " . $sid . " On Platform " . $psid . "  Found As " . var_export($result, true), 2, 2);
        return $result;
    }

     function GetResponseKeySet($vndr, $pspid, $sid, $rid) {
        $this->log->LogToFile($vndr, "Model::GetResponseKeySet :Called for PSP " . $pspid . " Service " . $sid . " And Resp Code " . $rid, 2, 2);
        $result = $this->db->SelectData("SELECT * FROM mvd_service_responses WHERE
                    pay_serv_id =:pspid AND service_id=:sid AND merch_resp_code=:src", array('pspid' => $pspid, 'sid' => $sid, 'src' => $rid));

        if (count($result) == 0) {
            $stfl = 190;
            $option = $this->db->SelectData("SELECT * FROM mvd_service_responses WHERE
                    pay_serv_id =:pspid AND service_id=:sid AND merch_resp_code=:src", array('pspid' => $pspid, 'sid' => $sid, 'src' => $stfl));
            return $option;
        } else {
            return $result;
        }
    }

     function GetOpcoResponseKeySet($vndr, $pspid, $sid, $rid) {

        $this->log->LogToFile($vndr, "GetOpcoResponseKeySet :Called for PSP " . $pspid . " Service " . $sid . " And Resp Code " . $rid, 2, 2);
        $result = $this->db->SelectData("SELECT * FROM mvd_service_responses WHERE
                    pay_serv_id =:pspid AND service_id=:sid AND pay_serv_resp_code=:src", array('pspid' => $pspid, 'sid' => $sid, 'src' => $rid));
        if (count($result) == 0) {
            $stfl = 190;
            $option = $this->db->SelectData("SELECT * FROM mvd_service_responses WHERE
                    pay_serv_id =:pspid AND service_id=:sid AND pay_serv_resp_code=:src", array('pspid' => $pspid, 'sid' => $sid, 'src' => $stfl));
            return $option;
        } else {
            return $result;
        }
    }

    function CloseTransaction($vndr, $merc_resp, $trans_array,$indicator2=false) {
        $this->log->LogToFile($vndr, "CloseTransaction :Transaction Picked For Updating and Closing \n"
                . var_export($trans_array, true) . " With Response Array " . var_export($merc_resp, true), 2, $indicator2);
        $now = date('Y-m-d H:i:s');
        if ($merc_resp['aggreg_resp_code'] == '100') {
            $trans_state = 'Completed';
            //Update Account Balance:

            $this->UpdateAccountBalance('credit', $trans_array[0]['service_id'], $trans_array[0]['transaction_amount']);
        } elseif ($merc_resp['aggreg_resp_code'] == '190') {
            $trans_state = 'Rolled Back';
        } elseif ($merc_resp['aggreg_resp_code'] == '1000') {
            $trans_state = 'Pending';
        } else {
            $trans_state = 'Failed';
        }
   // print_r($merc_resp);die();
         $postData = array();
		    if(isset($merc_resp['merch_pay_ref'])){
         $postData['merchant_trans_ref'] = $merc_resp['merch_pay_ref'];
		    }
         if(isset($merc_resp['pay_serv_trans_ref'])){
		     $postData['pay_serv_trans_ref'] = $merc_resp['pay_serv_trans_ref'];
	    	 }
         $postData['transaction_status'] = $trans_state;
		    if(isset($merc_resp['merchant_resp_code'])){
	         $postData['merchant_resp_code'] = $merc_resp['merchant_resp_code'];
		     }
         $postData['pay_serv_resp_code'] = $merc_resp['pay_serv_resp_code'];
         $postData['processing_response'] = $merc_resp['aggreg_resp_message'];
         $postData['date_closed'] = $now;
        // print_r($trans_array);die();
        $this->db->UpdateData('mvd_payment_transactions', $postData, "transaction_id = {$trans_array[0]['transaction_id']}");
    }

    function RecordTransactionFile($type, $trans_id, $xml_file_name) {
        //Find If Transaction Record Already Exists
        $res = $this->db->SelectData("SELECT * FROM sm_log_file_index WHERE transaction_id=:tid", array('tid' => $trans_id));
        if (count($res) > 0) {
            $postData = array(
                $type => $xml_file_name
            );
            $this->db->UpdateData('sm_log_file_index', $postData, "record_id = {$res[0]['record_id']}");
        } else {
            $today = date("Y-m-d H:i:s");
            $postData = array(
                'invent_date' => $today,
                'transaction_id' => $trans_id,
                $type => $xml_file_name
            );
            $this->db->InsertData('sm_log_file_index', $postData);
        }
    }

    function GetTransactionLogFileIndex($tid) {
        return $this->db->SelectData("SELECT * FROM sm_log_file_index WHERE transaction_id=:tid", array('tid' => $tid));
    }

    function UpdateAccountBalance($type, $sid, $amt){
        $result = $this->db->SelectData("SELECT * FROM mvd_aggregated_services WHERE service_id =:sid", array('sid'=>$sid));
        if($type == 'credit'){
            $balance = $result[0]['account_balance']+$amt;
        }
        else{
            $balance = $result[0]['account_balance']-$amt;
        }
        $postData = array(
            'account_balance'=>$balance
        );
        $this->db->UpdateData('mvd_aggregated_services', $postData, "service_id = {$sid}");
    }

    function GetRequestedService($id) {

        $res= $this->db->SelectData("SELECT * FROM mvd_payment_transactions t JOIN mvd_aggregated_services s ON
		t.service_id = s.service_id WHERE t.transaction_id='".$id."' ");

   return $res;
    }

    function VerifyAPIKey($vendor, $user_id, $api_key) {

        $this->log->LogToFile($vendor, "VerifyAPIKey: Called With UID ". $user_id. " AND API Key ". $api_key, 2, 1);
        $return = $this->db->SelectData("SELECT * FROM sm_vendor_api_keys
            WHERE vendor =:vndr AND user_id=:uid AND api_key=:scn and status='Active'", array('vndr' => $vendor, 'scn' => $api_key, 'uid' => $user_id));

        if (!empty($return)) {
            return true;
        } else {
            return false;
        }
    }



}

?>
