<?php

// Create a new assignment and add it to the `assignments` database
function create_assignment($class_id, $title, $post_date, $due_date, $desc, $duration = null) {
    global $db_connection;

    // Fail-safe
    if ($class_id == '' || $title == '' || $due_date == '')
        return false;

    // Inset the new assignment into the `assignments` database
    $db_connection->exec("INSERT INTO `assignments`
    ( `class_id`, `title`, `desc`, `post_date`, `due_date`, `duration` )
    VALUES
    ( '$class_id', '$title', '$desc', '$post_date', '$due_date', '$duration' )");

    // If we made it this far we were successful
    return true;
}

// Gets all of the assignments for a given class
function get_assignments($class_id) {
    global $db_connection;

    // Query 'assignmnets' for entrys with this this class_id
    $query = $db_connection->query("SELECT * FROM `assignments` WHERE `class_id`='$class_id' ORDER BY `due_date`;");
    $assignments = $query->fetchAll(PDO::FETCH_ASSOC);

    // Return the array to caller
    return $assignments;
}

// Get the information for a given assignment
function get_assignment($assg_id) {
    global $db_connection;

    // Query 'assignmnets' for entrys with this this assg_id
    $query = $db_connection->query("SELECT * FROM `assignments` WHERE `assg_id`='$assg_id';");
    $assignment = $query->fetchAll(PDO::FETCH_ASSOC)[0];

    // Return the array to caller
    return $assignment;
}

// Deletes given assignment
function remove_assignment($assg_id) {
    global $db_connection;

    // Get assignment info
    $assg_info = get_assignment($assg_id);

    // Get the current user's role for the assignment's class
    $role = get_user_role($_SESSION['user_id'], $assg_info['class_id']);

    // If user is the owner remove the class, otherwise just remove the relation
    switch($role) {
        case 'owner': {
            $db_connection->exec("DELETE FROM `assignments` WHERE `assg_id`='$assg_id'");
            return true;
        }
    }

    // If we made it this far we were not successful (should end in switch statment)
    return false;
}

// Marks the assignment as complete for given user
function finish_assignment($assg_id) {
    global $db_connection;

    // Fail-safe
    if ($assg_id == '' || $_SESSION['user_id'])
        return false;

    // Get current user id
    $user_id = $_SESSION['user_id'];

    // Inset the new class into the `classes` database
    $db_connection->exec("INSERT INTO `assignments-cmpl`
    ( `assg_id`, `user_id`, `timestamp` )
    VALUES
    ( '$assg_id', '$user_id', date('Y-m-d H:i:s'))");

    // If we made it this far we were successful
    return true;
}
