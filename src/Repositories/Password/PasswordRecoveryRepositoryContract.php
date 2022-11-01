<?php

namespace App\Repositories\Password;

interface PasswordRecoveryRepositoryContract
{
    
   public function verifyEmail($email);
   
   public function verifyCode($email,$code);
   
   public function updatePassword($params ,$conditionalParams );


}
