<?php include('../../includes/header-top.php'); ?>
<?php include('../../includes/header-bottom.php'); ?>
<?php include('../../includes/menu.php'); ?>
    <!-- START PAGE-CONTAINER -->
    <div class="page-container ">
    <?php include('../../includes/topbar.php'); ?>      
      <!-- START PAGE CONTENT WRAPPER -->
      <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
          <!-- START JUMBOTRON -->
          <div class="jumbotron" data-pages="parallax">
            <div class=" container-fluid sm-p-l-0 sm-p-r-0">
              <div class="inner">
                <!-- START BREADCRUMB -->
                <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                  <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                    for($i=1; $i<=count($breadcrumbs); $i++) {
                      if(count($breadcrumbs)==$i): $active = "active";
                        $crumb = explode("?", $breadcrumbs[$i]);
                        echo '<li class="breadcrumb-item '.$active.'">'.$crumb[0].'</li>';
                      endif;
                    }
                  ?>
                  <div>
                    <?php if($_SESSION['Role']=='Administrator'){ ?>
                      <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload ID Card" onclick="uploadIDCard()"> <i class="uil uil-upload"></i></button>
                    <?php } ?>
                  </div>
                </ol>
                <!-- END BREADCRUMB -->
              </div>
            </div>
          </div>
          <!-- END JUMBOTRON -->
          <!-- START CONTAINER FLUID -->
          <div class=" container-fluid">
            <!-- BEGIN PlACE PAGE CONTENT HERE -->
            <div class="row">
              <div class="col-md-12">
                <div class="pull-right">
                  <div class="col-xs-12">
                    <input type="text" id="search-table" class="form-control pull-right" placeholder="Search">
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="table-responsive">
                  <table class="table table-hover nowrap" id="tableWithSearch">
                    <thead>
                      <tr>
                        <th data-orderable="false">Photo</th>
                        <th>Lot No.</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Father Name</th>
                        <th>Center</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        $role_query = str_replace('{{ table }}', 'Himalayan_ID_Cards', $_SESSION['RoleQuery']);
                        $role_query = str_replace('{{ column }}', 'Center_Code', $role_query);
                        $lists = $conn->query("SELECT Himalayan_ID_Cards.*,CONCAT(Users.Name, ' (', Users.Code, ')') as Center FROM Himalayan_ID_Cards LEFT JOIN Users ON Himalayan_ID_Cards.Center_Code = Users.ID WHERE Himalayan_ID_Cards.ID IS NOT NULL $role_query");
                        while($list = $lists->fetch_assoc()){ ?>
                          <tr>
                            <td><img src="images/<?=$list['Photo']?>" width="32" height="32" class="thumbnail-wrapper d48 circular inline"></td>
                            <td><?=$list['Lot']?></td>
                            <td><?=$list['Student_ID']?></td>
                            <td><?=$list['Name']?></td>
                            <td><?=$list['Father_Name']?></td>
                            <td><?=$list['Center']?></td>
                            <td>
                              <a href="cards?student_id=<?php echo base64_encode($list['ID'])?>" target="_blank"><i class="uil uil-down-arrow icon-xs-right"></i></a>
                              <?php if($_SESSION['Role']=='Administrator'){
                                echo '<i class="uil uil-edit icon-xs-right cursor-pointer" onclick="editIDCard('.$list['ID'].')"></i>';
                                echo '<i class="uil uil-trash cursor-pointer" onclick="deleteIDCard('.$list['ID'].')"></i>';
                              } ?>
                            </td>
                          </tr>
                      <?php }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- END PLACE PAGE CONTENT HERE -->
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
<?php include('../../includes/footer-top.php'); ?>
<script type="text/javascript">
  function uploadIDCard(){
    $.ajax({
      url: '/id-cards/himalayan/uploads/create',
      type:'GET',
      success: function(data) {
        $('#md-modal-content').html(data);
        $('#mdmodal').modal('show');
      }
    })
  }
</script>
<script type="text/javascript">
  function editIDCard(id){
    $.ajax({
      url: '/id-cards/himalayan/edit?id='+id,
      type:'GET',
      success: function(data) {
        $('#lg-modal-content').html(data);
        $('#lgmodal').modal('show');
      }
    });
  }

  function deleteIDCard(id){
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/id-cards/himalayan/destroy?id="+id,
          type: 'DELETE',
          dataType: 'json',
          success: function(data) {
            if(data.status==200){
              notification('success', data.message);
              $('.modal').modal('hide');
              window.location.reload(true);
            }else{
              notification('danger', data.message);
            }
          }
        });
      }
    })
  }
</script>
<?php include('../../includes/footer-bottom.php'); ?>
        