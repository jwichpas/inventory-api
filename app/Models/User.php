<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_empresa',
        'active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Sobrescribe el método tokens() si es necesario
    public function tokens()
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Método para crear token con scope empresa
    public function createTokenForEmpresa($empresaId, $name = 'auth_token', array $abilities = ['*'])
    {
        return $this->createToken($name, [
            'empresa_id' => $empresaId,
            ...$abilities
        ]);
    }
<<<<<<< HEAD
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'user_companies')
                    ->withTimestamps(); // Solo incluye created_at y updated_at
    }
=======
>>>>>>> develop
}
