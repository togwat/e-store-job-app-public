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
    <link rel="stylesheet" href="css/model.css">
	<title>Models</title>

    <script src="js/DynamicForm.js"></script>
    <script src="js/AjaxQueryGetString.js"></script>
</head>

<?php 
require_once "php/generator.php";
require_once "php/ajaxQuery.php";
?>

<script>
let modelCount = 0; // used to keep track of how many models there are
window.onload = function() {
    // templates
    const formInputTemplate = document.createElement("input");
    const typeSelectTemplate = document.getElementById("mi-model-type-select");
    const problemSelectTemplate = document.getElementById("mi-problem-type-select");

    // button listeners
    formInputTemplate.setAttribute("class", "model-input");
    formInputTemplate.setAttribute("name", "mi-input[]");
    formInputTemplate.setAttribute("id", "mi-input");   // number gets added in DynamicForm class

    const deviceTypeForm = document.getElementById("mi-device-type");
    const problemTypeForm = document.getElementById("mi-problem-type");
    const modelForm = document.getElementById("mi-model");
    let deviceTypeDynamicForm = new DynamicForm(deviceTypeForm, deviceTypeForm.querySelector("#ic-device-type"), addTypeInput, removeInput, deviceTypeConfirm);
    let problemTypeDynamicForm = new DynamicForm(problemTypeForm, problemTypeForm.querySelector("#ic-problem-type"), addTypeInput, removeInput, problemTypeConfirm);
    let modelDynamicForm = new DynamicForm(modelForm, modelForm.querySelector("#ic-model"), addModelInput, removeInput, modelConfirm);

    // create DynamicForms for all model problems
    const problemForms = modelForm.querySelectorAll(".model-item-item");
    Array.from(problemForms).forEach(form => {
        let probemDynamicForm = new DynamicForm(form, form.querySelector(".model-item-problem"), addProblemInput, removeInput, problemConfirm);
    });

    // button functions
    function addTypeInput() {
        const newInput = formInputTemplate.cloneNode(true);
        this.container.appendChild(newInput);
        newInput.id = newInput.id + this.inputStack.length; // modify id
        this.inputStack.push(newInput);

    }

    function removeInput() {
        if(this.inputStack.length > 0) {
            this.container.removeChild(this.inputStack.pop());
        }
    }

    // confirmations, send new info to db
    function deviceTypeConfirm() {
        const query = "INSERT INTO `repair_jobs_database`.`device_types` (`device_type`) VALUES (?);";
        const modelSelects = document.querySelectorAll(".model-type-select");

        ajaxInsertItems(this.inputStack, query, this.container, modelSelects);

        // clear inputs
        this.inputStack = [];
    }

    function problemTypeConfirm() {
        const query = "INSERT INTO `repair_jobs_database`.`problem_types` (`problem_name`) VALUES (?);";
        const problemSelects = document.querySelectorAll(".problem-type-select");

        ajaxInsertItems(this.inputStack, query, this.container, problemSelects);

        // clear inputs
        this.inputStack = [];
    }

    // model functions
    function addModelInput() {
        const modelItemItem = document.createElement("div");
        modelItemItem.setAttribute("class", "model-item-item");
        modelItemItem.setAttribute("id", `mii${this.inputStack.length}`);   // modify id

        // model name and type inputs
        const doubleInputContainer = document.createElement("div");
        doubleInputContainer.setAttribute("class", "model-item-item-input");
        const modelInput = formInputTemplate.cloneNode(true);
        const typeInput = typeSelectTemplate.cloneNode(true);
        modelInput.setAttribute("name", "mi-name");
        modelInput.setAttribute("id", "mi-name");
        typeInput.setAttribute("name", "mi-type");
        typeInput.setAttribute("id", "mi-type");
        typeInput.setAttribute("class", "model-select");
        typeInput.classList.add("model-type-select");
        doubleInputContainer.appendChild(modelInput);
        doubleInputContainer.appendChild(typeInput);
        modelItemItem.appendChild(doubleInputContainer);

        this.container.appendChild(modelItemItem);
        this.inputStack.push(modelItemItem);
    }

    function modelConfirm() {
        const query = "INSERT INTO `repair_jobs_database`.`device_models` (`device_model`, `device_type`) (SELECT ?, device_type_id FROM device_types WHERE device_type = ?);";
        this.inputStack.forEach(item => {
            const modelInput = item.querySelector("#mi-name");
            const typeInput = item.querySelector("#mi-type");
            let model = modelInput.value.trim();
            let type = typeInput.value;
            if(model.length > 0) {
                let getString = new AjaxQueryGetString(query, [model, type], "change");

                const request = new XMLHttpRequest();
                request.open("GET", getString.getString);
                request.send();

                // convert to regular text
                let newItem = document.createElement("div");
                newItem.setAttribute("class", "model-item-item");
                newItem.setAttribute("id", "mi-mii" + modelCount);
                newItem.setAttribute("data-model", model)
                let newText = document.createElement("div");
                newText.innerHTML = `${model}, ${type}`;
                newText.setAttribute("class", "model-title");
                newItem.appendChild(newText);

                // add problem DynamicForm
                let newProblemContainer = document.createElement("div");
                newProblemContainer.setAttribute("class", "model-item-problem");
                newItem.appendChild(newProblemContainer);

                let newAddButton = document.createElement("button");
                newAddButton.setAttribute("type", "button");
                newAddButton.setAttribute("class", "model-button");
                newAddButton.setAttribute("id", "mb-add-mii" + modelCount);
                newAddButton.innerHTML = "+";
                newItem.appendChild(newAddButton);
                let newRemoveButton = document.createElement("button");
                newRemoveButton.setAttribute("type", "button");
                newRemoveButton.setAttribute("class", "model-button");
                newRemoveButton.setAttribute("id", "mb-remove-mii" + modelCount);
                newRemoveButton.innerHTML = "-";
                newItem.appendChild(newRemoveButton);
                let newConfirmButton = document.createElement("button");
                newConfirmButton.setAttribute("type", "button");
                newConfirmButton.setAttribute("class", "model-button");
                newConfirmButton.setAttribute("id", "mb-confirm-mii" + modelCount);
                newConfirmButton.innerHTML = "Confirm";
                newItem.appendChild(newConfirmButton);

                let newDynamicForm = new DynamicForm(newItem, newProblemContainer, addProblemInput, removeInput, problemConfirm);
                this.container.appendChild(newItem);
                modelCount++;
            }
            this.container.removeChild(item);
        });

        this.inputStack = [];
    }

    function addProblemInput() {
        const doubleInputContainer = document.createElement("div");
        doubleInputContainer.setAttribute("class", "model-item-problem-input");
        const problemInput = problemSelectTemplate.cloneNode(true);
        const priceInput = formInputTemplate.cloneNode(true);
        problemInput.setAttribute("name", "mi-type");
        problemInput.setAttribute("id", "mi-type");
        problemInput.setAttribute("class", "model-select");
        problemInput.classList.add("problem-type-select");
        priceInput.setAttribute("name", "mi-price");
        priceInput.setAttribute("id", "mi-price");
        priceInput.setAttribute("type", "number");
        doubleInputContainer.appendChild(problemInput);
        doubleInputContainer.appendChild(priceInput);

        this.container.appendChild(doubleInputContainer);
        this.inputStack.push(doubleInputContainer);
    }

    function problemConfirm() {
        const query = "INSERT INTO `repair_jobs_database`.`problems` (`problem_type`, `model`, `price`) (SELECT problem_type_id, device_model_id, ? FROM (SELECT problem_type_id FROM problem_types WHERE problem_name = ?) as pt, (SELECT device_model_id FROM device_models WHERE device_model = ?) as dm);";
        
        this.inputStack.forEach(item => {
            const problemInput = item.querySelector("#mi-type");
            const priceInput = item.querySelector("#mi-price");

            let problemType = problemInput.value;
            let price = priceInput.value.trim();
            let model = this.form.dataset.model;

            // open query
            let getString = new AjaxQueryGetString(query, [price, problemType, model], "change");
                    
            const request = new XMLHttpRequest();
            request.open("GET", getString.getString);
            request.send();

            // convert to regular text
            let newText = document.createElement("div");
            newText.innerHTML = `${problemType}, ${Number(price).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            this.container.appendChild(newText);

            this.container.removeChild(item);
        });

        this.inputStack = [];
    }

    // helper functions
    // only useful for type inputs
    function ajaxInsertItems(items, query, container, selects) {
        items.forEach(item => {
            let itemValue = item.value.trim();
            // don't count empty input
            if(itemValue.length > 0) {
                let getString = new AjaxQueryGetString(query, [itemValue], "change");

                const request = new XMLHttpRequest();
                request.open("GET", getString.getString);
                request.send();

                // replace inputs with regular text
                let newText = document.createElement("div");
                newText.innerHTML = itemValue;
                container.appendChild(newText);
                
                // add values to select lists
                Array.from(selects).forEach(select => {
                    const newOption = document.createElement("option");
                    newOption.setAttribute("value", itemValue);
                    newOption.innerHTML = itemValue;
                    select.appendChild(newOption);
                });
            }            
            container.removeChild(item);
        });
    }
}
</script>

<body>
<a class="back-link" href="menu.php">Back</a>
<div class="model-container">
    <form class="model-item" id="mi-device-type">
        <div class="model-title">Device types:</div>
        <div class="input-container" id="ic-device-type">
            <?php
            foreach($device_types as $type) {
                echo "<div>{$type}</div>";
            }
            ?>
        </div>
        <button class="model-button" type="button" id="mb-add-device-type">+</button>
        <button class="model-button" type="button" id="mb-remove-device-type">-</button>
        <button class="model-button" type="button" id="mb-confirm-device-type">Confirm</button>
    </form>

    <form class="model-item" id="mi-problem-type">
        <div class="model-title">Problem types:</div>
        <div class="input-container" id="ic-problem-type">
            <?php
            foreach($problem_types as $type) {
                echo "<div>{$type}</div>";
            }
            ?>
        </div>
        <button class="model-button" type="button" id="mb-add-problem-type">+</button>
        <button class="model-button" type="button" id="mb-remove-problem-type">-</button>
        <button class="model-button" type="button" id="mb-confirm-problem-type">Confirm</button>
    </form>

    <form class="model-item" id="mi-model">
        <div class="model-title">Models:</div>
        <div class="input-container" id="ic-model">
            <?php
            foreach($models as $index=>$model) {
                echo "
                <div class='model-item-item' id='mi-mii{$index}' data-model='{$model->getName()}'>
                    <div class='model-title'>{$model->getName()}, {$model->getType()}</div>
                    <div class='model-item-problem'>";

                    foreach($model->getProblems() as $problem => $price) {
                        $price = number_format($price, 2, ".", ",");
                        echo "<div>{$problem}, {$price}</div>";
                    }
                echo "</div>";
                // add new problem form
                echo "<button class='model-button' type='button' id='mb-add-mii{$index}'>+</button>
                <button class='model-button' type='button' id='mb-remove-mii{$index}'>-</button>
                <button class='model-button' type='button' id='mb-confirm-mii{$index}'>Confirm</button>";
                // add to model count
                echo "<script>modelCount++</script>";
                echo "</div>";
            }
            ?>
        </div>
        <select class="select-template model-type-select" id="mi-model-type-select">
            <?php
            foreach($device_types as $type) {
                echo "<option value='{$type}'>{$type}</option>";
            }
            ?>
        </select>
        <select class="select-template problem-type-select" id="mi-problem-type-select">
            <?php
            foreach($problem_types as $type) {
                echo "<option value='{$type}'>{$type}</option>";
            }
            ?>
        </select>
        <button class="model-button" type="button" id="mb-add-model">+</button>
        <button class="model-button" type="button" id="mb-remove-model">-</button>
        <button class="model-button" type="button" id="mb-confirm-model">Confirm</button>
    </form>
</div>
</body>
</html>