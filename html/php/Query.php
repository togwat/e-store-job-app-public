<?php
abstract class Query {
    protected $_query;
    protected $_parameters;
    protected $_connection;

    protected $_statement;

    public function __construct($query, $parameters, $connection) {
        $this->_query = $query;
        $this->_parameters = $parameters;
        $this->_connection = $connection;

        // prepare query
        $this->_statement = $this->_connection->prepare($this->_query);
    }

    abstract public function performQuery();

    public function setParameters($parameters) {
        $this->_parameters = $parameters;
    }
}
?>