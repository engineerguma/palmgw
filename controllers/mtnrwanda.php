<?php


class Mtnrwanda extends Controller {

    function __construct() {
        parent::__construct();

    }

    function Index(){
      $general=array('status'=>403,
                     'message'=>'Forbidden');
        header('Content-Type: application/json;charset=utf-8"');
        echo json_encode($general,true);
        exit();
    }

    function CompleteDebitRequest($req=false){

        $service_id = 2741;
        $xml_post = file_get_contents('php://input');
        $log_file_name = $this->model->log->LogXML('req_from_opco', 'MTNMoMo','DSTV', $xml_post);
        $this->model->ProcessThirdPartyPayment($service_id, $log_file_name);
    }






}
