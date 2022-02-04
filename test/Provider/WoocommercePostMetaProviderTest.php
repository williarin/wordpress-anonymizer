<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Test\Provider;

use Symfony\Component\Yaml\Yaml;
use Williarin\WordpressAnonymizer\Provider\WoocommercePostMetaProvider;
use Williarin\WordpressAnonymizer\Test\TestCase;

class WoocommercePostMetaProviderTest extends TestCase
{
    private WoocommercePostMetaProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->container->get(WoocommercePostMetaProvider::class);
    }

    public function testAnonymizePostMeta(): void
    {
        $this->provider->anonymize();
        $postMeta = $this->getWoocommercePostMeta();

        $original = [
            [
                '_billing_first_name' => null,
                '_billing_last_name' => null,
                '_billing_company' => null,
                '_billing_address_1' => null,
                '_billing_address_2' => null,
                '_billing_city' => null,
                '_billing_postcode' => null,
                '_billing_state' => null,
                '_billing_country' => null,
                '_billing_email' => 'justin@woo.local',
                '_billing_phone' => null,
                '_shipping_first_name' => null,
                '_shipping_last_name' => null,
                '_shipping_company' => null,
                '_shipping_address_1' => null,
                '_shipping_address_2' => null,
                '_shipping_city' => null,
                '_shipping_postcode' => null,
                '_shipping_state' => null,
                '_shipping_country' => null,
                '_stripe_customer_id' => null,
                '_customer_ip_address' => null,
                'payer_paypal_address' => null,
                'payer_first_name' => null,
                'payer_last_name' => null,
            ],
            [
                '_billing_first_name' => null,
                '_billing_last_name' => null,
                '_billing_company' => null,
                '_billing_address_1' => null,
                '_billing_address_2' => null,
                '_billing_city' => null,
                '_billing_postcode' => null,
                '_billing_state' => null,
                '_billing_country' => null,
                '_billing_email' => 'otis@woo.local',
                '_billing_phone' => null,
                '_shipping_first_name' => null,
                '_shipping_last_name' => null,
                '_shipping_company' => null,
                '_shipping_address_1' => null,
                '_shipping_address_2' => null,
                '_shipping_city' => null,
                '_shipping_postcode' => null,
                '_shipping_state' => null,
                '_shipping_country' => null,
                '_stripe_customer_id' => null,
                '_customer_ip_address' => null,
                'payer_paypal_address' => null,
                'payer_first_name' => null,
                'payer_last_name' => null,
            ],
            [
                '_billing_first_name' => 'Trudie',
                '_billing_last_name' => 'Metz',
                '_billing_company' => 'Amazon',
                '_billing_address_1' => '135 Wyandot Ave',
                '_billing_address_2' => null,
                '_billing_city' => 'Marion',
                '_billing_postcode' => '43302',
                '_billing_state' => 'Ohio',
                '_billing_country' => 'United States',
                '_billing_email' => 'trudie@woo.local',
                '_billing_phone' => '(740) 383-4031',
                '_shipping_first_name' => 'Trudie',
                '_shipping_last_name' => 'Metz',
                '_shipping_company' => 'Amazon',
                '_shipping_address_1' => '135 Wyandot Ave',
                '_shipping_address_2' => null,
                '_shipping_city' => 'Marion',
                '_shipping_postcode' => '43302',
                '_shipping_state' => 'Ohio',
                '_shipping_country' => 'United States',
                '_stripe_customer_id' => null,
                '_customer_ip_address' => null,
                'payer_paypal_address' => null,
                'payer_first_name' => null,
                'payer_last_name' => null,
            ],
        ];

        foreach ($original as $i => $meta) {
            foreach ($meta as $key => $value) {
                if ($value === null && $postMeta[$i][$key] === null) {
                    continue;
                }

                self::assertNotSame($value, $postMeta[$i][$key]);
            }
        }
    }

    private function getWoocommercePostMeta(): array
    {
        $data = Yaml::parseFile(__DIR__ . '/../../config/provider/woocommerce_postmeta.yaml');

        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->tablePrefix . 'postmeta', 'pm')
            ->join('pm', $this->tablePrefix . 'posts', 'p', 'p.ID = pm.post_id')
            ->where("p.post_type = 'shop_order'")
            ->groupBy('post_id')
        ;

        foreach (array_keys($data) as $key) {
            $queryBuilder
                ->addSelect(sprintf("MAX(Case WHEN meta_key = '%s' THEN meta_value END) %s", $key, $key))
            ;
        }

        return $queryBuilder->executeQuery()
            ->fetchAllAssociative()
        ;
    }
}
