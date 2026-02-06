<?php

namespace Database\Seeders;

use App\Domain\Content\Page;
use Illuminate\Database\Seeder;

class TemplatePagesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pages() as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    'title' => $pageData['title'],
                    'content' => $pageData['content'],
                    'seo_meta' => $pageData['seo_meta'] ?? [],
                    'status' => Page::STATUS_PUBLISHED,
                    'published_at' => now(),
                ],
            );
        }
    }

    private function pages(): array
    {
        return [
            $this->homePage(),
            $this->capabilitiesPage(),
            $this->documentationPage(),
            $this->facilitiesPage(),
            $this->rfqPage(),
            $this->useCasesPage(),
        ];
    }

    private function homePage(): array
    {
        return [
            'slug' => 'home',
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
        ];
    }

    private function capabilitiesPage(): array
    {
        return [
            'slug' => 'capabilities',
            'title' => 'Sluzby',
            'seo_meta' => [
                'title' => 'Sluzby | Strategie, design a vyvoj webu',
                'description' => 'Kompletni tvorba webu: strategie, UX, UI, vyvoj, integrace i optimalizace pro rust.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Nase schopnosti',
                        'title' => 'Tvorba webu od strategie po rust',
                        'description' => 'Pokryvame cely proces: vyzkum, UX, design, vyvoj, integrace i optimalizaci.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Strategie a UX'],
                            ['text' => 'Design system'],
                            ['text' => 'Integrace a API'],
                        ],
                        'stats' => [
                            ['value' => '10+ let', 'label' => 'zkusenosti tymu'],
                            ['value' => '50+ sektoru', 'label' => 'poznanych trhu'],
                        ],
                        'primary' => ['label' => 'Poptat projekt', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Ukazky prace', 'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null]],
                    ],
                ],
                [
                    'type' => 'capabilities_detailed',
                    'position' => 20,
                    'data' => [
                        'title' => 'Co umime dodat',
                        'subtitle' => 'Prakticke kompetence, ktere maji dopad',
                        'items' => [
                            [
                                'title' => 'Strategie a UX audit',
                                'description' => 'Analyza ciloveho publika, customer journey a navrh informacni architektury.',
                                'icon_key' => 'map-pin',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'workshop s tymem'],
                                    ['text' => 'wireframy a prototypy'],
                                    ['text' => 'prioritizace obsahu'],
                                ],
                            ],
                            [
                                'title' => 'UI design a system',
                                'description' => 'Moderni vizual, jasne pravidla a komponenty pro rychle iterace.',
                                'icon_key' => 'star',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'design system a UI kit'],
                                    ['text' => 'responsive varianty'],
                                    ['text' => 'priprava pro vyvoj'],
                                ],
                            ],
                            [
                                'title' => 'Vyvoj a integrace',
                                'description' => 'Rychly front-end, napojeni na CMS a integrace na vase nastroje.',
                                'icon_key' => 'cog',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'headless CMS'],
                                    ['text' => 'API integrace'],
                                    ['text' => 'automatizace publikace'],
                                ],
                            ],
                            [
                                'title' => 'SEO a optimalizace',
                                'description' => 'Technicke SEO, obsahove struktury a performance tuning.',
                                'icon_key' => 'check-circle',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'Lighthouse optimalizace'],
                                    ['text' => 'strukturovana data'],
                                    ['text' => 'prubezne testovani'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'process_steps',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Jak to probiha',
                        'title' => 'Jasny proces bez prekvapeni',
                        'description' => 'Soustredime se na transparentni plan a pravidelne milniky.',
                        'steps' => [
                            [
                                'step' => '01',
                                'title' => 'Workshop a zadani',
                                'description' => 'Definujeme cile, KPI a scope projektu.',
                                'icon_key' => 'chat',
                            ],
                            [
                                'step' => '02',
                                'title' => 'Design a prototyp',
                                'description' => 'Navrh UX toku, UI kit a prototyp k odsouhlaseni.',
                                'icon_key' => 'file-text',
                            ],
                            [
                                'step' => '03',
                                'title' => 'Vyvoj a QA',
                                'description' => 'Implementace, testy, optimalizace a priprava na launch.',
                                'icon_key' => 'cog',
                            ],
                            [
                                'step' => '04',
                                'title' => 'Spusteni a rust',
                                'description' => 'Nasazeni, analytika a prubezne zlepsovani.',
                                'icon_key' => 'check-circle',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'image_cta',
                    'position' => 40,
                    'data' => [
                        'subtitle' => 'Chcete to probrat?',
                        'title' => 'Ziskejte navrh reseni a cenovy ramec',
                        'description' => 'Rychla konzultace pomuze nastavit spravny smer projektu.',
                        'background_media_uuid' => null,
                        'primary' => ['label' => 'Domluvit konzultaci', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Proc s nami', 'link' => ['page_id' => null, 'url' => '/documentation', 'anchor' => null]],
                    ],
                ],
            ],
        ];
    }

    private function documentationPage(): array
    {
        return [
            'slug' => 'documentation',
            'title' => 'Dokumentace',
            'seo_meta' => [
                'title' => 'Dokumentace | Navody a checklisty pro web',
                'description' => 'Brief sablony, obsahove checklisty, SEO standardy a postupy pro spravu webu.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Dokumentace',
                        'title' => 'Navody, checklisty a technicke standardy',
                        'description' => 'Prakticke materialy pro zadani, obsah a spravu webu po spusteni.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Brief sablony'],
                            ['text' => 'Obsahove checklisty'],
                            ['text' => 'Technicke standardy'],
                        ],
                    ],
                ],
                [
                    'type' => 'documentation_search',
                    'position' => 20,
                    'data' => [
                        'placeholder' => 'Hledejte v navodech a sablonach...',
                        'button_label' => 'Hledat',
                        'quick_links' => [
                            ['label' => 'Brief', 'anchor' => 'brief'],
                            ['label' => 'SEO', 'anchor' => 'seo'],
                            ['label' => 'FAQ', 'anchor' => 'faq'],
                        ],
                    ],
                ],
                [
                    'type' => 'doc_categories',
                    'position' => 30,
                    'data' => [
                        'categories' => [
                            [
                                'title' => 'Brief a zadani',
                                'icon_key' => 'file-text',
                                'image_media_uuid' => null,
                                'docs' => [
                                    [
                                        'title' => 'Sablona zadani webu',
                                        'description' => 'Jak pripravit vstupy a cile projektu.',
                                        'type' => 'PDF',
                                        'size' => '1.1 MB',
                                        'file_url' => null,
                                    ],
                                    [
                                        'title' => 'Stakeholder checklist',
                                        'description' => 'Seznam informaci pro hladky start.',
                                        'type' => 'PDF',
                                        'size' => '720 KB',
                                        'file_url' => null,
                                    ],
                                ],
                            ],
                            [
                                'title' => 'Obsah a SEO',
                                'icon_key' => 'globe',
                                'image_media_uuid' => null,
                                'docs' => [
                                    [
                                        'title' => 'Obsahovy plan pro web',
                                        'description' => 'Struktura stranek, tone of voice a CTA.',
                                        'type' => 'DOCX',
                                        'size' => '860 KB',
                                        'file_url' => null,
                                    ],
                                    [
                                        'title' => 'SEO zaklady',
                                        'description' => 'On-page standardy a technicke minimum.',
                                        'type' => 'PDF',
                                        'size' => '540 KB',
                                        'file_url' => null,
                                    ],
                                ],
                            ],
                            [
                                'title' => 'Provoz a sprava',
                                'icon_key' => 'support',
                                'image_media_uuid' => null,
                                'docs' => [
                                    [
                                        'title' => 'Checklist po spusteni',
                                        'description' => 'Co zkontrolovat po deployi.',
                                        'type' => 'PDF',
                                        'size' => '610 KB',
                                        'file_url' => null,
                                    ],
                                    [
                                        'title' => 'Sprava obsahu v CMS',
                                        'description' => 'Doporuceny postup aktualizaci.',
                                        'type' => 'PDF',
                                        'size' => '950 KB',
                                        'file_url' => null,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'faq',
                    'position' => 40,
                    'data' => [
                        'subtitle' => 'Caste dotazy',
                        'title' => 'FAQ k tvorbe webu',
                        'description' => 'Odpovedi na nejcastejsi otazky ohledne spoluprace a procesu.',
                        'items' => [
                            [
                                'question' => 'Jak dlouho trva vytvorit novy web?',
                                'answer' => 'Bezne 4-8 tydnu podle rozsahu a dostupnosti vstupu.',
                            ],
                            [
                                'question' => 'Dodavate i obsah a copywriting?',
                                'answer' => 'Ano, pripravime obsahovy plan i texty na zaklade workshopu.',
                            ],
                            [
                                'question' => 'Mohu si obsah spravovat sam?',
                                'answer' => 'Ano, CMS je navrzene pro rychlou a bezpecnou spravu.',
                            ],
                            [
                                'question' => 'Jak probiha predani projektu?',
                                'answer' => 'Po schvaleni provedeme launch, vyskoleni a nastavime analytiku.',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function facilitiesPage(): array
    {
        return [
            'slug' => 'facilities',
            'title' => 'Zazemi',
            'seo_meta' => [
                'title' => 'Zazemi a tym | Jak pracujeme',
                'description' => 'Poznejte nas tym, lokace a standardy kvality. Podpora, proces a infrastruktura.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Nas tym a zazemi',
                        'title' => 'Kde vznikaji vase weby',
                        'description' => 'Rozdeleny tym designeru, vyvojaru a strategu. Pracujeme hybridne.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Product team'],
                            ['text' => 'QA a testovani'],
                            ['text' => 'Support 24/7'],
                        ],
                        'stats' => [
                            ['value' => '35+', 'label' => 'specialistu v tymu'],
                            ['value' => '3 lokaci', 'label' => 'v CR a SR'],
                        ],
                        'primary' => ['label' => 'Poptat projekt', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Poznat tym', 'link' => ['page_id' => null, 'url' => '/documentation', 'anchor' => null]],
                    ],
                ],
                [
                    'type' => 'facility_stats',
                    'position' => 20,
                    'data' => [
                        'subtitle' => 'Zazemi',
                        'title' => 'Kapacity pro rust projektu',
                        'stats' => [
                            ['value' => '120+ webu', 'label' => 'rocne realizujeme', 'icon_key' => 'check-circle'],
                            ['value' => '99.9 %', 'label' => 'uptime hostingu', 'icon_key' => 'shield'],
                            ['value' => '< 2h', 'label' => 'reakcni doba supportu', 'icon_key' => 'support'],
                        ],
                    ],
                ],
                [
                    'type' => 'facilities_grid',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Lokace',
                        'title' => 'Pracujeme napric regiony',
                        'description' => 'Kancelare, studio a vyvojove centrum pro rychlou spolupraci.',
                        'items' => [
                            [
                                'name' => 'Praha - Studio',
                                'location' => 'Praha',
                                'address' => 'Krizikova 12, 186 00 Praha',
                                'type' => 'Design a strategicke studio',
                                'size' => '12 mist',
                                'icon_key' => 'building',
                                'image_media_uuid' => null,
                                'phone' => '+420 123 456 789',
                                'email' => 'studio@example.com',
                                'manager' => 'Eva Novakova',
                                'hours' => 'Po-Pa 9:00-17:00',
                                'features' => [
                                    ['text' => 'workshopy s klienty'],
                                    ['text' => 'prototypovaci lab'],
                                ],
                                'certifications' => [
                                    ['text' => 'ISO 27001'],
                                    ['text' => 'WCAG ready process'],
                                ],
                            ],
                            [
                                'name' => 'Brno - Vyvojove centrum',
                                'location' => 'Brno',
                                'address' => 'Botanicka 8, 602 00 Brno',
                                'type' => 'Vyvoj a QA',
                                'size' => '20 mist',
                                'icon_key' => 'cog',
                                'image_media_uuid' => null,
                                'phone' => '+420 987 654 321',
                                'email' => 'dev@example.com',
                                'manager' => 'Jan Svoboda',
                                'hours' => 'Po-Pa 8:00-16:30',
                                'features' => [
                                    ['text' => 'automatizovane testy'],
                                    ['text' => 'performance lab'],
                                ],
                                'certifications' => [
                                    ['text' => 'OWASP practices'],
                                    ['text' => 'GDPR compliance'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'facility_standards',
                    'position' => 40,
                    'data' => [
                        'subtitle' => 'Standardy',
                        'title' => 'Jak drzime kvalitu',
                        'description' => 'Kombinace procesu, checklistu a automatizace.',
                        'items' => [
                            ['icon_key' => 'shield', 'title' => 'Bezpecnost', 'description' => 'Pen testy a pravidelne audity.'],
                            ['icon_key' => 'check-circle', 'title' => 'Kvalita kodu', 'description' => 'Code review a CI pipeline.'],
                            ['icon_key' => 'globe', 'title' => 'Dostupnost', 'description' => 'WCAG a performance standardy.'],
                        ],
                    ],
                ],
                [
                    'type' => 'support_cards',
                    'position' => 50,
                    'data' => [
                        'subtitle' => 'Podpora',
                        'title' => 'Co dostanete po spusteni',
                        'description' => 'Kontinualni zlepsovani a podpora rustu.',
                        'cards' => [
                            [
                                'icon_key' => 'support',
                                'title' => 'Kontinualni podpora',
                                'description' => 'Reakce na incidenty a pravidelne update.',
                                'link_label' => 'Zjistit vic',
                                'link' => ['page_id' => null, 'url' => '/documentation', 'anchor' => null],
                            ],
                            [
                                'icon_key' => 'calendar',
                                'title' => 'Planovani releasu',
                                'description' => 'Mesicni roadmapy a priority.',
                                'link_label' => 'Jak pracujeme',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                            [
                                'icon_key' => 'check',
                                'title' => 'Kvalita a SLA',
                                'description' => 'Dohled nad stabilitou a dostupnosti.',
                                'link_label' => 'Podminky',
                                'link' => ['page_id' => null, 'url' => '/documentation#sla', 'anchor' => null],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'map_placeholder',
                    'position' => 60,
                    'data' => [
                        'subtitle' => 'Kde nas najdete',
                        'title' => 'Pracujeme po cele CR',
                        'description' => 'Schuzky lze domluvit osobne nebo online.',
                        'note' => 'Mapa bude doplnena v dalsi fazi.',
                    ],
                ],
                [
                    'type' => 'image_cta',
                    'position' => 70,
                    'data' => [
                        'subtitle' => 'Chcete projekt resit s nami?',
                        'title' => 'Pojdme naplanovat dalsi kroky',
                        'description' => 'Domluvime si workshop a pripravime navrh reseni.',
                        'background_media_uuid' => null,
                        'primary' => ['label' => 'Rezervovat termin', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Prohlidnout sluzby', 'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null]],
                    ],
                ],
            ],
        ];
    }

    private function rfqPage(): array
    {
        return [
            'slug' => 'rfq',
            'title' => 'Poptavka',
            'seo_meta' => [
                'title' => 'Poptavka | Tvorba webu na miru',
                'description' => 'Popiste nam projekt a ziskejte navrh reseni do 48 hodin.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Poptavka',
                        'title' => 'Ziskejte navrh reseni do 48 hodin',
                        'description' => 'Popiste nam projekt a my pripravime prvni navrh postupu.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Bezplatna konzultace'],
                            ['text' => 'Rychla reakce'],
                            ['text' => 'Jasny plan'],
                        ],
                        'stats' => [
                            ['value' => '24h', 'label' => 'prvni reakce'],
                            ['value' => '48h', 'label' => 'navrh reseni'],
                        ],
                        'primary' => ['label' => 'Odeslat poptavku', 'link' => ['page_id' => null, 'url' => '#form', 'anchor' => null]],
                        'secondary' => ['label' => 'Vratit se na web', 'link' => ['page_id' => null, 'url' => '/', 'anchor' => null]],
                    ],
                ],
                [
                    'type' => 'trust_showcase',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Proc nam klienti veri',
                        'title' => 'Duvodne argumenty pro spolupraci',
                        'description' => 'Kombinujeme strategii, design a vyvoj do jednoho procesu.',
                        'cards' => [
                            ['icon_key' => 'check-circle', 'title' => 'Vysledky', 'description' => 'Metriky, ktere prokazatelne rostou.'],
                            ['icon_key' => 'shield', 'title' => 'Kvalita', 'description' => 'Procesy a QA na kazdem projektu.'],
                            ['icon_key' => 'support', 'title' => 'Podpora', 'description' => 'Stabilni tym a rychla reakce.'],
                        ],
                        'cta_title' => 'Chcete to probrat osobne?',
                        'cta_description' => 'Rezervujte si nezavaznou konzultaci.',
                        'cta_background_media_uuid' => null,
                        'cta_button' => ['label' => 'Domluvit konzultaci', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                    ],
                ],
            ],
        ];
    }

    private function useCasesPage(): array
    {
        return [
            'slug' => 'use-cases',
            'title' => 'Use cases',
            'seo_meta' => [
                'title' => 'Use cases | Ukazky projektu',
                'description' => 'Realne projekty s konkretnimi vysledky a dopady na byznys.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Use cases',
                        'title' => 'Ukazky realnych projektu',
                        'description' => 'Vybrane projekty s konkretnimi vysledky a poucenimi.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'B2B i B2C'],
                            ['text' => 'Redesign i novy web'],
                            ['text' => 'Meritelne vysledky'],
                        ],
                        'stats' => [
                            ['value' => '120+', 'label' => 'dokoncenych projektu'],
                            ['value' => '35 %', 'label' => 'prumerny rust konverzi'],
                        ],
                        'primary' => ['label' => 'Poptat projekt', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Nase sluzby', 'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null]],
                    ],
                ],
                [
                    'type' => 'use_case_tabs',
                    'position' => 20,
                    'data' => [
                        'subtitle' => 'Scenare nasazeni',
                        'title' => 'Jak pomahame ruznym firmam',
                        'description' => 'Konkretni situace a reseni pro ruzne segmenty.',
                        'items' => [
                            [
                                'industry' => 'SaaS a technologie',
                                'icon_key' => 'globe',
                                'image_media_uuid' => null,
                                'challenge' => 'Nizke konverze z trialu a nejasna value proposition.',
                                'solution' => 'Redesign, nove messaging a onboarding flow.',
                                'results' => [
                                    ['text' => '+42 % aktivaci trialu'],
                                    ['text' => '+28 % konverzni pomer'],
                                    ['text' => '-30 % bounce rate'],
                                ],
                            ],
                            [
                                'industry' => 'Sluzby a poradenstvi',
                                'icon_key' => 'briefcase',
                                'image_media_uuid' => null,
                                'challenge' => 'Slozity web, kteremu zakaznici nerozumeli.',
                                'solution' => 'Zjednodusena struktura a jasne CTA.',
                                'results' => [
                                    ['text' => '+55 % poptavek'],
                                    ['text' => '2x delsi cas na webu'],
                                    ['text' => 'lepsi kvalita leadu'],
                                ],
                            ],
                            [
                                'industry' => 'E-commerce',
                                'icon_key' => 'building',
                                'image_media_uuid' => null,
                                'challenge' => 'Pomale nacitani a slozity checkout.',
                                'solution' => 'Optimalizace performance a zrychleni nakupu.',
                                'results' => [
                                    ['text' => '-40 % doba nacitani'],
                                    ['text' => '+19 % objednavek'],
                                    ['text' => 'vyssi opakovane nakupy'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'stats_cards',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Metriky',
                        'title' => 'Co se u klientu typicky zlepsi',
                        'description' => 'Ukazkove dopady po nasazeni.',
                        'items' => [
                            ['value' => '+30 %', 'label' => 'navyseni organicke navstevnosti', 'icon_key' => 'globe'],
                            ['value' => '+25 %', 'label' => 'vyssi konverze', 'icon_key' => 'check-circle'],
                            ['value' => '-35 %', 'label' => 'kratsi doba nacitani', 'icon_key' => 'cog'],
                        ],
                    ],
                ],
                [
                    'type' => 'testimonials',
                    'position' => 40,
                    'data' => [
                        'title' => 'Zkusenosti klientu',
                        'subtitle' => 'Zadani, realizace, vysledek',
                        'description' => 'Kratke reference na spolupraci a dopad.',
                        'testimonials' => [
                            [
                                'quote' => 'Po redesignu mame jasnejsi onboarding a vice objednavek.',
                                'name' => 'Klara Vostrova',
                                'role' => 'Product Lead, Cloudpeak',
                                'rating' => 5,
                                'media_uuid' => null,
                            ],
                            [
                                'quote' => 'Diky strukture obsahu jsme zrychlili obchodni cyklus.',
                                'name' => 'Tomas Kral',
                                'role' => 'CEO, Brightdesk',
                                'rating' => 4.5,
                                'media_uuid' => null,
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'image_cta',
                    'position' => 50,
                    'data' => [
                        'subtitle' => 'Mate podobny projekt?',
                        'title' => 'Pojdme to spolecne rozjet',
                        'description' => 'Povidame si o vasich cilech a pripravime navrh reseni.',
                        'background_media_uuid' => null,
                        'primary' => ['label' => 'Zacit spolupraci', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Kontakt', 'link' => ['page_id' => null, 'url' => '/documentation', 'anchor' => null]],
                    ],
                ],
            ],
        ];
    }
}
