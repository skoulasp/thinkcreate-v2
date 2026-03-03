<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWebsiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'homepage_source' => ['required', Rule::in(['default', 'blog', 'page'])],
            'posts_per_page' => ['required', 'integer', 'min:1', 'max:50'],
            'homepage_page_id' => [
                Rule::requiredIf(fn () => $this->input('homepage_source') === 'page'),
                'nullable',
                'integer',
                Rule::exists('pages', 'id')->where(
                    fn ($query) => $query->where('status', 'published')->where('published_at', '<=', now())
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'homepage_page_id.required' => 'Select a published page when homepage source is set to Page.',
            'posts_per_page.integer' => 'Posts per page must be a whole number.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('homepage_source') !== 'page') {
            $this->merge([
                'homepage_page_id' => null,
            ]);
        }
    }
}
