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
        $xml_request = file_get_contents('php://input');
        $log_file_name = $this->model->log->LogRequest('req_from_mtn',$xml_request,1);
        $get_http_response_code ='HTTP Response code: 200';
        echo $get_http_response_code;

      //  echo '<ns0:debitcompletedresponse xmlns:ns0="http://www.ericsson.com/em/emm/callback/v1_0"/>';
        //$this->model->ProcessThirdPartyPayment($service_id, $log_file_name);
    }






}
