<?php

namespace Nornas;

require_once(SITE_PATH . "/vendor/tcpdf/tcpdf.php");

class PDF extends \TCPDF{
	public function __construct ()
	{
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	}

	public function Header() {
        $this->SetFont('helvetica', 9);
		$this->Cell(90,10,'Data e hora da geração: ' . date("d/m/Y H:i:s"), 0, false, 'L', false, '', 0, false, 'T', 'M');
		$this->Cell(110,10,'Página: ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'R', false, '', 0, false, 'T', 'M');
	}
	
	public function Footer()
	{
		$year = date("Y");

        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 9);
		$this->Cell(0, 10, "Copyright © - Todos os direitos são reservados - Nenhuma parte pode ser reproduzida sem autorização. {$year}", 0, false, 'C', false, '', 0, false, 'T', 'M');
    }
}