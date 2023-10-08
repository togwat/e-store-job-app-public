<?php
require_once "php/loginCheck.php";
require_once "php/adminCheck.php";
?>

<!--jobs page-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<title>Jobs</title>

	<link rel="stylesheet" href="css/job.css">
	<link rel="stylesheet" href="css/dl.css">

	<script src="js/AjaxQueryGetString.js"></script>
	<script src="js/AjaxJob.js"></script>
</head>

<script>
var jobItems;
let ajaxJobs = {};	// key = id, value = job object

window.onload = function() {
	jobItems = document.querySelectorAll(".job-item");

	Array.from(jobItems).forEach(item => {
		item.reset();	// reset job item 'forms' on page load/reload

		// create js job objects
		const jobSection = item.querySelector("#jis-job");
		const customerSection = item.querySelector("#jis-customer");
		const problemSection = item.querySelector("#jis-problem");
			
		let id = jobSection.querySelector("#dd-id");
		let repaired = jobSection.querySelector("#dd-repaired");
		let model = jobSection.querySelector("#dd-model");
		let note = jobSection.querySelector("#dd-note");
		let passcode = jobSection.querySelector("#dd-passcode");
		let charge = jobSection.querySelector("#dd-charge");
		let receiveDate = jobSection.querySelector("#dd-receive-date");
		let pickupDate = jobSection.querySelector("#dd-pickup-date");

		let customer = customerSection.querySelector("#dd-customer");
		let phone1 = customerSection.querySelector("#dd-phone1");
		let phone2 = customerSection.querySelector("#dd-phone2");
		let email = customerSection.querySelector("#dd-email");
		let address = customerSection.querySelector("#dd-address");

		let problems = [];
		let problemsList = Array.from(problemSection.querySelector("#ol-problems").querySelectorAll("li"));
		problemsList.forEach(problem => {
			problems.push(problem.innerHTML);
		});

		let changeRepairedCheckbox = jobSection.querySelector(`#ji-repaired-${id.innerHTML}`);
		let chargeInput = jobSection.querySelector(`#ji-charge-${id.innerHTML}`);
		let pickupDateInput = jobSection.querySelector(`#ji-pickup-date-${id.innerHTML}`);

		let priceEstimate = problemSection.querySelector("#dd-price-estimate");
		let pickupEstimate = jobSection.querySelector("#dd-pickup-estimate");

		let job = new AjaxJob(id.innerHTML, Number(changeRepairedCheckbox.checked), model.innerHTML, note.innerHTML, passcode.innerHTML, charge.innerHTML, receiveDate.innerHTML, pickupDate.innerHTML, customer.innerHTML, phone1.innerHTML, phone2.innerHTML, email.innerHTML, address.innerHTML, problems, priceEstimate.innerHTML, pickupEstimate.innerHTML);

		// add to dict of jobs
		ajaxJobs[id.innerHTML] = job;

		// change script
		const submitButton = item.querySelector(".job-submit");

		submitButton.addEventListener("click", function(event) {
			// retrieve changes
			let newRepaired = Number(changeRepairedCheckbox.checked);
			let newCharge = chargeInput.value;
			let newPickupDate = pickupDateInput.value;

			// update AjaxJob object
			// repaired jobs need a charge and a date
			if(newCharge === "" || newPickupDate === "") {
				alert("Repaired jobs need a charge and a pickup date!");	
			}
			else {
				job.applyChanges(newRepaired, newCharge, newPickupDate);
			
				// update page
				repaired.innerHTML = newRepaired ? "Repaired" : "Not repaired";
				charge.innerHTML = (Number(newCharge)).toLocaleString('en-US', {minimumFractionDigits: 2});	// xxxx,xx.xx
				pickupDate.innerHTML = newPickupDate; // wrong date
			}

			event.preventDefault();	// prevent page refresh
		});
	});

	// filter script
	// get all elements
	const repairedCheckbox = document.getElementById("fc-repaired");
	const notRepairedCheckbox = document.getElementById("fc-not-repaired");
	const receiveDateStart = document.getElementById("fd-receive-start");
	const receiveDateEnd = document.getElementById("fd-receive-end");
	const pickupDateStart = document.getElementById("fd-pickup-start");
	const pickupDateEnd = document.getElementById("fd-pickup-end");
	const searchBar = document.getElementById("fi-search");
	const applyButton = document.getElementById("fb-apply");

	function applyFilter() {
		Array.from(jobItems).forEach(item => {
			const jobSection = item.querySelector("#jis-job");
			let id = jobSection.querySelector("#dd-id").innerHTML;
			let found = ajaxJobs[id].search(Number(repairedCheckbox.checked), Number(notRepairedCheckbox.checked), receiveDateStart.value, receiveDateEnd.value, pickupDateStart.value, pickupDateEnd.value, searchBar.value);

		if(found) {
			item.style.display = "block";
		}
			// hide if not found
			else {
				item.style.display = "none";
			}
		});

		event.preventDefault();	// prevent page refresh
	}

	// run once on page load
	applyFilter();

	// on filter apply
	applyButton.addEventListener("click", applyFilter);
}
</script>

<?php
require_once "php/Connection.php";
require_once "php/SelectQuery.php";
require_once "php/Job.php";
require_once "php/splitArray.php";

$connection = Connection::getConnection();

// select all jobs
$job_query = new SelectQuery("SELECT job_id, device_models.device_model, note, passcode, charge, name, phone_number, phone_number_secondary, email, address, successful_repair, receive_date, pickup_date, estimate_charge, estimate_pickup_date
								FROM jobs
									INNER JOIN customers
										ON jobs.customer = customers.customer_id
									INNER JOIN device_models
										ON jobs.device_model = device_models.device_model_id;", array(), $connection);
$job_column_count = $connection->field_count;
$job_result = $job_query->performQuery();

$problem_query = new SelectQuery("SELECT problem_name, problems.price FROM problem_types
									INNER JOIN problems
										ON problem_types.problem_type_id = problems.problem_type
									INNER JOIN job_problems
										ON problems.problem_id = job_problems.problem
											WHERE job_problems.job = ?;", array(), $connection);
$problem_column_count = $connection->field_count;
?>

<body>
<a class="back-link" href="menu.php">Back</a>
<div class="job-section" id="js-filter">
	<form class="form" id="f-filter">
		<!--search-->
		<label class="form-label" for="fi-search">Search</label>
		<input class="form-input" type="text" name="fi-search" id="fi-search">

		<!--filter-->
		<label class="form-label" for="fc-repaired">Repaired
			<input class="form-checkbox" type="checkbox" id="fc-repaired" name="fc-repaired">
		</label>
		<label class="form-label" for="fc-not-repaired">Not repaired
			<input class="form-checkbox" type="checkbox" id="fc-not-repaired" name="fc-not-repaired" checked>
		</label>

		<div class="js-section">
			<p>Receive date</p>
			<input class="form-date" type="date" id="fd-receive-start" name="fd-receive-start">
			-
			<input class="form-date" type="date" id="fd-receive-end" name="fd-receive-end">
		</div>
		<div class="js-section">
			<p>Pickup date</p>
			<input class="form-date" type="date" id="fd-pickup-start" name="fd-pickup-start">
			-
			<input class="form-date" type="date" id="fd-pickup-end" name="fd-pickup-end">
		</div>

		<button class="form-button" id="fb-apply">Apply filter</button>
	</form>
</div>
<div class="job-section" id="js-job">
	<?php
	// sort result into an array of rows
	$job_rows = splitArray($job_result, $job_column_count);
	
	foreach($job_rows as $job) {
		// get problems
		$problem_query->setParameters(array($job[0]));	// job id
		$problem_result = $problem_query->performQuery();
		$problem_rows = splitArray($problem_result, $problem_column_count);

		// create html items
		$job_obj = new Job($job, $problem_rows);

		$job_obj->display();
	}
	?>
</div>
</body>
</html>
