<?php

namespace App\Imports;

use App\Models\Cliente;
use App\Models\Evento;
use App\Models\EventoCliente;
use App\Models\Importacion;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ClientesImport implements ToModel, WithStartRow
{

    private $rows = 0;
    public $errors = [];

    public function __construct(Importacion $importacion, $evento){
        $this->importacion = $importacion;
        $this->evento = $evento;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        if (!isset($row[0])) {
            return null;
        }

        ++$this->rows;

        try {
            // Verificar si la fila tiene un valor en la columna requerida
            if (empty($row[3])) {
                throw new Exception('El campo "Correo" está vacío');
            }

            // Verifica si el email ya está registrado
            if (Cliente::where([['idcampania', $this->importacion->idcampania],['email', $row[3]]])->exists()) {
                throw new Exception('Correo duplicado, ya existe en el sistema.');
            }

            // Verificar errores
            if (!empty($this->errors)) {
                return null;
            }

            return  new Cliente([
                'nombres' => $row[0],
                'apellidopaterno' => $row[1],
                'apellidomaterno' => $row[2],
                'email' => $row[3],
                'telefono' => $row[4],
                'carrera' => $row[5],
                'tipodocumento' => $row[6],
                'dni' => $row[7],
                'grado' => $row[8],
                'colegio' => $row[9],
                'sede_destino' => $row[10],
                'anioegreso' => $row[11],
                'es_padre_familia' => $row[12],
                'procedencia' => $row[13],
                'campaign_content' => $row[14],
                'campaign_medium' => $row[15],
                'campaign_name' => $row[16],
                'campaign_source' => $row[17],
                'campaign_term' => $row[18],
                'uuidimportacion' => $this->importacion->uuidimportacion,
                'idcampania' => $this->importacion->idcampania,
            ]);

    } catch (Exception $e) {
        // Almacenar el error y la fila en la que ocurrió
        $this->errors[] = [
            'row' => $this->rows  + 1,
            'message' => $e->getMessage()
        ];
        return null;
    }

        /* $eventos = new EventoCliente([
            'idcliente' => $cliente->idcliente,
            'idevento' => $this->evento,
        ]); */

        //return dd([$cliente->idcliente, $eventos]);

        //return dd($this->idevento);
    }
    public function startRow(): int
    {
        return 2;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }


}
