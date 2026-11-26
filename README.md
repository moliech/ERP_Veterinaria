# VetPets ERP — Sistema de Gestión Empresarial Modular y Parametrizable (PetShop)

[![Pruebas del ERP](https://github.com/moliech/ERP_Veterinaria/actions/workflows/pruebas.yml/badge.svg)](https://github.com/moliech/ERP_Veterinaria/actions/workflows/pruebas.yml)
![Laravel](https://img.shields.io/badge/Laravel-11+-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker Sail](https://img.shields.io/badge/Docker-Sail-2496ED?style=flat&logo=docker&logoColor=white)
![Pint](https://img.shields.io/badge/Style-Pint%20PASS-success?style=flat)
![Tests](https://img.shields.io/badge/PHPUnit-42%20passed%20(284%20assertions)-brightgreen?style=flat)

**VetPets ERP** es un sistema de planificación de recursos empresariales (ERP) desarrollado en **Laravel 11+, PHP 8.4, MySQL 8.4 y Docker Sail** para la gestión comercial de tiendas minoristas de mascotas (*VetPets Comercial S.A.S.*). La solución integra **15 subsistemas funcionales** que cubren los **17 requisitos oficiales de la asignatura**: entidades maestras con `SoftDeletes`, órdenes de compra con recepción parcial o total, **Kardex permanente transaccional**, módulo de **Tarifas e IVA diferencial (0 %, 5 % y 19 %)**, facturación en **Punto de Venta (POS)**, **3 documentos PDF con Marca Blanca**, **Dashboard con 7 KPIs y tablas accionables**, seguridad **RBAC de 42 permisos con Rol Activo en sesión**, **auditoría de 6 modelos y bitácora de seguridad**, **API RESTful v1 con Sanctum (7 endpoints)**, **importación/exportación Excel v4**, **automatización asíncrona (eventos, colas, Mailpit y Schedule)** e integración continua en **GitHub Actions**.

* **Integrantes:** Jhon Esteban Molina Echavarría (`esteban`) · Heiber Lozano Mercado (`heiber`)
* **Asignatura:** Software de Gestión Empresarial (COTECNOVA — 2026)
* **Docente:** Ing. Jhon James Cano Sánchez
* **Entrega Final:** 25 de noviembre de 2026 (Etiqueta Git: **`final`**)
* **Sustentación Presencial:** 26 de noviembre de 2026 (8:15 p. m.)
* **Informe Final Oficial (PDF — Normas APA 7):** [Informe_Final_ERP_VetPets_Molina_Lozano.pdf](docs/final/Informe_Final_ERP_VetPets_Molina_Lozano.pdf)
* **Colección Postman API REST v1:** [ERP_API.postman_collection.json](ERP_API.postman_collection.json)

---

## 📚 Documentación Oficial e Índice de Entregas

### 📑 Entrega Final (`tag: final`)
* 📘 **[Informe Final PDF — APA 7 (52 págs., 35 sin anexos)](docs/final/Informe_Final_ERP_VetPets_Molina_Lozano.pdf)**: Documento maestro completo con los 10 capítulos, 16 tablas, 13 figuras, 18 referencias APA 7 y Anexos A–E.
* 📝 **[Informe Final Markdown (Fuente Maestra)](docs/final/Informe_Final_ERP_VetPets_Molina_Lozano.md)**: Versión fuente en Markdown sincronizada con el PDF.
* 🔌 **[Colección Postman API REST v1 (`ERP_API.postman_collection.json`)](ERP_API.postman_collection.json)**: Colección lista para importar y probar los 7 endpoints con token Bearer Sanctum y Rol Activo.

### 📅 Histórico de Entregas Anteriores
* **[Avance 2 — Segundo Corte (`tag: avance-2`)](docs/avance2/Avance2_VetPets_Molina_Lozano.md)**: Informe y capturas del segundo corte.
* **[Clase 10 — Compras, Proveedores y PDF](docs/clase_10/README.md)** · **[Análisis del Negocio](docs/analisis.md)** · **[Diccionario de Datos](docs/diccionario.md)** · **[MER Mermaid](docs/diagrama_mer.md)**.

---

## 🛠️ Prerrequisitos de Entorno y Mapeo de Puertos

Antes de desplegar el proyecto, verificar que los siguientes puertos estén libres en el equipo anfitrión:

| Servicio | Puerto Host | Puerto Contenedor | Propósito y Acceso |
| :--- | :---: | :---: | :--- |
| **Aplicación Web ERP** | **`8060`** | `80` | Interfaz web principal: **`http://localhost:8060`** |
| **Buzón de Correos (Mailpit)** | **`8025`** | `8025` | Interfaz web para correos y PDFs adjuntos: **`http://localhost:8025`** |
| **Servidor SMTP (Mailpit)** | **`1025`** | `1025` | Puerto SMTP interno para notificaciones en cola |
| **Base de Datos MySQL 8.4** | **`3307`** | `3306` | Motor relacional InnoDB (mapeado a `3307` para evitar conflictos locales) |

* **Requisitos:** Docker Desktop (con WSL 2 en Windows) o Docker Engine + Docker Compose en Linux/macOS, y Git.

---

## 🚀 Despliegue desde Cero (Clon Limpio con Docker Sail)

```bash
# 1. Clonar el repositorio y ubicarse en la etiqueta oficial de entrega
git clone https://github.com/moliech/ERP_Veterinaria.git
cd ERP_Veterinaria
git checkout final

# 2. Crear archivo de entorno (ya preconfigurado con puertos 8060, 3307 y Mailpit 1025)
cp .env.example .env

# 3. Instalar dependencias PHP (Elegir Opción A o B):

# Opción A (Con Composer instalado en el equipo anfitrión):
composer install

# Opción B (Sin PHP/Composer local — contenedor temporal oficial de Laravel Sail):
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# 4. Levantar los contenedores de Sail (Web, MySQL 8.4 y Mailpit)
./vendor/bin/sail up -d

# 5. Generar llave de aplicación, ejecutar migraciones con seeders y compilar frontend
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

## 🌐 URLs y Servicios Activos

* 🖥️ **ERP Web (Inicio de Sesión):** **`http://localhost:8060`**
* 📬 **Buzón de Correos y Reportes (Mailpit):** **`http://localhost:8025`**
* 🔌 **API RESTful v1 Base URL:** **`http://localhost:8060/api/v1`**

---

## 🧪 Comandos de Verificación y Demostración en Consola

```bash
# 1. Verificar estándar de estilo PSR-12 (Laravel Pint — 170 archivos, PASS)
./vendor/bin/sail pint --test

# 2. Ejecutar las 2 pruebas con traza narrativa en consola (Compra/Kardex y Venta API con IVA 19%)
./vendor/bin/sail test --filter=FlujoComercialKardexTest

# 3. Ejecutar la suite completa de pruebas automatizadas (42 passed, 284 assertions en 14 clases)
./vendor/bin/sail test

# 4. Ejecutar el comando programado de resumen diario y verificar llegada en Mailpit (:8025)
./vendor/bin/sail artisan erp:resumen-diario
```

---

## 🔑 Credenciales y Usuarios de Prueba por Rol (`DatabaseSeeder`)

| Perfil / Puesto de Trabajo | Correo Electrónico | Contraseña | Roles Base (Spatie) | Permiso Directo | Estado | Propósito de Demostración |
| :--- | :--- | :---: | :---: | :---: | :---: | :--- |
| **Administrador General** | `admin@test.com` | `password` | `admin` (42 permisos) | — | Activo | Control total: Configuración, Matriz RBAC, Identidad Corporativa, Módulos y Auditoría. |
| **Almacenista de Bodega** | `almacenista@test.com` | `password` | `almacenista` | — | Activo | Catálogo, Lotes, Excel, Kardex, Proveedores y Compras con recepción (bloqueo `403` en Ventas). |
| **Vendedor de Mostrador** | `vendedor@test.com` | `password` | `vendedor` | — | Activo | Clientes, consulta de catálogo y Ventas POS con IVA diferencial (bloqueo `403` en Compras). |
| **Supervisor Multi-Rol** | `supervisor@test.com` | `password` | `vendedor`, `almacenista` | `ver-reportes` | Activo | **Demostración de Rol Activo:** Elige rol en `/rol-activo` (y en `/api/v1/login`) conservando acceso a reportes. |
| **Usuario Ex-Empleado** | `inactivo@test.com` | `password` | `vendedor` | — | **Inactivo** | **Demostración de Bloqueo (`RN-13`):** Rechazado en login web y API; sesión expulsada si se desactiva. |

---

## 🗄️ Subsistemas Funcionales de la Entrega Final (15 Subsistemas)

| # | Subsistema / Módulo | Tablas Principales | Controlador / Servicio Clave | Características Principales |
| :---: | :--- | :--- | :--- | :--- |
| **1** | **Categorías Comerciales** | `categories` | `CategoryController` | CRUD completo, `FormRequest`, `SoftDeletes`, papelera y restauración. |
| **2** | **Inventario y Lotes** | `products` | `ProductController` | Control de stock, código de lote, semáforo de vencimiento y `SoftDeletes`. |
| **3** | **Tarifas e IVA Diferencial** | `products` | `PricingController` | Precio base, margen bruto sobre último costo e IVA (**0 %**, **5 %**, **19 %**). |
| **4** | **Directorio de Clientes** | `owners`, `owner_phones` | `ClientController` | CRUD completo, teléfonos múltiples (`1:N`), papelera y restauración. |
| **5** | **Proveedores Mayoristas** | `providers` | `ProviderController` | CRUD con NIT único, contacto comercial y borrado lógico. |
| **6** | **Compras y Recepción Parcial** | `purchases`, `purchase_details` | `PurchaseService` | Órdenes `pendiente` → `parcial` → `recibida` / `anulada`, actualización de costo y entrada en Kardex. |
| **7** | **Punto de Venta (POS)** | `sales`, `sale_details` | `SaleService` | `DB::transaction`, bloqueo por stock/lote vencido, IVA diferencial y anulación con sello `ANULADA`. |
| **8** | **Documentos PDF (Marca Blanca)** | `company_settings` | `DomPDF` (`pdf/*`) | 3 PDFs parametrizables: Factura de venta, Orden de compra y Kardex horizontal por fechas. |
| **9** | **Dashboard y Analítica** | Consultas agregadas | `DashboardController` | **7 KPIs** filtrados por permiso + tablas accionables de reabastecimiento y caducidad ($\le 60$ días). |
| **10** | **Seguridad RBAC y Rol Activo** | `users`, `roles`, `permissions` | `AccessControlService` | **42 permisos** (32 operativos + 10 administrativos), Rol Activo, permisos directos y clonación de perfiles. |
| **11** | **Identidad Corporativa y Módulos** | `company_settings`, `system_modules` | `CompanySettingController`, `ThemeContrastService` | Marca Blanca con validación **WCAG 2.1 AA** y gestor modular con dependencias protegidas. |
| **12** | **Kardex Permanente** | `inventory_movements` | `InventoryService` | Bloqueo pesimista (`lockForUpdate`), entradas, salidas, anulaciones, ajustes auditados y `kardex:inicializar`. |
| **13** | **Auditoría y Bitácora** | `activity_log` | `AuditController` | `LogsActivity` en **6 modelos** + canal `activity('seguridad')` con *Visual Diffs*. |
| **14** | **API REST v1 y Excel v4** | `personal_access_tokens` | `Api\*Controller`, `ExcelController`, `ProductsImport` | **7 endpoints Sanctum** con habilidad `rol:<nombre>` + exportación/importación Excel validada por fila. |
| **15** | **Automatización y CI/CD** | `notifications`, `jobs` | `SaleRegistered`, `PurchaseReceived`, `SendDailySummary` | Colas asíncronas, Mailpit con PDF adjunto, campana UI, Schedule diario (19:00) y GitHub Actions. |

---

## 🏛️ Estructura del Repositorio

```text
ERP_Veterinaria/
├── app/
│   ├── Console/Commands/      # SendDailySummary (erp:resumen-diario), InitializeKardex
│   ├── Events/ & Listeners/   # SaleRegistered/CheckLowStock, PurchaseReceived/NotifyPurchaseReceived
│   ├── Exports/ & Imports/    # ProductsExport, SalesExport, ProductsTemplateExport, ProductsImport
│   ├── Http/Controllers/      # Controladores Web, Settings/* y Api/* (v1)
│   ├── Http/Middleware/       # EnsureActiveRoleSelected, EnsureModuleEnabled, EnsureUserIsActive
│   ├── Http/Requests/         # FormRequests de validación por módulo
│   ├── Models/                # 11 modelos de dominio con SoftDeletes y LogsActivity
│   ├── Notifications/         # LowStockNotification, PurchaseReceivedNotification, DailySummaryNotification
│   └── Services/              # SaleService, PurchaseService, InventoryService, AccessControlService, ThemeContrastService
├── database/
│   ├── migrations/            # 23 migraciones (30 tablas físicas en MySQL 8.4)
│   └── seeders/               # 8 seeders encadenados (42 permisos, 3 roles, 5 usuarios, transacciones y Kardex)
├── docs/
│   ├── final/                 # Informe Final PDF/MD (APA 7), diagramas verticales y capturas actualizadas
│   ├── avance2/               # Documentación histórica del segundo corte (tag avance-2)
│   └── clase_10/              # Evidencias históricas de la Clase 10
├── tests/Feature/             # 14 clases de prueba (42 pruebas, 284 aserciones) incluyendo SmokeRoutesTest
├── .github/workflows/         # pruebas.yml (CI con PHP 8.4, MySQL 8.4, Pint y PHPUnit)
├── ERP_API.postman_collection.json
└── compose.yaml               # Docker Sail (Web 8060, MySQL 3307, Mailpit 8025/1025)
```

---

## 🧪 Pruebas Automatizadas y Calidad de Código

La suite de pruebas se ejecuta contra **MySQL 8.4** tanto en local (`./vendor/bin/sail test`) como en **GitHub Actions**:

```bash
./vendor/bin/sail pint --test   # PASS (170 files)
./vendor/bin/sail test          # Tests: 42 passed (284 assertions) — 14 clases de prueba
```

| Suite / Archivo | Clases | Pruebas | Aserciones | Resultado |
| :--- | :---: | :---: | :---: | :---: |
| `Unit/ExampleTest` y `Feature/ExampleTest` | 2 | 2 | 3 | **PASS** |
| `Feature/Auth/*.php` y `ProfileTest.php` | 7 | 23 | 59 | **PASS** |
| `Feature/SecurityConfigTest.php` | 1 | 5 | 60 | **PASS** |
| `Feature/ApiRestTest.php` | 1 | 5 | 106 | **PASS** |
| `Feature/Clase14Test.php` | 1 | 4 | 11 | **PASS** |
| `Feature/FlujoComercialKardexTest.php` | 1 | 2 | 6 | **PASS** |
| `Feature/SmokeRoutesTest.php` | 1 | 1 | 39 | **PASS** |
| **Total Verificado** | **14** | **42** | **284** | **42 PASS** |
