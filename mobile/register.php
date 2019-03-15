<?php
    //Include the databaseConfig file
    include 'H:\Software\Xampp\htdocs\smartSocietyWS\databaseConfig.php';

    $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 
    
    if ($con) {
        $randomID = getToken(40);
        $newUsername = $_GET['username'];
        if(preg_match('/^\w{5,}$/', $newUsername)) { 
            // \w equals "[0-9A-Za-z_]"
            // valid username, alphanumeric & longer than or equals 5 chars
            $userMatchQuery = "SELECT * FROM users WHERE username='$newUsername'";
            $result = mysqli_query($con,$userMatchQuery);
            if (mysqli_num_rows($result)>0) {
                //Username already Exist
                $data=['message'=>'user exist'];
                echo json_encode($data);
            } else {
                 //Check if email exist
                $email = $_GET['email'];
                if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$email)) {
                    //Email Valid
                    $emailMatchQuery = "SELECT * FROM users WHERE email='$email'";
                    $resultEmail = mysqli_query($con,$emailMatchQuery);
                    if (mysqli_num_rows($resultEmail)>0) {
                        //Email already Exist
                        $data=['message'=>'email exist'];
                        echo json_encode($data);
                    } else {
                        //Create User Account
                        $newUsername = $_GET['username'];
                        $fname = $_GET['fname'];
                        $lname = $_GET['lname'];
                        $mobile = $_GET['mobile'];
                        $gender = $_GET['gender'];
                        
                        //Date Formating
                        $dob = $_GET['dob'];
                        $dobM = explode('-',$dob);
                        $day = $dobM[0];
                        $month = $dobM[1];
                        $year = $dobM[2];
                        $finalDOB = "$year-$month-$day";
                        
                        $deviceID = $_GET['deviceId'];
                        $createdDate = date('Y-m-d', time());
                        $createQuery = "INSERT INTO users (id,username,email,firstname,lastname,mobile,gender,dob,device_id,created_on) VALUES ('$randomID','$newUsername','$email','$fname','$lname','$mobile','$gender','$finalDOB','$deviceID','$createdDate')";
                        if (mysqli_query($con,$createQuery)) {
                            //Done
                            $data=['message'=>'success'];
                            echo json_encode($data);
                        } else {
                            //Failure
                            $data=['message'=>'failure'];
                            echo json_encode($data);
                        }
                    }
                } else {
                    //Invalid Email
                    $data=['message'=>'Invalid Email'];
                    echo json_encode($data);
                } 
            } 
        } else {
            $data=['message'=>'Invalid Username'];
            echo json_encode($data);
        }
    } else {
        $data=['message'=>'Server Error'];
        echo json_encode($data);
    }

    function getToken($length) {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }

    function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    mysqli_close($con);
?>