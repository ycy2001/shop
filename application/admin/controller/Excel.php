<?php
namespace app\Admin\controller;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\Db;
class Excel extends Common
{
    public function test()
    {
        $spreadsheet = new Spreadsheet();
        $show=Db::query("select id,user_name,password from user");
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'user_name')
            ->setCellValue('C1', 'password');
        foreach ($show as $key=>$value){
           $i=$key+2;
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $value['id'])
                ->setCellValue('B'.$i, $value['user_name'])
                ->setCellValue('C'.$i, $value['password']);
        }


        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    public function daoexcel(){
        $helper = new Sample();
        if (isset($_FILES['myfile']) && !empty($_FILES['myfile'])) {
            $string = strrev($_FILES['myfile']['name']);
            $array = explode('.', $string);
            $ex = strrev($array[0]);
            $arr_excel = ['xlsx','xls'];
            if (in_array($ex, $arr_excel)) {
                $upload_path = "static/excel/";
                $rename = date("Ymd"). rand(100, 999) . "." . $ex;
                if (move_uploaded_file($_FILES['myfile']['tmp_name'], $upload_path . $rename)) {
        $inputFileName = "static/excel/".$rename;
        $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        foreach ( $sheetData as $key=>$val){
            $name=$val['B'];
            $password=$val['C'];
            $add = Db::query("insert into `user_to` (`name`,`password`)values('$name','$password')");
        }
        unlink("static/excel/$rename");
                }
            }
        }

    }
}