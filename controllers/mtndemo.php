<?php


class Mtndemo extends Controller {

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

    function ReceiveDebitRequest($req=false){
        $xml_post = file_get_contents('php://input');
        $log_file_name = $this->model->log->LogRequest('req_from_merc',$xml_post,1);
      $post_arry=$this->model->FormatXMLTOArray($xml_post);
    //  print_r($post_arry);die();
        $this->model->ProcessDebitRequest($post_arry, 'req_from_merc');
    }

    function ReceiveCreditRequest($req=false){
        $xml_post = file_get_contents('php://input');
        $log_file_name = $this->model->log->LogRequest('req_from_merc',$xml_request,1);
        $this->model->ProcessCreditRequest($service_id, 'req_from_merc');
    }





}
