<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST,GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

$data = file_get_contents('php://input');
$data = json_decode($data, true);

// DB
require '../../includes/db-config.php';

if (empty($data)) {
  http_response_code(400);
  exit(json_encode(["status" => false, "message" => ['Full Name is required.', 'Phone is required.', 'Course is required.', 'Source is required.','University is required.']]));
}

$error = array();

$name = array_key_exists('name', $data) ? mysqli_real_escape_string($conn, $data['name']) : '';
if (empty($name)) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message'=>'Name is required!']));
}

$mobile = array_key_exists('mobile', $data) ? mysqli_real_escape_string($conn, $data['mobile']) : '';
if (empty($mobile)) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message'=>'Mobile is required!']));
}

// Email
$email = array_key_exists('email', $data) ? mysqli_real_escape_string($conn, $data['email']) : '';
if (empty($email)) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message'=>'Email is required!']));
}

$course = array_key_exists('course', $data) ? mysqli_real_escape_string($conn, $data['course']) : '';
if (empty($course)) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message'=>'Course is required!']));
}

$source = array_key_exists('source', $data) ? mysqli_real_escape_string($conn, $data['source']) : '';
if (empty($source)) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message'=>'Source is required!']));
}

$university = array_key_exists('university', $data) ? mysqli_real_escape_string($conn, $data['university']) : '';
if (empty($university)) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message'=>'University is required!']));
}

$vertical = array_key_exists('vertical', $data) ? mysqli_real_escape_string($conn, $data['vertical']) : '';
if (empty($vertical)) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message'=>'Vertical is required!']));
}


// Mobile
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['status' => false, 'message' => 'Not a valid email!']);
  exit();
}

// Alternate Email
$alternate_email = array_key_exists('alternate_email', $data) ? mysqli_real_escape_string($conn, $data['alternate_email']) : '';
if (!empty($alternate_email)) {
  if (!filter_var($alternate_email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Not a valid alternate email!']);
    exit();
  }
}

// Mobile
if (strlen(filter_var($mobile, FILTER_SANITIZE_NUMBER_INT)) < 10) {
  http_response_code(400);
  echo json_encode(array("status" => false, "message" => "Not a valid mobile!"));
  exit();
}

function validateMobile($mobile){
  return preg_match('/^[6-9]\d{9}$/', $mobile);
}

if(!validateMobile($mobile)){
  http_response_code(400);
  $response = json_encode(['status'=>false, "message"=>"Not a valid mobile!"]);
  exit($response);
}

// Alternate Mobile
$alternate_mobile = array_key_exists('alternate_mobile', $data) ? mysqli_real_escape_string($conn, $data['alternate_mobile']) : '';
if (!empty($alternate_mobile)) {
  if (strlen(filter_var($alternate_mobile, FILTER_SANITIZE_NUMBER_INT)) < 10) {
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "Not a valid alternate mobile!"));
    exit();
  }

  if(!validateMobile($alternate_mobile)){
    http_response_code(400);
    $response = json_encode(['status'=>false, "message"=>"Not a valid alternate mobile!"]);
    exit($response);
  }
}

// University
$university = $conn->query("SELECT ID, ID_Suffix, Max_Character FROM Universities WHERE (Name LIKE '$university' OR Short_Name LIKE '$university') AND Vertical LIKE '$vertical' AND Has_Unique_StudentID = 1");
if ($university->num_rows > 0) {
  $university = $university->fetch_assoc();
  $suffix = $university['ID_Suffix'];
  $characters = $university['Max_Character'];
  $university_id = $university['ID'];
} else {
  http_response_code(404);
  exit(json_encode(["status" => false, "message" => "University not found!"]));
}

// Scheme
$scheme = $conn->query("SELECT Scheme_ID FROM Admission_Sessions WHERE University_ID = $university_id AND Current_Status = 1");
if($scheme->num_rows > 0) {
  http_response_code(400);
  exit(json_encode(['status' => false, 'message' => 'Please configure admission session!']));
}

$scheme = $scheme->fetch_assoc();
$scheme_id = $scheme['Scheme_ID'];

// Course
$course = $conn->query("SELECT ID FROM Courses WHERE (Name LIKE '$course' OR Short_Name LIKE '$course') AND University_ID = $university_id");
if ($course->num_rows == 0) {
  http_response_code(404);
  exit(json_encode(["status" => false, "message" => "Course not found!"]));
}

$course = $course->fetch_assoc();
$course_id = $course['ID'];

// Sub-Course
$sub_course = array_key_exists('sub_course', $data) ? mysqli_real_escape_string($conn, $data['sub_course']) : '';
if (!empty($sub_course)) {
  $sub_course = $conn->query("SELECT ID FROM Sub_Courses WHERE (Name LIKE '$sub_course' OR Short_Name LIKE '$sub_course') AND Course_ID = $course_id AND Scheme_ID = $scheme_id");
  if ($sub_course->num_rows > 0) {
    $sub_course = $sub_course->fetch_assoc();
  } else {
    $sub_course = $conn->query("SELECT ID FROM Sub_Courses WHERE Course_ID = $course_id AND Scheme_ID = $scheme_id AND University_ID = $university_id");
    $sub_course = $sub_course->fetch_assoc();  
  }
}else{
  $sub_course = $conn->query("SELECT ID FROM Sub_Courses WHERE Course_ID = $course_id AND Scheme_ID = $scheme_id AND University_ID = $university_id");
  $sub_course = $sub_course->fetch_assoc();
}

$sub_course_id = $sub_course['ID'];


// Source
$source = $conn->query("SELECT ID FROM Sources WHERE Name LIKE '$source'");
if ($source->num_rows == 0) {
  $source = $conn->query("INSERT INTO Sources (`Name`) VALUES ('$source')");
  $source = $conn->insert_id;
}else{
  $source = $source->fetch_assoc();
  $source = $source['ID'];
}

// Sub-Source
$sub_source = array_key_exists('sub_source', $data) ? mysqli_real_escape_string($conn, $data['sub_source']) : '';
if(!empty($sub_source)){
  $sub_source = $conn->query("SELECT ID FROM Sub_Sources WHERE Name LIKE '$sub_source'");
  if($sub_source->num_rows>0){
    $sub_source = $sub_source->fetch_assoc();
    $sub_source_id = $sub_source['ID'];
  }else{
    $sub_source_id = 'NULL';
  }
}

// Country
$country_id = 'NULL';
$requested_country = array_key_exists('country', $data) ? mysqli_real_escape_string($conn, $data['country']) : '';
if (!empty($requested_country)) {
  $country = $conn->query("SELECT ID FROM Countries WHERE Name LIKE '$requested_country'");
  if ($country->num_rows > 0) {
    $country_id = $country->fetch_assoc();
    $country_id = $country_id['ID'];
  } else {
    $country_id = 'NULL';
  }
}

// State
$state_id = 'NULL';
$requested_state = array_key_exists('state', $data) ? mysqli_real_escape_string($conn, $data['state']) : '';
if (!empty($requested_state)) {
  $state = $conn->query("SELECT ID FROM States WHERE Name LIKE '$requested_state'");
  if ($state->num_rows > 0) {
    $state_id = $state->fetch_assoc();
    $state_id = $state_id['ID'];
  } else {
    $state_id = 'NULL';
  }
}

// City
$city_id = 'NULL';
$requested_city = array_key_exists('city', $data) ? mysqli_real_escape_string($conn, $data['city']) : '';
if (!empty($requested_city)) {
  $city = $conn->query("SELECT ID FROM Cities WHERE Name LIKE '$requested_city'");
  if ($city->num_rows > 0) {
    $city_id = $city->fetch_assoc();
    $city_id = $city_id['ID'];
  } else {
    $city_id = 'NULL';
  }
}

// Stage
$stage = $conn->query("SELECT ID FROM Stages WHERE Is_First = 1");
$stage = $stage->fetch_assoc();
$stage = $stage['ID'];

// Re-Enquired Stage
$re_enquired_stage = $conn->query("SELECT ID FROM Stages WHERE Is_ReEnquired = 1");
$re_enquired_stage = $re_enquired_stage->fetch_assoc();
$re_enquired_stage = $re_enquired_stage['ID'];

// Final Stage
$final_stage = $conn->query("SELECT ID FROM Stages WHERE Is_Final = 1");
$final_stage = $final_stage->fetch_assoc();
$final_stage = $final_stage['ID'];

// Final Stage


// Reason
$reason = $conn->query("SELECT ID FROM Reasons WHERE Stage_ID = $stage");
if ($reason->num_rows > 0) {
  $reason = $reason->fetch_assoc();
  $reason = $reason['ID'];
} else {
  $reason = 'NULL';
}

// Checks

// Check Mobile & Email
$check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Email LIKE '$email' AND Leads.Mobile = '$mobile' AND Lead_Status.University_ID = $university_id");
if($check->num_rows>0){
  exit(json_encode(['status'=>false, 'message'=>'Email and Mobile already exist!']));
}

// Check Mobile
$check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Mobile = '$mobile' AND Lead_Status.University_ID = $university_id");
if($check->num_rows>0){
  exit(json_encode(['status'=>false, 'message'=>'Mobile already exist!']));
}

// Check Email
$check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Email LIKE '$email' AND Lead_Status.University_ID = $university_id");
if($check->num_rows>0){
  exit(json_encode(['status'=>false, 'message'=>'Email already exist!']));
}

// Add
$student_id = generateStudentID($conn, $suffix, $characters, $university_id);


