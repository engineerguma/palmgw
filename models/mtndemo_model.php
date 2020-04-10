<?php

class Mtndemo_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    /*
     * Core Merchant Functions
     */

    function ProcessDebitRequest($request,$log_name){

      $fromfri = explode('/', $request['fromfri']);
      $tofri = explode('/', $request['tofri']);
      $request['msisdn']=substr($fromfri[0], 4);
      $request['merchant']=substr($tofri[0], 4);
    //  print_r($request);die();
      $customer=$this->GetCustomerDetails($request['msisdn']);
        //print_r($customer);die();
        $this->log->LogRequest($log_name,"Mtndemo_Model  Customer data ". var_export($customer,true),2);
       if(count($customer)>0){
         $balance =$customer[0]['account_balance']-$request['amount'];
         //print_r($request);die();
          if($customer[0]['account_balance']>$request['amount']){

          $verify=$this->verifyTransaction($request['externaltransactionid']);
         if(count($verify)==0){

         $post= array();
         $post['external_id']=$request['externaltransactionid'];
         $post['referenceid']=$request['referenceid'];
         $post['phonenumber']=$request['msisdn'];
         $post['transaction_type']='debit';
         $post['transaction_date']=date('Y-m-d H:i:s');
         $post['transaction_amount']=$request['amount'];
         $post['running_balance']=$balance;
       $momo_genID=$this->SaveTransactionData($post);

       header('Content-Type: text/xml');

       while(ob_get_level())ob_end_clean();
       ignore_user_abort();
       ob_start();
       // Send the response
        echo '<?xml version="1.0" encoding="UTF-8"?> <ns0:debitresponse xmlns:ns0="http://www.ericsson.com/em/emm/financial/v1_0"><transactionid>'.$momo_genID.'</transactionid><status>PENDING</status></ns0:debitresponse>';
       $size = ob_get_length();
       // Disable compression (in case content length is compressed).
       header("Content-Encoding: none");
       header("Content-Length:".$size);
       // Close the connection.
       header("Connection: close");
       // Flush all output.
       ob_end_flush();
       ob_flush();
       flush();

         if (is_callable('fastcgi_finish_request')) {
       // This works in Nginx but the next approach not
           fastcgi_finish_request();// important when using php-fpm!
           }

        $user=array('account_balance'=>$balance);
        $this->UpdateCustomerBalance($customer[0]['record_id'],$user);

        $this->log->LogRequest($log_name,"Mtndemo_Model  checking Request ". var_export($request,true),2);

        $routing=$this->GetRouting($request['merchant'],'debit_callback');
        $this->log->LogRequest($log_name,"Mtndemo_Model  GetRouting data ". var_export($routing,true),2);

        $transaction=$this->GetTransaction($momo_genID);
        $this->log->LogRequest($log_name,"Mtndemo_Model  GetTransaction data ". var_export($transaction,true),2);

        $sendxml='<?xml version="1.0" encoding="UTF-8"?>
<ns0:debitcompletedrequest xmlns:ns0="http://www.ericsson.com/em/emm">
   <transactionid>'.$transaction[0]['transaction_id'].'</transactionid>
   <externaltransactionid>'.$transaction[0]['external_id'].'</externaltransactionid>
   <receiverinfo>
      <fri>'.$request['tofri'].'</fri>
   </receiverinfo>
   <status>SUCCESSFUL</status>
</ns0:debitcompletedrequest>';

$this->log->LogRequest($log_name,"Mtndemo_Model  Completed XML TO Merchant ". var_export($sendxml,true),2);

  $respo =$this->ProcessDebitCompleted($routing[0]['routing_url'],$sendxml);

  $this->log->LogRequest($log_name,"Mtndemo_Model  ProcessDebitCompleted Response ". var_export($respo,true),2);
     exit();

      }else{
      //duplicate_trans ref
   $response='<?xml version="1.0" encoding="UTF-8"?><ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="REFERENCE_ID_ALREADY_IN_USE"/>';
      }


       }else{
       //balance_insufficient
    $response='<?xml version="1.0" encoding="UTF-8"?><ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="TARGET_AUTHORIZATION_ERROR"/>';
       }

       }else{
        //not found
        $response='<?xml version="1.0" encoding="UTF-8"?><ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="AUTHORIZATION_SENDER_ACCOUNT_NOT_ACTIVE"/>';

       }
     header('Content-Type: text/xml');
     echo $response;
     exit();
    }


    function GetCustomerDetails($acc){
    $det =$this->db->SelectData("Select * from customer_accounts where phonenumber='".$acc."'");
    return $det;
  }

    function ProcessGwCreditRequest($request,$log_name){

            $fromfri = explode('/', $request['receivingfri']);
            $tofri = explode('/', $request['sendingfri']);
            $request['msisdn']=substr($fromfri[0], 4);
            $request['merchant']=substr($tofri[0], 4);
            //print_r($request);die();
            $customer=$this->GetCustomerDetails($request['msisdn']);
              //print_r($customer);die();
              $this->log->LogRequest($log_name,"Mtndemo_Model  Customer data ". var_export($customer,true),2);
             if(count($customer)>0){

               $balance =$customer[0]['account_balance']+$request['amount'];

                $verify=$this->verifyTransaction($request['providertransactionid']);
               if(count($verify)==0){

               $post= array();
               $post['external_id']=$request['providertransactionid'];
               $post['referenceid']=$request['referenceid'];
               $post['phonenumber']=$request['msisdn'];
               $post['transaction_type']='debit';
               $post['transaction_date']=date('Y-m-d H:i:s');
               $post['transaction_amount']=$request['amount'];
               $post['running_balance']=$balance;
             $momo_genID=$this->SaveTransactionData($post);

              $user=array('account_balance'=>$balance);
              $this->UpdateCustomerBalance($customer[0]['record_id'],$user);

              $response='<?xml version="1.0" encoding="UTF-8"?>
              <ns0:sptransferresponse xmlns:ns0="http://www.ericsson.com/em/emm/serviceprovider/v1_0/backend">
              <transactionid>'.$momo_genID.'</transactionid></ns0:sptransferresponse>';

        $this->log->LogRequest($log_name,"Mtndemo_Model  ProcessDebitCompleted Response ". var_export($response,true),2);

            }else{
            //duplicate_trans ref
         $response='<?xml version="1.0" encoding="UTF-8"?><ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="REFERENCE_ID_ALREADY_IN_USE"/>';
            }


             }else{
              //not found
              $response='<?xml version="1.0" encoding="UTF-8"?><ns0:errorResponse xmlns:ns0="http://www.ericsson.com/lwac" errorcode="AUTHORIZATION_SENDER_ACCOUNT_NOT_ACTIVE"/>';

             }
           header('Content-Type: text/xml');
           echo $response;
           exit();



/////////////
    $header=['Content-Type: application/xml',
'Accept: application/xml'];
     $request =$this->SendByCurl(GW_REQUEST_URL.'sptransfer',$header,$request);

     return $request;
    }



        function ProcessDebitCompleted($url,$request){

              $header=['Content-Type: application/xml',
          'Accept: application/xml'];
         $response =$this->SendByGeneralCurl($url,$request,$header);
         return $response;
        }


    function SendByGeneralCurl($url,$request_data,$header){


      //   $this->log->LogRequest($log_name,$request_data,2);

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
      $result = curl_exec($ch);
       if (curl_errno($ch) > 0) {
        ///$result= curl_error($ch);
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
