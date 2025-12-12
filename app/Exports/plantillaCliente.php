<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class plantillaCliente implements WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        $cabecera = [
            'Nombre *',
            'Apellido Paterno *',
            'Apellido Materno *',
            'Correo *',
            'Telefono *',
            'Carreras *',
            'Tipo Documento *',
            'Nº de Documento *',
            'Grado',
            'Año Egreso',
            'Procedencia',
            'campaign_content',
            'campaign_medium',
            'campaign_name',
            'campaign_source',
            'campaign_term',

        ];

        $guia = [
            'Laura Elizabeth',
            'Aguilar',
            'Rivera',
            'Correo@gmail.com',
            '999999999',
            'Medicina Humana',
            'DNI',
            '77777777',
            '5to',
            '',
            'Orgánico',
            '',
            '',
            '',
            '',
            '',
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

    // Definir eventos para agregar la validación de datos
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Aplicar validación de datos para
                $this->applyDataValidation($sheet, 'G', '"DNI,C. EXTRANJERIA,PASAPORTE,OTROS"', 2, 900);

                // Aplicar validación de datos para "Año Egreso"
                $this->applyDataValidation($sheet, 'I', '"4to,5to"', 2, 900);

                // Aplicar validación de datos para "Año Egreso"
                $this->applyDataValidation($sheet, 'K', '"Orgánico,Pauta"', 2, 900);

                
            },
        ];
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
    private function applyDataValidation(Worksheet $sheet, string $column, string $formula1, int $startRow, int $endRow): void
    {
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1($formula1);

        for ($row = $startRow; $row <= $endRow; $row++) {
            $cell = $column . $row;
            $sheet->getCell($cell)->setDataValidation(clone $validation);
        }
    }

    
}
