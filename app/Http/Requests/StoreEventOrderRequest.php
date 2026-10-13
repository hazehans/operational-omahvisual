<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Customer
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required_without:customer_id', 'nullable', 'string', 'max:255'],
            'customer_phone' => ['required_without:customer_id', 'nullable', 'string', 'max:50'],
            'customer_organization' => ['nullable', 'string', 'max:255'],

            // Event Details
            'event_name' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'in:indoor,outdoor,semi-outdoor'],
            'venue_name' => ['required', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string'],
            'google_maps_url' => ['nullable', 'string', 'max:500'],
            'loading_date' => ['required', 'date'],
            'loading_time' => ['required', 'string', 'max:50'],
            'ready_time' => ['nullable', 'string', 'max:50'],
            'event_start_date' => ['required', 'date'],
            'event_end_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'string', 'max:50'],
            'electricity_source' => ['nullable', 'string', 'max:100'],
            'dress_code' => ['nullable', 'string', 'max:100'],
            'stage_width_m' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'stage_depth_m' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'stage_height_m' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'stage_layout_json' => ['nullable', 'string'],
            'stage_layout_image' => ['nullable', 'string'],
            'stage_layout_front_image' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            // Screens
            'screens' => ['required', 'array', 'min:1'],
            'screens.*.screen_name' => ['required', 'string', 'max:100'],
            'screens.*.brand_series' => ['required', 'string', 'in:SAGA,LITE,LAMPRO,MAVIC'],
            'screens.*.pixel_pitch' => ['required', 'string', 'max:50'],
            'screens.*.width_m' => ['required', 'numeric', 'min:0.5', 'max:100'],
            'screens.*.height_m' => ['required', 'numeric', 'min:0.5', 'max:50'],
            'screens.*.rigging_method' => ['required', 'string', 'in:ground_stake,hanging,stage_frame'],
            'screens.*.stage_specification' => ['nullable', 'string', 'max:255'],

            // Crew & Logistics
            'crew_pic_event_name' => ['nullable', 'string', 'max:255'],
            'crew_1_name' => ['nullable', 'string', 'max:255'],
            'crew_2_name' => ['nullable', 'string', 'max:255'],
            'crew_3_name' => ['nullable', 'string', 'max:255'],
            'crew_4_name' => ['nullable', 'string', 'max:255'],
            'crew_5_name' => ['nullable', 'string', 'max:255'],
            'crew_6_name' => ['nullable', 'string', 'max:255'],
            'crew_7_name' => ['nullable', 'string', 'max:255'],
            'vehicle_name' => ['nullable', 'string', 'max:100'],

            // Financials
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'down_payment_method' => ['nullable', 'string', 'max:100'],

            // Multimedia Add-ons
            'tv_43_qty' => ['nullable', 'integer', 'min:0'],
            'tv_50_qty' => ['nullable', 'integer', 'min:0'],
            'tv_55_qty' => ['nullable', 'integer', 'min:0'],
            'tv_65_qty' => ['nullable', 'integer', 'min:0'],
            'jimmy_jib' => ['nullable', 'string', 'max:100'],
        ];
    }
}
