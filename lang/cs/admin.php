<?php

return [
    // Navigation groups
    'navigation' => [
        'content' => 'Obsah',
        'products' => 'Produkty',
        'commerce' => 'E-commerce',
        'marketing' => 'Marketing',
        'settings' => 'Nastavení',
    ],

    // Common actions
    'actions' => [
        'create' => 'Vytvořit',
        'edit' => 'Upravit',
        'delete' => 'Smazat',
        'save' => 'Uložit',
        'cancel' => 'Zrušit',
        'view' => 'Zobrazit',
        'back' => 'Zpět',
        'search' => 'Hledat',
        'filter' => 'Filtrovat',
        'export' => 'Exportovat',
        'import' => 'Importovat',
        'preview' => 'Náhled',
        'close' => 'Zavřít',
    ],

    // Common labels
    'labels' => [
        'title' => 'Název',
        'slug' => 'URL slug',
        'status' => 'Stav',
        'created_at' => 'Vytvořeno',
        'updated_at' => 'Upraveno',
        'published_at' => 'Publikováno',
        'description' => 'Popis',
        'content' => 'Obsah',
        'image' => 'Obrázek',
        'email' => 'E-mail',
        'name' => 'Název',
        'price' => 'Cena',
        'quantity' => 'Množství',
        'total' => 'Celkem',
        'type' => 'Typ',
        'position' => 'Pozice',
        'active' => 'Aktivní',
        'categories' => 'Kategorie',
        'tags' => 'Štítky',
        'brands' => 'Značky',
        'attributes' => 'Atributy',
        'main_image' => 'Hlavní obrázek',
        'gallery' => 'Galerie',
    ],

    // Statuses
    'statuses' => [
        'draft' => 'Koncept',
        'published' => 'Publikováno',
        'archived' => 'Archivováno',
        'pending' => 'Čeká',
        'active' => 'Aktivní',
        'inactive' => 'Neaktivní',
        'completed' => 'Dokončeno',
        'cancelled' => 'Zrušeno',
        'failed' => 'Selhalo',
    ],

    // Resources
    'resources' => [
        'page' => [
            'label' => 'Stránka',
            'plural' => 'Stránky',
            'navigation' => 'Stránky',
        ],
        'product' => [
            'label' => 'Produkt',
            'plural' => 'Produkty',
            'navigation' => 'Produkty',
        ],
        'order' => [
            'label' => 'Objednávka',
            'plural' => 'Objednávky',
            'navigation' => 'Objednávky',
        ],
        'payment' => [
            'label' => 'Platba',
            'plural' => 'Platby',
            'navigation' => 'Platby',
        ],
        'subscriber' => [
            'label' => 'Odběratel',
            'plural' => 'Odběratelé',
            'navigation' => 'Odběratelé',
        ],
        'navigation' => [
            'label' => 'Navigace',
            'plural' => 'Navigace',
            'navigation' => 'Navigace',
        ],
        'form' => [
            'label' => 'Formulář',
            'plural' => 'Formuláře',
            'navigation' => 'Formuláře',
        ],
        'funnel' => [
            'label' => 'Funnel',
            'plural' => 'Funnely',
            'navigation' => 'Funnely',
        ],
        'contract' => [
            'label' => 'Smlouva',
            'plural' => 'Smlouvy',
            'navigation' => 'Smlouvy',
        ],
    ],

    // Page resource specific
    'page' => [
        'sections' => [
            'info' => 'Informace o stránce',
            'content_blocks' => 'Obsahové bloky',
            'seo' => 'SEO',
            'publishing' => 'Publikování',
            'open_graph' => 'Open Graph',
        ],
        'block_groups' => [
            'hero' => 'Hero',
            'content' => 'Obsah',
            'cta' => 'CTA a formuláře',
            'features' => 'Funkce a prezentace',
            'data' => 'Statistiky a data',
            'layout' => 'Rozložení a navigace',
        ],
        'blocks' => [
            'hero' => 'Hero',
            'text' => 'Text',
            'image' => 'Obrázek',
            'testimonials' => 'Reference',
                        'contact_form' => 'Kontaktni formular',
            'premium_cta' => 'Prémiové CTA',
            'page_hero' => 'Hero stranky',
            'service_highlights' => 'Sluzby - prehled',
            'capabilities_detailed' => 'Detailni schopnosti',
            'doc_categories' => 'Kategorie dokumentace',
            'documentation_search' => 'Vyhledavani v dokumentaci',
            'facilities_grid' => 'Prehled provozu',
            'facility_standards' => 'Standardy provozu',
            'facility_stats' => 'Statistiky provozu',
            'faq' => 'Caste dotazy',
            'image_cta' => 'Obrazkove CTA',
            'image_grid' => 'Galerie obrazku',
            'industries_served' => 'Obsluhovana odvetvi',
            'map_placeholder' => 'Misto mapy',
            'process_steps' => 'Kroky procesu',
            'process_workflow' => 'Prubeh procesu',
            'stats_cards' => 'Karty statistik',
            'stats_showcase' => 'Prezentace statistik',
            'support_cards' => 'Karty podpory',
            'technology_innovation' => 'Technologie a inovace',
            'trust_showcase' => 'Ukazka duvery',
            'use_case_tabs' => 'Zalozky pripadu pouziti',
            'new' => 'Nový blok',
        ],
        'actions' => [
            'add_block' => 'Přidat blok',
        ],
        'fields' => [
            'heading' => 'Nadpis',
            'subheading' => 'Podnadpis',
            'body' => 'Tělo textu',
            'background_image' => 'Obrázek na pozadí',
            'alt' => 'Alternativní text',
            'caption' => 'Popisek',
            'cta_title' => 'Název CTA',
            'button_text' => 'Text tlačítka',
            'button_url' => 'URL tlačítka',
            'cta_primary_label' => 'Text primárního CTA',
            'cta_primary_page' => 'Stránka primárního CTA',
            'cta_primary_url' => 'URL primárního CTA',
            'cta_secondary_label' => 'Text sekundárního CTA',
            'cta_secondary_page' => 'Stránka sekundárního CTA',
            'cta_secondary_url' => 'URL sekundárního CTA',
            'cta_primary_label' => 'Text primárního CTA',
            'cta_primary_page' => 'Stránka primárního CTA',
            'cta_primary_url' => 'URL primárního CTA',
            'cta_secondary_label' => 'Text sekundárního CTA',
            'cta_secondary_page' => 'Stránka sekundárního CTA',
            'cta_secondary_url' => 'URL sekundárního CTA',
            'style' => 'Styl',
            'form_id' => 'Formulář',
            'form_title' => 'Název formuláře',
            'subtitle' => 'Podtitulek',
            'testimonials' => 'Reference',
            'quote' => 'Citace',
            'role' => 'Pozice',
            'photo' => 'Fotka',
            'rating' => 'Hodnocení',
            'success_title' => 'Titulek po odeslani',
            'success_message' => 'Zprava po odeslani',
            'submit_label' => 'Text tlacitka',
            'more_info_label' => 'Text odkazu',
            'services' => 'Sluzby',
            'service_page' => 'Stranka sluzby',
            'service_url' => 'URL sluzby',
            'service_anchor' => 'Kotva sluzby',
            'cta_label' => 'Text CTA',
            'cta_page' => 'Stranka CTA',
            'cta_url' => 'CTA URL',
            'cta_anchor' => 'CTA kotva',
            'rating' => 'Hodnocení',
            'success_title' => 'Titulek po odeslani',
            'success_message' => 'Zprava po odeslani',
            'submit_label' => 'Text tlacitka',
            'more_info_label' => 'Text odkazu',
            'services' => 'Sluzby',
            'service_page' => 'Stranka sluzby',
            'service_url' => 'URL sluzby',
            'service_anchor' => 'Kotva sluzby',
            'cta_label' => 'Text CTA',
            'cta_page' => 'Stranka CTA',
            'cta_url' => 'CTA URL',
            'cta_anchor' => 'CTA kotva',
            'items' => 'Položky',
            'features' => 'Výhody',
            'feature' => 'Výhoda',
            'icon' => 'Ikona',
            'icon_placeholder' => 'Vyberte ikonu...',
            'background_image_helper' => 'URL nebo media UUID dle strategie médií.',
            'buttons' => 'Tlačítka',
            'button_label' => 'Text tlačítka',
            'button_style' => 'Styl tlačítka',
            'button_page' => 'Stránka tlačítka',
            'button_page_placeholder' => 'Vyberte stránku...',
            'button_page_helper' => 'Nebo použijte vlastní URL níže.',
            'button_url_placeholder' => '/stranka, #sekce, https://...',
            'button_url_helper' => 'Podporuje: /relativni-cesta, #anchor, https://external.com',
            'stats' => 'Statistiky',
            'stat_value' => 'Hodnota',
            'stat_label' => 'Popisek',            'background_media_uuid' => 'Obrazek na pozadi',            'badges' => 'Odrazky',            'primary.label' => 'Text primarniho CTA',            'primary.link.page_id' => 'Stranka primarniho CTA',            'primary.link.url' => 'URL primarniho CTA',            'primary.link.anchor' => 'Kotva primarniho CTA',            'secondary.label' => 'Text sekundarniho CTA',            'secondary.link.page_id' => 'Stranka sekundarniho CTA',            'secondary.link.url' => 'URL sekundarniho CTA',            'secondary.link.anchor' => 'Kotva sekundarniho CTA',            'cta.label' => 'Text CTA',            'cta.link.page_id' => 'Stranka CTA',            'cta.link.url' => 'URL CTA',            'cta.link.anchor' => 'Kotva CTA',            'image_media_uuid' => 'Obrazek',            'steps' => 'Kroky',            'benefits' => 'Vyhody',            'logos' => 'Loga klientu',            'note' => 'Poznamka',            'sidebar_title' => 'Nadpis sidebaru',            'contact_items' => 'Kontaktni karty',            'trust_items' => 'Duvodni body',            'cards' => 'Karty',            'cta_description' => 'Popis CTA',            'cta_background_media_uuid' => 'CTA obrazek na pozadi',            'cta_button.label' => 'Text CTA tlacitka',            'cta_button.link.page_id' => 'Stranka CTA',            'cta_button.link.url' => 'URL CTA',            'cta_button.link.anchor' => 'Kotva CTA',            'placeholder' => 'Placeholder',            'quick_links' => 'Rychle odkazy',
            'address' => 'Adresa',
            'anchor' => 'Kotva',
            'anchor_placeholder' => 'section-id',
            'answer' => 'Odpoved',
            'categories' => 'Kategorie',
            'certifications' => 'Certifikace',
            'challenge' => 'Vyzva',
            'description' => 'Popis',
            'docs' => 'Dokumenty',
            'email' => 'E-mail',
            'file_url' => 'URL souboru',
            'file_url_placeholder' => '/media/... nebo https://...',
            'helper' => 'Napoveda',
            'hours' => 'Oteviraci doba',
            'icon_key' => 'Ikona',
            'industry' => 'Odvetvi',
            'label' => 'Popisek',
            'link.anchor' => 'Kotva odkazu',
            'link.page_id' => 'Stranka odkazu',
            'link.url' => 'URL odkazu',
            'link_label' => 'Text odkazu',
            'location' => 'Lokalita',
            'manager' => 'Vedouci',
            'media_uuid_helper' => 'UUID media (MediaPicker v CMS).',
            'more_info_label_helper' => 'Fallback: home.services.moreInfo',
            'name' => 'Nazev',
            'number' => 'Cislo',
            'override_media_alt_text_helper' => 'Prepsat alt text media',
            'phone' => 'Telefon',
            'question' => 'Otazka',
            'results' => 'Vysledky',
            'size' => 'Velikost',
            'solution' => 'Reseni',
            'step' => 'Krok',
            'text' => 'Text',
            'title' => 'Nazev',
            'type' => 'Typ',
            'value' => 'Hodnota',
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        ],
        'seo' => [
            'title' => 'SEO titulek',
            'description' => 'Meta popis',
            'title_helper' => 'Doporučeno: 50-60 znaků',
            'description_helper' => 'Doporučeno: 150-160 znaků',
            'og_title' => 'OG titulek',
            'og_description' => 'OG popis',
            'og_image' => 'OG obrázek',
        ],
    ],

    // Product resource specific
    'product' => [
        'sections' => [
            'info' => 'Informace o produktu',
            'product_data' => 'Data produktu',
            'status' => 'Stav',
            'taxonomies' => 'Taxonomie',
            'media' => 'Média',
            'seo' => 'SEO',
        ],
        'tabs' => [
            'description' => 'Popis',
            'pricing' => 'Cena',
            'attributes' => 'Atributy',
        ],
        'fields' => [
            'sku' => 'SKU',
            'price' => 'Cena',
            'sale_price' => 'Akční cena',
            'stock' => 'Skladem',
            'is_active' => 'Aktivní',
            'short_description' => 'Krátký popis',
            'description' => 'Popis produktu',
        
        
        
        
        
        
        
        
        
        ],
        'price_helper' => 'Cena v :currency',
        'price_variable_helper' => 'Cena je nastavena na variantách pro variabilní produkty',
        'active_helper' => 'Pouze aktivní produkty jsou viditelné na e-shopu',
        'attributes_helper' => 'Vyberte atributy produktu (barva, velikost, atd.)',
        'slug_helper' => 'URL-friendly identifikátor produktu',
        'short_description_helper' => 'Krátký popis zobrazený v přehledu produktů',
        'description_helper' => 'Detailní popis produktu s formátováním',
    ],

    // Order resource specific
    'order' => [
        'fields' => [
            'order_number' => 'Číslo objednávky',
            'customer' => 'Zákazník',
            'items' => 'Položky',
            'subtotal' => 'Mezisoučet',
            'shipping' => 'Doprava',
            'tax' => 'DPH',
            'total' => 'Celkem',
        
        
        
        
        
        
        
        
        
        ],
        'statuses' => [
            'pending' => 'Čeká na zpracování',
            'processing' => 'Zpracovává se',
            'shipped' => 'Odesláno',
            'delivered' => 'Doručeno',
            'cancelled' => 'Zrušeno',
            'refunded' => 'Vráceno',
        ],
    ],

    // Styles
    'styles' => [
        'primary' => 'Primární',
        'secondary' => 'Sekundární',
        'outline' => 'Obrysový',
    ],

    // Messages
    'messages' => [
        'saved' => 'Úspěšně uloženo.',
        'deleted' => 'Úspěšně smazáno.',
        'created' => 'Úspěšně vytvořeno.',
        'updated' => 'Úspěšně aktualizováno.',
        'error' => 'Došlo k chybě.',
        'confirm_delete' => 'Opravdu chcete smazat?',
    ],

    // Preview
    'preview' => [
        'title' => 'Náhled',
        'mode' => 'Režim náhledu',
        'unknown_block' => 'Neznámý typ bloku: :type',
    ],
];
