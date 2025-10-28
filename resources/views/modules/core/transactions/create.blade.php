<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Movimiento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('transacciones.store') }}" method="POST" x-data="{ type: '{{ old('type', 'Egreso') }}' }">
                        @csrf

                        <input type="hidden" name="type" x-model="type">
                        <div class="mb-4 border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button type="button" @click="type = 'Egreso'"
                                        :class="type === 'Egreso' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                                    {{ __('Egreso / Gasto') }}
                                </button>
                                <button type="button" @click="type = 'Ingreso'"
                                        :class="type === 'Ingreso' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                                    {{ __('Ingreso') }}
                                </button>
                                <button type="button" @click="type = 'Transferencia'"
                                        :class="type === 'Transferencia' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                                    {{ __('Transferencia') }}
                                </button>
                            </nav>
                        </div>

                        <div>
                            <label for="amount" class="block font-medium text-sm text-gray-700">{{ __('Monto') }}</label>
                            <input id="amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" required
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>
                        <div class="mt-4">
                            <label for="transaction_date" class="block font-medium text-sm text-gray-700">{{ __('Fecha') }}</label>
                            <input id="transaction_date" name="transaction_date" type="date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>
                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">{{ __('Descripción (Opcional)') }}</label>
                            <input id="description" name="description" type="text" value="{{ old('description') }}"
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div x-show="type === 'Ingreso' || type === 'Egreso'" class="space-y-4">
                            <div class="mt-4">
                                <label for="account_id" class="block font-medium text-sm text-gray-700">{{ __('Cuenta') }}</label>
                                <select id="account_id" name="account_id" :disabled="type === 'Transferencia'"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Selecciona una cuenta') }}</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} (${{ number_format($account->balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-4" x-show="type === 'Egreso'">
                                <label for="category_id_egreso" class="block font-medium text-sm text-gray-700">{{ __('Categoría de Egreso') }}</label>
                                <select id="category_id_egreso" name="category_id" :disabled="type !== 'Egreso'"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Selecciona una categoría') }}</option>
                                    @foreach($egresoCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-4" x-show="type === 'Ingreso'">
                                <label for="category_id_ingreso" class="block font-medium text-sm text-gray-700">{{ __('Categoría de Ingreso') }}</label>
                                <select id="category_id_ingreso" name="category_id" :disabled="type !== 'Ingreso'"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Selecciona una categoría') }}</option>
                                    @foreach($ingresoCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div x-show="type === 'Transferencia'" class="space-y-4">
                            <div class="mt-4">
                                <label for="account_from_id" class="block font-medium text-sm text-gray-700">{{ __('Cuenta Origen') }}</label>
                                <select id="account_from_id" name="account_from_id" :disabled="type !== 'Transferencia'"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Selecciona cuenta origen') }}</option>
                                    @foreach($transferFromAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_from_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} (${{ number_format($account->balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-4">
                                <label for="account_to_id" class="block font-medium text-sm text-gray-700">{{ __('Cuenta Destino') }}</label>
                                <select id="account_to_id" name="account_to_id" :disabled="type !== 'Transferencia'"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Selecciona cuenta destino') }}</option>
                                    @foreach($accounts as $account) <option value="{{ $account->id }}" {{ old('account_to_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} (${{ number_format($account->balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transacciones.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancelar') }}</a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Guardar Movimiento') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
