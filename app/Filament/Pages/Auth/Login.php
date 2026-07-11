<?php

namespace App\Filament\Pages\Auth;

use App\Models\Company;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                Select::make('company_id')
                    ->label('Perusahaan')
                    ->placeholder('Pilih perusahaan')
                    ->options(fn (): array => Company::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                $this->getRememberFormComponent(),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if (! $response || ! Filament::auth()->check()) {
            return $response;
        }

        $company = Filament::auth()->user()
            ->companies()
            ->whereKey($this->data['company_id'])
            ->where('is_active', true)
            ->first();

        if (! $company) {
            Filament::auth()->logout();
            session()->regenerateToken();

            throw ValidationException::withMessages([
                'data.company_id' => 'Akun ini tidak memiliki akses ke perusahaan yang dipilih.',
            ]);
        }

        session(['selected_company_id' => $company->id]);

        return $response;
    }
}
