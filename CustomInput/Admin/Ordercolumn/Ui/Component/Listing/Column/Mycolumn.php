<?php
namespace CustomInput\Admin\Ordercolumn\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class Mycolumn extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Mycolumn constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param ResourceConnection $resource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        ResourceConnection $resource,
        array $components = [],
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->resource = $resource;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $orderId = $item['entity_id'];
                $email = $this->getOrderEmail($orderId);
                $passportNumber = $this->getPassportNumber($orderId);

                // Debug output
                // echo "<pre>";
                // var_dump($item);

                $inputFieldHtml = $email . ' | Passport: ' . $passportNumber;
                $item[$this->getData('name')] = $inputFieldHtml;
            }
        }
        return $dataSource;
    }

    protected function getOrderEmail($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
            return $order->getCustomerEmail();
        } catch (\Exception $e) {
            return '';
        }
    }
    
    protected function getPassportNumber($orderId)
    {
        try {
            $salesOrderAddressTable = $this->resource->getTableName('sales_order_address');
    
            $passportNumber = $this->resource->getConnection()
                ->fetchOne(
                    $this->resource->getConnection()
                        ->select()
                        ->from($salesOrderAddressTable, ['passport_number'])
                        ->where('parent_id = ?', $orderId)
                );
    
            return $passportNumber;
        } catch (\Exception $e) {
            // Handle exception (e.g., order not found)
            return '';
        }
    }
    
}
