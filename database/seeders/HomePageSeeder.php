<?php

namespace Database\Seeders;

use App\Domain\Content\Page;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Domovska stranka',
                'seo_meta' => [
                    'title' => 'Tvorba webovek na miru | Weby, ktere prodavaji',
                    'description' => 'Navrhujeme a vyvijime weby a e-shopy, ktere zrychluji obchod a dlouhodobe podporuji rust. Strategie, design a vyvoj v jednom timu.',
                ],
                'content' => [
                    [
                        'type' => 'hero',
                        'position' => 10,
                        'data' => [
                            'title' => 'Weby, ktere prodavaji a rostou s vami',
                            'subtitle' => 'Strategie, design a vyvoj na jednom miste',
                            'description' => 'Navrhujeme a vyvijime webove prezentace a e-shopy, ktere zlepsuji konverze, zrychluji obchod a dlouhodobe podporuji rust.',
                            'background_media_uuid' => null,
                            'primary' => [
                                'label' => 'Chci konzultaci',
                                'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null],
                            ],
                            'secondary' => [
                                'label' => 'Prohlidnout sluzby',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                            'stats' => [
                                ['value' => '120+', 'label' => 'spustenych webu'],
                                ['value' => '4-8 tydnu', 'label' => 'prumerna doba dodani'],
                                ['value' => '35 %', 'label' => 'prumerny narust konverzi'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'service_highlights',
                        'position' => 20,
                        'data' => [
                            'title' => 'Kompletni tvorba webovek bez bolesti',
                            'subtitle' => 'Od strategie po mereni vysledku',
                            'description' => 'Postarame se o cely proces: pruzkum, UX, UI, vyvoj, obsah i nasazeni.',
                            'more_info_label' => 'Zjistit vice',
                            'services' => [
                                [
                                    'title' => 'Strategie a UX',
                                    'description' => 'Audity, wireframy a informacni architektura pro web, ktery dava smysl.',
                                    'icon_key' => 'map-pin',
                                    'link' => ['page_id' => null, 'url' => '/capabilities#strategy', 'anchor' => null],
                                ],
                                [
                                    'title' => 'UI design',
                                    'description' => 'Moderni vizual, jasna hierarchie a system komponent pro rychly rozvoj.',
                                    'icon_key' => 'star',
                                    'link' => ['page_id' => null, 'url' => '/capabilities#design', 'anchor' => null],
                                ],
                                [
                                    'title' => 'Vyvoj a integrace',
                                    'description' => 'Rychly, bezpecny a skvele indexovany web s integracemi na vase nastroje.',
                                    'icon_key' => 'cog',
                                    'link' => ['page_id' => null, 'url' => '/capabilities#development', 'anchor' => null],
                                ],
                                [
                                    'title' => 'Optimalizace a rust',
                                    'description' => 'SEO, analytika, A/B testy a prubezne vylepsovani.',
                                    'icon_key' => 'check-circle',
                                    'link' => ['page_id' => null, 'url' => '/capabilities#growth', 'anchor' => null],
                                ],
                            ],
                            'cta' => [
                                'label' => 'Podivat se na proces',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                        ],
                    ],
                    [
                        'type' => 'image_grid',
                        'position' => 30,
                        'data' => [
                            'subtitle' => 'Ukazky prace',
                            'title' => 'Design, ktery buduje duveru',
                            'description' => 'Vybirame kombinaci cisteho designu, rychlosti a srozumitelnosti.',
                            'items' => [
                                [
                                    'title' => 'Landing page pro SaaS',
                                    'description' => 'Jasna hodnotova nabidka a vyrazne CTA.',
                                    'image_media_uuid' => null,
                                ],
                                [
                                    'title' => 'Firemni web pro B2B',
                                    'description' => 'Dostupne case studies a lead capture.',
                                    'image_media_uuid' => null,
                                ],
                                [
                                    'title' => 'E-shop s konfiguraci',
                                    'description' => 'Lehke filtrovani a rychla objednavka.',
                                    'image_media_uuid' => null,
                                ],
                            ],
                            'cta' => [
                                'label' => 'Zobrazit realizace',
                                'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null],
                            ],
                        ],
                    ],
                    [
                        'type' => 'industries_served',
                        'position' => 40,
                        'data' => [
                            'subtitle' => 'Obory, kterym rozumime',
                            'title' => 'B2B i B2C weby pro ruzne segmenty',
                            'description' => 'Prisposobujeme strukturu a obsah potrebam konkretniho oboru.',
                            'items' => [
                                [
                                    'title' => 'Technologie a SaaS',
                                    'description' => 'Duveryhodne messaging, rychla aktivace trialu.',
                                    'icon_key' => 'globe',
                                    'features' => [
                                        ['text' => 'jasne vysvetlene value props'],
                                        ['text' => 'case studies a social proof'],
                                        ['text' => 'rychle lead formy'],
                                    ],
                                ],
                                [
                                    'title' => 'Sluzby a poradenstvi',
                                    'description' => 'Prezentace know-how a jednoduche objednani konzultace.',
                                    'icon_key' => 'briefcase',
                                    'features' => [
                                        ['text' => 'profesionalni vizual'],
                                        ['text' => 'pruvodce sluzbami'],
                                        ['text' => 'kontakty na tym'],
                                    ],
                                ],
                                [
                                    'title' => 'E-commerce',
                                    'description' => 'Konverzni flow, doporuceni a rychle checkouty.',
                                    'icon_key' => 'building',
                                    'features' => [
                                        ['text' => 'rychle filtrovani'],
                                        ['text' => 'detail produktu s benefity'],
                                        ['text' => 'napojeni na ERP'],
                                    ],
                                ],
                            ],
                            'cta' => [
                                'label' => 'Zjistit, co funguje ve vasem oboru',
                                'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null],
                            ],
                        ],
                    ],
                    [
                        'type' => 'technology_innovation',
                        'position' => 50,
                        'data' => [
                            'subtitle' => 'Technologie, ktere urychluji rust',
                            'title' => 'Moderni stack bez kompromisu',
                            'description' => 'Stavime weby na aktualnich technologiich a automatizaci.',
                            'image_media_uuid' => null,
                            'items' => [
                                [
                                    'title' => 'Headless CMS',
                                    'description' => 'Flexibilni obsah a rychle nasazeni kampani.',
                                    'icon_key' => 'file-text',
                                ],
                                [
                                    'title' => 'Integrace a API',
                                    'description' => 'Napojeni na CRM, ERP a marketingove nastroje.',
                                    'icon_key' => 'cog',
                                ],
                                [
                                    'title' => 'Performance first',
                                    'description' => 'Lighthouse score a rychlejsi indexace.',
                                    'icon_key' => 'check',
                                ],
                            ],
                            'cta' => [
                                'label' => 'Prohlednout technicke schopnosti',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                        ],
                    ],
                    [
                        'type' => 'process_workflow',
                        'position' => 60,
                        'data' => [
                            'subtitle' => 'Jasny proces',
                            'title' => 'Od zadani k vysledkum',
                            'description' => 'Transparentni kroky, pravidelna komunikace a kontrola kvality.',
                            'steps' => [
                                [
                                    'number' => '01',
                                    'title' => 'Workshop a pruzkum',
                                    'description' => 'Definujeme cile, segmenty a hlavni message.',
                                    'image_media_uuid' => null,
                                ],
                                [
                                    'number' => '02',
                                    'title' => 'Design a prototyp',
                                    'description' => 'Navrh UX toku a UI kit pro rychly vyvoj.',
                                    'image_media_uuid' => null,
                                ],
                                [
                                    'number' => '03',
                                    'title' => 'Vyvoj a uvedeni',
                                    'description' => 'Implementace, testy, nasazeni a mereni.',
                                    'image_media_uuid' => null,
                                ],
                            ],
                            'benefits' => [
                                [
                                    'icon_key' => 'check-circle',
                                    'title' => 'Rychle iterace',
                                    'description' => 'Kratke sprinty a pravidelne demo.',
                                ],
                                [
                                    'icon_key' => 'shield',
                                    'title' => 'Bezpecny provoz',
                                    'description' => 'Overene postupy a stabilni hosting.',
                                ],
                                [
                                    'icon_key' => 'chat',
                                    'title' => 'Aktivni komunikace',
                                    'description' => 'Jediny kontaktni bod a jasne terminy.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'stats_showcase',
                        'position' => 70,
                        'data' => [
                            'subtitle' => 'Vysledky v cislech',
                            'title' => 'Dopad na byznys',
                            'description' => 'Fokus na metriky, ktere posouvaji prodeje a leady.',
                            'background_media_uuid' => null,
                            'stats' => [
                                ['value' => '+62 %', 'label' => 'navyseni organicke navstevnosti', 'icon_key' => 'globe'],
                                ['value' => '+28 %', 'label' => 'vyssi konverzni pomer', 'icon_key' => 'check'],
                                ['value' => '-35 %', 'label' => 'zkraceni doby nacitani', 'icon_key' => 'cog'],
                            ],
                            'logos' => [
                                ['label' => 'TechFlow'],
                                ['label' => 'NordicSoft'],
                                ['label' => 'PixelForge'],
                                ['label' => 'CloudPeak'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'testimonials',
                        'position' => 80,
                        'data' => [
                            'title' => 'Co rikaji klienti',
                            'subtitle' => 'Spolecna prace prinasi vysledky',
                            'description' => 'Realne zkusenosti tymu, ktere s nami zvladly redesign i novy web.',
                            'testimonials' => [
                                [
                                    'quote' => 'Web nam zvedl pocet poptavek o tretinu uz v prvnim mesici.',
                                    'name' => 'Martin Vlk',
                                    'role' => 'CEO, Vlk Solutions',
                                    'rating' => 5,
                                    'media_uuid' => null,
                                ],
                                [
                                    'quote' => 'Ocenujeme rychlou komunikaci a detailni insighty k obsahu.',
                                    'name' => 'Andrea Kovacova',
                                    'role' => 'Marketing Manager, Brightio',
                                    'rating' => 4.5,
                                    'media_uuid' => null,
                                ],
                                [
                                    'quote' => 'Konecne mame web, ktery se snadno spravuje a prinos je meritelny.',
                                    'name' => 'Petr Benda',
                                    'role' => 'Head of Sales, Lumenix',
                                    'rating' => 5,
                                    'media_uuid' => null,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'premium_cta',
                        'position' => 90,
                        'data' => [
                            'title' => 'Pojdme postavit web, ktery vydelava',
                            'subtitle' => 'Ziskejte navrh reseni do 48 hodin',
                            'description' => 'Strucna konzultace zdarma a plan dalsich kroku.',
                            'background_media_uuid' => null,
                            'buttons' => [
                                [
                                    'label' => 'Zacit nezavazne',
                                    'page_id' => null,
                                    'url' => '/rfq',
                                    'style' => 'primary',
                                ],
                                [
                                    'label' => 'Napsat email',
                                    'page_id' => null,
                                    'url' => 'mailto:hello@example.com',
                                    'style' => 'secondary',
                                ],
                            ],
                            'stats' => [
                                ['value' => '48 h', 'label' => 'dodani navrhu'],
                                ['value' => '0 Kc', 'label' => 'vstupni konzultace'],
                            ],
                        ],
                    ],
                ],
                'status' => Page::STATUS_PUBLISHED,
                'published_at' => now(),
            ],
        );
    }
}
