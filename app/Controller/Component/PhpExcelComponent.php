<?php
/**
 * Component for working with PHPExcel class.
 *
 * @package PhpExcel
 * @author segy
 */
class PhpExcelComponent extends Component {
    /**
     * Instance of PHPExcel class
     *
     * @var PHPExcel
     */
    protected $_xls;
    protected $_objReader;

    /**
     * Pointer to current row
     *
     * @var int
     */
    protected $_row = 1;

    /**
     * Internal table params
     *
     * @var array
     */
    protected $_tableParams;

    /**
     * Number of rows
     *
     * @var int
     */
    protected $_maxRow = 0;

    /**
     * Create new worksheet or load it from existing file
     *
     * @return $this for method chaining
     */
    public function createWorksheet() {
        // load vendor classes
        App::import('Vendor', 'phpexcel');

        $this->_xls = new PHPExcel();
        $this->_row = 1;

        return $this;
    }
    
    public function setExcelReader($inputPath) {
        // load vendor classes
        App::import('Vendor', 'phpexcel');
        
        $inputFileType = PHPExcel_IOFactory::identify($inputPath);
        $this->_objReader = PHPExcel_IOFactory::createReader($inputFileType);
    }

    /**
     * Create new worksheet from existing file
     *
     * @param string $file path to excel file to load
     * @return $this for method chaining
     */
    public function loadWorksheet($file , $startRow = 1, $chunkSize = 0, $import = TRUE , $sheet = 0) {
        // load vendor classes
        if($import)    App::import('Vendor', 'phpexcel');
        
        if($chunkSize > 0 && !empty($this->_objReader))
        {
            // load ChunkReadFilter class ...
            if($import)     App::import('Vendor', 'chunkreadfilter');
            
            /**  Create a new Instance of our Read Filter, passing in the limits on which rows we want to read  **/
	        $chunkFilter = new ChunkReadFilter($startRow,$chunkSize);
            
            /**  Tell the Reader that we want to use the new Read Filter that we've just Instantiated  **/
	        $this->_objReader->setReadFilter($chunkFilter);
        }

        $this->_xls = ( empty($this->_objReader) ? PHPExcel_IOFactory::load($file) : $this->_objReader->load($file) );
        
        if(isset($chunkFilter))
        {
            $this->setActiveSheet($sheet , TRUE);
            
            if($import)
            {
                $spreadsheetInfo = $this->_objReader->listWorksheetInfo($file);
                $this->_maxRow = $spreadsheetInfo[$sheet]['totalRows'];
            }
        }
        else // general use...
        {
            $this->setActiveSheet($sheet);
        }
        
        $this->_row = $startRow;

        return $this;
    }

    /**
     * Add sheet
     *
     * @param string $name
     * @return $this for method chaining
     */
    public function addSheet($name) {
        $index = $this->_xls->getSheetCount();
        $this->_xls->createSheet($index)
            ->setTitle($name);

        $this->setActiveSheet($index);

        return $this;
    }

    /**
     * Set active sheet
     *
     * @param int $sheet
     * @return $this for method chaining
     */
    public function setActiveSheet($sheet , $strict = FALSE) {
        if($strict)
        {
            $this->_xls->setActiveSheetIndex($sheet);
        }
        else
        {
            $this->_maxRow = $this->_xls->setActiveSheetIndex($sheet)->getHighestRow();
            $this->_row = 1;
        }

        return $this;
    }
    
    /**
     * Get total available sheet
     *
     * @return int total available sheet
     */
    public function getSheetCount() {
        return $this->_xls->getSheetCount();
    }

    /**
     * Set worksheet name
     *
     * @param string $name name
     * @return $this for method chaining
     */
    public function setSheetName($name) {
        $this->_xls->getActiveSheet()->setTitle($name);

        return $this;
    }

    /**
     * Overloaded __call
     * Move call to PHPExcel instance
     *
     * @param string function name
     * @param array arguments
     * @return the return value of the call
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->_xls, $name), $arguments);
    }

    /**
     * Set default font
     *
     * @param string $name font name
     * @param int $size font size
     * @return $this for method chaining
     */
    public function setDefaultFont($name, $size) {
        $this->_xls->getDefaultStyle()->getFont()->setName($name);
        $this->_xls->getDefaultStyle()->getFont()->setSize($size);

        return $this;
    }

    /**
     * Set row pointer
     *
     * @param int $row number of row
     * @return $this for method chaining
     */
    public function setRow($row) {
        $this->_row = (int)$row;

        return $this;
    }

    /**
     * Start table - insert table header and set table params
     *
     * @param array $data data with format:
     *   label   -   table heading
     *   width   -   numeric (leave empty for "auto" width)
     *   filter  -   true to set excel filter for column
     *   wrap    -   true to wrap text in column
     * @param array $params table parameters with format:
     *   offset  -   column offset (numeric or text)
     *   font    -   font name of the header text
     *   size    -   font size of the header text
     *   bold    -   true for bold header text
     *   italic  -   true for italic header text
     * @return $this for method chaining
     */
    public function addTableHeader($data, $params = array()) {
        // offset
        $offset = 0;
        if (isset($params['offset']))
            $offset = is_numeric($params['offset']) ? (int)$params['offset'] : PHPExcel_Cell::columnIndexFromString($params['offset']);

        // font name
        if (isset($params['font']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setName($params['font']);

        // font size
        if (isset($params['size']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setSize($params['size']);

        // bold
        if (isset($params['bold']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setBold($params['bold']);

        // italic
        if (isset($params['italic']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setItalic($params['italic']);

        // set internal params that need to be processed after data are inserted
        $this->_tableParams = array(
            'header_row' => $this->_row,
            'offset' => $offset,
            'row_count' => 0,
            'auto_width' => array(),
            'filter' => array(),
            'wrap' => array()
        );

        foreach ($data as $d) {
            // set label
            $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($offset, $this->_row, $d['label']);

            // set width
            if (isset($d['width']) && is_numeric($d['width']))
                $this->_xls->getActiveSheet()->getColumnDimensionByColumn($offset)->setWidth((float)$d['width']);
            else
                $this->_tableParams['auto_width'][] = $offset;

            // filter
            if (isset($d['filter']) && $d['filter'])
                $this->_tableParams['filter'][] = $offset;

            // wrap
            if (isset($d['wrap']) && $d['wrap'])
                $this->_tableParams['wrap'][] = $offset;

            $offset++;
        }
        $this->_row++;

        return $this;
    }

    /**
     * Write array of data to current row
     *
     * @param array $data
     * @return $this for method chaining
     */
    public function addTableRow($data) {
        $offset = $this->_tableParams['offset'];

        foreach ($data as $d)
            $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($offset++, $this->_row, $d);

        $this->_row++;
        $this->_tableParams['row_count']++;

        return $this;
    }

    /**
     * End table - set params and styles that required data to be inserted first
     *
     * @return $this for method chaining
     */
    public function addTableFooter() {
        // auto width
        foreach ($this->_tableParams['auto_width'] as $col)
            $this->_xls->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);

        // filter (has to be set for whole range)
        if (count($this->_tableParams['filter']))
            $this->_xls->getActiveSheet()->setAutoFilter(PHPExcel_Cell::stringFromColumnIndex($this->_tableParams['filter'][0]) . ($this->_tableParams['header_row']) . ':' . PHPExcel_Cell::stringFromColumnIndex($this->_tableParams['filter'][count($this->_tableParams['filter']) - 1]) . ($this->_tableParams['header_row'] + $this->_tableParams['row_count']));

        // wrap
        foreach ($this->_tableParams['wrap'] as $col)
            $this->_xls->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . ($this->_tableParams['header_row'] + 1) . ':' . PHPExcel_Cell::stringFromColumnIndex($col) . ($this->_tableParams['header_row'] + $this->_tableParams['row_count']))->getAlignment()->setWrapText(true);

        return $this;
    }

    /**
     * Write array of data to current row starting from column defined by offset
     *
     * @param array $data
     * @return $this for method chaining
     */
    public function addData($data, $offset = 0) {
        // solve textual representation
        if (!is_numeric($offset))
            $offset = PHPExcel_Cell::columnIndexFromString($offset);

        foreach ($data as $d)
            $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($offset++, $this->_row, $d);

        $this->_row++;

        return $this;
    }

    /**
     * Get array of data from current row
     *
     * @param int $max
     * @return array row contents
     */
    public function getTableData($max = 100) {
        if ($this->_row > $this->_maxRow)
            return false;

        $data = array();

        for ($col = 0; $col < $max; $col++)
            $data[] = $this->_xls->getActiveSheet()->getCellByColumnAndRow($col, $this->_row)->getValue();

        $this->_row++;

        return $data;
    }

    /**
     * Get writer
     *
     * @param $writer
     * @return PHPExcel_Writer_Iwriter
     */
    public function getWriter($writer) {
        return PHPExcel_IOFactory::createWriter($this->_xls, $writer);
    }

    /**
     * Save to a file
     *
     * @param string $file path to file
     * @param string $writer
     * @return bool
     */
    public function save($file, $writer = 'Excel2007') {
        $objWriter = $this->getWriter($writer);
        return $objWriter->save($file);
    }

    /**
     * Output file to browser
     *
     * @param string $file path to file
     * @param string $writer
     * @return exit on this call
     */
    public function output($filename = 'export.xlsx', $writer = 'Excel2007') {
        // remove all output
        ob_end_clean();

        // headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // writer
        $objWriter = $this->getWriter($writer);
        $objWriter->save('php://output');

        exit;
    }

    /**
     * Free memory
     *
     * @return void
     */
    public function freeMemory() {
        // This must be called before unsetting to prevent memory leaks
        $this->_xls->disconnectWorksheets();
        // Again, unset variables to free up memory
        unset($this->_xls);
    }
    
    /**
     * Delete selected Excel Column
     *
     * @param string $col - starting index of column to be removed (alphabetical)
     * @param string $length - number of column to be removed
     * @return void
     */
    public function removeColumn($col , $length = 1) {
        $this->_xls->getActiveSheet()->removeColumn($col, $length);
    }
}
