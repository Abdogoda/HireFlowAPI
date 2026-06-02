<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Post;
use App\Models\Vacancy;
use App\Policies\CompanyPolicy;
use App\Policies\PostPolicy;
use App\Policies\VacancyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Post::class => PostPolicy::class,
        Vacancy::class => VacancyPolicy::class,
    ];

    public function boot(): void
    {
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Vacancy::class, VacancyPolicy::class);
    }
}