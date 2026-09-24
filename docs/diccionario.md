# Diccionario de Datos: VetPets ERP

Este documento contiene la especificación detallada del esquema de base de datos para el sistema **VetPets ERP**, correspondiente a las entidades activas en el primer corte.

---

## 1. Tabla: `categories` (Categorías de Productos y Servicios)
Almacena la clasificación general de los artículos del catálogo e insumos médicos.

| Campo | Tipo de Dato | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | `BIGINT (Unsigned)` | No | PK | Identificador único de la categoría. |
| `name` | `VARCHAR(100)` | No | - | Nombre de la categoría (ej. Farmacia, Alimentos, etc.). |
| `description` | `TEXT` | Sí | - | Descripción detallada de la categoría. |
| `active` | `BOOLEAN` | No | - | Estado lógico (true: activo, false: inactivo). Default: `true`. |
| `created_at` | `TIMESTAMP` | Sí | - | Fecha y hora de creación del registro. |
| `updated_at` | `TIMESTAMP` | Sí | - | Fecha y hora de última actualización del registro. |

---

## 2. Tabla: `products` (Catálogo de Productos e Insumos)
Almacena la información técnica, precios de venta y control de stock de los productos.

| Campo | Tipo de Dato | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | `BIGINT (Unsigned)` | No | PK | Identificador único del producto. |
| `category_id` | `BIGINT (Unsigned)` | No | FK | Identificador de la categoría a la que pertenece (`categories.id`). |
| `barcode` | `VARCHAR(50)` | Sí | Unique | Código de barras único para escaneo en punto de venta. |
| `name` | `VARCHAR(150)` | No | - | Nombre comercial del producto o servicio. |
| `sale_price` | `DECIMAL(10,2)` | No | - | Precio de venta al público en moneda local. |
| `current_stock` | `INT` | No | - | Existencias actuales en inventario. Default: `0`. |
| `minimum_stock` | `INT` | No | - | Umbral mínimo de stock para alertas de reabastecimiento. Default: `5`. |
| `active` | `BOOLEAN` | No | - | Estado lógico del producto. Default: `true`. |
| `created_at` | `TIMESTAMP` | Sí | - | Fecha y hora de creación del registro. |
| `updated_at` | `TIMESTAMP` | Sí | - | Fecha y hora de última actualización del registro. |

---

## 3. Tabla: `providers` (Proveedores de Insumos)
Almacena los datos de contacto y comerciales de las empresas suministradoras.

| Campo | Tipo de Dato | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | `BIGINT (Unsigned)` | No | PK | Identificador único del proveedor. |
| `nit` | `VARCHAR(20)` | No | Unique | Número de Identificación Tributaria o documento fiscal. |
| `company_name` | `VARCHAR(150)` | No | - | Razón social o nombre de la empresa proveedora. |
| `contact_name` | `VARCHAR(100)` | Sí | - | Nombre de la persona de contacto comercial. |
| `phone` | `VARCHAR(20)` | Sí | - | Teléfono principal de contacto. |
| `email` | `VARCHAR(100)` | Sí | - | Correo electrónico institucional del proveedor. |
| `address` | `VARCHAR(150)` | Sí | - | Dirección física de la sede o bodega del proveedor. |
| `active` | `BOOLEAN` | No | - | Estado del proveedor. Default: `true`. |
| `created_at` | `TIMESTAMP` | Sí | - | Fecha y hora de creación del registro. |
| `updated_at` | `TIMESTAMP` | Sí | - | Fecha y hora de última actualización del registro. |

---

## 4. Tabla: `owners` (Propietarios / Clientes)
Almacena la información de los clientes dueños de las mascotas atendidas.

| Campo | Tipo de Dato | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | `BIGINT (Unsigned)` | No | PK | Identificador único del propietario. |
| `document_type` | `VARCHAR(10)` | No | - | Tipo de documento (CC, CE, PAS, NIT). |
| `document_number` | `VARCHAR(20)` | No | Unique | Número de documento de identidad. |
| `first_name` | `VARCHAR(50)` | No | - | Nombres del propietario. |
| `last_name` | `VARCHAR(50)` | No | - | Apellidos del propietario. |
| `address` | `VARCHAR(150)` | Sí | - | Dirección de residencia. |
| `email` | `VARCHAR(100)` | Sí | - | Correo electrónico de contacto. |
| `created_at` | `TIMESTAMP` | Sí | - | Fecha y hora de creación del registro. |
| `updated_at` | `TIMESTAMP` | Sí | - | Fecha y hora de última actualización del registro. |

---

## 5. Tabla: `owner_phones` (Teléfonos de Propietarios)
Permite registrar múltiples números telefónicos de contacto por propietario ($1:N$).

| Campo | Tipo de Dato | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | `BIGINT (Unsigned)` | No | PK | Identificador único del registro telefónico. |
| `owner_id` | `BIGINT (Unsigned)` | No | FK | Propietario al que pertenece el número (`owners.id`). |
| `phone_number` | `VARCHAR(20)` | No | - | Número telefónico o celular de contacto. |
| `phone_type` | `VARCHAR(20)` | No | - | Clasificación del teléfono (Móvil, Fijo, Trabajo). |
| `created_at` | `TIMESTAMP` | Sí | - | Fecha y hora de creación del registro. |
| `updated_at` | `TIMESTAMP` | Sí | - | Fecha y hora de última actualización del registro. |

---

## 6. Tabla: `pets` (Mascotas Pacientes)
Almacena el perfil de las mascotas registradas vinculadas a su propietario ($1:N$).

| Campo | Tipo de Dato | Nulo | Clave | Descripción |
| :--- | :--- | :---: | :---: | :--- |
| `id` | `BIGINT (Unsigned)` | No | PK | Identificador único de la mascota. |
| `owner_id` | `BIGINT (Unsigned)` | No | FK | Propietario responsable de la mascota (`owners.id`). |
| `name` | `VARCHAR(50)` | No | - | Nombre de la mascota. |
| `species` | `VARCHAR(30)` | No | - | Especie animal (Canino, Felino, Ave, etc.). |
| `breed` | `VARCHAR(50)` | Sí | - | Raza de la mascota. |
| `birth_date` | `DATE` | Sí | - | Fecha estimada de nacimiento. |
| `weight` | `DECIMAL(5,2)` | Sí | - | Peso de la mascota registrado en kilogramos (Kg). |
| `created_at` | `TIMESTAMP` | Sí | - | Fecha y hora de creación del registro. |
| `updated_at` | `TIMESTAMP` | Sí | - | Fecha y hora de última actualización del registro. |