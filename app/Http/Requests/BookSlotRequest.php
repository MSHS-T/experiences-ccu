<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Slot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $slot = request()->route('slot');
        return $slot->bookings_count < $slot->manipulation->max_booking_per_slot;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $slot = request()->route('slot');
        $otherBookingsEmails = $slot->manipulation->bookings
            ->map(fn(Booking $booking) => $booking->email)
            ->filter(fn(?string $email) => filled($email))
            ->toArray();

        $requirementsRules = collect($slot->manipulation->requirements)
            ->mapWithKeys(fn($r, $k) => ['requirements-' . $k => 'accepted'])
            ->all();
        return [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => [
                'required',
                'email',
                Rule::notIn($otherBookingsEmails),
                function ($attribute, $value, $fail) {
                    $history = BookingHistory::where('hashed_email', md5($value))->first();
                    if ($history !== null && $history->blocked) {
                        $fail('L\'email saisi a été bloqué par l\'administrateur de la plateforme.');
                    }
                }
            ],
            'commitment'     => 'accepted',
            ...$requirementsRules
        ];
    }

    public function messages(): array
    {
        $slot = request()->route('slot');
        $requirementsMessages = collect($slot->manipulation->requirements)
            ->mapWithKeys(fn($r, $k) => ['requirements-' . $k => 'Vous devez confirmer que vous faites partie de tous les critères d\'inclusion.'])
            ->all();
        return [
            'first_name.required'     => 'Le prénom est requis.',
            'last_name.required'      => 'Le nom est requis.',
            'email.required'          => 'L\'email est requis.',
            'email.email'             => 'L\'email n\'est pas valide.',
            'email.not_in'            => 'L\'email saisi est déjà inscrit pour cette manipulation.',
            'commitment.accepted'     => 'Vous devez attester que les informations fournies sont vraies et vous engager à honorer le rendez-vous.',
            ...$requirementsMessages
        ];
    }
}
