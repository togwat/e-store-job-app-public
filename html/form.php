<?php
require_once "php/loginCheck.php";
?>

<!--form page-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Form</title>

    <script src="js/AjaxDatalist.js"></script>
    <script src="js/AjaxQueryGetString.js"></script>

    <link rel="stylesheet" href="css/form.css">
</head>

<?php
require_once "php/generator.php";

require_once "php/Connection.php";
require_once "php/SelectQuery.php";
require_once "php/ChangeQuery.php";

$connection = Connection::getConnection();

// form submission
if(isset($_POST["fi-phone1"]) && isset($_POST["fi-product-model"]) && isset($_POST["fi-problem"])) {
    // retrieve customer form info
    $name = trim($_POST["fi-name"]);
    // make phone numbers pure number
    $phone1 = preg_replace('/\s+/', '', $_POST["fi-phone1"]);
    $phone2 = preg_replace('/\s+/', '', $_POST["fi-phone2"]);
    $email = trim($_POST["fi-email"]);
    $address = trim($_POST["fi-address"]);

    // retrieve problem form info
    $model = trim($_POST["fi-product-model"]);
    $passcode = $_POST["fi-passcode"];  // don't trim passcode in case spacebar in passcode
    $problems = $_POST["fi-problem"];   // is an array
    $problems = array_unique($problems);    // remove duplicate problems
    $problems = array_filter($problems);    // remove empty problem fields
    $notes = trim($_POST["ft-notes"]);

    // get estimates
    $price_estimate = $_POST["fi-price-estimate"];
    $pickup_estimate = $_POST["fi-pickup-estimate"];

    // insert customer(if doesn't exist)
    $customer_query = new SelectQuery("SELECT customer_id FROM customers WHERE phone_number = ?;",array($phone1), $connection);
    $customer = $customer_query->performQuery();
    
    // customer doesn't exist
    if(!array_key_exists(0, $customer) || !isset($customer[0])) {
        $insert_customer_arguments = array($name, $phone1, $phone2, $email, $address);
        $insert_customer_query = new ChangeQuery("INSERT INTO `repair_jobs_database`.`customers` (`name`, `phone_number`, `phone_number_secondary`, `email`, `address`) VALUES (?, ?, ?, ?, ?);", $insert_customer_arguments, $connection);
        $insert_customer_query->performQuery();
        $customer = array($connection->insert_id);
    }

    // insert new job
    $model_id_query = new SelectQuery("SELECT device_model_id FROM device_models WHERE device_model = ?;", array($model), $connection);
    $model_id = $model_id_query->performQuery();

    $insert_job_arguments = array($customer[0], $model_id[0], $notes, $passcode, $price_estimate, $pickup_estimate);
    $insert_job_query = new ChangeQuery("INSERT INTO `repair_jobs_database`.`jobs` (`customer`, `device_model`, `receive_date`, `note`, `passcode`, `successful_repair`, `estimate_charge`, `estimate_pickup_date`) VALUES (?, ?, CURDATE(), ?, ?, 0, ?, ?);", $insert_job_arguments, $connection);
    $insert_job_query->performQuery();
    $job_id = $connection->insert_id;

    // insert problems
    $problem_type_query = new SelectQuery("SELECT problem_type_id FROM problem_types WHERE problem_name = ?;", array(), $connection);   // empty parameters set in loop
    $problem_id_query = new SelectQuery("SELECT problem_id FROM problems WHERE problem_type = ? AND model = ?;", array(), $connection); // same as above
    $insert_problem_query = new ChangeQuery("INSERT INTO `repair_jobs_database`.`job_problems` (`problem`, `job`) VALUES (?, ?);", array(), $connection);   // same as above
    
    foreach($problems as $problem) {
        if(!empty($problem)) {
            $problem_type_query->setParameters(array($problem));
            $problem_type = $problem_type_query->performQuery();

            $problem_id_query->setParameters(array($problem_type[0], $model_id[0]));
            $problem_id = $problem_id_query->performQuery();

            $insert_problem_query->setParameters(array($problem_id[0], $job_id));
            $insert_problem_query->performQuery();
        }
    }

    // go to summary page
    function arrayGet($array, $arrayName) {
        // modify array items to the format of $arrayName[]=$item
        $getArray = array_map(function($item) use($arrayName) {
            return "{$arrayName}[]={$item}";
        }, $array);

        return join("&", $getArray);
    }

    $getProblems = arrayGet($problems, "problems");
    header("Location: summary.php?name={$name}&phone1={$phone1}&email={$email}&model={$model}&price_estimate={$price_estimate}&pickup_estimate={$pickup_estimate}&note={$notes}&job_id={$job_id}&{$getProblems}");
}
?>

<body>
<a class="back-link" href="menu.php">Back</a>

<form class="form" id="f-job" method="post">
	<h1 class="form-header">E-Store Repair Job Form</h1>
    <!--customer form-->
    <div class="form-section" id="fs-customer">
        <div class="form-item">
            <label class="form-label" for="fi-name">Name</label>
	        <input class="form-input" type="text" maxlength="64" name="fi-name" id="fi-name">
        </div>
        <div class="form-item">
	        <label class="form-label" for="fi-phone1">Phone number</label>
	        <input class="form-input" type="tel" maxlength="11" name="fi-phone1" id="fi-phone1" placeholder="required" required>
        </div>
        <div class="form-item">
	        <label class="form-label" for="fi-phone2">Secondary phone number</label>
	        <input class="form-input" type="tel" maxlength="11" name="fi-phone2" id="fi-phone2">
        </div>
        <div class="form-item">
	        <label class="form-label" for="fi-email">Email</label>
	        <input class="form-input" type="text" maxlength="255" name="fi-email" id="fi-email">
        </div>
        <div class="form-item">
	        <label class="form-label" for="fi-address">Address</label>
	        <input class="form-input" type="text" maxlength="128" name="fi-address" id="fi-address">
        </div>
    </div>
    <br/>

    <!--problem form-->
    <div class="form-section" id="fs-problem">
        <div class="form-item">
	        <label class="form-label" for="fs-product-type">Product type</label>
	        <select class="form-select" name="fs-product-type" id="fs-product-type">
                <?php
                // add product types from db, array from generator.php
                foreach($device_types as $type) {
                    echo "<option value='{$type}'>{$type}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-item">
	        <label class="form-label" for="fi-product-model">Product model</label>
	        <input class="form-input" name="fi-product-model" id="fi-product-model" list="fipm-list" placeholder="required" required>
            <datalist id="fipm-list">
                <script>
                    const typeInput = document.getElementById("fs-product-type");
                    const fipmList = document.getElementById("fipm-list");

                    const modelQuery = "SELECT device_model FROM device_models INNER JOIN device_types ON device_types.device_type_id = device_models.device_type WHERE device_types.device_type = ?;";

                    let fipmAjaxList = new AjaxDatalist(modelQuery, [typeInput.value], fipmList);
                    fipmAjaxList.createDatalist();  // default value

                    // on selection change
                    typeInput.addEventListener("change", function() {
                        // get model recommendation via ajax
                        fipmAjaxList.setParameters([typeInput.value]);
                        fipmAjaxList.createDatalist();                
                    });
                </script>
            </datalist>
        </div>
        <div class="form-item">
	        <label class="form-label" for="fi-passcode">Passcode</label>
	        <input class="form-input" type="text" maxlength="64" name="fi-passcode" id="fi-passcode">
        </div>
        <!--Problems have dynamic size, fi-problem[] is recognised as an array by php-->
        <div id="problem-container" class="form-item">
	        <label class="form-label" for="fi-problem1">Problems</label>
	        <input class="form-input form-problem" name="fi-problem[]" id="fi-problem1" list="fip-list" placeholder="required" required>
            <datalist id="fip-list">
                <script>
                    const modelInput = document.getElementById("fi-product-model");
                    const fipList = document.getElementById("fip-list");
                    const problemContainer = document.getElementById("problem-container");
                    let problemInputs = problemContainer.querySelectorAll(".form-input");

                    // update all listeners on problem inputs
                    function updateProblemInputs() {
                        problemInputs = problemContainer.querySelectorAll(".form-input");

                        Array.from(problemInputs).forEach(input => {
                        input.addEventListener("change", function() {
                            getEstimate(problemInputs);
                        });
                    })
                    }
                    
                    Array.from(problemInputs).forEach(input => {
                        input.addEventListener("change", function() {
                            getEstimate(problemInputs);
                        });
                    })

                    const problemQuery = "SELECT problem_name, price FROM problem_types INNER JOIN problems ON problems.problem_type = problem_types.problem_type_id WHERE problems.model = (SELECT device_model_id FROM device_models WHERE device_model = ?);";

                    let fipAjaxList = new AjaxDatalistDictionary(problemQuery, [modelInput.value], fipList);
                    fipAjaxList.createDatalist();
                    
                    // on model edit
                    modelInput.addEventListener("change", function() {
                        fipAjaxList.setParameters([modelInput.value]);
                        fipAjaxList.createDatalist();
                        // reset problems
                        problemInputs = problemContainer.querySelectorAll(".form-input");
                        Array.from(problemInputs).forEach(input => {
                            input.value = "";
                        });
                        getEstimate(problemInputs);
                    });
                </script>
            </datalist>
        </div>
        <div class="form-item form-button-container">
            <button class="form-button" type="button" id="fb-add-problem">Add problem</button>
	        <button class="form-button" type="button" id="fb-remove-problem">Remove problem</button>
            <script>
                let problemCount = document.querySelectorAll(".form-problem").length;

                const addButton = document.getElementById("fb-add-problem");
                const removeButton = document.getElementById("fb-remove-problem");

                // on add click
                addButton.addEventListener("click", function() {
                    // create new problem
                    const newProblem = document.createElement("input");
                    newProblem.setAttribute("class", "form-input form-problem");
                    newProblem.setAttribute("name", "fi-problem[]");
                    newProblem.setAttribute("id", "fi-problem" + ++problemCount);   // increments problemCount as well
                    newProblem.setAttribute("list", "fip-list");

                    problemContainer.appendChild(newProblem);

                    newProblem.addEventListener("change", function() {
                        updateProblemInputs();
                        getEstimate(problemInputs);
                    });
                });

                // on remove click
                removeButton.addEventListener("click", function() {
                    // at least 1 problem needs to exist
                    if(problemCount > 1) {
                        problemContainer.removeChild(problemContainer.lastChild);
                        problemCount--;
                        updateProblemInputs();
                        getEstimate(problemInputs);
                    }
                });
            </script>
        </div>
        <div class="form-item">
            <label class="form-label" for="fi-price-estimate">Estimated price</label>
	        <input class="form-input" type="number" name="fi-price-estimate" id="fi-price-estimate">
            <script>
                const priceEstimateInput = document.getElementById("fi-price-estimate");

                function getEstimate(inputs) {
                    let hidden = document.getElementById("fi-hidden");

                    // parse dictionary
                    var problemDictionary = new Map(JSON.parse(hidden.value));
                    let estimate = 0;

                     Array.from(inputs).forEach(input => {
                         estimate += Number(problemDictionary.get(input.value));
                     });
                    if(isNaN(estimate)) {
                        priceEstimateInput.placeholder = 0;
                    }
                    else {
                        priceEstimateInput.placeholder = estimate;
                    }
                }
            </script>
        </div>
        <div class="form-item">
            <label class="form-label" for="fi-pickup-estimate">Estimated pickup date</label>
	        <input class="form-input" type="date" name="fi-pickup-estimate" id="fi-pickup-estimate" value="<?php echo date('Y-m-d');?>">
        </div>
        <div class="form-item">
	        <label class="form-label" for="ft-notes">Notes</label>
	        <textarea class="form-textarea" maxlength="512" name="ft-notes" id="ft-notes"></textarea>
        </div>
    </div>
    
    <div class="form-section">
	    <input class="form-button" id="fb-submit" type="submit" value="Submit">
    </div>
</form>
</body>
</html>
