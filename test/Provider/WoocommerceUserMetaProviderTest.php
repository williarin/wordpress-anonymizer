<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Test\Provider;

use Symfony\Component\Yaml\Yaml;
use Williarin\WordpressAnonymizer\Provider\WoocommerceUserMetaProvider;
use Williarin\WordpressAnonymizer\Test\TestCase;

class WoocommerceUserMetaProviderTest extends TestCase
{
    private WoocommerceUserMetaProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->container->get(WoocommerceUserMetaProvider::class);
    }

    public function testAnonymizeUserMeta(): void
    {
        $this->provider->anonymize();
        $userMeta = $this->getWoocommerceUserMeta();

        $original = [
            ...array_fill(0, 5, [
                'billing_first_name' => null,
                'billing_last_name' => null,
                'billing_company' => null,
                'billing_address_1' => null,
                'billing_address_2' => null,
                'billing_city' => null,
                'billing_postcode' => null,
                'billing_state' => null,
                'billing_country' => null,
                'billing_email' => null,
                'billing_phone' => null,
                'shipping_first_name' => null,
                'shipping_last_name' => null,
                'shipping_company' => null,
                'shipping_address_1' => null,
                'shipping_address_2' => null,
                'shipping_city' => null,
                'shipping_postcode' => null,
                'shipping_state' => null,
                'shipping_country' => null,
                '_stripe_customer_id' => null,
                '_customer_ip_address' => null,
                'payer_paypal_address' => null,
                'payer_first_name' => null,
                'payer_last_name' => null,
            ]),
            [
                'billing_first_name' => 'Justin',
                'billing_last_name' => 'Hills',
                'billing_company' => 'Google',
                'billing_address_1' => '4571 Ersel Street',
                'billing_address_2' => null,
                'billing_city' => 'Dallas',
                'billing_postcode' => '75204',
                'billing_state' => 'Texas',
                'billing_country' => 'United States',
                'billing_email' => 'justin@woo.local',
                'billing_phone' => '214-927-9108',
                'shipping_first_name' => 'Justin',
                'shipping_last_name' => 'Hills',
                'shipping_company' => 'Google',
                'shipping_address_1' => '4571 Ersel Street',
                'shipping_address_2' => null,
                'shipping_city' => 'Dallas',
                'shipping_postcode' => '75204',
                'shipping_state' => 'Texas',
                'shipping_country' => 'United States',
                '_stripe_customer_id' => null,
                '_customer_ip_address' => null,
                'payer_paypal_address' => null,
                'payer_first_name' => null,
                'payer_last_name' => null,
            ],
            [
                'billing_first_name' => 'Ottis',
                'billing_last_name' => 'Bruen',
                'billing_company' => 'Facebook',
                'billing_address_1' => '81 Spring St',
                'billing_address_2' => null,
                'billing_city' => 'New York',
                'billing_postcode' => '10012',
                'billing_state' => 'North Dakota',
                'billing_country' => 'United States',
                'billing_email' => 'ottis@woo.local',
                'billing_phone' => '(646) 613-1367',
                'shipping_first_name' => 'Ottis',
                'shipping_last_name' => 'Bruen',
                'shipping_company' => 'Facebook',
                'shipping_address_1' => '81 Spring St',
                'shipping_address_2' => null,
                'shipping_city' => 'New York',
                'shipping_postcode' => '10012',
                'shipping_state' => 'North Dakota',
                'shipping_country' => 'United States',
                '_stripe_customer_id' => null,
                '_customer_ip_address' => null,
                'payer_paypal_address' => null,
                'payer_first_name' => null,
                'payer_last_name' => null,
            ],
        ];

        foreach ($original as $i => $meta) {
            foreach ($meta as $key => $value) {
                if ($value === null && $userMeta[$i][$key] === null) {
                    continue;
                }

                self::assertNotSame($value, $userMeta[$i][$key]);
            }
        }
    }

    private function getWoocommerceUserMeta(): array
    {
        $data = Yaml::parseFile(__DIR__ . '/../../config/provider/woocommerce_usermeta.yaml');

        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->tablePrefix . 'usermeta')
            ->groupBy('user_id')
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
