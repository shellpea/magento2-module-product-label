<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ProductLabel
 * @author    Houda EL RHOZLANE <houda.elrhozlane@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\ProductLabel\Ui\Component\ProductLabel\Form\Modifier;

/**
 * Smile Product Label edit form data provider modifier :
 *
 * Used to populate "store_id" field according to current value of "store_id" for current product label.
 *
 * @category  Smile
 * @package   Smile\ProductLabel
 * @author    Houda EL RHOZLANE <houda.elrhozlane@smile.fr>
 */
class Stores implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     * @var \Smile\ProductLabel\Model\ProductLabel\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * AttributeOptions constructor.
     *
     * @param \Smile\ProductLabel\Model\ProductLabel\Locator\LocatorInterface $locator             ProductLabel Locator
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface                $attributeRepository Attribute Repository
     */
    public function __construct(
        \Smile\ProductLabel\Model\ProductLabel\Locator\LocatorInterface $locator,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
    ) {
        $this->locator             = $locator;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productLabel = $this->locator->getProductLabel();

        if ($productLabel && $productLabel->getId() && !empty($productLabel->getStores()) && empty($data[$productLabel->getId()]['store_id'])) {
            $data[$productLabel->getId()]['store_id'] = $productLabel->getStores();
        }

        if ($productLabel && $productLabel->getAttributeId() && !$this->isScopeStore($productLabel->getAttributeId())) {
            $data[$productLabel->getId()]['store_id'] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $productLabel = $this->locator->getProductLabel();

        $meta['general']['children']['storeviews']['arguments']['data']['config']['visible'] = false;
        if ($productLabel && $productLabel->getAttributeId() && $this->isScopeStore($productLabel->getAttributeId())) {
            $meta['general']['children']['storeviews']['arguments']['data']['config']['visible'] = true;
        }

        return $meta;
    }

    /**
     * Check if an attribute is store scoped.
     *
     * @param int $attributeId The attribute Id
     *
     * @return array
     */
    private function isScopeStore($attributeId)
    {
        $attribute = $this->attributeRepository->get($attributeId);

        return ($attribute->getAttributeId() && $attribute->isScopeStore());
    }
}
