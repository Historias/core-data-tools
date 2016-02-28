<?php
namespace Historian\Importer\WikiData;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;

class ItemFinder
{
    private $client;

    private $promises = [];

    private $itemIds = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function find(Property $propertyId, Claim $claim)
    {
        $this->promises[] = $this->client->getAsync(
            (new Uri($this->client->getConfig('base_url')))
                ->withQuery(sprintf('q=CLAIM[%d:%d]', $propertyId->value(), $claim->value()))
        )->then(
            function (Response $response) {
                $this->itemIds = array_merge(
                    $this->itemIds,
                    json_decode($response->getBody()->getContents(), true)['items']
                );
                array_unique($this->itemIds);
            }
        );
    }

    public function getMatchingItems() : array
    {
        (new EachPromise($this->promises))->promise()->wait();

        return $this->itemIds;
    }
}
