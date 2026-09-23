<?php
/**
 * Product + Service JSON-LD — тільки для /en/shopify-marketing-agency/.
 *
 * Прив'язка навмисно по URL, а не по post ID: ID сторінки в БД може
 * відрізнятись між локальним оточенням і продом, а шлях — ні.
 *
 * Контент схеми (текст, sku, огляди) — фіксований, як затверджено.
 */

/**
 * Чи це фронтовий запит сторінки /en/shopify-marketing-agency/.
 */
function skalum_is_shopify_marketing_agency_page(): bool
{
    $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';

    return rtrim($path, '/') === '/en/shopify-marketing-agency';
}

add_action('wp_footer', function () {
    if (!skalum_is_shopify_marketing_agency_page()) {
        return;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'       => 'Product',
                '@id'         => 'https://skalum.agency/en/shopify-ads-agency/#product',
                'name'        => 'Shopify Ads & PPC Management Package',
                'description' => 'Full-service Shopify PPC, Meta Ads, and Paid Growth management package for e-commerce brands.',
                'sku'         => 'SKALUM-SHOPIFY-ADS-PACKAGE',
                'category'    => 'Marketing Services > Paid Search & PPC Management',
                'image'       => [
                    'https://skalum.agency/wp-content/uploads/2025/12/logo-linkedin.png',
                ],
                'brand' => [
                    '@type' => 'Brand',
                    'name'  => 'Skalum',
                ],
                'offers' => [
                    '@type'            => 'Offer',
                    'url'              => 'https://skalum.agency/en/shopify-ads-agency/',
                    'priceCurrency'    => 'EUR',
                    'price'            => '1350',
                    'priceValidUntil'  => '2027-12-31',
                    'itemCondition'    => 'https://schema.org/NewCondition',
                    'availability'     => 'https://schema.org/InStock',
                ],
                'aggregateRating' => [
                    '@type'       => 'AggregateRating',
                    'ratingValue' => '5.0',
                    'reviewCount' => '13',
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ],
                'review' => [
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Vlad and his team actually made real progress on improving our pages SEO. He understand very technically how this is done and would recommend him if you are looking to rank higher on google.',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Elias Constantopedos',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Prestige Health and Wellness',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => "I have been working with Vlad for over a year and am absolutely thrilled! He implements all agreements quickly and reliably and drives the processes forward independently. In regular consultations, we jointly define the next goals, which are then consistently implemented. This has led to a steady increase in my organic views and the development is simply fantastic. Thank you very much for the great cooperation!",
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Pavel Deuble',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Unternehmenswerk.de',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Amazing Communication and Good Work with solid advice!',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'ADeel',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Sweet Habibi',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => "Working with Vlad was an excellent experience! They provided top-notch SEO support, from content optimization to high-quality backlinks and link building. Communication was smooth, deadlines were met, and the results were visible quickly. I highly recommend Vlad for anyone looking to improve their website’s SEO performance.",
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Peter Sutter',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Marketing Agency',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Vlad is a highly skilled SEO. He has helped us in several aspects including SEO Audits, Link building and content analysis. Will definitely use him again in the future.',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Darrius',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Double Up Marketing Group',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Vlad and his team did a great job to improve SEO on our site.',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Preston Wong',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Hüga Collective',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Had a 60-minute call with Vlad about the SEO strategy and redesign for my medical website, and it was genuinely productive. He clearly knows his field — he came prepared, had already reviewed my site and Google Ads account beforehand, and walked me through concrete, well-reasoned recommendations rather than generic advice.',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Herr Dr. Bézard',
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => "Vlad did solid technical SEO work on our Shopify store across several milestones. He fixed a sitewide duplicate title tag problem (153 pages down to zero, verified in Ahrefs), cleaned up broken internal links, and caught and removed spam links that a previous contractor had planted in our blog content — something we wouldn't have found without him. His fixes held up through a full theme migration, which says a lot about how carefully he implemented them. He knows Shopify's quirks well and his recommendations were technically sound. Communication was clear and he was straightforward to work with on a milestone basis. I'd hire him again for scoped technical SEO work.",
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Joel Pinkham',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Ants on a Melon',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Vlad gave me a realistic view of the market and what’s achievable. No fluff, no overpromising — just solid guidance and a clear plan. Exactly what I needed.',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Gokberk Koylu',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'G-Berg Heizung',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Great job, I think we already worked for 2+ years now. After a bit more than 6 months our page was the number 1 result on Google for our niche (and we started from zero)',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Andre',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'SEO for German Webflow website',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Communicated well for the consultation, provided solutions upfront, and provided continued support post-consultation. Will jump on a project to work with Vlad’s team.',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Sabrina Bai',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'One Maay',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Vlad was clear, responsive and methodical. He delivered the keyword research and mapping quickly, and explained the rationale in a way that made the next steps easy to understand. Communication was smooth and professional, and he was receptive to working iteratively and respecting existing editorial tone.',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Brooke Belldon',
                            'worksFor' => [
                                '@type' => 'Organization',
                                'name'  => 'Saint Cellier',
                            ],
                        ],
                    ],
                    [
                        '@type'      => 'Review',
                        'reviewBody' => 'Vlad is easily the best SEO contractor I found on Upwork. Great skills and good value – I will be working with him in the future also',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => '5',
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name'  => 'Shopify SEO Consultant',
                        ],
                    ],
                ],
            ],
            [
                '@type' => 'Service',
                '@id'   => 'https://skalum.agency/en/shopify-ads-agency/#service',
                'name'  => 'Shopify Ads Agency Services',
                'provider' => [
                    '@type' => 'Organization',
                    'name'  => 'Skalum',
                    'url'   => 'https://skalum.agency/en/',
                ],
                'offers' => [
                    '@id' => 'https://skalum.agency/en/shopify-ads-agency/#product',
                ],
            ],
        ],
    ];
    ?>
<script type="application/ld+json">
<?= wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php
}, 20);
