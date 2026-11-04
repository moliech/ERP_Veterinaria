# Clase 10 — Módulo de Compras, Proveedores y Reportes PDF

**Asignatura:** Software de Gestión Empresarial (COTECNOVA - 2026)  
**Seminario:** Desarrollo de Aplicaciones Web en Laravel con Enfoque RAD  
**Integrantes:** Jhon Esteban Molina Echavarria · Heiber Lozano Mercado  
**Proyecto:** VetPets ERP (PetShop & Retail)

---

## 📌 1. Resumen de la Sesión y Objetivos

En esta sesión se completó el ciclo de abastecimiento e inventario del ERP, articulando los procesos de compra a distribuidores mayoristas con el control físico de bodega y la emisión de documentos tributarios.

### Componentes Construidos:
1. **Directorio de Proveedores:** Gestión de empresas proveedoras (`providers`) con NIT único, persona de contacto, dirección y soporte de borrado lógico (`SoftDeletes`).
2. **Órdenes de Compra:** Cabecera secuencial (`purchases`, ej. `OC-00001`) y líneas de detalle (`purchase_details`) con cantidades pedidas (`quantity_ordered`) y recibidas (`quantity_received`).
3. **Servicio Transaccional (`PurchaseService`):** Lógica desacoplada en `app/Services/PurchaseService.php` ejecutada bajo `DB::transaction`, asegurando atomicidad al recibir pedidos o cancelar órdenes.
4. **Reportes y Facturas en PDF:** Integración de la biblioteca `barryvdh/laravel-dompdf` para la descarga formal de órdenes de compra y facturas con encabezado de identidad corporativa.

---

## 🗄️ 2. Modelo de Datos de Abastecimiento

| Tabla | Propósito | Campos Clave y Restricciones |
| :--- | :--- | :--- |
| **`providers`** | Directorio de proveedores mayoristas | `id`, `nit` (UK), `company_name`, `contact_person`, `phone`, `email`, `active`, `deleted_at`. |
| **`purchases`** | Cabecera de la orden de compra | `id`, `purchase_number` (UK), `provider_id` (FK), `user_id` (FK), `purchase_date`, `subtotal`, `tax`, `total`, `status` (`pendiente`, `parcial`, `recibida`, `cancelada`), `deleted_at`. |
| **`purchase_details`** | Ítems de la orden de compra | `id`, `purchase_id` (FK), `product_id` (FK), `quantity_ordered`, `quantity_received`, `unit_cost`, `subtotal`. |

---

## ⚙️ 3. Reglas de Negocio Implementadas

1. **La orden no suma stock al crearse:** Una orden nace en estado `pendiente` con `quantity_received = 0`. El inventario en bodega no se incrementa hasta que se registre la recepción física.
2. **Recepción Parcial o Total:** El servicio suma al `current_stock` únicamente las unidades entregadas y actualiza el costo de adquisición (`cost_price`) del producto en la tabla `products`.
3. **Control de Sobrerecepción:** Se impide registrar una recepción mayor a las unidades pendientes por entregar (`quantity_ordered - quantity_received`), arrojando excepción de validación.
4. **Protección de Integridad:** No se permite eliminar proveedores que cuenten con compras registradas en el histórico.

---

## 🛡️ 4. Permisos y Roles Asignados

Sobre `spatie/laravel-permission` se incorporaron los permisos del módulo de abastecimiento:
* **Administrador (`admin`):** Acceso total (`ver-proveedores`, `crear-proveedores`, `editar-proveedores`, `eliminar-proveedores`, `ver-compras`, `crear-compras`, `recibir-compras`, `anular-compras`).
* **Almacenista (`almacenista`):** Acceso a bodega y compras (`ver-proveedores`, `crear-proveedores`, `ver-compras`, `crear-compras`, `recibir-compras`). Restringido para eliminar proveedores o anular compras ya recibidas.
* **Vendedor (`vendedor`):** Sin acceso a compras ni proveedores (módulo oculto en menú y bloqueado por middleware con error `403 Forbidden`).

---

## 📸 5. Evidencias Técnicas de Funcionamiento

### Captura 1. Directorio de Proveedores (`/providers`)
Listado de distribuidores mayoristas con sus datos de contacto y estado activo:
![Captura 1 - Listado de Proveedores](capturas/Captura%201_Listado%20de%20Proveedores.png)

---

### Captura 2. Formulario de Nueva Orden de Compra (`/purchases/create`)
Selección de proveedor, carga dinámica de productos y cálculo automático de totales:
![Captura 2 - Formulario de Orden de Compra](capturas/Captura%202_Formulario%20de%20Orden%20de%20Compra.png)

---

### Captura 3. Detalle de Orden en Estado Parcial
Orden `OC-00001` con 10 unidades pedidas y recepción parcial de 6 unidades, dejando 4 pendientes:
![Captura 3 - Detalle Orden Parcial](capturas/Captura%203_Detalle%20Orden%20Parcial.png)

---

### Captura 4. Control de Excepciones y Validación de Stock
Validación del sistema bloqueando operaciones fuera de los límites de existencias o pedidos:
![Captura 4 - Validacion Error](capturas/Captura%204_Validacion%20Error%20de%20Recepcion.png)

---

### Captura 5. Verificación de Stock Aumentado en MySQL
Consulta en consola MySQL que demuestra el incremento de unidades tras la recepción:
![Captura 5 - MySQL Stock Aumentado](capturas/Captura%205_MySQL%20Stock%20Aumentado.png)

---

### Captura 6. Generación de Documentos Formales en PDF (`DomPDF`)
Documento descargable con encabezado institucional, número secuencial, discriminación de lotes e IVA:
![Captura 6 - Reportes PDF Generados](capturas/Captura%206_Reportes%20PDF%20Generados.png)

---

### Captura 7. Dashboard Gerencial con KPIs de Compras
Panel de control con el balance de operaciones mensuales y compras acumuladas del mes:
![Captura 7 - Dashboard KPIs de Compras](capturas/Captura%207_Dashboard%20KPIs%20de%20Compras.png)
