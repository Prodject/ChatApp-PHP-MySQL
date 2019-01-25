# Chat App PHP MySQL JavaScript
Chat application using PHP, MySQL and JavaScript, made by fetch() requests and long polling.
Including: file uploads, channels, authentication, security issues that were taken into consideration, RESTful API, OOP, use of ES7.<br><br>
<b>functions.php</b><br>
Important design pattern that was implemented here is the Dependency Injection, which means the classes aren't dependent on each other.<br>
By passing the <b>instance</b> of the DB class to the UserMapper class, I was able to make the UserMapper class totally independent of DB class changes.<br>
Furthermore, I used <b>UserMapper</b> which moves data between objects and the database<br>
while keeping them independent of each other and the mapper itself.<br>
With Data Mapper, the in-memory objects needn't know even that there's a database present;<br>
they need no SQL interface code, and certainly no knowledge of the database schema.

