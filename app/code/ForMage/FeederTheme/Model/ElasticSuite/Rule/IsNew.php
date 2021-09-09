<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCatalogRule
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace ForMage\FeederTheme\Model\ElasticSuite\Rule;

use Smile\ElasticsuiteCatalogRule\Api\Rule\Condition\Product\SpecialAttributeInterface;
use Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory;
use Smile\ElasticsuiteCore\Search\Request\QueryInterface;

/**
 * "Is New" special attribute class
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalogRule
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class IsNew implements SpecialAttributeInterface
{
    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $booleanSource;

    /**
     * Image constructor.
     *
     * @param \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory $queryFactory  Query Factory
     * @param \Magento\Config\Model\Config\Source\Yesno                 $booleanSource Boolean Source
     */
    public function __construct(QueryFactory $queryFactory, \Magento\Config\Model\Config\Source\Yesno $booleanSource)
    {
        $this->queryFactory  = $queryFactory;
        $this->booleanSource = $booleanSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return 'is_product_new';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQuery(\Smile\ElasticsuiteCatalogRule\Model\Rule\Condition\Product $condition)
    {
        $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $clauses = [];

        $newFromDateEarlier = $this->queryFactory->create(
            QueryInterface::TYPE_RANGE,
            ['field' => 'news_from_date', 'bounds' => ['lte' => $now]]
        );

        $newsToDateLater = $this->queryFactory->create(
            QueryInterface::TYPE_RANGE,
            ['field' => 'news_to_date', 'bounds' => ['gte' => $now]]
        );

        $missingNewsFromDate = $this->queryFactory->create(QueryInterface::TYPE_MISSING, ['field' => 'news_from_date']);
        $missingNewsToDate   = $this->queryFactory->create(QueryInterface::TYPE_MISSING, ['field' => 'news_to_date']);

        // Product is new if "news_from_date" is earlier than now and he has no "news_to_date".
        $clauses[] = $this->queryFactory->create(
            QueryInterface::TYPE_BOOL,
            ['must' => [$newFromDateEarlier, $missingNewsToDate]]
        );

        // Product is new if "news_to_date" is later than now and he has no "news_from_date".
        $clauses[] = $this->queryFactory->create(
            QueryInterface::TYPE_BOOL,
            ['must' => [$missingNewsFromDate, $newsToDateLater]]
        );

        // Product is new if now is between "news_from_date" and "news_to_date".
        $clauses[] = $this->queryFactory->create(
            QueryInterface::TYPE_BOOL,
            ['must' => [$newFromDateEarlier, $newsToDateLater]]
        );

        // Product is new if one of previously built queries match.
        return $this->queryFactory->create(QueryInterface::TYPE_BOOL, ['should' => $clauses]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOperatorName()
    {
        return ' ';
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueElementType()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueName($value)
    {
        return ' ';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($value)
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueOptions()
    {
        return $this->booleanSource->toOptionArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Only new products');
    }
}
