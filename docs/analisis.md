# Análisis de la Empresa — VetPets ERP

## 1. Datos Generales
- **Nombre:** VetPets ERP
- **Giro del negocio:** Comercialización minorista de alimentos balanceados, snacks, medicamentos veterinarios, antiparasitarios, accesorios y productos de higiene para mascotas (PetShop & Retail).
- **Modelo de arquitectura:** Sistema modular parametrizable con soporte de **Identidad Corporativa** (permite configurar razón social, NIT, paleta de colores y parámetros de facturación) y **Gestión de Módulos** con validación de dependencias.
- **Tamaño:** Pequeña a Mediana Empresa (Pyme Comercial).

---

## 2. Procesos Clave

1. **Abastecimiento y Órdenes de Compra:**
   - Registro de proveedores mayoristas con NIT, contacto y teléfono.
   - Creación de órdenes de compra en estado inicial `pendiente` sin alterar el inventario.
   - Recepción física parcial o total en bodega: incrementa el `current_stock` del producto y actualiza automáticamente el costo unitario de compra (`cost_price`).
   - Bloqueo estricto para no recibir más unidades de las pactadas.

2. **Inventario, Control de Lotes y Tarifas:**
   - **Inventario y Lotes (`/products`):** Registro de existencias físicas, costo de compra, código de lote (`batch_code`) y fecha de vencimiento (`expiration_date`) con semáforo automático (*Lote vigente*, *Próximo a vencer*, *Lote vencido*, *No perecedero*).
   - **Tarifas e IVA (`/tarifas`):** Módulo independiente para fijar el precio base de venta (`sale_price`), calcular el margen de rentabilidad (%) y asignar la tarifa de **IVA diferencial**:
     - **0 % (Exento):** Medicamentos y antiparasitarios.
     - **5 % (Tarifa Diferencial):** Alimentos concentrados e insumos nutricionales.
     - **19 % (Tarifa General):** Artículos de paseo, camas, juguetes e higiene.

3. **Ventas y Facturación POS:**
   - Selección de cliente y productos en el punto de venta.
   - Cálculo del impuesto línea por línea según el IVA diferencial de cada producto.
   - Validación de stock disponible y **bloqueo automático de lotes vencidos**.
   - Transacción ACID (`DB::transaction`) con bloqueo de fila (`lockForUpdate()`), descuento atómico de inventario y emisión de factura consecutiva.
   - Anulación de venta con restitución automática de las unidades al inventario.

4. **Documentos Oficiales en PDF:**
   - Generación de facturas de venta y órdenes de compra mediante `DomPDF`, incorporando el membrete institucional, NIT, desglose de IVA y pie de página de **Identidad Corporativa**.

5. **Seguridad y Control de Acceso (RBAC):**
   - Gestión de usuarios y roles sobre Spatie Laravel-Permission.
   - Modelo de **Rol Activo en sesión** (`EnsureActiveRoleSelected`) para usuarios con múltiples perfiles (ej. `supervisor@test.com` alternando entre `vendedor` y `almacenista`).
   - Soporte de permisos directos individuales (`ver-reportes`).
   - Matriz web interactiva de permisos y reglas de protección (no auto-desactivarse, proteger al último admin).

---

## 3. Entidades Principales del Sistema

- `users`: Usuarios y empleados con estado activo/inactivo.
- `roles` / `permissions`: Estructura de Spatie RBAC.
- `categories`: Clasificación comercial del catálogo.
- `products`: Catálogo con stock, costo de compra, precio de venta, tarifa de IVA, código de lote y fecha de vencimiento.
- `providers`: Distribuidores mayoristas con control de integridad referencial.
- `purchases`: Cabecera de órdenes de compra con estado (`pendiente`, `parcial`, `recibida`, `cancelada`).
- `purchase_details`: Detalle de compra con cantidad pedida, cantidad recibida y costo unitario.
- `owners`: Directorio de clientes con documento y datos de contacto.
- `owner_phones`: Teléfonos asociados a cada cliente.
- `sales`: Facturas de venta POS con subtotal, total de IVA diferencial y total.
- `sale_details`: Detalle de venta con cantidad, precio unitario y subtotal.
- `company_settings`: Registro de identidad corporativa (nombre, NIT, dirección, color, ícono, pie de página).
- `system_modules`: Catálogo de módulos con interruptores de activación y dependencias.
