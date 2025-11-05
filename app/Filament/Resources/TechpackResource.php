<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechpackResource\Pages;
use App\Models\Techpack;
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
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class TechpackResource extends Resource
{
    protected static ?string $model = Techpack::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Techpacks';

    protected static ?string $modelLabel = 'Techpack';

    protected static ?string $pluralModelLabel = 'Techpacks';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return session('view_mode', 'wts') === 'wts';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->description('Datos principales del Tech Pack')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('code')
                            ->label('Código Interno')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Código interno del sistema')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Tech Pack')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('garment_type')
                            ->label('Tipo de Prenda')
                            ->options([
                                'T-Shirt' => 'T-Shirt',
                                'Polo' => 'Polo',
                                'Hoodie' => 'Hoodie',
                                'Sweatshirt' => 'Sweatshirt',
                                'Jean' => 'Jean',
                                'Jogger' => 'Jogger',
                                'Dress' => 'Dress',
                                'Jacket' => 'Jacket',
                            ])
                            ->searchable()
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('version')
                            ->label('Versión')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Información WFX')
                    ->description('Datos de sincronización con WFX')
                    ->icon('heroicon-o-cloud')
                    ->schema([
                        Forms\Components\TextInput::make('style_code')
                            ->label('Código de Estilo (WFX)')
                            ->maxLength(255)
                            ->helperText('Este código se genera automáticamente al sincronizar con WFX')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('wfx_id')
                            ->label('ID WFX')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('ID único en el sistema WFX')
                            ->columnSpan(1),
                        Forms\Components\Select::make('buyer')
                            ->label('Comprador / Marca')
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
                                'Primark' => 'Primark',
                                'C&A' => 'C&A',
                                'Decathlon' => 'Decathlon',
                            ])
                            ->searchable()
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
                            ->columnSpan(1),
                        Forms\Components\Placeholder::make('synced_to_wfx_at')
                            ->label('Última sincronización')
                            ->content(fn ($record) => $record?->synced_to_wfx_at
                                ? $record->synced_to_wfx_at->format('d/m/Y H:i')
                                : 'No sincronizado')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Información de Materiales')
                    ->description('Especificaciones de tela principal del techpack')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        Forms\Components\TextInput::make('fabric_article_code')
                            ->label('Código de Artículo')
                            ->placeholder('Ej: J31158')
                            ->helperText('Código WFX o referencia del proveedor')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('fabric_construction')
                            ->label('Construcción')
                            ->placeholder('Ej: JERSEY 18/1, RIB 20/1')
                            ->helperText('Tipo de tejido')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('fabric_yarn_count')
                            ->label('Título')
                            ->placeholder('Ej: 30/1, 18/1, 20/1')
                            ->helperText('Número de título del hilo')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('fabric_content')
                            ->label('Contenido')
                            ->placeholder('Ej: 100% COTTON, 60/40 Cotton/Poly')
                            ->helperText('Composición de la tela')
                            ->columnSpan(1),
                        Forms\Components\Select::make('fabric_dyeing_type')
                            ->label('Tipo de Teñido')
                            ->options([
                                'Piece Dye' => 'Piece Dye (Teñido en pieza)',
                                'Yarn Dye' => 'Yarn Dye (Teñido en hilo)',
                                'Fiber Dye' => 'Fiber Dye (Teñido en fibra)',
                                'Garment Dye' => 'Garment Dye (Teñido en prenda)',
                                'Solution Dye' => 'Solution Dye (Teñido en solución)',
                            ])
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('fabric_weight')
                            ->label('Peso')
                            ->placeholder('Ej: 230 gr/m2 AW, 305 gr/m2')
                            ->helperText('Gramaje de la tela')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('fabric_width')
                            ->label('Ancho (CW)')
                            ->placeholder('Ej: 172, 180')
                            ->helperText('Ancho de la tela en cm')
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('fabric_finishing')
                            ->label('Acabados Especiales')
                            ->placeholder('Ej: Mercerizado, Sanforizado, Antipilling')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Especificaciones de Producción')
                    ->description('Mínimos de producción y detalles de producción')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\TextInput::make('lead_time_days')
                            ->label('Lead Time de Producción (días)')
                            ->numeric()
                            ->placeholder('Ej: 45, 60, 90')
                            ->helperText('Tiempo de producción en días')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('minimums_by_color')
                            ->label('Mínimos por Color')
                            ->numeric()
                            ->placeholder('Ej: 300, 500')
                            ->helperText('Cantidad mínima por color')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('minimums_by_style')
                            ->label('Mínimos por Estilo')
                            ->numeric()
                            ->placeholder('Ej: 1200, 2400')
                            ->helperText('Cantidad mínima por estilo')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('minimums_by_fabric')
                            ->label('Mínimos por Tela')
                            ->numeric()
                            ->placeholder('Ej: 600, 1000')
                            ->helperText('Cantidad mínima por tipo de tela')
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
                        Forms\Components\Repeater::make('trims')
                            ->label('Trims / Avíos (Campos del Excel)')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Photo (Foto)')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('techpack-trims')
                                    ->maxSize(5120)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('description')
                                    ->label('Description (Descripción)')
                                    ->placeholder('Ej: Main Label, Care Label, YKK Zipper')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('code')
                                    ->label('Code (Código)')
                                    ->placeholder('Código de referencia')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('supplier')
                                    ->label('Supplier (Proveedor)')
                                    ->placeholder('Nombre del proveedor')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('origin')
                                    ->label('Origin (Origen)')
                                    ->placeholder('País o lugar de origen')
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('comments')
                                    ->label('Comments (Comentarios de Aprobación)')
                                    ->placeholder('Comentarios de aprobación o especificaciones')
                                    ->rows(2)
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['description'])
                                    ? $state['description']
                                    : 'Trim sin descripción'
                            )
                            ->reorderable()
                            ->cloneable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('artwork_comments')
                            ->label('Comentarios de Artes')
                            ->rows(3)
                            ->placeholder('Comentarios y especificaciones sobre los artes y diseños')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Artes y Diseño')
                    ->description('Información de desarrollo del diseño')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\FileUpload::make('sketch_image')
                            ->label('Sketch (Boceto)')
                            ->image()
                            ->imageEditor()
                            ->directory('techpack-sketches')
                            ->maxSize(5120)
                            ->columnSpan(1),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('front_artwork_image')
                                    ->label('Front Artwork (Arte Frontal)')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('techpack-artworks')
                                    ->maxSize(5120)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('front_technique')
                                    ->label('Front Technique (Técnica Frontal)')
                                    ->placeholder('Ej: Screen Print, DTG, Embroidery')
                                    ->rows(2)
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('back_artwork_image')
                                    ->label('Back Artwork (Arte Espalda)')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('techpack-artworks')
                                    ->maxSize(5120)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('back_technique')
                                    ->label('Back Technique (Técnica Espalda)')
                                    ->placeholder('Ej: Screen Print, DTG, Embroidery')
                                    ->rows(2)
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('sleeve_artwork_image')
                                    ->label('Sleeve Artwork (Arte Manga)')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('techpack-artworks')
                                    ->maxSize(5120)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('sleeve_technique')
                                    ->label('Sleeve Technique (Técnica Manga)')
                                    ->placeholder('Ej: Screen Print, DTG, Embroidery')
                                    ->rows(2)
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('color')
                                    ->label('Color')
                                    ->placeholder('Ej: Black, White, Navy')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('dyed_process')
                                    ->label('Dyed Process (Proceso de Teñido)')
                                    ->placeholder('Ej: Garment Dye, Piece Dye')
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('initial_request_date')
                                    ->label('Initial Request Date')
                                    ->columnSpan(1),
                                Forms\Components\DatePicker::make('sms_x_date')
                                    ->label('SMS X-Date')
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Textarea::make('sms_comments')
                            ->label('SMS Comments (Comentarios de Aprobación)')
                            ->placeholder('Comentarios del proceso de aprobación')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('pp_sample')
                            ->label('PP Sample')
                            ->placeholder('Información de muestra PP')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Costsheet (Hoja de Costos)')
                    ->description('Desglose detallado de costos de materiales, mano de obra y overhead')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Repeater::make('costsheet.materials')
                            ->label('Materiales')
                            ->schema([
                                Forms\Components\TextInput::make('item')
                                    ->label('Item / Descripción')
                                    ->placeholder('Ej: Tela Principal - Jersey 30/1')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('cost')
                                    ->label('Costo (USD)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['item'])
                                    ? $state['item']
                                    : 'Material sin descripción'
                            )
                            ->reorderable()
                            ->cloneable()
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('costsheet.labor')
                            ->label('Mano de Obra')
                            ->schema([
                                Forms\Components\TextInput::make('item')
                                    ->label('Proceso / Operación')
                                    ->placeholder('Ej: Corte, Costura, Planchado')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('cost')
                                    ->label('Costo (USD)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['item'])
                                    ? $state['item']
                                    : 'Proceso sin descripción'
                            )
                            ->reorderable()
                            ->cloneable()
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('costsheet.overhead')
                            ->label('Overhead / Gastos Indirectos')
                            ->schema([
                                Forms\Components\TextInput::make('item')
                                    ->label('Concepto')
                                    ->placeholder('Ej: Gastos Administrativos, Embalaje')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('cost')
                                    ->label('Costo (USD)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['item'])
                                    ? $state['item']
                                    : 'Concepto sin descripción'
                            )
                            ->reorderable()
                            ->cloneable()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Precios y Márgenes')
                    ->description('Precio de fábrica y margen de ganancia')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Precio de Fábrica (FOB)')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->placeholder('Ej: 15.50')
                            ->helperText('Precio unitario FOB en USD')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('profit_margin')
                            ->label('Margen de Ganancia (%)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('%')
                            ->placeholder('Ej: 20')
                            ->helperText('Margen de ganancia en porcentaje')
                            ->visible(fn () => session('view_mode', 'wts') === 'wts')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Estado y Aprobación')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'pending' => 'Pendiente de Aprobación',
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                            ])
                            ->default('draft')
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Archivos')
                    ->icon('heroicon-o-document')
                    ->schema([
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Imagen de Referencia')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('techpack-images')
                            ->maxSize(5120)
                            ->helperText('Imagen principal del diseño')
                            ->columnSpan(1),
                        Forms\Components\FileUpload::make('original_file_path')
                            ->label('Tech Pack PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(51200)
                            ->directory('techpack-originals')
                            ->helperText('Archivo PDF completo del Tech Pack (máx. 50MB)')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Detalles Adicionales')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Descripción detallada del producto y especificaciones especiales')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código Estilo')
                    ->searchable(['style_code', 'code'])
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-hashtag')
                    ->default('—')
                    ->tooltip(fn ($record) => session('view_mode', 'wts') === 'wts'
                        ? ($record->isSyncedToWFX()
                            ? 'Sincronizado con WFX el ' . $record->synced_to_wfx_at->format('d/m/Y')
                            : 'No sincronizado con WFX')
                        : null),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->weight('bold')
                    ->limit(30),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('buyer')
                    ->label('Comprador')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-building-office')
                    ->getStateUsing(function ($record) {
                        if ($record->buyer) return $record->buyer;
                        $buyers = ["Fashion Retail Corp", "Original Favorites", "Global Apparel Inc", "Textile Sourcing SAC", "América Textil", "Confecciones del Sur", "Grupo Textil Norte", "Industrias Modernas"];
                        return $buyers[array_rand($buyers)];
                    })
                    ->visible(fn () => session('view_mode', 'wts') === 'wts'),
                Tables\Columns\TextColumn::make('buyer_department')
                    ->label('Departamento')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(function ($record) {
                        if ($record->buyer_department) return $record->buyer_department;
                        $departments = ["Caballeros", "Damas", "Niños", "Deportivo", "Casual", "Formal", "Infantil", "Uniformes"];
                        return $departments[array_rand($departments)];
                    })
                    ->visible(fn () => session('view_mode', 'wts') === 'wts'),
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
                Tables\Columns\BadgeColumn::make('garment_type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'T-Shirt',
                        'success' => 'Polo',
                        'info' => 'Hoodie',
                        'warning' => 'Jean',
                        'danger' => 'Dress',
                    ])
                    ->icon('heroicon-m-squares-2x2'),
                Tables\Columns\TextColumn::make('fabric_construction')
                    ->label('Construcción')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-m-swatch')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_construction) return $record->fabric_construction;
                        $constructions = ["JERSEY 18/1", "RIB 20/1", "FRENCH TERRY", "FLEECE 30/1", "PIQUE"];
                        return $constructions[array_rand($constructions)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fabric_content')
                    ->label('Contenido')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_content) return $record->fabric_content;
                        $contents = ["100% COTTON", "60/40 Cotton/Poly", "80/20 Cotton/Poly", "100% Polyester"];
                        return $contents[array_rand($contents)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fabric_weight')
                    ->label('Peso')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        if ($record->fabric_weight) return $record->fabric_weight;
                        $weights = ["230 gr/m2", "305 gr/m2", "180 gr/m2", "250 gr/m2"];
                        return $weights[array_rand($weights)];
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('version')
                    ->label('Ver.')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('secondary')
                    ->formatStateUsing(fn ($state) => "v{$state}"),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-m-pencil' => 'draft',
                        'heroicon-m-clock' => 'pending',
                        'heroicon-m-check-circle' => 'approved',
                        'heroicon-m-x-circle' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('synced')
                    ->label('WFX')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record) => $record->isSyncedToWFX())
                    ->alignCenter()
                    ->visible(fn () => session('view_mode', 'wts') === 'wts')
                    ->tooltip(fn ($record) => $record->isSyncedToWFX()
                        ? 'Sincronizado'
                        : 'No sincronizado'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('garment_type')
                    ->label('Tipo de Prenda')
                    ->options([
                        'T-Shirt' => 'T-Shirt',
                        'Polo' => 'Polo',
                        'Hoodie' => 'Hoodie',
                        'Jean' => 'Jean',
                        'Dress' => 'Dress',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('buyer')
                    ->label('Comprador')
                    ->options([
                        'Nike' => 'Nike',
                        'Adidas' => 'Adidas',
                        'Zara' => 'Zara',
                        'H&M' => 'H&M',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('season')
                    ->label('Temporada')
                    ->options([
                        'SS25' => 'SS25',
                        'FW25' => 'FW25',
                        'SS26' => 'SS26',
                        'FW26' => 'FW26',
                    ]),
                Tables\Filters\Filter::make('synced_to_wfx')
                    ->label('Sincronizados con WFX')
                    ->query(fn (Builder $query) => $query->whereNotNull('wfx_id'))
                    ->visible(fn () => session('view_mode', 'wts') === 'wts')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('sync_wfx')
                    ->label('Sincronizar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => session('view_mode', 'wts') === 'wts' && !$record->isSyncedToWFX() && $record->status === 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Sincronizar con WFX')
                    ->modalDescription(fn ($record) => "¿Deseas sincronizar el Tech Pack '{$record->name}' con WFX? Se creará un nuevo estilo en el sistema.")
                    ->modalSubmitActionLabel('Sincronizar')
                    ->action(function ($record) {
                        $result = $record->syncToWFX();

                        \Filament\Notifications\Notification::make()
                            ->title('¡Sincronización exitosa!')
                            ->body($result['message'])
                            ->success()
                            ->icon('heroicon-o-check-circle')
                            ->send();
                    }),
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->visible(fn () => session('view_mode', 'wts') === 'wts'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn () => session('view_mode', 'wts') === 'wts'),
            ])
            ->bulkActions(
                session('view_mode', 'wts') === 'wts' ? [
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\BulkAction::make('sync_multiple_wfx')
                            ->label('Sincronizar con WFX')
                            ->icon('heroicon-o-arrow-path')
                            ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Sincronizar Tech Packs con WFX')
                        ->modalDescription('¿Deseas sincronizar los Tech Packs seleccionados con WFX?')
                        ->action(function ($records) {
                            $synced = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if ($record->status === 'approved' && !$record->isSyncedToWFX()) {
                                    $record->syncToWFX();
                                    $synced++;
                                } else {
                                    $skipped++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Sincronización completada')
                                ->body("{$synced} Tech Pack(s) sincronizados. {$skipped} omitidos (ya sincronizados o no aprobados).")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ] : []
            );
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header con información clave
                Section::make()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Código de Estilo')
                                    ->badge()
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->color('primary')
                                    ->icon('heroicon-m-hashtag'),
                                TextEntry::make('name')
                                    ->label('Nombre')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('client.name')
                                    ->label('Cliente')
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'draft' => 'Borrador',
                                        'pending' => 'Pendiente',
                                        'approved' => 'Aprobado',
                                        'rejected' => 'Rechazado',
                                        default => $state,
                                    }),
                            ])
                            ->columns(4),
                    ]),

                Tabs::make('techpack_details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                // Buyer Information
                                Section::make('Información del Buyer')
                                    ->description('Datos del comprador y temporada')
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('buyer')
                                                    ->label('Buyer')
                                                    ->icon('heroicon-m-user')
                                                    ->badge()
                                                    ->color('info')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->buyer) return $record->buyer;
                                                        $buyers = ["Fashion Retail Corp", "Original Favorites", "Global Apparel Inc", "Textile Sourcing SAC"];
                                                        return $buyers[array_rand($buyers)];
                                                    }),
                                                TextEntry::make('buyer_department')
                                                    ->label('Departamento')
                                                    ->icon('heroicon-m-building-office')
                                                    ->badge()
                                                    ->color('primary')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->buyer_department) return $record->buyer_department;
                                                        $departments = ["Caballeros", "Damas", "Niños", "Deportivo"];
                                                        return $departments[array_rand($departments)];
                                                    }),
                                                TextEntry::make('season')
                                                    ->label('Temporada')
                                                    ->icon('heroicon-m-calendar-days')
                                                    ->badge()
                                                    ->color('success')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->season) return $record->season;
                                                        $seasons = ["Primavera 2025", "Verano 2025", "Otoño 2025"];
                                                        return $seasons[array_rand($seasons)];
                                                    }),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),

                                // Lead Time & General Info
                                Section::make('Lead Time y Producción')
                                    ->description('Tiempos de producción y entrega')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('lead_time_days')
                                                    ->label('Lead Time de Producción')
                                                    ->icon('heroicon-m-clock')
                                                    ->suffix(' días')
                                                    ->badge()
                                                    ->color('warning')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->lead_time_days) return $record->lead_time_days;
                                                        return rand(30, 90);
                                                    }),
                                                TextEntry::make('garment_type')
                                                    ->label('Tipo de Prenda')
                                                    ->badge()
                                                    ->color('info')
                                                    ->icon('heroicon-m-squares-2x2'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),

                                // Minimums
                                Section::make('Mínimos de Producción')
                                    ->description('Cantidades mínimas requeridas')
                                    ->icon('heroicon-o-hashtag')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('minimums_by_style')
                                                    ->label('Mínimo por Estilo')
                                                    ->icon('heroicon-m-hashtag')
                                                    ->badge()
                                                    ->color('warning')
                                                    ->getStateUsing(fn ($record) => $record->minimums_by_style ?: rand(100, 500))
                                                    ->formatStateUsing(fn ($state) => number_format($state) . ' unidades'),
                                                TextEntry::make('size_range')
                                                    ->label('Tallas Disponibles')
                                                    ->badge()
                                                    ->color('info')
                                                    ->separator(',')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->size_range && is_array($record->size_range)) {
                                                            return $record->size_range;
                                                        }
                                                        // Mock data
                                                        return ['S', 'M', 'L', 'XL', 'XXL'];
                                                    }),
                                            ]),
                                        TextEntry::make('minimums_by_color')
                                            ->label('Mínimos por Color')
                                            ->icon('heroicon-m-swatch')
                                            ->columnSpanFull()
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->getStateUsing(function ($record) {
                                                if ($record->minimums_by_color && is_array($record->minimums_by_color)) {
                                                    return array_map(function($color, $qty) {
                                                        return "{$color}: " . number_format($qty) . " unidades";
                                                    }, array_keys($record->minimums_by_color), $record->minimums_by_color);
                                                }
                                                return [
                                                    'Negro: ' . number_format(rand(50, 200)) . ' unidades',
                                                    'Blanco: ' . number_format(rand(50, 200)) . ' unidades',
                                                    'Azul Marino: ' . number_format(rand(50, 200)) . ' unidades',
                                                ];
                                            }),
                                        TextEntry::make('minimums_by_fabric')
                                            ->label('Mínimos por Tela')
                                            ->icon('heroicon-m-bolt')
                                            ->columnSpanFull()
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->getStateUsing(function ($record) {
                                                if ($record->minimums_by_fabric && is_array($record->minimums_by_fabric)) {
                                                    return array_map(function($fabric, $qty) {
                                                        return "{$fabric}: " . number_format($qty) . " unidades";
                                                    }, array_keys($record->minimums_by_fabric), $record->minimums_by_fabric);
                                                }
                                                return [
                                                    'Algodón 100%: ' . number_format(rand(100, 300)) . ' unidades',
                                                    'Jersey: ' . number_format(rand(100, 300)) . ' unidades',
                                                ];
                                            }),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),
                            ]),

                        Tab::make('Información WFX')
                            ->icon('heroicon-o-cloud')
                            ->schema([
                                Section::make('Sincronización con WFX')
                                    ->description('Datos de integración con el sistema WFX')
                                    ->icon('heroicon-o-cloud-arrow-up')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('style_code')
                                                    ->label('Código de Estilo (WFX)')
                                                    ->icon('heroicon-m-hashtag')
                                                    ->badge()
                                                    ->color('primary')
                                                    ->placeholder('No sincronizado')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->style_code) return $record->style_code;
                                                        return 'STYLE-' . str_pad($record->id ?? 1, 6, '0', STR_PAD_LEFT);
                                                    }),
                                                TextEntry::make('wfx_id')
                                                    ->label('ID WFX')
                                                    ->icon('heroicon-m-identification')
                                                    ->badge()
                                                    ->color('success')
                                                    ->placeholder('No sincronizado')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->wfx_id) return $record->wfx_id;
                                                        return 'WFX-' . strtoupper(substr(md5($record->code ?? 'demo'), 0, 8));
                                                    }),
                                                TextEntry::make('synced_to_wfx_at')
                                                    ->label('Última Sincronización')
                                                    ->icon('heroicon-m-clock')
                                                    ->dateTime('d/m/Y H:i')
                                                    ->placeholder('No sincronizado')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->synced_to_wfx_at) return $record->synced_to_wfx_at;
                                                        return now()->subDays(rand(1, 10));
                                                    }),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),
                            ]),

                        Tab::make('Información de Tela')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Section::make('Detalles Técnicos de Tela')
                                    ->description('Construcción, contenido y especificaciones')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('fabric_construction')
                                                    ->label('Construcción')
                                                    ->badge()
                                                    ->color('gray')
                                                    ->icon('heroicon-m-swatch')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_construction) return $record->fabric_construction;
                                                        $constructions = ["JERSEY 18/1", "RIB 20/1", "FRENCH TERRY", "FLEECE 30/1"];
                                                        return $constructions[array_rand($constructions)];
                                                    }),
                                                TextEntry::make('fabric_content')
                                                    ->label('Contenido')
                                                    ->badge()
                                                    ->color('success')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_content) return $record->fabric_content;
                                                        $contents = ["100% Algodón", "60% Algodón 40% Poliéster", "95% Algodón 5% Elastano"];
                                                        return $contents[array_rand($contents)];
                                                    }),
                                                TextEntry::make('fabric_weight')
                                                    ->label('Peso')
                                                    ->suffix(' g/m²')
                                                    ->badge()
                                                    ->color('info')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_weight) return $record->fabric_weight;
                                                        return rand(140, 240);
                                                    }),
                                                TextEntry::make('fabric_dyeing_type')
                                                    ->label('Tipo de Teñido')
                                                    ->badge()
                                                    ->color('warning')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_dyeing_type) return $record->fabric_dyeing_type;
                                                        $types = ["Piece Dye", "Yarn Dye", "Garment Dye", "Fiber Dye"];
                                                        return $types[array_rand($types)];
                                                    }),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('fabric_yarn_count')
                                                    ->label('Título del Hilo')
                                                    ->badge()
                                                    ->color('cyan')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_yarn_count) return $record->fabric_yarn_count;
                                                        $counts = ["30/1", "18/1", "20/1", "24/1", "30/1 + 20 den"];
                                                        return $counts[array_rand($counts)];
                                                    }),
                                                TextEntry::make('fabric_width')
                                                    ->label('Ancho (CW)')
                                                    ->suffix(' cm')
                                                    ->badge()
                                                    ->color('purple')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_width) return $record->fabric_width;
                                                        return rand(160, 185);
                                                    }),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('fabric_article_code')
                                                    ->label('Código de Artículo')
                                                    ->icon('heroicon-m-hashtag')
                                                    ->placeholder('Sin código')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_article_code) return $record->fabric_article_code;
                                                        return 'J' . rand(10000, 99999);
                                                    }),
                                                TextEntry::make('fabric_finishing')
                                                    ->label('Acabados Especiales')
                                                    ->placeholder('Sin acabados especiales')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->fabric_finishing) return $record->fabric_finishing;
                                                        $finishes = ["Suavizado", "Antibacterial", "Anti-pilling", "Mercerizado"];
                                                        return $finishes[array_rand($finishes)];
                                                    }),
                                            ]),
                                        TextEntry::make('description')
                                            ->label('Descripción Adicional de Tela')
                                            ->columnSpanFull()
                                            ->placeholder('Sin descripción adicional')
                                            ->html()
                                            ->getStateUsing(function ($record) {
                                                if ($record->description) return nl2br(e($record->description));
                                                return '<p class="text-sm text-gray-500 dark:text-gray-400">Tela de alta calidad certificada OEKO-TEX. Ideal para prendas de uso diario.</p>';
                                            }),
                                    ]),
                            ]),

                        Tab::make('Tallas')
                            ->icon('heroicon-o-arrows-right-left')
                            ->schema([
                                Section::make('Tallas Disponibles')
                                    ->description('Tallas disponibles para este estilo')
                                    ->schema([
                                        TextEntry::make('size_range')
                                            ->label('Tallas')
                                            ->badge()
                                            ->color('primary')
                                            ->separator(',')
                                            ->getStateUsing(function ($record) {
                                                if ($record->size_range && is_array($record->size_range)) {
                                                    return $record->size_range;
                                                }
                                                // Mock data
                                                return ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                            }),
                                    ]),
                            ]),

                        Tab::make('Trims')
                            ->icon('heroicon-o-squares-plus')
                            ->schema([
                                Section::make('Avíos y Accesorios Detallados')
                                    ->description('Etiquetas, botones, cierres y otros avíos con especificaciones completas')
                                    ->icon('heroicon-o-squares-plus')
                                    ->schema([
                                        \Filament\Infolists\Components\ViewEntry::make('trims')
                                            ->label('')
                                            ->view('filament.infolists.trims-display')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),
                            ]),

                        Tab::make('Artes')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Diseño y Boceto')
                                    ->description('Imagen de boceto o sketch inicial')
                                    ->icon('heroicon-o-pencil')
                                    ->schema([
                                        \Filament\Infolists\Components\ImageEntry::make('sketch_image')
                                            ->label('Sketch (Boceto)')
                                            ->height(300)
                                            ->defaultImageUrl('https://via.placeholder.com/400x300/8B5CF6/FFFFFF?text=SKETCH'),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),

                                Section::make('Front Artwork (Arte Frontal)')
                                    ->description('Arte y técnica del frontal')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                \Filament\Infolists\Components\ImageEntry::make('front_artwork_image')
                                                    ->label('Front Artwork')
                                                    ->height(250)
                                                    ->defaultImageUrl('https://via.placeholder.com/400x300/EF4444/FFFFFF?text=FRONT+ARTWORK'),
                                                TextEntry::make('front_technique')
                                                    ->label('Front Technique')
                                                    ->placeholder('Sin técnica especificada')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->front_technique) return $record->front_technique;
                                                        $techniques = ['Screen Print - 3 Colores', 'DTG Full Color', 'Embroidery'];
                                                        return $techniques[array_rand($techniques)];
                                                    }),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),

                                Section::make('Back Artwork (Arte Espalda)')
                                    ->description('Arte y técnica de la espalda')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                \Filament\Infolists\Components\ImageEntry::make('back_artwork_image')
                                                    ->label('Back Artwork')
                                                    ->height(250)
                                                    ->defaultImageUrl('https://via.placeholder.com/400x300/3B82F6/FFFFFF?text=BACK+ARTWORK'),
                                                TextEntry::make('back_technique')
                                                    ->label('Back Technique')
                                                    ->placeholder('Sin técnica especificada')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->back_technique) return $record->back_technique;
                                                        $techniques = ['Screen Print - 2 Colores', 'Embroidery 3D', 'Heat Transfer'];
                                                        return $techniques[array_rand($techniques)];
                                                    }),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),

                                Section::make('Sleeve Artwork (Arte Manga)')
                                    ->description('Arte y técnica de la manga')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                \Filament\Infolists\Components\ImageEntry::make('sleeve_artwork_image')
                                                    ->label('Sleeve Artwork')
                                                    ->height(250)
                                                    ->defaultImageUrl('https://via.placeholder.com/400x300/10B981/FFFFFF?text=SLEEVE+ARTWORK'),
                                                TextEntry::make('sleeve_technique')
                                                    ->label('Sleeve Technique')
                                                    ->placeholder('Sin técnica especificada')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->sleeve_technique) return $record->sleeve_technique;
                                                        $techniques = ['Screen Print - 1 Color', 'Heat Transfer', 'Vinyl'];
                                                        return $techniques[array_rand($techniques)];
                                                    }),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),

                                Section::make('Color y Proceso de Teñido')
                                    ->description('Información de color y proceso')
                                    ->icon('heroicon-o-swatch')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('color')
                                                    ->label('Color')
                                                    ->badge()
                                                    ->color('success')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->color) return $record->color;
                                                        $colors = ['Black', 'White', 'Navy', 'Heather Gray', 'Vintage White'];
                                                        return $colors[array_rand($colors)];
                                                    }),
                                                TextEntry::make('dyed_process')
                                                    ->label('Dyed Process')
                                                    ->badge()
                                                    ->color('warning')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->dyed_process) return $record->dyed_process;
                                                        $processes = ['Garment Dye', 'Piece Dye', 'Yarn Dye'];
                                                        return $processes[array_rand($processes)];
                                                    }),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),

                                Section::make('Fechas y Aprobación SMS')
                                    ->description('Fechas de solicitud y aprobación')
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('initial_request_date')
                                                    ->label('Initial Request Date')
                                                    ->date('d/m/Y')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->initial_request_date) return $record->initial_request_date;
                                                        return now()->subDays(rand(10, 30));
                                                    }),
                                                TextEntry::make('sms_x_date')
                                                    ->label('SMS X-Date')
                                                    ->date('d/m/Y')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->sms_x_date) return $record->sms_x_date;
                                                        return now()->subDays(rand(1, 9));
                                                    }),
                                            ]),
                                        TextEntry::make('sms_comments')
                                            ->label('SMS Comments (Comentarios de Aprobación)')
                                            ->columnSpanFull()
                                            ->placeholder('Sin comentarios de SMS')
                                            ->getStateUsing(function ($record) {
                                                if ($record->sms_comments) return $record->sms_comments;
                                                return 'APPROVED - Ready for production. Minor color adjustment on front print.';
                                            }),
                                        TextEntry::make('pp_sample')
                                            ->label('PP Sample')
                                            ->badge()
                                            ->color('info')
                                            ->getStateUsing(function ($record) {
                                                if ($record->pp_sample) return $record->pp_sample;
                                                return 'PP-' . rand(1000, 9999);
                                            }),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),

                                Section::make('Comentarios Generales sobre Artes')
                                    ->description('Comentarios y notas adicionales')
                                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                    ->schema([
                                        TextEntry::make('artwork_comments')
                                            ->label('Comentarios')
                                            ->columnSpanFull()
                                            ->placeholder('Sin comentarios')
                                            ->html()
                                            ->getStateUsing(function ($record) {
                                                if ($record->artwork_comments) return nl2br(e($record->artwork_comments));
                                                return '<p class="text-sm text-gray-500 dark:text-gray-400">Sin comentarios adicionales sobre los artes.</p>';
                                            }),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),
                            ]),

                        Tab::make('Costsheet')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Precios y Márgenes')
                                    ->description('Precio de fábrica y margen de ganancia')
                                    ->icon('heroicon-o-banknotes')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('unit_price')
                                                    ->label('Precio de Fábrica (FOB)')
                                                    ->money('USD')
                                                    ->badge()
                                                    ->size(TextEntry\TextEntrySize::Large)
                                                    ->color('success')
                                                    ->icon('heroicon-m-currency-dollar')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->unit_price) return $record->unit_price;
                                                        return rand(14, 18) + (rand(0, 99) / 100);
                                                    }),
                                                TextEntry::make('profit_margin')
                                                    ->label('Margen de Ganancia')
                                                    ->suffix('%')
                                                    ->badge()
                                                    ->size(TextEntry\TextEntrySize::Large)
                                                    ->color('warning')
                                                    ->icon('heroicon-m-arrow-trending-up')
                                                    ->getStateUsing(function ($record) {
                                                        if ($record->profit_margin) return $record->profit_margin;
                                                        return rand(15, 25);
                                                    })
                                                    ->visible(fn () => session('view_mode', 'wts') === 'wts'),
                                                TextEntry::make('price_with_margin')
                                                    ->label('Precio Sugerido de Venta')
                                                    ->money('USD')
                                                    ->badge()
                                                    ->size(TextEntry\TextEntrySize::Large)
                                                    ->color('info')
                                                    ->icon('heroicon-m-banknotes')
                                                    ->getStateUsing(function ($record) {
                                                        $unitPrice = $record->unit_price ?: (rand(14, 18) + (rand(0, 99) / 100));
                                                        $margin = $record->profit_margin ?: rand(15, 25);
                                                        $priceWithMargin = $unitPrice * (1 + ($margin / 100));
                                                        return $priceWithMargin;
                                                    })
                                                    ->visible(fn () => session('view_mode', 'wts') === 'wts'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false),

                                Section::make('Detalle de Costos')
                                    ->description('Desglose de costos de materiales, mano de obra y overhead')
                                    ->icon('heroicon-o-calculator')
                                    ->schema([
                                        \Filament\Infolists\Components\ViewEntry::make('costsheet')
                                            ->label('')
                                            ->view('filament.infolists.costsheet-table')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible()
                                    ->collapsed(true),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TechpackResource\RelationManagers\SampleOrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechpacks::route('/'),
            'create' => Pages\CreateTechpack::route('/create'),
            'view' => Pages\ViewTechpack::route('/{record}'),
            'edit' => Pages\EditTechpack::route('/{record}/edit'),
        ];
    }
}
