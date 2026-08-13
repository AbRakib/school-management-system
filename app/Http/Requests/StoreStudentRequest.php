<?php

namespace App\Http\Requests;

use App\Support\SchoolRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole([SchoolRoles::SUPER_ADMIN, SchoolRoles::ADMIN]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'admission_no' => ['nullable', 'string', 'max:50', Rule::unique('students', 'admission_no')],
            'class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id'],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'guardian_id' => ['nullable', 'integer', 'exists:guardians,id', 'required_if:guardian_mode,existing'],
            'guardian_mode' => ['required', Rule::in(['existing', 'new'])],
            'guardian.name' => ['required_if:guardian_mode,new', 'nullable', 'string', 'max:255'],
            'guardian.phone' => ['required_if:guardian_mode,new', 'nullable', 'string', 'max:30'],
            'guardian.email' => ['nullable', 'email', 'max:255', Rule::unique('guardians', 'email')],
            'guardian.relation' => ['required_if:guardian_mode,new', 'nullable', Rule::in(['father', 'mother', 'guardian'])],
            'guardian.occupation' => ['nullable', 'string', 'max:255'],
            'guardian.address' => ['nullable', 'string', 'max:1000'],
            'student_category_id' => ['nullable', 'integer', 'exists:student_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'religion' => ['nullable', 'string', 'max:100'],
            'present_address' => ['required', 'string', 'max:1000'],
            'permanent_address' => ['nullable', 'string', 'max:1000'],
            'admission_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'graduated', 'transferred'])],
            'create_user' => ['boolean'],
        ];
    }
}
