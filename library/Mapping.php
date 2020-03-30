<?php

class Mapping {

    public $_match_up_array = array(

        'trnasaction_amount' => 'transaction_amount',
        'account_number' => 'account_number',
        'transaction_reference' => 'transaction_reference',
        'transaction_reference_number' => 'transaction_reference_number',
        'merchant_account' => 'merchant_account',
        'transaction_source' => 'transaction_source',
        'transaction_destination' => 'transaction_destination',
        'transaction_reason' => 'transaction_reason',
        'currency' => 'transaction_currency',

        'apikey' => 'api_key',
    );

    function __construct() {

    }

    function ParseXMLFromURL($url){
        $xmlp = simplexml_load_file($url);
        $p_array = $this->ObjectToArray($xmlp);
        return $p_array;
    }

    function ParseXMLRequest($xml_post, $level = false, $source = false, $serv_id = false) {

       	   $stan_array=null;
        if ($level==true) {
	    $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($xml_post);
        libxml_clear_errors();
        $xmln = $doc->saveXML($doc->documentElement);
        $xmlp = simplexml_load_string($xmln);
		$complete = $xmlp->body->envelope->body->requestpaymentcompleted;	//for payment completed only
		if(count($complete)>0){
		$stan_array = json_decode(json_encode((array)$complete), TRUE);
		}else{//if it fails
		 $p_array = $xmlp->body->envelope->body->processrequestresponse;
         $p_array = json_decode(json_encode((array)$p_array), TRUE);
       foreach ($p_array['return'] as $key => $value) {
       $name=strtolower($value['name']);
       $value=$value['value'];
       $stan_array["$name"]= $value;
       }
	      }
        } else {
        $xmln = $xml_post;
        $xmlp = simplexml_load_string($xmln);
        $p_array = $this->ObjectToArray($xmlp);
        $stan_array = $this->ArrayFlattener($p_array);
        }

	   $standard_array = $this->Standardize($stan_array);
        return $standard_array;
    }






    function Standardize($data_array) {
        //Convert to Single
        $result_array = array();
        foreach ($data_array as $key => $value) {
            $standard_key = $this->_match_up_array[$key];
            if (!empty($standard_key)) {
                $result_array[$standard_key] = $value;
            }
        }
        return $result_array;
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
