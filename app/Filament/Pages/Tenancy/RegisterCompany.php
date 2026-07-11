<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Tambah perusahaan';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama perusahaan')
                    ->required()
                    ->maxLength(255),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        $company = Company::query()->create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(6)),
            'is_active' => true,
        ]);

        $company->users()->attach(auth()->user());

        session(['selected_company_id' => $company->id]);

        return $company;
    }
}
