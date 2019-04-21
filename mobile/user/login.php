<?php
    include '../../databaseConfig.php';

    $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

    $data = array();

    if ($con) {
        if ($_SERVER['REQUEST_METHOD']=='POST') {
            if (isset($_REQUEST['email']) and isset($_REQUEST['password'])) {
                $email = $_REQUEST['email'];
                $password = $_REQUEST['password'];

                //Lets covert password to md5
                $md5pass = md5($password);

                //Now need to check if the user exist in database or not.
                $searchEmailQuery = "SELECT * from users WHERE email='$email'";
                $result = mysqli_query($con,$searchEmailQuery);
                if (mysqli_num_rows($result)>0) {
                    //User exist with this Email ID
                    //Now will check for the password value if it matches particular value or not.
                    $passMatchQuery = "SELECT id FROM users WHERE email='$email' AND pass='$md5pass'";
                    $passResult = mysqli_query($con,$passMatchQuery);
                    if (mysqli_num_rows($passResult)>0) {
                        //User is Valid, Login Success!
                        //Also we need to update the login status in the users table
                        $dataOut = array();
                        while ($r = mysqli_fetch_row($passResult)) {
                            $dataOut = $r;
                        }
                        $idValue = $dataOut[0];
                        $status = 1; //for login success.
                        $queryStmt = "UPDATE users SET login_status='$status' WHERE id='$idValue'";
                        $resultLogout = mysqli_query($con,$queryStmt);
                        if ($resultLogout) {
                            //Now status is set to 1 for Logged in.
                            //Now we will again get all the values from the users table to get the updated value login_status.
                            $q =  "SELECT email,username,fullname,dob,mobile,gender,id,login_status FROM users WHERE email='$email' AND pass='$md5pass'";
                            $finalResult = mysqli_query($con,$q);
                            if (mysqli_num_rows($finalResult)>0) {
                                $dataOutput = array();
                                while ($row = mysqli_fetch_row($finalResult)) {
                                    $dataOutput = $row;
                                }
                                $emailValue = $dataOutput[0];
                                $username = $dataOutput[1];
                                $fullname = $dataOutput[2];
                                $dob = $dataOutput[3];
                                $mobile = $dataOutput[4];
                                $gender =$dataOutput[5];
                                $id = $dataOutput[6];
                                $logStatus = $dataOutput[7];
                                //Status Updated
                                $data['status']='success';
                                $data['email']=$emailValue;
                                $data['username']=$username;
                                $data['fullname']=$fullname;
                                $data['dob']=$dob;
                                $data['mobile']=$mobile;
                                $data['gender']=$gender;
                                $data['userid']=$id;
                                $data['login_status']=$logStatus;
                            }
                        } else {
                            //Server Error
                            $data['error'] = false;
                            $data['status'] = 'Server Error!, Try Again Later';
                            $data['message'] = 'Server Error!, Try Again Later';
                        }
                    } else {
                        //Invalid Password
                        $data['error']=true;
                        $data['status']='Invalid Password';
                        $data['message']='Invalid Password';  
                    }
                } else {
                    $data['error']=true;
                    $data['status']='Email Id does not exist!';
                    $data['message']='Email Id does not exist!';
                }
            } else {
                $data['error'] = true;
                $data['message'] = 'Required Data Missing!';
            }
        } else {
            $data['error'] = true;
            $data['message'] = 'Invalid Request'; 
        }
    } else {
        $data['error']=true;
        $data['message']="Error! Try Again After Sometime";
    }
    
    echo json_encode($data);

    mysqli_close($con);
?>
