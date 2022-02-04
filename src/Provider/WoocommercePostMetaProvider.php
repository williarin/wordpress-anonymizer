<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Provider;

use Symfony\Component\Yaml\Yaml;

final class WoocommercePostMetaProvider extends AbstractAnonymizerProvider
{
    public function anonymize(): void
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('post_id')
            ->from($this->tablePrefix . 'postmeta', 'pm')
            ->join('pm', $this->tablePrefix . 'posts', 'p', 'p.ID = pm.post_id')
            ->where("p.post_type = 'shop_order'")
            ->groupBy('post_id')
        ;

        foreach (array_keys($this->data) as $key) {
            $queryBuilder
                ->addSelect(sprintf(
                    "MAX(Case WHEN meta_key = '%s' THEN meta_value END) %s",
                    is_array($this->data[$key]) ? $this->data[$key]['name'] : $key,
                    $key,
                ))
            ;
        }

        $postMeta = $queryBuilder->executeQuery();

        $this->replaceMetaValues($postMeta, 'postmeta', 'post_id');
    }

    protected function load(): void
    {
        $this->data = Yaml::parseFile(__DIR__ . '/../../config/provider/woocommerce_postmeta.yaml');
    }
}
