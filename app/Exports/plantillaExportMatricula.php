<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class plantillaExportMatricula implements WithHeadings, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        $cabecera = [
            'Correo',
            'Celular',
        ];

        $guia = [
            'correo@gmail.com',
            '987987987',
        ];

        return array($cabecera,$guia);
    }

     //darle stylo a las columnas o filas
     public function styles(Worksheet $sheet)
     {
         return [
         1    => [
                 'font' => [
                     'bold' => true,
                     'color' => ['argb' => 'ffffff'],
                 ],
                 'fill' => [
                     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                     'color' => ['argb' => 'FF1d428a'],
                 ],                
             ],                
         ];
     }
 
     //dar un tamaño de espacio automatico a cada columna del excel
     public function AutoSize(): array
     {
         return [];
     }

      /**
     * Aplica la validación de datos a un rango específico de celdas.
     *
     * @param Worksheet $sheet
     * @param string $column
     * @param string $formula1
     * @param int $startRow
     * @param int $endRow
     * @return void
     */
}
