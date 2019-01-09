document.addEventListener('DOMContentLoaded', () => {


    // Get the messages, and refresh every second.
    get_messages();
    // setInterval(function() { get_messages(); }, 40000);

    // Get the rooms, and refresh every second.
    get_rooms();
    // setInterval(function() { get_rooms(); }, 40000);

    // Create a new room.
    check_Input('create_room', 'create_room_btn');
    document.querySelector('#create_room_btn').onclick = () => {
        var room = document.querySelector('#create_room').value;
        // Initializes the input
        document.querySelector('#create_room').value = '';
        // OPEN AJAX REQUEST
        fetchSendData('rooms.php', room, 'addRoom', 'error');
        // Request rooms list.
        get_rooms();
        get_messages();
      };

    // Add a new message.
    check_Input('message', 'submit');
    document.querySelector('#submit').onclick = () => {
        var text = document.querySelector('#message').value;
        // Initializes the input
        document.querySelector('#message').value = '';
        // OPEN AJAX REQUEST
        fetchSendData('messages.php', text, 'addMessage', 'error');
        // Request messages.
        get_messages();
        scrollToBottom();
      };
    // Upload a file.
    document.querySelector('#submit_file').onclick = () => {
        var fileInput = document.querySelector('#upload_file');
        var file = fileInput.files[0];
        // OPEN AJAX REQUEST
        fetchSendData('messages.php', file, 'addFile', 'error');
        // Request messages.
        get_messages();
        disappearing_title('loading_file', 'Uploading...', '');
        scrollToBottom();
      };

});
// Outside the DOM loading callback function.
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

// Change the user's room.
function changeRoom(room_id, room_name) {
    fetchSendData('rooms.php', room_id, 'changeRoom', 'error');
    document.getElementById("room_display").innerHTML = room_name;
    // Display the messages of the room.
    get_messages();
    get_rooms();
    scrollToBottom();
    return false;
}

// Fetch functions
async function fetchRequestData(pageToSend) {
  try {
      var response = await fetch(pageToSend);
      var text = await response.text();
      return text;
  } catch(err) {
      return err;
  }
}
// POST.
async function fetchSendData(pageToSend, dataToSend, action, elementId) {
  var data = new FormData();
  data.append('action', action);
  data.append('data', dataToSend);
  try {
      var response = await fetch(pageToSend, {
      method: 'post',
      body: data
      });
      var json = await response.json();
      if (json.result) {
        document.getElementById(elementId).innerHTML = json.message;
      } else {
        document.getElementById(elementId).innerHTML = json.message;
      }
  } catch(err) {
      document.getElementById(elementId).innerHTML = err;
  }
}

function scrollToBottom() {
    var chatDiv = document.getElementById("chat");
    chatDiv.scrollTop = chatDiv.scrollHeight;
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function disappearing_title(elementId, textBefore, textAfter) {
  document.getElementById(elementId).innerHTML = textBefore;
  await sleep(5000);
  document.getElementById(elementId).innerHTML = textAfter;
}

  async function get_messages() {
      var data = new FormData();
      data.append('action', "getMessages");
      try {
          var response = await fetch('messages.php', {
          method: 'post',
          body: data
          });
          var json = await response.json();
          if (json.result) {
            document.getElementById("messages").innerHTML = "";
            document.getElementById("error").innerHTML = "";
            for (var mes in json.message) {
              var TR = document.createElement("tr");
              document.getElementById("messages").appendChild(TR);

              var details = {
                  time      : json.message[mes]['time'],
                  user_name : json.message[mes]['user_name'],
                  text      : json.message[mes]['text']
              };
              for (const [key, value] of Object.entries(details)) {
                var TD = document.createElement("td");
                TD.innerHTML = value;
                TR.appendChild(TD);
              }
            }
          } else {
            document.getElementById('chat').innerHTML += json.message;
          }
      } catch(err) {
            document.getElementById('chat').innerHTML += err;
      }
  }

async function get_rooms() {
      var data = new FormData();
      data.append('action', "getRooms");
      try {
          var response = await fetch('rooms.php', {
          method: 'post',
          body: data
          });
          var json = await response.json();
          if (json.result) {
            document.getElementById("rooms_ul").innerHTML = "";
            for (let room in json.message) {
                let room_id      = json.message[room]['room_id'];
                let room_name    = json.message[room]['room_name'];

                let li = document.createElement("li");
                let link = document.createElement("a");
                link.href = "#";
                link.className = "list-group-item list-group-item-action";
                link.onclick = function (){ changeRoom(room_id, room_name); };
                link.id = room_id;
                link.innerHTML = room_name;

                document.getElementById("rooms_ul").appendChild(li);
                li.appendChild(link);
            }
          } else {
            document.getElementById('error').innerHTML += json.message;
          }
      } catch(err) {
          document.getElementById('error').innerHTML += err;
      }
}



// function fetchSendData(elementId, pageToSend, dataToSend) {
//   var data = new FormData();
//   data.append('data', dataToSend);
//   fetch(pageToSend, {
//     method: 'post',
//     body: data
//     })
//   .then(handleResponse)
//   .then(data => document.getElementById(elementId).innerHTML = data)
//   .catch(error => document.getElementById(elementId).innerHTML = error);
//   }

// function fetchRequestData(elementId, pageToSend) {
//   fetch(pageToSend)
//   .then(handleResponse)
//   .then(text => {
//         document.getElementById(elementId).innerHTML = text;
//       })
//   .catch(error => document.getElementById(elementId).innerHTML = error);
// }

// function handleResponse(response) {
//   var text = await response.text();
//   .then(text => {
//     if (response.ok) {
//       return text;
//     } else {
//       return Promise.reject({
//         status: response.status,
//         statusText: response.statusText,
//         err: text
//       });
//     }
//   });
// }

// // AJAX POST request
// function ajaxRequest(elementId, pageToSend, dataToSend) {
//     var request;
//     if (window.XMLHttpRequest) {
//       // code for IE7+, Firefox, Chrome, Opera, Safari
//       request = new XMLHttpRequest();
//     } else {
//       // code for IE6, IE5
//       request = new ActiveXObject("Microsoft.XMLHTTP");
//     }
//     request.onreadystatechange = function() {
//       if (this.readyState == 4 && this.status == 200) {
//           document.getElementById(elementId).innerHTML = this.responseText;
//       }
//     };
//     request.open("POST", pageToSend, true);
//     var data = new FormData();
//     data.append('data', dataToSend);
//     request.send(data);
// }

// // AJAX GET request
// function ajaxRequestGET(elementId, pageToSend) {
//       var request;
//       if (window.XMLHttpRequest) {
//         // code for IE7+, Firefox, Chrome, Opera, Safari
//         request = new XMLHttpRequest();
//       } else {
//         // code for IE6, IE5
//         request = new ActiveXObject("Microsoft.XMLHTTP");
//       }
//       request.onreadystatechange = function() {
//         if (this.readyState == 4 && this.status == 200) {
//             document.getElementById(elementId).innerHTML = this.responseText;
//         }
//       };
//       request.open("GET", pageToSend, true);
//       request.send();
// }



