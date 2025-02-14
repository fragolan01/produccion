
 /*1.  Proveedores*/
CREATE TABLE `plataforma_productos_proveedores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del proveedor',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `nombre_proveedor` VARCHAR(250)  COMMENT 'Nombre o descripción del proveedor',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_general_ci 
COMMENT='Tabla para almacenar los proveedores';


 
/*2. Marcas */
CREATE TABLE `plataforma_productos_marcas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de la marca',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `id_proveedor` INT UNSIGNED  COMMENT 'Identificador del proveedor (clave foránea)',
  `nombre_marca` VARCHAR(250)  COMMENT 'Nombre o descripción de la marca',

  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_proveedor`) REFERENCES `plataforma_productos_proveedores` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Tabla para almacenar las marcas';



 /*3. Moneda */
CREATE TABLE `plataforma_productos_moneda` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de la moneda',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `moneda` CHAR(20)  COMMENT 'Tipo moneda (usd)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Tabla para almacenar las monedas';



/*4. Envios */
CREATE TABLE `plataforma_productos_envios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de envíos',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `nombre_envio` VARCHAR(250)  COMMENT 'Nombre del envío',
  `costo` DECIMAL(10,2)  COMMENT 'Costo del envío',
  `id_moneda` INT UNSIGNED  COMMENT 'Identificador de la moneda (clave foránea)',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_moneda`) REFERENCES `plataforma_productos_moneda` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Tabla para almacenar los envíos';



  /*5. Tipos Publicaciones */
CREATE TABLE `plataforma_productos_tipos_publicacion` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del tipo de publicación',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `site_id` CHAR(20) DEFAULT 'MLM' COMMENT 'Identificador del sitio (ej. MLM)',
  `tipo_publi_id` CHAR(20)  COMMENT 'Identificador del tipo de publicación asociado al sitio',
  `name` VARCHAR(250)  COMMENT 'Nombre del tipo de publicación',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tipo_publi_id` (`tipo_publi_id`) COMMENT 'Llave única para la tipo publicacion'
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Tabla para almacenar los tipos de publicación';




/* 6. Total ITEMS Meli */
CREATE TABLE `plataforma_productos_total_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del ITEM',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `search_type` CHAR(20) DEFAULT 'scan' COMMENT 'Tipo de búsqueda (scan total, etc.)',

  `scroll_id` VARCHAR(250)  COMMENT 'Identificador para actualizar dinámicamente',
  `seller_id` INT UNSIGNED  COMMENT 'Identificador del vendedor',
  `limit` INT UNSIGNED DEFAULT 250 COMMENT 'Límite de resultados por publicación (hasta 50)',
  `item_id` CHAR(20)  COMMENT 'Identificador del ítem en Meli',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_item_id` (`item_id`) COMMENT 'Llave única para publicaciones Meli'
  
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Tabla para almacenar los ítems publicados en Meli';




/* 7. plataforma_productos_atributos */
CREATE TABLE `plataforma_productos_atributos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del registro',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `item_id` CHAR(20)  COMMENT 'ITEM_ID Publicaciones Meli (Llave foránea)',

  `title` VARCHAR(250)  COMMENT 'Nombre de la publicación en Meli',
  `family_name` VARCHAR(20) COMMENT 'Nombre familia publicacion',
  `seller_id` INT UNSIGNED COMMENT 'ID Sello de publicacion',
  `category_id` CHAR(20)  COMMENT 'ID de la categoría de la publicación',
  `price` DECIMAL(10,2)  COMMENT 'Precio de la publicación',
  `currency_id` CHAR(10)  COMMENT 'El tipo de moneda de publicacion',
  `listing_type_id` CHAR(20)  COMMENT 'ID del tipo de publicación en Meli (Llave foránea)',
  `condition` CHAR(10)  COMMENT 'Indica si el producto es nuevo o usado',
  `permalink` TEXT  COMMENT 'Enlace de la publicación en Meli',

  PRIMARY KEY (`id`),
  FOREIGN KEY (`listing_type_id`) REFERENCES `plataforma_productos_tipos_publicacion` (`tipo_publi_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Tabla para almacenar los atributos de ítems publicados en Meli';





/* 8.  plataforma_productos_syscom */
CREATE TABLE `plataforma_productos_syscom` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del ITEM',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `id_producto_syscom` INT UNSIGNED NOT NULL COMMENT 'ID producto syscom',
  `modelo` VARCHAR(50)  COMMENT 'El modelo clasificado por Syscom',
  `total_existencia` INT UNSIGNED COMMENT 'existencias por producto',
  `titulo` TEXT COMMENT 'Descripcion del producto por syscom',

  `marca` VARCHAR(250)  COMMENT 'Marca correspondiente al producto Fk tabla marca ',
  `imagen` TEXT  COMMENT 'imagen del producto',
  `link_privado` TEXT  COMMENT 'link de la publicacion en SYSCOM',
  `descripcion` TEXT  COMMENT 'Detalles adicionales al producto',
  `caracteristicas` TEXT  COMMENT 'Caracteristicas del producto',
  `imagens` TEXT  COMMENT 'Una imagen del producto',
  `peso` DECIMAL(10,2)  COMMENT 'Peso del producto',
  `alto` DECIMAL(10,2)  COMMENT 'altura del producto',
  `largo` DECIMAL(10,2)  COMMENT 'Largo del producto',
  `ancho` DECIMAL(10,2)  COMMENT 'Ancho del producto',

  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id_producto_syscom` (`id_producto_syscom`) COMMENT 'Llave única para el ID producto syscom'
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='La tabla contiene todos los productos que vende fragolan de syscom';




/* 9. plataforma_productos_result_campania */
CREATE TABLE `plataforma_productos_result_campania` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `campaign_id` INT UNSIGNED NOT NULL COMMENT 'ID campania',
  `nombre_campania` VARCHAR(250) COMMENT 'Nombre de la campania',
  `status` CHAR(20) COMMENT 'Estado de la campania ON/OFF',
  `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de ultima modificacion',
  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion',
  `channel` CHAR(20)  COMMENT 'imagen del producto',
  `acos_target` DECIMAL(10,2) NOT NULL COMMENT 'El costo de la campania',
  `limit` INT UNSIGNED DEFAULT 250 COMMENT 'limite del numero de resultados',
  `date_from` DATE NOT NULL COMMENT 'Fecha inicio para obtener el reporte de campanias',
  `date_to` DATE NOT NULL COMMENT 'Fecha final para obtener el reporte de campanias',

  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_campaign_id` (`campaign_id`) COMMENT 'Llave única para el ID campania'

) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='La tabala que contiene todas las campanias con un rango de fecha';




/* Sin poblar */
/* 10. plataforma_productos_metric_campania */
CREATE TABLE `plataforma_productos_metric_campania` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `id_campaign` INT UNSIGNED  COMMENT 'ID campania (Llave foranea)',
 
  `clicks` INT UNSIGNED  COMMENT 'Numero clicks de la campania',
  `prints` INT UNSIGNED  COMMENT 'Impreciones de campania',
  `cost` DECIMAL(10,2)  COMMENT 'Costo de campania',
  `cpc` DECIMAL(10,2)  COMMENT 'Costo CPC campania',
  `ctr` DECIMAL(10,2)  COMMENT 'Costo CTR campania',
  `direct_amount` DECIMAL(10,2)  COMMENT 'Monto directo campania',
  `indirect_amount` DECIMAL(10,2)  COMMENT 'Monto indirecto campania',
  `total_amount` DECIMAL(10,2)  COMMENT 'Total de montos',
  `direct_units_quantity` INT UNSIGNED NOT NULL COMMENT 'total Unidades directas ',
  `indirect_units_quantity` INT UNSIGNED NOT NULL COMMENT 'total Unidades indirectas ',
  `direct_items_quantity` INT UNSIGNED NOT NULL COMMENT 'cantidad_artículos_directos',

  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_campaign`) REFERENCES `plataforma_productos_result_campania` (`campaign_id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='La tabala que contiene todas las campanias con un rango de fecha';




/* 11. plataforma_productos_anuncio_meli */
CREATE TABLE `plataforma_productos_anuncio_meli` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  
  `item_id` CHAR(20)  COMMENT 'Identificador del ítem en Meli (Llave foranea)', 
  `campaign_id` INT UNSIGNED  COMMENT 'Id Campania (Llave foranea)',

  `price` DECIMAL(10,2)  COMMENT 'Precio venta del producto',
  `title` TEXT  COMMENT 'Descripcion del producto',
  `status` CHAR(20)  COMMENT 'Estado de la publicacion',
  `domain_id` CHAR(20)  COMMENT 'Dominio de la publicacion',
  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del publicacion',
  `channel` CHAR(20)  COMMENT 'Canal de publicacion',
  `brand_value_id` INT UNSIGNED  COMMENT 'Valor de la marca ID',
  `brand_value_name` VARCHAR(250)  COMMENT 'Nombre de la publicidad',
  `current_level` CHAR(20)  COMMENT 'nivel actual de la publicidad',
  `permalink` TEXT  COMMENT 'Link del anuncio',

  PRIMARY KEY (`id`),
  FOREIGN KEY (`item_id`) REFERENCES `plataforma_productos_total_items` (`item_id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE,
  FOREIGN KEY (`campaign_id`) REFERENCES `plataforma_productos_result_campania` (`campaign_id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='La tabala el anuncio correspondiente por id publicacion Meli';




/* 12. plataforma_productos_tipo_cambio */
CREATE TABLE `plataforma_productos_tipo_cambio` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha y hora actual TC',

  `normal` decimal(10,2)  COMMENT 'tipo de cambio normal en Syscom',
  `preferencial` decimal(10,0) ,
  `un_dia` decimal(10,0) ,
  `una_semana` decimal(10,0) ,
  `dos_semanas` decimal(10,0) ,
  `tres_semanas` decimal(10,0) ,
  `un_mes` decimal(10,0) ,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Almacena el TC diario';




/* 13. plataforma_productos_precios_syscom */
CREATE TABLE `plataforma_productos_precios_syscom` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha y hora actual TC',

  `id_producto_syscom` INT UNSIGNED COMMENT 'ID syscom de la tabla plataforma_productos_syscom',
  `precio1` DECIMAL(10,2) ,
  `precio_especial` DECIMAL(10,2) ,
  `precio_descuento` DECIMAL(10,2) ,
  `precio_lista` DECIMAL(10,2) ,

  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_producto_syscom`) REFERENCES `plataforma_productos_syscom` (`id_producto_syscom`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 




/* 14. plataforma_productos_categorias_syscom */
CREATE TABLE `plataforma_productos_categorias_syscom` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_producto_syscom` INT UNSIGNED COMMENT 'ID syscom de la tabla plataforma_productos_syscom',
  `id_categorias` int UNSIGNED,
  `nombre` VARCHAR(250) ,
  `nivel` INT UNSIGNED ,

  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_producto_syscom`) REFERENCES `plataforma_productos_syscom` (`id_producto_syscom`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 



/* 15  plataforma_productos_inventario_mini*/
CREATE TABLE `plataforma_productos_inventario_mini` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_syscom` INT UNSIGNED COMMENT 'Id productos Syscom FK',
  `inv_min` INT UNSIGNED COMMENT 'Inventario minimo por producto',

  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_syscom`) REFERENCES `plataforma_productos_syscom` (`id_producto_syscom`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 



/* 16  plataforma_productos_modelo*/
CREATE TABLE `plataforma_productos_modelo` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `modelo` VARCHAR(100) COMMENT 'Modelo de producto syscom',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 



/* 17  plataforma_productos_descuento*/
CREATE TABLE `plataforma_productos_descuento` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_producto_syscom` INT UNSIGNED DEFAULT 0 COMMENT 'Id productos Syscom FK',
  `descuento` DECIMAL(10,2) COMMENT 'descuento para cada producto',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_producto_syscom`) REFERENCES `plataforma_productos_syscom` (`id_producto_syscom`)
  ON DELETE SET NULL 
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci




/* 18  plataforma_productos_syscom_meli*/
CREATE TABLE `plataforma_productos_syscom_meli` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_prod_syscom` INT UNSIGNED DEFAULT 0 COMMENT 'Id productos Syscom',
  `id_item_meli` CHAR(20) COMMENT 'Item publicacion Mercado Libre',
  `id_envios` INT UNSIGNED COMMENT 'Item de tabla envios ',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci


ALTER TABLE `plataforma_productos_syscom_meli`
ADD UNIQUE (`id_prod_syscom`);

 


/* 19  plataforma_productos_variables*/
CREATE TABLE `plataforma_productos_variables` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `iva` DECIMAL(10,2) DEFAULT 0.16 COMMENT 'iva',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci



/* 20  plataforma_productos_pack*/
CREATE TABLE `plataforma_productos_pack` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_prod_syscom` INT UNSIGNED DEFAULT 0 COMMENT 'Id productos Syscom',
  `id_item_meli` CHAR(20) COMMENT 'Item publicacion Mercado Libre',
  `num_piezas` INT UNSIGNED DEFAULT 0 COMMENT 'Id productos Syscom',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_prod_syscom`) REFERENCES `plataforma_productos_syscom_meli` (`id_prod_syscom`)
  ON DELETE SET NULL 
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci




/* ********************** POR CREAR ********************************** */
/* 20. plataforma_productos_stock */
CREATE TABLE `plataforma_productos_stock` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_categoria` CHAR(20) COMMENT 'Llave foránea a la tabla plataforma_productos_arbol_de_categorias',
  `proveedor_id` INT UNSIGNED COMMENT 'Llave foránea a la tabla plataforma_productos_proveedores',
  `marca_id` INT UNSIGNED COMMENT 'Llave foránea a la tabla plataforma_productos_marcas',
  `costo_envio_id` INT UNSIGNED COMMENT 'Llave foránea a la tabla plataforma_productos_envios',
  `item_id` CHAR(20) COMMENT 'Llave foránea a la tabla plataforma_productos_total_items',
  `producto_syscom_id` INT UNSIGNED COMMENT 'Llave foránea a la tabla plataforma_productos_syscom',
  `id_sub_categoria` CHAR(20) COMMENT 'Llave foránea a la tabla plataforma_productos_detalle_por_categoria',

  `inv_minimo` INT UNSIGNED  COMMENT 'Inventario mínimo del producto para publicar en Meli',
  `orden` INT UNSIGNED  COMMENT 'Orden para el reporte',
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de captura',
  `status` INT UNSIGNED  COMMENT 'Estado del producto en Mercado Libre',
  `modelo` VARCHAR(250)  COMMENT 'Modelo del producto en Syscom',

  `fijo_plataforma` TEXT  COMMENT 'URL interna del vendedor',
  `url_proveedor_1` TEXT  COMMENT 'URL 1 del proveedor',
  `url_proveedor_2` TEXT  COMMENT 'URL 2 del proveedor',
  `url_proveedor_3` TEXT  COMMENT 'URL 3 del proveedor',
  `url_proveedor_4` TEXT  COMMENT 'URL 4 del proveedor',
  `url_proveedor_5` TEXT  COMMENT 'URL 5 del proveedor',
  `url_proveedor_6` TEXT  COMMENT 'URL 6 del proveedor',
  `observaciones` TEXT  COMMENT 'Observaciones sobre el producto',

  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_categoria`) REFERENCES `plataforma_productos_arbol_de_categorias` (`id_categoria`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`proveedor_id`) REFERENCES `plataforma_productos_proveedores` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`marca_id`) REFERENCES `plataforma_productos_marcas` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`costo_envio_id`) REFERENCES `plataforma_productos_envios` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `plataforma_productos_total_items` (`item_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`producto_syscom_id`) REFERENCES `plataforma_productos_syscom` (`id_producto_syscom`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (`id_sub_categoria`) REFERENCES `plataforma_productos_detalle_por_categoria` (`id_children_category`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 
  COMMENT='Tabla para almacenar información del stock de productos.';



/* ================================== TABLAS SIN CREAR ================================================================ */


/* 1. plataforma_productos_arbol_de_categorias */
/*Esta tabla queda fuera No es posible asociarla con los productos id*/
CREATE TABLE `plataforma_productos_arbol_de_categorias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del ITEM',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `id_categoria` CHAR(20) COMMENT 'Id de la categoria',
  `name_categoria` VARCHAR(250)  COMMENT 'Nombre de la categoria',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id_categoria` (`id_categoria`) COMMENT 'Llave única para la columna categoria_id'
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_general_ci 
COMMENT='Tabla que almacena el total de las categorias';


/* 2. plataforma_productos_detalle_por_categoria */
/* FUERA NO ES POSIBLE ASOCIARLA CON LAS CATEGORIAS DE PUBLI MELI */
CREATE TABLE `plataforma_productos_detalle_por_categoria` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del ITEM',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',
  `id_categoria` CHAR(20) COMMENT 'Id de la categoria',
  `id_children_category` CHAR(20) COMMENT 'Id sub-categoria la categoria',
  `name_categoria` VARCHAR(250)  COMMENT 'Nombre de la categoria',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id_children_category` (`id_children_category`) COMMENT 'Llave única para la columna id_children_categoriy',
  FOREIGN KEY (`id_categoria`) REFERENCES `plataforma_productos_arbol_de_categorias` (`id_categoria`)
  ON DELETE SET NULL 
  ON UPDATE CASCADE
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_general_ci 
COMMENT='Tabla que almacena las subcategorias ';


/* 3  plataforma_productos_children_category */
CREATE TABLE `plataforma_productos_children_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_children_category` CHAR(20) COMMENT 'ID de children de subcategoria (Llave foranea)',
  `id_categoria3` CHAR(20) COMMENT 'ID subcategoria a consultar (UK)',
  `name_categoria3` VARCHAR(150) COMMENT 'Nombre de subcategoria',

  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id_categoria3` (`id_categoria3`) COMMENT 'Llave única para children category',
  FOREIGN KEY (`id_children_category`) REFERENCES `plataforma_productos_detalle_por_categoria` (`id_children_category`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 




/* 4  plataforma_productos_children_sub_category */
CREATE TABLE `plataforma_productos_children_sub_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_categoria3` CHAR(20) COMMENT 'ID de children de subcategoria (Llave foranea)',
  `id_children_sub_categoria` CHAR(20) COMMENT 'ID subcategoria a consultar (UK)',
  `name_categoria4` VARCHAR(150) COMMENT 'Nombre de subcategoria',

  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id_children_sub_categoria` (`id_children_sub_categoria`) COMMENT 'Llave única para children sub category',
  FOREIGN KEY (`id_categoria3`) REFERENCES `plataforma_productos_children_category` (`id_categoria3`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 



/* 5  plataforma_productos_children_sub_category5 */
CREATE TABLE `plataforma_productos_children_sub_category5` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `id_dominio` INT UNSIGNED DEFAULT 9999 COMMENT 'Identificador del dominio asociado',

  `id_children_sub_categoria` CHAR(20) COMMENT 'ID de children de subcategoria (Llave foranea)',
  `id_children_sub_categoria5` CHAR(20) COMMENT 'ID subcategoria a consultar (UK)',
  `name_categoria5` VARCHAR(150) COMMENT 'Nombre de subcategoria',

  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id_children_sub_categoria5` (`id_children_sub_categoria5`) COMMENT 'Llave única para children sub category 5',
  FOREIGN KEY (`id_children_sub_categoria`) REFERENCES `plataforma_productos_children_sub_category` (`id_children_sub_categoria`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT 
  CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci 