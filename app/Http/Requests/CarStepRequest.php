<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $step = $this->input('step');
        $singleField = $this->input('single_field');
        $minDate = config('app.min_purchase_date', '2000-01-01');
        $rules = [];

        switch ($step) {
            case 1:
                $rules = [
                    'model' => ['required', 'string', 'max:255'],
                    'vehicle_category' => ['nullable', 'string', 'max:255'],
                    'plate_number' => ['nullable', 'string', 'max:255'],
                    'purchase_date' => ['required', 'date', 'after:' . $minDate],
                    'insurance_expiry_date' => ['nullable', 'date', 'after:' . $minDate],
                ];
                break;
            case 2:
                $rules = [
                    'manufacturing_year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
                    'engine_capacity' => ['nullable', 'string', 'max:50'],
                    'number_of_keys' => ['nullable', 'integer', 'min:1', 'max:10'],
                    'place_of_manufacture' => ['nullable', 'string', 'max:255'],
                ];
                break;
            case 3:
                $rules = [
                    'options' => ['nullable', 'array'],
                    'options.*' => ['string', 'max:255'],
                    'all_options' => ['nullable', 'string'],
                ];
                break;
            case 4:
                $rules = [
                    'purchase_price' => ['required', 'numeric', 'min:0'],
                    'expected_sale_price' => ['required', 'numeric', 'min:0'],
                    'status' => ['required', Rule::in(['not_received','paint','upholstery','mechanic','electrical','agency','polish','ready'])],
                    'bulk_deal_id' => ['nullable', 'exists:bulk_deals,id'],
                ];
                break;
            case 5:
                $rules = [
                    'chassis_inspection' => ['nullable', 'string'],
                    'transmission' => ['nullable', 'string', 'max:255'],
                    'motor' => ['nullable', 'string', 'max:255'],
                    'body_notes' => ['nullable', 'string'],
                ];
                break;
            case 6:
                // For AJAX validation, we skip file validation since files can't be sent via AJAX
                // File validation will be handled on final form submission
                $rules = [];
                break;
        }

        // Only validate fields present in the request
        $fieldsToValidate = array_intersect_key($rules, $this->all());
        $rules = array_intersect_key($rules, $fieldsToValidate);

        // For single field validation, only validate that field
        if ($singleField && isset($rules[$singleField])) {
            $rules = [$singleField => $rules[$singleField]];
        }

        return $rules;
    }

    public function messages(): array
    {
        $minDate = config('app.min_purchase_date', '2000-01-01');
        return [
            'model.required' => 'Car model is required.',
            'purchase_date.required' => 'Purchase date is required.',
            'purchase_date.after' => 'Purchase date must be after ' . $minDate . '.',
            'insurance_expiry_date.required' => 'Insurance expiry date is required.',
            'insurance_expiry_date.after' => 'Insurance expiry date must be after ' . $minDate . '.',
            'manufacturing_year.required' => 'Manufacturing year is required.',
            'manufacturing_year.integer' => 'Manufacturing year must be a number.',
            'manufacturing_year.min' => 'Manufacturing year must be at least 1900.',
            'manufacturing_year.max' => 'Manufacturing year cannot be in the future.',
            'number_of_keys.min' => 'Number of keys must be at least 1.',
            'number_of_keys.max' => 'Number of keys cannot exceed 10.',
            'purchase_price.required' => 'Purchase price is required.',
            'purchase_price.numeric' => 'Purchase price must be a number.',
            'purchase_price.min' => 'Purchase price cannot be negative.',
            'expected_sale_price.required' => 'Expected sale price is required.',
            'expected_sale_price.numeric' => 'Expected sale price must be a number.',
            'expected_sale_price.min' => 'Expected sale price cannot be negative.',
            'status.required' => 'Car status is required.',
            'status.in' => 'Please select a valid car status.',
            'car_license.image' => 'License image must be a valid image file.',
            'car_license.mimes' => 'License image must be JPG, JPEG, or PNG.',
            'car_license.max' => 'License image cannot exceed 2MB.',
            'car_images.*.image' => 'Car images must be valid image files.',
            'car_images.*.mimes' => 'Car images must be JPG, JPEG, or PNG.',
            'car_images.*.max' => 'Each car image cannot exceed 2MB.',
        ];
    }
} 