# VetPets ERP - Sistema de Gestión Empresarial Veterinario

**VetPets ERP** es una plataforma web desarrollada en **Laravel 11+**, diseñada para la administración integral de Clínicas Veterinarias y Tiendas de Mascotas (PetShop).

**Integrantes:** Jhon Esteban Molina Echavarria - Heiber Lozano Mercado  
**Asignatura:** Software de Gestión Empresarial (COTECNOVA)

---

## 🛠️ Estado del Proyecto (Avanzado hasta Clase 5)

El sistema cuenta con la arquitectura de base de datos traducida a **Migraciones**, **Modelos Eloquent** con asignación masiva (`$fillable`) y relaciones de entidad, junto con **Seeders** funcionales de prueba.

### 🗄️ Módulos de Base de Datos Implementados

| Módulo | Entidad / Tabla | Modelo Eloquent | Relación | Descripión |
| :--- | :--- | :--- | :---: | :--- |
| **Catálogo** | `categories` | `Category` | $1:N$ a `Product` | Clasificación de servicios médicos y productos. |
| **Productos** | `products` | `Product` | $N:1$ a `Category` | Catálogo con precios, stock actual y stock mínimo. |
| **Proveedores** | `providers` | `Provider` | N/A | Registro de laboratorios e insumos médicos. |
| **Clientes** | `owners` | `Owner` | $1:N$ a `Pet`, `OwnerPhone` | Expedientes de propietarios de mascotas. |
| **Teléfonos** | `owner_phones` | `OwnerPhone` | $N:1$ a `Owner` | Contactos telefónicos de los propietarios. |
| **Pacientes** | `pets` | `Pet` | $N:1$ a `Owner` | Historias biológicas de las mascotas atendidas. |
| **Usuarios** | `users` | `User` | N/A | Usuarios con autenticación Laravel Breeze. |

---

## 🔑 Credenciales de Acceso Local

* **Servidor Local:** **`http://localhost:8060`**
* **Página de Login:** **`http://localhost:8060/login`**

| Rol / Usuario | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@vetpets.com` | `password123` |
| **Usuario Esteban** | `esteban@vetpets.com` | `password123` |

---

## 📸 Vista Previa del Sistema

| Landing Page (Página de Inicio) | Login Personalizado |
|---|---|
| ![Landing Page](docs/visual/captura_1_landing.png) | ![Login](docs/visual/captura_2_login.png) |

| Registro de Usuarios | Panel de Control (Dashboard con KPIs) |
|---|---|
| ![Registro](docs/visual/captura_3_registro.png) | ![Dashboard](docs/visual/captura_4_dashboard.png) |

---

## 🚀 Despliegue e Inicialización Local (Docker Sail)

Para poner a punto el proyecto desde cero en una máquina local:

```bash
# 1. Clonar el repositorio
git clone https://github.com/moliech/ERP_Veterinaria.git
cd ERP_Veterinaria

# 2. Instalar dependencias de PHP y entorno
composer install
cp .env.example .env
php artisan key:generate

# 3. Levantar contenedores Docker Sail
./vendor/bin/sail up -d

# 4. Ejecutar migraciones y sembrado de datos (Clase 5)
./vendor/bin/sail php artisan migrate:fresh --seed

# 5. Compilar assets de frontend (Vite / Tailwind)
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

## 🏛️ Estructura del Repositorio

```text
ERP_Veterinaria/
├── app/                       # Modelos Eloquent (Category, Product, Provider, Owner, Pet, OwnerPhone)
├── config/                    # Configuración de Laravel
├── database/                  # Migraciones (0001..0006) y Seeders de la Base de Datos
│   ├── migrations/            # Tablas categories, products, providers, owners, owner_phones, pets
│   └── seeders/               # CategorySeeder, ProductSeeder, ProviderSeeder, OwnerSeeder, PetSeeder, UserSeeder
├── docs/                      # Documentación Técnica e Investigación
│   ├── analisis.md            # Documento de Análisis del Negocio
│   ├── diagrama_mer.md        # Modelo Entidad-Relación (MER)
│   └── visual/                # Capturas de pantalla de la Interfaz
├── public/                    # Archivos públicos y manifest compilado por Vite
├── resources/                 # Vistas Blade, estilos Tailwind CSS y JavaScript
├── routes/                    # Rutas web y autenticación (web.php, auth.php)
├── storage/                   # Almacenamiento local y logs de la aplicación
├── compose.yaml               # Configuración de Docker Sail (Puerto 8060 & MySQL 3306)
├── composer.json              # Dependencias de PHP / Laravel
├── package.json               # Dependencias de Node.js / Vite / Tailwind
└── README.md                  # Documentación principal del repositorio
```

---

## 📄 Documentación Técnica

* 📋 **[Análisis del Negocio](docs/analisis.md)**: Descripción del modelo de negocio, procesos clave y preguntas de gestión.
* 🗂️ **[Diagrama Entidad-Relación](docs/diagrama_mer.md)**: Especificación de entidades, llaves primarias/foráneas y relaciones ($1:1$, $1:N$, $N:M$).

---

## 🎨 Identidad Gráfica y Tecnologías

- **Paleta de Colores:** Verde Esmeralda Corporativo (`#059669`), Slate Dark (`#0f172a`) y Grises Neutros (`#f8fafc`).
- **Librerías:** Laravel Breeze, Tailwind CSS, Font Awesome 6.5 (Iconografía Veterinaria), Alpine.js.
- **Entorno:** PHP 8.x, MySQL/MariaDB 8.4, Docker Sail, Node.js 22 LTS, Vite 6.
