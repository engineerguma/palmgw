<?php

class Mtnrwanda_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    /*
     * Core Merchant Functions
     */

    function ProcessDebitCompleted($request,$log_name){

     $response =$this->SendByGeneralCurl(MTN_REQUEST_URL,$request,$log_name);
     return $response;
    }

    function ProcessGwDebitRequest($request){

    $header=['Authorization: Basic '.base64_encode(MTN_USER.':'.MTN_PASS),
'Content-Type: application/xml',
'Accept: application/xml'];
     $request =$this->SendByCurl(MTN_REQUEST_URL.'debit',$header,$request);

     return $request;
    }

    function ProcessGwCreditRequest($request){

    $header=['Authorization: Basic '.base64_encode(MTN_USER.':'.MTN_PASS),
'Content-Type: application/xml',
'Accept: application/xml'];
     $request =$this->SendByCurl(MTN_REQUEST_URL.'sptransfer',$header,$request);

     return $request;
    }


    function ProcessGwStatusRequest($request){

    $header=['Authorization: Basic '.base64_encode(MTN_USER.':'.MTN_PASS),
'Content-Type: application/xml',
'Accept: application/xml'];
     $request =$this->SendByCurl(MTN_REQUEST_URL.'gettransactionstatus',$header,$request);

     return $request;
    }

    function SendByCurl($url,$header,$request_data){


  $this->log->LogRequest('req_to_mtn',$request_data,2);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
      curl_setopt($ch, CURLOPT_SSLVERSION, 6);
      curl_setopt($ch, CURLOPT_SSLCERT ,  "/home/sslcertificates/197_243_14_94.crt" );
      curl_setopt($ch, CURLOPT_SSLKEY ,  "/home/sslcertificates/197_243_14_94.pem" );
       $result = curl_exec($ch);
       if (curl_errno($ch) > 0) {
        $result= curl_error($ch);
        }

      return $result;
    }


    function SendByGeneralCurl($url,$request_data,$log_name){


         $this->log->LogRequest($log_name,$request_data,2);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          //  curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
      $result = curl_exec($ch);
       if (curl_errno($ch) > 0) {
        $result= curl_error($ch);
        }

      return $result;
    }




}
