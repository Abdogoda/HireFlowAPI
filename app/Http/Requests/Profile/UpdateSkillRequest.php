<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'proficiency_level' => 'sometimes|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'sometimes|numeric|nullable|min:0',
            'endorsement_count' => 'sometimes|integer|nullable|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Skill name must not exceed 255 characters',
            'proficiency_level.in' => 'Proficiency level must be beginner, intermediate, advanced, or expert',
            'years_of_experience.numeric' => 'Years of experience must be numeric',
            'years_of_experience.min' => 'Years of experience must be 0 or greater',
            'endorsement_count.integer' => 'Endorsement count must be an integer',
            'endorsement_count.min' => 'Endorsement count must be 0 or greater',
        ];
    }
}