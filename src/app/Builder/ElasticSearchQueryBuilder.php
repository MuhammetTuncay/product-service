<?php

namespace App\Builder;

use App\Data\SearchFilterData;

class ElasticSearchQueryBuilder
{
    protected array $params = [
        'index' => 'products',
        'body' => [
            'query' => [
                'bool' => [
                    'filter' => [],
                    'must' => [],
                ],
            ],
        ],
    ];

    /**
     * @param string $field
     * @param array|null $filter
     * @return $this
     */
    public function whereTerms(string $field, ?array $filter): static
    {
        $this->params['body']['query']['bool']['filter'][] = [
            'terms' => [
                $field => $filter,
            ],
        ];

        return $this;
    }

    /**
     * @param string $path
     * @param string $field
     * @param array|null $filters
     * @return $this
     */
    public function whereNestedTerms(string $path, string $field, ?array $filters): static
    {
        $this->params['body']['query']['bool']['filter'][] = [
            'nested' => [
                'path' => $path,
                'query' => [
                    'bool' => [
                        'filter' => [
                            'terms' => [
                                $path . '.' . $field => $filters,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this;
    }

    /**
     * @param string $field
     * @param string|null $query
     * @return $this
     */
    public function whereSimpleQueryString(string $field, ?string $query): static
    {
        $this->params['body']['query']['bool']['must'][] = [
            'simple_query_string' => [
                'query' => "{$query}* OR {$query}",
                'fields' => [$field],
                'default_operator' => 'OR',
                'flags' => -1,
                'analyze_wildcard' => true,
                'fuzzy_max_expansions' => 50,
                'fuzzy_prefix_length' => 0,
                'fuzzy_transpositions' => true,
                'boost' => 1,
                'auto_generate_synonyms_phrase_query' => true,
            ],
        ];

        return $this;
    }

    /**
     * @param string $sort
     * @param string $order
     * @return $this
     */
    public function sort(string $sort, string $order): static
    {
        $this->params['body']['sort'] = [
            $sort => ['order' => $order],
        ];

        return $this;
    }

    /**
     * @param int $from
     * @return $this
     */
    public function from(int $from): static
    {
        $this->params['from'] = $from;

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function size(int $size): static
    {
        $this->params['size'] = $size;

        return $this;
    }

    public function build(SearchFilterData $searchFilterData): array
    {
        if ($searchFilterData->query) {
            $this->whereSimpleQueryString('name', $searchFilterData->query);
        }

        if ($searchFilterData->sort && $searchFilterData->order) {
            $this->sort($searchFilterData->sort, $searchFilterData->order);
        }

        if ($searchFilterData->product_ids) {
            $this->whereTerms('product_id', explode(',', $searchFilterData->product_ids));
        }

        if ($searchFilterData->category) {
            $this->whereNestedTerms('category', 'category_id', array_map('intval', explode(',', $searchFilterData->category)));
        }

        if ($searchFilterData->page) {
            $this->from(($searchFilterData->page - 1) * $searchFilterData->limit);
        }

        if ($searchFilterData->limit) {
            $this->size($searchFilterData->limit);
        }

        return $this->params;

    }
}
