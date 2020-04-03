<?php

class Mtndemo_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    /*
     * Core Merchant Functions
     */

    function ProcessDebitCompleted($request,$log_name){

     $response =$this->SendByGeneralCurl(GW_REQUEST_URL,$request,$log_name);
     return $response;
    }

    function ProcessDebitRequest($request){


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



        function FormatXMLTOArray($xml){
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML($xml);
            libxml_clear_errors();
            $xmln = $doc->saveXML($doc->documentElement);
            $object = simplexml_load_string($xmln);
            $array = $this->ObjectToArray($object);
             $f_array = $this->ArrayFlattener($array);
          //  $stan=$this->map->StandardizeOperatorParams($f_array);
         return $f_array;
        }



            function ObjectToArray($obj) {
                if (!is_array($obj) && !is_object($obj))
                    return $obj;
                if (is_object($obj))
                    $obj = get_object_vars($obj);
                return array_map(__METHOD__, $obj);
            }

            function ArrayFlattener($array) {
                if (!is_array($array)) {
                    return FALSE;
                }
                $result = array();
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        $result = array_merge($result, $this->ArrayFlattener($value));
                    } else {
                        $result[$key] = $value;

                    }
                }

                return $result;
            }


}
