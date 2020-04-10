<?php


class Mtndemo extends Controller {

    function __construct() {
        parent::__construct();

    }

    function Index(){

      header("Content-type: text/xml; charset=utf-8");
   $error ='<?xml version="1.0" encoding="UTF-8"?><ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="Forbidden"/>';
       echo $error;
        exit();
    }

    function ReceiveDebitRequest($req=false){
        $xml_request = file_get_contents('php://input');
      if(empty($xml_request)==false){
        $log_file_name = $this->model->log->LogRequest('req_from_merc',$xml_request,1);
      $post_arry=$this->model->FormatXMLTOArray($xml_request);
    //  print_r($post_arry);die();
        $this->model->ProcessDebitRequest($post_arry, 'req_from_merc');
      }else{

        header("Content-type: text/xml; charset=utf-8");
     $error ='<?xml version="1.0" encoding="UTF-8"?><ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="Invalid_request"/>';
         echo $error;
          exit();
      }

    }

    function ReceiveCreditRequest($req=false){
        $xml_request = file_get_contents('php://input');
        if(empty($xml_request)==false){
        $log_file_name = $this->model->log->LogRequest('req_from_merc',$xml_request,1);
       $post_arry=$this->model->FormatXMLTOArray($xml_request);
        $this->model->ProcessGwCreditRequest($post_arry, 'req_from_merc');
       }else{

         header("Content-type: text/xml; charset=utf-8");
      $error ='<?xml version="1.0" encoding="UTF-8"?>
      <ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="Invalid_request"/>';
          echo $error;
          exit();
       }
    }





}
