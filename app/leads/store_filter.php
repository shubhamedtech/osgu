<?php
  if(isset($_POST['filter_for'])){
    require '../../includes/db-config.php';
    session_start();

    $filter_for = array_filter($_POST['filter_for']);
    $operator = $_POST['filter_operator'];
    $values = $_POST['filter_value'];

    $basic_operators = array("equal_to"=>"=", "not_equal_to"=>"<>", "less_than"=>"<", "greater_than"=>">", "less_or_equal_to"=>"<=", "greater_or_equal_to"=>">=");
    $basic_operator_keys = array_keys($basic_operators);

    $multiple_operators = array("in"=>"IN", "not_in"=>"NOT IN");
    $multiple_operator_keys = array_keys($multiple_operators);

    $date_fields = array('Created_On', 'Updated_On');
    
    $filter_by = "";
    foreach($filter_for as $key => $value){
      if((array_key_exists($key, $operator) && !empty($operator[$key])) && array_key_exists($key, $values) && !empty($values[$key])){
        $column = $value=='Departments' ? 'Lead_Status.Department_ID' : ($value=='Categories' ? 'Lead_Status.Category_ID' : ($value=='Sub_Categories' ? 'Lead_Status.Sub_Category_ID' : ($value=='Stages' ? 'Lead_Status.Stage_ID' : ($value=='Reasons' ? 'Lead_Status.Reason_ID' : ($value=='Sources' ? 'Leads.Source_ID' : ($value=='Sub_Sources' ? 'Leads.Sub_Source_ID' : ($value=='Users' ? 'Lead_Status.User_ID' : ($value=='Created_On' ? 'DATE_FORMAT(Lead_Status.Created_At, "%d-%m-%Y")' : ($value=='Updated_On' ? 'DATE_FORMAT(Lead_Status.Updated_At, "%d-%m-%Y")' : '')))))))));

        if(empty($column)){
          continue;
        }

        $filter_values = implode(',', $values[$key]);

        // if operator is in basic operator
        if(in_array($operator[$key], $basic_operator_keys)){
          $applied_operator = $basic_operators[$operator[$key]];
          if(in_array($value, $date_fields)){
            $filter_by .= " AND (".$column.$applied_operator."'".$filter_values."')";
          }else{
            $filter_by .= " AND (".$column.$applied_operator.$filter_values.")";
          }
        }

        // if operator is in multiple_operators
        if(in_array($operator[$key], $multiple_operator_keys)){
          $applied_operator = $multiple_operators[$operator[$key]];
          $filter_by .= " AND (".$column." ".$applied_operator." (".$filter_values."))";
        }

        // Prefix
        if($operator[$key]=='has_prefix'){
          $filter_by .= " AND (".$value.".Name LIKE '".$filter_values."%')";
        }

        // Suffix
        if($operator[$key]=='has_suffix'){
          $filter_by .= " AND (".$value.".Name LIKE '%".$filter_values."')";
        }

        // Contains
        if($operator[$key]=='contains'){
          $filter_by .= " AND (".$value.".Name LIKE '%".$filter_values."%')";
        }

        // Not Contains
        if($operator[$key]=='not_contains'){
          $filter_by .= " AND (".$value.".Name NOT LIKE '%".$filter_values."%')";
        }
      }
    }

    if(!empty($filter_by)){
      $_SESSION['lead_filter_query'] = $filter_by;
      echo json_encode(["status"=>200, "filter_data"=>$_POST]);
    }else{
      echo json_encode(["status"=>400, "message"=>'Please select values']);
    }
  }
