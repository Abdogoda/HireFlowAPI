<?php

namespace App\Models;

use App\Models\CompanyMembership;
use App\Models\CompanySocialProfile;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['created_by', 'slug', 'name', 'description', 'logo', 'website', 'industry', 'company_size', 'founded_year', 'location', 'email', 'phone', 'is_verified'])]
class Company extends Model
{
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CompanyMembership::class);
    }

    public function socialProfiles(): HasMany
    {
        return $this->hasMany(CompanySocialProfile::class);
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }
}