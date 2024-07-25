<?php

class Index extends Controller {

    function __construct() {
        parent::__construct();
    }

    function Index(){

        header('Content-Type: application/json');       
	    $general=array('status'=>403,
                     'message'=>'Forbidden');
        echo json_encode($general);
    }
}
