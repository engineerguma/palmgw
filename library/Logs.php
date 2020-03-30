<?php

class Logs {

    function __construct() {

    }



    function LogRequest($fl, $log, $lv, $id = false) {
        $filext = date('Y_m_d');
		$logtofile=null;
		$todays_folder = 'systemlog/tmp/'.$fl.'/' . date('Y_m_d');
        $filename = $todays_folder.'/' . $fl . '_' . $filext . '.txt';
	    if (is_dir($todays_folder)) {
	    $logtofile = $this->PrepareLog($log, $lv, $id);
        } else {
            mkdir($todays_folder,0777, true);
	    $logtofile = $this->PrepareLog($log, $lv, $id);
        }
	   file_put_contents($filename, $logtofile . "\n", FILE_APPEND);

    }



    function PrepareLog($log, $level, $id = false) {
        $logcont = '';
        if ($id == 1) {
            $class = 'info';
        } elseif ($id == 2) {
            $class = 'warning';
        } elseif ($id == 3) {
            $class = 'observe';
        } elseif ($id == 4) {
            $class = 'successful';
        } else {
            $class = 'failed';
        }

        switch ($level) {
            case 1:
                $logcont = '<ul class="rectangle-list"><li><a href="" class="leading">' . date('Y-m-d H:i:s') . ' [LOG ENTRY] ' . $log . '</a>
        <ol>';
                break;
            case 2:
                $logcont .='<li><a href="" class="' . $class . '">' . date('Y-m-d H:i:s') . ' [LOG ENTRY] ' . $log . '</a></li>';
                break;
            case 3:
                $logcont .='<li><a href="" class="' . $class . '">' . date('Y-m-d H:i:s') . ' [LOG ENTRY] ' . $log . '</a></li></ol>
    </li></ul>';
                break;
            default:
                break;
        }
        return $logcont;
    }

}
