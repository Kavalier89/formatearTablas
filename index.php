<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<form action="index.php" method="post">


<?php
/* El siguiente codigo nos permitirá acceder a una base de datos, mostrar todas las tablas en un select y descargar el archivo XML de cada tabla, configurando los parametros se pueden pasar también a CSV o JSON. */



// Conectar a la base de datos
$conn = mysqli_connect("localhost", "root", "Matins", "premiership");

// Obtener la lista de tablas de la base de datos
$tables = mysqli_query($conn, "SHOW TABLES");

// Crear el select y mostrar las opciones de tabla
echo "<select name='table'>";
while ($row = mysqli_fetch_array($tables)) {
    echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
}
echo "</select>";

// Cerrar la conexión a la base de datos
mysqli_close($conn);


if (isset($_POST['submit']) && $_POST['formato'] == 'xml') {
    // Conectar a la base de datos
    $conn = mysqli_connect("localhost", "root", "Matins", "premiership");

    // Obtener el nombre de la tabla seleccionada
    $table = $_POST['table'];

    // Realizar una consulta a la base de datos
    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);

    // Crear un objeto SimpleXMLElement
    $xml = new SimpleXMLElement('<data/>');

    // Recorre el arreglo de datos y agrega elementos al objeto SimpleXMLElement
    while ($row = mysqli_fetch_assoc($result)) {
        $item = $xml->addChild('item');
        foreach ($row as $key => $value) {
            $item->addChild($key, $value);
        }
    }

    // Guarda el archivo XML con el nombre de la tabla seleccionada
    $xml->asXML($table . ".xml");
}
if (isset($_POST['submit']) && $_POST['formato'] == 'json') {
    // Conectar a la base de datos
    $conn = mysqli_connect("localhost", "root", "Matins", "premiership");

    // Obtener el nombre de la tabla seleccionada
    $table = $_POST['table'];

    // Realizar una consulta a la base de datos
    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);

    // Crear un objeto para almacenar los datos en formato JSON
    $jsonArray = array();

    // Recorre el arreglo de datos y agrega elementos al arreglo JSON
    while ($row = mysqli_fetch_assoc($result)) {
        // Agrega cada fila como un elemento del arreglo JSON
        $jsonArray[] = $row;
    }

    // Guarda el archivo JSON con el nombre de la tabla seleccionada
    file_put_contents($table . ".json", json_encode($jsonArray, JSON_PRETTY_PRINT));

}
  if (isset($_POST['submit']) && $_POST['formato'] == 'csv') {
        // Conectar a la base de datos
        $conn = mysqli_connect("localhost", "root", "Matins", "premiership");
    
        // Obtener el nombre de la tabla seleccionada
        $table = $_POST['table'];
    
        // Realizar una consulta a la base de datos
        $query = "SELECT * FROM $table";
        $result = mysqli_query($conn, $query);
    
        // Crear un archivo CSV y escribir los datos
        $csvFile = fopen($table . ".csv", 'w');
    
        // Escribir los nombres de las columnas como encabezado
        $header = array();
        while ($column = mysqli_fetch_field($result)) {
            $header[] = $column->name;
        }
        fputcsv($csvFile, $header);
    
        // Recorrer los datos y escribir cada fila en el archivo CSV
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($csvFile, $row);
        }
    
        // Cerrar el archivo CSV
        fclose($csvFile);
    
        // Cerrar la conexión a la base de datos
        mysqli_close($conn);
    }

?>




<button name="submit">Crea la copia el archivo</button>
<select name="formato">Escoje el formato<option value="csv">CSV</option><option value="xml">XML</option><option value="json">JSON</option></select>

</form>


</body>
</html>