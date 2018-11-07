<?php

namespace App;
use App\Models\BaseModelTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hash;



class User extends Authenticatable
{
    use Notifiable,BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
        'email_verified_at',
    ];

    public $validationRules = [
        'name'  => 'required',
        'email'  => 'required|email|unique:users,email',
    ];
    public $validationMessages = [
        'required' => ':attribute is required',
        'unique' => ':attribute already exists',
    ];

    public function getValidationRules()
    {
        $rules = $this->validationRules;

        if ($this->id) { //for update ignore own email for unique
            $rules['email'] = $rules['email'] . ',' . $this->id;
        }

        return $rules;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->api_token = str_random(60);
            $user->setAttribute('password', Hash::make(\Request::get('password', $user->getAttribute('password'))));
        });
    }
}
