<?php
ini_set('display_errors', 1);
if (isset($_POST['course_id']) && isset($_POST['semester_id'])) {
    require '../../../../includes/db-config.php';
    session_start();

    $sub_course_id = intval($_POST['course_id']);
    $semester = intval( $_POST['semester_id']);
    $scheme = intval( $_POST['scheme_id']);;
    // $semester = $semester[1];

    $syllabus_ids = '';
    $codes = $conn->query("SELECT ID FROM Syllabi WHERE Sub_Course_ID = " . $sub_course_id . " AND Semester = " . $semester . " AND Scheme_ID = " . $scheme . "");
    if ($codes->num_rows > 0) {
        while ($row = $codes->fetch_assoc()) {
        $syllabus_ids = $row['ID'];
        }

        $date_sheets = $conn->query("SELECT Date_Sheets.*, Exam_Sessions.Name as Exam_Session, Syllabi.Sub_Course_ID as Sub_Course_ID, Syllabi.Name, Syllabi.ID as Syllb_ID, Syllabi.Code FROM Date_Sheets LEFT JOIN Syllabi ON Date_Sheets.Syllabus_ID = Syllabi.ID LEFT JOIN Exam_Sessions ON Date_Sheets.Exam_Session_ID = Exam_Sessions.ID WHERE Syllabus_ID = '". $syllabus_ids ."' ORDER BY Exam_Date ASC");
        if ($date_sheets->num_rows == 0) {
        echo '<center><h1>Results Not Available</h1></center>';
        } else {
            while ($row = mysqli_fetch_assoc($date_sheets)) {
                $data[] = array( 
                  "Exam_Session" => $row["Exam_Session"],
                  "Code" => $row["Code"],
                  "Name" => $row["Name"],
                  "Date"  => date("l, dS M, Y", strtotime($row['Exam_Date'])),
                  "Time" => date("h:i A", strtotime($row['Start_Time'])) . " to " . date("h:i A", strtotime($row['End_Time'])),
                  "View Result" => date("h:i A", strtotime($row['Start_Time'])) . " to " . date("h:i A", strtotime($row['End_Time'])),
                  "Syllb_ID" => $row["Syllb_ID"],
                  "Sub_Course_ID" => $row["Sub_Course_ID"],
                  "ID" => $row["ID"],
                );
            }
            
            ## Response
            $response = array(
            //   "draw" => intval($draw),
            //   "iTotalRecords" => $totalRecords,
            //   "iTotalDisplayRecords" => $totalRecordwithFilter,
              "aaData" => $data
            );
            echo json_encode($response);
        }
    }
}
?>
