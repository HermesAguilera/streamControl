<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

echo "=== DATOS DEL CLIENTE ===" . PHP_EOL;

$cliente = App\Models\Cliente::with('persona')->first();

if ($cliente) {
    echo 'ID: ' . $cliente->id . PHP_EOL;
    echo 'Numero Cliente: ' . var_export($cliente->numero_cliente, true) . PHP_EOL;
    echo 'RTN: ' . var_export($cliente->rtn, true) . PHP_EOL;
    echo 'Persona ID: ' . $cliente->persona_id . PHP_EOL;
    echo 'Empresa ID: ' . var_export($cliente->empresa_id, true) . PHP_EOL;
    if ($cliente->persona) {
        echo 'Persona Nombre: ' . $cliente->persona->primer_nombre . PHP_EOL;
    }
    
    echo PHP_EOL . "=== VERIFICANDO CONDICIONES ===" . PHP_EOL;
    echo 'isset numero_cliente: ' . var_export(isset($cliente->numero_cliente), true) . PHP_EOL;
    echo 'numero_cliente !== null: ' . var_export($cliente->numero_cliente !== null, true) . PHP_EOL;
    echo 'numero_cliente !== "": ' . var_export($cliente->numero_cliente !== '', true) . PHP_EOL;
    
    echo 'isset rtn: ' . var_export(isset($cliente->rtn), true) . PHP_EOL;
    echo 'rtn !== null: ' . var_export($cliente->rtn !== null, true) . PHP_EOL;
    echo 'rtn !== "": ' . var_export($cliente->rtn !== '', true) . PHP_EOL;
} else {
    echo 'No se encontró ningún cliente.' . PHP_EOL;
}
