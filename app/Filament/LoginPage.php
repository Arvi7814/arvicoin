<?php

namespace App\Filament;

use App\Helpers\PhoneNumberHelper;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/**
 * @property ComponentContainer $form
 */
class LoginPage extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public $phone_number = '';

    public $password = '';

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'phone_number' => __('filament::login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt([
            'phone_number' => $data['phone_number'],
            'password' => $data['password'],
        ])) {
            throw ValidationException::withMessages([
                'phone_number' => __('filament::login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('phone_number')
                ->label(__('filament::login.fields.phone_number.label'))
                ->tel()
                ->telRegex(PhoneNumberHelper::regex())
                ->mask(
                    fn (TextInput\Mask $mask) => PhoneNumberHelper::mask($mask)
                )
                ->required()
                ->autocomplete(),
            TextInput::make('password')
                ->label(__('filament::login.fields.password.label'))
                ->password()
                ->required(),
        ];
    }

    public function render(): View
    {
        return view('filament::login')
            ->layout('filament::components.layouts.card', [
                'title' => __('filament::login.title'),
            ]);
    }
}
