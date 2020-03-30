<?php
class Databaseconfig extends PDO {

    public function __construct() {
        parent::__construct(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    }

    public function InsertData($table, $data, $return_id = false) {
        ksort($data);
        $fieldlog=null;
        $fieldNames = implode(', ', array_keys($data));
        $fieldInputs = ':' . implode(', :', array_keys($data));
        $sql_statement = "INSERT INTO $table
                    ($fieldNames)
            VALUES  ($fieldInputs)";
        $sth = $this->prepare($sql_statement);

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
            $fieldlog .= "$key = :$value,";
        }

        $sth->execute();
        $res = $this->lastInsertId($return_id);
        $now = date('Y-m-d H:i:s');
		if(isset($_SESSION['uid'])){
		 $user_id = $_SESSION['uid'];
		}else{
		 $user_id = 0;
		}


        $logdata = array(
            'log_time' => $now,
            'table_name' => $table,
            'query_executed' => $sql_statement,
            'data_set' => $fieldlog,
            'user_id' => $user_id
        );
        $this->DBOperationLog($logdata);

        return $res;
    }

    public function UpdateData($table, $data, $where) {
        ksort($data);
		$fieldlog=null;
        $fieldDetails = NULL;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = :$key,";
            $fieldlog .= "$key = :$value,";
        }

        $fieldDetails = rtrim($fieldDetails, ',');

        $sql_statement = "UPDATE $table SET $fieldDetails WHERE $where";

        $sth = $this->prepare($sql_statement);

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
        $now = date('Y-m-d H:i:s');
	    	if(isset($_SESSION['uid'])){
		      $user_id = $_SESSION['uid'];
	      	}else{
          $user_id = 0;
	      	}

        $logdata = array(
            'log_time' => $now,
            'table_name' => $table,
            'query_executed' => $sql_statement,
            'data_set' => $fieldlog,
            'user_id' => $user_id
        );
        $this->DBOperationLog($logdata);
    }

    public function SelectData($sql, $data = array(), $fetchMode = PDO::FETCH_ASSOC) {
        $sth = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }

    public function DeleteData($table, $where, $limit = 1) {
        $sql_statement = "DELETE FROM $table WHERE $where LIMIT $limit";
        return $this->exec($sql_statement);
        $now = date('Y-m-d H:i:s');
	    	if(isset($_SESSION['uid'])){
		      $user_id = $_SESSION['uid'];
	      	}else{
          $user_id = 0;
	      	}

        $logdata = array(
            'log_time' => $now,
            'table_name' => $table,
            'query_executed' => $sql_statement,
            'user_id' => $user_id
        );
        $this->DBOperationLog($logdata);
    }

    public function DBOperationLog($data) {
        $fieldNames = implode(', ', array_keys($data));
        $fieldInputs = ':' . implode(', :', array_keys($data));
        $sql_statement = "INSERT INTO sm_system_log
                    ($fieldNames)
            VALUES  ($fieldInputs)";
        $sth = $this->prepare($sql_statement);
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
    }

}

?>
