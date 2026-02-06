<?php

return [
    // Navigation groups
    'navigation' => [
        'content' => 'Content',
        'products' => 'Products',
        'commerce' => 'Commerce',
        'marketing' => 'Marketing',
        'settings' => 'Settings',
    ],

    // Common actions
    'actions' => [
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'view' => 'View',
        'back' => 'Back',
        'search' => 'Search',
        'filter' => 'Filter',
        'export' => 'Export',
        'import' => 'Import',
        'preview' => 'Preview',
        'close' => 'Close',
    ],

    // Common labels
    'labels' => [
        'title' => 'Title',
        'slug' => 'URL Slug',
        'status' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'published_at' => 'Published At',
        'description' => 'Description',
        'content' => 'Content',
        'image' => 'Image',
        'email' => 'Email',
        'name' => 'Name',
        'price' => 'Price',
        'quantity' => 'Quantity',
        'total' => 'Total',
        'type' => 'Type',
        'position' => 'Position',
        'active' => 'Active',
        'categories' => 'Categories',
        'tags' => 'Tags',
        'brands' => 'Brands',
        'attributes' => 'Attributes',
        'main_image' => 'Main Image',
        'gallery' => 'Gallery',
    ],

    // Statuses
    'statuses' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
        'pending' => 'Pending',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'failed' => 'Failed',
    ],

    // Resources
    'resources' => [
        'page' => [
            'label' => 'Page',
            'plural' => 'Pages',
            'navigation' => 'Pages',
        ],
        'product' => [
            'label' => 'Product',
            'plural' => 'Products',
            'navigation' => 'Products',
        ],
        'order' => [
            'label' => 'Order',
            'plural' => 'Orders',
            'navigation' => 'Orders',
        ],
        'payment' => [
            'label' => 'Payment',
            'plural' => 'Payments',
            'navigation' => 'Payments',
        ],
        'subscriber' => [
            'label' => 'Subscriber',
            'plural' => 'Subscribers',
            'navigation' => 'Subscribers',
        ],
        'navigation' => [
            'label' => 'Navigation',
            'plural' => 'Navigations',
            'navigation' => 'Navigation',
        ],
        'form' => [
            'label' => 'Form',
            'plural' => 'Forms',
            'navigation' => 'Forms',
        ],
        'funnel' => [
            'label' => 'Funnel',
            'plural' => 'Funnels',
            'navigation' => 'Funnels',
        ],
        'contract' => [
            'label' => 'Contract',
            'plural' => 'Contracts',
            'navigation' => 'Contracts',
        ],
    ],

    // Page resource specific
    'page' => [
        'sections' => [
            'info' => 'Page Info',
            'content_blocks' => 'Content Blocks',
            'seo' => 'SEO',
            'publishing' => 'Publishing',
            'open_graph' => 'Open Graph',
        ],
        'block_groups' => [
            'hero' => 'Hero',
            'content' => 'Content',
            'cta' => 'CTA & Forms',
            'features' => 'Features & Showcase',
            'data' => 'Stats & Data',
            'layout' => 'Layout & Navigation',
        ],
        'blocks' => [
            'hero' => 'Hero',
            'text' => 'Text',
            'image' => 'Image',
            'cta' => 'Call to Action',
            'form_embed' => 'Form Embed',
            'testimonials' => 'Testimonials',
            'contact_form' => 'Contact form',
            'capabilities' => 'Capabilities',
            'premium_cta' => 'Premium CTA',
            'page_hero' => 'Page Hero',
            'service_highlights' => 'Service Highlights',
            'capabilities_detailed' => 'Capabilities (Detailed)',
            'doc_categories' => 'Documentation Categories',
            'documentation_search' => 'Documentation Search',
            'facilities_grid' => 'Facilities Grid',
            'facility_standards' => 'Facility Standards',
            'facility_stats' => 'Facility Stats',
            'faq' => 'FAQ',
            'image_cta' => 'Image CTA',
            'image_grid' => 'Image Grid',
            'industries_served' => 'Industries Served',
            'map_placeholder' => 'Map Placeholder',
            'process_steps' => 'Process Steps',
            'process_workflow' => 'Process Workflow',
            'stats_cards' => 'Stats Cards',
            'stats_showcase' => 'Stats Showcase',
            'support_cards' => 'Support Cards',
            'technology_innovation' => 'Technology & Innovation',
            'trust_showcase' => 'Trust Showcase',
            'use_case_tabs' => 'Use Case Tabs',
            'new' => 'New Block',
        ],
        'actions' => [
            'add_block' => 'Add Block',
        ],
        'fields' => [
            // Common fields
            'title' => 'Title',
            'subtitle' => 'Subtitle',
            'heading' => 'Heading',
            'subheading' => 'Subheading',
            'description' => 'Description',
            'body' => 'Body Text',
            'text' => 'Text',
            'label' => 'Label',
            'name' => 'Name',
            'value' => 'Value',
            'type' => 'Type',
            'style' => 'Style',
            'number' => 'Number',
            'step' => 'Step',
            'helper' => 'Helper',
            'note' => 'Note',
            'placeholder' => 'Placeholder',

            // Media fields
            'background_image' => 'Background Image',
            'background_media_uuid' => 'Background image',
            'image_media_uuid' => 'Image',
            'cta_background_media_uuid' => 'CTA background image',
            'alt' => 'Alt Text',
            'caption' => 'Caption',
            'photo' => 'Photo',
            'background_image_helper' => 'URL or media UUID depending on media strategy.',
            'media_uuid_helper' => 'Media UUID (MediaPicker in CMS).',
            'override_media_alt_text_helper' => 'Override media alt text',

            // Icon fields
            'icon' => 'Icon',
            'icon_key' => 'Icon',
            'icon_placeholder' => 'Select icon...',

            // LinkPicker-powered CTA fields (nested dot-notation)
            'primary.label' => 'Primary CTA label',
            'primary.link.page_id' => 'Primary CTA page',
            'primary.link.url' => 'Primary CTA URL',
            'primary.link.anchor' => 'Primary CTA anchor',
            'secondary.label' => 'Secondary CTA label',
            'secondary.link.page_id' => 'Secondary CTA page',
            'secondary.link.url' => 'Secondary CTA URL',
            'secondary.link.anchor' => 'Secondary CTA anchor',
            'cta.label' => 'CTA label',
            'cta.link.page_id' => 'CTA page',
            'cta.link.url' => 'CTA URL',
            'cta.link.anchor' => 'CTA anchor',
            'cta_button.label' => 'CTA button label',
            'cta_button.link.page_id' => 'CTA button page',
            'cta_button.link.url' => 'CTA button URL',
            'cta_button.link.anchor' => 'CTA button anchor',
            'link.page_id' => 'Link page',
            'link.url' => 'Link URL',
            'link.anchor' => 'Link anchor',
            'link_label' => 'Link label',

            // Legacy CTA fields (flat keys, kept for backward compatibility)
            'cta_title' => 'CTA Title',
            'cta_label' => 'CTA label',
            'cta_page' => 'CTA page',
            'cta_url' => 'CTA URL',
            'cta_anchor' => 'CTA anchor',
            'cta_description' => 'CTA description',
            'cta_primary_label' => 'Primary CTA label',
            'cta_primary_page' => 'Primary CTA page',
            'cta_primary_url' => 'Primary CTA URL',
            'cta_secondary_label' => 'Secondary CTA label',
            'cta_secondary_page' => 'Secondary CTA page',
            'cta_secondary_url' => 'Secondary CTA URL',

            // Button fields
            'button_text' => 'Button Text',
            'button_url' => 'Button URL',
            'button_label' => 'Button label',
            'button_style' => 'Button style',
            'button_page' => 'Button page',
            'button_page_placeholder' => 'Select a page...',
            'button_page_helper' => 'Or use custom URL below.',
            'button_url_placeholder' => '/page, #section, https://...',
            'button_url_helper' => 'Supports: /relative-path, #anchor, https://external.com',
            'buttons' => 'Buttons',

            // Repeater / collection fields
            'items' => 'Items',
            'features' => 'Features',
            'feature' => 'Feature',
            'services' => 'Services',
            'stats' => 'Stats',
            'stat_value' => 'Value',
            'stat_label' => 'Label',
            'steps' => 'Steps',
            'benefits' => 'Benefits',
            'badges' => 'Badges',
            'logos' => 'Trusted logos',
            'cards' => 'Cards',
            'categories' => 'Categories',
            'certifications' => 'Certifications',
            'results' => 'Results',
            'quick_links' => 'Quick links',
            'contact_items' => 'Contact items',
            'trust_items' => 'Trust indicators',
            'docs' => 'Documents',
            'testimonials' => 'Testimonials',

            // Service fields
            'service_page' => 'Service page',
            'service_url' => 'Service URL',
            'service_anchor' => 'Service anchor',

            // Anchor fields
            'anchor' => 'Anchor',
            'anchor_placeholder' => 'section-id',

            // Form fields
            'form_id' => 'Form',
            'form_title' => 'Form Title',
            'submit_label' => 'Submit label',
            'success_title' => 'Success title',
            'success_message' => 'Success message',
            'more_info_label' => 'More info label',
            'more_info_label_helper' => 'Fallback: home.services.moreInfo',
            'sidebar_title' => 'Sidebar title',

            // Testimonial fields
            'quote' => 'Quote',
            'role' => 'Role',
            'rating' => 'Rating',

            // Use case / industry fields
            'industry' => 'Industry',
            'challenge' => 'Challenge',
            'solution' => 'Solution',

            // Contact / location fields
            'address' => 'Address',
            'email' => 'Email',
            'phone' => 'Phone',
            'hours' => 'Hours',
            'location' => 'Location',
            'manager' => 'Manager',

            // Document fields
            'file_url' => 'File URL',
            'file_url_placeholder' => '/media/... or https://...',
            'size' => 'Size',

            // FAQ fields
            'question' => 'Question',
            'answer' => 'Answer',
        ],
        'seo' => [
            'title' => 'SEO Title',
            'description' => 'Meta Description',
            'title_helper' => 'Recommended: 50-60 characters',
            'description_helper' => 'Recommended: 150-160 characters',
            'og_title' => 'OG Title',
            'og_description' => 'OG Description',
            'og_image' => 'OG Image',
        ],
    ],

    // Product resource specific
    'product' => [
        'sections' => [
            'info' => 'Product Info',
            'product_data' => 'Product Data',
            'status' => 'Status',
            'taxonomies' => 'Taxonomies',
            'media' => 'Media',
            'seo' => 'SEO',
        ],
        'tabs' => [
            'description' => 'Description',
            'pricing' => 'Pricing',
            'attributes' => 'Attributes',
        ],
        'fields' => [
            'sku' => 'SKU',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'stock' => 'Stock',
            'is_active' => 'Active',
            'short_description' => 'Short Description',
            'description' => 'Product Description',
        ],
        'price_helper' => 'Price in :currency',
        'price_variable_helper' => 'Price is set on variants for variable products',
        'active_helper' => 'Only active products are visible on the storefront',
        'attributes_helper' => 'Select product attributes (color, size, etc.)',
        'slug_helper' => 'URL-friendly identifier for the product',
        'short_description_helper' => 'Short description displayed in product listings',
        'description_helper' => 'Detailed product description with formatting',
    ],

    // Order resource specific
    'order' => [
        'fields' => [
            'order_number' => 'Order Number',
            'customer' => 'Customer',
            'items' => 'Items',
            'subtotal' => 'Subtotal',
            'shipping' => 'Shipping',
            'tax' => 'Tax',
            'total' => 'Total',
        ],
        'statuses' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ],
    ],

    // LinkPicker component
    'link_picker' => [
        'link_type' => 'Link Type',
        'type_url' => 'Custom URL',
        'type_page' => 'Page',
        'page' => 'Page',
        'page_placeholder' => 'Select a page...',
        'page_helper' => 'Or use custom URL below.',
        'url' => 'URL',
        'url_placeholder' => '/page, #section, https://...',
        'url_helper' => 'Supports: /relative-path, #anchor, https://external.com',
        'anchor' => 'Anchor',
        'anchor_placeholder' => 'section-id',
        'target' => 'Open in',
        'target_self' => 'Same window',
        'target_blank' => 'New window/tab',
        'use_global' => 'Use global setting',
    ],

    // Styles
    'styles' => [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
        'outline' => 'Outline',
    ],

    // Messages
    'messages' => [
        'saved' => 'Successfully saved.',
        'deleted' => 'Successfully deleted.',
        'created' => 'Successfully created.',
        'updated' => 'Successfully updated.',
        'error' => 'An error occurred.',
        'confirm_delete' => 'Are you sure you want to delete?',
    ],

    // Preview
    'preview' => [
        'title' => 'Preview',
        'mode' => 'Preview Mode',
        'unknown_block' => 'Unknown block type: :type',
    ],
];
