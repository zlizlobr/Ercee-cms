@php
    $formId = $block['data']['form_id'] ?? null;
    $form = $formId ? \App\Domain\Form\Form::active()->find($formId) : null;
@endphp

@if($form)
    <div class="rounded-lg bg-gray-100 p-8" id="form-container-{{ $form->id }}">
        <h3 class="mb-6 text-xl font-bold text-gray-900">{{ $form->name }}</h3>

        <form
            id="dynamic-form-{{ $form->id }}"
            class="space-y-4"
            data-form-id="{{ $form->id }}"
            onsubmit="return submitForm(event, {{ $form->id }})"
        >
            <input type="hidden" name="_hp_field" value="">

            <div>
                <label for="email-{{ $form->id }}" class="block text-sm font-medium text-gray-700">
                    Email <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email-{{ $form->id }}"
                    name="email"
                    required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            @foreach($form->schema as $field)
                <div>
                    <label for="{{ $field['name'] }}-{{ $form->id }}" class="block text-sm font-medium text-gray-700">
                        {{ $field['label'] }}
                        @if(!empty($field['required']))
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    @switch($field['type'])
                        @case('text')
                            <input
                                type="text"
                                id="{{ $field['name'] }}-{{ $form->id }}"
                                name="{{ $field['name'] }}"
                                {{ !empty($field['required']) ? 'required' : '' }}
                                class="mt-1 block w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @break

                        @case('email')
                            <input
                                type="email"
                                id="{{ $field['name'] }}-{{ $form->id }}"
                                name="{{ $field['name'] }}"
                                {{ !empty($field['required']) ? 'required' : '' }}
                                class="mt-1 block w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @break

                        @case('textarea')
                            <textarea
                                id="{{ $field['name'] }}-{{ $form->id }}"
                                name="{{ $field['name'] }}"
                                rows="4"
                                {{ !empty($field['required']) ? 'required' : '' }}
                                class="mt-1 block w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                            @break

                        @case('select')
                            <select
                                id="{{ $field['name'] }}-{{ $form->id }}"
                                name="{{ $field['name'] }}"
                                {{ !empty($field['required']) ? 'required' : '' }}
                                class="mt-1 block w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Select an option</option>
                                @foreach($field['options'] ?? [] as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('checkbox')
                            <div class="mt-1">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        id="{{ $field['name'] }}-{{ $form->id }}"
                                        name="{{ $field['name'] }}"
                                        value="1"
                                        {{ !empty($field['required']) ? 'required' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-600">{{ $field['label'] }}</span>
                                </label>
                            </div>
                            @break
                    @endswitch
                </div>
            @endforeach

            <div>
                <button
                    type="submit"
                    class="w-full rounded-md bg-blue-600 px-6 py-3 text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Submit
                </button>
            </div>

            <div id="form-message-{{ $form->id }}" class="hidden"></div>
        </form>
    </div>
@endif
