<?php
  if(isset($_POST['data']) 
    && is_array($_POST['data']) 
    && array_key_exists('details', $_POST['data'])
    && array_key_exists('lead_owner', $_POST['data'])
  ){
    session_start();

    $details = $_POST['data']['details'];
    $lead_owner = $_POST['data']['lead_owner'];

    $_SESSION['lead_details'] = $details;
    
    $full_name = $details['full_name'];
    $contact = json_decode($details['contact'], true);
    $education = json_decode($details['education'], true);
    $student_id = $details['student_id'];
    $extra = json_decode($details['lead_information'], true);
    $owner_id = $lead_owner['employee_id'];
    $owner_name = $lead_owner['name'];
    $owner_email = $lead_owner['email'];

    if($extra['employment_status']!='Government Job'){
      $_SESSION['full_name'] = $full_name;
      $_SESSION['email'] = $contact['email'];
      $_SESSION['phone'] = $contact['phone'];
      $_SESSION['course'] = $education['course'];
      $_SESSION['specialization'] = $education['specialization'];
      if(array_key_exists('lead_source', $extra)){
        $_SESSION['course_key'] = $extra['course_key'];
        $_SESSION['specialization_key'] = $extra['specialization_key'];
      }else{
        $_SESSION['course_key'] = $extra['course'];
        $_SESSION['specialization_key'] = $extra['specialization'];
      }
      $_SESSION['student_id'] = $student_id;
      if(array_key_exists('lead_source', $extra)){
        $_SESSION['step'] = $extra['step']<4 ? $extra['step']+1 : $extra['step'];
      }else{
        $_SESSION['step'] = $extra['step']==1 ? 1 : $extra['step']+1;
      }
      $_SESSION['owner_name'] = $owner_name;
      $_SESSION['owner_email'] = $owner_email;
      $_SESSION['owner_id'] = $owner_id;

      

      if(!isset($_SESSION['loggedin_mail'])){
        $loggged_in_url = 'https://universityadmission.co.in/email-templates/student/logged-in?key=a96a4044-a008-4434-a191-473d105e55b5&student_id='.$_SESSION['student_id'].'&student_name='.$_SESSION['full_name'].'&counsellor_email='.$_SESSION['owner_email'].'&counsellor_name='.$_SESSION['owner_name'].'&type=Logged%20In&course='.$_SESSION['course'].' ('.$_SESSION['specialization'].')&student_contact='.$_SESSION['phone'].'&step='.$_SESSION['step'];
        $loggged_in_url = str_replace(' ', '%20', $loggged_in_url);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $loggged_in_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'x-api-key: d322b681-a561-4bd6-bd31-4c5e23d84dee'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $_SESSION['loggedin_mail'] = 1;
      }

      if($_SESSION['step']>2){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://universityadmission.co.in/api/students/fetch?key=a96a4044-a008-4434-a191-473d105e55b5&student_id='.$student_id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $_SESSION['erp_data'] = json_decode($response, true);
      }

      echo json_encode(['status'=>true, 'message'=>'Success']);
    }else{
      echo json_encode(['status'=>false, 'message'=>'Your enquiry submitted successfully!']);
      session_destroy();
    }
  }
