<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('student')?->id;

        return ['nis' => ['required', 'max:30', Rule::unique('students')->ignore($id)], 'nisn' => ['nullable', 'max:30', Rule::unique('students')->ignore($id)], 'name' => 'required|max:255', 'gender' => 'required|in:L,P', 'class_id' => 'required|exists:school_classes,id', 'birth_place' => 'nullable|max:100', 'birth_date' => 'nullable|date', 'address' => 'nullable|string', 'parent_name' => 'nullable|max:255', 'parent_phone' => 'nullable|max:30', 'photo' => 'nullable|image|max:2048', 'is_active' => 'nullable|boolean'];
    }
}
