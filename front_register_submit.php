<?php
    include('database.inc.php');
    include('function.inc.php');
    include('constant.inc.php');
    session_start();
    $type=get_safe_value($_POST['type']);
    $added_on=date('Y-m-d h:i:s');
    if($type=='register'){
        $name=get_safe_value($_POST['name']);
        $email=get_safe_value($_POST['email']);
        $mobile=get_safe_value($_POST['mobile']);
        $password=get_safe_value($_POST['password']);
        $check=mysqli_num_rows(mysqli_query($con,"select * from user where email='$email'"));
        if($check>0){
            $arr=array('status'=>'error','msg'=>'Email id already registered','field'=>'email_error');   
        }
        else{
            // $new_password=password_hash($password,PASSWORD_BCRYPT);
            // mysqli_query($con,"insert into user(name,email,mobile,password,status,email_verify,added_on) values('$name','$email',
            // '$mobile','$new_password','1','1','$added_on')");
            mysqli_query($con,"insert into user(name,email,mobile,password,status,email_verify,added_on) values('$name','$email',
            '$mobile','$password','1','1','$added_on')");
            $res=mysqli_query($con,"select * from user where email='$email'");
            $row=mysqli_fetch_assoc($res);
            $_SESSION['FOOD_USER_ID']=$row['id'];
            $_SESSION['FOOD_USER_NAME']=$row['name'];
            $arr=array('status'=>'success','msg'=>'Thank you for registering.','field'=>'form_msg'); 
          }
        echo json_encode($arr);
    }
    if($type=='login'){
        $email=get_safe_value($_POST['user_email']);
        $password=get_safe_value($_POST['user_password']);
        $res=mysqli_query($con,"select * from user where email='$email'");
        $check=mysqli_num_rows($res);
        if($check>0){
            $row=mysqli_fetch_assoc($res);
            $status=$row['status'];
            $dbpassword=$row['password'];
            
            if($status==1){
                // if(password_verify($password,$dbpassword)){
                //     $_SESSION['FOOD_USER_ID']=$row['id'];
                //     $_SESSION['FOOD_USER_NAME']=$row['name'];
                //     $arr=array('status'=>'success','msg'=>'');
                // }else{
                //     $arr=array('status'=>'error','msg'=>'bla');
                // }
                if($password==$dbpassword){
                    $_SESSION['FOOD_USER_ID']=$row['id'];
                    $_SESSION['FOOD_USER_NAME']=$row['name'];
                    $arr=array('status'=>'success','msg'=>'');
                    if(isset($_SESSION['cart']) && count($_SESSION['cart'])>0){
						foreach($_SESSION['cart'] as $key=>$val){
							manageUserCart($_SESSION['FOOD_USER_ID'],$val['qty'],$key);
						}
					}
                }else{
                    $arr=array('status'=>'error','msg'=>'Password Incorrect');
                }
                
            }else{
                $arr=array('status'=>'error','msg'=>'Your account is deactivated:@:@');
            }
        }
        else{
            $arr=array('status'=>'error','msg'=>'Please enter valid login details :@:@');  
        }
        echo json_encode($arr);
    }
    
?>
