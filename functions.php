<?php
  require_once 'const.php';

    class DB
    {
        private $dbhost;
        private $dbname;
        private $dbuser;
        private $dbpass;

        private $connection;

        function __construct($dbhost, $dbname, $dbuser, $dbpass)
        {
            $this->dbhost = $dbhost;
            $this->dbname = $dbname;
            $this->dbuser = $dbuser;
            $this->dbpass = $dbpass;
            $this->connect();

        }

        public function connect()
        {
            $this->connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        }

        function __get($connection)
        {
            return $this->connection;
        }
    }



    class UserMapper
    {
        private $db;

        function __construct($connection)
        {
            $this->db = $connection;
        }

        public function register($user_name, $pass)
        {
            try {
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users(username, password, id, room) VALUES(?,?,NULL,'1')";
                if ($stmt = $this->db->connection->prepare($sql))
                {
                    $stmt->bind_param('ss', $user_name, $hashed_pass);
                    $stmt->execute();
                    $stmt->close();
                    return true;
                }
                else {
                    $error = $this->db->connection->errno . ' ' . $this->db->connection->error;
                    return $error;
                }
            } catch (mysqli_sql_exception $e) {
               return $e->getMessage();
            }
        }

        public function login($user_name, $pass)
        {
            try {
                $stmt = $this->db->connection->prepare("SELECT password, room FROM users WHERE username=?");
                $stmt->bind_param('s', $user_name);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($user_saved_pass, $room_id);
                $stmt->fetch();
                if ($stmt->num_rows > 0)
                {
                    if (password_verify($pass, $user_saved_pass))
                    {
                        $_SESSION['user_name'] = $user_name;
                        $_SESSION['room']      = $room_id;
                        return true;
                    }
                    else
                    {
                        return INCORRECT_PASS;
                    }
                } else {
                        return INCORRECT_USER;
                }
                $stmt->close();

            } catch (mysqli_sql_exception $e) {
               return   $e->getMessage();
            }
        }

        public function is_username_free($user_name)
        {
          try {
              $stmt = $this->db->connection->prepare("SELECT username FROM users WHERE username=?");
              $stmt->bind_param('s', $user_name);
              $stmt->execute();
              $stmt->store_result();
              } catch (Exception $e) {
                  return $e->getMessage();
              }
              if ($stmt->num_rows) {
                 return USERNAME_EXISTS;
              } else {
                return true;
              }
        }

        public function get_room_name($room_id)
        {
            try {
                $stmt = $this->db->connection->prepare("SELECT name FROM rooms WHERE id=?");
                $stmt->bind_param('i', $room_id);
                $stmt->execute();
                $stmt->bind_result($room_name);
                $stmt->fetch();
                $stmt->close();
                return $room_name;
            } catch (mysqli_sql_exception $e) {
                return false;
            }
        }

        public function add_message($user_name, $text, $room_id)
        {
            try {
                $stmt = $this->db->connection->prepare("INSERT INTO messages VALUES(NULL,?,DEFAULT,?,?)");
                $stmt->bind_param('ssi', $user_name, $text, $room_id);
                $stmt->execute();
                $stmt->close();
                return true;
            } catch (mysqli_sql_exception $e) {
                return false;
            }
        }

        public function display_messages($room_id)
        {
            try {
                $stmt = $this->db->connection->prepare("SELECT username, time, text FROM messages WHERE room=? LIMIT 100");
                $stmt->bind_param('i', $room_id);
                $stmt->execute();
                $stmt->bind_result($user_name, $time, $text);
                $messages = array();
                $i = 1;
                while ($stmt->fetch()) {
                    $messages["message$i"] = array("time"=>$time, "user_name"=>$user_name, "text"=>$text);
                    $i++;
                }
                $stmt->close();
                return $messages;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        //// Rooms area.
        public function room_exists($room_name)
        {
          try {
              $result = $this->db->connection->prepare("SELECT name FROM rooms WHERE name=?");
              $result->bind_param('s', $room_name);
              $result->execute();
            } catch (Exception $e) {
              return $e->getMessage();
            }
          $result->store_result();
          if ($result->num_rows == 0) {
            $result->close();
            return false;
          } else {
            $result->close();
            return true;
          }
        }

        public function add_room($user_name, $room_name)
        {
            try {
                // Add to rooms list.
                $stmt = $this->db->connection->prepare("INSERT INTO rooms VALUES(NULL, ?)");
                $stmt->bind_param('s', $room_name);
                $stmt->execute();
                $room_id = $stmt->insert_id;
                // Change the user's room.
                $this->change_room($user_name, $room_id);
                $stmt->close();
                return true;
                // MAKE EVERYTHING RETURN TRUE OR (MESSAGE INSTEAD OF FALSE). NOT FALSE.
            } catch (mysqli_sql_exception $e) {
                return false;
            }
        }

        public function change_room($user_name, $room_id)
        {
                $stmt = $this->db->connection->prepare("UPDATE users SET room=? WHERE username=?");
                $stmt->bind_param('is', $room_id, $user_name);
                $stmt->execute();
                $_SESSION['room'] = $room_id;
                $stmt->close();
                return true;
            if ($stmt === FALSE) {
                return DB_ERROR;
            }
        }

        public function get_rooms()
        {
            try {
              $result = $this->db->connection->prepare("SELECT id, name FROM rooms");
              $result->execute();
              $result->bind_result($room_id, $room_name);
              $rooms = array();
              $i = 1;
              while ($result->fetch()) {
                    $rooms["room$i"] = array("room_id"=>$room_id, "room_name"=>$room_name);
                    $i++;
              }
              $result->close();
              return $rooms;
            } catch (Exception $e) {
                return false;
            }
        }
    }


class File
{
    private $db;
    public $file_name;
    public $gen_name;
    public $ext;
    public $file_size;
    public $date_uploaded;
    public $user_name;
    public $new_path;
    const UPLOADS_DIR = 'uploaded_files/';

    public function __construct($connection, $original_name, $user_name)
    {
        $this->db = $connection;
        $this->file_name = $original_name;
        $this->gen_name = generateRandomString();
        $this->file_size = $_FILES['data']['size'];
        $this->user_name = $user_name;
        $this->date_uploaded = time();
        $this->ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $this->new_path = self::UPLOADS_DIR + $this->gen_name;

        move_uploaded_file($_FILES['data']['tmp_name'], $this->new_path);
        $this->upload_file();
    }

    public function check_img()
    {
        $img_array = array('jpg', 'jpeg', 'gif', 'png', 'tiff');
        if (in_array($this->ext, $img_array))
            return TRUE;
    }

    private function upload_file()
    {
        try {
            $stmt = $this->db->connection->prepare("INSERT INTO files VALUES(NULL,?,DEFAULT,?,?,?,?)");
            $stmt->bind_param('sssis', $this->user_name, $this->file_name, $this->gen_name, $this->file_size,
                               $this->ext);
            $stmt->execute();
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "$e->getMessage()";
        }
    }

    public function post_img()
    {
        $mime = $_FILES['data']['type'];
        $contents = file_get_contents($this->new_path);
        $base64 = base64_encode($contents);
        return "<img src='data:$mime;base64,$base64' alt='$this->file_name' height='150' width='150'>";
    }

    public function post_file()
    {
        try {
            $stmt = $this->db->connection->prepare("SELECT id FROM files WHERE username=? AND gen_name=?");
            $stmt->bind_param('ss', $this->user_name, $this->gen_name);
            $stmt->execute();
            $stmt->bind_result($id);
            $stmt->fetch();
            $file_name = "$this->file_name";
            // file size in MB.
            $file_size = round(($this->file_size/1048576), 4);
            return "<a href='download.php?id=$id'>$file_name</a>, size: $file_size MB.";


        } catch (mysqli_sql_exception $e) {
            echo "$e->getMessage()";
        }
    }

    public function file_details($id)
    {
    try {
        $stmt = $this->db->connection->prepare("SELECT original_name, gen_name, size, ext  FROM files WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($original_name, $gen_name, $size, $ext);
        $stmt->fetch();
        $details = array('file_path'     => $this->new_path,
                         'original_name' => "$original_name",
                         'size'          => "$size");

        return $details;
        } catch (mysqli_sql_exception $e) {
            echo "$e->getMessage()";
        }
    }
}





    function createTable($name, $query) {
        try {
            queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
            echo "Table '$name' created or already exists.<br>";
        } catch (Exception $e) {
            echo "$e->getMessage()";
        }

    }


    function destroy_session() {
        $_SESSION=array();

        if (session_id() != "" || isset($_COOKIE[session_name()]))
          setcookie(session_name(), '', time()-2592000, '/');

        session_destroy();
    }


   function validate_username($user)
    {
        if (strlen($user) < 3 || (preg_match("/[^a-zA-Z0-9_-]/", $user)))
        {
            return false;
        }
        else return true;
    }

    function validate_pass($pass)
    {
        if (strlen($pass) < 3)
        {
            return false;
        }
        else return true;
    }

    function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // Display Errors:
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

?>
