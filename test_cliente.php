<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

echo "=== CREANDO Y VERIFICANDO CLIENTE ===" . PHP_EOL;

// Crear una persona primero
$persona = App\Models\Persona::create([
    'primer_nombre' => 'Juan',
    'primer_apellido' => 'Pérez',
    'dni' => '1234567890123',
    'tipo_persona' => 'natural',
    'direccion' => 'Test Address',
    'telefono' => '1234-5678',
    'sexo' => 'MASCULINO',
    'fecha_nacimiento' => '1990-01-01',
    'municipio_id' => 1,
    'pais_id' => 1,
    'empresa_id' => 1,
]);

echo "Persona creada - ID: " . $persona->id . PHP_EOL;

// Crear el cliente
$cliente = App\Models\Cliente::create([
    'persona_id' => $persona->id,
    'empresa_id' => 1,
    'rtn' => '1234567890123456',
]);

echo "Cliente creado - ID: " . $cliente->id . PHP_EOL;

// Verificar los datos
$clienteVerificado = App\Models\Cliente::find($cliente->id);

echo PHP_EOL . "=== VERIFICANDO DATOS ===" . PHP_EOL;
echo 'ID: ' . $clienteVerificado->id . PHP_EOL;
echo 'Numero Cliente: ' . var_export($clienteVerificado->numero_cliente, true) . PHP_EOL;
echo 'RTN: ' . var_export($clienteVerificado->rtn, true) . PHP_EOL;
echo 'Persona ID: ' . $clienteVerificado->persona_id . PHP_EOL;
echo 'Empresa ID: ' . var_export($clienteVerificado->empresa_id, true) . PHP_EOL;

// Verificar las condiciones de la vista
echo PHP_EOL . "=== CONDICIONES DE LA VISTA ===" . PHP_EOL;
echo 'isset numero_cliente: ' . var_export(isset($clienteVerificado->numero_cliente), true) . PHP_EOL;
echo 'numero_cliente !== null: ' . var_export($clienteVerificado->numero_cliente !== null, true) . PHP_EOL;
echo 'numero_cliente !== "": ' . var_export($clienteVerificado->numero_cliente !== '', true) . PHP_EOL;

echo 'isset rtn: ' . var_export(isset($clienteVerificado->rtn), true) . PHP_EOL;
echo 'rtn !== null: ' . var_export($clienteVerificado->rtn !== null, true) . PHP_EOL;
echo 'rtn !== "": ' . var_export($clienteVerificado->rtn !== '', true) . PHP_EOL;

// Simular lo que haría la vista
echo PHP_EOL . "=== RESULTADO DE LA VISTA ===" . PHP_EOL;

$numeroClienteResult = (isset($clienteVerificado->numero_cliente) && $clienteVerificado->numero_cliente !== null && $clienteVerificado->numero_cliente !== '') 
    ? $clienteVerificado->numero_cliente 
    : 'No especificado';
echo 'Número de Cliente mostraría: ' . $numeroClienteResult . PHP_EOL;

$rtnResult = (isset($clienteVerificado->rtn) && $clienteVerificado->rtn !== null && $clienteVerificado->rtn !== '') 
    ? $clienteVerificado->rtn 
    : 'No especificado';
echo 'RTN mostraría: ' . $rtnResult . PHP_EOL;
