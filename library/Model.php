<?php

class Model {

    function __construct() {
        $this->log = new Logs();
        $this->db = new Databaseconfig();

    }



    function SaveTransactionData($data){
   //print_r($data);die();
      $trans_id = $this->db->InsertData("payments", $data, 'transaction_id');
      return $trans_id;
    }


    function UpdateCustomerBalance($rec_id,$update_data) {
          // print_r($update_data);die();
      $this->db->UpdateData('customer_accounts', $update_data, "record_id = {$rec_id}");
    }

    function verifyTransaction($ref) {
          // print_r($update_data);die();
      return $this->db->SelectData("Select * from payments where external_id='".$ref."' ");
    }


    function GetTransaction($ref) {
          // print_r($update_data);die();
      return $this->db->SelectData("Select * from payments where transaction_id='".$ref."' ");
    }


    function GetRouting($ref,$type) {
          // print_r($update_data);die();
      return $this->db->SelectData("Select * from merchant_routing where merchant_ref='".$ref."' AND routing_type='".$type."' ");
    }


}

?>
