# AGENTS.md - Guía para Modelos de IA

## Contexto del Proyecto

**Nombre:** Cotizador WTS
**Stack Técnico:** Laravel 12 + Filament 3 + SQLite
**Propósito:** Sistema de gestión de cotizaciones (RFQ), Tech Packs, y Sample Orders para la industria textil/moda

## Objetivo Principal

Este proyecto es un **MOCKUP FUNCIONAL** para crear prototipos visuales que luego se trasladarán a Figma. Por lo tanto:

- ✅ **Prioridad:** UX/UI excepcional, componentes visuales perfectos
- ✅ **Backend:** Datos mockeados están bien, no necesita integración real
- ❌ **No es necesario:** Validaciones complejas, lógica de negocio real, optimizaciones de performance

## Principios de Desarrollo

### 1. Experiencia de Usuario (UX/UI)

**Actúa como un experto en UX/UI con muchos años de experiencia:**

- Crear componentes con las mejores prácticas de diseño
- Cada tipo de dato debe tener la representación visual perfecta
- Priorizar claridad, jerarquía visual y usabilidad
- Usar espaciado, tipografía y colores de manera consistente
- Pensar en el flujo del usuario y minimizar fricción

### 2. Filament 3 - Framework UI

**SIEMPRE usar Filament 3** para todos los componentes:

```php
// ✅ CORRECTO - Usar componentes nativos de Filament
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Section;

// ❌ INCORRECTO - No crear componentes Blade custom innecesarios
```

**Componentes Filament a priorizar:**
- **Forms:** TextInput, Select, DatePicker, Toggle, Repeater, FileUpload
- **Tables:** TextColumn, BadgeColumn, ImageColumn, SelectColumn
- **Infolists:** TextEntry, Section, Grid, Tabs, RepeatableEntry
- **Actions:** Action buttons, Modal actions, Bulk actions
- **Notifications:** Toast notifications para feedback

### 3. Mejores Prácticas por Tipo de Dato

| Tipo de Dato | Componente Recomendado | Ejemplo de Uso |
|--------------|------------------------|----------------|
| **Texto corto** | `TextInput` / `TextEntry` | Nombres, códigos |
| **Texto largo** | `Textarea` / `TextEntry` con markdown | Descripciones |
| **Moneda/Precio** | `TextInput` con `prefix('$')` y `numeric()` | Precios, costos |
| **Porcentaje** | `TextInput` con `suffix('%')` | Márgenes |
| **Fechas** | `DatePicker` / `DateTimePicker` | Fechas de entrega |
| **Estados/Status** | `Select` + `BadgeColumn` con colores | Estados de proceso |
| **Imágenes** | `FileUpload` con preview + `ImageColumn` | Fotos de productos |
| **Archivos** | `FileUpload` con `acceptedFileTypes()` | PDFs, documentos |
| **Listas dinámicas** | `Repeater` con schemas anidados | Materiales, medidas |
| **Relaciones** | `Select` con `relationship()` y `searchable()` | Cliente, Proveedor |
| **JSON estructurado** | `Repeater` o `KeyValue` | Configuraciones |
| **Tablas complejas** | HTML custom en `TextEntry` con clases Filament | Costsheets |

### 4. Organización Visual

**Secciones y Layouts:**
```php
// ✅ Agrupar campos relacionados en Sections
Forms\Components\Section::make('Información Básica')
    ->description('Datos principales del registro')
    ->icon('heroicon-o-information-circle')
    ->schema([...])
    ->columns(2)
    ->collapsible()
    ->collapsed(false);

// ✅ Usar Tabs para organizar mucha información
Tabs::make('quote_details')
    ->tabs([
        Tab::make('General')->icon('heroicon-o-home'),
        Tab::make('Materiales')->icon('heroicon-o-cube'),
    ]);

// ✅ Grids para layouts responsivos
Grid::make(['default' => 1, 'md' => 2, 'lg' => 3])
    ->schema([...]);
```

### 5. Colores y Estados

**Sistema de colores consistente:**
```php
'draft' => 'secondary',      // Gris
'pending' => 'warning',       // Amarillo/Naranja
'in_progress' => 'info',      // Azul
'approved' => 'success',      // Verde
'rejected' => 'danger',       // Rojo
'completed' => 'success',     // Verde
'cancelled' => 'danger',      // Rojo
```

**Iconos relevantes:**
- Usuarios: `heroicon-o-user`, `heroicon-o-users`
- Documentos: `heroicon-o-document`, `heroicon-o-document-text`
- Fechas: `heroicon-o-calendar`, `heroicon-o-clock`
- Estados: `heroicon-o-check-circle`, `heroicon-o-x-circle`
- Acciones: `heroicon-o-plus`, `heroicon-o-pencil`, `heroicon-o-trash`

### 6. Feedback y Comunicación

**Notificaciones claras:**
```php
Notification::make()
    ->title('Operación exitosa')
    ->body('El techpack fue sincronizado con WFX')
    ->success()
    ->icon('heroicon-o-check-circle')
    ->send();
```

**Helper text útil:**
```php
->helperText('Solo se muestran techpacks aprobados del cliente seleccionado')
```

### 7. Accesibilidad

- Labels descriptivos para todos los campos
- Placeholders que guían al usuario
- Mensajes de error claros y accionables
- Descripciones en sections complejas
- Tooltips para funcionalidades avanzadas

## Estructura del Proyecto

### Modelos Principales

```
Client (Cliente)
├── Techpack (Tech Pack)
│   ├── SampleOrder (Orden de Muestra)
│   └── FabricMaterial (Materiales)
└── Quote (Cotización/RFQ)
    ├── QuoteType (Tipo de Cotización)
    ├── Supplier (Proveedor)
    ├── ProductionMilestone (Hitos TNA)
    └── PurchaseOrder (Orden de Compra)
```

### Tipos de Usuario

1. **WTS Internal** (Usuario interno)
   - Acceso completo a todas las funcionalidades
   - Ve márgenes de ganancia
   - Puede gestionar todos los registros

2. **Supplier** (Proveedor)
   - Vista acotada/limitada
   - No ve márgenes de ganancia
   - Solo ve cotizaciones asignadas a ellos

## Integración con WFX (Sistema PLM Externo)

**IMPORTANTE:** La integración con WFX es **MOCKEADA** por ahora.

- No implementar llamadas HTTP reales
- Simular respuestas exitosas/errores con delays artificiales
- Usar datos de ejemplo consistentes
- Crear métodos stub que se puedan implementar más adelante

**Ejemplo de mock:**
```php
// ✅ Correcto - Mock simple
public function syncToWFX()
{
    sleep(1); // Simular delay de red

    Notification::make()
        ->title('Sincronizado con WFX')
        ->body("Estilo #{$this->style_code} creado exitosamente")
        ->success()
        ->send();

    return ['success' => true, 'wfx_id' => 'WFX-' . rand(1000, 9999)];
}
```

## Campos y Nomenclatura WFX

### Tech Pack
- `style_code`: Código del estilo en WFX (ej: "WFX-001")
- `buyer`: Comprador/Brand (ej: "Nike", "Adidas")
- `buyer_department`: Departamento (ej: "Men's", "Women's", "Kids")
- `season`: Temporada (ej: "SS25", "FW25")

### Estados Homologados
```php
// Portal → WFX
'draft' => 'DRAFT',
'pending' => 'PENDING_APPROVAL',
'approved' => 'APPROVED',
'in_production' => 'IN_PRODUCTION',
'completed' => 'COMPLETED',
'rejected' => 'REJECTED',
'cancelled' => 'CANCELLED'
```

## Datos de Ejemplo / Seeders

Crear seeders con datos **realistas y variados**:

- Nombres de marcas reales (Nike, Zara, H&M, Adidas)
- Tipos de prendas diversos (T-shirts, Hoodies, Jeans, Dresses)
- Rangos de precios lógicos ($5-$150)
- Fechas coherentes (creación < entrega)
- Estados distribuidos (no todo "completado")
- Materiales textiles reales (Cotton, Polyester, Spandex)

**Ejemplo de datos mock:**
```php
'buyers' => ['Nike', 'Adidas', 'Zara', 'H&M', 'GAP', 'Old Navy'],
'departments' => ['Men's', 'Women's', 'Kids', 'Unisex'],
'seasons' => ['SS25', 'FW25', 'SS26', 'FW26'],
'garment_types' => ['T-Shirt', 'Polo', 'Hoodie', 'Jogger', 'Jean', 'Dress'],
```

## Comandos Útiles

```bash
# Correr migraciones
php artisan migrate

# Crear migration
php artisan make:migration add_fields_to_table

# Crear seeder
php artisan make:seeder TableSeeder

# Correr seeders
php artisan db:seed

# Limpiar cache
php artisan optimize:clear

# Crear Filament Resource
php artisan make:filament-resource ModelName
```

## Flujo de Trabajo para Nuevas Features

1. **Migración** → Agregar campos necesarios a la BD
2. **Modelo** → Actualizar $fillable y $casts
3. **Resource** → Actualizar form(), table(), infolist()
4. **Seeder** → Agregar datos de ejemplo
5. **Testing Visual** → Verificar en el navegador

## Próximos Pasos (Para Implementación Real)

Cuando este mockup se traslade a producción:

1. Implementar API client real para WFX
2. Agregar validaciones robustas
3. Implementar permisos granulares con policies
4. Agregar testing automatizado
5. Optimizar queries con eager loading
6. Implementar queue jobs para sincronizaciones
7. Agregar logs y monitoring

## Notas Importantes

- **NO** modificar migraciones existentes, crear nuevas
- **SIEMPRE** usar Filament 3, nunca componentes custom innecesarios
- **Priorizar** la experiencia visual sobre la funcionalidad backend
- **Documentar** cambios importantes en este archivo
- **Pensar** en diseño primero, código después

---

**Última actualización:** 2025-10-31
**Versión:** 1.0
**Mantenedor:** AI Development Team
