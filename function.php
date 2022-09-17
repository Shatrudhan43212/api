<?php
//error_reporting(0);
header("Content-type:application/json");
$conn = mysqli_connect("localhost", "root", "", "test") or die("Connection is failed. " . mysqli_connect_error());
$jsonToArray = json_decode(file_get_contents('php://input'), true);
$keys = array_keys($jsonToArray);
$values = array_values($jsonToArray);
// $jsonData = json_encode(["name" => "Vikash kumar", "email" => "viskah@gmail.com", "mobile" => "9638587410"]);
// $jsonToArray = json_decode($jsonData, true);   
// print_r($jsonToArray) ;die;
// die;

if($_SERVER['REQUEST_METHOD'] === 'POST'){ if (isset($_GET['insert'])) { InsertData($keys, $values, $conn); } }
elseif($_SERVER['REQUEST_METHOD'] === 'GET'){ if (isset($_GET['fetch'])) { FetchData(trim($_GET['fetch']), $conn); } }
elseif($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH'){ if (isset($_GET['update_user_id'])) { UpdateData(trim($_GET['update_user_id']), $keys, $values, $conn); } }
elseif($_SERVER['REQUEST_METHOD'] === "DELETE"){ if (isset($_GET['delete_user_id'])) { DeleteUser(trim($_GET['delete_user_id']), $conn); } }
else { echo json_encode(['status' => 'error', 'message' => 'unauthorized access!']); die(); }

// Insert function
function InsertData($keys, $values, $conn)
{
    if (!empty($keys) && !empty($values)) {
        $alreadyExists = $conn->query("SELECT id FROM users WHERE email = '" . $keys[1] . "' OR mobile = '" . $values[2] . "'")->num_rows;
        if ($alreadyExists > 0) {
            echo json_encode(['status' => 'Failed', 'message' => 'Email address or Mobile Number already registred!']);
        } else {
            $sql = "INSERT INTO users (" . implode(', ', $keys) . ") " . "VALUES ('" . implode("', '", $values) . "')";
            $results = $conn->query($sql);
            if ($results == true && $results > 0) { echo json_encode(['status' => 'Success', 'message' => 'Data Inserted Successfully!']); } 
            else { echo json_encode(['status' => 'Failed', 'message' => 'Data Not Inserted!']); }
        }
    }
}

// Update function
function UpdateData($user_id, $key, $values, $conn)
{
    if (!empty($keys) && !empty($values)) {
        $alreadyExists = $conn->query("SELECT id FROM users WHERE id = '" . $user_id . "'")->num_rows;
        if ($alreadyExists > 0) {
            $sqlArr = [];
            foreach ($jsonToArray as $key => $val) { $sqlArr[] = "$key = '$val'";  }
            $updateQuery = "UPDATE users set " . implode(', ', $sqlArr) . " WHERE id = '" . $user_id . "'";
            $results = $conn->query($updateQuery);
            if ($results == true && $results > 0) { echo json_encode(['status' => 'Success', 'message' => 'Data Updated Successfully!']); } 
            else { echo json_encode(['status' => 'Failed', 'message' => 'Data Not Updated!']); }
        } 
        else { echo json_encode(['status' => 'Failed', 'message' => 'User id not found!']); }
    }
}

// Delete Function
function DeleteUser($user_id, $conn)
{
    if (!empty($keys) && !empty($values)) {
        $alreadyExists = $conn->query("SELECT id FROM users WHERE id = '" . $user_id . "'")->num_rows;
        if ($alreadyExists > 0) {
            $deleteQuery = "DELETE FROM users WHERE id = '" . $user_id . "'";
            $results = $conn->query($deleteQuery);
            if ($results == true && $results > 0) { echo json_encode(['status' => 'Success', 'message' => 'Data Deleted Successfully!']); } 
            else { echo json_encode(['status' => 'Failed', 'message' => 'Data Not Deleted!']); }
        } 
        else { echo json_encode(['status' => 'Failed', 'message' => 'User id not found!']); }
    }
}

// Fetch single or all rows function
function FetchData($user_id, $conn)
{
    $query = "SELECT * FROM users";
    if(!empty($user_id)){ $query .= " WHERE id = '" . $user_id . "'"; }
    if ($conn->query($query)->num_rows > 0) {
        $results = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
        if ($results == true && $results > 0) { echo json_encode(['status' => 'Success', 'data' => $results]); }
    } 
    else { echo json_encode(['status' => 'Failed', 'data' => 'Data Not Found']); }
}

?>
