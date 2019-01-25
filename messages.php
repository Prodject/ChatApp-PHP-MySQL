<?php
  require_once 'functions.php';
  require_once 'UserClass.php';
  require_once 'const.php';


    $user_name = $_SESSION['user_name'];
    $room_id = $_SESSION['room'];

    $connection = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    $mapper     = new UserMapper($connection);
    $user       = new User($user_name, $room_id, $mapper);

    switch ($_POST['action']):
        case "addMessage":
          $text = $_POST['data'];
          if ($user->add_message($text))
            echo json_encode(array("result"=>true, "message"=>null));
          else
            echo json_encode(array("result"=>false, "message"=>DB_ERROR));
        break;

        case "addFile":
          $original_name = $_FILES['data']['name'];
          $file = new File($connection, $original_name, $user_name);
          // Check for image file.
          if ($file->check_img()) {
            $post = $file->post_img();
            $user->add_message($post);
          } // If the file isn't an image, display a link to download:
          else {
            $post = $file->post_file();
            $user->add_message($post);
          }
        break;

        case "getMessages":
        if (!$_SESSION['lmtime']) {
          $messages = $mapper->display_messages($room_id);
          $lm       = end($messages);
          show_messages($messages, $lm['time']);
        }

        else {
          while (true) {
            $room_id = $user->get_room();
            $messages = $mapper->display_messages($room_id);
            $lm       = end($messages);

            if ( ($lm['time'] > $_SESSION['lmtime']) || ($room_id != $_SESSION['room']) ) {
              show_messages($messages, $lm['time']);
              break;
            }


            sleep(2);
          }
        }
        break;

    endswitch;


function show_messages($messages, $last_mes_time) {
  if ($messages) {
    echo json_encode(array("result"=>true, "message"=>$messages));
    $_SESSION['lmtime'] = $last_mes_time;
  }
  else {
    echo json_encode(array("result"=>false, "message"=>DB_ERROR));
  }
}


?>

