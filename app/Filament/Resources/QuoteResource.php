<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Cotizaciones';

    protected static ?string $modelLabel = 'Cotización';

    protected static ?string $pluralModelLabel = 'Cotizaciones';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return session('view_mode', 'wts') === 'wts';
    }

    public static function form(Form $form): Form
    {
        $isWtsInternal = session('view_mode', Auth::user()?->user_type ?? 'wts_internal') === 'wts';

        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->description('Datos principales de la cotización')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('N° de Cotización')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'RFQ-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT))
                            ->prefixIcon('heroicon-m-hashtag')
                            ->columnSpan(1),
                        Forms\Components\Select::make('supplier_id')
                            ->label('Proveedor')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-m-building-office')
                            ->columnSpan(1),
                        Forms\Components\Select::make('quote_type_id')
                            ->label('Tipo de Cotización')
                            ->relationship('quoteType', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('created_date')
                            ->label('Fecha de Creación')
                            ->required()
                            ->default(now())
                            ->prefixIcon('heroicon-m-calendar')
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('delivery_date')
                            ->label('Fecha Límite de Entrega')
                            ->helperText('Fecha límite para recibir la cotización del proveedor')
                            ->required()
                            ->prefixIcon('heroicon-m-clock')
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'pending' => 'Pendiente',
                                'in_production' => 'En Producción',
                                'completed' => 'Completado',
                                'cancelled' => 'Cancelado',
                            ])
                            ->default('draft')
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Información del Buyer')
                    ->description('Datos del comprador y temporada')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Select::make('buyer')
                            ->label('Buyer / Brand')
                            ->options([
                                'Nike' => 'Nike',
                                'Adidas' => 'Adidas',
                                'Puma' => 'Puma',
                                'Under Armour' => 'Under Armour',
                                'Zara' => 'Zara',
                                'H&M' => 'H&M',
                                'Mango' => 'Mango',
                                'Uniqlo' => 'Uniqlo',
                                'GAP' => 'GAP',
                                'Old Navy' => 'Old Navy',
                                'Banana Republic' => 'Banana Republic',
                                'Target' => 'Target',
                                'Walmart' => 'Walmart',
                                'Levi\'s' => 'Levi\'s',
                                'Calvin Klein' => 'Calvin Klein',
                                'Tommy Hilfiger' => 'Tommy Hilfiger',
                                'Ralph Lauren' => 'Ralph Lauren',
                                'Forever 21' => 'Forever 21',
                                'American Eagle' => 'American Eagle',
                                'Abercrombie & Fitch' => 'Abercrombie & Fitch',
                                'Hollister' => 'Hollister',
                                'Victoria\'s Secret' => 'Victoria\'s Secret',
                                'Columbia' => 'Columbia',
                                'The North Face' => 'The North Face',
                                'Patagonia' => 'Patagonia',
                                'Lululemon' => 'Lululemon',
                                'Primark' => 'Primark',
                                'C&A' => 'C&A',
                                'Decathlon' => 'Decathlon',
                            ])
                            ->searchable()
                            ->prefixIcon('heroicon-m-building-office')
                            ->columnSpan(1),
                        Forms\Components\Select::make('buyer_department')
                            ->label('Departamento')
                            ->options([
                                'Men\'s' => 'Men\'s',
                                'Women\'s' => 'Women\'s',
                                'Kids' => 'Kids',
                                'Boys' => 'Boys',
                                'Girls' => 'Girls',
                                'Toddler' => 'Toddler',
                                'Infant' => 'Infant',
                                'Baby' => 'Baby',
                                'Unisex' => 'Unisex',
                                'Teen' => 'Teen',
                                'Young Adult' => 'Young Adult',
                                'Maternity' => 'Maternity',
                                'Plus Size' => 'Plus Size',
                            ])
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\Select::make('season')
                            ->label('Temporada')
                            ->options([
                                'SS24' => 'Spring/Summer 2024',
                                'FW24' => 'Fall/Winter 2024',
                                'SS25' => 'Spring/Summer 2025',
                                'FW25' => 'Fall/Winter 2025',
                                'SS26' => 'Spring/Summer 2026',
                                'FW26' => 'Fall/Winter 2026',
                                'SS27' => 'Spring/Summer 2027',
                                'FW27' => 'Fall/Winter 2027',
                                'Holiday24' => 'Holiday 2024',
                                'Holiday25' => 'Holiday 2025',
                                'Holiday26' => 'Holiday 2026',
                                'Resort25' => 'Resort 2025',
                                'Resort26' => 'Resort 2026',
                                'Pre-Fall25' => 'Pre-Fall 2025',
                                'Pre-Fall26' => 'Pre-Fall 2026',
                            ])
                            ->searchable()
                            ->prefixIcon('heroicon-m-calendar')
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Tech Packs')
                    ->description('Selecciona los Tech Packs para esta cotización')
                    ->icon('heroicon-o-document-duplicate')
                    ->schema([
                        Forms\Components\Select::make('techpacks')
                            ->label('Tech Packs')
                            ->multiple()
                            ->relationship(
                                'techpacks',
                                'name',
                                function (Builder $query, Get $get, ?Quote $record) {
                                    $query->where('status', 'approved')
                                        ->whereDoesntHave('quotes', function (Builder $related) use ($record) {
                                            if ($record) {
                                                $related->where('quotes.id', '!=', $record->id);
                                            }
                                        });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->style_code} - {$record->name}")
                            ->searchable()
                            ->searchPrompt('Busca por código de estilo o nombre...')
                            ->preload()
                            ->helperText('Solo se muestran Tech Packs aprobados.')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('has_artwork')
                            ->label('Incluye Diseño de Arte')
                            ->helperText('Indica si los Tech Packs incluyen diseño de arte (serigrafía, bordados, etc.)')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Precios y Costos')
                    ->description('Información de precios y márgenes')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad Total')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffixIcon('heroicon-m-cube')
                            ->helperText('Cantidad total de prendas'),
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Precio Fábrica (FOB)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->minValue(0)
                            ->step('0.01')
                            ->helperText('Precio por unidad desde fábrica'),
                        Forms\Components\TextInput::make('profit_margin')
                            ->label('Margen de Ganancia')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->step('0.1')
                            ->visible($isWtsInternal)
                            ->helperText('Solo visible para WTS Internal'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Especificaciones de Producción')
                    ->description('Requisitos de producción y cantidades mínimas')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\TextInput::make('lead_time_days')
                            ->label('Lead Time de Producción')
                            ->numeric()
                            ->suffix('días')
                            ->minValue(1)
                            ->helperText('Tiempo de producción desde la orden hasta el envío')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('minimums_by_style')
                            ->label('Mínimo por Estilo')
                            ->numeric()
                            ->suffix('unidades')
                            ->helperText('Cantidad mínima por estilo/diseño')
                            ->columnSpan(1),
                        Forms\Components\CheckboxList::make('size_range')
                            ->label('Tallas Disponibles')
                            ->options([
                                'XXS' => 'XXS',
                                'XS' => 'XS',
                                'S' => 'S',
                                'M' => 'M',
                                'L' => 'L',
                                'XL' => 'XL',
                                'XXL' => 'XXL',
                                '3XL' => '3XL',
                                '4XL' => '4XL',
                                '5XL' => '5XL',
                            ])
                            ->columns(5)
                            ->gridDirection('row')
                            ->helperText('Selecciona todas las tallas disponibles')
                            ->columnSpan(2),
                        Forms\Components\KeyValue::make('minimums_by_color')
                            ->label('Mínimos por Color')
                            ->keyLabel('Color')
                            ->valueLabel('Cantidad mínima')
                            ->helperText('Especifica la cantidad mínima requerida por cada color')
                            ->addActionLabel('Agregar color')
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('minimums_by_fabric')
                            ->label('Mínimos por Tela')
                            ->keyLabel('Tipo de Tela')
                            ->valueLabel('Mínimo (ej: 1 tonelada = 5000 prendas)')
                            ->helperText('Mínimos de tela expresados en rollo/tonelada')
                            ->addActionLabel('Agregar tela')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Tabs::make('Especificaciones Detalladas')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información de Tela')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Forms\Components\Repeater::make('fabric_information')
                                    ->label('Telas')
                                    ->schema([
                                        Forms\Components\TextInput::make('fabric_name')
                                            ->label('Nombre/Código de Tela')
                                            ->required(),
                                        Forms\Components\Select::make('construction')
                                            ->label('Construcción')
                                            ->options([
                                                'Single Jersey' => 'Single Jersey',
                                                'Rib 1x1' => 'Rib 1x1',
                                                'Rib 2x2' => 'Rib 2x2',
                                                'Fleece' => 'Fleece',
                                                'French Terry' => 'French Terry',
                                                'Pique' => 'Pique',
                                                'Interlock' => 'Interlock',
                                                'Double Jersey' => 'Double Jersey',
                                                'Jersey Spandex' => 'Jersey Spandex',
                                                'Slub Jersey' => 'Slub Jersey',
                                                'Waffle' => 'Waffle',
                                                'Pointelle' => 'Pointelle',
                                                'Thermal' => 'Thermal',
                                                'Ottoman' => 'Ottoman',
                                                'Twill' => 'Twill',
                                                'Poplin' => 'Poplin',
                                                'Canvas' => 'Canvas',
                                                'Denim' => 'Denim',
                                                'Chambray' => 'Chambray',
                                                'Corduroy' => 'Corduroy',
                                                'Jacquard' => 'Jacquard',
                                                'Mesh' => 'Mesh',
                                            ])
                                            ->searchable(),
                                        Forms\Components\TextInput::make('yarn_count')
                                            ->label('Título (Yarn Count)')
                                            ->placeholder('ej: 30/1, 20/1 + 20 den')
                                            ->helperText('Especifica el título del hilo'),
                                        Forms\Components\TextInput::make('composition')
                                            ->label('Contenido / Composición')
                                            ->placeholder('ej: 100% Cotton, 95% Cotton 5% Spandex')
                                            ->helperText('Composición de la tela'),
                                        Forms\Components\Select::make('dyeing_type')
                                            ->label('Tipo de Teñido')
                                            ->options([
                                                'Piece Dye' => 'Piece Dye (Teñido en Pieza)',
                                                'Yarn Dye' => 'Yarn Dye (Teñido en Hilo)',
                                                'Fiber Dye' => 'Fiber Dye (Teñido en Fibra)',
                                                'Garment Dye' => 'Garment Dye (Teñido en Prenda)',
                                                'Space Dye' => 'Space Dye',
                                                'Reactive Dye' => 'Reactive Dye',
                                                'Vat Dye' => 'Vat Dye',
                                                'Acid Dye' => 'Acid Dye',
                                                'Disperse Dye' => 'Disperse Dye',
                                                'Pigment Dye' => 'Pigment Dye',
                                                'Natural Dye' => 'Natural Dye',
                                                'Greige' => 'Greige (Sin Teñir)',
                                                'Bleached' => 'Bleached (Blanqueado)',
                                            ])
                                            ->searchable(),
                                        Forms\Components\TextInput::make('weight')
                                            ->label('Peso')
                                            ->placeholder('ej: 180 GSM, 200 GSM')
                                            ->helperText('Peso de la tela'),
                                        Forms\Components\TextInput::make('special_finishes')
                                            ->label('Acabados Especiales')
                                            ->placeholder('ej: Enzyme Wash, Peach Finish, Silicon Softener')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Agregar tela')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['fabric_name'] ?? 'Tela')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Tallas')
                            ->icon('heroicon-o-arrows-pointing-out')
                            ->schema([
                                Forms\Components\CheckboxList::make('size_range')
                                    ->label('Tallas Disponibles')
                                    ->options([
                                        'XXS' => 'XXS',
                                        'XS' => 'XS',
                                        'S' => 'S',
                                        'M' => 'M',
                                        'L' => 'L',
                                        'XL' => 'XL',
                                        'XXL' => 'XXL',
                                        '3XL' => '3XL',
                                        '4XL' => '4XL',
                                        '5XL' => '5XL',
                                    ])
                                    ->columns(5)
                                    ->gridDirection('row')
                                    ->helperText('Selecciona todas las tallas que estarán disponibles para este estilo')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Trims')
                            ->icon('heroicon-o-puzzle-piece')
                            ->schema([
                                Forms\Components\Repeater::make('trims_list')
                                    ->label('Trims / Avíos')
                                    ->schema([
                                        Forms\Components\TextInput::make('trim_name')
                                            ->label('Nombre del Trim')
                                            ->required()
                                            ->placeholder('ej: Zipper, Button, Label, Thread'),
                                        Forms\Components\TextInput::make('trim_code')
                                            ->label('Código/Referencia')
                                            ->placeholder('Código del proveedor o WFX'),
                                        Forms\Components\Textarea::make('trim_specs')
                                            ->label('Especificaciones')
                                            ->rows(3)
                                            ->placeholder('Material, color, tamaño, cantidad por prenda, etc.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Agregar trim')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['trim_name'] ?? 'Trim')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Artes')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\Repeater::make('artwork_details')
                                    ->label('Artes Incluidos en la Cotización')
                                    ->schema([
                                        Forms\Components\TextInput::make('artwork_name')
                                            ->label('Nombre del Arte')
                                            ->required()
                                            ->placeholder('ej: Logo Frontal, Gráfico Espalda'),
                                        Forms\Components\Select::make('artwork_type')
                                            ->label('Tipo de Arte')
                                            ->options([
                                                'Screen Print' => 'Serigrafía (Screen Print)',
                                                'Embroidery' => 'Bordado (Embroidery)',
                                                'Heat Transfer' => 'Transfer Térmico',
                                                'Digital Print' => 'Impresión Digital',
                                                'Sublimation' => 'Sublimación',
                                                'Patch' => 'Parche (Patch)',
                                            ])
                                            ->searchable(),
                                        Forms\Components\TextInput::make('artwork_location')
                                            ->label('Ubicación')
                                            ->placeholder('ej: Frente Centro, Espalda Alta, Manga Izquierda'),
                                        Forms\Components\Textarea::make('artwork_notes')
                                            ->label('Comentarios / Notas')
                                            ->rows(3)
                                            ->placeholder('Colores, tamaño, observaciones especiales...')
                                            ->helperText('Agrega cualquier comentario relevante sobre este arte')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Agregar arte')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['artwork_name'] ?? 'Arte')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Plan de Acción (TNA)')
                    ->description('Time & Action Calendar - Cronograma de producción')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('import_tna')
                                ->label('Importar TNA desde CSV')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('primary')
                                ->action(function () {
                                    // Mock: Acción para importar TNA desde CSV
                                    // En producción: procesar archivo CSV y llenar la tabla
                                }),
                        ]),
                        Forms\Components\Repeater::make('tna_milestones')
                            ->label('Hitos del TNA')
                            ->schema([
                                Forms\Components\TextInput::make('task')
                                    ->label('Tarea / Hito')
                                    ->required()
                                    ->placeholder('ej: Fabric Order, Lab Dip Approval, PP Sample')
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('responsible')
                                    ->label('Responsable')
                                    ->placeholder('ej: Supplier, WTS, Client')
                                    ->columnSpan(2),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Fecha Límite')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'pending' => 'Pendiente',
                                        'in_progress' => 'En Progreso',
                                        'completed' => 'Completado',
                                        'delayed' => 'Retrasado',
                                    ])
                                    ->default('pending')
                                    ->columnSpan(2),
                                Forms\Components\DatePicker::make('completed_date')
                                    ->label('Fecha Completado')
                                    ->columnSpan(2),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notas')
                                    ->rows(2)
                                    ->placeholder('Comentarios, observaciones...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(11)
                            ->addActionLabel('Agregar hito')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['task'] ?? 'Nuevo hito')
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->helperText('Agrega los hitos del cronograma de producción. También puedes importar desde un CSV.'),
                        Forms\Components\Placeholder::make('tna_note')
                            ->label('Nota')
                            ->content('Este TNA puede aplicarse a varios estilos/techpacks de esta cotización. Los hitos se guardarán y podrán ser consultados desde la vista de detalle.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isWtsInternal = session('view_mode', Auth::user()?->user_type ?? 'wts_internal') === 'wts';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('N° RFQ')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-m-hashtag')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-office')
                    ->visible($isWtsInternal),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-user')
                    ->visible(!$isWtsInternal),
                Tables\Columns\TextColumn::make('buyer')
                    ->label('Comprador')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-building-office')
                    ->visible($isWtsInternal)
                    ->getStateUsing(function ($record) {
                        if ($record->buyer) return $record->buyer;
                        $buyers = ["Fashion Retail Corp", "Original Favorites", "Global Apparel Inc", "Textile Sourcing SAC", "América Textil", "Confecciones del Sur", "Grupo Textil Norte", "Industrias Modernas"];
                        return $buyers[array_rand($buyers)];
                    }),
                Tables\Columns\TextColumn::make('buyer_department')
                    ->label('Departamento')
                    ->badge()
                    ->color('gray')
                    ->visible($isWtsInternal)
                    ->getStateUsing(function ($record) {
                        if ($record->buyer_department) return $record->buyer_department;
                        $departments = ["Caballeros", "Damas", "Niños", "Deportivo", "Casual", "Formal", "Infantil", "Uniformes"];
                        return $departments[array_rand($departments)];
                    }),
                Tables\Columns\TextColumn::make('season')
                    ->label('Temporada')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-calendar')
                    ->getStateUsing(function ($record) {
                        if ($record->season) return $record->season;
                        $seasons = ["Primavera 2025", "Verano 2025", "Otoño 2025", "Invierno 2025", "Primavera 2026", "Verano 2026"];
                        return $seasons[array_rand($seasons)];
                    }),
                Tables\Columns\TextColumn::make('created_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Entrega')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->delivery_date && $record->delivery_date->isPast() ? 'danger' : 'success')
                    ->icon('heroicon-m-clock')
                    ->tooltip(fn ($record) => $record->delivery_date && $record->delivery_date->isPast()
                        ? '¡Vencida!'
                        : 'Días restantes: ' . now()->diffInDays($record->delivery_date, false)),
                Tables\Columns\TextColumn::make('techpacks_count')
                    ->label('Tech Packs')
                    ->counts('techpacks')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-document-duplicate')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio FOB')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('profit_margin')
                    ->label('Margen')
                    ->suffix('%')
                    ->visible($isWtsInternal)
                    ->color('success')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'info' => 'in_production',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-m-pencil' => 'draft',
                        'heroicon-m-clock' => 'pending',
                        'heroicon-m-cog-6-tooth' => 'in_production',
                        'heroicon-m-check-circle' => 'completed',
                        'heroicon-m-x-circle' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'in_production' => 'En Producción',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'in_production' => 'En Producción',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('buyer')
                    ->label('Buyer')
                    ->options([
                        'Nike' => 'Nike',
                        'Adidas' => 'Adidas',
                        'Zara' => 'Zara',
                        'H&M' => 'H&M',
                        'GAP' => 'GAP',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('season')
                    ->label('Temporada')
                    ->options([
                        'SS25' => 'SS25',
                        'FW25' => 'FW25',
                        'SS26' => 'SS26',
                    ]),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions(
                $isWtsInternal ? [
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ] : []
            );
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $isWtsInternal = session('view_mode', Auth::user()?->user_type ?? 'wts_internal') === 'wts';

        return $infolist
            ->schema([
                // Header con información clave
                Section::make()
                    ->schema([
                        Grid::make(7)
                            ->schema([
                                TextEntry::make('code')
                                    ->label('N° de cotización')
                                    ->badge()
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->color('primary'),
                                TextEntry::make('client.name')
                                    ->label('Cliente')
                                    ->icon('heroicon-m-user')
                                    ->badge()
                                    ->color('info')
                                    ->visible(!$isWtsInternal),
                                TextEntry::make('buyer')
                                    ->label('Buyer')
                                    ->icon('heroicon-m-user')
                                    ->badge()
                                    ->color('info')
                                    ->visible($isWtsInternal)
                                    ->getStateUsing(function ($record) {
                                        if ($record->buyer) return $record->buyer;
                                        $buyers = ["Fashion Retail Corp", "Original Favorites", "Global Apparel Inc", "Textile Sourcing SAC"];
                                        return $buyers[array_rand($buyers)];
                                    }),
                                TextEntry::make('buyer_department')
                                    ->label('Departamento')
                                    ->icon('heroicon-m-building-office')
                                    ->badge()
                                    ->color('gray')
                                    ->visible($isWtsInternal)
                                    ->getStateUsing(function ($record) {
                                        if ($record->buyer_department) return $record->buyer_department;
                                        $departments = ["Caballeros", "Damas", "Niños", "Deportivo"];
                                        return $departments[array_rand($departments)];
                                    }),
                                TextEntry::make('season')
                                    ->label('Temporada')
                                    ->icon('heroicon-m-calendar-days')
                                    ->badge()
                                    ->color('warning')
                                    ->getStateUsing(function ($record) {
                                        if ($record->season) return $record->season;
                                        $seasons = ["Primavera 2025", "Verano 2025", "Otoño 2025", "Invierno 2025"];
                                        return $seasons[array_rand($seasons)];
                                    }),
                                TextEntry::make('supplier.name')
                                    ->label('Proveedor')
                                    ->icon('heroicon-m-globe-alt')
                                    ->visible($isWtsInternal),
                                TextEntry::make('created_date')
                                    ->label('Fecha')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'pending' => 'warning',
                                        'in_production' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'draft' => 'Borrador',
                                        'pending' => 'Pendiente',
                                        'in_production' => 'En Producción',
                                        'completed' => 'Completado',
                                        'cancelled' => 'Cancelado',
                                        default => $state,
                                    }),
                            ])
                            ->columns(7),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TechpacksRelationManager::class,
            RelationManagers\SampleOrdersRelationManager::class,
            RelationManagers\PurchaseOrdersRelationManager::class,
            RelationManagers\ProductionMilestonesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}

