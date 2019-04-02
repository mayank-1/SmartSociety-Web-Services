<?php
    //Include the databaseConfig file
    include '../../databaseConfig.php';

    $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
    
    $data = array();
    
    if ($con) {
        if ($_SERVER['REQUEST_METHOD']=='POST') {
            if (isset($_REQUEST['username']) and
                    isset($_REQUEST['email']) and
                        isset($_REQUEST['fullname']) and
                            isset($_REQUEST['mobile']) and
                                isset($_REQUEST['gender']) and
                                    isset($_REQUEST['dob']) and 
                                        isset($_REQUEST['device_id']) and
                                            isset($_REQUEST['password'])) {
                //Perform Further Task
                $newUsername = $_REQUEST['username'];
                if(preg_match('/^\w{3,}$/', $newUsername)) { 
                    // \w equals "[0-9A-Za-z_]"
                    // valid username, alphanumeric & longer than or equals 5 chars
                    $userMatchQuery = "SELECT * FROM users WHERE username='$newUsername'";
                    $result = mysqli_query($con,$userMatchQuery);
                    if (mysqli_num_rows($result)>0) {
                        //Username already Exist
                        $data['error'] = true;
                        $data['message']='Username: '.$newUsername.' taken. Please choose different Username.';
                    } else {
                        //Check if email exist
                        $email = $_REQUEST['email'];
                        if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$email)) {
                            //Email Valid
                            $emailMatchQuery = "SELECT * FROM users WHERE email='$email'";
                            $resultEmail = mysqli_query($con,$emailMatchQuery);
                            if (mysqli_num_rows($resultEmail)>0) {
                                //Email already Exist
                                $data['error'] = false;
                                $data['message']='Email '.$email.' exist. Please Login with your email ID.';
                            } else {
                                $randomID = getToken(40);
                                //Create User Account
                                $newUsername = $_REQUEST['username'];
                                
                                $fullname = $_REQUEST['fullname'];
                                $mobile = $_REQUEST['mobile'];
                                $gender = $_REQUEST['gender'];
    
                                //Covert Password to MD5
                                $password = $_REQUEST['password'];
                                $md5pass = md5($password);
                                
                                //Date Formating
                                $dob = $_REQUEST['dob'];
                                $dobM = explode('-',$dob);
                                $day = $dobM[0];
                                $month = $dobM[1];
                                $year = $dobM[2];
                                $finalDOB = "$year-$month-$day";
                                
                                $deviceID = $_REQUEST['device_id'];
                                $createdDate = date('Y-m-d', time());
                                if (isset($_REQUEST['login_status'])) {
                                    $loginStatus = 1;
                                } else {
                                    $loginStatus = 0;
                                }

                                $createQuery = "INSERT INTO users (id,username,fullname,email,pass,mobile,gender,dob,device_id,login_status,created_on) VALUES ('$randomID','$newUsername','$fullname','$email','$md5pass','$mobile','$gender','$finalDOB','$deviceID','$loginStatus','$createdDate')";
                                if (mysqli_query($con,$createQuery)) {

                                     //Done
                                     $data['error'] = false;
                                     $data['message']='success';
                                     $data['loggedin']=$loginStatus;
                                     $data['userid']=$randomID;
                                     $data['fullname']=$fullname;
                                     $data['username']=$newUsername;
                                     $data['email']=$email;
                                     $data['dob']=$dob;
                                } else {
                                    //Failure
                                    $data['error'] = true;
                                    $data['message']='failure';
                                }
                            }
                        } else {
                            //Invalid Email
                            $data['error'] = true;
                            $data['message']='Invalid Email';
                        } 
                    } 
                } else {
                    $data['error'] = true;
                    $data['message']='Invalid Username';
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
        $data['error'] = true;
        $data['message']='Server Error';
    }

    echo json_encode($data);
        

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