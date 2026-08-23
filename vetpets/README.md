# Aplicación Backend VetPets ERP (Laravel)

Este directorio contiene la aplicación base desarrollada en **Laravel** para **VetPets ERP** (Sistema de Gestión Empresarial para Clínica Veterinaria y Tienda de Mascotas).

---

## 🚀 Despliegue Local

1. **Instalar dependencias:**
   ```bash
   composer install
   ```

2. **Configurar el archivo de entorno `.env`:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Ejecutar el servidor local:**
   ```bash
   php artisan serve
   ```
   * Acceso local: **`http://127.0.0.1:8000`**

---

## 📁 Estructura del Código Fuente

* **`app/`**: Modelos Eloquent, Controladores y Lógica de Negocio del ERP.
* **`config/`**: Archivos de configuración de Laravel y conexión a MariaDB/MySQL.
* **`database/`**: Migraciones de tablas (`users`, `propietarios`, `mascotas`, `productos`, `ventas`, etc.) y Seeders.
* **`routes/web.php`**: Definición de rutas de la aplicación.
* **`resources/views/`**: Plantillas Blade para las interfaces del sistema.
