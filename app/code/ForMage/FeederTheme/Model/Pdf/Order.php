<?php

namespace ForMage\FeederTheme\Model\Pdf;

use Magento\Sales\Model\Order\Pdf\Invoice;

class Order extends Invoice
{
    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $appEmulation,
        array $data = []
    ) {
        $this->appEmulation = $appEmulation;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $appEmulation,
            $data
        );
    }

    /**
     * Return PDF document
     *
     * @param  \Magento\Sales\Model\Order[] $orders
     *
     * @return \Zend_Pdf
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Pdf_Exception
     */
    public function getPdf($orders = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);

        foreach ($orders as $order) {
            if ($order->getStoreId()) {
                $this->appEmulation->startEnvironmentEmulation(
                    $order->getStoreId(),
                    \Magento\Framework\App\Area::AREA_FRONTEND,
                    true
                );
            }
            $page = $this->newPage();
            $this->_setFontBold($page, 10);
            $order->setOrder($order);
            /* Add image */
            $this->insertLogo($page, $order->getStore());
            /* Add address */
            $this->insertAddress($page, $order->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );

            $page = $this->insertComment(
                $page,
                $order->getBoldOrderComment()
            );

            $page = $this->addComments($order, $page);

            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                /* Keep it compatible with the invoice */
                $item->setQty($item->getQtyOrdered());
                $item->setOrderItem($item);

                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $order);
            if ($order->getStoreId()) {
                $this->appEmulation->stopEnvironmentEmulation();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    protected function addComments($order, $page)
    {
        $comments = $order->getStatusHistoryCollection();
        if (!count($comments)) return $page;

        $statuses = [];
        foreach ($comments as $status) {
            if (!strlen($status->getComment())) continue;

            $statuses[] = $status->getComment();
        }

        $page = $this->insertComment(
            $page,
            implode("\n", $statuses),
            'Comments'
        );

        return $page;
    }

    protected function insertComment(\Zend_Pdf_Page $page, $comment, $header = null)
    {
        if (!$comment) {
            return $page;
        }

        if ($this->y < 50) {
            $page = $this->newPage();
        }

        $this->drawCommentHeader($page, $header);
        $comment = $this->formatComment($comment);
        $page = $this->drawCommentBody($page, $comment);

        return $page;
    }

    protected function drawCommentHeader(\Zend_Pdf_Page $page, $header = null)
    {
        $this->setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $page->drawText(__($header ?? 'Order Comment'), 35, $this->y -10);
        $this->y -= 30;
        return $page;
    }

    protected function drawCommentBody(\Zend_Pdf_Page $page, $commentLines)
    {
        $this->prepareCommentBodyBlock($page, count($commentLines));

        foreach($commentLines as $index => $line) {
            $page->drawText($line, 35, $this->y);
            $this->y -= 10;

            if ($this->y <= 15) {
                $page = $this->newPage();
                $remainingLines = count($commentLines) - ($index + 1);
                $this->drawCommentHeader($page);
                $this->prepareCommentBodyBlock($page, $remainingLines);
            }
        }
        return $page;
    }

    protected function prepareCommentBodyBlock(\Zend_Pdf_Page $page, $numLines)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(
            25,
            $this->y + 15,
            570,
            max($this->y - (10 * $numLines) + 10, 15)
        );

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
    }

    protected function formatComment($text)
    {
        $result = [];
        foreach (explode("\n", $text) as $str) {
            foreach ($this->string->split($str, 130, true, true) as $part) {
                $result[] = $part;
            }
            $result[] = '';
        }
        return $result;
    }

    private function isEnabled($type, $store)
    {
        return $this->scopeConfig->isSetFlag(
            'sales_pdf/'.$type.'/bold_put_comment',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

}
