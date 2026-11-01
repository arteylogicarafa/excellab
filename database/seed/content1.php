<?php
// Módulos 1 a 4: lecciones con su ejercicio de comprobación.

// ============ MÓDULO 1 · Introducción a Excel ============
$l = L(1, '¿Qué es Excel?', 'Qué es una hoja de cálculo y para qué sirve.',
    '<p><strong>Microsoft Excel</strong> es una <em>hoja de cálculo</em>: un programa que organiza la información en filas y columnas y realiza cálculos de forma automática. Si cambias un dato, todos los resultados que dependen de él se actualizan solos.</p><ul><li>Registrar y ordenar datos (listas, inventarios, notas).</li><li>Calcular con fórmulas y funciones.</li><li>Representar resultados con gráficos.</li></ul>',
    '<p>Una lista de compras: al escribir cantidad y precio, Excel calcula el total sin necesidad de calculadora. Si el precio del cuaderno sube, el total se corrige automáticamente.</p>',
    S(['Producto', 'Cantidad', 'Precio unitario'], [['Cuaderno', 3, 2.5], ['Lápiz', 10, 0.4], ['Borrador', 2, 0.75]]));
X(1, $l, 'Usos de Excel', 'Concepto de Excel', 'basico', 'multiple_choice',
    '¿Cuál de las siguientes tareas es la más adecuada para realizar en Excel?',
    ['*Calcular automáticamente el total de ventas del mes y graficarlo', 'Redactar una carta formal con membrete', 'Editar un video', 'Diseñar un logotipo'],
    'Excel está pensado para organizar datos y calcular con ellos. Redactar cartas corresponde a un procesador de texto, y el video o el diseño gráfico a otras herramientas.');

$l = L(1, 'Entorno de trabajo', 'Cinta de opciones, barra de fórmulas y zona de trabajo.',
    '<p>La ventana de Excel se compone de:</p><ul><li><strong>Cinta de opciones:</strong> pestañas (Inicio, Insertar, Fórmulas, Datos, Vista…) con los comandos agrupados.</li><li><strong>Cuadro de nombres:</strong> muestra la dirección de la celda activa (por ejemplo B3).</li><li><strong>Barra de fórmulas:</strong> muestra y permite editar el contenido real de la celda activa.</li><li><strong>Hoja de cálculo:</strong> la cuadrícula de celdas.</li><li><strong>Pestañas de hojas y barra de estado:</strong> abajo, para cambiar de hoja y ver resúmenes como suma o promedio de la selección.</li></ul>',
    '<p>Selecciona la celda B2 y observa: el Cuadro de nombres dice <code>B2</code> y la barra de fórmulas muestra su contenido. Si la celda tiene una fórmula, la celda muestra el resultado, pero la barra de fórmulas muestra la fórmula.</p>',
    null, 'assets/img/excel-entorno.svg');
X(1, $l, 'Barra de fórmulas', 'Entorno de trabajo', 'basico', 'multiple_choice',
    '¿Dónde se visualiza y edita el contenido real (la fórmula) de la celda seleccionada?',
    ['*En la barra de fórmulas', 'En el cuadro de nombres', 'En la barra de estado', 'En la cinta de opciones'],
    'El cuadro de nombres indica la dirección de la celda; la barra de estado muestra resúmenes; la barra de fórmulas muestra y permite editar el contenido.');

$l = L(1, 'Libros y hojas', 'La estructura de un archivo de Excel.',
    '<p>Un archivo de Excel (<code>.xlsx</code>) se llama <strong>libro</strong> y contiene una o varias <strong>hojas</strong>. Cada hoja es una cuadrícula independiente.</p><ul><li>Insertar hoja: botón <strong>+</strong> junto a las pestañas.</li><li>Cambiar nombre: doble clic sobre la pestaña.</li><li>Mover o copiar: arrastrar la pestaña (con Ctrl para copiar).</li><li>Referenciar otra hoja: <code>=Ventas!B3</code> toma la celda B3 de la hoja llamada Ventas.</li></ul>',
    '<p>Un libro llamado <em>Contabilidad 2026</em> puede tener una hoja por mes: Enero, Febrero, Marzo… y una hoja Resumen que suma los totales de cada mes con fórmulas como <code>=Enero!B10+Febrero!B10</code>.</p>',
    null, 'assets/img/libro-hojas.svg');
X(1, $l, 'Libro y hojas', 'Libros y hojas', 'basico', 'true_false',
    'Un archivo de Excel recibe el nombre de libro y puede contener varias hojas de cálculo.',
    ['*Verdadero', 'Falso'],
    'El archivo es el libro y dentro tiene tantas hojas como necesites.');

$l = L(1, 'Filas, columnas y celdas', 'Cómo se identifican y seleccionan las celdas.',
    '<p>Las <strong>columnas</strong> se nombran con letras (A, B, C… hasta XFD) y las <strong>filas</strong> con números (1 a 1 048 576). La <strong>celda</strong> es la intersección de una columna y una fila y su dirección combina ambas: <code>B3</code> es la columna B, fila 3.</p><p>Un <strong>rango</strong> es un grupo de celdas contiguas y se escribe <code>B2:B6</code> (desde B2 hasta B6).</p>',
    '<p>En la tabla de ejemplo, el texto “Lápiz” está en <code>A2</code> y el número 10 está en <code>B2</code>. El rango <code>B2:B4</code> abarca las tres cantidades.</p>',
    S(['Producto', 'Cantidad'], [['Lápiz', 10], ['Cuaderno', 5], ['Borrador', 8]]));
X(1, $l, 'Dirección de una celda', 'Celdas y rangos', 'basico', 'analyze_table',
    'Observa la hoja. ¿En qué celda está escrito el texto “Cuaderno”?',
    'A3', 'La columna A contiene los productos y “Cuaderno” está en la fila 3 (la fila 1 es el encabezado), por tanto su dirección es A3.', null,
    ['data' => S(['Producto', 'Cantidad'], [['Lápiz', 10], ['Cuaderno', 5], ['Borrador', 8]]), 'kind' => 'text', 'instr' => 'Escribe solo la dirección, por ejemplo B4.']);

$l = L(1, 'Tipos de datos', 'Texto, números, fechas y valores lógicos.',
    '<p>En una celda puedes escribir distintos tipos de datos:</p><ul><li><strong>Texto:</strong> se alinea a la izquierda (nombres, códigos).</li><li><strong>Números:</strong> se alinean a la derecha y se pueden calcular.</li><li><strong>Fechas y horas:</strong> son números con formato (<code>15/03/2026</code>).</li><li><strong>Lógicos:</strong> VERDADERO o FALSO.</li><li><strong>Fórmulas:</strong> empiezan con <code>=</code>.</li></ul><p>Un dato que parece número pero está alineado a la izquierda suele ser texto y no se podrá sumar.</p>',
    '<p>En una lista de clientes, el nombre es texto, la edad es un número, la fecha de registro es una fecha y la columna “Activo” podría ser VERDADERO o FALSO.</p>',
    S(['Nombre', 'Edad', 'Registro', 'Activo'], [['Rosa Díaz', 34, '15/03/2026', 'VERDADERO'], ['Mario Paz', 28, '02/04/2026', 'FALSO']]));
X(1, $l, 'Tipo de dato', 'Tipos de datos', 'basico', 'multiple_choice',
    'Al escribir 15/03/2026 en una celda, Excel lo reconoce como:',
    ['*Una fecha', 'Un texto', 'Una fórmula', 'Un error'],
    'Excel interpreta ese formato como una fecha y la guarda internamente como un número de serie, lo que permite calcular días entre fechas.');

$l = L(1, 'Introducción a las fórmulas', 'Cómo escribir una fórmula sencilla.',
    '<p>Una <strong>fórmula</strong> siempre empieza con el signo <code>=</code>. Combina valores, referencias a celdas y operadores: <code>+</code> suma, <code>-</code> resta, <code>*</code> multiplica, <code>/</code> divide y <code>^</code> potencia.</p><p>Se confirma con <kbd>Enter</kbd> y se edita con <kbd>F2</kbd>. Lo importante: si cambias un dato referenciado, el resultado se recalcula solo.</p>',
    '<p>Con cantidad en A2 y precio en B2, la fórmula <code>=A2*B2</code> da el importe. Al cambiar B2 de 2,5 a 3, el resultado pasa de 10 a 12 sin tocar la fórmula.</p>',
    S(['Cantidad', 'Precio', 'Importe'], [[4, 2.5, '=A2*B2']]));
X(1, $l, 'Primera fórmula', 'Fórmulas', 'basico', 'complete_formula',
    'Escribe la fórmula que, ubicada en C2, calcule el importe multiplicando la cantidad por el precio.',
    '=A2*B2||=B2*A2', 'La fórmula multiplica las celdas A2 y B2 con el operador *. Recuerda que empieza con =.', null,
    ['data' => S(['Cantidad', 'Precio', 'Importe'], [[4, 2.5, '']]), 'kind' => 'formula']);

// ============ MÓDULO 2 · Formato y organización ============
$l = L(2, 'Formato de celdas', 'Fuente, alineación y el cuadro Formato de celdas.',
    '<p>En la pestaña <strong>Inicio</strong> encuentras fuente, tamaño, negrita (<kbd>Ctrl+N</kbd>), cursiva, alineación y color. Para más opciones usa <kbd>Ctrl+1</kbd>, que abre <strong>Formato de celdas</strong> con las pestañas Número, Alineación, Fuente, Borde, Relleno y Proteger.</p><p>Aplica el formato a una selección completa para que la tabla tenga un aspecto uniforme.</p>',
    '<p>Para un encabezado: selecciona A1:C1, aplica negrita, centra el texto y pon un relleno verde claro. Así se distingue de los datos.</p>',
    S(['Producto', 'Cantidad', 'Precio'], [['Cuaderno', 3, 2.5], ['Lápiz', 10, 0.4]]));
X(2, $l, 'Atajo de formato', 'Formato de celdas', 'basico', 'multiple_choice',
    '¿Qué atajo de teclado abre el cuadro “Formato de celdas”?',
    ['*Ctrl+1', 'Ctrl+F', 'Ctrl+P', 'Ctrl+Z'], 'Ctrl+1 abre Formato de celdas. Ctrl+F busca, Ctrl+P imprime y Ctrl+Z deshace.');

$l = L(2, 'Formato de números', 'Moneda, porcentaje, fecha y otros formatos numéricos.',
    '<p>El formato cambia <strong>cómo se ve</strong> un número, no su valor. Los más usados: General, Número (decimales), Moneda, Contabilidad, Fecha, Porcentaje, Fracción, Científico y Texto.</p><p>El formato <strong>Porcentaje</strong> multiplica visualmente por 100: el valor <code>0,25</code> se muestra como <code>25 %</code>.</p>',
    '<p>Si escribes 1500 y aplicas formato Moneda con dos decimales, verás <code>$1.500,00</code>, pero el valor sigue siendo 1500 y puedes usarlo en cálculos.</p>',
    S(['Valor', 'Formato aplicado', 'Se muestra'], [[1500, 'Moneda', '$1.500,00'], [0.25, 'Porcentaje', '25%'], ['15/03/2026', 'Fecha larga', 'domingo, 15 de marzo de 2026']]));
X(2, $l, 'Formato porcentaje', 'Formato de números', 'basico', 'solve_operation',
    'Escribes 0,15 en una celda y le aplicas el formato Porcentaje. ¿Qué número se mostrará (sin el símbolo %)?',
    '15', 'El formato Porcentaje multiplica por 100 al mostrar: 0,15 × 100 = 15 %.', null, ['kind' => 'number']);

$l = L(2, 'Bordes y colores', 'Mejorar la lectura con bordes, rellenos y colores.',
    '<p>Los <strong>bordes</strong> separan datos y los <strong>rellenos</strong> destacan encabezados o totales. Se encuentran en Inicio → Fuente (Bordes y Color de relleno).</p><p>Buenas prácticas: usar pocos colores (2 o 3), mantener buen contraste entre texto y fondo y reservar los colores llamativos para lo realmente importante.</p>',
    '<p>Una tabla clara: encabezado verde oscuro con texto blanco, filas de datos con borde fino gris y la fila de totales en negrita con borde superior doble.</p>',
    S(['Producto', 'Cantidad', 'Total'], [['Cuaderno', 3, 7.5], ['Lápiz', 10, 4], ['Total', 13, 11.5]]));
X(2, $l, 'Diseño de tablas', 'Bordes y colores', 'basico', 'true_false',
    'Un buen diseño de tabla usa muchos colores distintos para que cada dato destaque.',
    ['Verdadero', '*Falso'], 'Demasiados colores saturan y confunden. Lo recomendable es usar pocos colores con buen contraste.');

$l = L(2, 'Combinar celdas', 'Cuándo conviene combinar y cuándo no.',
    '<p><strong>Combinar y centrar</strong> (Inicio → Alineación) une varias celdas en una sola. Es útil para títulos sobre una tabla.</p><p>Pero dentro de la tabla de datos <strong>no se recomienda</strong>: dificulta ordenar, filtrar y copiar. Una alternativa es <em>Centrar en la selección</em> (Formato de celdas → Alineación → Horizontal), que centra el texto sin unir celdas.</p>',
    '<p>Título “Ventas del primer trimestre” centrado sobre las columnas A:D combinando A1:D1. Los datos desde la fila 3 se dejan sin combinar.</p>', null);
X(2, $l, 'Combinar celdas', 'Combinar celdas', 'basico', 'multiple_choice',
    '¿Por qué se recomienda no combinar celdas dentro de una tabla de datos?',
    ['*Porque dificulta ordenar, filtrar y copiar los datos', 'Porque hace que las fórmulas dejen de funcionar para siempre', 'Porque aumenta el tamaño del archivo a gigabytes', 'Porque Excel lo prohíbe'],
    'Las celdas combinadas rompen la estructura de filas y columnas que necesitan ordenar y filtrar.');

$l = L(2, 'Ajuste de columnas y filas', 'Cambiar el ancho y alto para que todo se vea.',
    '<p>Para ajustar una columna al contenido, haz <strong>doble clic</strong> en el borde derecho de su letra. También puedes arrastrar el borde o usar Inicio → Formato → Ancho de columna.</p><p>Si un número no cabe, la celda muestra <code>#####</code>: basta con ensanchar la columna. Para textos largos usa <strong>Ajustar texto</strong>, que lo distribuye en varias líneas.</p>',
    '<p>La columna “Descripción” corta el texto: se ensancha con doble clic o se activa “Ajustar texto” y la fila crece sola.</p>', null);
X(2, $l, 'Almohadillas ####', 'Ajuste de columnas', 'basico', 'multiple_choice',
    'Una celda muestra “#####”. ¿Cuál es la causa más probable?',
    ['*La columna es demasiado estrecha para mostrar el número', 'La fórmula tiene un error de sintaxis', 'El archivo está dañado', 'La celda está protegida'],
    '#### aparece cuando el ancho de la columna no alcanza para el valor. Se soluciona ensanchando la columna.');

$l = L(2, 'Tablas', 'Convertir un rango en una tabla de Excel.',
    '<p>Una <strong>Tabla</strong> (Insertar → Tabla, o <kbd>Ctrl+T</kbd>) añade filtros automáticos, estilos con filas alternadas, una fila de totales opcional y crece sola al agregar datos nuevos.</p><p>Requisitos: una fila de encabezados con nombres únicos y sin filas o columnas vacías en medio.</p>',
    '<p>Al convertir A1:C20 en tabla y añadir un registro en la fila 21, la tabla se expande y las fórmulas y gráficos que la usan se actualizan.</p>',
    S(['Producto', 'Cantidad', 'Precio'], [['Cuaderno', 3, 2.5], ['Lápiz', 10, 0.4]]));
X(2, $l, 'Atajo de tablas', 'Tablas', 'basico', 'multiple_choice',
    '¿Qué atajo convierte un rango de datos en una Tabla de Excel?',
    ['*Ctrl+T', 'Ctrl+K', 'Ctrl+W', 'Ctrl+E'], 'Ctrl+T crea la tabla. Ctrl+K inserta un hipervínculo y Ctrl+W cierra el libro.');

// ============ MÓDULO 3 · Operaciones y fórmulas básicas ============
$gastos = S(['Concepto', 'Monto'], [['Alquiler', 500], ['Servicios', 120], ['Comida', 300]]);

$l = L(3, 'Suma', 'Sumar valores con el operador +.',
    '<p>El operador <code>+</code> suma valores o celdas: <code>=B2+B3</code>. Puedes encadenar varios sumandos: <code>=B2+B3+B4</code>. Cuando hay muchos datos es más práctico usar la función SUMA (módulo 4).</p>',
    '<p>Con ingresos de enero, febrero y marzo en B2:B4, el total del trimestre se calcula con <code>=B2+B3+B4</code>.</p>',
    S(['Mes', 'Ingresos'], [['Enero', 1200], ['Febrero', 1350], ['Marzo', 1100]]));
X(3, $l, 'Sumar celdas', 'Suma', 'basico', 'solve_operation',
    'Con los datos de la hoja, ¿qué valor devuelve la fórmula =B2+B3+B4?', '920',
    '500 + 120 + 300 = 920.', null, ['data' => $gastos, 'kind' => 'number']);

$l = L(3, 'Resta', 'Calcular diferencias y saldos.',
    '<p>El operador <code>-</code> resta: <code>=B2-B3</code>. Es la base de cálculos como el saldo (ingresos − gastos) o la diferencia entre lo planificado y lo real.</p>',
    '<p>Con ingresos en B2 y gastos en B3, el saldo es <code>=B2-B3</code>. Un resultado negativo indica déficit.</p>',
    S(['Concepto', 'Monto'], [['Ingresos', 2500], ['Gastos', 1875]]));
X(3, $l, 'Calcular saldo', 'Resta', 'basico', 'solve_operation',
    'Con los datos de la hoja, ¿qué resultado da =B2-B3?', '625', '2500 − 1875 = 625.', null,
    ['data' => S(['Concepto', 'Monto'], [['Ingresos', 2500], ['Gastos', 1875]]), 'kind' => 'number']);

$l = L(3, 'Multiplicación', 'Multiplicar con el operador *.',
    '<p>El asterisco <code>*</code> multiplica: <code>=B2*C2</code>. Se usa para importes (cantidad × precio), porcentajes (<code>=B2*0,16</code>) y muchos otros cálculos.</p>',
    '<p>Si compras 12 cuadernos a 3,50 cada uno, el importe es <code>=12*3,5</code> o, con celdas, <code>=B2*C2</code>.</p>',
    S(['Producto', 'Cantidad', 'Precio', 'Importe'], [['Cuaderno', 12, 3.5, '=B2*C2']]));
X(3, $l, 'Calcular importe', 'Multiplicación', 'basico', 'solve_operation',
    'Un cuaderno cuesta 3,50 y compras 12. ¿Cuánto debes pagar en total?', '42', '12 × 3,50 = 42.', null, ['kind' => 'number']);

$l = L(3, 'División', 'Dividir y evitar el error #¡DIV/0!.',
    '<p>La barra <code>/</code> divide: <code>=B2/C2</code>. Dividir entre cero es imposible y Excel muestra el error <code>#¡DIV/0!</code>. Se puede evitar con una condición (<code>=SI(C2=0;0;B2/C2)</code>, módulo 5).</p>',
    '<p>Para el precio por unidad de un paquete: total en B2 y unidades en C2, <code>=B2/C2</code>. Si C2 está vacío o es 0, aparece #¡DIV/0!.</p>',
    S(['Total pagado', 'Unidades', 'Precio por unidad'], [[24, 6, '=A2/B2']]));
X(3, $l, 'Error de división', 'División', 'basico', 'multiple_choice',
    '¿Qué error muestra Excel si escribes =A1/B1 y la celda B1 contiene 0?',
    ['*#¡DIV/0!', '#¡VALOR!', '#¿NOMBRE?', '#¡REF!'], 'Dividir entre cero genera #¡DIV/0!. #¡VALOR! aparece con tipos de dato incorrectos y #¿NOMBRE? con nombres de función desconocidos.');

$l = L(3, 'Referencias relativas y absolutas', 'Qué pasa con las fórmulas al copiarlas.',
    '<p>Una referencia <strong>relativa</strong> (<code>B2</code>) cambia al copiar la fórmula: al bajar una fila se convierte en B3. Una <strong>absoluta</strong> (<code>$B$2</code>) permanece fija. Las <strong>mixtas</strong> fijan solo la columna (<code>$B2</code>) o solo la fila (<code>B$2</code>).</p><p>Con <kbd>F4</kbd> alternas entre los tipos mientras editas la fórmula.</p>',
    '<p>Si la tasa de IVA está en E1, para calcular el IVA de cada precio usa <code>=B2*$E$1</code>. Al copiarla hacia abajo, B2 pasa a B3, B4… pero $E$1 se mantiene.</p>',
    S(['Producto', 'Precio', '', '', 'IVA'], [['Lápiz', 10, '', '', 0.16], ['Cuaderno', 25, '', '', ''], ['Regla', 8, '', '', '']]));
X(3, $l, 'Referencia absoluta', 'Referencias', 'intermedio', 'complete_formula',
    'La tasa de IVA está en C2. Escribe la fórmula que calcule el IVA del precio de B3 de modo que, al copiarla hacia abajo, siga apuntando siempre a la tasa.',
    '=B3*$C$2||=$C$2*B3', 'La referencia a la tasa debe ser absoluta ($C$2) para que no cambie al copiar; el precio (B3) es relativo.', null,
    ['data' => S(['Producto', 'Precio', 'Tasa de IVA'], [['Lápiz', 10, 0.16], ['Cuaderno', 25, ''], ['Regla', 8, '']]), 'kind' => 'formula']);

$l = L(3, 'Operaciones combinadas', 'El orden en que Excel resuelve los operadores.',
    '<p>Excel respeta la jerarquía: primero <strong>paréntesis</strong>, luego <strong>potencias</strong>, después <strong>multiplicación y división</strong> (de izquierda a derecha) y por último <strong>suma y resta</strong>.</p><p>Por eso <code>=2+3*4</code> da 14, mientras que <code>=(2+3)*4</code> da 20.</p>',
    '<p>Para el promedio de dos notas, hay que usar paréntesis: <code>=(B2+C2)/2</code>. Sin ellos, <code>=B2+C2/2</code> divide solo C2.</p>',
    S(['Nota 1', 'Nota 2', 'Promedio'], [[14, 16, '=(A2+B2)/2']]));
X(3, $l, 'Jerarquía de operadores', 'Operaciones combinadas', 'intermedio', 'solve_operation',
    '¿Qué valor devuelve la fórmula =(8+4)/2*3-1?', '17', 'Primero el paréntesis: 12. Luego 12/2 = 6; 6*3 = 18; finalmente 18−1 = 17.', null, ['kind' => 'number']);

// ============ MÓDULO 4 · Funciones básicas ============
$ventasSem = S(['Día', 'Ventas'], [['Lunes', 120], ['Martes', 150], ['Miércoles', 90], ['Jueves', 200], ['Viernes', 175]]);

$l = L(4, 'Función SUMA', 'Sumar rangos de forma rápida.',
    '<p>Sintaxis: <code>=SUMA(número1; [número2]; …)</code>. Acepta rangos (<code>B2:B6</code>), celdas sueltas o ambos: <code>=SUMA(B2:B3;B5)</code>. Ignora el texto y las celdas vacías.</p><p>El botón <strong>Autosuma</strong> (Σ, atajo <kbd>Alt+=</kbd>) la escribe por ti.</p>',
    '<p>Para sumar las ventas de la semana en B2:B6: <code>=SUMA(B2:B6)</code>. Si agregas una venta dentro del rango, el total se actualiza.</p>', $ventasSem);
X(4, $l, 'Usar SUMA', 'SUMA', 'basico', 'complete_formula',
    'Escribe la fórmula que sume todas las ventas de la semana (B2:B6).', '=SUMA(B2:B6)',
    'SUMA recibe el rango B2:B6. El resultado es 120 + 150 + 90 + 200 + 175 = 735.', null, ['data' => $ventasSem, 'kind' => 'formula']);

$l = L(4, 'Función PROMEDIO', 'Calcular la media aritmética.',
    '<p>Sintaxis: <code>=PROMEDIO(número1; [número2]; …)</code>. Suma los valores y divide entre la cantidad de números. <strong>Ignora</strong> celdas vacías y texto (pero no los ceros).</p>',
    '<p>Para el promedio de las notas de B2:B5: <code>=PROMEDIO(B2:B5)</code>.</p>',
    S(['Alumno', 'Nota'], [['Ana', 15], ['Luis', 12], ['Marta', 18], ['Pedro', 11]]));
X(4, $l, 'Usar PROMEDIO', 'PROMEDIO', 'basico', 'solve_operation',
    '¿Qué valor devuelve =PROMEDIO(B2:B5) con los datos de la hoja?', '14', '(15 + 12 + 18 + 11) / 4 = 56 / 4 = 14.', null,
    ['data' => S(['Alumno', 'Nota'], [['Ana', 15], ['Luis', 12], ['Marta', 18], ['Pedro', 11]]), 'kind' => 'number']);

$l = L(4, 'Función MAX', 'Encontrar el valor más alto.',
    '<p>Sintaxis: <code>=MAX(número1; [número2]; …)</code>. Devuelve el mayor valor de un rango. Muy útil para detectar la venta más alta, la nota máxima o el precio más caro.</p>',
    '<p>Con las ventas en B2:B6, <code>=MAX(B2:B6)</code> devuelve 200, la venta del jueves.</p>', $ventasSem);
X(4, $l, 'Usar MAX', 'MAX', 'basico', 'complete_formula',
    'Escribe la fórmula que devuelva la venta más alta del rango B2:B6.', '=MAX(B2:B6)',
    'MAX devuelve el mayor valor del rango; en este caso, 200.', null, ['data' => $ventasSem, 'kind' => 'formula']);

$l = L(4, 'Función MIN', 'Encontrar el valor más bajo.',
    '<p>Sintaxis: <code>=MIN(número1; [número2]; …)</code>. Devuelve el menor valor del rango. Complementa a MAX para calcular rangos o detectar mínimos.</p>',
    '<p>Con temperaturas en B2:B6, <code>=MIN(B2:B6)</code> da la más baja de la semana.</p>',
    S(['Ciudad', 'Temperatura'], [['Caracas', 28], ['Maracay', 31], ['Mérida', 25], ['Maracaibo', 33], ['Valencia', 29]]));
X(4, $l, 'Usar MIN', 'MIN', 'basico', 'solve_operation',
    '¿Qué valor devuelve =MIN(B2:B6) con los datos de la hoja?', '25', 'El menor valor de 28, 31, 25, 33 y 29 es 25.', null,
    ['data' => S(['Ciudad', 'Temperatura'], [['Caracas', 28], ['Maracay', 31], ['Mérida', 25], ['Maracaibo', 33], ['Valencia', 29]]), 'kind' => 'number']);

$lect = S(['Lecturas'], [[10], ['N/D'], [25], [''], [40]]);
$l = L(4, 'Función CONTAR', 'Contar celdas que contienen números.',
    '<p>Sintaxis: <code>=CONTAR(valor1; [valor2]; …)</code>. Cuenta únicamente las celdas con <strong>números</strong> (y fechas). Ignora texto, errores y celdas vacías.</p>',
    '<p>En la lista de lecturas, <code>=CONTAR(A2:A6)</code> cuenta solo las celdas numéricas y no el texto “N/D”.</p>', $lect);
X(4, $l, 'Usar CONTAR', 'CONTAR', 'basico', 'solve_operation',
    '¿Qué valor devuelve =CONTAR(A2:A6) con los datos de la hoja?', '3', 'Solo hay tres números: 10, 25 y 40. El texto “N/D” y la celda vacía no se cuentan.', null,
    ['data' => $lect, 'kind' => 'number']);

$l = L(4, 'Función CONTARA', 'Contar celdas que no están vacías.',
    '<p>Sintaxis: <code>=CONTARA(valor1; [valor2]; …)</code>. Cuenta las celdas <strong>no vacías</strong>, sin importar si contienen texto o números. Sirve para saber cuántos registros hay en una lista.</p>',
    '<p>Con la misma lista de lecturas, <code>=CONTARA(A2:A6)</code> también cuenta el texto “N/D”, pero no la celda vacía.</p>', $lect);
X(4, $l, 'Usar CONTARA', 'CONTARA', 'basico', 'solve_operation',
    '¿Qué valor devuelve =CONTARA(A2:A6) con los datos de la hoja?', '4', 'CONTARA cuenta las celdas no vacías: 10, “N/D”, 25 y 40. Son 4.', null,
    ['data' => $lect, 'kind' => 'number']);
