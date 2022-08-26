<?php
/**
* report class generates Excel, PDF, and CSV files using PHPSpreadsheet, https://phpspreadsheet.readthedocs.io/en/latest/
*
*/
namespace IGBIllinois;

use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
* report class generates Excel, PDF, and CSV files using PHPSpreadsheet, https://phpspreadsheet.readthedocs.io/en/latest/
*
* Provides functions to generate Excel 2003, Excel 2007, PDF, and CSV files
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
* @static
*
*
*/
class report {

	/**
	* Creates an Excel 2003 Report File.
	*
	* It outputs it using php headers
	*
	* @param array $data an associative array of data
	* @param string $filename filename off the output file
	* @static
	* @return void
	*/
	public static function create_excel_2003_report($data,$filename) {
		ob_clean();
		$excel_file = self::create_generic_excel($data);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		ob_end_clean();
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel_file,'Xls');
		setlocale(LC_ALL, 'en_US');
		$writer->save('php://output');
		exit();

	}

	/**
	* Creates an Excel 2007 Report File.
	*
	* It outputs it using php headers
	*
	* @param array $data an associative array of data
        * @param string $filename filename off the output file
        * @static
        * @return void
	*/
	public static function create_excel_2007_report($data,$filename) {
		ob_clean();
		$excel_file = self::create_generic_excel($data);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=" . $filename);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		ob_end_clean();
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel_file,'Xlsx');
		setlocale(LC_ALL, 'en_US');
		$writer->save('php://output');
		exit();
	}

        /**
        * Creates a PDF Report File.
        *
	* It outputs it using php headers
	*
        * @param array $data an associative array of data
        * @param string $filename filename off the output file
        * @static
        * @return void
        */
        public static function create_pdf_report($data,$filename) {
                ob_clean();
                $excel_file = self::create_generic_excel($data);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment;filename=" . $filename);
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
		ob_end_clean();
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel_file,'Tcpdf');
		setlocale(LC_ALL, 'en_US');
                $writer->save('php://output');
		exit();
        }

        /**
        * Creates a generic excel report.
        *
	* Used by by create_excel_2003_report() and create_excel_2007_report()
        * @param array $data an associative array of data
        * @static
        * @return \PhpOffice\PhpSpreadsheet\Spreadsheet;
        */
	private static function create_generic_excel($data) {

		$excel_file = new Spreadsheet();
		$excel_file->setActiveSheetIndex(0);
		if (count($data) !== 0 ) {
			//Creates headers
			$headings = array_keys($data[0]);
			for ($i=0;$i<count($headings);$i++) {
				$column = $i+1;
				$excel_file->getActiveSheet()->setCellValueByColumnAndRow($column,1,$headings[$i]);
				$excel_file->getActiveSheet()->getStyleByColumnAndRow($column,1)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$excel_file->getActiveSheet()->getStyleByColumnAndRow($column,1)->getFont()->setBold(true);
				$excel_file->getActiveSheet()->getStyleByColumnAndRow($column,1)->getFont()->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
				$excel_file->getActiveSheet()->getColumnDimensionByColumn($column)->setAutoSize(true);
			}
			//Adds data
			$rows = count($data);
			$start_row = 2;
			foreach ($data as $row_data) {
				$column=1;
				foreach ($row_data as $key => $value) {
					$excel_file->getActiveSheet()->setCellValueByColumnAndRow($column,$start_row,$value);
					$excel_file->getActiveSheet()->getStyleByColumnAndRow($column,$start_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
					$excel_file->getActiveSheet()->getStyleByColumnAndRow($column,$start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
					$column++;
				}
				$start_row++;
			}
		}
		$excel_file->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$excel_file->getActiveSheet()->getPageSetup()->setFitToPage(true);
		return $excel_file;

	}

	/**
        * Creates a CSV Report File.
	*
        * Outputs it using php headers
	*
        * @param array $data an associative array of data
        * @param string $filename filename off the output file
        * @static
        * @return void
        */

	public static function create_csv_report($data,$filename) {
		if (ob_get_length() > 0) {
			ob_clean();
		}
		$delimiter = ",";
		$file_handle = fopen('php://output','w');
		$headings = array_keys($data[0]);
		ob_start();
		fputcsv($file_handle,$headings,$delimiter);
		foreach ($data as $row) {
			fputcsv($file_handle,$row,$delimiter);
		}
		fclose($file_handle);
		$result = ob_get_clean();
		//Sets headers then downloads the csv report file.
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Pragma: no-cache');
		echo $result;
	
	}

}
?>
