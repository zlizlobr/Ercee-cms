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
        'blocks' => [
            'hero' => 'Hero sekce',
            'text' => 'Text',
            'image' => 'Obrázek',
            'cta' => 'Výzva k akci',
            'form_embed' => 'Vložený formulář',
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
            'style' => 'Styl',
            'form_id' => 'Formulář',
            'form_title' => 'Název formuláře',
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
