<?php
class Job {
    // job related
    protected $_id;
    protected $_model;
    protected $_note;
    protected $_passcode;
    protected $_charge;
    protected $_problems;   // assoc array, key = problem, value = predicted charge

    // customer info
    protected $_customer;
    protected $_phone1;
    protected $_phone2;
    protected $_email;
    protected $_address;

    // filter related
    protected $_repaired;
    protected $_receive_date;
    protected $_pickup_date;
    
    protected $_price_estimate;
    protected $_pickup_estimate;

    public function __construct($result, $problems) {
        $this->_id = $result[0];
        $this->_model = $result[1];
        $this->_note = $result[2];
        $this->_passcode = $result[3];
        $this->_charge = $result[4];

        $this->_customer = $result[5];
        $this->_phone1 = $result[6];
        $this->_phone2 = $result[7];
        $this->_email = $result[8];
        $this->_address = $result[9];

        $this->_repaired = $result[10]; // boolean
        $this->_receive_date = $result[11];
        $this->_pickup_date = $result[12];

        $this->_price_estimate = $result[13];
        $this->_pickup_estimate = $result[14];


        foreach($problems as $problem) {
            $this->_problems[$problem[0]] = $problem[1];
        }
        
    }
    
    // echo to html
    public function display() {
        // formatting
        $repaired_text = ($this->_repaired == 0) ? "Not repaired" : "Repaired";
        $repaired_checked = ($this->_repaired == 0) ? "" : "checked";

        $charge = number_format($this->_charge, 2, ".", ",");
        $price_estimate = number_format($this->_price_estimate, 2, ".", ",");

        $receive_date = "";
        if(isset($this->_receive_date)) {
            $receive_date = date("d-m-Y", strtotime($this->_receive_date));
        }
        $pickup_date = "";
        if(isset($this->_pickup_date)) {
            $pickup_date = date("d-m-Y", strtotime($this->_pickup_date));
        }
        $pickup_estimate = "";
        if(isset($this->_pickup_estimate)) {
            $pickup_estimate = date("d-m-Y", strtotime($this->_pickup_estimate));
        }

        // job info
        echo "<form class='job-item' id='ji-{$this->_id}'>";

        echo "<dl class='job-item-section' id='jis-job'>
            <div><dt>ID</dt><dd id='dd-id'>{$this->_id}</dd></div>
            <div><dt>Repaired</dt><dd id='dd-repaired'>{$repaired_text}</dd></div>
            <div><label class='job-label' for='ji-repaired-{$this->_id}'>Set repaired: </label>
            <input class='job-input' type='checkbox' name='ji-repaired-{$this->_id}' id='ji-repaired-{$this->_id}' {$repaired_checked}></div>
            <div><dt>Model</dt><dd id='dd-model'>{$this->_model}</dd></div>
            <div><dt>Note</dt><dd id='dd-note'>{$this->_note}</dd></div>
            <div><dt>Passcode</dt><dd id='dd-passcode'>{$this->_passcode}</dd></div>
            <div><dt>Charge</dt><dd id='dd-charge'>{$charge}</dd></div>
            <div><label class='job-label' for='ji-charge-{$this->_id}'>Set charge: </label>
            <input class='job-input' type='number' name='ji-charge-{$this->_id}' id='ji-charge-{$this->_id}' value='{$this->_charge}'></div>
            <div><dt>Receive date</dt><dd id='dd-receive-date'>{$receive_date}</dd></div>
            <div><dt>Estimated pickup date</dt><dd id='dd-pickup-estimate'>{$pickup_estimate}</dd></div>
            <div><dt>Pickup date</dt><dd id='dd-pickup-date'>{$pickup_date}</dd></div>
            <div><label class='job-label' for='ji-pickup-date-{$this->_id}'>Set pickup date: </label>
            <input class='job-input' type='date' name='ji-pickup-date-{$this->_id}' id='ji-pickup-date-{$this->_id}' value='{$this->_pickup_date}'></div>
        </dl>";

        // customer info
        echo "<dl class='job-item-section' id='jis-customer'>
            <div><dt>Customer name</dt><dd id='dd-customer'>{$this->_customer}</dd></div>
            <div><dt>Phone 1</dt><dd id='dd-phone1'>{$this->_phone1}</dd></div>
            <div><dt>Phone 2</dt><dd id='dd-phone2'>{$this->_phone2}</dd></div>
            <div><dt>Email</dt><dd id='dd-email'>{$this->_email}</dd></div>
            <div><dt>Address</dt><dd id='dd-address'>{$this->_address}</dd></div>
        </dl>";

        // problem info
        
        echo "<dl class='job-item-section' id='jis-problem'>";
        echo "<div><ol id='ol-problems'>Problems: ";
        foreach($this->_problems as $problem => $charge) {
            echo "<li>{$problem}</li>";
        }

        echo "</ol></div>";

        echo "<div><dt>Estimated charge</dt><dd id='dd-price-estimate'>\${$price_estimate}</dd></div>";
        echo "</dl>";

        echo "<input class='job-button job-submit' type='submit' id='jb-submit-{$this->_id}' value='Apply changes'>";

        echo "</form>";
    }
}
?>