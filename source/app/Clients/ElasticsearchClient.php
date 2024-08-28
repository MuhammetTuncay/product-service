<?php

namespace App\Clients;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

class ElasticsearchClient
{
    protected Client $client;
    protected string $indexName;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('app.elastic.server')])
            ->build();
        $this->indexName = config('app.elastic.index');
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function index(array $params): Elasticsearch|Promise
    {
        return $this->client->index($params);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(array $params): Elasticsearch|Promise
    {
        return $this->client->search($params);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function update(array $params): Elasticsearch|Promise
    {
        return $this->client->update($params);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function delete(array $params): Elasticsearch|Promise
    {
        return $this->client->delete($params);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function bulk(array $params): Elasticsearch|Promise
    {
        return $this->client->bulk($params);
    }

    public function createIndex(): Elasticsearch|Promise|string
    {
        $params = [
            'index' => $this->indexName,
            'body' => json_decode(file_get_contents(public_path('product_setting.json')), true)
        ];

        try {
            if ($this->client->indices()->exists(['index' => $this->indexName])->asBool()){
                $this->client->indices()->delete(['index' => $this->indexName]);
            }
            return $this->client->indices()->create($params);
        } catch (ClientResponseException|MissingParameterException|ServerResponseException $e) {
            return $e->getMessage();
        }
    }

    public function putMapping(): Elasticsearch|Promise|string
    {
        $params = [
            'index' => $this->indexName,
            'body' => json_decode(file_get_contents(public_path('product_mapping.json')), true)
        ];

        try {
            return $this->client->indices()->putMapping($params);
        } catch (ClientResponseException|MissingParameterException|ServerResponseException $e) {
            return $e->getMessage();
        }
    }

    public function createMappingAndSetting(): \Illuminate\Http\JsonResponse
    {
        $index = $this->createIndex();
        $mapping = $this->putMapping();

        return response()->json(['index' => $index, 'mapping' => $mapping]);
    }
}
