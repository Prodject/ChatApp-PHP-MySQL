# Chat App PHP MySQL JavaScript
Chat application using PHP, MySQL and JavaScript.
Including: file uploads, channels, authentication, security issues that were taken into consideration, RESTful API, OOP, use of fetch and promises.

<b>functions.php</b>
Following Dependency Injection design pattern, I created independency classes. By passing the class object to the constructor of the other classes (for example - created an instance of DB class, passed that instance to the User class and by doing so creating it independence of DB class changes).
Furthermore, I used UserMapper in order to seperate the DB operations from the User Class. Making a better API and seperation between the website layers.

