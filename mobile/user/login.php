<?php
    include '../../databaseConfig.php';

    $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

    if ($con) {
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
            $passMatchQuery = "SELECT email,username,firstname,lastname,dob,mobile,gender FROM users WHERE email='$email' AND pass='$md5pass'";
            $passResult = mysqli_query($con,$passMatchQuery);
            if (mysqli_num_rows($passResult)>0) {
                //User is Valid, Login Success!
                $data = array();
                while ($row = mysqli_fetch_row($passResult)) {
                    $data = $row;
                }
                $emailValue = $data[0];
                $username = $data[1];
                $firstname = $data[2];
                $lastname = $data[3];
                $dob = $data[4];
                $mobile = $data[5];
                $gender =$data[6];

                $finalOutput = ['status'=>'success','email'=>$emailValue,'username'=>$username,'fname'=>$firstname,'lname'=>$lastname,'dob'=>$dob,'mobile'=>$mobile,'gender'=>$gender];
                echo json_encode($finalOutput);
            } else {
                //Invalid Password
                $data = ['message'=>'Invalid Password'];
                echo json_encode($data);
            }
        } else {
            //User does not exist
            $data = ['message'=>'User does not exist'];
            echo json_encode($data);
        }
    } else {
        //Connectivity Error
        $data = ['message'=>'Connection Error'];
        echo json_encode($data);
    }
?>
