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
                'title' => 'Truhlarstvi na miru | Kvalitni nabytek a drevene vyrobky',
                'description' => 'Vyrабime nabytek na miru, kuchyne, vestavene skrine a drevene interiery. Tradicni remeslo s modernim pristupem.',
            ],
            'content' => [
                [
                    'type' => 'hero',
                    'position' => 10,
                    'data' => [
                        'title' => 'Nabytek na miru s dusi remesla',
                        'subtitle' => 'Truhlarstvi s tradicí',
                        'description' => 'Vyrабime kvalitni nabytek, kuchyne a drevene interiery presne podle vasich predstav. Kazdy kus je original vytvoreny s laskou k drevu.',
                        'background_media_uuid' => null,
                        'primary' => [
                            'label' => 'Nezavazna poptavka',
                            'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null],
                        ],
                        'secondary' => [
                            'label' => 'Nase realizace',
                            'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null],
                        ],
                        'stats' => [
                            ['value' => '500+', 'label' => 'Vyrobenych kusu'],
                            ['value' => '25+', 'label' => 'Let zkusenosti'],
                            ['value' => '98%', 'label' => 'Spokojenych zakazniku'],
                        ],
                    ],
                ],
                [
                    'type' => 'service_highlights',
                    'position' => 20,
                    'data' => [
                        'title' => 'Nase sluzby',
                        'subtitle' => 'Co pro vas vyrobime',
                        'description' => 'Nabizime kompletni truhlarsky servis od navrhu az po montaz. Pracujeme s masivnim drevem i modernimí materialy.',
                        'more_info_label' => 'Vice informaci',
                        'services' => [
                            [
                                'title' => 'Kuchyne na miru',
                                'description' => 'Navrhneme a vyrobime kuchynskou linku presne do vaseho prostoru. Od klasickych az po moderne minimalisticke.',
                                'icon_key' => 'building',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                            [
                                'title' => 'Vestavene skrine',
                                'description' => 'Maximalne vyuzijeme kazdy centimetr. Vestavene skrine, satny a ulozne systemy na miru.',
                                'icon_key' => 'briefcase',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                            [
                                'title' => 'Nabytek z masivu',
                                'description' => 'Jidelni stoly, zidle, postele a dalsi nabytek z masivniho dreva. Kazdy kus je jedinecny.',
                                'icon_key' => 'star',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                            [
                                'title' => 'Interiery a obklady',
                                'description' => 'Drevene obklady sten, stropni podhled, schodiste a dalsi interiérové prvky.',
                                'icon_key' => 'cog',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                        ],
                        'cta' => [
                            'label' => 'Vsechny sluzby',
                            'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                        ],
                    ],
                ],
                [
                    'type' => 'image_grid',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Portfolio',
                        'title' => 'Ukazky nasi prace',
                        'description' => 'Kazdy projekt je pro nas unikatni. Podivejte se na nase realizace z poslednich let.',
                        'items' => [
                            [
                                'title' => 'Dubova kuchyne',
                                'description' => 'Kuchynska linka z masivu dubu s granitovou deskou.',
                                'image_media_uuid' => null,
                            ],
                            [
                                'title' => 'Vestavena satna',
                                'description' => 'Prostorna satna na miru v podkrovi rodinneho domu.',
                                'image_media_uuid' => null,
                            ],
                            [
                                'title' => 'Jidelni stul pro 10 osob',
                                'description' => 'Masivni orechovy stul s lavicemi pro velkou rodinu.',
                                'image_media_uuid' => null,
                            ],
                        ],
                        'cta' => [
                            'label' => 'Zobrazit vse',
                            'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null],
                        ],
                    ],
                ],
                [
                    'type' => 'industries_served',
                    'position' => 40,
                    'data' => [
                        'subtitle' => 'Pro koho pracujeme',
                        'title' => 'Nasi zakaznici',
                        'description' => 'Vyrابime na miru pro domacnosti, firmy i architektonicka studia.',
                        'items' => [
                            [
                                'title' => 'Rodinne domy a byty',
                                'description' => 'Kompletni vybaveni interieru — kuchyne, skrine, postele, stoly a dalsi nabytek.',
                                'icon_key' => 'building',
                                'features' => [
                                    ['text' => 'Bezplatne zamereni'],
                                    ['text' => '3D vizualizace'],
                                    ['text' => 'Montaz v cene'],
                                ],
                            ],
                            [
                                'title' => 'Komercni prostory',
                                'description' => 'Vybaveni kancelari, recepcí, obchodu a restauraci na miru.',
                                'icon_key' => 'briefcase',
                                'features' => [
                                    ['text' => 'Seriova i zakazkova vyroba'],
                                    ['text' => 'Splneni hygienickych norem'],
                                    ['text' => 'Koordinace s architektem'],
                                ],
                            ],
                            [
                                'title' => 'Architekti a designeri',
                                'description' => 'Realizace navrhu dle vasich projektu s durazem na detail a materialovou presnost.',
                                'icon_key' => 'star',
                                'features' => [
                                    ['text' => 'Vyroba dle vykresove dokumentace'],
                                    ['text' => 'Netradicni materialy a povrchy'],
                                    ['text' => 'Vzorky a prototypy'],
                                ],
                            ],
                        ],
                        'cta' => [
                            'label' => 'Nase reference',
                            'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null],
                        ],
                    ],
                ],
                [
                    'type' => 'technology_innovation',
                    'position' => 50,
                    'data' => [
                        'subtitle' => 'Technologie a material',
                        'title' => 'Tradicni remeslo, moderni pristup',
                        'description' => 'Spojujeme rucni zpracovani s CNC technologii pro maximalni presnost a kvalitu.',
                        'image_media_uuid' => null,
                        'items' => [
                            [
                                'title' => 'CNC obrабeni',
                                'description' => 'Presne frezovani a rezani na CNC strojich pro slozite tvary a opakovatelnou kvalitu.',
                                'icon_key' => 'cog',
                            ],
                            [
                                'title' => 'Certifikovane drevo',
                                'description' => 'Pouzivame drevo z udrzitelnych zdroju s FSC certifikaci. Dub, buk, orech, jasan.',
                                'icon_key' => 'shield',
                            ],
                            [
                                'title' => 'Povrchove upravy',
                                'description' => 'Olejovani, moridla, laky i specialni povrchove upravy pro dlouhou zivotnost.',
                                'icon_key' => 'star',
                            ],
                        ],
                        'cta' => [
                            'label' => 'Nase schopnosti',
                            'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                        ],
                    ],
                ],
                [
                    'type' => 'process_workflow',
                    'position' => 60,
                    'data' => [
                        'subtitle' => 'Jak pracujeme',
                        'title' => 'Od napadu k hotovemu vyrobku',
                        'description' => 'Provedeme vas celym procesem — od prvni konzultace az po montaz a predani.',
                        'steps' => [
                            [
                                'number' => '01',
                                'title' => 'Konzultace a zamereni',
                                'description' => 'Prijdeme k vam, zamerime prostor a probreme vase predstavy. Konzultace je zdarma.',
                                'image_media_uuid' => null,
                            ],
                            [
                                'number' => '02',
                                'title' => 'Navrh a vizualizace',
                                'description' => 'Pripravime detailni navrh vcetne 3D vizualizace a vybereme materialy.',
                                'image_media_uuid' => null,
                            ],
                            [
                                'number' => '03',
                                'title' => 'Vyroba v dilne',
                                'description' => 'Kazdy kus vyrابime v nasi dilne s durazem na kvalitu zpracovani a detail.',
                                'image_media_uuid' => null,
                            ],
                            [
                                'number' => '04',
                                'title' => 'Montaz a predani',
                                'description' => 'Profesionalni montaz na miste a predani hotoveho vyrobku vcetne zarucniho listu.',
                                'image_media_uuid' => null,
                            ],
                        ],
                        'benefits' => [
                            ['icon_key' => 'check-circle', 'title' => 'Bezplatna konzultace', 'description' => 'Zamereni a prvni konzultace u vas doma zdarma.'],
                            ['icon_key' => 'calendar', 'title' => 'Dodrzovani terminu', 'description' => 'Jasny harmonogram a komunikace o stavu zakazky.'],
                            ['icon_key' => 'shield', 'title' => 'Zaruka 5 let', 'description' => 'Na vsechny nase vyrobky poskytujeme petiletou zaruku.'],
                        ],
                    ],
                ],
                [
                    'type' => 'stats_showcase',
                    'position' => 70,
                    'data' => [
                        'subtitle' => 'Cisla mluvi za nas',
                        'title' => 'Nase vysledky',
                        'description' => 'Zakazky, ktere mluvi za kvalitu nasi prace.',
                        'background_media_uuid' => null,
                        'stats' => [
                            ['value' => '500+', 'label' => 'Vyrobenych kusu nabytku', 'icon_key' => 'check-circle'],
                            ['value' => '25+', 'label' => 'Let na trhu', 'icon_key' => 'calendar'],
                            ['value' => '200+', 'label' => 'Spokojenych rodin', 'icon_key' => 'user'],
                            ['value' => '5 let', 'label' => 'Zaruka na vsechno', 'icon_key' => 'shield'],
                        ],
                        'logos' => [
                            ['label' => 'Interiery CZ'],
                            ['label' => 'ArchStudio'],
                            ['label' => 'BydleniPlus'],
                        ],
                    ],
                ],
                [
                    'type' => 'testimonials',
                    'position' => 80,
                    'data' => [],
                ],
                [
                    'type' => 'premium_cta',
                    'position' => 90,
                    'data' => [
                        'title' => 'Mate napad na novy nabytek?',
                        'subtitle' => 'Pojdme ho spolecne vytvorit',
                        'description' => 'Ozvete se nam a domluvime si bezplatnou konzultaci primo u vas doma.',
                        'background_media_uuid' => null,
                        'buttons' => [
                            [
                                'label' => 'Nezavazna poptavka',
                                'page_id' => null,
                                'url' => '/rfq',
                                'style' => 'primary',
                            ],
                            [
                                'label' => 'Nase realizace',
                                'page_id' => null,
                                'url' => '/use-cases',
                                'style' => 'secondary',
                            ],
                        ],
                        'stats' => [
                            ['value' => '24h', 'label' => 'Odpoved na poptavku'],
                            ['value' => 'Zdarma', 'label' => 'Konzultace a zamereni'],
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
                'title' => 'Nase sluzby | Truhlarstvi na miru',
                'description' => 'Kuchyne, skrine, nabytek z masivu, interiery. Kompletni truhlarsky servis od navrhu po montaz.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Nase sluzby',
                        'title' => 'Kompletni truhlarsky servis',
                        'description' => 'Od navrhu az po montaz — vse pod jednou strechou. Pracujeme s masivem, dyhou i modernimí materialy.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Nabytek na miru'],
                            ['text' => 'Masivni drevo'],
                            ['text' => '5 let zaruka'],
                        ],
                        'stats' => [
                            ['value' => '500+', 'label' => 'Realizaci'],
                            ['value' => '25+', 'label' => 'Let praxe'],
                        ],
                        'primary' => ['label' => 'Poptat zakazku', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Nase realizace', 'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null]],
                    ],
                ],
                [
                    'type' => 'capabilities_detailed',
                    'position' => 20,
                    'data' => [
                        'title' => 'Co vsechno umime',
                        'subtitle' => 'Detailni prehled nasich sluzeb',
                        'items' => [
                            [
                                'title' => 'Kuchyne na miru',
                                'description' => 'Navrhneme a vyrobime kuchynskou linku, ktera presne sedi do vaseho prostoru a odpovidа vasemu stylu.',
                                'icon_key' => 'building',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'Bezplatne zamereni u vas doma'],
                                    ['text' => '3D vizualizace pred vyrobou'],
                                    ['text' => 'Masiv, dyha i lamino'],
                                    ['text' => 'Vcetne spotrebicu a prislusenstvi'],
                                ],
                            ],
                            [
                                'title' => 'Vestavene skrine a satny',
                                'description' => 'Vyuzijeme kazdy centimetr prostoru. Vestavne skrine, pochozi satny a ulozne systemy.',
                                'icon_key' => 'briefcase',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'Atypicke rozmery a tvary'],
                                    ['text' => 'Posuvne i kridlove dvere'],
                                    ['text' => 'Vnitrni organizery na miru'],
                                    ['text' => 'LED osvetleni'],
                                ],
                            ],
                            [
                                'title' => 'Nabytek z masivu',
                                'description' => 'Jidelni stoly, zidle, postele, komody a dalsi nabytek z masivniho dreva na zakazku.',
                                'icon_key' => 'star',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'Dub, orech, jasan, buk'],
                                    ['text' => 'Rucni i strojove zpracovani'],
                                    ['text' => 'Prirozene olejovane povrchy'],
                                    ['text' => 'Kazdy kus je original'],
                                ],
                            ],
                            [
                                'title' => 'Interiery a specialni projekty',
                                'description' => 'Drevene obklady, schodiste, knihovny, pracovny — realizujeme i slozitejsi interiérové projekty.',
                                'icon_key' => 'cog',
                                'image_media_uuid' => null,
                                'features' => [
                                    ['text' => 'Drevene obklady sten a stropu'],
                                    ['text' => 'Schodiste a zabradli'],
                                    ['text' => 'Knihovny a pracovni stoly'],
                                    ['text' => 'Spoluprace s architekty'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'process_steps',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Jak to probíha',
                        'title' => 'Nas overeny postup',
                        'description' => 'Kazda zakazka prochazi jasne definovanym procesem od prvniho kontaktu az po montaz.',
                        'steps' => [
                            [
                                'step' => '01',
                                'title' => 'Prvni konzultace',
                                'description' => 'Probreme vase predstavy, zamerime prostor a doporucime materialy. Zdarma a nezavazne.',
                                'icon_key' => 'chat',
                            ],
                            [
                                'step' => '02',
                                'title' => 'Navrh a cenova nabidka',
                                'description' => 'Pripravime detailni navrh, 3D vizualizaci a cenovou nabidku ke schvaleni.',
                                'icon_key' => 'file-text',
                            ],
                            [
                                'step' => '03',
                                'title' => 'Vyroba',
                                'description' => 'Po odsouhlaseni zacneme s vyrobou v nasi dilne. Informujeme vas o prubehu.',
                                'icon_key' => 'cog',
                            ],
                            [
                                'step' => '04',
                                'title' => 'Montaz a predani',
                                'description' => 'Profesionalni montaz na miste, kontrola kvality a predani zarucniho listu.',
                                'icon_key' => 'check-circle',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'image_cta',
                    'position' => 40,
                    'data' => [
                        'subtitle' => 'Zaujala vas nase nabidka?',
                        'title' => 'Nechte si vyrobit nabytek na miru',
                        'description' => 'Kontaktujte nas pro bezplatnou konzultaci a zamereni. Ozveme se vam do 24 hodin.',
                        'background_media_uuid' => null,
                        'primary' => ['label' => 'Poptat zakazku', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Nase realizace', 'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null]],
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
                'title' => 'Dokumentace | Navody k udrzbe nabytku',
                'description' => 'Navody na udrzbu dreva, peci o nabytek a technicke informace pro nase zakazniky.',
            ],
            'content' => [
                [
                    'type' => 'documentation_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Dokumentace',
                        'title' => 'Navody a pece o nabytek',
                        'description' => 'Vse, co potrebujete vedet o udrzbe vaseho nabytku, materiálech a nasich sluzbach.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Udrzba dreva'],
                            ['text' => 'Materialove listy'],
                            ['text' => 'FAQ'],
                        ],
                    ],
                ],
                [
                    'type' => 'documentation_search',
                    'position' => 20,
                    'data' => [
                        'placeholder' => 'Hledejte v navodech...',
                        'button_label' => 'Hledat',
                        'quick_links' => [
                            ['label' => 'Pece o masiv', 'anchor' => 'pece-o-masiv'],
                            ['label' => 'Materialove listy', 'anchor' => 'materialy'],
                            ['label' => 'Caste dotazy', 'anchor' => 'faq'],
                        ],
                    ],
                ],
                [
                    'type' => 'doc_categories',
                    'position' => 30,
                    'data' => [
                        'categories' => [
                            [
                                'title' => 'Pece o dreveny nabytek',
                                'icon_key' => 'info',
                                'image_media_uuid' => null,
                                'docs' => [
                                    [
                                        'title' => 'Udrzba olejovaneho dreva',
                                        'description' => 'Jak spravne osetrit a udrzovat olejovane povrchy. Doporucene oleje a postup.',
                                        'type' => 'PDF',
                                        'size' => '1.2 MB',
                                        'file_url' => null,
                                    ],
                                    [
                                        'title' => 'Pece o lakovaný nabytek',
                                        'description' => 'Cisteni, ochrana a obnovа lakovanych povrchu.',
                                        'type' => 'PDF',
                                        'size' => '980 KB',
                                        'file_url' => null,
                                    ],
                                ],
                            ],
                            [
                                'title' => 'Materialove listy',
                                'icon_key' => 'file-text',
                                'image_media_uuid' => null,
                                'docs' => [
                                    [
                                        'title' => 'Druhy dreva — prehled',
                                        'description' => 'Vlastnosti dubu, orechu, jasanu, buku a dalsich drevin, ktere pouzivame.',
                                        'type' => 'PDF',
                                        'size' => '2.1 MB',
                                        'file_url' => null,
                                    ],
                                    [
                                        'title' => 'Povrchove upravy',
                                        'description' => 'Porovnani oleju, laku, morídel a vosku — vlastnosti a pouziti.',
                                        'type' => 'PDF',
                                        'size' => '1.5 MB',
                                        'file_url' => null,
                                    ],
                                ],
                            ],
                            [
                                'title' => 'Zarucni podminky',
                                'icon_key' => 'shield',
                                'image_media_uuid' => null,
                                'docs' => [
                                    [
                                        'title' => 'Zarucni podminky',
                                        'description' => 'Rozsah zaruky, reklamacni postup a kontakty pro servisni pozadavky.',
                                        'type' => 'PDF',
                                        'size' => '600 KB',
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
                        'subtitle' => 'Casto kladene otazky',
                        'title' => 'FAQ',
                        'description' => 'Odpovedi na nejcastejsi otazky nasich zakazniku.',
                        'items' => [
                            [
                                'question' => 'Jak dlouho trva vyroba nabytku na miru?',
                                'answer' => 'Zavisí na rozsahu zakazky. Jednodussi kusy (police, stoly) jsou hotove do 3-4 tydnu. Slozitejsi projekty jako kuchyne nebo kompletni vybaveni bytu mohou trvat 6-10 tydnu.',
                            ],
                            [
                                'question' => 'Jake drevo pouzivate?',
                                'answer' => 'Nejcasteji pracujeme s dubem, oreche, jasanem a bukem. Pouzivame certifikovane drevo z udrzitelnych zdroju. Dle priání klienta muzeme zpracovat i exoticke dreviny.',
                            ],
                            [
                                'question' => 'Je konzultace a zamereni zdarma?',
                                'answer' => 'Ano, prvni konzultace vcetne zamereni u vas doma je zcela zdarma a nezavazna. Na zaklade zamereni pripravime navrh a cenovou nabidku.',
                            ],
                            [
                                'question' => 'Jak se o dreveny nabytek starat?',
                                'answer' => 'Olejovany nabytek doporucujeme 1-2x rocne preolejovat. Lakovaný staci otrit vlhkym hadrikem. Detailni navody najdete v nasi sekci dokumentace.',
                            ],
                            [
                                'question' => 'Poskytujete zaruku?',
                                'answer' => 'Ano, na vsechny nase vyrobky poskytujeme zaruku 5 let. V pripade potreb nabizime i pozarucni servis a udrzbu.',
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
            'title' => 'Nase dilna',
            'seo_meta' => [
                'title' => 'Nase dilna a zazemí | Truhlarstvi',
                'description' => 'Podivejte se, kde vznika vas nabytek. Moderni dilna s tradicnim prístupem k remeslu.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Nase zazemí',
                        'title' => 'Dilna, kde vznika vas nabytek',
                        'description' => 'Moderni vybavena dilna s CNC strojnim parkем a prostorem pro rucni zpracovani. Kombinujeme tradici s technologiemi.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'CNC frezka'],
                            ['text' => 'Lakovaci box'],
                            ['text' => 'Sklad dreva'],
                        ],
                        'stats' => [
                            ['value' => '800 m2', 'label' => 'Plocha dilny'],
                            ['value' => '12', 'label' => 'Truhlaru'],
                        ],
                        'primary' => ['label' => 'Domluvte si navstevu', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => null,
                    ],
                ],
                [
                    'type' => 'facility_stats',
                    'position' => 20,
                    'data' => [
                        'subtitle' => 'V cislech',
                        'title' => 'Nase dilna a tym',
                        'stats' => [
                            ['value' => '800 m2', 'label' => 'Vyrobni plocha', 'icon_key' => 'building'],
                            ['value' => '12', 'label' => 'Truhlaru a stolaru', 'icon_key' => 'user'],
                            ['value' => '3', 'label' => 'CNC stroje', 'icon_key' => 'cog'],
                            ['value' => '25+', 'label' => 'Let tradice', 'icon_key' => 'calendar'],
                        ],
                    ],
                ],
                [
                    'type' => 'facilities_grid',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Nase prostory',
                        'title' => 'Dilna a showroom',
                        'description' => 'Navstivte nasi dilnu a showroom, kde si muzete prohlednout vzorky materialu a hotove vyrobky.',
                        'items' => [
                            [
                                'name' => 'Hlavni dilna',
                                'location' => 'Ricany u Prahy',
                                'address' => 'Prumyslova 142, 251 01 Ricany',
                                'type' => 'Vyrobni dilna',
                                'size' => '800 m2',
                                'icon_key' => 'building',
                                'image_media_uuid' => null,
                                'phone' => '+420 123 456 789',
                                'email' => 'dilna@truhlarstvi.cz',
                                'manager' => 'Josef Dvorak',
                                'hours' => 'Po-Pa 7:00 - 16:00',
                                'features' => [
                                    ['text' => 'CNC strojni park'],
                                    ['text' => 'Lakovaci a moridlova kabina'],
                                    ['text' => 'Sklad masivniho dreva'],
                                    ['text' => 'Vzorkovna materialu'],
                                ],
                                'certifications' => [
                                    ['text' => 'FSC certifikace'],
                                    ['text' => 'ISO 9001'],
                                ],
                            ],
                            [
                                'name' => 'Showroom',
                                'location' => 'Praha 4, Nusle',
                                'address' => 'Táborská 65, 140 00 Praha 4',
                                'type' => 'Showroom',
                                'size' => '120 m2',
                                'icon_key' => 'star',
                                'image_media_uuid' => null,
                                'phone' => '+420 987 654 321',
                                'email' => 'showroom@truhlarstvi.cz',
                                'manager' => 'Marie Novakova',
                                'hours' => 'Po-Pa 10:00 - 18:00, So 10:00 - 14:00',
                                'features' => [
                                    ['text' => 'Ukazky hotovych vyrobku'],
                                    ['text' => 'Vzorky dreva a povrchu'],
                                    ['text' => 'Konzultacni mistnost'],
                                ],
                                'certifications' => [],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'facility_standards',
                    'position' => 40,
                    'data' => [
                        'subtitle' => 'Nase standardy',
                        'title' => 'Na cem stojime',
                        'description' => 'Kvalita, tradicе a udrзitelnost jsou pilire nasi prace.',
                        'items' => [
                            ['icon_key' => 'shield', 'title' => 'Certifikovane materialy', 'description' => 'FSC drevo z udrзitelnych zdroju, ekologicke laky a oleje.'],
                            ['icon_key' => 'check-circle', 'title' => 'Kontrola kvality', 'description' => 'Kazdy kus prochazi vnitrni kontrolou pred expedici.'],
                            ['icon_key' => 'star', 'title' => 'Remeslna tradice', 'description' => '25 let zkusenosti a rucni zpracovani s cilom pro detail.'],
                        ],
                    ],
                ],
                [
                    'type' => 'support_cards',
                    'position' => 50,
                    'data' => [
                        'subtitle' => 'Proc si nas vybrat',
                        'title' => 'Vase vyhody',
                        'description' => 'Co ziskate, kdyz se rozhodnete pro nase truhlarstvi.',
                        'cards' => [
                            [
                                'icon_key' => 'support',
                                'title' => 'Pozarucni servis',
                                'description' => 'I po zaruce se o vas nabytek postarame — opravy, preolejovani, upravy.',
                                'link_label' => 'Kontakt',
                                'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null],
                            ],
                            [
                                'icon_key' => 'chat',
                                'title' => 'Osobni pristup',
                                'description' => 'Kazda zakazka ma sveho mistra, ktery vas provede celym procesem.',
                                'link_label' => 'Vice info',
                                'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null],
                            ],
                            [
                                'icon_key' => 'shield',
                                'title' => 'Zaruka 5 let',
                                'description' => 'Na vsechny vyrobky poskytujeme petiletou zaruku na konstrukci i povrch.',
                                'link_label' => 'Reference',
                                'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'map_placeholder',
                    'position' => 60,
                    'data' => [
                        'subtitle' => 'Kde nas najdete',
                        'title' => 'Mapa nasi dilny a showroomu',
                        'description' => 'Navstivte nasi dilnu v Ricanech nebo showroom v Praze.',
                        'note' => 'Interaktivni mapa bude doplnena.',
                    ],
                ],
                [
                    'type' => 'image_cta',
                    'position' => 70,
                    'data' => [
                        'subtitle' => 'Chcete videt nasi praci na zivo?',
                        'title' => 'Navstivte nas showroom',
                        'description' => 'Prohledrete si nase vyrobky, osahejte materialy a proberte svuj projekt s nasim navrharem.',
                        'background_media_uuid' => null,
                        'primary' => ['label' => 'Domluvit navstevu', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => null,
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
                'title' => 'Poptavka | Nabytek na miru — bezplatna konzultace',
                'description' => 'Popiste nam svuj projekt a my pripravime cenovou nabidku. Zamereni a konzultace zdarma.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Poptavka',
                        'title' => 'Popiste nam svuj projekt',
                        'description' => 'Vyplnte formular a ozveme se vam do 24 hodin. Konzultace a zamereni je zdarma.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Odpoved do 24h'],
                            ['text' => 'Zamereni zdarma'],
                            ['text' => 'Bez zavazku'],
                        ],
                        'stats' => [],
                        'primary' => null,
                        'secondary' => null,
                    ],
                ],
                [
                    'type' => 'rfq_form_sidebar',
                    'position' => 20,
                    'data' => [
                        'form_id' => 'rfq-truhlar',
                        'sidebar_title' => 'Kontaktni udaje',
                        'contact_items' => [
                            ['icon_key' => 'mail', 'title' => 'Email', 'value' => 'info@truhlarstvi.cz', 'helper' => 'Odpovime do 24 hodin'],
                            ['icon_key' => 'phone', 'title' => 'Telefon', 'value' => '+420 123 456 789', 'helper' => 'Po-Pa 7:00 - 16:00'],
                            ['icon_key' => 'building', 'title' => 'Dilna', 'value' => 'Ricany u Prahy', 'helper' => 'Moznost navstevy po domluva'],
                        ],
                        'steps' => [
                            ['step' => '1', 'title' => 'Popiste svuj projekt', 'description' => 'Rekrete nam, co potrebujete — rozmer, material, styl.'],
                            ['step' => '2', 'title' => 'Bezplatne zamereni', 'description' => 'Prijdeme k vam, zamerime prostor a probreme detaily.'],
                            ['step' => '3', 'title' => 'Navrh a nabidka', 'description' => 'Pripravime 3D vizualizaci a cenovou nabidku.'],
                        ],
                        'trust_items' => [
                            ['icon_key' => 'shield', 'text' => '5 let zaruka na vsechny vyrobky'],
                            ['icon_key' => 'check-circle', 'text' => '500+ vyrobenych kusu nabytku'],
                            ['icon_key' => 'star', 'text' => '98% spokojenych zakazniku'],
                        ],
                    ],
                ],
                [
                    'type' => 'trust_showcase',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Proc nam duvеrovat',
                        'title' => 'Kvalita overena casem',
                        'description' => '25 let na trhu, stovky spokojenych zakazniku a nabytek, ktery vydrzi generace.',
                        'cards' => [
                            ['icon_key' => 'calendar', 'title' => '25 let tradice', 'description' => 'Ctvrt stoleti zkusenosti s vyrobou nabytku na miru.'],
                            ['icon_key' => 'shield', 'title' => 'Certifikovane materialy', 'description' => 'FSC drevo, ekologicke laky a overeni dodavatele.'],
                            ['icon_key' => 'check-circle', 'title' => '5 let zaruka', 'description' => 'Na kazdy vyrobek poskytujeme plnou peteletou zaruku.'],
                        ],
                        'cta_title' => 'Nechte se inspirovat',
                        'cta_description' => 'Podivejte se na nase realizace a zjistete, co vsechno umime.',
                        'cta_background_media_uuid' => null,
                        'cta_button' => [
                            'label' => 'Nase realizace',
                            'link' => ['page_id' => null, 'url' => '/use-cases', 'anchor' => null],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function useCasesPage(): array
    {
        return [
            'slug' => 'use-cases',
            'title' => 'Reference',
            'seo_meta' => [
                'title' => 'Reference | Nase realizace nabytku na miru',
                'description' => 'Podivejte se na realizace nasich zakazniku — kuchyne, skrine, stoly a dalsi nabytek z masivu.',
            ],
            'content' => [
                [
                    'type' => 'page_hero',
                    'position' => 10,
                    'data' => [
                        'subtitle' => 'Reference',
                        'title' => 'Nase realizace',
                        'description' => 'Kazdy kus nabytku ma svuj pribeh. Podivejte se, jak jsme pomohli nasim zakaznikum s vybavenim jejich domovu a firem.',
                        'background_media_uuid' => null,
                        'badges' => [
                            ['text' => 'Kuchyne'],
                            ['text' => 'Nabytek z masivu'],
                            ['text' => 'Interiery'],
                        ],
                        'stats' => [
                            ['value' => '500+', 'label' => 'Realizaci'],
                            ['value' => '200+', 'label' => 'Spokojenych rodin'],
                        ],
                        'primary' => ['label' => 'Poptat podobnou zakazku', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Nase sluzby', 'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null]],
                    ],
                ],
                [
                    'type' => 'use_case_tabs',
                    'position' => 20,
                    'data' => [
                        'subtitle' => 'Pripadove studie',
                        'title' => 'Vybrané realizace',
                        'description' => 'Detailni pohled na nektere z nasich zakazek.',
                        'items' => [
                            [
                                'industry' => 'Kuchyne na miru',
                                'icon_key' => 'building',
                                'image_media_uuid' => null,
                                'challenge' => 'Rodina v novostavbe potrebovala kuchyni do atypickeho prostoru s ostruvkem a velkou uloznou kapacitou. Standardni reseni ze salone nevyhovovalo.',
                                'solution' => 'Navrhli jsme kuchyni z masivu dubu s granitovou pracovni deskou. Ostruvek slouzí zaroven jako jidelni stul. Vsechny skrinky jsou na miru s soft-close kovaиim.',
                                'results' => [
                                    ['text' => 'Dokonale vyuziti kazdeho centimetru'],
                                    ['text' => '40% vice ulozneho prostoru oproti standardu'],
                                    ['text' => 'Dubovy masiv s 5letou zarukou'],
                                    ['text' => 'Kompletni realizace za 8 tydnu'],
                                ],
                            ],
                            [
                                'industry' => 'Firemni interiery',
                                'icon_key' => 'briefcase',
                                'image_media_uuid' => null,
                                'challenge' => 'Architektonicke studio potrebovalo vybavit kancelare vcetne recepce, jednacich mistnosti a pracovnich stolu. Pozadavek na jednotny vizualni styl.',
                                'solution' => 'Vyrobili jsme kompletni kancelarsky nabytek z jasanu vcetne recepcniho pultu, konferencniho stolu pro 12 osob a 15 pracovnich stolu s kabelovym managementem.',
                                'results' => [
                                    ['text' => 'Jednotny design celeho interieru'],
                                    ['text' => 'Ergonomicke pracovni stoly'],
                                    ['text' => 'Reprezentativni recepce z masivu'],
                                ],
                            ],
                            [
                                'industry' => 'Rodinne domy',
                                'icon_key' => 'user',
                                'image_media_uuid' => null,
                                'challenge' => 'Klient rekonstruoval starsi rodinny dum a chtel kompletni vybavení — vestavene skrine, schodiste, knihovnu a nabytek do obyvaku.',
                                'solution' => 'Realizovali jsme cely interier v duchu moderniho venkovskeho stylu. Dubove schodiste, pochozi satna v podkrovи, knihovna pres celou stenu a jidelni set pro 8 osob.',
                                'results' => [
                                    ['text' => 'Kompletni vybaveni domu z jednoho zdroje'],
                                    ['text' => 'Konzistentni design vsech prvku'],
                                    ['text' => 'Realizace v prubehu 3 mesicu'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'stats_cards',
                    'position' => 30,
                    'data' => [
                        'subtitle' => 'Nase vysledky',
                        'title' => 'Cisla, ktera mluvi za nas',
                        'description' => 'Kazdy kus nabytku je pro nas zavazek ke kvalite.',
                        'items' => [
                            ['value' => '500+', 'label' => 'Vyrobenych kusu', 'icon_key' => 'check-circle'],
                            ['value' => '25+', 'label' => 'Let na trhu', 'icon_key' => 'calendar'],
                            ['value' => '98%', 'label' => 'Spokojenych zakazniku', 'icon_key' => 'star'],
                            ['value' => '5 let', 'label' => 'Zaruka na kazdy vyrobek', 'icon_key' => 'shield'],
                        ],
                    ],
                ],
                [
                    'type' => 'testimonials',
                    'position' => 40,
                    'data' => [],
                ],
                [
                    'type' => 'image_cta',
                    'position' => 50,
                    'data' => [
                        'subtitle' => 'Zaujaly vas nase realizace?',
                        'title' => 'Nechte si vyrobit nabytek na miru',
                        'description' => 'Ozvete se nam a domluvime bezplatnou konzultaci a zamereni. Vyrobime presne to, co potrebujete.',
                        'background_media_uuid' => null,
                        'primary' => ['label' => 'Poptat zakazku', 'link' => ['page_id' => null, 'url' => '/rfq', 'anchor' => null]],
                        'secondary' => ['label' => 'Nase sluzby', 'link' => ['page_id' => null, 'url' => '/capabilities', 'anchor' => null]],
                    ],
                ],
            ],
        ];
    }
}
