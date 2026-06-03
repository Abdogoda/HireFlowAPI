<?php

namespace App\Support;

final class OpenApiSpec
{
    public static function make(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'HireFlow API',
                'version' => '1.0.0',
                'description' => 'Swagger documentation for the HireFlow API.',
            ],
            'servers' => [
                [
                    'url' => '/api',
                    'description' => 'API base path',
                ],
            ],
            'tags' => [
                ['name' => 'Auth',    'description' => 'Authentication and account lifecycle endpoints'],
                ['name' => 'Profile', 'description' => 'Authenticated profile and portfolio endpoints'],
                ['name' => 'Company', 'description' => 'Company and company member management endpoints'],
                ['name' => 'Post',    'description' => 'Personal and company post endpoints with attachments, tags, and scheduling'],
                ['name' => 'Vacancy', 'description' => 'Job vacancy management endpoints'],
            ],
            'paths' => self::paths(),
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
                'schemas' => [
                    'ApiSuccessResponse' => [
                        'type' => 'object',
                        'required' => ['success', 'message', 'data', 'timestamp'],
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => true],
                            'message' => ['type' => 'string', 'example' => 'Operation successful'],
                            'data' => ['nullable' => true, 'type' => ['object', 'array', 'null']],
                            'timestamp' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'ApiErrorResponse' => [
                        'type' => 'object',
                        'required' => ['success', 'message', 'timestamp'],
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => false],
                            'message' => ['type' => 'string', 'example' => 'Validation failed'],
                            'errors' => ['nullable' => true, 'type' => ['object', 'array', 'null']],
                            'data' => ['nullable' => true, 'type' => ['object', 'array', 'null']],
                            'timestamp' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'Role' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'candidate'],
                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Default candidate role'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'UserSimple' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'Jane Doe'],
                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                            'avatar' => ['type' => 'string', 'nullable' => true, 'example' => 'avatars/jane.jpg'],
                            'bio' => ['type' => 'string', 'nullable' => true, 'example' => 'Product designer and frontend developer.'],
                            'email_verified_at' => ['type' => 'string', 'nullable' => true, 'format' => 'date-time'],
                            'role' => ['$ref' => '#/components/schemas/Role'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'UserProfile' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'Jane Doe'],
                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                            'avatar' => ['type' => 'string', 'nullable' => true, 'example' => 'avatars/jane.jpg'],
                            'bio' => ['type' => 'string', 'nullable' => true, 'example' => 'Product designer and frontend developer.'],
                            'phone_number' => ['type' => 'string', 'nullable' => true, 'example' => '+1 555 0100'],
                            'address' => ['type' => 'string', 'nullable' => true, 'example' => '100 Main Street'],
                            'city' => ['type' => 'string', 'nullable' => true, 'example' => 'Boston'],
                            'state' => ['type' => 'string', 'nullable' => true, 'example' => 'MA'],
                            'country' => ['type' => 'string', 'nullable' => true, 'example' => 'USA'],
                            'gender' => ['type' => 'string', 'nullable' => true, 'example' => 'female'],
                            'marital_status' => ['type' => 'string', 'nullable' => true, 'example' => 'single'],
                            'religion' => ['type' => 'string', 'nullable' => true, 'example' => 'None'],
                            'email_verified_at' => ['type' => 'string', 'nullable' => true, 'format' => 'date-time'],
                            'role' => ['$ref' => '#/components/schemas/Role'],
                            'skills' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/Skill'],
                            ],
                            'experiences' => [
                                'type' => 'array',
                                'items' => ['type' => 'object'],
                            ],
                            'projects' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/Project'],
                            ],
                            'resumes' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/Resume'],
                            ],
                            'social_profiles' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/SocialProfile'],
                            ],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'Skill' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'Laravel'],
                            'proficiency_level' => ['type' => 'string', 'nullable' => true, 'example' => 'advanced'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'Project' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'title' => ['type' => 'string', 'example' => 'HireFlow API'],
                            'description' => ['type' => 'string', 'example' => 'A recruiting platform API'],
                            'url' => ['type' => 'string', 'nullable' => true, 'format' => 'uri', 'example' => 'https://example.com'],
                            'technologies' => ['type' => 'string', 'nullable' => true, 'example' => 'Laravel, PostgreSQL'],
                            'start_date' => ['type' => 'string', 'format' => 'date', 'example' => '2026-01-01'],
                            'end_date' => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-06-01'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'Resume' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'title' => ['type' => 'string', 'nullable' => true, 'example' => 'Backend CV'],
                            'file_name' => ['type' => 'string', 'example' => 'backend-cv.pdf'],
                            'file_path' => ['type' => 'string', 'example' => 'resumes/backend-cv.pdf'],
                            'file_url' => ['type' => 'string', 'format' => 'uri', 'example' => 'https://example.com/storage/resumes/backend-cv.pdf'],
                            'is_primary' => ['type' => 'boolean', 'example' => true],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'SocialProfile' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'social_profile_type' => ['type' => 'integer', 'example' => 1],
                            'profile_url' => ['type' => 'string', 'format' => 'uri', 'example' => 'https://linkedin.com/in/janedoe'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'LoginRequest' => [
                        'type' => 'object',
                        'required' => ['email', 'password'],
                        'properties' => [
                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                            'password' => ['type' => 'string', 'example' => 'password123'],
                        ],
                    ],
                    'RegisterRequest' => [
                        'type' => 'object',
                        'required' => ['name', 'email', 'password', 'password_confirmation', 'role_id'],
                        'properties' => [
                            'name' => ['type' => 'string', 'example' => 'Jane Doe'],
                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                            'password' => ['type' => 'string', 'format' => 'password', 'example' => 'password123'],
                            'password_confirmation' => ['type' => 'string', 'format' => 'password', 'example' => 'password123'],
                            'role_id' => ['type' => 'integer', 'example' => 1],
                        ],
                    ],
                    'ForgotPasswordRequest' => [
                        'type' => 'object',
                        'required' => ['email'],
                        'properties' => [
                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                        ],
                    ],
                    'ResetPasswordRequest' => [
                        'type' => 'object',
                        'required' => ['token', 'email', 'password', 'password_confirmation'],
                        'properties' => [
                            'token' => ['type' => 'string', 'example' => 'reset-token-here'],
                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                            'password' => ['type' => 'string', 'format' => 'password', 'example' => 'newpassword123'],
                            'password_confirmation' => ['type' => 'string', 'format' => 'password', 'example' => 'newpassword123'],
                        ],
                    ],
                    'UpdateProfileRequest' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string', 'example' => 'Jane Doe'],
                            'bio' => ['type' => 'string', 'nullable' => true, 'example' => 'Product designer and frontend developer.'],
                            'phone_number' => ['type' => 'string', 'nullable' => true, 'example' => '+1 555 0100'],
                            'address' => ['type' => 'string', 'nullable' => true, 'example' => '100 Main Street'],
                            'city' => ['type' => 'string', 'nullable' => true, 'example' => 'Boston'],
                            'state' => ['type' => 'string', 'nullable' => true, 'example' => 'MA'],
                            'country' => ['type' => 'string', 'nullable' => true, 'example' => 'USA'],
                            'gender' => ['type' => 'string', 'nullable' => true, 'enum' => ['male', 'female', 'other']],
                            'marital_status' => ['type' => 'string', 'nullable' => true, 'enum' => ['single', 'married', 'divorced', 'widowed']],
                            'religion' => ['type' => 'string', 'nullable' => true, 'example' => 'None'],
                        ],
                    ],
                    'PictureUploadRequest' => [
                        'type' => 'object',
                        'required' => ['picture', 'type'],
                        'properties' => [
                            'picture' => ['type' => 'string', 'format' => 'binary'],
                            'type' => ['type' => 'string', 'enum' => ['profile', 'thumbnail']],
                        ],
                    ],
                    'AvatarUploadRequest' => [
                        'type' => 'object',
                        'required' => ['avatar'],
                        'properties' => [
                            'avatar' => ['type' => 'string', 'format' => 'binary'],
                        ],
                    ],
                    'CvUploadRequest' => [
                        'type' => 'object',
                        'required' => ['file'],
                        'properties' => [
                            'file' => ['type' => 'string', 'format' => 'binary'],
                            'title' => ['type' => 'string', 'nullable' => true, 'example' => 'Backend CV'],
                        ],
                    ],
                    'StoreProjectRequest' => [
                        'type' => 'object',
                        'required' => ['title', 'description', 'start_date'],
                        'properties' => [
                            'title' => ['type' => 'string', 'example' => 'HireFlow API'],
                            'description' => ['type' => 'string', 'example' => 'A recruiting platform API'],
                            'url' => ['type' => 'string', 'nullable' => true, 'format' => 'uri', 'example' => 'https://example.com'],
                            'start_date' => ['type' => 'string', 'format' => 'date', 'example' => '2026-01-01'],
                            'end_date' => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-06-01'],
                            'technologies' => ['type' => 'string', 'nullable' => true, 'example' => 'Laravel, PostgreSQL'],
                        ],
                    ],
                    'UpdateProjectRequest' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string', 'example' => 'HireFlow API'],
                            'description' => ['type' => 'string', 'example' => 'A recruiting platform API'],
                            'url' => ['type' => 'string', 'nullable' => true, 'format' => 'uri', 'example' => 'https://example.com'],
                            'start_date' => ['type' => 'string', 'format' => 'date', 'example' => '2026-01-01'],
                            'end_date' => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-06-01'],
                            'technologies' => ['type' => 'string', 'nullable' => true, 'example' => 'Laravel, PostgreSQL'],
                        ],
                    ],
                    'StoreSkillRequest' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string', 'example' => 'Laravel'],
                            'proficiency_level' => ['type' => 'string', 'nullable' => true, 'enum' => ['beginner', 'intermediate', 'advanced', 'expert']],
                            'years_of_experience' => ['type' => 'number', 'nullable' => true, 'example' => 4],
                            'endorsement_count' => ['type' => 'integer', 'nullable' => true, 'example' => 12],
                        ],
                    ],
                    'UpdateSkillRequest' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string', 'example' => 'Laravel'],
                            'proficiency_level' => ['type' => 'string', 'nullable' => true, 'enum' => ['beginner', 'intermediate', 'advanced', 'expert']],
                            'years_of_experience' => ['type' => 'number', 'nullable' => true, 'example' => 4],
                            'endorsement_count' => ['type' => 'integer', 'nullable' => true, 'example' => 12],
                        ],
                    ],
                    'StoreSocialProfileRequest' => [
                        'type' => 'object',
                        'required' => ['social_profile_type', 'profile_url'],
                        'properties' => [
                            'social_profile_type' => ['type' => 'integer', 'enum' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'example' => 1],
                            'profile_url' => ['type' => 'string', 'format' => 'uri', 'example' => 'https://linkedin.com/in/janedoe'],
                        ],
                    ],
                    'UpdateSocialProfileRequest' => [
                        'type' => 'object',
                        'properties' => [
                            'social_profile_type' => ['type' => 'integer', 'enum' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'example' => 1],
                            'profile_url' => ['type' => 'string', 'format' => 'uri', 'example' => 'https://linkedin.com/in/janedoe'],
                        ],
                    ],
                    'Company' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'slug' => ['type' => 'string', 'example' => 'acme-ltd'],
                            'name' => ['type' => 'string', 'example' => 'Acme Ltd'],
                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'A sample company'],
                            'logo' => ['type' => 'string', 'nullable' => true, 'example' => 'logos/acme.png'],
                            'website' => ['type' => 'string', 'nullable' => true, 'format' => 'uri', 'example' => 'https://acme.test'],
                            'industry' => ['type' => 'string', 'nullable' => true, 'example' => 'Technology'],
                            'company_size' => ['type' => 'string', 'nullable' => true, 'example' => '51-200'],
                            'founded_year' => ['type' => 'integer', 'nullable' => true, 'example' => 2020],
                            'location' => ['type' => 'string', 'nullable' => true, 'example' => 'London, UK'],
                            'email' => ['type' => 'string', 'nullable' => true, 'format' => 'email', 'example' => 'hello@acme.test'],
                            'phone' => ['type' => 'string', 'nullable' => true, 'example' => '123456789'],
                            'created_by' => ['type' => 'integer', 'example' => 1],
                            'is_verified' => ['type' => 'boolean', 'example' => true],
                            'owner' => ['$ref' => '#/components/schemas/CompanyOwner'],
                            'people' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/CompanyPerson'],
                            ],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'CompanyPerson' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'company_role' => ['type' => 'string', 'example' => 'recruiter'],
                            'position' => ['type' => 'string', 'example' => 'Developer'],
                            'information' => ['type' => 'string', 'nullable' => true, 'example' => 'Works on backend features'],
                            'start_date' => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-01-01'],
                            'end_date' => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-12-31'],
                            'is_current_position' => ['type' => 'boolean', 'example' => true],
                            'user' => ['$ref' => '#/components/schemas/UserSimple'],
                            'is_company_owner' => ['type' => 'boolean', 'example' => false],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'CompanyOwner' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'Jane Doe'],
                            'email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                            'avatar' => ['type' => 'string', 'nullable' => true, 'example' => 'avatars/jane.jpg'],
                            'bio' => ['type' => 'string', 'nullable' => true, 'example' => 'Product designer and frontend developer.'],
                            'email_verified_at' => ['type' => 'string', 'nullable' => true, 'format' => 'date-time'],
                            'role' => ['$ref' => '#/components/schemas/Role'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'company_role' => ['type' => 'string', 'example' => 'owner'],
                        ],
                    ],
                    'StoreCompanyRequest' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string', 'example' => 'Acme Ltd'],
                            'slug' => ['type' => 'string', 'nullable' => true, 'example' => 'acme-ltd'],
                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'A sample company'],
                            'logo' => ['type' => 'string', 'nullable' => true, 'example' => 'logos/acme.png'],
                            'website' => ['type' => 'string', 'nullable' => true, 'format' => 'uri', 'example' => 'https://acme.test'],
                            'industry' => ['type' => 'string', 'nullable' => true, 'example' => 'Technology'],
                            'company_size' => ['type' => 'string', 'nullable' => true, 'example' => '51-200'],
                            'founded_year' => ['type' => 'integer', 'nullable' => true, 'example' => 2020],
                            'location' => ['type' => 'string', 'nullable' => true, 'example' => 'London, UK'],
                            'email' => ['type' => 'string', 'nullable' => true, 'format' => 'email', 'example' => 'hello@acme.test'],
                            'phone' => ['type' => 'string', 'nullable' => true, 'example' => '123456789'],
                            'is_verified' => ['type' => 'boolean', 'nullable' => true, 'example' => false],
                        ],
                    ],
                    'UpdateCompanyRequest' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string', 'example' => 'Acme Ltd'],
                            'slug' => ['type' => 'string', 'nullable' => true, 'example' => 'acme-ltd'],
                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'A sample company'],
                            'logo' => ['type' => 'string', 'nullable' => true, 'example' => 'logos/acme.png'],
                            'website' => ['type' => 'string', 'nullable' => true, 'format' => 'uri', 'example' => 'https://acme.test'],
                            'industry' => ['type' => 'string', 'nullable' => true, 'example' => 'Technology'],
                            'company_size' => ['type' => 'string', 'nullable' => true, 'example' => '51-200'],
                            'founded_year' => ['type' => 'integer', 'nullable' => true, 'example' => 2020],
                            'location' => ['type' => 'string', 'nullable' => true, 'example' => 'London, UK'],
                            'email' => ['type' => 'string', 'nullable' => true, 'format' => 'email', 'example' => 'hello@acme.test'],
                            'phone' => ['type' => 'string', 'nullable' => true, 'example' => '123456789'],
                            'is_verified' => ['type' => 'boolean', 'nullable' => true, 'example' => true],
                        ],
                    ],
                    'StoreCompanyPersonRequest' => [
                        'type' => 'object',
                        'required' => ['member_id', 'company_role', 'position'],
                        'properties' => [
                            'member_id'           => ['type' => 'integer', 'example' => 2],
                            'company_role'        => ['type' => 'string', 'enum' => ['owner', 'admin', 'recruiter', 'employee'], 'example' => 'recruiter'],
                            'position'            => ['type' => 'string', 'example' => 'Developer'],
                            'information'         => ['type' => 'string', 'nullable' => true, 'example' => 'Works on backend features'],
                            'start_date'          => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-01-01'],
                            'end_date'            => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-12-31'],
                            'is_current_position' => ['type' => 'boolean', 'nullable' => true, 'example' => true],
                        ],
                    ],
                    'UpdateCompanyPersonRequest' => [
                        'type' => 'object',
                        'properties' => [
                            'company_role'        => ['type' => 'string', 'enum' => ['owner', 'admin', 'recruiter', 'employee'], 'example' => 'admin'],
                            'position'            => ['type' => 'string', 'example' => 'Operations Lead'],
                            'information'         => ['type' => 'string', 'nullable' => true, 'example' => 'Handles recruiting operations'],
                            'start_date'          => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-02-01'],
                            'end_date'            => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-12-31'],
                            'is_current_position' => ['type' => 'boolean', 'nullable' => true, 'example' => true],
                        ],
                    ],
                    'CompanySocialProfile' => [
                        'type' => 'object',
                        'properties' => [
                            'id'                  => ['type' => 'integer', 'example' => 1],
                            'social_profile_type' => ['type' => 'integer', 'example' => 1, 'description' => '1=LinkedIn, 2=GitHub, 3=Twitter, 4=Facebook, 5=Instagram, 6=TikTok, 7=Discord, 8=YouTube, 9=Website, 10=Other'],
                            'profile_url'         => ['type' => 'string', 'format' => 'uri', 'example' => 'https://linkedin.com/company/acme'],
                            'created_at'          => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at'          => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'StoreCompanySocialProfileRequest' => [
                        'type' => 'object',
                        'required' => ['social_profile_type', 'profile_url'],
                        'properties' => [
                            'social_profile_type' => ['type' => 'integer', 'enum' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'example' => 1],
                            'profile_url'         => ['type' => 'string', 'format' => 'uri', 'example' => 'https://linkedin.com/company/acme'],
                        ],
                    ],
                    'UpdateCompanySocialProfileRequest' => [
                        'type' => 'object',
                        'properties' => [
                            'social_profile_type' => ['type' => 'integer', 'enum' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'example' => 2],
                            'profile_url'         => ['type' => 'string', 'format' => 'uri', 'example' => 'https://github.com/acme'],
                        ],
                    ],
                    'Tag' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'hiring'],
                            'slug' => ['type' => 'string', 'example' => 'hiring'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'PostAttachment' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'file_name' => ['type' => 'string', 'example' => 'brief.pdf'],
                            'file_path' => ['type' => 'string', 'example' => 'posts/1/attachments/brief.pdf'],
                            'file_url' => ['type' => 'string', 'format' => 'uri', 'example' => 'https://example.com/storage/posts/1/attachments/brief.pdf'],
                            'mime_type' => ['type' => 'string', 'nullable' => true, 'example' => 'application/pdf'],
                            'size' => ['type' => 'integer', 'nullable' => true, 'example' => 122880],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'Post' => [
                        'type' => 'object',
                        'properties' => [
                            'id'           => ['type' => 'integer', 'example' => 1],
                            'user_id'      => ['type' => 'integer', 'example' => 1],
                            'company_id'   => ['type' => 'integer', 'nullable' => true, 'example' => 1],
                            'title'        => ['type' => 'string', 'example' => 'Weekly Hiring Update'],
                            'content'      => ['type' => 'string', 'example' => '<p>We are <strong>hiring</strong> engineers.</p>'],
                            'category'     => ['type' => 'string', 'enum' => ['general', 'announcement', 'update', 'event', 'news'], 'example' => 'update'],
                            'status'       => ['type' => 'string', 'enum' => ['draft', 'published', 'scheduled', 'archived'], 'example' => 'published'],
                            'post_date'    => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-06-02', 'description' => 'Required when status is `scheduled`.'],
                            'post_time'    => ['type' => 'string', 'nullable' => true, 'example' => '10:30:00', 'description' => 'Required when status is `scheduled`. Accepts H:i or H:i:s format.'],
                            'scheduled_at' => [
                                'type'        => 'string',
                                'nullable'    => true,
                                'format'      => 'date-time',
                                'example'     => '2026-06-10T14:30:00',
                                'description' => 'Computed ISO-8601 field combining post_date + post_time. Null when either field is not set.',
                            ],
                            'author'       => ['$ref' => '#/components/schemas/UserSimple'],
                            'company'      => [
                                'nullable' => true,
                                'type'     => ['object', 'null'],
                                'properties' => [
                                    'id'   => ['type' => 'integer', 'example' => 1],
                                    'name' => ['type' => 'string', 'example' => 'Acme Jobs'],
                                    'slug' => ['type' => 'string', 'example' => 'acme-jobs'],
                                ],
                            ],
                            'tags' => [
                                'type'  => 'array',
                                'items' => ['$ref' => '#/components/schemas/Tag'],
                            ],
                            'attachments' => [
                                'type'  => 'array',
                                'items' => ['$ref' => '#/components/schemas/PostAttachment'],
                            ],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                            'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'StorePostRequest' => [
                        'type'     => 'object',
                        'required' => ['title', 'content', 'category'],
                        'description' => 'When status is `scheduled`, both post_date (today or future) and post_time are required. The scheduler (runs every minute via cron) will automatically flip the status to `published` when the scheduled time arrives.',
                        'properties' => [
                            'company_id' => ['type' => 'integer', 'nullable' => true, 'example' => 1],
                            'title'      => ['type' => 'string', 'example' => 'Weekly Hiring Update'],
                            'content'    => ['type' => 'string', 'example' => '<p>We are <strong>hiring</strong> engineers.</p>'],
                            'category'   => ['type' => 'string', 'enum' => ['general', 'announcement', 'update', 'event', 'news'], 'example' => 'update'],
                            'status'     => ['type' => 'string', 'enum' => ['draft', 'published', 'scheduled', 'archived'], 'example' => 'draft', 'description' => 'Defaults to `draft`. Setting to `published` auto-fills post_date/post_time with the current timestamp.'],
                            'post_date'  => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-06-10', 'description' => 'Required when status=scheduled. Must be today or a future date.'],
                            'post_time'  => ['type' => 'string', 'nullable' => true, 'example' => '14:30', 'description' => 'Required when status=scheduled. Accepts H:i (e.g. 14:30) or H:i:s (e.g. 14:30:00).'],
                            'tags' => [
                                'type'    => 'array',
                                'items'   => ['type' => 'string'],
                                'example' => ['hiring', 'engineering'],
                            ],
                            'attachments' => [
                                'type'  => 'array',
                                'items' => ['type' => 'string', 'format' => 'binary'],
                            ],
                        ],
                    ],
                    'UpdatePostRequest' => [
                        'type'        => 'object',
                        'description' => 'All fields are optional. When changing status to `scheduled`, post_date and post_time become required.',
                        'properties'  => [
                            'title'     => ['type' => 'string', 'example' => 'Weekly Hiring Update'],
                            'content'   => ['type' => 'string', 'example' => '<p>Updated post body.</p>'],
                            'category'  => ['type' => 'string', 'enum' => ['general', 'announcement', 'update', 'event', 'news'], 'example' => 'announcement'],
                            'status'    => ['type' => 'string', 'enum' => ['draft', 'published', 'scheduled', 'archived'], 'example' => 'scheduled'],
                            'post_date' => ['type' => 'string', 'nullable' => true, 'format' => 'date', 'example' => '2026-06-15', 'description' => 'Required when status=scheduled. Must be today or a future date.'],
                            'post_time' => ['type' => 'string', 'nullable' => true, 'example' => '09:00', 'description' => 'Required when status=scheduled. Accepts H:i or H:i:s.'],
                            'tags' => [
                                'type'    => 'array',
                                'items'   => ['type' => 'string'],
                                'example' => ['updates', 'team'],
                            ],
                            'attachments' => [
                                'type'  => 'array',
                                'items' => ['type' => 'string', 'format' => 'binary'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function paths(): array
    {
        return [
            '/auth/register' => [
                'post' => self::operation(
                    'Register a new user',
                    'Auth',
                    self::requestBody('#/components/schemas/RegisterRequest'),
                    self::success('User registered successfully. Please verify your email.', [
                        'user' => self::userSimpleExample(),
                    ], 201),
                    [
                        '400' => self::error('Validation failed'),
                        '422' => self::error('Validation failed'),
                    ],
                ),
            ],
            '/auth/login' => [
                'post' => self::operation(
                    'Authenticate an existing user',
                    'Auth',
                    self::requestBody('#/components/schemas/LoginRequest'),
                    self::success('Login successful', [
                        'user' => self::userSimpleExample(),
                        'token' => '1|example-token',
                    ]),
                    [
                        '401' => self::error('Invalid credentials'),
                        '422' => self::error('Validation failed'),
                    ],
                ),
            ],
            '/auth/forgot-password' => [
                'post' => self::operation(
                    'Send a password reset link',
                    'Auth',
                    self::requestBody('#/components/schemas/ForgotPasswordRequest'),
                    self::success('Password reset link sent to your email', null),
                    [
                        '404' => self::error('We could not find a user with that email address.'),
                        '422' => self::error('Validation failed'),
                    ],
                ),
            ],
            '/auth/reset-password' => [
                'post' => self::operation(
                    'Reset a password using a token',
                    'Auth',
                    self::requestBody('#/components/schemas/ResetPasswordRequest'),
                    self::success('Password reset successfully', null),
                    [
                        '404' => self::error('We could not find a user with that email address.'),
                        '422' => self::error('Validation failed'),
                    ],
                ),
            ],
            '/auth/verify-email/{id}/{hash}' => [
                'get' => self::operation(
                    'Verify a user email address',
                    'Auth',
                    null,
                    self::success('Email verified successfully', null),
                    [
                        '401' => self::error('Invalid signature or unauthenticated request'),
                        '403' => self::error('Invalid verification link'),
                        '422' => self::error('Validation failed'),
                    ],
                    false,
                    [
                        self::parameter('id', 'path', 'string', 'User id'),
                        self::parameter('hash', 'path', 'string', 'Signed verification hash'),
                    ],
                    'The verification link must be signed and will expire according to the application settings.',
                ),
            ],
            '/auth/logout' => [
                'post' => self::operation(
                    'Revoke the current access token',
                    'Auth',
                    null,
                    self::success('Logout successful', null),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
            ],
            '/auth/refresh-token' => [
                'post' => self::operation(
                    'Refresh the current access token',
                    'Auth',
                    null,
                    self::success('Token refreshed successfully', [
                        'token' => '1|refreshed-token',
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
            ],
            '/auth/resend-verification-email' => [
                'post' => self::operation(
                    'Resend the email verification link',
                    'Auth',
                    null,
                    self::success('Verification email sent', null),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
            ],
            '/profile' => [
                'get' => self::operation(
                    'Get the authenticated user profile',
                    'Profile',
                    null,
                    self::success('Profile retrieved successfully', [
                        'profile' => self::userProfileExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
                'patch' => self::operation(
                    'Update the authenticated user profile',
                    'Profile',
                    self::requestBody('#/components/schemas/UpdateProfileRequest'),
                    self::success('Profile updated successfully', [
                        'profile' => self::userProfileExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
            ],
            '/profile/avatar' => [
                'post' => self::operation(
                    'Upload a profile avatar',
                    'Profile',
                    self::multipartBody('#/components/schemas/AvatarUploadRequest'),
                    self::success('Avatar uploaded successfully', [
                        'url' => 'https://example.com/storage/avatars/jane.jpg',
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
                'delete' => self::operation(
                    'Delete the current profile avatar',
                    'Profile',
                    null,
                    self::success('Avatar deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
            ],
            '/profile/picture' => [
                'post' => self::operation(
                    'Upload a profile picture or thumbnail',
                    'Profile',
                    self::multipartBody('#/components/schemas/PictureUploadRequest'),
                    self::success('Picture uploaded successfully', [
                        'url' => 'https://example.com/storage/profile-picture.jpg',
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
                'delete' => self::operation(
                    'Delete a profile picture or thumbnail',
                    'Profile',
                    null,
                    self::success('Picture deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
            ],
            '/profile/cvs' => [
                'get' => self::operation(
                    'List the authenticated user CVs',
                    'Profile',
                    null,
                    self::success('CVs retrieved successfully', [
                        'cvs' => [self::resumeExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
                'post' => self::operation(
                    'Upload a CV or resume',
                    'Profile',
                    self::multipartBody('#/components/schemas/CvUploadRequest'),
                    self::success('CV uploaded successfully', [
                        'cv' => self::resumeExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
            ],
            '/profile/cvs/{cv}' => [
                'get' => self::operation(
                    'Get a specific CV or resume',
                    'Profile',
                    null,
                    self::success('CV retrieved successfully', [
                        'cv' => self::resumeExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to view this CV'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('cv', 'path', 'integer', 'CV id')],
                ),
                'delete' => self::operation(
                    'Delete a CV or resume',
                    'Profile',
                    null,
                    self::success('CV deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to delete this CV'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('cv', 'path', 'integer', 'CV id')],
                ),
            ],
            '/profile/projects' => [
                'get' => self::operation(
                    'List the authenticated user projects',
                    'Profile',
                    null,
                    self::success('Projects retrieved successfully', [
                        'projects' => [self::projectExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
                'post' => self::operation(
                    'Create a project',
                    'Profile',
                    self::requestBody('#/components/schemas/StoreProjectRequest'),
                    self::success('Project created successfully', [
                        'project' => self::projectExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
            ],
            '/profile/projects/{project}' => [
                'get' => self::operation(
                    'Get a specific project',
                    'Profile',
                    null,
                    self::success('Project retrieved successfully', [
                        'project' => self::projectExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to view this project'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('project', 'path', 'integer', 'Project id')],
                ),
                'patch' => self::operation(
                    'Update a project',
                    'Profile',
                    self::requestBody('#/components/schemas/UpdateProjectRequest'),
                    self::success('Project updated successfully', [
                        'project' => self::projectExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to update this project'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [self::parameter('project', 'path', 'integer', 'Project id')],
                ),
                'delete' => self::operation(
                    'Delete a project',
                    'Profile',
                    null,
                    self::success('Project deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to delete this project'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('project', 'path', 'integer', 'Project id')],
                ),
            ],
            '/profile/skills' => [
                'get' => self::operation(
                    'List the authenticated user skills',
                    'Profile',
                    null,
                    self::success('Skills retrieved successfully', [
                        'skills' => [self::skillExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
                'post' => self::operation(
                    'Create a skill',
                    'Profile',
                    self::requestBody('#/components/schemas/StoreSkillRequest'),
                    self::success('Skill created successfully', [
                        'skill' => self::skillExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
            ],
            '/profile/skills/{skill}' => [
                'get' => self::operation(
                    'Get a specific skill',
                    'Profile',
                    null,
                    self::success('Skill retrieved successfully', [
                        'skill' => self::skillExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to view this skill'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('skill', 'path', 'integer', 'Skill id')],
                ),
                'patch' => self::operation(
                    'Update a skill',
                    'Profile',
                    self::requestBody('#/components/schemas/UpdateSkillRequest'),
                    self::success('Skill updated successfully', [
                        'skill' => self::skillExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to update this skill'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [self::parameter('skill', 'path', 'integer', 'Skill id')],
                ),
                'delete' => self::operation(
                    'Delete a skill',
                    'Profile',
                    null,
                    self::success('Skill deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to delete this skill'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('skill', 'path', 'integer', 'Skill id')],
                ),
            ],
            '/profile/social-profiles' => [
                'get' => self::operation(
                    'List the authenticated user social profiles',
                    'Profile',
                    null,
                    self::success('Social profiles retrieved successfully', [
                        'social_profiles' => [self::socialProfileExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                ),
                'post' => self::operation(
                    'Create a social profile',
                    'Profile',
                    self::requestBody('#/components/schemas/StoreSocialProfileRequest'),
                    self::success('Social profile created successfully', [
                        'social_profile' => self::socialProfileExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
            ],
            '/profile/social-profiles/{socialProfile}' => [
                'get' => self::operation(
                    'Get a specific social profile',
                    'Profile',
                    null,
                    self::success('Social profile retrieved successfully', [
                        'social_profile' => self::socialProfileExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to view this social profile'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('socialProfile', 'path', 'integer', 'Social profile id')],
                ),
                'patch' => self::operation(
                    'Update a social profile',
                    'Profile',
                    self::requestBody('#/components/schemas/UpdateSocialProfileRequest'),
                    self::success('Social profile updated successfully', [
                        'social_profile' => self::socialProfileExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to update this social profile'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [self::parameter('socialProfile', 'path', 'integer', 'Social profile id')],
                ),
                'delete' => self::operation(
                    'Delete a social profile',
                    'Profile',
                    null,
                    self::success('Social profile deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to delete this social profile'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('socialProfile', 'path', 'integer', 'Social profile id')],
                ),
            ],
            '/companies' => [
                'get' => self::operation(
                    'List all companies with search and filters',
                    'Company',
                    null,
                    self::success('Companies retrieved successfully', [
                        'companies' => [self::companyExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                    [
                        self::parameter('search', 'query', 'string', 'Search across name, slug, description, industry, and location', false),
                        self::parameter('industry', 'query', 'string', 'Filter by exact industry', false),
                        self::parameter('location', 'query', 'string', 'Filter by partial location', false),
                        self::parameter('company_size', 'query', 'string', 'Filter by exact company size', false),
                        self::parameter('is_verified', 'query', 'boolean', 'Filter by verification status', false),
                        self::parameter('founded_year', 'query', 'integer', 'Filter by founded year', false),
                        self::parameter('sort_by', 'query', 'string', 'Sort field: name, created_at, founded_year', false),
                        self::parameter('sort_direction', 'query', 'string', 'Sort direction: asc or desc', false),
                    ],
                ),
                'post' => self::operation(
                    'Create a company',
                    'Company',
                    self::requestBody('#/components/schemas/StoreCompanyRequest'),
                    self::success('Company created successfully', [
                        'company' => self::companyExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
            ],
            '/companies/my' => [
                'get' => self::operation(
                    'List companies for the authenticated user',
                    'Company',
                    null,
                    self::success('User companies retrieved successfully', [
                        'companies' => [self::companyExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                    [
                        self::parameter('search', 'query', 'string', 'Search across name, slug, description, industry, and location', false),
                        self::parameter('industry', 'query', 'string', 'Filter by exact industry', false),
                        self::parameter('location', 'query', 'string', 'Filter by partial location', false),
                        self::parameter('company_size', 'query', 'string', 'Filter by exact company size', false),
                        self::parameter('is_verified', 'query', 'boolean', 'Filter by verification status', false),
                        self::parameter('founded_year', 'query', 'integer', 'Filter by founded year', false),
                        self::parameter('sort_by', 'query', 'string', 'Sort field: name, created_at, founded_year', false),
                        self::parameter('sort_direction', 'query', 'string', 'Sort direction: asc or desc', false),
                    ],
                ),
            ],
            '/companies/{company}' => [
                'get' => self::operation(
                    'Get a specific company',
                    'Company',
                    null,
                    self::success('Company retrieved successfully', [
                        'company' => self::companyExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('company', 'path', 'integer', 'Company id')],
                ),
                'patch' => self::operation(
                    'Update a company',
                    'Company',
                    self::requestBody('#/components/schemas/UpdateCompanyRequest'),
                    self::success('Company updated successfully', [
                        'company' => self::companyExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to update this company'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [self::parameter('company', 'path', 'integer', 'Company id')],
                ),
                'delete' => self::operation(
                    'Delete a company',
                    'Company',
                    null,
                    self::success('Company deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to delete this company'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('company', 'path', 'integer', 'Company id')],
                ),
            ],
            '/companies/{company}/people' => [
                'get' => self::operation(
                    'List all people in a company',
                    'Company',
                    null,
                    self::success('Company members retrieved successfully', [
                        'members' => [self::companyPersonExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('company', 'path', 'integer', 'Company id')],
                ),
                'post' => self::operation(
                    'Add a person to a company',
                    'Company',
                    self::requestBody('#/components/schemas/StoreCompanyPersonRequest'),
                    self::success('Person added to company successfully', [
                        'person' => self::companyPersonExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to manage company people'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [self::parameter('company', 'path', 'integer', 'Company id')],
                ),
            ],
            '/companies/{company}/people/{membership}' => [
                'patch' => self::operation(
                    'Update a company member membership',
                    'Company',
                    self::requestBody('#/components/schemas/UpdateCompanyPersonRequest'),
                    self::success('Person updated successfully', [
                        'person' => self::companyPersonExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to manage company people'),
                        '404' => self::error('Resource not found'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [
                        self::parameter('company', 'path', 'integer', 'Company id'),
                        self::parameter('membership', 'path', 'integer', 'Company membership id'),
                    ],
                ),
                'delete' => self::operation(
                    'Remove a company member',
                    'Company',
                    null,
                    self::success('Person removed from company successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to manage company people'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [
                        self::parameter('company', 'path', 'integer', 'Company id'),
                        self::parameter('membership', 'path', 'integer', 'Company membership id'),
                    ],
                ),
            ],
            '/companies/{company}/social-profiles' => [
                'get' => self::operation(
                    'List all social profiles for a company',
                    'Company',
                    null,
                    self::success('Company social profiles retrieved successfully', [
                        'social_profiles' => [self::companySocialProfileExample()],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('company', 'path', 'integer', 'Company id')],
                ),
                'post' => self::operation(
                    'Add a social profile to a company',
                    'Company',
                    self::requestBody('#/components/schemas/StoreCompanySocialProfileRequest'),
                    self::success('Company social profile created successfully', [
                        'social_profile' => self::companySocialProfileExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to manage this company'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [self::parameter('company', 'path', 'integer', 'Company id')],
                ),
            ],
            '/companies/{company}/social-profiles/{socialProfile}' => [
                'get' => self::operation(
                    'Get a specific company social profile',
                    'Company',
                    null,
                    self::success('Company social profile retrieved successfully', [
                        'social_profile' => self::companySocialProfileExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('This social profile does not belong to this company'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [
                        self::parameter('company', 'path', 'integer', 'Company id'),
                        self::parameter('socialProfile', 'path', 'integer', 'Social profile id'),
                    ],
                ),
                'patch' => self::operation(
                    'Update a company social profile',
                    'Company',
                    self::requestBody('#/components/schemas/UpdateCompanySocialProfileRequest'),
                    self::success('Company social profile updated successfully', [
                        'social_profile' => self::companySocialProfileExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to manage this company'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [
                        self::parameter('company', 'path', 'integer', 'Company id'),
                        self::parameter('socialProfile', 'path', 'integer', 'Social profile id'),
                    ],
                ),
                'delete' => self::operation(
                    'Delete a company social profile',
                    'Company',
                    null,
                    self::success('Company social profile deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to manage this company'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [
                        self::parameter('company', 'path', 'integer', 'Company id'),
                        self::parameter('socialProfile', 'path', 'integer', 'Social profile id'),
                    ],
                ),
            ],
            '/posts' => [
                'get' => self::operation(
                    'List posts with search, filters, and pagination',
                    'Post',
                    null,
                    self::success('Posts retrieved successfully', [
                        'posts' => [self::postExample()],
                        'pagination' => [
                            'current_page' => 1,
                            'total' => 1,
                            'per_page' => 15,
                            'last_page' => 1,
                            'from' => 1,
                            'to' => 1,
                        ],
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                    ],
                    true,
                    [
                        self::parameter('search', 'query', 'string', 'Search across title, content, company, and tags', false),
                        self::parameter('company_id', 'query', 'integer', 'Filter by company id', false),
                        self::parameter('user_id', 'query', 'integer', 'Filter by author id', false),
                        self::parameter('category', 'query', 'string', 'Filter by category', false),
                        self::parameter('status', 'query', 'string', 'Filter by status', false),
                        self::parameter('tag', 'query', 'string', 'Filter by a tag name or slug', false),
                        self::parameter('per_page', 'query', 'integer', 'Items per page', false),
                        self::parameter('sort_by', 'query', 'string', 'Sort field: title, post_date, post_time, status, category, created_at, updated_at', false),
                        self::parameter('sort_direction', 'query', 'string', 'Sort direction: asc or desc', false),
                    ],
                ),
                'post' => self::operation(
                    'Create a post',
                    'Post',
                    self::multipartBody('#/components/schemas/StorePostRequest'),
                    self::success('Post created successfully', [
                        'post' => self::postExample(),
                    ], 201),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to create this post'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                ),
            ],
            '/posts/{post}' => [
                'get' => self::operation(
                    'Get a specific post',
                    'Post',
                    null,
                    self::success('Post retrieved successfully', [
                        'post' => self::postExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('post', 'path', 'integer', 'Post id')],
                ),
                'patch' => self::operation(
                    'Update a post',
                    'Post',
                    self::multipartBody('#/components/schemas/UpdatePostRequest'),
                    self::success('Post updated successfully', [
                        'post' => self::postExample(),
                    ]),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to update this post'),
                        '422' => self::error('Validation failed'),
                    ],
                    true,
                    [self::parameter('post', 'path', 'integer', 'Post id')],
                ),
                'delete' => self::operation(
                    'Delete a post',
                    'Post',
                    null,
                    self::success('Post deleted successfully', null),
                    [
                        '401' => self::error('Unauthenticated'),
                        '403' => self::error('You do not have permission to delete this post'),
                        '404' => self::error('Resource not found'),
                    ],
                    true,
                    [self::parameter('post', 'path', 'integer', 'Post id')],
                ),
            ],
        ];
    }

    private static function operation(
        string $summary,
        string $tag,
        ?array $requestBody,
        array $successResponse,
        array $errorResponses,
        bool $secured = false,
        array $parameters = [],
        ?string $description = null,
    ): array {
        $successStatusCode = $successResponse['statusCode'] ?? 200;
        $successContent = $successResponse['response'] ?? $successResponse;

        $operation = [
            'tags' => [$tag],
            'summary' => $summary,
            'description' => $description ?? $summary,
            'responses' => [
                (string) $successStatusCode => $successContent,
            ] + $errorResponses,
        ];

        if ($secured) {
            $operation['security'] = [['bearerAuth' => []]];
        }

        if ($requestBody !== null) {
            $operation['requestBody'] = $requestBody;
        }

        if ($parameters !== []) {
            $operation['parameters'] = $parameters;
        }

        return $operation;
    }

    private static function requestBody(string $schemaRef): array
    {
        return [
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => ['$ref' => $schemaRef],
                ],
            ],
        ];
    }

    private static function multipartBody(string $schemaRef): array
    {
        return [
            'required' => true,
            'content' => [
                'multipart/form-data' => [
                    'schema' => ['$ref' => $schemaRef],
                ],
            ],
        ];
    }

    private static function success(string $message, ?array $data, int $statusCode = 200): array
    {
        return [
            'statusCode' => $statusCode,
            'response' => [
                'description' => $message,
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/ApiSuccessResponse'],
                        'example' => [
                            'success' => true,
                            'message' => $message,
                            'data' => $data,
                            'timestamp' => '2026-05-22T00:00:00+00:00',
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function error(string $message): array
    {
        return [
            'description' => $message,
            'content' => [
                'application/json' => [
                    'schema' => ['$ref' => '#/components/schemas/ApiErrorResponse'],
                    'example' => [
                        'success' => false,
                        'message' => $message,
                        'errors' => null,
                        'data' => null,
                        'timestamp' => '2026-05-22T00:00:00+00:00',
                    ],
                ],
            ],
        ];
    }

    private static function parameter(string $name, string $in, string $type, string $description, bool $required = true): array
    {
        return [
            'name' => $name,
            'in' => $in,
            'required' => $required,
            'description' => $description,
            'schema' => [
                'type' => $type,
            ],
        ];
    }

    private static function userSimpleExample(): array
    {
        return [
            'id' => 1,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'avatar' => 'avatars/jane.jpg',
            'bio' => 'Product designer and frontend developer.',
            'email_verified_at' => '2026-05-22T00:00:00+00:00',
            'role' => [
                'id' => 1,
                'name' => 'candidate',
                'description' => 'Default candidate role',
                'created_at' => '2026-05-22T00:00:00+00:00',
                'updated_at' => '2026-05-22T00:00:00+00:00',
            ],
            'created_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function userProfileExample(): array
    {
        return [
            'id' => 1,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'avatar' => 'avatars/jane.jpg',
            'bio' => 'Product designer and frontend developer.',
            'phone_number' => '+1 555 0100',
            'address' => '100 Main Street',
            'city' => 'Boston',
            'state' => 'MA',
            'country' => 'USA',
            'gender' => 'female',
            'marital_status' => 'single',
            'religion' => 'None',
            'email_verified_at' => '2026-05-22T00:00:00+00:00',
            'role' => [
                'id' => 1,
                'name' => 'candidate',
                'description' => 'Default candidate role',
                'created_at' => '2026-05-22T00:00:00+00:00',
                'updated_at' => '2026-05-22T00:00:00+00:00',
            ],
            'skills' => [self::skillExample()],
            'experiences' => [],
            'projects' => [self::projectExample()],
            'resumes' => [self::resumeExample()],
            'social_profiles' => [self::socialProfileExample()],
            'created_at' => '2026-05-22T00:00:00+00:00',
            'updated_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function skillExample(): array
    {
        return [
            'id' => 1,
            'name' => 'Laravel',
            'proficiency_level' => 'advanced',
            'created_at' => '2026-05-22T00:00:00+00:00',
            'updated_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function projectExample(): array
    {
        return [
            'id' => 1,
            'title' => 'HireFlow API',
            'description' => 'A recruiting platform API',
            'url' => 'https://example.com',
            'technologies' => 'Laravel, PostgreSQL',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-01',
            'created_at' => '2026-05-22T00:00:00+00:00',
            'updated_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function resumeExample(): array
    {
        return [
            'id' => 1,
            'title' => 'Backend CV',
            'file_name' => 'backend-cv.pdf',
            'file_path' => 'resumes/backend-cv.pdf',
            'file_url' => 'https://example.com/storage/resumes/backend-cv.pdf',
            'is_primary' => true,
            'created_at' => '2026-05-22T00:00:00+00:00',
            'updated_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function socialProfileExample(): array
    {
        return [
            'id' => 1,
            'social_profile_type' => 1,
            'profile_url' => 'https://linkedin.com/in/janedoe',
            'created_at' => '2026-05-22T00:00:00+00:00',
            'updated_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function companyExample(): array
    {
        return [
            'id' => 1,
            'slug' => 'acme-ltd',
            'name' => 'Acme Ltd',
            'description' => 'A sample company',
            'logo' => 'logos/acme.png',
            'website' => 'https://acme.test',
            'industry' => 'Technology',
            'company_size' => '51-200',
            'founded_year' => 2020,
            'location' => 'London, UK',
            'email' => 'hello@acme.test',
            'phone' => '123456789',
            'created_by' => 1,
            'is_verified' => true,
            'owner' => self::companyOwnerExample(),
            'people' => [self::companyPersonExample()],
            'created_at' => '2026-05-22T00:00:00+00:00',
            'updated_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function companyPersonExample(): array
    {
        return [
            'id' => 1,
            'company_role' => 'recruiter',
            'position' => 'Developer',
            'information' => 'Works on backend features',
            'start_date' => '2026-01-01',
            'end_date' => null,
            'is_current_position' => true,
            'user' => self::userSimpleExample(),
            'is_company_owner' => false,
            'created_at' => '2026-05-22T00:00:00+00:00',
            'updated_at' => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function companySocialProfileExample(): array
    {
        return [
            'id'                  => 1,
            'social_profile_type' => 1,
            'profile_url'         => 'https://linkedin.com/company/acme',
            'created_at'          => '2026-05-22T00:00:00+00:00',
            'updated_at'          => '2026-05-22T00:00:00+00:00',
        ];
    }

    private static function companyOwnerExample(): array
    {
        return self::userSimpleExample() + [
            'company_role' => 'owner',
        ];
    }

    private static function postExample(): array
    {
        return [
            'id'           => 1,
            'user_id'      => 1,
            'company_id'   => 1,
            'title'        => 'Weekly Hiring Update',
            'content'      => '<p>We are <strong>hiring</strong> engineers.</p>',
            'category'     => 'update',
            'status'       => 'published',
            'post_date'    => '2026-06-10',
            'post_time'    => '14:30:00',
            'scheduled_at' => '2026-06-10T14:30:00',
            'author'       => self::userSimpleExample(),
            'company'      => [
                'id'   => 1,
                'name' => 'Acme Jobs',
                'slug' => 'acme-jobs',
            ],
            'tags'        => [self::tagExample()],
            'attachments' => [self::postAttachmentExample()],
            'created_at'  => '2026-06-02T00:00:00+00:00',
            'updated_at'  => '2026-06-02T00:00:00+00:00',
        ];
    }

    private static function tagExample(): array
    {
        return [
            'id' => 1,
            'name' => 'hiring',
            'slug' => 'hiring',
            'created_at' => '2026-06-02T00:00:00+00:00',
            'updated_at' => '2026-06-02T00:00:00+00:00',
        ];
    }

    private static function postAttachmentExample(): array
    {
        return [
            'id' => 1,
            'file_name' => 'brief.pdf',
            'file_path' => 'posts/1/attachments/brief.pdf',
            'file_url' => 'https://example.com/storage/posts/1/attachments/brief.pdf',
            'mime_type' => 'application/pdf',
            'size' => 122880,
            'created_at' => '2026-06-02T00:00:00+00:00',
            'updated_at' => '2026-06-02T00:00:00+00:00',
        ];
    }
}