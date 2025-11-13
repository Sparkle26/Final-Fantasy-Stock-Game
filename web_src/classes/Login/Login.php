<?php

class LoginForm{

    static function render(){
        return '<section id="login">
        <div id="welcome-text">Login</div>
        <div>
        <div id="basicContainer">
            <!-- Form to get login info from user -->
            <form action="../data_src/api/user/read.php" method="post" style="text-align: left">
                <label for="username"><b>Username: </b></label>
                <input type="text" placeholder="Enter Username" name="username" id="username" required>

                <label for="password"><b>Password: </b></label>
                <input type="password" placeholder="Enter Password" name="password" id="password" required>

                <input type ="submit" value="Login">
            </form>
            
            <!-- Register button for if not already an admin -->
            <a class="nav-link" href="index.php?page=register">
                <i class="fas fa-key"></i> Register
            </a>
        </div>
        </div>
        </section>';
    }

}




?>