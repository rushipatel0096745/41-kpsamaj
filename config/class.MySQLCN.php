<?php

class MySQLCN {
    protected $CONN = null;

    function __construct() {


        //$user = "root"; $pass = ""; $server = "localhost"; $dbase = "frontpage";
        // $user = "api_maulik";
        // $pass = "DB2!Etu4l0cJ56Pav#";
        // $server = "127.0.0.1";
        // $dbase = "api_maulik";
        // $conn = mysqli_connect($server, $user, $pass, $dbase);
        // mysqli_set_charset($conn,"utf8");

        // local setup
        $user = "root";
        $pass = "Root@123";
        $server = "localhost";
        $dbase = "api_maulik";
        $conn = mysqli_connect($server, $user, $pass, $dbase);
        mysqli_set_charset($conn,"utf8");

        //$conn = mysql_connect($server,$user,$pass);
        if (!$conn || mysqli_connect_errno()) {
            $this->error("Connection attempt failed");
        }

        //if(!mysql_select_db($dbase,$conn)) {
        //$this->error("Dbase Select failed");
        //}

        $this->CONN = $conn;
        return true;
    }

    function close() {
        $conn = $this->CONN;
        $close = mysqli_close($conn);
        if (!$close) {
            $this->error("Connection close failed");
        }
        return true;
    }

    function error($text) {
        $conn = $this->CONN;
        $no = mysqli_errno($conn);
        $msg = mysqli_error($conn);
        ;
        exit;
    }

    function select($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^select/i", $sql)) {
            echo "Wrong Query<hr>$sql<p>";
            echo "<H2>Wrong function silly!</H2>\n";
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if ($results === false) {
            error_log("SQL ERROR: " . mysqli_error($conn) . " | QUERY: " . $sql);
            return false;
        }

        if ((!$results) or ( empty($results))) {
            return false;
        }
        $count = 0;
        $data = array();
        //while ($row = mysqli_fetch_array($results)) {
        while ($row = mysqli_fetch_assoc($results)) {
            $data[$count] = $row;
            $count++;
        }
        
        mysqli_free_result($results);
        return $data;
    }

    function insert($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^insert/i", $sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if (!$results) {
            echo "Insert Operation Failed..<hr>" . mysqli_error($this->CONN);
            $this->error("Insert Operation Failed..");
            $this->error("<H2>No results!</H2>\n");
            return false;
        }
        $id = mysqli_insert_id($this->CONN);
        return $id;
    }

    //Dont remove this - Added by sreejan//
    function adder($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^insert/i", $sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        // $results = @mysql_query($sql, $conn);
        $results = mysqli_query($conn, $sql);


        if (!$results)
            $id = "";
        else
            // $id = mysql_insert_id();
            $id = mysqli_insert_id($conn);

        return $id;
    }

    function edit($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^update/i", $sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if (!$results) {
            $this->error("<H2>No results!</H2>\n");
            return false;
        }
        $rows = 0;
        $rows = mysqli_affected_rows($conn);
        return $rows;
    }

    function sql_query($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql) or die("Query Failed..<hr>" . mysqli_error($conn));
        if (!$results) {
            $message = "Query went bad!";
            $this->error($message);
            return false;
        }
        // (Martin Huba) also SHOW... commands return some results
        if (!(preg_match("/^select/i", $sql) || preg_match("/^show/i", $sql))) {
            return true;
        } else {
            $count = 0;
            $data = array();
            while ($row = mysqli_fetch_array($results)) {
                $data[$count] = $row;
                $count++;
            }
            mysqli_free_result($results);
            return $data;
        }
    }
	
	function delete($sql = "") {
        if (empty($sql)) {
            return false;
        }
        if (!preg_match("/^delete/i", $sql)) {
            echo "Wrong Query<hr>$sql<p>";
            echo "<H2>Wrong function silly!</H2>\n";
            return false;
        }
        if (empty($this->CONN)) {
            return false;
        }
        $conn = $this->CONN;
        $results = mysqli_query($conn, $sql);
        if ((!$results) or ( empty($results))) {
            return false;
        }
    }
    // ✅ NEW escape function added
    function escape($value) {
        return mysqli_real_escape_string($this->CONN, $value);
    }

    function sendOtpSms($mobile, $msg, $templateid)
    {
        $url = "https://sms.bkarma.in/pushapi/sendbulkmsg";

        $params = [
            'username'   => '41kpsamaj',
            'dest'       => $mobile,
            'apikey'     => 'Rj86aIKeHVxH1NGoEXcMkCFbvm8egPTT',
            'signature'  => 'EKTSMJ',
            'msgtype'    => 'PM',
            'msgtxt'     => $msg,
            'entityid'   => '1701159599937465072',
            'templateid' => $templateid
        ];

        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    

//ends the class over here
}

?>