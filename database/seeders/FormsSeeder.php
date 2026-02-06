<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Forms\Domain\Form;

class FormsSeeder extends Seeder
{
    public function run(): void
    {
        Form::query()->updateOrCreate(
            ['name' => 'Kontaktni formular'],
            [
                'active' => true,
                'schema' => [
                    [
                        'name' => 'name',
                        'label' => 'Jmeno',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Vase jmeno',
                    ],
                    [
                        'name' => 'email',
                        'label' => 'E-mail',
                        'type' => 'email',
                        'required' => true,
                        'placeholder' => 'vas@email.cz',
                    ],
                    [
                        'name' => 'phone',
                        'label' => 'Telefon',
                        'type' => 'tel',
                        'required' => false,
                        'placeholder' => '+420 123 456 789',
                    ],
                    [
                        'name' => 'topic',
                        'label' => 'Tema',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            [
                                'label' => 'Cenova nabidka',
                                'value' => 'pricing',
                            ],
                            [
                                'label' => 'Konzultace',
                                'value' => 'consultation',
                            ],
                            [
                                'label' => 'Jine',
                                'value' => 'other',
                            ],
                        ],
                    ],
                    [
                        'name' => 'message',
                        'label' => 'Zprava',
                        'type' => 'textarea',
                        'required' => true,
                        'placeholder' => 'S cim vam muzeme pomoct?',
                    ],
                    [
                        'name' => 'consent',
                        'label' => 'Souhlas se zpracovanim udaju',
                        'type' => 'checkbox',
                        'required' => true,
                    ],
                ],
                'data_options' => [
                    'submit_button_text' => 'Odeslat poptavku',
                    'success_title' => 'Dekujeme!',
                    'success_message' => 'Ozveme se vam do 24 hodin.',
                    'sidebar' => [
                        [
                            'type' => 'contact_info',
                            'title' => 'Potrebujete pomoc?',
                            'items' => [
                                [
                                    'label' => 'Telefon',
                                    'value' => '+420 XXX XXX XXX',
                                    'note' => 'Po-Pa, 8:00-17:00',
                                    'icon' => 'phone',
                                    'tone' => 'blue',
                                ],
                                [
                                    'label' => 'E-mail',
                                    'value' => 'info@ercee.cz',
                                    'note' => 'Odpovime do 24 hodin',
                                    'icon' => 'mail',
                                    'tone' => 'teal',
                                ],
                            ],
                        ],
                        [
                            'type' => 'steps',
                            'title' => 'Jak to funguje',
                            'items' => [
                                [
                                    'title' => 'Odpoved',
                                    'description' => 'Vasi poptavku zpracujeme do 24 hodin',
                                    'number' => '1',
                                    'tone' => 'blue',
                                ],
                                [
                                    'title' => 'Konzultace',
                                    'description' => 'Domluvime si hovor pro upresneni',
                                    'number' => '2',
                                    'tone' => 'teal',
                                ],
                            ],
                        ],
                        [
                            'type' => 'trust_indicators',
                            'title' => 'Proc my?',
                            'items' => [
                                [
                                    'text' => '10+ let zkusenosti v oboru',
                                    'icon' => 'trend',
                                    'tone' => 'green',
                                ],
                                [
                                    'text' => '99% spokojenost klientu',
                                    'icon' => 'check',
                                    'tone' => 'blue',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
