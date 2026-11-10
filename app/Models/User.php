<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Scopes\UserColegioScope;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected string $guard_name = 'web';

    protected static function booted(): void
    {
        static::addGlobalScope(new UserColegioScope);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'colegio_id',
        'nombres',
        'apellidos',
        'dni',
        'telefono',
        'foto_path',
        'email',
        'password',
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

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function estudiante(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Estudiante::class);
    }

    public function hijos(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'apoderado_estudiante', 'apoderado_id', 'estudiante_id')
            ->withPivot('parentesco', 'colegio_id')
            ->withTimestamps();
    }

    public function seccionesAsignadas(): BelongsToMany
    {
        return $this->belongsToMany(Seccion::class, 'asignaciones_docentes', 'docente_id', 'seccion_id')
            ->withPivot('area_id', 'colegio_id')
            ->withTimestamps();
    }

    public function getNameAttribute(): string
    {
        return trim($this->nombres.' '.$this->apellidos);
    }
}
