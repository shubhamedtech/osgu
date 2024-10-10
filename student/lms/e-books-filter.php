
<?php
    require '../../includes/db-config.php';
    
    $base_url="https://".$_SERVER['HTTP_HOST']."/";
    $course_id=intval($_POST['course_id']);
    if (isset($_POST['subject_id']) ) {
        $subject_id = intval($_POST['subject_id']);
       
        $query="SELECT e_books.`id`, e_books.`file_type`, e_books.`file_path`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, e_books.`status` FROM e_books LEFT JOIN Sub_Courses ON Sub_Courses.ID = e_books.course_id LEFT JOIN Syllabi ON Syllabi.ID = e_books.subject_id WHERE e_books.subject_id =$subject_id  AND e_books.status !=2 AND e_books.course_id=$course_id AND e_books.status=1";

        $results = mysqli_query($conn, $query);
        $eBookData=array();
        $htmlReturnData="";
        while ($eBook = mysqli_fetch_assoc($results)) {
          $eBookData[]= $eBook;
          $htmlReturnData = $htmlReturnData.'<div class="col-sm-6 col-md-3 mb-3"><div class="stu-e-book-style"><p><i class="uil uil-book-open e-book-icon"></i></p><p class="subject_name"><span>'.$eBook['subject_name'].'</span></p></div><p class="mt-2" style="text-align:center"><a class="btn btn-dark" href="/student/lms/view-e-book?id='.$eBook['id'].'" >View</a></p></div>';
          //$htmlReturnData = $htmlReturnData.'<div class="col-sm-6 col-md-3 mb-3"><div><iframe style="width:100%" src="'.$base_url.$eBook['file_path'].' "></iframe></div><p class="mt-2" style="text-align:center"><a class="btn btn-dark" target="_blank" href="'.$base_url.$eBook['file_path'].'">View</a></p></div>';
        }
        if(count($eBookData)>0){
            echo $htmlReturnData; die;
        }else{
            echo  false; die;
        }
        
        //echo json_encode(["status"=>200,"message"=>"Data loaded successfully!","data"=>$htmlReturnData]); 
        //die;
    }

?>






