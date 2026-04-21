<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return __('messages.edit_user', ['name' => $this->record->name]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Only update password if provided and not empty
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }
        
        // Handle two_factor_confirmed_at - convert boolean toggle to datetime or null
        if (isset($data['two_factor_confirmed_at'])) {
            if ($data['two_factor_confirmed_at'] === true) {
                $data['two_factor_confirmed_at'] = now();
            } elseif ($data['two_factor_confirmed_at'] === false || $data['two_factor_confirmed_at'] === 0) {
                $data['two_factor_confirmed_at'] = null;
            }
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label(__('messages.delete'))
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalHeading(__('messages.delete_user_confirmation_title'))
                ->modalDescription(__('messages.delete_user_confirmation_description'))
                ->modalSubmitActionLabel(__('messages.delete'))
                ->modalCancelActionLabel(__('messages.cancel')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

