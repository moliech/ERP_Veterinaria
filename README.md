# VetPets ERP - Sistema de Gestión Empresarial Veterinario

**VetPets ERP** es un Sistema de Planificación de Recursos Empresariales (ERP) desarrollado con **Laravel** y **MariaDB/MySQL**, diseñado para la gestión integral de Clínicas Veterinarias y Tiendas de Mascotas.

---

## 🏛️ Arquitectura y Módulos del Sistema

El sistema está estructurado bajo los 9 módulos core de la asignatura:

1. **Gestión de Usuarios y Permisos:** Control de acceso basado en roles (RBAC).
2. **Gestión de Clientes y Proveedores:** Expedientes de propietarios, pacientes y registro de proveedores.
3. **Catálogo de Productos y Servicios:** Gestión de medicamentos, productos minoristas y servicios médicos.
4. **Control de Inventario:** Trazabilidad por lotes y fechas de vencimiento.
5. **Ventas y Facturación (POS):** Punto de venta en mostrador y facturación de consultas.
6. **Compras y Recepción:** Registro de órdenes de compra, historial de reabastecimiento e insumos.
7. **Dashboard y KPIs:** Indicadores de rendimiento e ingresos.
8. **Reportes y Automatizaciones:** Alertas de existencias y vencimiento de medicamentos.
9. **Auditoría y Trazabilidad:** Logs de actividad del sistema.

---

## 📄 Documentación del Proyecto

* **[DIAGRAMA_MER.md](DIAGRAMA_MER.md)**: Modelo Entidad-Relación interactivo en **Mermaid**, especificando llaves, relaciones ($1:1$, $1:N$, $N:M$) e historial de compras.
* **[ANALISIS_EMPRESA.md](ANALISIS_EMPRESA.md)**: Documento de análisis de negocio, descripción de los 3 procesos clave del negocio y respuestas a preguntas de gestión.

---

## 🚀 Despliegue Local (`vetpets`)

```bash
cd vetpets
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```
