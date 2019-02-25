<?php
  require_once 'mapper/functions.php';
  require_once 'header.php';

?>
<body>
    <h1>Login</h1>
    <p id="error"></p>
    <div class="form-group">
        <label for="username_login">Username</label>
        <input type="text" class="form-control" id="username_login" name="user_name" placeholder="Enter Username">
    </div>
    <div class="form-group">
        <label for="password_login">Password</label>
        <input type="password" class="form-control" id="password_login" name="pass" placeholder="Password">
    </div>
    <button class="btn btn-primary" id="login_btn">Submit</button>
    <div class="form-group">
        <a href="register.php">Don't have a user? Register here.</a>
    </div>


<script src="handler.js"></script>
<script>
fetchRequestLogin();
</script>
</body>
</html>
