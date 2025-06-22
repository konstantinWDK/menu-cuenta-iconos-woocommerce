2. Activa el plugin desde el menú **Plugins** en el panel de administración de WordPress.
3. Ve a **Apariencia > Menú cuenta WooCommerce** para configurar tus iconos.

---

## Uso

1. En **Apariencia > Menú cuenta WooCommerce**, verás la lista de todos los endpoints (Escritorio, Pedidos, Descargas, etc.), incluso los ocultos.
2. Para cada enlace:
- Haz clic en **Subir imagen** para elegir un icono de la Biblioteca de Medios.
- Pulsa **Eliminar** para quitar el icono.
- Marca **Ocultar este enlace** para que no se muestre en el frontend.
- Haz clic en **Personalizar tamaño**, **Ajustar márgenes** o **Ajustar padding** para desplegar los campos de configuración.
- Una vez editados, pulsa **Restablecer** para volver a los valores por defecto.
3. Guarda los cambios. Cuando visites tu página **Mi cuenta**, verás los iconos aplicados con los estilos configurados.

---

## Valores por defecto

- **Tamaño**: 20 px (ancho) × 20 px (alto)  
- **Márgenes**: top 0 px, right 8 px, bottom 0 px, left 0 px  
- **Padding**: top 0 px, right 0 px, bottom 0 px, left 0 px  

Puedes recuperar estos valores con el botón **Restablecer** para cada icono.

---

## Filtros y Hooks

- **Filtro** `woocommerce_account_menu_items`  
Se usa para ocultar los enlaces marcados antes de renderizar el menú.
- **Hook** `admin_menu`, `admin_init`, `admin_enqueue_scripts`  
Para registrar la página de ajustes y cargar el uploader de medios.
- **Hook** `woocommerce_before_account_navigation`  
Inyecta el JavaScript y CSS que añade los iconos personalizados en el frontend.

---

## Historial de versiones

- **1.4**  
- Botón **Restablecer** por icono.  
- Títulos sobre los campos de margen y padding.  
- **1.3**  
- Campos de margen y padding desglosados por lado (top/right/bottom/left).  
- **1.2**  
- Inyección de iconos en frontend mediante JavaScript para evitar escaping.  
- **1.1**  
- Integración del media uploader en un solo archivo.  
- **1.0**  
- Versión inicial: subida de iconos y ocultar enlaces.

---

## Licencia

Este plugin está licenciado bajo la **Licencia MIT**.  
Consulta el archivo `LICENSE` para más detalles.

---
