<?php
// generate objects eg. Model regardless of which page is first loaded
require_once "Model.php";
require_once "SelectQuery.php";
require_once "ChangeQuery.php";
require_once "Connection.php";
require_once "splitArray.php";

$connection = Connection::getConnection();

// generate model objects
$models = array();

$device_type_query = new SelectQuery("SELECT device_type FROM device_types;", array(), $connection);
$device_types = $device_type_query->performQuery();

$model_query = new SelectQuery("SELECT device_model FROM device_models 
                                    INNER JOIN device_types 
                                        ON device_types.device_type_id = device_models.device_type 
                                            WHERE device_types.device_type = ?;", array(), $connection);    // modifiable parameter

                                            
$model_problem_query = new SelectQuery("SELECT problem_types.problem_name, problems.price 
FROM problems 
    INNER JOIN problem_types 
        ON problem_types.problem_type_id = problems.problem_type 
    INNER JOIN device_models
        ON problems.model = device_models.device_model_id
            WHERE device_models.device_model = ?;", array(), $connection);  // modifiable parameter
$model_problem_column_count = $connection->field_count;

$problem_type_query = new SelectQuery("SELECT problem_name FROM problem_types;", array(), $connection);
$problem_types = $problem_type_query->performQuery();

// O(n^3) kek
foreach($device_types as $type) {
    // create model objects for each device type
    $model_query->setParameters(array($type));
    $model_result = $model_query->performQuery();

    foreach($model_result as $model_text) {
        $new_model = new Model($model_text, $type);
        $models[] = $new_model;

        // add problems to newly created model objects
        $model_problem_query->setParameters(array($model_text));
        $model_problem_result = $model_problem_query->performQuery();
        // split into rows
        $model_problem_rows = splitArray($model_problem_result, $model_problem_column_count);
        foreach($model_problem_rows as $row) {
            $new_model->addProblem($row[0], $row[1]);
        }
    }
}



?>