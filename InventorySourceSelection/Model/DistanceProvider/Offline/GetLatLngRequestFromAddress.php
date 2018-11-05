<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventorySourceSelection\Model\DistanceProvider\Offline;

use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySourceSelection\Model\DistanceProvider\GetLatLngRequestFromAddressInterface;
use Magento\InventorySourceSelection\Model\Request\LatLngRequest;
use Magento\InventorySourceSelection\Model\Request\LatLngRequestFactory;
use Magento\InventorySourceSelection\Model\ResourceModel\GetGeoNameDataByAddressRequest;
use Magento\InventorySourceSelectionApi\Api\Data\AddressRequestInterface;

/**
 * @inheritdoc
 */
class GetLatLngRequestFromAddress implements GetLatLngRequestFromAddressInterface
{
    private $latLngCache = [];

    /**
     * @var LatLngRequestFactory
     */
    private $latLngRequestFactory;

    /**
     * @var GetGeoNameDataByAddressRequest
     */
    private $getGeoNameDataByAddressRequest;

    /**
     * GetLatLngRequestFromAddress constructor.
     *
     * @param GetGeoNameDataByAddressRequest $getGeoNameDataByAddressRequest
     * @param LatLngRequestFactory $latLngRequestFactory
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GetGeoNameDataByAddressRequest $getGeoNameDataByAddressRequest,
        LatLngRequestFactory $latLngRequestFactory
    ) {
        $this->getGeoNameDataByAddressRequest = $getGeoNameDataByAddressRequest;
        $this->latLngRequestFactory = $latLngRequestFactory;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(AddressRequestInterface $addressRequest): LatLngRequest
    {
        $cacheKey = $addressRequest->getAsString();
        if (!isset($this->latLngCache[$cacheKey])) {
            $geoNameData = $this->getGeoNameDataByAddressRequest->execute($addressRequest);

            $this->latLngCache[$cacheKey] = $this->latLngRequestFactory->create([
                'lat' => (float)$geoNameData['latitude'],
                'lng' => (float)$geoNameData['longitude'],
            ]);
        }

        return $this->latLngCache[$cacheKey];
    }
}
