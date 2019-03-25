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
                    $passMatchQuery = "SELECT email,username,fullname,dob,mobile,gender,id FROM users WHERE email='$email' AND pass='$md5pass'";
                    $passResult = mysqli_query($con,$passMatchQuery);
                    if (mysqli_num_rows($passResult)>0) {
                        //User is Valid, Login Success!
                        $dataOutput = array();
                        while ($row = mysqli_fetch_row($passResult)) {
                            $dataOutput = $row;
                        }
                        $emailValue = $dataOutput[0];
                        $username = $dataOutput[1];
                        $fullname = $dataOutput[2];
                        $dob = $dataOutput[3];
                        $mobile = $dataOutput[4];
                        $gender =$dataOutput[5];
                        $id = $dataOutput[6];

                        $data['status']='success';
                        $data['email']=$emailValue;
                        $data['username']=$username;
                        $data['fullname']=$fullname;
                        $data['dob']=$dob;
                        $data['mobile']=$mobile;
                        $data['gender']=$gender;
                        $data['userid']=$id;
                    } else {
                        //Invalid Password
                        $data['error']=true;
                        $data['message']='Invalid Password';  
                    }
                } else {
                    $data['error']=true;
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
