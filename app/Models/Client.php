<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name', 'fantasy_name', 'cpf_cnpj', 'rg_ie', 'person_type',
        'email', 'phone', 'cellphone', 'zipcode', 'address', 'address_number',
        'complement', 'neighborhood', 'city', 'state', 'contact_name',
        'contact_phone', 'contact_email', 'status', 'observations',
        'profile_photo', 'created_by',
        'notification_preferences', 'last_contacted_at',
        'lead_source', 'nps_score',
    ];

    protected function casts(): array
    {
        return [
            'notification_preferences' => 'array',
            'last_contacted_at' => 'datetime',
            'nps_score' => 'decimal:1',
            'deleted_at' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'client_user')
            ->withPivot('is_main_contact');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContracts()
    {
        return $this->hasMany(Contract::class)->where('status', 'active');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function links()
    {
        return $this->hasManyThrough(Link::class, Contract::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPersonType($query, string $type)
    {
        return $query->where('person_type', $type);
    }

    public function getDocumentFormattedAttribute(): string
    {
        if ($this->person_type === 'pj') {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->cpf_cnpj);
        }
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf_cnpj);
    }

    public function getPhoneFormattedAttribute(): string
    {
        if (strlen($this->phone) === 11) {
            return '(' . substr($this->phone, 0, 2) . ') ' . substr($this->phone, 2, 5) . '-' . substr($this->phone, 7);
        }
        return '(' . substr($this->phone, 0, 2) . ') ' . substr($this->phone, 2, 4) . '-' . substr($this->phone, 6);
    }

    public function getAddressFullAttribute(): string
    {
        return "{$this->address}, {$this->address_number} - {$this->neighborhood}, {$this->city}-{$this->state}";
    }

    public function getNameDisplayAttribute(): string
    {
        return $this->fantasy_name ?? $this->company_name;
    }
}
