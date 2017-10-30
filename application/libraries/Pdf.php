<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class Pdf extends TCPDF
{
	public $_fonts_list = array();
	protected $last_page_flag = false;
	private $pdf_type = '';

	function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false,$pdf_type = '')
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$this->pdf_type = $pdf_type;
		$lg = array();
		$lg['a_meta_charset'] = 'UTF-8';
		// set some language-dependent strings (optional)
		$this->setLanguageArray($lg);
		$this->_fonts_list = $this->fontlist;;
	}

	public function Close() {
		$this->last_page_flag = true;
		parent::Close();
	}

	public function Header() {
		$this->SetFont('helvetica', 'B', 20);
	}

	public function Footer() {
        // Position at 15 mm from bottom
		$this->SetY(-15);

		$font_name = get_option('pdf_font');
	    $font_size = get_option('pdf_font_size');

	    if ($font_size == '') {
	        $font_size = 10;
	    }

		$this->SetFont($font_name, '', $font_size);

		do_action('pdf_footer',array('pdf_instance'=>$this,'type'=>$this->pdf_type));
        // Set font
		$this->SetFont('helvetica', 'I', 8);
		if(get_option('show_page_number_on_pdf') == 1){
			$this->SetTextColor(142,142,142);
			$this->Cell(0, 15, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}

	public function get_fonts_list(){
		return $this->_fonts_list;
	}

}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */
