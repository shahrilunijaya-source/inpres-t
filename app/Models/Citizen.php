<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ic', 'full_name', 'dob', 'gender', 'address', 'postcode', 'state'])]
class Citizen extends Model
{
    use HasFactory;

    protected $casts = [
        'dob' => 'date',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'applicant_ic', 'ic');
    }
}
