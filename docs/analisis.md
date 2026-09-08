# Análisis de Negocio: VetPets ERP (Clínica Veterinaria y Tienda de Mascotas)

## 1. Datos Generales de la Empresa

* **Nombre del Sistema:** `VetPets ERP`
* **Contexto Corporativo:** Sistema de Planificación de Recursos Empresariales (ERP) diseñado para la administración integral de Clínicas Veterinarias y Tiendas de Mascotas.
* **Modelo de Negocio:** Híbrido, combinando la prestación de servicios profesionales de salud/estética veterinaria con la comercialización al por menor de medicamentos, alimentos y accesorios.
* **Tipología de Clientes:** Personas naturales (propietarios de mascotas) y clientes corporativos o fundaciones de adopción.

---

## 2. Descripción de Procesos Clave del Negocio

El sistema automatiza e integra los siguientes tres procesos principales:

### A. Atención Médica
1. **Recepción y Agendamiento:** Se registra el ingreso del paciente (mascota) asignado a su propietario y se programa la cita médica.
2. **Consulta Clínica y Diagnóstico:** El médico veterinario atiende la consulta, registra los hallazgos en la historia clínica y prescribe medicamentos o insumos requeridos.
3. **Formulación y Liquidación:** Los insumos y medicamentos prescritos se consolidan junto a la tarifa de la consulta y se derivan al módulo de caja para su liquidación final.

### B. Ventas en Tienda (POS - Punto de Venta)
1. **Selección de Productos:** El cliente selecciona artículos del catálogo minorista (alimentos, juguetes, insumos).
2. **Facturación en Mostrador:** El cajero procesa los productos escaneando su código de barras y registra la forma de pago.
3. **Descuento de Stock:** Al confirmar la venta, el sistema descuenta automáticamente las unidades vendidas de las existencias del inventario en tiempo real.

### C. Compras a Proveedores e Historial de Abastecimiento
1. **Generación de la Orden de Compra:** Se crea el registro de compra asociando el proveedor, fecha de compra y número de factura entregado por el proveedor.
2. **Recepción de Mercancía y Detalle:** Se registran los productos específicos recibidos, precios de costo unitarios, subtotales y cantidades compradas.
3. **Control de Lotes y Vencimiento:** Al recibir medicamentos, el sistema vincula el detalle de la compra con la tabla de lotes registrando el número de lote y la fecha de expiración antes de habilitar el stock.

---

## 3. Estructura de Entidades y Base de Datos (Modelo Inicial)

Listado de entidades principales que conforman el modelo de base de datos del sistema:

1. **`Usuarios`**: Registra al personal de la empresa (administradores, veterinarios, cajeros).
   * *Campos:* `id`, `rol_id`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `email`, `password`.
2. **`Roles`**: Nivel de acceso y permisos del personal.
   * *Campos:* `id`, `nombre_rol`, `descripcion`.
3. **`Propietarios`**: Información de los clientes dueños de las mascotas.
   * *Campos:* `id`, `tipo_documento`, `numero_documento`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `direccion`, `email`.
4. **`Telefonos_Propietario`**: Registra uno o varios números telefónicos de contacto por propietario.
   * *Campos:* `id`, `propietario_id`, `numero_telefono`, `tipo_telefono`.
5. **`Mascotas_Pacientes`**: Información biológica y clínica de cada mascota atendida.
   * *Campos:* `id`, `propietario_id`, `nombre_mascota`, `especie`, `raza`, `fecha_nacimiento`, `peso`.
6. **`Categorias`**: Clasificación de productos y servicios (Farmacia, Alimentos, Accesorios, Consultas).
   * *Campos:* `id`, `nombre_categoria`, `descripcion`.
7. **`Productos`**: Catálogo general de artículos e insumos.
   * *Campos:* `id`, `categoria_id`, `codigo_barras`, `nombre_producto`, `precio_venta`, `stock_actual`, `stock_minimo`.
8. **`Proveedores`**: Empresas suministradoras de insumos y medicamentos.
   * *Campos:* `id`, `nit`, `razon_social`, `persona_contacto`, `telefono`.
9. **`Compras_Cabecera`**: Historial de órdenes de compra realizadas a proveedores (fecha, proveedor, total).
   * *Campos:* `id`, `proveedor_id`, `usuario_id`, `numero_factura_proveedor`, `fecha_compra`, `total_compra`, `estado`.
10. **`Compras_Detalle`**: Registro detallado de productos y cantidades recibidas en cada compra.
    * *Campos:* `id`, `compra_id`, `producto_id`, `cantidad_comprada`, `precio_costo_unitario`, `subtotal`, `numero_lote`, `fecha_vencimiento`.
11. **`Lotes_Farmacia`**: Registro de control de vencimiento y existencias por lote de medicamento.
    * *Campos:* `id`, `producto_id`, `compra_detalle_id`, `numero_lote`, `fecha_fabricacion`, `fecha_vencimiento`, `cantidad_recibida`, `cantidad_disponible`.
12. **`Ventas_Cabecera`**: Registro de facturas de venta generadas en mostrador o consulta.
    * *Campos:* `id`, `propietario_id`, `usuario_id`, `fecha_venta`, `total_venta`, `metodo_pago`.
13. **`Ventas_Detalle`**: Ítems o productos vendidos incluidos en cada factura.
    * *Campos:* `id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`.
14. **`Pasarela_Pagos_Logs`**: Registro de transacciones procesadas mediante medios de pago electrónicos.
    * *Campos:* `id`, `venta_id`, `referencia_transaccion`, `pasarela_proveedor`, `estado_transaccion`, `monto_procesado`, `fecha_procesamiento`.

---

## 4. Análisis y Respuestas a Preguntas de Gestión

### ¿Qué información se requiere consolidar de un cliente y su mascota?
Se consolida la información del propietario (nombres, apellidos, documento y teléfonos de contacto) y se vincula con los perfiles de sus mascotas (especie, raza, fecha de nacimiento, peso e historial clínico de atenciones).

### ¿Qué controles específicos de inventario requieren los productos de farmacia?
Los productos farmacéuticos requieren trazabilidad por lote mediante la tabla `Lotes_Farmacia`, registrando el **número de lote** y la **fecha de vencimiento** asociada a la compra realizada al proveedor, impidiendo la comercialización o aplicación de insumos caducados.

### ¿Cómo se articula el flujo de ventas y compras con el inventario?
Al ingresar una compra en `Compras_Cabecera` y `Compras_Detalle`, el sistema incrementa el `stock_actual` del producto y crea el registro del lote. Al realizar una venta o formular un insumo en `Ventas_Detalle`, el sistema descuenta automáticamente la cantidad vendida del inventario y del lote correspondiente en tiempo real.
