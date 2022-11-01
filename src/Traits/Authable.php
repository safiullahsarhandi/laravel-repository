<?php

namespace LaravelRepository\Traits;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

trait Authable
{
    protected $defaultProvider = 'user';

    protected function extendValidation(): array
    {
        $valid = true;
        $message = null;

        return [$valid, $message];
    }

    public function login(array $params)
    {
        extract($params);
        try {
            $user = $this->where('email', $email)->firstOrFail();
            if (Hash::check($password, $user->password)) {
            
                list($valid, $message) = $user->extendValidation();
                if (!$valid) {
                    throw new \Exception($message);
                }
                $token = $user->createToken("{$this->getTable()} Token");
                $model = $token->accessToken;
                $model->update(['device_token' => $device_id ?? null, 'device_type' => $device_type ?? null]);
                return $token->plainTextToken;
            }
            throw new \Exception('invalid user or password');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function register(array $params)
    {
        try {
            return $this->fill($params)->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout()
    {
        try {
            return $this->currentAccessToken()->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
