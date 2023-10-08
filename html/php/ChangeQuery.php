<?php
require_once "Query.php";

class ChangeQuery extends Query {
    // can be used for INSERT or UPDATE statements, anything that doesn't need a result return
    public function performQuery() {
        // no return value
        $this->_statement->execute($this->_parameters);
    }
}
?>