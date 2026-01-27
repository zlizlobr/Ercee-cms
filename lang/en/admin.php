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
        'blocks' => [
            'hero' => 'Hero Section',
            'text' => 'Text',
            'image' => 'Image',
            'cta' => 'Call to Action',
            'form_embed' => 'Form Embed',
            'testimonials' => 'Testimonials',
                        'contact_form' => 'Contact form',
            'new' => 'New Block',
        ],
        'actions' => [
            'add_block' => 'Add Block',
        ],
        'fields' => [
            'heading' => 'Heading',
            'subheading' => 'Subheading',
            'body' => 'Body Text',
            'background_image' => 'Background Image',
            'alt' => 'Alt Text',
            'caption' => 'Caption',
            'cta_title' => 'CTA Title',
            'button_text' => 'Button Text',
            'button_url' => 'Button URL',
            'style' => 'Style',
            'form_id' => 'Form',
            'form_title' => 'Form Title',
            'subtitle' => 'Subtitle',
            'testimonials' => 'Testimonials',
            'quote' => 'Quote',
            'role' => 'Role',
            'photo' => 'Photo',
            'rating' => 'Rating',            'success_title' => 'Success title',            'success_message' => 'Success message',            'submit_label' => 'Submit label',
        
        
        
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
            'description' => 'Product Description',            'success_title' => 'Success title',            'success_message' => 'Success message',            'submit_label' => 'Submit label',
        
        
        
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
            'total' => 'Total',            'success_title' => 'Success title',            'success_message' => 'Success message',            'submit_label' => 'Submit label',
        
        
        
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

    // Preview
    'preview' => [
        'title' => 'Preview',
        'mode' => 'Preview Mode',
        'unknown_block' => 'Unknown block type: :type',
    ],
];
