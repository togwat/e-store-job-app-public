<?php
require_once "Query.php";

class SelectQuery extends Query {  
    public function performQuery() {
        // returns an array
        $this->_statement->execute($this->_parameters);
        $result = $this->_statement->get_result();
        $result_array = array();
        while($row = $result->fetch_array(MYSQLI_NUM)) {
            foreach($row as $column) {
                $result_array[] = $column;
            }
        }

        return $result_array;
    }
}
?>