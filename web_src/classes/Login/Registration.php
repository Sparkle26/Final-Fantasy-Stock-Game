<?php

class RegistrationForm{

    static function render(){
            return '<div class="add">
            <div id="welcome-text">Register</div>
            <div id="basicContainer">
                
            <!-- Registration Form -->
            <form action="../data_src/api/user/create.php" method="post" style= "text-align: left">
                <label for="firstname"><b>First Name: </b></label>
                <input type="text" placeholder="Enter First Name" name="first" id="first" required>

                <label for="email"><b>Email: </b></label>
                <input type="text" placeholder="Enter Email" name="email" id="email" required>

                <label for="username"><b>Username: </b></label>
                <input type="text" placeholder="Enter Username" name="user" id="user" required>


                <label for="password"><b>Password: </b></label>
                <input type="password" placeholder="Enter Password" name="password" id="password" required>

                <input type ="submit" value="Register">
            </form>
            </div>
        </div>
    ';
    }

}




?>