<?php

return [
    // Navigation groups
    'navigation' => [
        'content' => 'Content',
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
        'blocks' => [
            'text' => 'Text',
            'image' => 'Image',
            'cta' => 'Call to Action',
            'form_embed' => 'Form Embed',
            'new' => 'New Block',
        ],
        'fields' => [
            'heading' => 'Heading',
            'body' => 'Body Text',
            'alt' => 'Alt Text',
            'caption' => 'Caption',
            'cta_title' => 'CTA Title',
            'button_text' => 'Button Text',
            'button_url' => 'Button URL',
            'style' => 'Style',
            'form_id' => 'Form ID',
            'form_title' => 'Form Title',
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
        'fields' => [
            'sku' => 'SKU',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'stock' => 'Stock',
            'is_active' => 'Active',
        ],
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
];
