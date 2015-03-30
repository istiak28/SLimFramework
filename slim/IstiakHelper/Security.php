<?php

//put your code here
    /**
     * 
     * crypt password technique.there are one type '$2a$%02d$'
     * for more http://php.net/manual/en/function.crypt.php
     */

/**
 * Description of Sequrity
 *
 * @author istiak
 */
class Security {

    
    public function cryptPass($input, $rounds = 10) {

        $salt = "";
        $saltChars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));

        for ($i = 0; $i < 30; $i++) {

            $salt .= $saltChars[array_rand($saltChars)];
        }
        return crypt($input, sprintf('$2y$%02d$', $rounds) . $salt);
    }

}



//$sec = new Security();
//
//$pass = '123';
//$hashedPass = $sec->cryptPass($pass);
//echo $hashedPass;
//echo '<br>';
//$inputPass = "123";
//
//if (crypt($inputPass, $hashedPass) == $hashedPass) { 
//    echo "Pssword ok";
//}
//else{
//      echo "not";
//}