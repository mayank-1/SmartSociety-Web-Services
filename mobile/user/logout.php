<?php
    //Include the databaseConfig file
    include '../../databaseConfig.php';

    $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

    $data = array();

    if ($con) {
        if ($_SERVER['REQUEST_METHOD']=='POST') {
            if (isset($_REQUEST['user_id'])) {
                $id = $_REQUEST['user_id'];

                //Check if the user login_status is true or false for the folling user_id from the USers table.
                $query = "SELECT login_status FROM users WHERE  id = '$id'";
                $result = mysqli_query($con,$query);
                if (mysqli_num_rows($result)>0) {
                    //User exist with this user_id
                    //User is Valid
                    $dataResult = array();
                    while ($row = mysqli_fetch_row($result)) {
                        $dataResult = $row;
                    }
                    $loginStatus = $dataResult[0];
                    if ($loginStatus == 1) {
                        //Set it to 0 for Logout and 1 for Login
                        $status = 0;
                        $queryStmt = "UPDATE users SET login_status='$status' WHERE id='$id'";
                        $resultLogout = mysqli_query($con,$queryStmt);
                        if ($resultLogout) {
                            //Data Updated
                            $data['error'] = false;
                            $data['message'] = 'Logout Success';
                        } else {
                            //Already Logged Out
                            $data['error'] = false;
                            $data['message'] = 'Server Error!, Try Again Later';
                        }
                    } else {
                        //Already Logged Out
                        $data['error'] = false;
                        $data['message'] = 'Already Logged Out';
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
    

    echo json_encode($data);

    mysqli_close($con);

?>