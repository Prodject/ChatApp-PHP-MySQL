// Validate the inputs fields.
function check_Input(input_id, submit_btn_id) {
    var input_element = document.getElementById(input_id);
    var submit_element = document.getElementById(submit_btn_id);
    if (input_element.value.length == 0)
        submit_element.disabled = true;
    input_element.onkeyup = () => {
        if (input_element.value.length > 0)
             submit_element.disabled = false;
        else
            submit_element.disabled = true;
    };
}


async function fetchSendData(username_input, password_input, action, pageToRedirect) {
    var user_name = document.getElementById(username_input).value;
    var password = document.getElementById(password_input).value;
    var data = new FormData();
    data.append('action', action);
    data.append('user_name', user_name);
    data.append('password', password);
    try {
        var response = await fetch('handler.php', {
        method: 'post',
        body: data
        });
        var json = await response.json();
        if (json.result) {
          alert(json.message);
          window.location.replace(pageToRedirect);
        } else if (!json.result) {
          document.getElementById('error').innerHTML = json.message;
        }
      } catch(err) {
        document.getElementById('error').innerHTML = err;
      }
}

// LOGIN area:
if (document.getElementById("username_login")) {
    check_Input('username_login', 'login_btn');
    check_Input('password_login', 'login_btn');
}
async function fetchRequestLogin() {
  var data = new FormData();
  data.append('action', 'login_check');
  try {
      var response = await fetch('handler.php', {
      method: 'post',
      body: data
      });
      var json = await response.json();
      if (json.logged_in) {
        alert("You are logged in already.");
        window.location.replace("index.php");
      }
    } catch(err) {
        alert(err);
        window.location.replace("index.php");
    }
}

document.getElementById('login_btn').onclick = () => {
    fetchSendData('username_login', 'password_login', 'login', 'index.php');
};
// REGISTER area:
document.getElementById('register_btn').onclick = () => {
    fetchSendData('username_register', 'password_register', 'register', 'login.php');
};

// INDEX area:
async function fetchIndex() {
  let data = new FormData();
  data.append('action', 'index');
  try {
      let response = await fetch('handler.php', {
      method: 'post',
      body: data
      });
      let json = await response.json();
      if (json.error) {
        document.getElementById('error').innerHTML = json.message;
      } else if (!json.error) {
        document.getElementById('user_name').innerHTML = json.user_name;
        document.getElementById('room_display').innerHTML = json.room;
      }
    } catch(err) {
      document.getElementById('error').innerHTML = err;
    }
}
