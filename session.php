<?php
class haha
{
    public function session()
    {
        session_start();
        $_SESSION['user_id'] = '111';
        $_SESSION['account'] = "admin";
        $_SESSION['password'] = 123456;
        echo "Session variables are set ";
    }
    
    public function cookie()
    {
        $cookie_name = "name";
        $cookie_value = "khant";
        setCookie($cookie_name,$cookie_value);
        if(!isset($_COOKIE[$cookie_name])){
            echo "cookie name " .$cookie_name . "is not set";
        }else{
            echo "Cookie" .$cookie_name. "is set";
            echo "<hr>";
            echo "Value is " .$_COOKIE[$cookie_name];
        }
    }
}


