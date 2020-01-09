<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/07/2016
 * Time: 20:35
 */

namespace Magenest\AdvancedProductOption\Helper;

use Magento\Customer\Api\Data\GroupInterface;

class CustomerGroup extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $groupRepository;

    protected $searchCriteriaBuilder;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->groupRepository       = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }//end __construct()


    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        $customerGroups = [
                           [
                            'label' => __('ALL GROUPS'),
                            'value' => "".GroupInterface::CUST_GROUP_ALL."",
                           ],
                          ];

        /*
            @var GroupInterface[] $groups
        */
        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($groups->getItems() as $group) {
            $customerGroups[] = [
                                 'label' => $group->getCode(),
                                 'value' => $group->getId(),
                                ];
        }

        return $customerGroups;
    }//end getCustomerGroups()
}//end class
