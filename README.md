# VetPets ERP — Sistema de Gestión Empresarial para Tienda de Mascotas (PetShop)

**VetPets ERP** es una aplicación web desarrollada con **Laravel 11, MySQL 8.4 y Docker Sail**, diseñada para la administración comercial de tiendas de mascotas. El sistema integra el ciclo comercial completo: catálogo, inventario y control de lotes con fechas de vencimiento, módulo de tarifas comerciales con IVA diferencial (0 %, 5 % y 19 %), proveedores, órdenes de compra con recepción parcial o total, facturación POS, documentos PDF, dashboard gerencial y un esquema de seguridad con rol activo en sesión.

* **Integrantes:** Jhon Esteban Molina Echavarria · Heiber Lozano Mercado
* **Asignatura:** Software de Gestión Empresarial (COTECNOVA - 2026)
* **Docente:** Ing. Jhon James Cano Sánchez
* **Etiqueta (Tag) de Entrega:** `avance-2`
* **Video de Sustentación:** [Ver Video en Google Drive](https://drive.google.com/drive/folders/154DKFAKOyDlauuwDyoacq1mFkrTdBTYA?usp=sharing)

---

## 🚀 Requisitos e Instalación Local (Paso a Paso)

Para levantar el proyecto en un entorno local con **Docker Sail**:

### 1. Clonar el repositorio
```bash
git clone https://github.com/moliech/ERP_Veterinaria.git
cd ERP_Veterinaria
```

### 2. Configurar variables de entorno
```bash
cp .env.example .env
```
*(El archivo `.env.example` ya viene configurado para los puertos de Docker Sail: web en el puerto `8060` y MySQL en el puerto `3307->3306`).*

### 3. Instalar dependencias e iniciar Docker Sail
```bash
composer install
./vendor/bin/sail up -d
```

### 4. Generar clave, ejecutar migraciones y seeders
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

El sistema quedará disponible en: **`http://localhost:8060`**

---

## 🔑 Usuarios de Prueba por Rol

El sistema cuenta con usuarios semilla listos para probar cada perfil:

| Usuario / Correo | Contraseña | Rol Principal | Características / Permisos |
| :--- | :--- | :---: | :--- |
| **`admin@test.com`** | `password` | **Administrador (`admin`)** | Acceso a todos los módulos: inventario, tarifas, clientes, ventas, proveedores, compras, dashboard, matriz de permisos, identidad corporativa y gestión de módulos. |
| **`almacenista@test.com`** | `password` | **Almacenista (`almacenista`)** | Gestión de bodega: categorías, inventario y lotes, proveedores y recepción de órdenes de compra. Sin acceso a ventas POS. |
| **`vendedor@test.com`** | `password` | **Vendedor (`vendedor`)** | Operación de mostrador: consulta de productos, gestión de clientes y facturación en caja POS. Sin acceso a compras ni proveedores. |
| **`supervisor@test.com`** | `password` | **Múltiples Roles (`supervisor`)** | **Demostración de Rol Activo:** Posee los roles `vendedor` y `almacenista`. Al iniciar sesión elige con cuál rol operar y puede conmutarlo desde el menú. Además, cuenta con el permiso directo `ver-reportes` para consultar el Dashboard en cualquier rol. |

---

## 📦 Módulos del Sistema (Segundo Corte)

| # | Módulo | Ruta | Descripción |
| :-: | :--- | :--- | :--- |
| **1** | **Categorías** | `/categories` | Clasificación de familias comerciales del catálogo (alimentos, snacks, accesorios, higiene, medicamentos). |
| **2** | **Inventario y Lotes** | `/products` | Control de stock físico, costo de compra, código de lote y semáforo de vencimiento (*Lote vigente*, *Próximo a vencer*, *Lote vencido*, *No perecedero*). |
| **3** | **Tarifas e IVA** | `/tarifas` | Fijación del precio base de venta, cálculo automático del margen de utilidad (%) y asignación de IVA diferencial (**0 % Exento**, **5 % Alimentos/Insumos**, **19 % General**). |
| **4** | **Clientes** | `/clients` | Directorio de clientes con documento, dirección, correo y múltiples teléfonos. |
| **5** | **Ventas POS** | `/sales` | Facturación en mostrador con liquidación de IVA por producto, bloqueo de lotes vencidos, descuento de stock y anulación con devolución. |
| **6** | **Proveedores** | `/providers` | Catálogo de distribuidores y marcas mayoristas con NIT, teléfono y contacto. |
| **7** | **Órdenes de Compra** | `/purchases` | Gestión de pedidos a proveedores con recepción parcial o total que suma stock y actualiza el costo de compra en bodega. |
| **8** | **Documentos PDF** | `/sales/{id}/pdf`<br>`/purchases/{id}/pdf` | Generación de facturas de venta y órdenes de compra en PDF con encabezado dinámico y desglose fiscal. |
| **9** | **Dashboard** | `/dashboard` | Indicadores mensuales (ventas, compras, balance), los cinco productos más vendidos, alertas de reabastecimiento y lotes por vencer. |
| **10** | **Seguridad y Accesos** | `/configuracion` | Gestión de usuarios, roles, matriz interactiva de permisos y selección de rol activo en sesión. |
| **11** | **Identidad Corporativa** | `/configuracion/identidad-corporativa` | Personalización del nombre de la empresa, NIT, teléfono, paleta de colores y pie de página de facturas. |
| **12** | **Gestión de Módulos** | `/configuracion/modulos` | Activación o desactivación de componentes del sistema con validación de dependencias. |

---

## 📸 Evidencias de Funcionamiento (Segundo Corte)

Las capturas de pantalla que respaldan la integración del segundo corte se encuentran en la carpeta **`docs/avance2/`**:

1. **`figura1.png` — Recepción Parcial de Compra y Aumento de Stock en MySQL:** Detalle de la orden de compra con recepción parcial de 6 sobre 10 unidades y consulta SQL evidenciando el incremento de `current_stock` y actualización de `cost_price`.
2. **`figura2.png` — Configuración de Tarifas e IVA Diferencial (`/tarifas`):** Módulo de precios de venta con cálculo de margen de utilidad en vivo y tarifas del 0 %, 5 % y 19 %.
3. **`figura3.png` — Venta POS con IVA Diferencial y Descuento en MySQL:** Facturación combinando productos con diferente tasa de IVA y consulta en MySQL demostrando la deducción de unidades.
4. **`figura4A.png` y `figura4B.png` — Reglas de Negocio:** Alerta de stock insuficiente al vender y comprobante de factura `anulada` con devolución de existencias.
5. **`figura5.png` — Factura de Venta Generada en PDF:** Documento PDF generado con `DomPDF` con los datos de Identidad Corporativa y discriminación de impuestos.
6. **`figura6A.png` y `figura6B.png` — Seguridad y Rol Activo:** Pantalla de selección de rol activo para `supervisor@test.com` y respuesta `403 Forbidden` al intentar entrar a un módulo no autorizado.
7. **`figura7.png` — Dashboard Principal:** Indicadores mensuales de ventas y compras, los cinco productos más vendidos y productos para reabastecimiento.

---

## 🧪 Pruebas Automatizadas

El proyecto cuenta con una suite de pruebas en PHPUnit cubriendo autenticación, roles, rol activo, permisos, reglas transaccionales, tarifas e IVA:

```bash
./vendor/bin/sail test
```
*Resultado:* **30 tests passing (122 assertions)**
