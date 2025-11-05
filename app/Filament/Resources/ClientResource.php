<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return session('view_mode', 'wts') === 'wts';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Empresa')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('legal_name')
                            ->label('Razón Social')
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('tax_id')
                            ->label('RUC/ID Fiscal')
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('contact')
                            ->label('Nombre de Contacto')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Dirección')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('country_code')
                            ->label('País (Código)')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ubicación Internacional')
                    ->schema([
                        Forms\Components\Select::make('country')
                            ->label('País')
                            ->options([
                                'Argentina' => 'Argentina',
                                'Uruguay' => 'Uruguay',
                                'Chile' => 'Chile',
                                'Brasil' => 'Brasil',
                                'Paraguay' => 'Paraguay',
                            ])
                            ->searchable(),
                        Forms\Components\Select::make('timezone')
                            ->label('Zona Horaria')
                            ->options([
                                'America/Buenos_Aires' => 'Buenos Aires',
                                'America/Montevideo' => 'Montevideo',
                                'America/Santiago' => 'Santiago',
                                'America/Sao_Paulo' => 'São Paulo',
                            ])
                            ->searchable(),
                        Forms\Components\Select::make('currency')
                            ->label('Moneda')
                            ->options([
                                'USD' => 'USD',
                                'ARS' => 'ARS',
                                'EUR' => 'EUR',
                                'BRL' => 'BRL',
                            ])
                            ->default('USD')
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Configuración Comercial')
                    ->schema([
                        Forms\Components\TextInput::make('credit_limit')
                            ->label('Límite de Crédito')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('payment_terms')
                            ->label('Términos de Pago')
                            ->placeholder('30 días')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('logo_url')
                            ->label('Logo')
                            ->image()
                            ->directory('client-logos')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Sincronización ERP')
                    ->schema([
                        Forms\Components\Placeholder::make('erp_sync')
                            ->label('')
                            ->content('Configuración de sincronización con ERP (próximamente)'),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact')
                    ->label('Contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon('heroicon-m-phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('currency')
                    ->label('Moneda')
                    ->colors([
                        'primary' => 'USD',
                        'warning' => 'ARS',
                        'success' => 'EUR',
                        'info' => 'BRL',
                    ]),
                Tables\Columns\TextColumn::make('credit_limit')
                    ->label('Límite')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('currency')
                    ->label('Moneda')
                    ->options([
                        'USD' => 'USD',
                        'ARS' => 'ARS',
                        'EUR' => 'EUR',
                        'BRL' => 'BRL',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClients::route('/'),
        ];
    }
}
