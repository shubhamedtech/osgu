let key = 'a96a4044-a008-4434-a191-473d105e55b5';
let crm_url = 'https://collegevidya.edutra.io/api/l2l/'

// Loader
function showLoader(message){
  HoldOn.open({
    theme:'sk-folding-cube',
    message:message
  });
}

function hideLoader(){
  HoldOn.close();
}


// Active Session Sub Course
function getSubCourse(course, id){
  $('#'+id+'_specialization').html('');
  $('#'+id+'_specialization').append('<option value="" selected="" disabled="">Select Specialization</option>');
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/sub_courses?course='+course,
    type:'GET',
    dataType:'json',
    success: function(data){
      Object.keys(data).forEach(function(k){
        $('#'+id+'_specialization').append('<option value="'+k+'">'+data[k]+'</option>');
      });
    }
  })
}

// Number Key Active Only
function isNumberKey(evt){
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
  return true;
}

// Check Email
function checkEmail(email, id){
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)){
    var form = new FormData();
    form.append("key", key);
    form.append("email", email);
    $.ajax({
      url: crm_url+'check-email',
      method: "POST",
      timeout: 0,
      processData: false,
      contentType: false,
      data:form,
      dataType: 'json',
      success: function(response){
        if(response.status){
          $('#'+id+'_email_error').html('Email already exists!');
        }else{
          $('#'+id+'_email_error').html('');
        }
      }
    })
  }else{
    $('#'+id+'_email_error').html('');
  }
}

// check Phone
function checkPhone(phone, id){
  var phoneno = /^\d{10}$/;
  if(phone.match(phoneno) && phone.length==10){
    var form = new FormData();
    form.append("key", key);
    form.append("phone", phone);
    $.ajax({
      url: crm_url+'check-phone',
      method: "POST",
      timeout: 0,
      processData: false,
      contentType: false,
      data:form,
      dataType: 'json',
      success: function(response){
        if(response.status){
          $('#'+id+'_phone_error').html('Mobile No. already exists!');
        }else{
          $('#'+id+'_phone_error').html('');
        }
      }
    })    
  }else{
    $('#'+id+'_phone_error').html('');
  }
}

$("form#register_form").submit(function(e) {
  showLoader('Please wait...');
  $(':input[type="submit"]').prop('disabled', true);
  e.preventDefault();    
  var formData = new FormData(this);
  formData.append("institute", key);
  formData.append("source", "Website");
  formData.append("step", "0");
  formData.append("lead_source", "website");
  $.ajax({
    url: crm_url+'store',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(data){
      hideLoader();
      if(data.status){
        $('#register_form')[0].reset();
        $(':input[type="submit"]').prop('disabled', false);
        if(formData.get('employment_status')!='Government Job'){
          sendLoginCredentials(data);
          openApplicationForm(data);
        }else{
          Swal.fire(
            'Thank You!',
            'Our counsellor will contact you soon!',
            'success'
          )
        }
      }else{
        $(':input[type="submit"]').prop('disabled', false);
        Toast.fire({
          icon: 'error',
          title: data.message,
        })
      }
    },
    cache: false,
    contentType: false,
    processData: false
  });
});

function sendLoginCredentials(data){
  $.ajax({
    url: '/application-form/send/login-credentials',
    type: 'POST',
    data: {data, key},
    success: function(response){
      console.log(response);
    }
  })
}

$("form#login_form").submit(function(e) {
  $(':input[type="submit"]').prop('disabled', true);
  e.preventDefault();
  var formData = new FormData(this);
  formData.append("key", key);
  $.ajax({
    url: crm_url+'login',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(data){
      if(data.status){
        $('.modal').modal('hide');
        $('#login_form')[0].reset();
        Toast.fire({
          icon: 'success',
          title: data.message,
        })
        setTimeout(function() {
          openApplicationForm(data)
        }, 1000);
      }else{
        $(':input[type="submit"]').prop('disabled', false);
        Toast.fire({
          icon: 'error',
          title: data.message,
        })
      }
    },
    cache: false,
    contentType: false,
    processData: false
  });
});

// Application Form
function openApplicationForm(data){
  $.ajax({
    url:'/application-form/set-session',
    type:'POST',
    data: {data:data},
    dataType: 'json',
    success: function(response){
      if(response.status){
        window.location.href="/application-form"
      }else{
        Swal.fire(
          'Thank You!',
          'Our counsellor will contact you soon!',
          'success'
        )
      }
    }
  })
}

// Regions
function getRegions(pincode, id){
  $('#'+id+'_city').html('');
  $('#'+id+'_city').append('<option value="">Select</option>');
  $('#'+id+'_district').html('');
  $('#'+id+'_district').append('<option value="">Select</option>');
  $('#'+id+'_state').val('');
  if(pincode.length==6){
    $.ajax({
      url:'https://universityadmission.co.in/api/regions?pincode='+pincode,
      type:'GET',
      dataType: 'json',
      success: function(response){
        response.city.forEach(element => 
          $('#'+id+'_city').append('<option value="'+element+'">'+element+'</option>')
        );
        response.district.forEach(element => 
          $('#'+id+'_district').append('<option value="'+element+'">'+element+'</option>')
        );
        response.state.forEach(element => 
          $('#'+id+'_state').val(element)
        );
      }
    })
  }
}

// Set Same as Permanent
function setCorrspondenceAsPermanent(){
  var value = $('#set_address').prop('checked');
  var pincode = $('#permanent_pincode').val();
  var id = 'correspondence';
  $('#'+id+'_address').val('');
  $('#'+id+'_pincode').val('');
  $('#'+id+'_city').html('');
  $('#'+id+'_city').append('<option value="">Select</option>');
  $('#'+id+'_district').html('');
  $('#'+id+'_district').append('<option value="">Select</option>');
  $('#'+id+'_state').val('');
  if(value){
    if(pincode.length==6){
      $.ajax({
        url:'https://universityadmission.co.in/api/regions?pincode='+pincode,
        type:'GET',
        dataType: 'json',
        success: function(response){
          response.city.forEach(element => 
            $('#'+id+'_city').append('<option value="'+element+'">'+element+'</option>')
          );
          response.district.forEach(element => 
            $('#'+id+'_district').append('<option value="'+element+'">'+element+'</option>')
          );
          response.state.forEach(element => 
            $('#'+id+'_state').val(element)
          );
          $('#'+id+'_address').val($('#permanent_address').val());
          $('#'+id+'_pincode').val(pincode);
          $('#'+id+'_city').val($('#permanent_city').val());
          $('#'+id+'_district').val($('#permanent_district').val());
        }
      })
    }
  }
}


// Aadhar Validator
// multiplication table d
var d=[
  [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
  [1, 2, 3, 4, 0, 6, 7, 8, 9, 5], 
  [2, 3, 4, 0, 1, 7, 8, 9, 5, 6], 
  [3, 4, 0, 1, 2, 8, 9, 5, 6, 7], 
  [4, 0, 1, 2, 3, 9, 5, 6, 7, 8], 
  [5, 9, 8, 7, 6, 0, 4, 3, 2, 1], 
  [6, 5, 9, 8, 7, 1, 0, 4, 3, 2], 
  [7, 6, 5, 9, 8, 2, 1, 0, 4, 3], 
  [8, 7, 6, 5, 9, 3, 2, 1, 0, 4], 
  [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
];
// permutation table p
var p=[
  [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], 
  [1, 5, 7, 6, 2, 8, 3, 0, 9, 4], 
  [5, 8, 0, 3, 7, 9, 6, 1, 4, 2], 
  [8, 9, 1, 6, 0, 4, 3, 5, 2, 7], 
  [9, 4, 5, 3, 1, 2, 6, 8, 7, 0], 
  [4, 2, 8, 6, 5, 7, 3, 9, 0, 1], 
  [2, 7, 9, 3, 8, 0, 6, 4, 1, 5], 
  [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
];
// inverse table inv
var inv = [0, 4, 3, 2, 1, 5, 6, 7, 8, 9];
// converts string or number to an array and inverts it
function invArray(array){
  
  if (Object.prototype.toString.call(array) == "[object Number]"){
    array = String(array);
  }
  
  if (Object.prototype.toString.call(array) == "[object String]"){
    array = array.split("").map(Number);
  }
  
  return array.reverse();
  
}
// generates checksum
function generate(array){
    
  var c = 0;
  var invertedArray = invArray(array);
  
  for (var i = 0; i < invertedArray.length; i++){
    c = d[c][p[((i + 1) % 8)][invertedArray[i]]];
  }
  
  return inv[c];
}
// validates checksum
function validate(array) {
  var c = 0;
  var invertedArray = invArray(array);
  
  for (var i = 0; i < invertedArray.length; i++){
    c=d[c][p[(i % 8)][invertedArray[i]]];
  }
  return (c === 0);
}

function validateAadhar(adhar) {
  adhar = adhar.replace(/-/g, '');
  //pretty dumb but the easiest solution to know if the number is 12 digit or not :)
  if (adhar >= 100000000000 && adhar <= 999999999999) {
    
    if(validate(adhar) == false) {
      $('#aadhar_number_error').html("Invalid Aadhar Number");
      return false;
    }
    else {
      $('#aadhar_number_success').html("Valid Aadhar Number");
      return true;
    }
  }
  else {
    $('#aadhar_number_error').html("");
    $('#aadhar_number_success').html("");
    return false;
  }
}

function submitValidateAadhar(adhar) {
  if(adhar.length>0){
    adhar = adhar.replace(/-/g, '');
    //pretty dumb but the easiest solution to know if the number is 12 digit or not :)
    if (adhar >= 100000000000 && adhar <= 999999999999) {
      
      if(validate(adhar) == false) {
        return false;
      }
      else {
        return true;
      }
    }
    else {
      return false;
    }
  }
}
// Aadhar Validation Ends

// Age Calculator
function getAge(dob) {
  var dateString = dob.split("-").reverse().join("-");
  var today = new Date();
  var birthDate = new Date(dateString);
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
  }
  $("#age").val(age);
}

if($('#step_1_form').length>0){
  $('#step_1_form').validate({
    rules: {
      full_name: {required: true },
      father_name: {required: true},
      mother_name: {required: true},
      dob: {required: true},
      gender: {required: true},
      category: {required: true},
      email: {required: true},
      phone: {required: true},
      permanent_address: {required: true},
      permanent_pincode: {required: true},
      permanent_city: {required: true},
      permanent_district: {required: true},
      permanent_state: {required: true},
      correspondence_address: {required: true},
      correspondence_pincode: {required: true},
      correspondence_city: {required: true},
      correspondence_district: {required: true},
      correspondence_state: {required: true},
      aadhar_number: {required: true,
        minlength:14
      },
    },
    messages:{
      aadhar_number:{
        minlength: "Minimum 12 characters."
      },
    },
    highlight: function(element) {
      $(element).addClass('error');
      $(element).closest('.form-control').addClass('has-error');
    },
    unhighlight: function(element) {
      $(element).removeClass('error');
      $(element).closest('.form-control').removeClass('has-error');
    }
  });
}

$("form#step_1_form").submit(function(e) {
  e.preventDefault();
  var formData = new FormData(this);
  formData.append("student_id", $('#unique_student_id').text());
  formData.append("step", "1");
  formData.append("lead_source", "website");
  var aadhar_number = $('#aadhar_number').val();
  if(!submitValidateAadhar(aadhar_number)){
    alert("Invalid Aadhar Number!");
    return false;
  }
  if($("form#step_1_form").valid()){
    showLoader('Please wait...');
    $(':input[type="submit"]').prop('disabled', true);
    $.ajax({
      url: crm_url+'update',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(data){
        if(data.status){
          openApplicationForm(data);
        }else{
          $(':input[type="submit"]').prop('disabled', false);
          Toast.fire({
            icon: 'error',
            title: data.message,
          })
        }
      },
      cache: false,
      contentType: false,
      processData: false
    });
  }
});

$(function() {
  if($('#admission_session').length>0){
    $.ajax({
      url:'https://universityadmission.co.in/api/settings/admission_session?key='+key,
      type: 'GET',
      dataType: 'json',
      success: function(data){
        Object.keys(data).forEach(function(element){
          $('#admission_session').append('<option value="'+element+'">'+data[element]+'</option>');
        });
        getCurrentSession();
      }
    });

    function getCurrentSession(){
      $.ajax({
        url:'https://universityadmission.co.in/api/settings/current_admission_session?key='+key,
        type: 'GET',
        dataType: 'json',
        success: function(data){
          Object.keys(data).forEach(function(element){
            $('#admission_session').val(data[element]);
          });
          getAdmissionTypes();
        }
      });
    }
  }
});

function getAdmissionTypes(){
  var admission_session = $('#admission_session').val();
  $('#admission_type').html('');
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/admission_type?session='+admission_session,
    type: 'GET',
    dataType: 'json',
    success: function(data){
      Object.keys(data).forEach(function(element){
        $('#admission_type').append('<option value="'+element+'">'+data[element]+'</option>');
      });
      getSelectedAdmissionType();
      hideFee();
    }
  });
}

function getSelectedAdmissionType(){
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/course_admission_type?course='+course_key,
    type: 'GET',
    dataType: 'json',
    success: function(data){
      Object.keys(data).forEach(function(element){
        $('#admission_type').val(data[element]);
      });
      getCourse();
      hideFee();
    }
  });
}

function getCourse(){
  var admission_session = $('#admission_session').val();
  var admission_type = $('#admission_type').val();
  $('#course').html('');
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/course?session='+admission_session+'&type='+admission_type,
    type: 'GET',
    dataType: 'json',
    success: function(data){
      Object.keys(data).forEach(function(element){
        $('#course').append('<option value="'+element+'">'+data[element]+'</option>');
      });
      $('select[name^="course"] option[value="'+course_key+'"]').attr("selected","selected");
      getSpecialization();
      hideFee();
    }
  });
}

function getSpecialization(){
  var admission_session = $('#admission_session').val();
  var admission_type = $('#admission_type').val();
  var course = $('#course').val();
  $('#sub_course').html('');
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/sub_course?session='+admission_session+'&type='+admission_type+'&course='+course,
    type: 'GET',
    dataType: 'json',
    success: function(data){
      Object.keys(data).forEach(function(element){
        $('#sub_course').append('<option value="'+element+'">'+data[element]+'</option>');
      });
      $('select[name^="sub_course"] option[value="'+specialization_key+'"]').attr("selected","selected");
      getDuration();
      hideFee();
    }
  });
}

function getDuration(){
  var admission_type = $('#admission_type').val();
  var sub_course = $('#sub_course').val();
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/duration?type='+admission_type+'&subcourse='+sub_course,
    type: 'GET',
    dataType: 'json',
    success: function(data){
      $('#semester').html('');
      Object.keys(data).forEach(function(element){
        if(Array.isArray(data[element])){
          data[element].forEach(element => 
            $('#semester').append('<option value="'+element+'">'+element+'</option>')
          );
        }else{
          $('#semester').append('<option value="'+data[element]+'">'+data[element]+'</option>');
        }
      });
      hideFee();
      setPaymentType();
    }
  });
}

function setPaymentType(){
  var value = $('#semester').val();
  $('#fee_type').html('');
  var semester = value==1 ? '1st' : value==2 ? '2nd' : value==3 ? '3rd' : value+'th';
  $('#fee_type').append('<option value="">Select</option>\
    <option value="1">Registration Fee</option>\
    <option value="2">'+semester+' Semester + Registration Fee</option>\
    <option value="3">Full Program Fee</option>'
  )
  $('select[name^="fee_type"] option[value="'+fee_type+'"]').attr("selected","selected");
  if(fee_type.length>0){
    getFee(fee_type);
  }
}

function hideFee(){
  $('#fee_table').html('');
}

function getFee(value){
  var admission_type = $('#admission_type').val();
  var sub_course = $('#sub_course').val();
  var semester = $('#semester').val();
  $('#fee_table').html('');
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/debit_ledger?sub_course='+sub_course+'&type='+admission_type+'&duration='+semester,
    type:'GET',
    dataType: 'json',
    success: function(data){
      $('#fee_table').html('<div class="col-md-12">\
        <div class="responsive">\
          <table class="table table-bordered nowrap">\
          <thead id="fee_table_header">\
          </thead>\
          <tbody id="fee_table_content">\
          </tbody>\
          </table>\
        </div>\
      </div>');

      var total_fee = 0;

      if(value==3){
        $('#fee_table_header').append('<tr class="text-center">\
          <th>Semester</th>\
          <th>Course Fee</th>\
          <th>Exam Fee</th>\
          <th>Registration Fee</th>\
          <th>Total</th>\
        </tr>');

        var full_program_fee = [];
        
      }

      Object.keys(data).forEach(function(element){
        if(value==1 && element==semester){
          Object.keys(data[element]).forEach(function(head){
            if(head=='Reg Fee'){
              $('#fee_table_content').html('<tr>\
                  <td class="fw-bold">Registration Fee</td>\
                  <td class="text-end">'+data[element][head]+'</td>\
                </tr>\
                <tr>\
                  <td>Total</td>\
                  <td class="fw-bold text-end">'+data[element][head]+'</td>\
                </tr>\
              ');
              total_fee = data[element][head];
            }
          });
        }else if(value==2 && element==semester){
          Object.keys(data[element]).forEach(function(head){
            var fee_head = head.replace("_", " ");
            var td_class = head=='Total' ? '' : 'fw-bold';
            var amount_td_class = head=='Total' ? 'fw-bold' : '';
            $('#fee_table_content').append('<tr>\
              <td class="'+td_class+'">'+fee_head+'</td>\
              <td class="text-end '+amount_td_class+'">'+data[element][head]+'</td>\
            </tr>');
            total_fee = head=='Total' ? data[element][head] : 0;
          });
        }else if(value==3){
          var reg_fee = data[element]['Reg Fee']===undefined ? '' : data[element]['Reg Fee'];
          full_program_fee.push(data[element]['Total']);
          $('#fee_table_content').append('<tr>\
            <td class="fw-bold text-center">'+element+'</td>\
            <td class="text-end">'+data[element]['Course_Fee']+'</td>\
            <td class="text-end">'+data[element]['Exam_Fee']+'</td>\
            <td class="text-end">'+reg_fee+'</td>\
            <td class="text-end">'+data[element]['Total']+'</td>\
          </tr>');
        }
      });

      if(value==3){
        full_program_fee.forEach(x => {
          total_fee += x;
        });

        $('#fee_table_content').append('<tr>\
          <td colspan="4">Total</td>\
          <td class="text-end fw-bold">'+total_fee+'</td>\
        </tr>');
      }

      $('#amount').val(total_fee);

      setPayableAmount(total_fee, value);
    }
  })
}

function setPayableAmount(amount, value){
  $.ajax({
    url: '/application-form/payable',
    type: 'POST',
    data:{amount: amount, value: value},
    success: function(data){

    }
  })
}

if($('#step_2_form').length>0){
  $('#step_2_form').validate({
    rules: {
      admission_session: {required: true},
      admission_type: {required: true},
      course: {required: true},
      sub_course: {required: true},
      semester: {required: true},
      fee_type: {required: true},
      amount: {required: true},
    },
    highlight: function(element) {
      $(element).addClass('error');
      $(element).closest('.form-control').addClass('has-error');
    },
    unhighlight: function(element) {
      $(element).removeClass('error');
      $(element).closest('.form-control').removeClass('has-error');
    }
  });
}


$("form#step_2_form").submit(function(e) {
  e.preventDefault();
  var formData = new FormData(this);
  formData.append("key", key);
  if($("#location_confirmation").length>0 && $("#location_confirmation").prop('checked') != true){
    Toast.fire({
      icon: 'error',
      title: 'Please accept term & condition!',
    })
    return false;
  }
  if($("form#step_2_form").valid()){
    showLoader('Please wait...');
    $.ajax({
      url:'/application-form/pay',
      type: 'POST',
      data:formData,
      dataType: 'json',
      success: function(response){
        if(response.status){
          hideLoader();
          sendPaymentMail('initiated');
          initiatePayment(response.data);
        }else{
          Toast.fire({
            icon: 'error',
            title: response.data,
          })
        }
      },
      cache: false,
      contentType: false,
      processData: false
    })
  }
});

// Payment Gateway
function initiatePayment(data){
  var easebuzzCheckout = new EasebuzzCheckout('WBJIW38NZG', 'prod')
  var options = {
    access_key: data,
    dataType: 'json',
    onResponse: (response) => {
      if(response.status=='success'){
        updateStudent(response);
        sendPaymentMail(response.status);
        Swal.fire(
          'Thank You!',
          'Payment Successful!',
          'success'
        )
      }else{
        sendPaymentMail(response.status);
        Swal.fire(
          'Payment Failed',
          'Please try again!',
          'error'
        )
      }
    },
    theme: "#FF3B3F" // color hex
  }
  easebuzzCheckout.initiatePayment(options);
}

// Step 2
function updateStudent(response){
  showLoader('Please wait...');
  $.ajax({
    url: '/application-form/update-2',
    type: 'POST',
    data: {response},
    dataType: 'json',
    success: function(response){
      openApplicationForm(response);
    },
    error: function(response){
      console.error(response);
    }
  })
}

// Required Doc
function requireDoucuments() {
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/require_doc?course='+course_key+'&admission_type='+admission_type_key,
    type: 'GET',
    success: function(data){
      if(data.match('both')){
        ugCourseRequired();
        pgCourseRequired();
      }else if(data.match('ug')){
        ugCourseRequired();
      }else if(data.match('pg')){
        pgCourseRequired();
      }else if(data.match('other')){
        otherCourseRequired();
      }else{
        ugCourseUnrequired();
        pgCourseUnrequired();
        otherCourseUnrequired();
      }
    }
  })
}

function ugCourseRequired() {
  $('.ug_required').html('*');
  $('#ug_subject').validate();
  $('#ug_subject').rules('add', { required: true });
  $('#ug_year').validate();
  $('#ug_year').rules('add', { required: true });
  $('#ug_board_university').validate();
  $('#ug_board_university').rules('add', { required: true });
  $('#ug_percentage').validate();
  $('#ug_percentage').rules('add', { required: true });
}

function ugCourseUnrequired() {
  $('.ug_required').html('');
  $('#ug_subject').rules('remove', 'required');
  $('#ug_year').rules('remove', 'required');
  $('#ug_board_university').rules('remove', 'required');
  $('#ug_percentage').rules('remove', 'required');
}

function pgCourseRequired() {
  $('.pg_required').html('*');
  $('#pg_subject').validate();
  $('#pg_subject').rules('add', { required: true });
  $('#pg_year').validate();
  $('#pg_year').rules('add', { required: true });
  $('#pg_board_university').validate();
  $('#pg_board_university').rules('add', { required: true });
  $('#pg_percentage').validate();
  $('#pg_percentage').rules('add', { required: true });
}

function pgCourseUnrequired() {
  $('.pg_required').html('');
  $('#pg_subject').rules('remove', 'required');
  $('#pg_year').rules('remove', 'required');
  $('#pg_board_university').rules('remove', 'required');
  $('#pg_percentage').rules('remove', 'required');
}

function otherCourseRequired() {
  $('.other_required').html('*');
  $('#other_subject').validate();
  $('#other_subject').rules('add', { required: true });
  $('#other_year').validate();
  $('#other_year').rules('add', { required: true });
  $('#other_board_university').validate();
  $('#other_board_university').rules('add', { required: true });
  $('#other_percentage').validate();
  $('#other_percentage').rules('add', { required: true });
}

function otherCourseUnrequired() {
  $('.other_required').html('');
  $('#other_subject').rules('remove', 'required');
  $('#other_year').rules('remove', 'required');
  $('#other_board_university').rules('remove', 'required');
  $('#other_percentage').rules('remove', 'required');
}

function checkMaxMarks(id) {
  var obtained = parseInt($('#'+id+'_marks_obtained').val());
  var max = parseInt($('#'+id+'_max_marks').val());
  var alerted = localStorage.getItem(id+'_alerted') || '';
  if (obtained > max) {
    if (alerted != 'yes') {
      alert("Obtained marks can not be higher than Maximum marks");
      $(':input[type="submit"]').prop('disabled', true);
      localStorage.setItem('alerted', 'yes');
      $('#'+id+'_percentage').prop("readonly", false);
      $('#'+id+'_percentage').val('');
    }
  } else {
    localStorage.setItem(id+'_alerted', 'no');
    $(':input[type="submit"]').prop('disabled', false);
    if ($('#'+id+'_marks_obtained').val().length > 0 && $('#'+id+'_max_marks').val().length > 0) {
      var percentage = (obtained / max) * 100;
      $('#'+id+'_percentage').val(percentage.toFixed(2));
      $('#'+id+'_percentage').prop("readonly", true);
    } else if ($('#'+id+'_marks_obtained').val().length == 0 || $('#'+id+'_max_marks').val().length == 0) {
      $('#'+id+'_percentage').prop("readonly", false);
      $('#'+id+'_percentage').val('');
    }
  }
}

function checkInterOther(value){
  if(value=='Other'){
    $('#inter_subject').replaceWith('<div class="container-flude" id="other_inter_subject">\
      <div style="display:flex;">\
        <input type="text" maxlength="39" name="inter_subject" id="inter_subject" placeholder="Stream" class="form-control">\
        <i class="fa fa-refresh" onclick="removeOtherInterSubject()" style="margin-top:15px; margin-left:5px;"></i>\
      </div>\
    </div>');
  }
}

function removeOtherInterSubject(){
  $('#other_inter_subject').replaceWith('<select class="form-control" name="inter_subject" onchange="checkInterOther(this.value)" id="inter_subject">\
    <option value="">Select</option>\
    <option value="Arts">Arts</option>\
    <option value="Commerce">Commerce</option>\
    <option value="Science-PCM">Science-PCM</option>\
    <option value="Science-PCB">Science-PCB</option>\
    <option value="Other">Other</option>\
  </select>');
}

function checkUGOther(value){
  if(value=='Other'){
    $('#ug_subject').replaceWith('<div class="container-flude" id="other_ug_subject">\
      <div style="display:flex;">\
        <input type="text" maxlength="39" name="ug_subject" id="ug_subject" placeholder="Course" class="form-control">\
        <i class="fa fa-refresh" onclick="removeOtherUGSubject()" style="margin-top:15px; margin-left:5px;"></i>\
      </div>\
    </div>');
  }
}

function removeOtherUGSubject(){
  $('#other_ug_subject').replaceWith('<select class="form-control" name="ug_subject" onchange="checkUGOther(this.value)" id="ug_subject">\
    <option value="">Select</option>\
    <option value="BA">BA</option>\
    <option value="BCOM">BCOM</option>\
    <option value="BSC">BSC</option>\
    <option value="BBA">BBA</option>\
    <option value="BCA">BCA</option>\
    <option value="BTECH">BTECH</option>\
    <option value="Other">Other</option>\
  </select>');
}

function checkPGOther(value){
  if(value=='Other'){
    $('#pg_subject').replaceWith('<div class="container-flude" id="other_pg_subject">\
      <div style="display:flex;">\
        <input type="text" maxlength="39" name="pg_subject" id="pg_subject" placeholder="Course" class="form-control">\
        <i class="fa fa-refresh" onclick="removeOtherPGSubject()" style="margin-top:15px; margin-left:5px;"></i>\
      </div>\
    </div>');
  }
}

function removeOtherPGSubject(){
  $('#other_pg_subject').replaceWith('<select class="form-control" name="pg_subject" onchange="checkPGOther(this.value)" id="pg_subject">\
    <option value="">Select</option>\
    <option value="MA">MA</option>\
    <option value="MCOM">MCOM</option>\
    <option value="MSC">MSC</option>\
    <option value="MBA">MBA</option>\
    <option value="MCA">MCA</option>\
    <option value="MTECH">MTECH</option>\
    <option value="Other">Other</option>\
  </select>');
}

if($('#step_3_form').length>0){
  requireDoucuments();
  $('#step_3_form').validate({
    rules: {
      hs_subject: {required: true},
      hs_year: {required: true},
      hs_board_university: {required: true},
      hs_percentage: {required: true},
    },
    highlight: function(element) {
      $(element).addClass('error');
      $(element).closest('.form-control').addClass('has-error');
    },
    unhighlight: function(element) {
      $(element).removeClass('error');
      $(element).closest('.form-control').removeClass('has-error');
    }
  });
}

$("form#step_3_form").submit(function(e) {
  e.preventDefault();
  var formData = new FormData(this);
  formData.append("key", key);
  if($("form#step_3_form").valid()){
    showLoader('Please wait...');
    $.ajax({
      url:'/application-form/update-3',
      type: 'POST',
      data:formData,
      dataType: 'json',
      success: function(response){
        if(response.status){
          openApplicationForm(response);
        }
      },
      cache: false,
      contentType: false,
      processData: false
    })
  }
});


// Image Preview
function readURL(input, showat) {
  fileValidationSingle(showat);
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      if(e.target.result!=''){
        $('#for_' + showat).attr('src', e.target.result);
        $('#for_' + showat).css('display', 'block');
      }else{
        $('#for_' + showat).attr('src', '');
        $('#for_' + showat).css('display', 'none');
      }
    }
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#photo").change(function() {
  readURL(this, 'photo');
});

$("#student_signature").change(function() {
  readURL(this, 'student_signature');
});

$("#parent_signature").change(function() {
  readURL(this, 'parent_signature');
});

function fileValidationSingle(id){
  var fi = document.getElementById(id);
  if (fi.files.length > 0) {
    for (var i = 0; i <= fi.files.length - 1; i++) {
      var fsize = fi.files.item(i).size;
      var file = Math.round((fsize / 1024));
      // The size of the file.
      if (file >= 250) {
        $('#'+id).val('');
        alert("File should be less than or equal to 250KB!");
        return false;
      }
    }
  }
}

function fileValidationMultiple(id){
  var fi = document.getElementById(id);
  if (fi.files.length > 0) {
    for (var i = 0; i <= fi.files.length - 1; i++) {
      var fsize = fi.files.item(i).size;
      var file = Math.round((fsize / 1024));
      // The size of the file.
      if (file >= 500) {
        $('#'+id).val('');
        alert("File should be less than or equal to 500KB each!");
        return false;
      }
    }
  }
}

// Multiple File Input
function getUploadingInput(value, id){
  $('#'+id+'_marksheet').html('');
  for(var i=1; i<=value; i++){
    var sem_suffix = i==1 ? 'st' : i==2 ? 'nd' : i==3 ? 'rd' : 'th';
    $('#'+id+'_marksheet').append('<div class="col-md-6 mb-2">\
      <input type="file" class="form-control form-control-file '+id+'_required" id="'+id+'_marksheet_'+i+'" onchange="fileValidationMultiple(this.id)" accept="image/png, image/jpg, image/jpeg" name="'+id+'_marksheet['+i+']" required>\
    </div>');
  }
}

// Required Doc
function requireDoucumentsForUploading() {
  $.ajax({
    url:'https://universityadmission.co.in/api/settings/require_doc?course='+course_key+'&admission_type='+admission_type_key,
    type: 'GET',
    success: function(data){
      if(data.match('both')){
        ugDocumentsRequired();
        pgDocumentsRequired();
      }else if(data.match('ug')){
        ugDocumentsRequired();
      }else if(data.match('pg')){
        pgDocumentsRequired();
      }else if(data.match('other')){
        otherDocumentsRequired();
      }else{
        ugDocumentsUnRequired();
        pgDocumentsUnRequired();
        otherDocumentsUnRequired();
      }
    }
  })
}

function ugDocumentsRequired(){
  $('.ug_required').html('*');
  $('#ug_no_of_files').rules('add', { required: true });
  $('#ug_no_of_marksheet').rules('add', { required: true });
}

function pgDocumentsRequired(){
  $('.pg_required').html('*');
  $('#pg_no_of_marksheet').rules('add', { required: true });
}

function otherDocumentsRequired(){
  $('.other_required').html('*');
  $('#other_no_of_marksheet').rules('add', { required: true });
}

function ugDocumentsUnRequired(){
  $('.ug_required').html('*');
  $('#ug_no_of_marksheet').rules('remove', { required: true });
}

function pgDocumentsUnRequired(){
  $('.pg_required').html('*');
  $('#pg_no_of_marksheet').rules('remove', { required: true });
}

function otherDocumentsUnRequired(){
  $('.other_required').html('*');
  $('#other_no_of_marksheet').rules('remove', { required: true });
}

if($('#step_4_form').length>0){
  requireDoucumentsForUploading();
  $('#step_4_form').validate({
    rules: {
      photo: {required: true},
      "signature[1]": {required: true},
      "signature[2]": {required: true},
      "aadhar_photo[1]": {required: true},
      "aadhar_photo[2]": {required: true},
      "high_school_marksheet[1]": {required: true},
    },
    highlight: function(element) {
      $(element).addClass('error');
      $(element).closest('.form-control').addClass('has-error');
    },
    unhighlight: function(element) {
      $(element).removeClass('error');
      $(element).closest('.form-control').removeClass('has-error');
    }
  });
  $('.high_required').each(function(){
    $(this).rules("add", {
      required: true,
    });   
  });
}

function showTermsCondition(){
  if($("form#step_4_form").valid()){
    $('#terms-condition').modal('show');
  }
}

$("form#step_4_form").submit(function(e) {
  e.preventDefault();
  var formData = new FormData(this);
  formData.append("key", key);
  if($("form#step_4_form").valid()){
    showLoader('Please wait...');
    $.ajax({
      url:'/application-form/update-4',
      type: 'POST',
      data:formData,
      dataType: 'json',
      success: function(response){
        if(response.status){
          hideLoader();
          $('.modal').modal('hide');
          window.location.reload(true);
        }
      },
      cache: false,
      contentType: false,
      processData: false
    })
  }
});

function downloadFeeReceipt(transaction_id){
  showLoader('Please wait...');
  $.ajax({
    url: '/application-form/generate',
    type: 'POST',
    data:{transaction_id},
    dataType: 'json',
    success: function(data){
      hideLoader();
      window.open("/application-form/receipts/"+transaction_id+".pdf", "_blank");
    }
  })
}

function payRemainingAmount(){
  showLoader('Please wait...');
  $.ajax({
    url:'/application-form/pay-remaining',
    type: 'POST',
    data:{key},
    dataType: 'json',
    success: function(response){
      sendPaymentMail('initiated');
      reInitiatePayment(response.data);
      hideLoader();
    }
  })
}

function reInitiatePayment(data){
  var easebuzzCheckout = new EasebuzzCheckout('WBJIW38NZG', 'prod')
  var options = {
    access_key: data,
    dataType: 'json',
    onResponse: (response) => {
      if(response.status==='success'){
        updatePayment(response);
        sendPaymentMail(response.status);
      }else{
        sendPaymentMail(response.status);
        Swal.fire(
          'Payment Failed',
          'Please try again!',
          'error'
        )
      }
    },
    theme: "#FF3B3F" // color hex
  }
  easebuzzCheckout.initiatePayment(options);
}

function updatePayment(response){
  $.ajax({
    url:'/application-form/update-ledger',
    type: 'POST',
    data:{response},
    dataType: 'json',
    succcess: function(response){
      if(response.status){
        Swal.fire(
          'Thank You!',
          'Payment Successful!',
          'success'
        );
        setTimeout(function(){
         window.location.reload(true);
        }, 3000);
      }else{
        Swal.fire(
          'Error',
          'Please contact your counsellor!',
          'error'
        )
      }
    }
  })
}

// Failed Payment Mail
function sendPaymentMail(status){
  $.ajax({
    url:'/application-form/send/payment-mail?status='+status,
    type: 'GET',
    success: function(data){

    }
  })
}