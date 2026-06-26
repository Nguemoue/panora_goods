<?php

namespace App\Filament\Admin\Pages;

use App\Settings\PaymentSetting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;

class PaymentSettingPage extends Page implements HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationLabel = "Paramètres";
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Cog;
    public ?array $data = [];
    protected string $view = 'filament.admin.pages.payment-setting-page';

    public function mount(): void
    {

        $this->form->fill(app(PaymentSetting::class)->toArray());
        //dd($this->data);
    }

    public function saveForm(): Action
    {
        return Action::make('save')
            ->color('danger')
            ->action(function () {
                dd($this->data);
            });
    }

    public function form(Schema $schema): Schema
    {
        $paymentSetting = app(PaymentSetting::class);

        return $schema
            ->statePath('data')
            ->columns(2)
            ->schema([
                ...Arr::map($paymentSetting->toArray(), static fn($item, $key) => TextInput::make($key)
                    ->numeric()
                    ->readOnly()
                    ->formatStateUsing(fn($state) => $state * 100)
                    ->suffix("%")
                    ->maxValue(100)),

                Action::make('save')
                    ->disabled()
                    ->action(function ($data) {

                        $setting = app(PaymentSetting::class);
                        foreach ($this->data as $key => $value) {
                            $setting->{$key} = ((float) $value)/100;
                        }
                        $setting->save();
                        Notification::make()
                            ->title("Paramètres mis a jour")
                            ->success()
                            ->send();
                        $this->refresh();
                    })
                    ->submit(null)

            ]);
    }
}
