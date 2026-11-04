# Diccionario de Datos — VetPets ERP (Segundo Corte)

Este documento especifica el esquema completo de base de datos de **VetPets ERP**, integrando las tablas de compras, inventario, control de lotes, tarifas con IVA diferencial, ventas POS, clientes, seguridad RBAC e identidad corporativa.

---

## 1. Tabla: `categories` (Categorías de Catálogo)
Clasifica las familias comerciales del catálogo (alimentos, snacks, higiene, accesorios, medicamentos).

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único de la categoría. |
| `name` | `VARCHAR(100)` | No | — | — | Nombre de la categoría comercial. |
| `description` | `TEXT` | Sí | — | `NULL` | Descripción de la categoría. |
| `active` | `BOOLEAN` | No | — | `true` | Estado operativo (`true` = Activa, `false` = Inactiva). |
| `deleted_at` | `TIMESTAMP` | Sí | — | `NULL` | Borrado lógico (`SoftDeletes`). |
| `created_at` / `updated_at` | `TIMESTAMP` | Sí | — | `NULL` | Marcas de tiempo de auditoría. |

---

## 2. Tabla: `products` (Inventario, Lotes y Tarifas)
Centraliza el inventario físico, el costo de compra, los datos de lote/vencimiento y las tarifas de venta con IVA diferencial.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único del producto. |
| `category_id` | `BIGINT UNSIGNED` | No | FK | — | Categoría comercial (`categories.id`). |
| `barcode` | `VARCHAR(50)` | Sí | Unique | `NULL` | Código de barras único. |
| `name` | `VARCHAR(150)` | No | — | — | Nombre comercial del producto. |
| `cost_price` | `DECIMAL(10,2)` | No | — | `0.00` | Costo de compra en bodega (actualizado al recibir órdenes de compra). |
| `sale_price` | `DECIMAL(10,2)` | No | — | `0.00` | Precio base de venta al público sin IVA (gestionado en **Tarifas e IVA**). |
| `tax_rate` | `DECIMAL(5,2)` | No | — | `19.00` | Porcentaje de IVA diferencial (`0.00` Exento, `5.00` Diferencial, `19.00` General). |
| `batch_code` | `VARCHAR(50)` | Sí | — | `NULL` | Código de lote o serie de fabricación (`LOT-2026-...`). |
| `expiration_date` | `DATE` | Sí | — | `NULL` | Fecha de vencimiento del lote (`NULL` si no perecedero). |
| `current_stock` | `INT` | No | — | `0` | Existencias actuales en inventario de bodega. |
| `minimum_stock` | `INT` | No | — | `5` | Umbral mínimo para generar alertas de reabastecimiento. |
| `active` | `BOOLEAN` | No | — | `true` | Estado del producto. |
| `deleted_at` | `TIMESTAMP` | Sí | — | `NULL` | Borrado lógico (`SoftDeletes`). |

---

## 3. Tabla: `providers` (Proveedores y Distribuidores)
Almacena los datos de distribuidores y proveedores mayoristas.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único del proveedor. |
| `nit` | `VARCHAR(30)` | No | Unique | — | NIT o documento tributario del distribuidor. |
| `company_name` | `VARCHAR(150)` | No | — | — | Razón social o nombre comercial. |
| `contact_person` | `VARCHAR(120)` | Sí | — | `NULL` | Nombre del asesor o persona de contacto. |
| `phone` | `VARCHAR(30)` | Sí | — | `NULL` | Teléfono de pedidos. |
| `email` | `VARCHAR(120)` | Sí | — | `NULL` | Correo electrónico de contacto. |
| `address` | `VARCHAR(200)` | Sí | — | `NULL` | Dirección de la bodega o sede del proveedor. |
| `active` | `BOOLEAN` | No | — | `true` | Estado del proveedor. |
| `deleted_at` | `TIMESTAMP` | Sí | — | `NULL` | Borrado lógico (`SoftDeletes`). |

---

## 4. Tabla: `purchases` (Cabecera de Órdenes de Compra)
Registra las solicitudes de mercancía realizadas a los proveedores.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único de la orden de compra. |
| `purchase_number` | `VARCHAR(25)` | No | Unique | — | Número consecutivo de orden (ej. `OC-00001`). |
| `provider_id` | `BIGINT UNSIGNED` | No | FK | — | Proveedor asociado (`providers.id`, `ON DELETE RESTRICT`). |
| `user_id` | `BIGINT UNSIGNED` | No | FK | — | Usuario comprador que registró la orden (`users.id`). |
| `purchase_date` | `DATE` | No | — | — | Fecha de emisión de la orden. |
| `subtotal` | `DECIMAL(12,2)` | No | — | `0.00` | Subtotal antes de impuestos. |
| `tax` | `DECIMAL(12,2)` | No | — | `0.00` | Impuesto calculado de la orden. |
| `total` | `DECIMAL(12,2)` | No | — | `0.00` | Total de la orden (`subtotal + tax`). |
| `status` | `VARCHAR(20)` | No | — | `'pendiente'` | Estado de la orden: `pendiente`, `parcial`, `recibida` o `cancelada`. |
| `notes` | `TEXT` | Sí | — | `NULL` | Observaciones de entrega. |
| `deleted_at` | `TIMESTAMP` | Sí | — | `NULL` | Borrado lógico (`SoftDeletes`). |

---

## 5. Tabla: `purchase_details` (Líneas de Detalle de Compra y Recepción)
Registra los productos solicitados y las cantidades recibidas físicamente en bodega.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único del ítem. |
| `purchase_id` | `BIGINT UNSIGNED` | No | FK | — | Orden de compra padre (`purchases.id`, `ON DELETE CASCADE`). |
| `product_id` | `BIGINT UNSIGNED` | No | FK | — | Producto solicitado (`products.id`, `ON DELETE RESTRICT`). |
| `quantity` | `INT UNSIGNED` | No | — | — | Cantidad total solicitada ($\ge 1$). |
| `received_quantity` | `INT UNSIGNED` | No | — | `0` | Cantidad acumulada recibida en bodega ($\le$ `quantity`). |
| `unit_cost` | `DECIMAL(12,2)` | No | — | — | Costo de compra unitario pactado con el distribuidor. |
| `subtotal` | `DECIMAL(12,2)` | No | — | — | Subtotal del renglón (`quantity * unit_cost`). |

---

## 6. Tabla: `owners` (Clientes)
Directorio de clientes compradores en mostrador.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único del cliente. |
| `document_type` | `VARCHAR(10)` | No | — | — | Tipo de documento (CC, NIT, CE, PAS). |
| `document_number` | `VARCHAR(20)` | No | Unique | — | Número de documento único. |
| `first_name` | `VARCHAR(50)` | No | — | — | Nombres del cliente. |
| `last_name` | `VARCHAR(50)` | No | — | — | Apellidos del cliente. |
| `address` | `VARCHAR(150)` | Sí | — | `NULL` | Dirección de residencia. |
| `email` | `VARCHAR(100)` | Sí | — | `NULL` | Correo electrónico de contacto. |

---

## 7. Tabla: `owner_phones` (Teléfonos de Clientes)
Permite almacenar múltiples líneas telefónicas por cliente ($1:N$).

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único del teléfono. |
| `owner_id` | `BIGINT UNSIGNED` | No | FK | — | Cliente asociado (`owners.id`, `ON DELETE CASCADE`). |
| `phone_number` | `VARCHAR(20)` | No | — | — | Número de teléfono o móvil. |

---

## 8. Tabla: `sales` (Cabecera de Facturas de Venta POS)
Encabezado de facturación de ventas en mostrador.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único de la venta. |
| `invoice_number` | `VARCHAR(25)` | No | Unique | — | Número secuencial de factura (ej. `FV-00001`). |
| `client_id` | `BIGINT UNSIGNED` | No | FK | — | Cliente comprador (`owners.id`, `ON DELETE RESTRICT`). |
| `user_id` | `BIGINT UNSIGNED` | No | FK | — | Vendedor que registró la venta (`users.id`). |
| `sale_date` | `DATE` | No | — | — | Fecha de expedición de la factura. |
| `subtotal` | `DECIMAL(12,2)` | No | — | `0.00` | Suma de los subtotales de las líneas antes de impuestos. |
| `tax` | `DECIMAL(12,2)` | No | — | `0.00` | Suma de IVA liquidado línea por línea según la tarifa de cada producto (0 %, 5 % o 19 %). |
| `total` | `DECIMAL(12,2)` | No | — | `0.00` | Total cobrado al cliente (`subtotal + tax`). |
| `status` | `VARCHAR(20)` | No | — | `'pagada'` | Estado de la factura: `pagada` o `anulada`. |
| `notes` | `TEXT` | Sí | — | `NULL` | Notas u observaciones de la venta. |
| `deleted_at` | `TIMESTAMP` | Sí | — | `NULL` | Borrado lógico al anular (`SoftDeletes`). |

---

## 9. Tabla: `sale_details` (Líneas de Detalle de Venta)
Registro individual de productos vendidos por factura.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único del renglón. |
| `sale_id` | `BIGINT UNSIGNED` | No | FK | — | Factura asociada (`sales.id`, `ON DELETE CASCADE`). |
| `product_id` | `BIGINT UNSIGNED` | No | FK | — | Producto vendido (`products.id`, `ON DELETE RESTRICT`). |
| `quantity` | `INT UNSIGNED` | No | — | — | Cantidad despachada ($\ge 1$). |
| `unit_price` | `DECIMAL(12,2)` | No | — | — | Precio unitario base de venta al momento de facturar. |
| `subtotal` | `DECIMAL(12,2)` | No | — | — | Subtotal del ítem (`quantity * unit_price`). |

---

## 10. Tabla: `company_settings` (Identidad Corporativa)
Almacena los parámetros configurables de la empresa emisora.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único. |
| `company_name` | `VARCHAR(150)` | No | — | — | Razón social de la empresa. |
| `business_subtitle` | `VARCHAR(150)` | Sí | — | `NULL` | Eslogan o subtítulo comercial. |
| `nit` | `VARCHAR(30)` | No | — | — | NIT o identificación fiscal. |
| `address` | `VARCHAR(200)` | Sí | — | `NULL` | Dirección física principal. |
| `phone` | `VARCHAR(50)` | Sí | — | `NULL` | Teléfono de atención. |
| `email` | `VARCHAR(120)` | Sí | — | `NULL` | Correo electrónico de contacto. |
| `primary_color` | `VARCHAR(30)` | No | — | `'emerald'` | Color corporativo de la interfaz (`emerald`, `indigo`, `blue`, etc.). |
| `icon_class` | `VARCHAR(100)` | No | — | `'fa-solid fa-store'` | Ícono representativo en FontAwesome. |
| `tax_rate` | `DECIMAL(5,2)` | No | — | `19.00` | Tarifa de IVA estándar por defecto. |
| `currency` | `VARCHAR(10)` | No | — | `'COP'` | Símbolo o código de moneda. |
| `invoice_prefix` | `VARCHAR(10)` | No | — | `'FV'` | Prefijo de consecutivo de facturas de venta. |
| `purchase_prefix` | `VARCHAR(10)` | No | — | `'OC'` | Prefijo de consecutivo de órdenes de compra. |
| `invoice_footer` | `TEXT` | Sí | — | `NULL` | Leyenda o mensaje legal al pie de los documentos PDF. |

---

## 11. Tabla: `system_modules` (Gestión de Módulos)
Administra los módulos del sistema con sus dependencias funcionales.

| Campo | Tipo de Dato | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :---: | :--- |
| `id` | `BIGINT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Identificador único del módulo. |
| `key` | `VARCHAR(50)` | No | Unique | — | Clave única del módulo (`products`, `pricing`, `sales`, etc.). |
| `name` | `VARCHAR(100)` | No | — | — | Nombre legible del módulo. |
| `description` | `TEXT` | Sí | — | `NULL` | Descripción de su propósito funcional. |
| `icon` | `VARCHAR(100)` | No | — | — | Ícono del módulo para el menú y dashboard. |
| `is_enabled` | `BOOLEAN` | No | — | `true` | Estado del módulo (`true` = Habilitado, `false` = Deshabilitado). |
| `is_core` | `BOOLEAN` | No | — | `false` | Indica si es módulo indispensable del núcleo. |
| `depends_on` | `JSON` | Sí | — | `NULL` | Arreglo de claves de módulos de los que depende. |
