# Diagrama Entidad-Relación (MER) - VetPets ERP

```mermaid
erDiagram
    ROLES ||--o{ USUARIOS : "asigna permisos a"
    PROPIETARIOS ||--o{ TELEFONOS_PROPIETARIO : "posee"
    PROPIETARIOS ||--o{ MASCOTAS_PACIENTES : "registra/dueño de"
    PROPIETARIOS ||--o{ VENTAS_CABECERA : "realiza compras"
    USUARIOS ||--o{ VENTAS_CABECERA : "procesa en caja"
    USUARIOS ||--o{ COMPRAS_CABECERA : "registra recepcion"
    CATEGORIAS ||--o{ PRODUCTOS : "clasifica"
    PRODUCTOS ||--o{ LOTES_FARMACIA : "controla existencias"
    PROVEEDORES ||--o{ COMPRAS_CABECERA : "suministra ordenes"
    COMPRAS_CABECERA ||--o{ COMPRAS_DETALLE : "contiene"
    PRODUCTOS ||--o{ COMPRAS_DETALLE : "se incluye en"
    COMPRAS_DETALLE ||--o{ LOTES_FARMACIA : "genera lote"
    VENTAS_CABECERA ||--o{ VENTAS_DETALLE : "contiene"
    PRODUCTOS ||--o{ VENTAS_DETALLE : "se incluye en"
    VENTAS_CABECERA ||--|| PASARELA_PAGOS_LOGS : "genera registro"

    ROLES {
        bigint id PK
        string nombre_rol
        string descripcion
    }

    USUARIOS {
        bigint id PK
        bigint rol_id FK
        string primer_nombre
        string segundo_nombre
        string primer_apellido
        string segundo_apellido
        string email
        string password
    }

    PROPIETARIOS {
        bigint id PK
        string tipo_documento
        string numero_documento
        string primer_nombre
        string segundo_nombre
        string primer_apellido
        string segundo_apellido
        string direccion
        string email
    }

    TELEFONOS_PROPIETARIO {
        bigint id PK
        bigint propietario_id FK
        string numero_telefono
        string tipo_telefono
    }

    MASCOTAS_PACIENTES {
        bigint id PK
        bigint propietario_id FK
        string nombre_mascota
        string especie
        string raza
        date fecha_nacimiento
        decimal peso
    }

    CATEGORIAS {
        bigint id PK
        string nombre_categoria
        string descripcion
    }

    PRODUCTOS {
        bigint id PK
        bigint categoria_id FK
        string codigo_barras
        string nombre_producto
        decimal precio_venta
        int stock_actual
        int stock_minimo
    }

    PROVEEDORES {
        bigint id PK
        string nit
        string razon_social
        string persona_contacto
        string telefono
    }

    COMPRAS_CABECERA {
        bigint id PK
        bigint proveedor_id FK
        bigint usuario_id FK
        string numero_factura_proveedor
        datetime fecha_compra
        decimal total_compra
        string estado
    }

    COMPRAS_DETALLE {
        bigint id PK
        bigint compra_id FK
        bigint producto_id FK
        int cantidad_comprada
        decimal precio_costo_unitario
        decimal subtotal
        string numero_lote
        date fecha_vencimiento
    }

    LOTES_FARMACIA {
        bigint id PK
        bigint producto_id FK
        bigint compra_detalle_id FK
        string numero_lote
        date fecha_fabricacion
        date fecha_vencimiento
        int cantidad_recibida
        int cantidad_disponible
    }

    VENTAS_CABECERA {
        bigint id PK
        bigint propietario_id FK
        bigint usuario_id FK
        datetime fecha_venta
        decimal total_venta
        string metodo_pago
    }

    VENTAS_DETALLE {
        bigint id PK
        bigint venta_id FK
        bigint producto_id FK
        int cantidad
        decimal precio_unitario
        decimal subtotal
    }

    PASARELA_PAGOS_LOGS {
        bigint id PK
        bigint venta_id FK
        string referencia_transaccion
        string pasarela_proveedor
        string estado_transaccion
        decimal monto_procesado
        datetime fecha_procesamiento
    }
```

## 🔗 Resumen de Relaciones y Cardinalidades

| Entidad Origen | Entidad Destino | Tipo de Relación | Explicación |
| :--- | :--- | :---: | :--- |
| `ROLES` | `USUARIOS` | **$1:N$** | Un rol asigna permisos a múltiples usuarios. |
| `PROPIETARIOS` | `TELEFONOS_PROPIETARIO` | **$1:N$** | Un propietario puede registrar $N$ números de contacto. |
| `PROPIETARIOS` | `MASCOTAS_PACIENTES` | **$1:N$** | Un propietario puede ser dueño de $N$ mascotas. |
| `PROPIETARIOS` | `VENTAS_CABECERA` | **$1:N$** | Un propietario realiza $N$ compras/facturas. |
| `USUARIOS` | `VENTAS_CABECERA` | **$1:N$** | Un cajero/usuario atiende $N$ facturas de venta. |
| `USUARIOS` | `COMPRAS_CABECERA` | **$1:N$** | Un usuario registra la recepción de $N$ órdenes de compra. |
| `CATEGORIAS` | `PRODUCTOS` | **$1:N$** | Una categoría agrupa múltiples productos. |
| `PROVEEDORES` | `COMPRAS_CABECERA` | **$1:N$** | Un proveedor suministra $N$ órdenes/facturas de compra. |
| `COMPRAS_CABECERA` | `COMPRAS_DETALLE` | **$1:N$** | Una orden de compra contiene $N$ ítems de productos. |
| `COMPRAS_DETALLE` | `LOTES_FARMACIA` | **$1:N$** | Un detalle de compra genera $N$ registros de lotes recibidos. |
| `PRODUCTOS` | `LOTES_FARMACIA` | **$1:N$** | Un producto de farmacia maneja $N$ lotes de expiración. |
| **`VENTAS_CABECERA`** | **`PRODUCTOS`** | **$N:M$** | **Muchos a Muchos:** Resuelta mediante la tabla pivote `VENTAS_DETALLE` ($1:N$). |
| **`PROVEEDORES`** | **`PRODUCTOS`** | **$N:M$** | **Muchos a Muchos:** Resuelta mediante el historial de compra `COMPRAS_CABECERA` / `COMPRAS_DETALLE` ($1:N$). |
| **`VENTAS_CABECERA`** | **`PASARELA_PAGOS_LOGS`** | **$1:1$** | **Uno a Uno:** Cada factura electrónica genera 1 log de pago en pasarela. |
