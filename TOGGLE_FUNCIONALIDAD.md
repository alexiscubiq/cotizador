# Toggle WTS / Proveedor - Funcionalidad

## 📍 Ubicación
El toggle se encuentra al **final del sidebar** de navegación, en la parte inferior izquierda de la pantalla.

## 🎯 ¿Qué afecta el toggle?

### En Quotes (Cotizaciones):

#### 1. **Formulario de Creación/Edición:**
- **Campo "Margen de Ganancia":**
  - ✅ **Vista WTS:** Campo VISIBLE - Se puede ver y editar el profit margin
  - ❌ **Vista Proveedor:** Campo OCULTO - El proveedor no ve ni puede editar este campo

#### 2. **Tabla de Lista (Index):**
- **Columna "Margen":**
  - ✅ **Vista WTS:** Columna VISIBLE - Muestra el % de margen de ganancia
  - ❌ **Vista Proveedor:** Columna OCULTA - No aparece en la tabla

#### 3. **Vista Detallada (Infolist):**
- **Sección "Precios y Costos" → Campo "Margen de Ganancia":**
  - ✅ **Vista WTS:** Campo VISIBLE con badge verde mostrando el porcentaje
  - ❌ **Vista Proveedor:** Campo NO SE MUESTRA en absoluto

## 🔄 Cómo Probarlo

### Paso 1: Acceder a Quotes
1. Login en http://localhost:8000/admin/login
2. Ir a "Quotes" en el menú lateral

### Paso 2: Ver en modo WTS
1. En el toggle del sidebar, asegúrate de tener seleccionado **"WTS"**
2. En la tabla, verás la columna **"Margen"** con porcentajes
3. Clic en "View" (ojo) de cualquier quote
4. Scroll hasta la sección "Precios y Costos"
5. Verás el campo **"Margen de Ganancia"** con badge verde

### Paso 3: Cambiar a modo Proveedor
1. En el toggle del sidebar, clic en **"Proveedor"**
2. La página se recargará
3. En la tabla, la columna **"Margen"** habrá desaparecido
4. Clic en "View" de cualquier quote
5. En "Precios y Costos", el **"Margen de Ganancia"** NO aparecerá

### Paso 4: Crear/Editar un Quote
1. Con vista **WTS** activa: Verás el campo "Margen de Ganancia" en el formulario
2. Cambia a vista **Proveedor**: El campo desaparecerá del formulario

## 💡 Caso de Uso

Este toggle es útil para:
- **Demos y Presentaciones:** Mostrar al cliente cómo se ve la interfaz desde su perspectiva
- **Screenshots para Figma:** Capturar ambas vistas para documentación
- **Testing:** Verificar que la información sensible no sea visible para proveedores
- **Desarrollo:** Probar la UI sin cambiar de usuario

## ⚠️ Importante

- El toggle es **solo para visualización** en este mockup
- En producción, esto estaría determinado por el `user_type` del usuario autenticado
- La sesión recuerda tu selección mientras navegas

## 🎨 Diseño del Toggle

```
┌─────────────────────┐
│ Vista: [WTS] [Proveedor] │
└─────────────────────┘
```

- Diseño tipo "segmented control"
- Botón activo tiene fondo blanco con sombra
- Botón inactivo tiene texto gris
- Pequeño y discreto (texto xs)
- Ubicado al final del sidebar con borde superior

## 📊 Resumen de Visibilidad

| Campo/Columna | Vista WTS | Vista Proveedor |
|---------------|-----------|-----------------|
| Profit Margin (Formulario) | ✅ Visible | ❌ Oculto |
| Margen (Tabla) | ✅ Visible | ❌ Oculto |
| Margen de Ganancia (Detalle) | ✅ Visible | ❌ Oculto |
| Todos los demás campos | ✅ Visible | ✅ Visible |

## 🚀 Próximas Extensiones Posibles

En el futuro, el toggle podría afectar:
- Precios de compra vs precios de venta
- Información de costsheet detallado
- Datos de contacto internos
- Notas privadas de WTS
- Análisis de rentabilidad
