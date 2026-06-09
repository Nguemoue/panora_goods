<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EditUserProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('photo')
                    ->label(__('users.profile_photo'))
                    ->disk('public')
                    ->columnSpanFull()
                    ->image(),
                TextInput::make('reference_number')
                    ->label(__('panels.reference_number'))
                    ->disabled()
                    ->readOnly(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
