<?php
// Ejercicios adicionales del banco, retos (Excel Challenge) y casos prácticos.

$vsem = S(['Día', 'Ventas'], [['Lunes', 120], ['Martes', 150], ['Miércoles', 90], ['Jueves', 200], ['Viernes', 175]]);
$vend = S(['Vendedor', 'Ventas'], [['Ana', 500], ['Luis', 300], ['Ana', 450], ['Luis', 200], ['Marta', 600]]);
$inv = S(['Producto', 'Existencia', 'Mínimo'], [['Arroz', 8, 10], ['Harina', 25, 12], ['Aceite', 4, 6]]);

// ---- Módulo 1 ----
X(1, null, 'Inicio de una fórmula', 'Fórmulas', 'basico', 'true_false', 'Toda fórmula en Excel debe comenzar con el signo igual (=).', ['*Verdadero', 'Falso'], 'Sin el signo = Excel interpreta lo escrito como texto.');
X(1, null, 'Dirección de celda', 'Celdas y rangos', 'basico', 'multiple_choice', '¿Cuál es la dirección de la celda ubicada en la columna C, fila 5?', ['*C5', '5C', 'C-5', 'F3'], 'Primero va la letra de la columna y después el número de la fila: C5.');
X(1, null, 'Leer una celda', 'Celdas y rangos', 'basico', 'analyze_table', 'Observa la hoja. ¿Qué valor contiene la celda C3?', '2.5', 'C3 está en la columna Precio y en la fila 3, que corresponde al Cuaderno: 2,5.', null,
    ['data' => S(['Producto', 'Cantidad', 'Precio'], [['Lápiz', 10, 0.4], ['Cuaderno', 5, 2.5], ['Borrador', 8, 0.75]]), 'kind' => 'number']);
// ---- Módulo 2 ----
X(2, null, 'Formato moneda', 'Formato de números', 'basico', 'multiple_choice', '¿Qué formato de número debes aplicar para mostrar 1500 como $1.500,00?', ['*Moneda', 'Fecha', 'Porcentaje', 'Texto'], 'El formato Moneda agrega el símbolo y los decimales, sin cambiar el valor.');
X(2, null, 'Formato y valor', 'Formato de números', 'basico', 'true_false', 'Aplicar el formato Moneda a un número cambia su valor real en los cálculos.', ['Verdadero', '*Falso'], 'El formato solo cambia la apariencia; el valor almacenado es el mismo.');
// ---- Módulo 3 ----
X(3, null, 'Potencias', 'Operaciones combinadas', 'intermedio', 'solve_operation', '¿Qué valor devuelve la fórmula =2^3+10/5?', '10', 'Primero la potencia (2^3 = 8) y la división (10/5 = 2); luego la suma: 8 + 2 = 10.', null, ['kind' => 'number']);
X(3, null, 'Calcular un saldo', 'Resta', 'basico', 'complete_formula', 'Con los ingresos en B2 y los gastos en B3, escribe la fórmula que calcule el saldo.', '=B2-B3', 'El saldo es ingresos menos gastos.', null,
    ['data' => S(['Concepto', 'Monto'], [['Ingresos', 2500], ['Gastos', 1875]]), 'kind' => 'formula']);
X(3, null, 'Precio con descuento', 'Operaciones combinadas', 'intermedio', 'practical', 'Un producto cuesta 80 y tiene un descuento del 15 %. ¿Cuál es el precio final?', '68', 'Descuento: 80 × 0,15 = 12. Precio final: 80 − 12 = 68. En Excel: =80*(1-0,15).', null, ['kind' => 'number']);
// ---- Módulo 4 ----
X(4, null, 'Identificar CONTAR', 'Funciones básicas', 'basico', 'identify_function', '¿Qué función cuenta cuántas celdas de un rango contienen números?', ['*CONTAR', 'CONTARA', 'SUMA', 'MAX'], 'CONTAR solo considera celdas numéricas. CONTARA cuenta todas las celdas no vacías.');
X(4, null, 'Identificar MIN', 'Funciones básicas', 'basico', 'identify_function', 'Necesitas conocer el precio más bajo de una lista. ¿Qué función usas?', ['*MIN', 'MAX', 'PROMEDIO', 'CONTARA'], 'MIN devuelve el valor más pequeño de un rango.');
X(4, null, 'Promedio de ventas', 'PROMEDIO', 'basico', 'complete_formula', 'Escribe la fórmula que calcule el promedio de las ventas del rango B2:B6.', '=PROMEDIO(B2:B6)', 'PROMEDIO(B2:B6) = 735 / 5 = 147.', null, ['data' => $vsem, 'kind' => 'formula']);
X(4, null, 'Días con buenas ventas', 'Análisis de tablas', 'intermedio', 'analyze_table', 'Analiza la hoja. ¿En cuántos días las ventas fueron superiores a 100?', '4', 'Superan 100: lunes (120), martes (150), jueves (200) y viernes (175). El miércoles (90) no.', null, ['data' => $vsem, 'kind' => 'number']);
X(4, null, 'Nota más alta', 'MAX', 'intermedio', 'practical', 'Un docente tiene las notas de 30 alumnos en el rango B2:B31. Escribe la fórmula para obtener la nota más alta.', '=MAX(B2:B31)', 'MAX devuelve el mayor valor del rango B2:B31.', null, ['kind' => 'formula']);
// ---- Módulo 5 ----
X(5, null, 'Contar con criterio', 'CONTAR.SI', 'intermedio', 'complete_formula', 'Escribe la fórmula que cuente cuántas ventas del rango B2:B6 son mayores que 150.', '=CONTAR.SI(B2:B6;">150")', 'El criterio va entre comillas: ">150". Superan 150 las ventas del jueves (200) y del viernes (175): son 2.', null, ['data' => $vsem, 'kind' => 'formula']);
X(5, null, 'Sumar por vendedor', 'SUMAR.SI', 'avanzado', 'complete_formula', 'Escribe la fórmula que sume las ventas (B2:B6) del vendedor "Luis" (columna A2:A6).', '=SUMAR.SI(A2:A6;"Luis";B2:B6)', 'SUMAR.SI(rango; criterio; rango_suma). Luis suma 300 + 200 = 500.', null, ['data' => $vend, 'kind' => 'formula']);
X(5, null, 'Resultado de SI', 'SI', 'intermedio', 'multiple_choice', '¿Qué devuelve la fórmula =SI(5>3;"Sí";"No")?', ['*Sí', 'No', 'VERDADERO', '#¡VALOR!'], 'La condición 5>3 es verdadera, por lo que devuelve el segundo argumento: "Sí".');
X(5, null, 'Comisión por ventas', 'SI', 'avanzado', 'practical', 'Una empresa paga el 10 % de comisión si las ventas (B2) son de 1000 o más, y 5 % si son menores. Escribe la fórmula para C2.',
    '=SI(B2>=1000;B2*0.1;B2*0.05)||=SI(B2>=1000;B2*10%;B2*5%)||=B2*SI(B2>=1000;0.1;0.05)||=SI(B2>=1000;B2*0,1;B2*0,05)', 'Si B2>=1000 se multiplica por 0,10; en caso contrario por 0,05.', 25, ['kind' => 'formula']);
X(5, null, 'Criterios en CONTAR.SI', 'CONTAR.SI', 'intermedio', 'true_false', 'CONTAR.SI permite usar criterios con operadores, como ">10".', ['*Verdadero', 'Falso'], 'Los criterios pueden ser valores o comparaciones entre comillas.');
// ---- Módulo 6 ----
X(6, null, 'Primera columna en BUSCARV', 'BUSCARV', 'intermedio', 'true_false', 'En BUSCARV, el valor buscado debe encontrarse en la primera columna de la matriz.', ['*Verdadero', 'Falso'], 'BUSCARV siempre busca en la primera columna de la matriz y devuelve datos de columnas a su derecha.');
X(6, null, 'Atajo de filtros', 'Filtros', 'basico', 'multiple_choice', '¿Qué atajo activa o desactiva los filtros de una tabla?', ['*Ctrl+Mayús+L', 'Ctrl+F', 'Ctrl+K', 'Alt+F4'], 'Ctrl+Mayús+L alterna el filtro automático.');
X(6, null, 'Limitar valores', 'Validación de datos', 'intermedio', 'multiple_choice', '¿Qué herramienta permite que una celda solo acepte números enteros entre 1 y 20?', ['*Validación de datos', 'Formato condicional', 'Ordenar', 'Filtro'], 'La validación de datos restringe lo que se puede escribir y muestra un mensaje de error.');
// ---- Módulo 7 ----
X(7, null, 'Partes de un todo', 'Gráficos circulares', 'basico', 'multiple_choice', '¿Qué gráfico usarías para mostrar qué porción del presupuesto se destina a cada área?', ['*Gráfico circular', 'Gráfico de líneas', 'Gráfico de dispersión', 'Ninguno'], 'El circular muestra la proporción de cada parte respecto del total.');
X(7, null, 'Caída de ventas', 'Interpretación', 'intermedio', 'analyze_table', 'Según la tabla, ¿cuántas unidades cayeron las ventas de febrero a marzo?', '300', 'Febrero: 1200; marzo: 900. La caída fue de 1200 − 900 = 300.', null,
    ['data' => S(['Mes', 'Ventas'], [['Enero', 1000], ['Febrero', 1200], ['Marzo', 900], ['Abril', 1500]]), 'kind' => 'number']);
// ---- Módulo 8 ----
X(8, null, 'Valor del inventario', 'Control de inventario', 'intermedio', 'practical', '¿Cuál es el valor total del inventario (existencia × costo de cada producto)?', '136', 'Tornillos: 40×2,5 = 100; Tuercas: 30×1,2 = 36. Total: 136.', null,
    ['data' => S(['Producto', 'Existencia', 'Costo'], [['Tornillos', 40, 2.5], ['Tuercas', 30, 1.2]]), 'kind' => 'number']);
X(8, null, 'Bonificaciones', 'Registro de trabajadores', 'avanzado', 'practical', 'La empresa paga un bono del 8 % a los trabajadores que ganan menos de 1000. ¿Cuánto se paga en bonos en total?', '136', 'Cumplen: Carla (900) y Mario (800). Bonos: 72 + 64 = 136.', null,
    ['data' => S(['Nombre', 'Salario'], [['Carla', 900], ['Diego', 1100], ['Elena', 1000], ['Mario', 800]]), 'kind' => 'number']);
X(8, null, 'Promedio de calificaciones', 'Calificaciones', 'basico', 'practical', 'Las notas de un alumno son 12, 14, 16 y 18. ¿Cuál es su promedio?', '15', '(12 + 14 + 16 + 18) / 4 = 60 / 4 = 15.', null, ['kind' => 'number']);
X(8, null, 'Productos a reponer', 'Control de inventario', 'avanzado', 'analyze_table', 'Analiza la hoja. ¿Cuántos productos tienen una existencia menor que el mínimo?', '2', 'Arroz (8 < 10) y Aceite (4 < 6) necesitan reposición. Harina (25 ≥ 12) no.', null, ['data' => $inv, 'kind' => 'number']);
X(8, null, 'Función para contar reprobados', 'Calificaciones', 'intermedio', 'identify_function', 'Quieres saber cuántos alumnos reprobaron en una lista con la columna “Condición”. ¿Qué función usas?', ['*CONTAR.SI', 'SUMAR.SI', 'MAX', 'PROMEDIO'], 'CONTAR.SI cuenta las celdas que cumplen un criterio, por ejemplo "Reprobado".');

// ---------------------------------------------------------------------------
// RETOS
// ---------------------------------------------------------------------------
$ch = [
    ['Reto 1: Total de una compra', 'basico', 'Estás haciendo las compras de la semana en el supermercado y quieres saber cuánto vas a pagar antes de llegar a la caja.',
        'Calcular el total de la compra.', S(['Producto', 'Cantidad', 'Precio unitario'], [['Arroz', 2, 1.8], ['Aceite', 1, 4.5], ['Leche', 6, 1.1], ['Huevos', 2, 3.2]]),
        'Calcula el importe de cada producto (cantidad × precio) y súmalos. Puedes usar =SUMAPRODUCTO(B2:B5;C2:C5). Escribe el total.', 5, 20, 'number', '21.1', 0.01,
        'Multiplica cantidad por precio en cada fila y suma los resultados.', '¡Correcto! 3,6 + 4,5 + 6,6 + 6,4 = 21,10.', 'No coincide. Recuerda multiplicar la cantidad por el precio de cada producto antes de sumar.'],
    ['Reto 2: Promedio de calificaciones', 'basico', 'La profesora necesita el promedio del grupo en el primer parcial para presentarlo en la reunión de docentes.',
        'Determinar el promedio de las notas del curso.', S(['Alumno', 'Nota'], [['Ana', 14], ['Luis', 17], ['Marta', 12], ['Pedro', 19], ['Sofía', 15], ['Juan', 10], ['Rosa', 16], ['Mario', 13]]),
        'Usa la función PROMEDIO sobre el rango B2:B9 y escribe el resultado.', 5, 20, 'number', '14.5', 0.01,
        'Usa =PROMEDIO(B2:B9).', '¡Excelente! La suma es 116 y entre 8 alumnos da 14,5.', 'Revisa el rango: deben entrar las 8 notas y ninguna celda de texto.'],
    ['Reto 3: La venta más alta', 'basico', 'Una tienda registra las ventas de la semana de siete vendedores y el gerente desea premiar al que logró la venta más alta.',
        'Identificar el valor de la venta más alta.', S(['Vendedor', 'Ventas'], [['Ana', 1250], ['Luis', 980], ['Marta', 1420], ['Pedro', 1105], ['Sofía', 1390], ['Juan', 870], ['Rosa', 1315]]),
        'Usa la función MAX sobre el rango B2:B8 y escribe el valor.', 5, 25, 'number', '1420', 0.01,
        'La función MAX devuelve el valor mayor de un rango.', '¡Correcto! La mayor venta es de Marta: 1420.', 'Ese no es el mayor valor. Usa =MAX(B2:B8).'],
    ['Reto 4: ¿Quiénes aprobaron?', 'intermedio', 'En una lista de notas, la nota mínima aprobatoria es 10. El docente quiere saber cuántos alumnos aprobaron.',
        'Determinar cuántos alumnos aprobaron (nota ≥ 10).', S(['Alumno', 'Nota'], [['Ana', 15], ['Luis', 8], ['Marta', 10], ['Pedro', 9], ['Sofía', 18], ['Juan', 7], ['Rosa', 12]]),
        'Crea una columna con =SI(B2>=10;"Aprobado";"Reprobado") y cuenta con CONTAR.SI, o usa directamente =CONTAR.SI(B2:B8;">=10"). Escribe la cantidad.', 10, 35, 'number', '4', 0,
        'Recuerda que la nota 10 también aprueba (>=).', '¡Muy bien! Aprobaron Ana, Marta, Sofía y Rosa: 4 alumnos.', 'Verifica la condición: la nota 10 ya es aprobatoria.'],
    ['Reto 5: Ingresos de una empresa', 'intermedio', 'Una pequeña empresa vende un producto cuyo precio cambió en marzo. Debe calcular los ingresos totales del primer cuatrimestre.',
        'Calcular los ingresos totales (unidades × precio) de los cuatro meses.', S(['Mes', 'Unidades', 'Precio unitario'], [['Enero', 120, 15], ['Febrero', 150, 15], ['Marzo', 90, 18], ['Abril', 200, 18]]),
        'Calcula el ingreso de cada mes (unidades × precio) y suma los cuatro. Escribe el total.', 10, 40, 'number', '9270', 0.01,
        'Cada mes tiene su propio precio; no multipliques el total de unidades por un solo precio.', '¡Perfecto! 1800 + 2250 + 1620 + 3600 = 9270.', 'Revisa que cada mes use su precio. Enero y febrero: 15; marzo y abril: 18.'],
    ['Reto 6: Organiza un inventario', 'avanzado', 'Una ferretería actualiza su inventario y necesita conocer el valor total de las existencias para el balance de fin de mes.',
        'Calcular el valor total del inventario (existencia × costo).', S(['Producto', 'Existencia', 'Costo', 'Mínimo'], [['Tornillos', 120, 0.25, 100], ['Tuercas', 80, 0.3, 100], ['Clavos', 300, 0.1, 150], ['Arandelas', 60, 0.15, 80], ['Martillos', 15, 12, 10], ['Alicates', 8, 9.5, 10]]),
        'Multiplica existencia por costo en cada fila y suma (SUMAPRODUCTO(B2:B7;C2:C7)). Escribe el valor total.', 15, 50, 'number', '349', 0.01,
        'Valor por producto = existencia × costo. Luego suma todos.', '¡Genial! 30 + 24 + 30 + 9 + 180 + 76 = 349.', 'No coincide. Comprueba que usas la columna de costo y no la de mínimo.'],
    ['Reto 7: Comisiones por ventas', 'avanzado', 'La empresa paga una comisión del 5 % únicamente sobre las ventas superiores a 1000. Hay que calcular el total de comisiones a pagar.',
        'Calcular el total de comisiones (solo ventas mayores a 1000).', S(['Vendedor', 'Ventas'], [['Ana', 1250], ['Luis', 980], ['Marta', 1420], ['Pedro', 1105], ['Sofía', 1390], ['Juan', 870], ['Rosa', 1315]]),
        'Suma solo las ventas mayores a 1000 con SUMAR.SI(B2:B8;">1000") y multiplica por 5 %.', 15, 50, 'number', '324', 0.01,
        'Usa SUMAR.SI con el criterio ">1000" y multiplica por 0,05.', '¡Correcto! Las ventas válidas suman 6480 y el 5 % es 324.', 'Revisa el criterio: solo cuentan las ventas mayores a 1000, y la comisión es 5 %.'],
    ['Reto 8: Promedio por sección', 'avanzado', 'Tres secciones presentaron el mismo examen. La coordinación quiere comparar el rendimiento de la sección B.',
        'Calcular el promedio de notas de la sección B.', S(['Sección', 'Nota'], [['A', 14], ['B', 12], ['A', 16], ['C', 9], ['B', 15], ['C', 11], ['A', 18], ['B', 13], ['C', 10]]),
        'Usa =PROMEDIO.SI(A2:A10;"B";B2:B10). Escribe el resultado con dos decimales.', 15, 45, 'number', '13.33', 0.01,
        'PROMEDIO.SI promedia solo las filas cuyo criterio coincide.', '¡Muy bien! (12 + 15 + 13) / 3 = 13,33.', 'Verifica que promedias solo las notas de la sección B.'],
];
foreach ($ch as $i => [$title, $diff, $sit, $obj, $data, $instr, $min, $pts, $kind, $ans, $tol, $hint, $ok, $fail]) {
    ins('challenges', ['title' => $title, 'difficulty' => $diff, 'situation' => $sit, 'objective' => $obj, 'data_json' => js($data),
        'instructions' => $instr, 'est_minutes' => $min, 'points' => $pts, 'answer_kind' => $kind, 'correct_answer' => $ans,
        'tolerance' => $tol, 'hint' => $hint, 'feedback_ok' => $ok, 'feedback_fail' => $fail, 'position' => $i + 1, 'status' => 'active']);
}

// ---------------------------------------------------------------------------
// CASOS PRÁCTICOS
// ---------------------------------------------------------------------------
$q = fn($label, $answer, $kind = 'number', $tol = 0.01) => ['label' => $label, 'answer' => $answer, 'kind' => $kind, 'tolerance' => $tol];
$cases = [
    ['Administración', 'Control de gastos del hogar', 'Una familia quiere conocer en qué se va su dinero cada mes. Llevan un registro simple con el concepto, la categoría y el monto de cada gasto.',
        'Analiza el registro de gastos de octubre y responde a las preguntas de la administración familiar.',
        S(['Concepto', 'Categoría', 'Monto'], [['Alquiler', 'Vivienda', 450], ['Electricidad', 'Servicios', 62.5], ['Agua', 'Servicios', 18], ['Internet', 'Servicios', 30], ['Mercado', 'Alimentación', 280], ['Transporte', 'Transporte', 75], ['Cine', 'Ocio', 40]]),
        'Usa SUMA y SUMAR.SI en Excel (o en cálculo manual) sobre los datos y escribe los resultados.',
        'Resultados exactos; uso correcto de funciones de suma por criterio.', 'platform',
        [$q('Total de gastos del mes', 955.5), $q('Total gastado en la categoría Servicios', 110.5), $q('Concepto con el mayor gasto', 'Alquiler', 'text'), $q('Si el ingreso del mes fue 1000, ¿cuánto dinero sobra?', 44.5)], 50],
    ['Administración', 'Presupuesto mensual (entrega en Excel)', 'Una microempresa de servicios prepara su presupuesto mensual y debe compararlo con lo realmente ejecutado.',
        'Completa la plantilla: calcula la diferencia entre lo presupuestado y lo real, la fila de totales y agrega un gráfico de columnas comparando ambos montos.',
        S(['Concepto', 'Presupuestado', 'Real', 'Diferencia'], [['Alquiler del local', 600, 600, ''], ['Servicios', 150, 172, ''], ['Publicidad', 200, 140, ''], ['Materiales', 350, 410, ''], ['Transporte', 120, 95, ''], ['Totales', '', '', '']]),
        'Descarga la plantilla, completa la columna Diferencia (=B2-C2), calcula los totales con SUMA, aplica formato de moneda y agrega un gráfico. Sube el archivo (.xlsx).',
        'Fórmulas correctas (40 %), totales (20 %), formato (20 %) y gráfico (20 %).', 'file', null, 60],
    ['Ventas', 'Registro de ventas de tecnología', 'Una tienda de tecnología registra las unidades vendidas y el precio de cada producto durante la semana.',
        'Calcula el importe de cada producto y analiza los resultados de la semana.',
        S(['Producto', 'Unidades', 'Precio unitario'], [['Laptop', 3, 650], ['Mouse', 25, 12], ['Teclado', 15, 28], ['Monitor', 6, 140]]),
        'Importe = unidades × precio. Con los importes responde las preguntas.',
        'Resultados exactos.', 'platform',
        [$q('Importe total de ventas de la semana', 3510), $q('Total de unidades vendidas', 49), $q('Importe del producto más vendido en unidades (Mouse)', 300), $q('¿Qué producto generó el mayor importe?', 'Laptop', 'text')], 50],
    ['Inventario', 'Control de existencias (entrega en Excel)', 'Un almacén de materiales de oficina necesita saber el valor de lo que tiene y qué productos debe reponer.',
        'Completa la plantilla: calcula el valor de cada producto, el valor total del inventario y una columna que indique "Reponer" u "OK".',
        S(['Producto', 'Existencia', 'Costo unitario', 'Mínimo', 'Valor', 'Estado'], [['Resmas de papel', 40, 4.5, 50, '', ''], ['Bolígrafos (caja)', 18, 6, 10, '', ''], ['Carpetas', 120, 0.8, 100, '', ''], ['Tóner', 5, 38, 6, '', ''], ['Grapadoras', 14, 7.5, 8, '', '']]),
        'Valor = existencia × costo. Estado: =SI(B2<D2;"Reponer";"OK"). Aplica formato condicional para resaltar "Reponer" y calcula el valor total con SUMA. Sube el archivo.',
        'Cálculos correctos (50 %), condición con SI (30 %), formato condicional y total (20 %).', 'file', null, 60],
    ['Recursos Humanos', 'Nómina y promedios salariales', 'El departamento de recursos humanos de una empresa mediana debe presentar un informe de sueldos al gerente general.',
        'Analiza la nómina del área administrativa y responde.',
        S(['Nombre', 'Cargo', 'Salario base'], [['Carla', 'Analista', 1200], ['Diego', 'Analista', 1250], ['Elena', 'Gerente', 2400], ['Mario', 'Asistente', 800], ['Lucía', 'Asistente', 850]]),
        'Usa SUMA, PROMEDIO y CONTAR.SI como apoyo.',
        'Resultados exactos.', 'platform',
        [$q('Total de la nómina mensual', 6500), $q('Salario promedio de toda la plantilla', 1300), $q('Salario promedio de los Asistentes', 825), $q('¿Cuántos empleados ganan más que el promedio de la plantilla?', 1, 'number', 0)], 50],
    ['Educación', 'Registro de calificaciones del curso', 'Un docente de Paquetes Informáticos registra los tres parciales de sus alumnos y debe calcular promedios y aprobados (nota mínima 10).',
        'Calcula el promedio de cada alumno y los indicadores del curso.',
        S(['Alumno', 'Parcial 1', 'Parcial 2', 'Parcial 3'], [['Ana', 14, 16, 18], ['Luis', 10, 8, 9], ['Marta', 17, 18, 19], ['Pedro', 12, 11, 13], ['Sofía', 9, 10, 8]]),
        'Calcula =PROMEDIO(B2:D2) para cada alumno y responde.',
        'Resultados exactos.', 'platform',
        [$q('Promedio de Marta', 18), $q('Cantidad de alumnos aprobados (promedio ≥ 10)', 3, 'number', 0), $q('Promedio general del grupo (promedio de los cinco promedios)', 12.8)], 50],
];
foreach ($cases as [$cat, $title, $ctx, $prob, $data, $instr, $crit, $mode, $qs, $pts]) {
    ins('practical_cases', ['category' => $cat, 'title' => $title, 'context' => $ctx, 'problem' => $prob, 'data_json' => js($data),
        'instructions' => $instr, 'criteria' => $crit, 'mode' => $mode, 'questions_json' => js($qs), 'points' => $pts, 'status' => 'active']);
}
