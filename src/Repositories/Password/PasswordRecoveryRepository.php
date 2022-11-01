<?php

namespace App\Repositories\Password;

use App\Repositories\Password\PasswordRecoveryRepositoryContract;
use Illuminate\Database\Eloquent\Model;
use App\Core\Wrappers\OTP\OTP;
use Exception;

class PasswordRecoveryRepository implements PasswordRecoveryRepositoryContract
{
    protected $model;

    public function setModel(Model $model)
    {

        $this->model = $model;
    }

    public function verifyEmail($email)
    {
        try {

            $otp = new OTP();
            $otp->to($email)->send();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function verifyCode($email, $code)
    {

        try {
            $otp = new OTP();
            $result = $otp->to($email)->code($code)->verify();
            
            if (!$result) throw new \Exception('invalid code verification failed.');

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updatePassword($newPassword, $conditionalParams)
    {
        extract($conditionalParams);
        try {
            $verified = $this->verifyCode($email, $code);

            $user = $this->model->where('email', $email)->firstOrFail();
            $user->password = $newPassword;
            $user->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
