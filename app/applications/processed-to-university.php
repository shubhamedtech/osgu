<?php
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    if($_SESSION['Role']!='Center'){
      $id = mysqli_real_escape_string($conn, $_POST['id']);
      $id = base64_decode($id);
      $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

      $update = $conn->query("UPDATE Students SET Processed_To_University = now() WHERE ID = $id");
      if($update){

          //The url you wish to send the POST request to
          $url = "https://svu.svu.edu.in/stuservice.asmx/AddStudent?lrI0ZOkQfaAIkFqQwVhaSLekcOlDAL084NuPM3GJEW=";
          $student_data = $conn->query("SELECT * FROM Students WHERE ID = $id");
          $student_acadamic = $conn->query("SELECT * FROM student_academics WHERE Student_ID = $id AND Type = 'Intermediate'");
          // $student_data[];
          while ($student = $student_data->fetch_assoc()) {
              $enquiry_no = is_string($student['Unique_ID']) ? $student['Unique_ID'] : "SVU11112";
              $name = is_string($student['First_Name']) ? $student['First_Name']: "NA";
              $fname = is_string($student['Father_Name']) ? $student['Father_Name'] : "NA";
              $mname = is_string($student['Mother_Name']) ? $student['Mother_Name'] : "NA";
              $email = is_string($student['Email']) ? $student['Email'] : "NA";
              $dob = is_string(date('d/m/Y', strtotime($student['DOB']))) ? date('d/m/Y', strtotime($student['DOB'])) : "NA";
              $adhar = is_string($student['Aadhar_Number']) ? str_replace("-", '', $student['Aadhar_Number']) : "NA";
              $gender = is_string($student['Gender']) ? $student['Gender'] : "NA";
              $marital_status = is_string($student['Marital_Status']) ? $student['Marital_Status'] : "NA";
              $religion = is_string($student['Religion']) ? $student['Religion'] : "NA";
              $address = is_string($student['Address']) ? $student['Address']: "NA";
              $cont_no = is_string($student['Contact']) ? $student['Contact'] : "NA";
              $session = is_string($student['Admission_Session_ID'])? $student['Admission_Session_ID'] : "NA";
              $course = is_string($student['Course_ID']) ? $student['Course_ID'] : "NA";
              $bloodgroup = "NA";
              $ParentAnnualIncome ="NA";
              $MotherToung ="NA";
              $category = is_string($student['Category']) ? $student['Category'] : "NA";
              $city= "NA";
              $states= "NA";
              $country= $student['Nationality'] == "Indian" ? "INDIA" : "NRI";
              $pincode= "NA";
              $lastqualification= "NA";
              $passingyear= "NA";
              $rollno= "NA";
              $percentage= "NA";
              $branch= "NA";
              $courseapi= "NA";
              $CounsellorMode= "NA";
              $CounsellorName= "NA";
              $AdmissionType= "NA";
              $admsem= "NA";
              $stupicpath= "NA";
              $stuhmpath= "NA";
              $key= "NA";
              $admmth= "NA";
              $add_data = array();
              foreach(json_decode($address) as $address_data){
                $add_data[] = $address_data;
              }
          }
          $acadamics =  mysqli_fetch_row($student_acadamic);
          $passin_year = is_string($acadamics[3]) ? $acadamics[3]: "NA";
          $lastqualification = is_string($acadamics[2]) ? $acadamics[2]: "NA";
          $lastqualification_strim = is_string($acadamics[4]) ? $acadamics[4]: "NA";
          $lastqualification_roll = is_string($acadamics[5]) ? $acadamics[5]: "NA";
          $address = is_string($add_data[0]) ? $add_data[0]: "NA";
          $pincode = is_string($add_data[1]) ? $add_data[1]: "NA";
          $city = is_string($add_data[2]) ? $add_data[2]: "NA";
          $district = is_string($add_data[3]) ? $add_data[3]: "NA" ;
          $state = is_string($add_data[4]) ? $add_data[4]: "NA";

          // print_r('key=lrI0ZOkQfaAIkFqQwVhaSLekcOlDAL084NuPM3GJEW&EnquiryNo='.$enquiry_no.'&name='.$name.'&fname='.$fname.'&mname='.$mname.'&gender='.$gender.'&dob='.$dob.'&bloodgroup=&MaritalStatus='.$marital_status.'&ParentAnnualIncome=&MotherToung=&Religion='.$religion.'&Category='.$category.'&ContNo='.$cont_no.'&address='.$address.'&city='.$city.'&states='.$state.'&country='.$country.'&pincode='.$pincode.'&lastqualification='.$lastqualification.'&passingyear='.$passin_year.'&rollno='.$lastqualification_roll.'&percentage=&course='.$lastqualification_strim.'&Session=2023-24&branch=&courseapi=&emailAadharno=&email='.$email.'&Aadharno='.$adhar.'&CounsellorMode=&CounsellorName=&AdmissionType=&admsem=&stupicpath=&stuhmpath=&admmth=');
          $ch = curl_init();
          curl_setopt($ch,CURLOPT_URL, $url);
          curl_setopt($ch,CURLOPT_POST, true);
          curl_setopt($ch,CURLOPT_POSTFIELDS, 'key=lrI0ZOkQfaAIkFqQwVhaSLekcOlDAL084NuPM3GJEW&EnquiryNo='.$enquiry_no.'&name='.$name.'&fname='.$fname.'&mname='.$mname.'&gender='.$gender.'&dob='.$dob.'&bloodgroup=&MaritalStatus='.$marital_status.'&ParentAnnualIncome=&MotherToung=&Religion='.$religion.'&Category='.$category.'&ContNo='.$cont_no.'&address='.$address.'&city='.$city.'&states='.$state.'&country='.$country.'&pincode='.$pincode.'&lastqualification='.$lastqualification.'&passingyear='.$passin_year.'&rollno='.$lastqualification_roll.'&percentage=&course='.$lastqualification_strim.'&Session=2023&branch=&courseapi=&emailAadharno=&email='.$email.'&Aadharno='.$adhar.'&CounsellorMode=&CounsellorName=&AdmissionType=&admsem=&stupicpath=&stuhmpath=&admmth=');
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
          curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
          $result = curl_exec($ch);
         
          if($result){
            echo json_encode(['status'=>200, 'message'=>$result]);
          }
      }else{
        echo json_encode(['status'=>400, 'message'=>'Sorry, Something went wrong!']);
      }
    }else{
      echo json_encode(['status'=>403, 'message'=>'You are not authorized!']);
    }
  }
