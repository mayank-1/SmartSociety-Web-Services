<?php
    //Include the databaseConfig file
    include '../../databaseConfig.php';

    $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

    $data = array();

    if ($con) {
        if ($_SERVER['REQUEST_METHOD']=='POST') {
            if (isset($_REQUEST['user_id'])) {
                $id = $_REQUEST['user_id'];

                //Check if the user login_status is true or false for the folling user_id in the Users table.
                $query = "SELECT login_status FROM users WHERE  id = '$id'";
                $result = mysqli_query($con,$query);
                if (mysqli_num_rows($result)>0) {
                    //User exist with this user_id
                    if (mysqli_num_rows($result)>0) {
                        //User is Valid
                        $dataResult = array();
                        while ($row = mysqli_fetch_row($result)) {
                            $dataResult = $row;
                        }
                        $loginStatus = $dataResult[0];
                        if ($loginStatus == 1) {
                            $logged_in = true;
                            $data['error'] = false;
                            $data['logged_in'] = true;
                        } else {
                            $logged_in = false;
                            $data['error'] = false;
                            $data['logged_in'] = false;
                        }
                    } else {
                        //Invalid UserID
                        $data['error'] = true;
                        $data['message'] = 'Invalid UserID';
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
            $data['message']="Server Error, Try Again Later";
        }
    }

    echo json_encode($data);

    mysqli_close($con);

?>