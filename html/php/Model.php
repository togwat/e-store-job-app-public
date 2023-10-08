<?php
class Model {
    protected $_name;
    protected $_type;
    protected $_problems = array(); // assoc array problem: price

    public function __construct($name, $type) {
        $this->_name = $name;
        $this->_type = $type;
    }

    public function getName() {
        return $this->_name;
    }

    public function getType() {
        return $this->_type;
    }

    public function getProblems() {
        return $this->_problems;
    }

    public function getPrice($problem) {
        return $this->_problems[$problem];
    }

    public function addProblem($problem, $price) {
        $this->_problems[$problem] = $price;
    }
}
?>