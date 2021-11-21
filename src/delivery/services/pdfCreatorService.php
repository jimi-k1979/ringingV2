<?php

declare(strict_types=1);

namespace DrlArchive\delivery\services;

class pdfCreatorService
{
    public const PORTRAIT_PAGE = 'P';
    public const MILLIMETRE_PAGE_UNITS = 'mm';
    public const A4_PAGE_FORMAT = 'A4';
    public const UNICODE_ON = true;
    public const UTF8_ENCODING = 'UTF-8';
    public const NO_DISC_CACHING = false;
    public const DEVON_RINGING_ARCHIVE = 'Devon Ringing Archive';
    public const AUTHOR_NAME = 'James Kerslake';
    public const DEFAULT_MARGIN_LEFT = 15;
    public const DEFAULT_MARGIN_TOP = 27;
    public const DEFAULT_MARGIN_RIGHT = 15;
    public const DEFAULT_MARGIN_BOTTOM = 25;
    public const AUTO_PAGE_BREAK_TRUE = true;
    public const OUTPUT_INLINE = 'I';

    private static ?self $creator = null;

    private ?\TCPDF $pdfCreator = null;

    private function __construct()
    {
        $this->pdfCreator = new \TCPDF(
            self::PORTRAIT_PAGE,
            self::MILLIMETRE_PAGE_UNITS,
            self::A4_PAGE_FORMAT,
            self::UNICODE_ON,
            self::UTF8_ENCODING,
            self::NO_DISC_CACHING
        );

        $this->pdfCreator->setCreator(self::DEVON_RINGING_ARCHIVE);
        $this->pdfCreator->setAuthor(self::AUTHOR_NAME);
        $this->pdfCreator->setPrintHeader(false);
        $this->pdfCreator->setPrintFooter(false);
        $this->pdfCreator->setMargins(
            self::DEFAULT_MARGIN_LEFT,
            self::DEFAULT_MARGIN_TOP,
            self::DEFAULT_MARGIN_RIGHT
        );
        $this->pdfCreator->setAutoPageBreak(
            self::AUTO_PAGE_BREAK_TRUE,
            self::DEFAULT_MARGIN_BOTTOM
        );
    }

    public static function service(): self
    {
        if (is_null(self::$creator)) {
            self::$creator = new pdfCreatorService();
        }
        return self::$creator;
    }

    public function setPdfMetaData(
        string $title,
        string $subject,
        string $keywords
    ): self {
        $this->pdfCreator->setTitle($title);
        $this->pdfCreator->setSubject($subject);
        $this->pdfCreator->setKeywords($keywords);

        return self::$creator;
    }

    public function newPage(): self
    {
        $this->pdfCreator->AddPage();
        return self::$creator;
    }

    public function addHtml(string $html): self
    {
        $this->pdfCreator->writeHTML($html);
        return self::$creator;
    }

    public function outputToScreen(string $filename): void
    {
        $this->pdfCreator->Output($filename, self::OUTPUT_INLINE);
    }
}
