# Chat App PHP MySQL JavaScript
Chat application using PHP, MySQL and JavaScript.
Including: file uploads, channels, authentication, security issues that were taken into consideration, RESTful API, OOP, use of fetch and promises.
<pre>
<b>functions.php</b>
Following Dependency Injection design pattern, the classes aren't dependent on each other.
By passing the <b>instance</b> of the DB class to the User class, I was able to make the User class totally independent of DB class changes.

A layer of Mappers that 
Furthermore, I used UserMapper which moves data between objects and the database
while keeping them independent of each other and the mapper itself.
With Data Mapper the in-memory objects needn't know even that there's a database present;
they need no SQL interface code, and certainly no knowledge of the database schema.
</pre>

